<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Album;
use App\Models\Category;

class AlbumHeaderServiceProvider extends ServiceProvider
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
        View::composer('client.layouts.partials.header', function ($view) {
            // VIP: Load tất cả Albums
            $vipAlbums = Album::select('id', 'name', 'slug', 'image', 'icon')
                ->orderBy('name', 'asc')
                ->get();

            // FREE: Load tất cả Categories
            $freeCategories = Category::select('id', 'name', 'slug', 'image')
                ->orderBy('name', 'asc')
                ->get();

            $view->with('headerVipAlbums', $vipAlbums);
            $view->with('headerFreeCategories', $freeCategories);
        });
    }
}
