<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use Exception;

class GoogleDriveService
{
    protected Drive $drive;

    public function __construct()
    {
        $client = new Client();

        $credentialsPath = storage_path(env('GOOGLE_DRIVE_CREDENTIALS_PATH', 'app/credentials/google-drive-credentials.json'));
        if (!file_exists($credentialsPath)) {
            throw new Exception("Google Drive credentials file not found at: {$credentialsPath}");
        }

        $client->setAuthConfig($credentialsPath);
        $client->addScope(Drive::DRIVE_READONLY);

        $this->drive = new Drive($client);
    }

    /**
     * Extract folder ID from Drive URL.
     */
    public function extractFolderIdFromUrl(string $url): ?string
    {
        return preg_match('/folders\/([a-zA-Z0-9_-]+)/', $url, $matches)
            ? $matches[1]
            : null;
    }

    /**
     * List all files in a Drive folder.
     */
    public function listFilesInFolder(string $folderId): array
    {
        $params = [
            'q' => "'{$folderId}' in parents and trashed = false",
            'fields' => 'files(id, name, mimeType, size)'
        ];

        $results = $this->drive->files->listFiles($params);
        return $results->getFiles() ?? [];
    }

    /**
     * Download a single Drive file with streaming (RAM-safe).
     */
    public function downloadFile(string $fileId, string $fileName, string $setId): string
    {
        $setDir = storage_path("app/private/sets/{$setId}");
        $filePath = "{$setDir}/{$fileName}";

        if (file_exists($filePath)) {
            return "private/sets/{$setId}/{$fileName}";
        }

        if (!is_dir($setDir)) {
            mkdir($setDir, 0755, true);
        }

        // Increase memory limit temporarily for this operation
        $originalMemoryLimit = ini_get('memory_limit');
        ini_set('memory_limit', '512M');

        try {
            $response = $this->drive->files->get($fileId, ['alt' => 'media']);
            
            // Google Drive API returns a PSR-7 response with getBody() method
            // @phpstan-ignore-next-line
            // @var \Psr\Http\Message\StreamInterface $body
            $body = $response->getBody();
            
            // Stream to disk in chunks to avoid memory issues
            $dest = fopen($filePath, 'wb'); // 'wb' for binary write
            if (!$dest) {
                throw new Exception("Cannot open file for writing: {$filePath}");
            }

            // Stream in 2MB chunks for better performance
            while (!$body->eof()) {
                $chunk = $body->read(2 * 1024 * 1024); // 2MB chunks
                if ($chunk !== false && $chunk !== '') {
                    fwrite($dest, $chunk);
                    // Force garbage collection periodically for large files
                    if (ftell($dest) % (10 * 1024 * 1024) == 0) {
                        gc_collect_cycles();
                    }
                }
            }
            fclose($dest);
        } finally {
            // Restore original memory limit
            ini_set('memory_limit', $originalMemoryLimit);
        }

        return "private/sets/{$setId}/{$fileName}";
    }

    /**
     * Download a whole folder as ZIP (memory-optimized for large files).
     */
    public function downloadFolderAsZip(string $folderUrl, string $setId, string $setName): string
    {
        $folderId = $this->extractFolderIdFromUrl($folderUrl);
        if (!$folderId) {
            throw new Exception('Không tìm thấy thư mục trên Google Drive.');
        }

        $downloadDir = storage_path('app/private/downloads');
        if (!is_dir($downloadDir)) {
            mkdir($downloadDir, 0755, true);
        }

        // Nếu zip tồn tại rồi -> trả luôn
        $existingZip = glob("{$downloadDir}/set_{$setId}_*.zip");
        if (!empty($existingZip)) {
            return $existingZip[0];
        }

        $files = $this->listFilesInFolder($folderId);
        if (empty($files)) {
            throw new Exception('Không tìm thấy file nào trong thư mục.');
        }

        $tempSetDir = storage_path("app/private/sets/{$setId}");
        if (is_dir($tempSetDir)) {
            $this->deleteDirectory($tempSetDir);
        }

        foreach (glob("{$downloadDir}/set_{$setId}_*.zip_*") as $partialZip) {
            @unlink($partialZip);
        }

        $zipFileName = "set_{$setId}_" . time() . ".zip";
        $zipPath = "{$downloadDir}/{$zipFileName}";

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception('Không thể tạo file ZIP.');
        }

        // Tối ưu bộ nhớ: download và add từng file, sau đó xóa ngay
        foreach ($files as $file) {
            try {
                // Download file với streaming
                $tempPath = $this->downloadFile($file->id, $file->name, $setId);
                $fullPath = storage_path("app/{$tempPath}");
                
                if (file_exists($fullPath)) {
                    // Add file vào ZIP với compression STORE (không nén để tiết kiệm RAM)
                    $zip->addFile($fullPath, $file->name);
                    $zip->setCompressionName($file->name, ZipArchive::CM_STORE);
                    
                    // Xóa file tạm ngay sau khi add vào ZIP để giải phóng disk space
                    // Note: File sẽ được xóa sau khi ZIP close, nhưng ta có thể xóa thủ công
                }
            } catch (\Throwable $e) {
                Log::warning('Error adding file to ZIP', [
                    'file_id' => $file->id,
                    'file_name' => $file->name,
                    'error' => $e->getMessage()
                ]);
                // Continue with next file
            }
        }

