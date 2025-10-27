<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class FileCleanupService
{
    /**
     * Clean up files in a directory older than specified hours
     */
    public function cleanupByAge(string $directory, int $hours = 24, bool $recursive = false): array
    {
        if (!is_dir($directory)) {
            return ['count' => 0, 'size' => 0, 'files' => []];
        }

        $cutoffTime = Carbon::now()->subHours($hours);
        $deletedCount = 0;
        $totalSize = 0;
        $deletedFiles = [];

        if ($recursive) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $fileTime = Carbon::createFromTimestamp($file->getMTime());

                    if ($fileTime->lt($cutoffTime)) {
                        $fileSize = $file->getSize();
                        $filePath = $file->getPathname();

                        if (@unlink($filePath)) {
                            $deletedCount++;
                            $totalSize += $fileSize;
                            $deletedFiles[] = $filePath;
                        }
                    }
                }
            }
        } else {
            $files = File::files($directory);

            foreach ($files as $file) {
                $fileTime = Carbon::createFromTimestamp($file->getMTime());

                if ($fileTime->lt($cutoffTime)) {
                    $fileSize = $file->getSize();
                    $filePath = $file->getPathname();

                    if (@unlink($filePath)) {
                        $deletedCount++;
                        $totalSize += $fileSize;
                        $deletedFiles[] = $filePath;
                    }
                }
            }
        }

        $this->cleanupEmptyDirectories($directory);

        return [
            'count' => $deletedCount,
            'size' => $totalSize,
            'files' => $deletedFiles
        ];
    }

    /**
     * Clean up all files in a specific set directory
     */
    public function cleanupSetFiles(int $setId): array
    {
        $setDirectory = storage_path("app/private/sets/{$setId}");

        if (!is_dir($setDirectory)) {
            return ['count' => 0, 'size' => 0, 'files' => []];
        }

        $deletedCount = 0;
        $totalSize = 0;
        $deletedFiles = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($setDirectory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $fileSize = $file->getSize();
                $filePath = $file->getPathname();

                if (@unlink($filePath)) {
                    $deletedCount++;
                    $totalSize += $fileSize;
                    $deletedFiles[] = $filePath;
                }
            }
        }

        $this->cleanupEmptyDirectories($setDirectory);

        // Remove the set directory itself if empty
        if (is_dir($setDirectory) && $this->isDirectoryEmpty($setDirectory)) {
            @rmdir($setDirectory);
        }

        return [
            'count' => $deletedCount,
            'size' => $totalSize,
            'files' => $deletedFiles
        ];
    }

    /**
     * Clean up specific set's ZIP file(s) in downloads directory
     * Handles multiple ZIP files with timestamp pattern: set_{$setId}_*.zip
     */
    public function cleanupSetZip(int $setId): array
    {
        $downloadDir = storage_path('app/private/downloads');
        $pattern = "{$downloadDir}/set_{$setId}_*.zip";
        $zipFiles = glob($pattern);

        $totalSize = 0;
        $deletedCount = 0;

        if (!empty($zipFiles)) {
            foreach ($zipFiles as $zipPath) {
                if (file_exists($zipPath)) {
                    $zipSize = filesize($zipPath);
                    if (@unlink($zipPath)) {
                        $totalSize += $zipSize;
                        $deletedCount++;
                    }
                }
            }
        }

        return [
            'deleted' => $deletedCount > 0, 
            'size' => $totalSize,
            'count' => $deletedCount
        ];
    }

    /**
     * Clean up both set files and ZIP for a specific set
     */
    public function cleanupSetCompletely(int $setId): array
    {
        $setFilesResult = $this->cleanupSetFiles($setId);
        $zipResult = $this->cleanupSetZip($setId);

        return [
            'set_files' => $setFilesResult,
            'zip' => $zipResult,
            'total_count' => $setFilesResult['count'] + $zipResult['count'],
            'total_size' => $setFilesResult['size'] + $zipResult['size']
        ];
    }

    /**
     * Remove empty directories recursively
     */
    private function cleanupEmptyDirectories(string $directory): int
    {
        if (!is_dir($directory)) {
            return 0;
        }

        $deletedDirs = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                $dirPath = $file->getPathname();

                if ($this->isDirectoryEmpty($dirPath)) {
                    if (@rmdir($dirPath)) {
                        $deletedDirs++;
                    }
                }
            }
        }

        return $deletedDirs;
    }

    /**
     * Check if directory is empty
     */
    private function isDirectoryEmpty(string $directory): bool
    {
        if (!is_dir($directory)) {
            return false;
        }

        $files = scandir($directory);
        return count($files) === 2; // Only . and ..
    }

    /**
     * Format bytes to human readable format
     */
    public function formatBytes(int $size, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }
}

