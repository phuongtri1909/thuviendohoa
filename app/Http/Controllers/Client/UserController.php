<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function userProfile()
    {
        $user = auth()->user();
        return view('client.pages.user.profile')->with('user', $user);
    }

}
