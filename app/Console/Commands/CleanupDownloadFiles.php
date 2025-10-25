<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupDownloadFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:download-files {--hours=24 : Delete files older than X hours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old download ZIP files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $cutoffTime = Carbon::now()->subHours($hours);
        
        $this->info("Cleaning up download files older than {$hours} hours...");
        
        $downloadPath = storage_path('app/private/downloads');
        $zipDeleted = $this->cleanupDirectory($downloadPath, '*.zip', $cutoffTime, 'ZIP files');
        
        $setsPath = storage_path('app/private/sets');
        $tempDeleted = $this->cleanupDirectory($setsPath, '*', $cutoffTime, 'Temp files', true);
        
        $this->cleanupEmptyDirectories($setsPath);
        
        $this->info("Cleanup completed!");
        $this->info("ZIP files deleted: {$zipDeleted['count']}");
        $this->info("ZIP space freed: " . $this->formatBytes($zipDeleted['size']));
        $this->info("Temp files deleted: {$tempDeleted['count']}");
        $this->info("Temp space freed: " . $this->formatBytes($tempDeleted['size']));
        $this->info("Total space freed: " . $this->formatBytes($zipDeleted['size'] + $tempDeleted['size']));
    }
    
    private function cleanupDirectory($directory, $pattern, $cutoffTime, $description, $recursive = false)
    {
        if (!is_dir($directory)) {
            $this->info("{$description} directory does not exist. Nothing to clean.");
            return ['count' => 0, 'size' => 0];
        }
        
        $deletedCount = 0;
        $totalSize = 0;
        
        if ($recursive) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $fileTime = Carbon::createFromTimestamp($file->getMTime());
                    
                    if ($fileTime->lt($cutoffTime)) {
                        $fileSize = $file->getSize();
                        $totalSize += $fileSize;
                        
                        if (unlink($file->getPathname())) {
                            $deletedCount++;
                            $this->line("Deleted: " . $file->getPathname() . " (" . $this->formatBytes($fileSize) . ")");
                        } else {
                            $this->error("Failed to delete: " . $file->getPathname());
                        }
                    }
                }
            }
        } else {
            $files = glob($directory . '/' . $pattern);
            
            foreach ($files as $file) {
                $fileTime = Carbon::createFromTimestamp(filemtime($file));
                
                if ($fileTime->lt($cutoffTime)) {
                    $fileSize = filesize($file);
                    $totalSize += $fileSize;
                    
                    if (unlink($file)) {
                        $deletedCount++;
                        $this->line("Deleted: " . basename($file) . " (" . $this->formatBytes($fileSize) . ")");
                    } else {
                        $this->error("Failed to delete: " . basename($file));
                    }
                }
            }
        }
        
        return ['count' => $deletedCount, 'size' => $totalSize];
    }
    
    private function cleanupEmptyDirectories($directory)
    {
        if (!is_dir($directory)) {
            return;
        }
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        $deletedDirs = 0;
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                $dirPath = $file->getPathname();
                
                $files = scandir($dirPath);
                if (count($files) === 2) {
                    if (rmdir($dirPath)) {
                        $deletedDirs++;
                        $this->line("Deleted empty directory: " . $dirPath);
                    }
                }
            }
        }
        
        if ($deletedDirs > 0) {
            $this->info("Empty directories deleted: {$deletedDirs}");
        }
    }
    
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}
