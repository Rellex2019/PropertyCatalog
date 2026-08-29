<?php

namespace App\Providers;

use App\Contracts\QRCodeGeneratorInterface;
use App\Services\QRCodeService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(QRCodeGeneratorInterface::class, QRCodeService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceRootUrl(config('app.url'));
        URL::forceScheme('https');
        Vite::prefetch(concurrency: 3);
    }
}
