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
     * List all files in a Drive folder (non-recursive).
     */
    public function listFilesInFolder(string $folderId): array
    {
        $params = [
            'q' => "'{$folderId}' in parents and trashed = false",
            'fields' => 'files(id, name, mimeType, size)',
            'pageSize' => 1000
        ];

        $results = $this->drive->files->listFiles($params);
        return $results->getFiles() ?? [];
    }

    /**
     * Download a single Drive file with streaming (memory-safe).
     */
    public function downloadFile(string $fileId, string $fileName, string $setId): string
    {
        $setDir = storage_path("app/private/sets/{$setId}");
        $filePath = "{$setDir}/{$fileName}";

        if (file_exists($filePath) && filesize($filePath) > 0) {
            return "private/sets/{$setId}/{$fileName}";
        }

        if (!is_dir($setDir)) {
            mkdir($setDir, 0755, true);
        }

        try {
            $response = $this->drive->files->get($fileId, ['alt' => 'media']);
            $body = $response->getBody();
            
            $dest = fopen($filePath, 'wb');
            if (!$dest) {
                throw new Exception("Cannot open file for writing: {$filePath}");
            }

            $totalWritten = 0;
            while (!$body->eof()) {
                $chunk = $body->read(8192);
                if ($chunk === '' || $chunk === false) {
                    break;
                }
                
                fwrite($dest, $chunk);
                $totalWritten += strlen($chunk);
                
                if ($totalWritten % (10 * 1024 * 1024) === 0) {
                    unset($chunk);
                    gc_collect_cycles();
                }
            }
            
            fclose($dest);

            if (!file_exists($filePath) || filesize($filePath) === 0) {
                throw new Exception("Failed to download file: {$fileName}");
            }

        } catch (\Throwable $e) {
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            throw new Exception("Error downloading file '{$fileName}': " . $e->getMessage());
        }

        return "private/sets/{$setId}/{$fileName}";
    }

    /**
     * Download folder as ZIP with FILE LOCK to prevent duplicate creation.
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

        $lockFile = "{$downloadDir}/set_{$setId}.lock";
        $lockHandle = fopen($lockFile, 'c');
        
        if (!$lockHandle) {
            throw new Exception('Cannot create lock file');
        }

        try {
            if (!flock($lockHandle, LOCK_EX | LOCK_NB)) {
                Log::info("Waiting for another process to finish creating ZIP", [
                    'set_id' => $setId
                ]);
                
                $waited = 0;
                $maxWait = 600;
                
                while (!flock($lockHandle, LOCK_EX | LOCK_NB) && $waited < $maxWait) {
                    sleep(2);
                    $waited += 2;
                    
                    $existingZip = $this->findExistingZip($downloadDir, $setId);
                    if ($existingZip) {
                        Log::info("ZIP created by another process", [
                            'set_id' => $setId,
                            'zip_path' => $existingZip
                        ]);
                        return $existingZip;
                    }
                }
                
                if ($waited >= $maxWait) {
                    throw new Exception('Timeout waiting for ZIP creation');
                }
            }

            $existingZip = $this->findExistingZip($downloadDir, $setId);
            if ($existingZip) {
                Log::info("ZIP already exists", [
                    'set_id' => $setId,
                    'zip_path' => $existingZip
                ]);
                return $existingZip;
            }

            $this->cleanupOldZips($downloadDir);

            $files = $this->listFilesInFolder($folderId);
            
            if (empty($files)) {
                throw new Exception('Không tìm thấy file nào trong thư mục Drive.');
            }

            Log::info("Creating new ZIP", [
                'set_id' => $setId,
                'set_name' => $setName,
                'files_count' => count($files)
            ]);

            $tempSetDir = storage_path("app/private/sets/{$setId}");
            if (is_dir($tempSetDir)) {
                $this->deleteDirectory($tempSetDir);
            }

            $zipFileName = "set_{$setId}_" . time() . ".zip";
            $zipPath = "{$downloadDir}/{$zipFileName}";

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new Exception('Không thể tạo file ZIP.');
            }

            $successCount = 0;
            $failCount = 0;

            foreach ($files as $file) {
                try {
                    $tempPath = $this->downloadFile($file->id, $file->name, $setId);
                    $fullPath = storage_path("app/{$tempPath}");
                    
                    if (file_exists($fullPath)) {
                        $zip->addFile($fullPath, $file->name);
                        
                        $extension = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));
                        $compressedTypes = ['psd', 'tiff', 'tif', 'bmp', 'svg', 'txt', 'xml'];
                        
                        if (!in_array($extension, $compressedTypes)) {
                            $zip->setCompressionName($file->name, ZipArchive::CM_STORE);
                        }
                        
                        $successCount++;
                    }
                } catch (\Throwable $e) {
                    $failCount++;
                    Log::warning('Error adding file to ZIP', [
                        'file_id' => $file->id,
                        'file_name' => $file->name,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $zip->close();

            if ($successCount === 0) {
                @unlink($zipPath);
                throw new Exception('Không thể thêm file nào vào ZIP.');
            }

            if (is_dir($tempSetDir)) {
                $this->deleteDirectory($tempSetDir);
            }

            Log::info('ZIP created successfully', [
                'set_id' => $setId,
                'zip_path' => $zipPath,
                'zip_size' => filesize($zipPath),
                'success_count' => $successCount,
                'fail_count' => $failCount
            ]);

            return $zipPath;

        } finally {
            if (is_resource($lockHandle)) {
                flock($lockHandle, LOCK_UN);
                fclose($lockHandle);
            }
            @unlink($lockFile);
        }
    }

    /**
     * Find existing ZIP for a set.
     */
    private function findExistingZip(string $downloadDir, string $setId): ?string
    {
        $existingZips = glob("{$downloadDir}/set_{$setId}_*.zip");
        
        foreach ($existingZips as $zipPath) {
            if (file_exists($zipPath) && filesize($zipPath) > 0) {
                return $zipPath;
            }
        }
        
        return null;
    }

    /**
     * Cleanup ZIPs older than 24 hours.
     */
    private function cleanupOldZips(string $downloadDir): void
    {
        $cutoff = time() - 86400;
        $cleaned = 0;
        
        foreach (glob("{$downloadDir}/*.zip") as $zipPath) {
            if (filemtime($zipPath) < $cutoff) {
                if (@unlink($zipPath)) {
                    $cleaned++;
                    Log::info('Cleaned up old ZIP', [
                        'file' => basename($zipPath)
                    ]);
                }
            }
        }
        
        if ($cleaned > 0) {
            Log::info("Cleaned up {$cleaned} old ZIP files");
        }
    }

    /**
     * Delete directory and contents recursively.
     */
    private function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir) ?: [], ['.', '..']);
        foreach ($files as $file) {
            $path = "{$dir}/{$file}";
            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }

        return @rmdir($dir);
    }

    /**
     * Stream ZIP file to client with optimized headers.
     */
    public function streamZipDownload(string $zipPath, string $downloadName)
    {
        if (!file_exists($zipPath)) {
            abort(404, 'File không tồn tại.');
        }

        $cleanName = $this->cleanFileName($downloadName);
        $timestamp = date('Ymd_His');
        $finalName = ($cleanName ?: 'download') . '_' . $timestamp . '.zip';

        set_time_limit(0);
        ignore_user_abort(false);

        return response()->stream(function () use ($zipPath) {
            $stream = fopen($zipPath, 'rb');
            if (!$stream) {
                return;
            }

            while (!feof($stream)) {
                if (connection_aborted()) {
                    break;
                }
                
                echo fread($stream, 1024 * 1024);
                ob_flush();
                flush();
            }
            
            fclose($stream);
        }, 200, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $finalName . '"',
            'Content-Length' => filesize($zipPath),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Clean filename for safe download.
     */
    private function cleanFileName(string $fileName): string
    {
        $fileName = pathinfo($fileName, PATHINFO_FILENAME);
        $fileName = $this->removeVietnameseTones($fileName);
        $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fileName);
        $fileName = preg_replace('/_+/', '_', $fileName);
        $fileName = trim($fileName, '_-');
        
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