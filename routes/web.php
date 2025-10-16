<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\HomeController;


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
});


Route::group(['middleware' => 'guest'], function () {
    Route::get('login', function () {
        return view('client.pages.auth.login');
    })->name('login');

    Route::post('login', [AuthController::class, 'login'])->name('login.post');
});
