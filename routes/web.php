<?php

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\UserController;
use App\Http\Controllers\Client\FacebookController;

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
        Route::post('update-profile/update-name', [UserController::class, 'updateName'])->name('update.name');
        Route::post('update-avatar', [UserController::class, 'updateAvatar'])->name('update.avatar');
        Route::post('update-password', [UserController::class, 'updatePassword'])->name('update.password');
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

    Route::get('/forgot-password', function () {
        return view('client.pages.auth.forgot-password');
    })->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot.password');

    Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    Route::get('login/facebook', [FacebookController::class, 'redirectToFacebook'])->name('login.facebook');
    Route::get('login/facebook/callback', [FacebookController::class, 'handleFacebookCallback'])->name('auth.facebook.callback');
});
