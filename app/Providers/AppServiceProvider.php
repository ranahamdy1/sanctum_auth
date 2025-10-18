<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

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
    public function boot()
    {
        // Customize reset password URL
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return "https://myapp.page.link/reset-password?token={$token}&email={$user->email}";
        });
    }
}
