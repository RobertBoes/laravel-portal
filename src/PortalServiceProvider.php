<?php

namespace RobertBoes\LaravelPortal;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use RobertBoes\LaravelPortal\Http\Controllers\PortalController;

class PortalServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        Route::macro('portal', function (string $uri, string $routeName) {
            Route::get($uri, [PortalController::class, 'fallback'])
                ->middleware('portal:'.$routeName);
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-portal.php'),
            ], 'config');

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-portal');

        // Register the main class to use with the facade
        $this->app->singleton('laravel-portal', function () {
            return new Portal;
        });
    }
}
