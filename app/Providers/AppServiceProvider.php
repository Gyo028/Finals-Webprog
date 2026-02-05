<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator; // ✅ Added this import

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Skip SSL verification for Google OAuth on localhost
        if (request()->is('auth/google/callback') || request()->is('auth/google')) {
            config(['services.google.guzzle' => ['verify' => false]]);
        }

        // Ensures database compatibility for older MySQL versions
        Schema::defaultStringLength(191);

        // ✅ Force Laravel to use Bootstrap 5 styling for pagination
        Paginator::useBootstrapFive();
    }
}