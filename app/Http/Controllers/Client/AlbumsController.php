<?php

namespace App\Http\Controllers\Client;

use App\Models\Album;
use App\Models\Banner;
use App\Helpers\BannerHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AlbumsController extends Controller
{
    public function index(Request $request)
    {
        $bannerData = BannerHelper::getBannersDataForView('albums');
        
        $query = Album::query();
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $albums = $query->orderBy('created_at', 'desc')->paginate(30);
        
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