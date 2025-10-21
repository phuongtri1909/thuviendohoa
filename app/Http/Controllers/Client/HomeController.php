<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\BannerHelper;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $bannerData = BannerHelper::getBannersDataForView('home');
        
        return view('client.pages.home', $bannerData);
    }
}
