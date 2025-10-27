<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('client.pages.user.my-feedback', compact('feedbacks'));
    }
}

