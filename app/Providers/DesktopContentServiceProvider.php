<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\DesktopContent;

class DesktopContentServiceProvider extends ServiceProvider
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
        View::composer('components.client.desktop', function ($view) {
            $desktopContent = DesktopContent::byKey(DesktopContent::KEY_DESKTOP)
                ->active()
                ->first();

            $view->with('desktopContent', $desktopContent);
        });
    }
}