        $zip->close();

        // Cleanup: Xóa các file tạm đã được add vào ZIP
        $setDir = storage_path("app/private/sets/{$setId}");
        if (is_dir($setDir)) {
            $this->deleteDirectory($setDir);
        }

        return $zipPath;
    }

    /**
     * Helper: Delete directory and its contents recursively.
     */
    private function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "{$dir}/{$file}";
            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }

        return @rmdir($dir);
    }

    /**
     * Stream ZIP file to client.
     */
    public function streamZipDownload(string $zipPath, string $downloadName)
    {
        if (!file_exists($zipPath)) {
            abort(404, 'File không tồn tại hoặc đã bị xóa.');
        }

        $cleanName = $this->cleanFileName($downloadName);
        $timestamp = date('Y-m-d_H-i-s');
        if (!empty($cleanName) && trim($cleanName, '_') !== '') {
            $baseName = $cleanName . '_' . $timestamp;
        } else {
            $baseName = $timestamp;
        }
        $baseName = trim($baseName, '_');
        $finalName = $baseName . '.zip';
        $finalName = preg_replace('/\.zip_+$/', '.zip', $finalName);
        $finalName = preg_replace('/_+\.zip$/', '.zip', $finalName);
        $finalName = ltrim($finalName, '_');
        $finalName = preg_replace('/[\pZ\s\x00-\x1F\x7F]+$/u', '', $finalName);
        if (!preg_match('/\.zip$/i', $finalName)) {
            $finalName = preg_replace('/(\.zip).*$/i', '$1', $finalName);
            if (!preg_match('/\.zip$/i', $finalName)) {
                $finalName .= '.zip';
            }
        }
        $finalName = preg_replace('/(\.zip)[^A-Za-z0-9]*$/i', '$1', $finalName);

        set_time_limit(0);
        ignore_user_abort(true);

        return response()->stream(function () use ($zipPath) {
            $stream = fopen($zipPath, 'rb');
            while (!feof($stream)) {
                echo fread($stream, 1024 * 1024);
                ob_flush();
                flush();
            }
            fclose($stream);
        }, 200, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename=' . $finalName,
            'Content-Length' => filesize($zipPath),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Clean filename by removing special characters and trailing underscores.
     */
    private function cleanFileName(string $fileName): string
    {
        $fileName = pathinfo($fileName, PATHINFO_FILENAME);
        
        // Remove Vietnamese diacritics
        $fileName = $this->removeVietnameseTones($fileName);
        
        // Replace spaces with underscores
        $fileName = str_replace(' ', '_', $fileName);
        
        // Remove special characters except alphanumeric, underscore, dash
        $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '', $fileName);
        
        // Remove multiple consecutive underscores
        $fileName = preg_replace('/_+/', '_', $fileName);
        
        // Remove trailing underscores and dashes
        $fileName = rtrim($fileName, '_-');
        
        // Remove leading underscores and dashes
        $fileName = ltrim($fileName, '_-');
        
        // Final check: ensure no leading or trailing underscore
        while (substr($fileName, -1) === '_') {
            $fileName = rtrim($fileName, '_');
        }
        while (substr($fileName, 0, 1) === '_') {
            $fileName = ltrim($fileName, '_');
        }
        
        // Đảm bảo không rỗng sau khi clean
        return $fileName ?: 'file';
    }

    /**
     * Remove Vietnamese tones.
     */
    private function removeVietnameseTones(string $str): string
    {
        $map = [
            '/[àáạảãâầấậẩẫăằắặẳẵ]/u' => 'a',
            '/[èéẹẻẽêềếệểễ]/u' => 'e',
            '/[ìíịỉĩ]/u' => 'i',
            '/[òóọỏõôồốộổỗơờớợởỡ]/u' => 'o',
            '/[ùúụủũưừứựửữ]/u' => 'u',
            '/[ỳýỵỷỹ]/u' => 'y',
            '/[đ]/u' => 'd',
            '/[ÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴ]/u' => 'A',
            '/[ÈÉẸẺẼÊỀẾỆỂỄ]/u' => 'E',
            '/[ÌÍỊỈĨ]/u' => 'I',
            '/[ÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠ]/u' => 'O',
            '/[ÙÚỤỦŨƯỪỨỰỬỮ]/u' => 'U',
            '/[ỲÝỴỶỸ]/u' => 'Y',
            '/[Đ]/u' => 'D',
        ];
        return preg_replace(array_keys($map), array_values($map), $str);
    }
}
