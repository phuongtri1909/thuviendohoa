<?php

namespace App\Helpers;

use App\Models\Banner;

class BannerHelper
{
    /**
     * Get banners for a specific page
     */
    public static function getBannersForPage(string $page): \Illuminate\Database\Eloquent\Collection
    {
        return Banner::forPage($page)->get();
    }

    /**
     * Get banners for home page
     */
    public static function getHomeBanners(): \Illuminate\Database\Eloquent\Collection
    {
        return self::getBannersForPage(Banner::PAGE_HOME);
    }

    /**
     * Get banners for search page
     */
    public static function getSearchBanners(): \Illuminate\Database\Eloquent\Collection
    {
        return self::getBannersForPage(Banner::PAGE_SEARCH);
    }

    /**
     * Get banners data for view
     */
    public static function getBannersDataForView(string $page): array
    {
        $banners = self::getBannersForPage($page);
        
        return [
            'banners' => $banners,
            'has_banners' => $banners->count() > 0,
        ];
    }
}
