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
    }
}
