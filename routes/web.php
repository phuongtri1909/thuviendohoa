<?php

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\UserController;
use App\Http\Controllers\Client\AlbumsController;
use App\Http\Controllers\Client\SearchController;
use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Client\TwitterController;
use App\Http\Controllers\Client\FacebookController;
use App\Http\Controllers\Client\PurchaseSetController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::post('/search/filter', [SearchController::class, 'filter'])->name('search.filter');
Route::get('/search/set/{setSlug}', [SearchController::class, 'getSetDetailsBySlug'])->name('search.set.details');
Route::get('/search/set/id/{setId}', [SearchController::class, 'getSetDetails'])->name('search.set.details.id');

Route::get('/albums', [AlbumsController::class, 'index'])->name('albums');

Route::get('/blog', function () {
    return view('client.pages.blog');
})->name('blog');

Route::get('/blog/{blog}', function () {
    return view('client.pages.blog-item');
})->name('blog.item');

Route::get('get-link', function () {
    return view('client.pages.get-link');
})->name('get.link');

Route::post('payment/casso/callback', [PaymentController::class, 'cassoCallback'])->name('payment.casso.callback');

Route::group(['middleware' => 'auth'], function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('profile', [UserController::class, 'userProfile'])->name('profile');
        Route::post('update-profile/update-name', [UserController::class, 'updateName'])->name('update.name');
        Route::post('update-avatar', [UserController::class, 'updateAvatar'])->name('update.avatar');
        Route::post('update-password', [UserController::class, 'updatePassword'])->name('update.password');

        Route::get('payment', [PaymentController::class, 'index'])->name('payment');
        Route::post('payment/store', [PaymentController::class, 'store'])->name('payment.store');
        Route::get('payment/sse', [PaymentController::class, 'sse'])->name('payment.sse');

        Route::get('favorites', [UserController::class, 'favorites'])->name('favorites');
        Route::post('favorites/add', [UserController::class, 'addFavorite'])->name('favorites.add');
        Route::post('favorites/remove', [UserController::class, 'removeFavorite'])->name('favorites.remove');

        // dùng ở search-result.blade.php
        Route::post('/search/set/{setId}/favorite', [UserController::class, 'toggleFavorite'])->name('search.set.favorite');

        // Purchase & Download routes
        Route::get('purchase/check/{setId}', [PurchaseSetController::class, 'checkDownloadCondition'])->name('purchase.check');
        Route::post('purchase/confirm/{setId}', [PurchaseSetController::class, 'confirmPurchase'])->name('purchase.confirm');
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

    Route::get('auth/twitter', [TwitterController::class, 'redirectToTwitter'])->name('login.twitter');
    Route::get('auth/twitter/callback', [TwitterController::class, 'handleTwitterCallback'])->name('auth.twitter.callback');
});
