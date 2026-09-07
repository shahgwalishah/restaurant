<?php

namespace App\Providers;

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
        if (env('VERCEL') && config('database.default') === 'sqlite') {
            $writableDatabase = '/tmp/database.sqlite';
            $bundledDatabase = database_path('database.sqlite');

            if (! file_exists($writableDatabase)) {
                if (file_exists($bundledDatabase)) {
                    copy($bundledDatabase, $writableDatabase);
                } else {
                    touch($writableDatabase);
                }
            }

            config(['database.connections.sqlite.database' => $writableDatabase]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        if ($this->app->environment('production') || env('VERCEL')) {
            URL::forceScheme('https');
        }
    }
}
