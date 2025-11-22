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
use App\Http\Controllers\Admin\CoinController;
use App\Http\Controllers\Admin\CoinHistoryController;
use App\Http\Controllers\Admin\MonthlyBonusController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CategoryBlogController;
use App\Http\Controllers\Admin\TagBlogController;
use App\Http\Controllers\Admin\GetLinkConfigController;
use App\Http\Controllers\Admin\GetLinkHistoryController;
use App\Http\Controllers\Admin\ContentImageController;
use App\Http\Controllers\Admin\DesktopContentController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\FooterSettingController;
use App\Http\Controllers\Admin\AboutContentController;

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
        Route::post('sets/{set}/clean-files', [SetController::class, 'cleanFiles'])->name('sets.clean-files');

        Route::resource('banners', BannerController::class);

        Route::resource('banks', BankController::class)->except(['create', 'store', 'destroy']);

        Route::resource('packages', PackageController::class)->only(['index', 'edit', 'update']);

        Route::resource('payments', PaymentCassoController::class)->only(['index', 'show', 'destroy']);
        
        Route::resource('purchase-sets', PurchaseSetController::class)->only(['index', 'show']);
        
        Route::resource('users', AdminUserController::class)->only(['index', 'show']);
        
                Route::resource('coins', CoinController::class)->except(['edit', 'update', 'destroy']);
        Route::get('coins/package-users/{packageId}', [CoinController::class, 'getPackageUsers'])->name('coins.package-users');
        
        Route::resource('monthly-bonuses', MonthlyBonusController::class)->only(['index', 'show']);
        Route::resource('coin-histories', CoinHistoryController::class)->only(['index', 'show']);

        // Feedback routes
        Route::resource('feedback', \App\Http\Controllers\Admin\FeedbackController::class);
        Route::post('feedback/{id}/reply', [\App\Http\Controllers\Admin\FeedbackController::class, 'reply'])->name('feedback.reply');
        Route::post('feedback/{id}/mark-read', [\App\Http\Controllers\Admin\FeedbackController::class, 'markAsRead'])->name('feedback.mark-read');
        
        // Blog routes
        Route::resource('blogs', BlogController::class);
        Route::post('blogs/upload-image', [BlogController::class, 'uploadImage'])->name('blogs.upload-image');
        Route::post('blogs/cleanup-temp-images', [BlogController::class, 'cleanupTempImages'])->name('blogs.cleanup-temp-images');
        
        Route::resource('category-blogs', CategoryBlogController::class);
        Route::resource('tag-blogs', TagBlogController::class);
        
        Route::get('blog-sidebar-setting', [\App\Http\Controllers\Admin\BlogSidebarSettingController::class, 'edit'])->name('blog-sidebar-setting.edit');
        Route::put('blog-sidebar-setting', [\App\Http\Controllers\Admin\BlogSidebarSettingController::class, 'update'])->name('blog-sidebar-setting.update');
        Route::delete('blog-sidebar-setting/banner', [\App\Http\Controllers\Admin\BlogSidebarSettingController::class, 'deleteBanner'])->name('blog-sidebar-setting.delete-banner');
        
        Route::get('get-link-config', [GetLinkConfigController::class, 'edit'])->name('get-link-config.edit');
        Route::put('get-link-config', [GetLinkConfigController::class, 'update'])->name('get-link-config.update');
        
        Route::get('get-link-histories', [GetLinkHistoryController::class, 'index'])->name('get-link-histories.index');
        Route::get('get-link-histories/{id}', [GetLinkHistoryController::class, 'show'])->name('get-link-histories.show');
        Route::delete('get-link-histories/{id}', [GetLinkHistoryController::class, 'destroy'])->name('get-link-histories.destroy');
        
        Route::get('content-images', [ContentImageController::class, 'index'])->name('content-images.index');
        Route::get('content-images/{contentImage}/edit', [ContentImageController::class, 'edit'])->name('content-images.edit');
        Route::put('content-images/{contentImage}', [ContentImageController::class, 'update'])->name('content-images.update');
        Route::delete('content-images/{contentImage}/delete-image', [ContentImageController::class, 'deleteImage'])->name('content-images.delete-image');
        
        Route::get('desktop-contents', [DesktopContentController::class, 'index'])->name('desktop-contents.index');
        Route::get('desktop-contents/{desktopContent}/edit', [DesktopContentController::class, 'edit'])->name('desktop-contents.edit');
        Route::put('desktop-contents/{desktopContent}', [DesktopContentController::class, 'update'])->name('desktop-contents.update');
        Route::delete('desktop-contents/{desktopContent}/delete-logo', [DesktopContentController::class, 'deleteLogo'])->name('desktop-contents.delete-logo');
        Route::delete('desktop-contents/{desktopContent}/delete-feature-icon', [DesktopContentController::class, 'deleteFeatureIcon'])->name('desktop-contents.delete-feature-icon');
        
        // Pages routes
        Route::resource('pages', PageController::class);
        Route::post('pages/upload-image', [PageController::class, 'uploadImage'])->name('pages.upload-image');
        Route::post('pages/cleanup-temp-images', [PageController::class, 'cleanupTempImages'])->name('pages.cleanup-temp-images');
        
        // Footer Setting routes
        Route::get('footer-setting', [FooterSettingController::class, 'edit'])->name('footer-setting.edit');
        Route::put('footer-setting', [FooterSettingController::class, 'update'])->name('footer-setting.update');
        Route::delete('footer-setting/delete-partner', [FooterSettingController::class, 'deletePartner'])->name('footer-setting.delete-partner');
        
        // About Content routes
        Route::resource('about-contents', AboutContentController::class)->except(['create', 'store', 'destroy', 'show']);
    });
});
