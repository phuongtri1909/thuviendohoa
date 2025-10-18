<?php

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\UserController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/search', function () {
    return view('client.pages.search');
})->name('search');

Route::get('/blog', function () {
    return view('client.pages.blog');
})->name('blog');

Route::get('/blog/{blog}', function () {
    return view('client.pages.blog-item');
})->name('blog.item');

Route::get('get-link', function () {
    return view('client.pages.get-link');
})->name('get.link');

Route::group(['middleware' => 'auth'], function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('profile', [UserController::class, 'userProfile'])->name('profile');
    });
});


Route::group(['middleware' => 'guest'], function () {
    Route::get('login', function () {
        return view('client.pages.auth.login');
    })->name('login');

    Route::post('login', [AuthController::class, 'login'])->name('login.post');

    Route::get('register', function () {
        return view('client.pages.auth.register');
    })->name('register');

    Route::post('register', [AuthController::class, 'register'])->name('register.post');

    Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});
