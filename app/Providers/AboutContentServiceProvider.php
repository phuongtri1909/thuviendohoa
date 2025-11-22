<?php

namespace App\Providers;

use App\Models\AboutContent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AboutContentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            if (Schema::hasTable('about_contents')) {
                $aboutContents = AboutContent::all()->keyBy('key');
                View::share('aboutContents', $aboutContents);
            }
        } catch (\Exception $e) {
            Log::error('Error loading about contents: ' . $e->getMessage());
        }
    }
}
