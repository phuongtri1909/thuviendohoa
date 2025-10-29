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
        View::composer('components.client.content-image', function ($view) {
            $contentImage = ContentImage::byKey(ContentImage::KEY_CONTENT1)
                ->active()
                ->first();

            $view->with('contentImage', $contentImage);
        });

        View::composer('components.client.simple-content-image', function ($view) {
            $contentImage = ContentImage::byKey(ContentImage::KEY_CONTENT2)
                ->active()
                ->first();

            $view->with('contentImage', $contentImage);
        });
    }
}

