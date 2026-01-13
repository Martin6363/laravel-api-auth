<?php

namespace Vendor\ApiAuth\Providers;

use Illuminate\Support\ServiceProvider;

class ApiAuthServiceProvider extends ServiceProvider
{
    /**
     * Register package services (Services).
     */
    public function register(): void
    {
        // We enable the config.
        $this->mergeConfigFrom(
            __DIR__.'/../../config/api-auth.php', 'api-auth'
        );
    }

    /**
     * Load package resources.
     */

    public function boot(): void
    {
        // Loading the routers.
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');

        // We allow the user to export the config.
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/api-auth.php' => config_path('api-auth.php'),
            ], 'api-auth-config');
            
            $this->commands([
                \Vendor\ApiAuth\Console\Commands\InstallApiAuthCommand::class,
            ]);
        }
    }
}