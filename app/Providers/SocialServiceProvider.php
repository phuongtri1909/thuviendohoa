<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Client\SocialController;

class SocialServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer(
            ['client.layouts.partials.footer', 'components.contact_widget'],
            function ($view) {
                $view->with('socials', SocialController::getSocials());
            }
        );
    }
}
