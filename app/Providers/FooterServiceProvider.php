<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Page;
use App\Models\FooterSetting;

class FooterServiceProvider extends ServiceProvider
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
            if (Schema::hasTable('pages') && Schema::hasTable('footer_settings')) {
                // Share footer pages
                $footerPages = Page::where('status', 1)
                    ->orderBy('order', 'asc')
                    ->get(['id', 'title', 'slug']);
                
                View::share('footerPages', $footerPages);

                // Share footer setting
                $footerSetting = FooterSetting::first();
                View::share('footerSetting', $footerSetting);
            }
        } catch (\Exception $e) {
            // Silently catch exceptions during migration
        }
    }
}
