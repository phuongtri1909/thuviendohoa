<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupBlogTempImages extends Command
{
    protected $signature = 'blog:cleanup-temp-images {--hours=24 : Hours old for temp images to be deleted}';
    protected $description = 'Clean up temporary blog images older than specified hours';

    public function handle()
    {
        $hours = $this->option('hours');
        $cutoffTime = Carbon::now()->subHours($hours);
        
        $this->info("Cleaning up temp blog images older than {$hours} hours...");
        
        $deletedCount = 0;
        $totalSize = 0;
        
        $tempDirs = $this->findTempDirectories();
        
        foreach ($tempDirs as $tempDir) {
            $files = Storage::disk('public')->files($tempDir);
            
            foreach ($files as $file) {
                $fileTime = Carbon::createFromTimestamp(Storage::disk('public')->lastModified($file));
                
                if ($fileTime->lt($cutoffTime) && strpos($file, 'temp_') !== false) {
                    $fileSize = Storage::disk('public')->size($file);
                    
                    if (Storage::disk('public')->delete($file)) {
                        $deletedCount++;
                        $totalSize += $fileSize;
                        $this->line("Deleted: {$file}");
                    }
                }
            }
            
            // Remove empty temp directories
            if (empty(Storage::disk('public')->allFiles($tempDir))) {
                Storage::disk('public')->deleteDirectory($tempDir);
                $this->line("Removed empty directory: {$tempDir}");
            }
        }
        
        $sizeInMB = round($totalSize / 1024 / 1024, 2);
        $this->info("Cleanup completed! Deleted {$deletedCount} files ({$sizeInMB} MB).");
        
        return 0;
    }
    
    private function findTempDirectories()
    {
        $tempDirs = [];
        $blogDirs = Storage::disk('public')->directories('blogs');
        
        foreach ($blogDirs as $yearDir) {
            $monthDirs = Storage::disk('public')->directories($yearDir);
            foreach ($monthDirs as $monthDir) {
                $subDirs = Storage::disk('public')->directories($monthDir);
                foreach ($subDirs as $subDir) {
                    if (basename($subDir) === 'temp') {
                        $tempDirs[] = $subDir;
                    }
                }
            }
        }
        
        return $tempDirs;
    }
}
