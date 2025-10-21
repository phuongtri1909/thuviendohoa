<?php

namespace App\Http\Controllers\Client;

use App\Models\Category;
use App\Models\Album;
use Illuminate\Http\Request;
use App\Helpers\BannerHelper;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $bannerData = BannerHelper::getBannersDataForView('home');

        $categories = Category::orderBy('order', 'asc')->take(8)->get();
        
        $featuredAlbums = Album::with('featuredType')
            ->featured()
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();
            
        $trendingAlbums = Album::with('trendingType')
            ->trending()
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return view('client.pages.home', array_merge($bannerData, compact('categories', 'featuredAlbums', 'trendingAlbums')));
    }
}
