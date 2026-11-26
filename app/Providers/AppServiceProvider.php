<?php

namespace App\Providers;

use App\Services\EsignAssetGenerator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
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
    public function boot(): void
    {
        if ($this->app->environment('production') || str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Pastikan direktori dan aset kriptografi di volume persisten tersedia saat container berjalan
        if (!File::exists(storage_path('app/private/certs/bem_fikom.crt')) || !File::exists(storage_path('app/private/assets/cap_bem.png'))) {
            EsignAssetGenerator::generateAll();
        }
    }
}
