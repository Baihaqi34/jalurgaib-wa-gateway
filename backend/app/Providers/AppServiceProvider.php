<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\KaseraPayService::class, function () {
            return new \App\Services\KaseraPayService(
                key: (string) config('services.kasera.key', ''),
                baseUrl: (string) config('services.kasera.base_url', 'https://pay.kasera.id')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // di production pakai https
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Automatic authentication telemetry logging
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            if ($event->user instanceof \App\Models\User) {
                app(\App\Services\AuthenticationLogService::class)->logLogin($event->user, request(), 'session');
            }
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Logout::class, function ($event) {
            if ($event->user instanceof \App\Models\User) {
                app(\App\Services\AuthenticationLogService::class)->logLogout($event->user, request());
            }
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Failed::class, function ($event) {
            $email = $event->credentials['email'] ?? ($event->user?->email ?? '');
            if (!empty($email)) {
                app(\App\Services\AuthenticationLogService::class)->logFailed($email, request(), 'Email atau password salah');
            }
        });
    }
}
