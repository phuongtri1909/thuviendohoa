<?php

namespace App\Http\Controllers\Client;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Helpers\BannerHelper;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $bannerData = BannerHelper::getBannersDataForView('home');

        $categories = Category::orderBy('order', 'asc')->take(8)->get();

        return view('client.pages.home', $bannerData, compact('categories'));
    }
}
