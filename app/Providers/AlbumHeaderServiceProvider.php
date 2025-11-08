<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Album;
use App\Models\Set;

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
            $vipAlbums = Album::select('id', 'name', 'slug', 'image', 'icon')
                ->whereHas('albumSets.set', function($query) {
                    $query->where('type', Set::TYPE_PREMIUM)
                          ->where('status', Set::STATUS_ACTIVE);
                })
                ->orderBy('name', 'asc')
                ->get();

            $freeAlbums = Album::select('id', 'name', 'slug', 'image', 'icon')
                ->whereHas('albumSets.set', function($query) {
                    $query->where('type', Set::TYPE_FREE)
                          ->where('status', Set::STATUS_ACTIVE);
                })
                ->orderBy('name', 'asc')
                ->get();

            $view->with('headerVipAlbums', $vipAlbums);
            $view->with('headerFreeAlbums', $freeAlbums);
        });
    }
}
