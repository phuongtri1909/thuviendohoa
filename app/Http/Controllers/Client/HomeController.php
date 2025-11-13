<?php

namespace App\Http\Controllers\Client;

use App\Models\Category;
use App\Models\Album;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use App\Helpers\BannerHelper;
use App\Http\Controllers\Controller;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Load SEO settings from database for home page
        $seoSetting = SeoSetting::getByPageKey('home');
        
        if ($seoSetting) {
            SEOTools::setTitle($seoSetting->title);
            SEOTools::setDescription($seoSetting->description);
            SEOMeta::setKeywords($seoSetting->keywords);
            SEOTools::setCanonical(url()->current());

            OpenGraph::setTitle($seoSetting->title);
            OpenGraph::setDescription($seoSetting->description);
            OpenGraph::setUrl(url()->current());
            OpenGraph::setSiteName(config('app.name'));
            OpenGraph::addProperty('type', 'website');
            OpenGraph::addProperty('locale', 'vi_VN');
            if ($seoSetting->thumbnail) {
                OpenGraph::addImage($seoSetting->thumbnail_url);
            }

            TwitterCard::setTitle($seoSetting->title);
            TwitterCard::setDescription($seoSetting->description);
            TwitterCard::setType('summary_large_image');
            if ($seoSetting->thumbnail) {
                TwitterCard::addImage($seoSetting->thumbnail_url);
            }
        } else {
            // Fallback SEO
            SEOTools::setTitle('Trang chủ - ' . config('app.name'));
            SEOTools::setDescription('Thư viện đồ họa miễn phí và chất lượng cao');
            SEOTools::setCanonical(url()->current());

            OpenGraph::setTitle('Trang chủ - ' . config('app.name'));
            OpenGraph::setDescription('Thư viện đồ họa miễn phí và chất lượng cao');
            OpenGraph::setUrl(url()->current());
            OpenGraph::setSiteName(config('app.name'));
            OpenGraph::addProperty('type', 'website');
            OpenGraph::addProperty('locale', 'vi_VN');

            TwitterCard::setTitle('Trang chủ - ' . config('app.name'));
            TwitterCard::setType('summary_large_image');
        }
        
        $bannerData = BannerHelper::getBannersDataForView('home');

        $categories = Category::orderBy('order', 'asc')->take(8)->get();
        
        $featuredAlbums = Album::with('featuredType')
            ->featured()
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();
            
        $trendingAlbums = Album::with('trendingType')
            ->trending()
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return view('client.pages.home', array_merge($bannerData, compact('categories', 'featuredAlbums', 'trendingAlbums')));
    }
}
