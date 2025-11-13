<?php

namespace App\Http\Controllers\Client;

use App\Models\Album;
use App\Models\Banner;
use App\Models\SeoSetting;
use App\Helpers\BannerHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;

class AlbumsController extends Controller
{
    public function index(Request $request)
    {
        // SEO for albums page
        $seoSetting = SeoSetting::getByPageKey('albums');
        
        if ($seoSetting) {
            SEOTools::setTitle($seoSetting->title);
            SEOTools::setDescription($seoSetting->description);
            SEOMeta::setKeywords($seoSetting->keywords);
            SEOTools::setCanonical(url()->current());

            OpenGraph::setTitle($seoSetting->title);
            OpenGraph::setDescription($seoSetting->description);
            OpenGraph::setUrl(url()->current());
            OpenGraph::addProperty('type', 'website');
            if ($seoSetting->thumbnail) {
                OpenGraph::addImage($seoSetting->thumbnail_url);
            }

            TwitterCard::setTitle($seoSetting->title);
            TwitterCard::setDescription($seoSetting->description);
            if ($seoSetting->thumbnail) {
                TwitterCard::addImage($seoSetting->thumbnail_url);
            }
        }
        
        $bannerData = BannerHelper::getBannersDataForView('albums');
        
        $query = Album::query();
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $albums = $query->orderBy('order', 'asc')->orderBy('created_at', 'desc')->paginate(30);
        
        if ($request->ajax()) {
            return response()->json([
                'albums' => $albums->items(),
                'hasMore' => $albums->hasMorePages(),
                'nextPage' => $albums->currentPage() + 1
            ]);
        }
        
        return view('client.pages.albums', array_merge($bannerData, compact('albums')));
    }
}