<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected $client;
    protected $drive;

    public function __construct()
    {
        try {
            $this->client = new GoogleClient();
            
            // Use JSON credentials file from environment variable
            $credentialsPath = storage_path(env('GOOGLE_DRIVE_CREDENTIALS_PATH', 'app/credentials/google-drive-credentials.json'));
            
            if (!file_exists($credentialsPath)) {
                throw new \Exception('Google Drive credentials file not found at: ' . $credentialsPath);
            }
            
            $this->client->setAuthConfig($credentialsPath);
            $this->client->addScope(GoogleDrive::DRIVE_READONLY);
            $this->drive = new GoogleDrive($this->client);
            
        } catch (\Exception $e) {
            Log::error('Google Drive Service initialization failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get folder ID from Drive URL
     * Example: https://drive.google.com/drive/folders/1ABC123xyz -> 1ABC123xyz
     */
    public function extractFolderIdFromUrl($url)
    {
        if (preg_match('/folders\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * List all files in a Google Drive folder
     * 
     * @param string $folderId - Google Drive folder ID
     * @return array - Array of file metadata
     */
    public function listFilesInFolder($folderId)
    {
        try {
            $query = "'{$folderId}' in parents and trashed = false";
            $parameters = [
                'q' => $query,
                'fields' => 'files(id, name, mimeType, size, webContentLink)',
            ];
            $results = $this->drive->files->listFiles($parameters);
            return $results->getFiles();
        } catch (\Exception $e) {
            Log::error('Error listing Drive files', [
                'folder_id' => $folderId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Download file from Google Drive to storage/app/private
     * 
     * @param string $fileId - Google Drive file ID
     * @param string $fileName - File name to save
     * @param string $setId - Set ID for organizing files
     * @return string - Path to downloaded file
     */
    public function downloadFile($fileId, $fileName, $setId)
    {
        try {
            // Check if file already exists in temp storage
            $setDir = storage_path("app/private/sets/{$setId}");
            $filePath = "{$setDir}/{$fileName}";
            
            if (file_exists($filePath)) {
                return "private/sets/{$setId}/{$fileName}";
            }
            
            // File doesn't exist, download from Drive
            
            $response = $this->drive->files->get($fileId, ['alt' => 'media']);
            $content = $response->getBody()->getContents();
            
            // Ensure directory exists
            if (!is_dir($setDir)) {
                mkdir($setDir, 0755, true);
            }
            
            // Save directly to filesystem
            file_put_contents($filePath, $content);
            
            return "private/sets/{$setId}/{$fileName}";
        } catch (\Exception $e) {
            Log::error('Error downloading Drive file', [
                'file_id' => $fileId,
                'file_name' => $fileName,
                'set_id' => $setId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Download all files from a folder and create a ZIP archive
     * 
     * @param string $folderUrl - Google Drive folder URL
     * @param string $setId - Set ID
     * @param string $setName - Set name for ZIP file
     * @return string - Path to ZIP file
     */
    public function downloadFolderAsZip($folderUrl, $setId, $setName)
    {
        try {
            $folderId = $this->extractFolderIdFromUrl($folderUrl);
            
            if (!$folderId) {
                throw new \Exception('Không tìm thấy thư mục trên Google Drive');
            }

            // Check if ZIP already exists for this set
            $downloadDir = storage_path('app/private/downloads');
            if (!is_dir($downloadDir)) {
                mkdir($downloadDir, 0755, true);
            }

            // Look for existing ZIP files for this set
            $existingZips = glob($downloadDir . "/set_{$setId}_*.zip");
            if (!empty($existingZips)) {
                // Use the most recent ZIP file
                $zipPath = $existingZips[0];
                $latestTime = filemtime($zipPath);
                
                foreach ($existingZips as $zip) {
                    if (filemtime($zip) > $latestTime) {
                        $zipPath = $zip;
                        $latestTime = filemtime($zip);
                    }
                }
                
                return $zipPath;
            }

            // No existing ZIP, create new one

            // Get all files in folder
            $files = $this->listFilesInFolder($folderId);
            
            if (empty($files)) {
                throw new \Exception('Không tìm thấy file trong thư mục');
            }

            // Create ZIP file
            $zipFileName = "set_{$setId}_" . time() . ".zip";
            $zipPath = storage_path("app/private/downloads/{$zipFileName}");

            $zip = new \ZipArchive();
            $result = $zip->open($zipPath, \ZipArchive::CREATE);
            if ($result !== true) {
                throw new \Exception("Cannot create ZIP file. Error code: {$result}");
            }

            // Download each file and add to ZIP
            foreach ($files as $file) {
                $tempPath = $this->downloadFile($file->id, $file->name, $setId);
                $fullPath = storage_path("app/{$tempPath}");
                
                if (file_exists($fullPath)) {
                    $zip->addFile($fullPath, $file->name);
                } else {
                    Log::warning("Không tìm thấy file tạm: {$fullPath}");
                }
            }

            $zip->close();

            return $zipPath;
        } catch (\Exception $e) {
            Log::error('Lỗi tạo ZIP từ thư mục trên Google Drive', [
                'folder_url' => $folderUrl,
                'set_id' => $setId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Stream ZIP file to client for download
     * 
     * @param string $zipPath - Full path to ZIP file
     * @param string $downloadName - Name for downloaded file
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function streamZipDownload($zipPath, $downloadName)
    {
        // Clean filename: remove special characters and trailing underscores
        $cleanName = $this->cleanFileName($downloadName);
        
        // Add timestamp
        $timestamp = date('Y-m-d_H-i-s');
        $finalName = $cleanName . '_' . $timestamp . '.zip';
        
        // Final check: ensure no trailing underscore before .zip
        if (substr($finalName, -5) === '_.zip') {
            $finalName = substr($finalName, 0, -5) . '.zip';
        }
        
        
        return response()->stream(function() use ($zipPath) {
            $stream = fopen($zipPath, 'rb');
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename=' . $finalName,
            'Content-Length' => filesize($zipPath),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
    
    /**
     * Clean filename by removing special characters and trailing underscores
     */
    private function cleanFileName($fileName)
    {
        $original = $fileName;
        
        // Remove file extension if exists
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
        
        
        return $fileName;
    }
    
    /**
     * Remove Vietnamese tones/diacritics
     */
    private function removeVietnameseTones($str)
    {
        $str = preg_replace('/[àáạảãâầấậẩẫăằắặẳẵ]/u', 'a', $str);
        $str = preg_replace('/[èéẹẻẽêềếệểễ]/u', 'e', $str);
        $str = preg_replace('/[ìíịỉĩ]/u', 'i', $str);
        $str = preg_replace('/[òóọỏõôồốộổỗơờớợởỡ]/u', 'o', $str);
        $str = preg_replace('/[ùúụủũưừứựửữ]/u', 'u', $str);
        $str = preg_replace('/[ỳýỵỷỹ]/u', 'y', $str);
        $str = preg_replace('/[đ]/u', 'd', $str);
        $str = preg_replace('/[ÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴ]/u', 'A', $str);
        $str = preg_replace('/[ÈÉẸẺẼÊỀẾỆỂỄ]/u', 'E', $str);
        $str = preg_replace('/[ÌÍỊỈĨ]/u', 'I', $str);
        $str = preg_replace('/[ÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠ]/u', 'O', $str);
        $str = preg_replace('/[ÙÚỤỦŨƯỪỨỰỬỮ]/u', 'U', $str);
        $str = preg_replace('/[ỲÝỴỶỸ]/u', 'Y', $str);
        $str = preg_replace('/[Đ]/u', 'D', $str);
        
        return $str;
    }
}

