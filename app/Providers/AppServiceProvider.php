<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Exception $e) {
        }
    }
}
