<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;

class UserFeedbackController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $feedbacks = Feedback::where('email', $user->email)
            ->orWhere(function($query) use ($user) {
                $query->where('name', $user->name)
                      ->whereNotNull('name');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // SEO for my feedback
        $title = 'Góp ý của tôi - ' . config('app.name');
        $description = 'Xem các góp ý và phản hồi bạn đã gửi cho chúng tôi.';
        $keywords = 'gop y, feedback, phan hoi';
        
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOMeta::setKeywords($keywords);

        return view('client.pages.user.my-feedback', compact('feedbacks'));
    }
}

