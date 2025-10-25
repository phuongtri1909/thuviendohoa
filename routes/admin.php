<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\SoftwareController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\AlbumController;
use App\Http\Controllers\Admin\SetController;
use App\Http\Controllers\Admin\SocialController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LogoSiteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PaymentCassoController;
use App\Http\Controllers\Admin\PurchaseSetController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

Route::group(['as' => 'admin.'], function () {
    Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        return 'Cache cleared';
    })->name('clear.cache');

    Route::group(['middleware' => ['auth', 'check.role:admin']], function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('logo-site', [LogoSiteController::class, 'edit'])->name('logo-site.edit');
        Route::put('logo-site', [LogoSiteController::class, 'update'])->name('logo-site.update');
        Route::delete('logo-site/delete-logo', [LogoSiteController::class, 'deleteLogo'])->name('logo-site.delete-logo');
        Route::delete('logo-site/delete-favicon', [LogoSiteController::class, 'deleteFavicon'])->name('logo-site.delete-favicon');

        Route::resource('socials', SocialController::class)->except(['show']);

        Route::get('setting', [SettingController::class, 'index'])->name('setting.index');
        Route::put('setting/smtp', [SettingController::class, 'updateSMTP'])->name('setting.update.smtp');
        Route::put('setting/google', [SettingController::class, 'updateGoogle'])->name('setting.update.google');
        Route::put('setting/facebook', [SettingController::class, 'updateFacebook'])->name('setting.update.facebook');
        Route::put('setting/twitter', [SettingController::class, 'updateTwitter'])->name('setting.update.twitter');

        Route::resource('seo', SeoController::class)->except(['show', 'create', 'store', 'destroy']);

        Route::resource('categories', CategoryController::class);

        Route::resource('colors', ColorController::class);

        Route::resource('software', SoftwareController::class);

        Route::resource('tags', TagController::class);

        Route::resource('albums', AlbumController::class);

        Route::resource('sets', SetController::class);

        Route::resource('banners', BannerController::class);

        Route::resource('banks', BankController::class)->except(['create', 'store', 'destroy']);

        Route::resource('packages', PackageController::class)->only(['index', 'edit', 'update']);

        Route::resource('payments', PaymentCassoController::class)->only(['index', 'show', 'destroy']);
        
        Route::resource('purchase-sets', PurchaseSetController::class)->only(['index', 'show']);
        
        Route::resource('users', AdminUserController::class)->only(['index', 'show']);
    });
});
