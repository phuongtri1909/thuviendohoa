<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\ContentImage;

class ContentImageServiceProvider extends ServiceProvider
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
        View::composer('*', function ($view) {
            $content1 = ContentImage::byKey(ContentImage::KEY_CONTENT1)
                ->active()
                ->first();
            
            $content2 = ContentImage::byKey(ContentImage::KEY_CONTENT2)
                ->active()
                ->first();

            $view->with([
                'contentImage1' => $content1,
                'contentImage2' => $content2,
            ]);
        });
    }
}

