<?php

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\BlogController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\UserController;
use App\Http\Controllers\Client\AlbumsController;
use App\Http\Controllers\Client\SearchController;
use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Client\TwitterController;
use App\Http\Controllers\Client\FacebookController;
use App\Http\Controllers\Client\PurchaseSetController;
use App\Http\Controllers\Client\GetLinkController;
use App\Http\Controllers\Client\PageController as ClientPageController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Page routes
Route::get('/page/{slug}', [ClientPageController::class, 'show'])->name('page.show');

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::post('/search/filter', [SearchController::class, 'filter'])->name('search.filter');
Route::get('/search/set/{setSlug}', [SearchController::class, 'getSetDetailsBySlug'])->name('search.set.details');
Route::get('/search/set/id/{setId}', [SearchController::class, 'getSetDetails'])->name('search.set.details.id');

Route::get('/albums', [AlbumsController::class, 'index'])->name('albums');

Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/search/ajax', [BlogController::class, 'search'])->name('blog.search');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.item');

Route::get('get-link', [GetLinkController::class, 'index'])->name('get.link');

Route::post('payment/casso/callback', [PaymentController::class, 'cassoCallback'])->name('payment.casso.callback');

// Feedback routes
Route::post('feedback', [\App\Http\Controllers\Client\FeedbackController::class, 'store'])->name('feedback.store');
Route::get('feedback/captcha', [\App\Http\Controllers\Client\FeedbackController::class, 'generateCaptcha'])->name('feedback.captcha');

Route::group(['middleware' => 'auth'], function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('get-link/process', [GetLinkController::class, 'processGetLink'])->name('get.link.process');
    Route::get('get-link/config', [GetLinkController::class, 'getConfig'])->name('get.link.config');

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

        Route::get('purchases', [UserController::class, 'purchases'])->name('purchases');

        // dùng ở search-result.blade.php
        Route::post('/search/set/{setId}/favorite', [UserController::class, 'toggleFavorite'])->name('search.set.favorite');

        // Purchase & Download routes
        Route::get('purchase/check/{setId}', [PurchaseSetController::class, 'checkDownloadCondition'])->name('purchase.check');
        Route::post('purchase/confirm/{setId}', [PurchaseSetController::class, 'confirmPurchase'])->name('purchase.confirm');
        
        // Coin History routes
        Route::get('coin-history', [\App\Http\Controllers\Client\CoinHistoryController::class, 'index'])->name('coin-history');
        Route::post('coin-history/mark-read', [\App\Http\Controllers\Client\CoinHistoryController::class, 'markAsRead'])->name('coin-history.mark-read');
        Route::get('coin-history/unread-count', [\App\Http\Controllers\Client\CoinHistoryController::class, 'getUnreadCount'])->name('coin-history.unread-count');
        Route::get('coin-history/unread', [\App\Http\Controllers\Client\CoinHistoryController::class, 'getUnreadHistories'])->name('coin-history.unread');
        
        // User Feedback routes
        Route::get('my-feedback', [\App\Http\Controllers\Client\UserFeedbackController::class, 'index'])->name('my-feedback');       
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
