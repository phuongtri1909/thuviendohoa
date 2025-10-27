<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FileCleanupService;

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

    protected $cleanupService;

    public function __construct(FileCleanupService $cleanupService)
    {
        parent::__construct();
        $this->cleanupService = $cleanupService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        
        $this->info("Cleaning up download files older than {$hours} hours...");
        
        $downloadPath = storage_path('app/private/downloads');
        $zipDeleted = $this->cleanupService->cleanupByAge($downloadPath, $hours, false);
        
        $setsPath = storage_path('app/private/sets');
        $tempDeleted = $this->cleanupService->cleanupByAge($setsPath, $hours, true);
        
        $this->info("Cleanup completed!");
        $this->info("ZIP files deleted: {$zipDeleted['count']}");
        $this->info("ZIP space freed: " . $this->cleanupService->formatBytes($zipDeleted['size']));
        $this->info("Temp files deleted: {$tempDeleted['count']}");
        $this->info("Temp space freed: " . $this->cleanupService->formatBytes($tempDeleted['size']));
        $this->info("Total space freed: " . $this->cleanupService->formatBytes($zipDeleted['size'] + $tempDeleted['size']));
    }
}
