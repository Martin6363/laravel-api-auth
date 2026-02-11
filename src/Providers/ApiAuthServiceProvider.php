<?php

namespace Martin6363\ApiAuth\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Martin6363\ApiAuth\Http\Middleware\ThrottleAuthRequests;

class ApiAuthServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     */
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__.'/../../config/api-auth.php',
            'api-auth'
        );
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->loadTranslations();
        $this->registerMiddleware();
        $this->loadRoutes();
        $this->loadViews();

        // Publish assets
        if ($this->app->runningInConsole()) {
            $this->publishAssets();
        }
    }

    /**
     * Load package language files.
     */
    protected function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'api-auth');

        $locale = $this->app->getLocale();
        $packageAuthPath = __DIR__."/../../lang/{$locale}/auth.php";

        if (! file_exists($packageAuthPath)) {
            return;
        }

        $packageTranslations = require $packageAuthPath;

        $lines = [];

        foreach ($packageTranslations as $key => $value) {
            $lines["auth.$key"] = $value;
        }

        $this->app['translator']->addLines($lines, $locale);
    }

    /**
     * Register package middleware.
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('throttle.auth', ThrottleAuthRequests::class);
    }

    /**
     * Load package routes.
     */
    protected function loadRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }

    protected function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'api-auth');
    }

    /**
     * Publish package assets.
     */
    protected function publishAssets(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../../config/api-auth.php' => config_path('api-auth.php'),
        ], 'api-auth-config');

        // Publish language files
        // Published to lang/en/auth.php to merge with Laravel's auth translations
        $this->publishes([
            __DIR__.'/../../lang/en' => $this->app->langPath('en'),
        ], 'api-auth-lang');

        $this->publishes([
            __DIR__.'/../../src/Http/Controllers/v1' => app_path('Http/Controllers/ApiAuth/v1'),
            __DIR__.'/../../src/Services/v1' => app_path('Services/ApiAuth/v1'),
            __DIR__.'/../../src/Http/Requests/v1' => app_path('Http/Requests/ApiAuth/v1'),
            __DIR__.'/../../src/Http/Resources/v1' => app_path('Http/Resources/ApiAuth/v1'),
        ], 'api-auth-logic');

        $this->publishes([
            __DIR__.'/../../src/Services/v1' => app_path('Services/ApiAuth/v1'),
        ], 'api-auth-services');

        $this->publishes([
            __DIR__.'/../../src/Http/Controllers/v1' => app_path('Http/Controllers/ApiAuth/v1'),
        ], 'api-auth-controllers');

        $this->publishes([
            __DIR__.'/../../src/Http/Requests/v1' => app_path('Http/Requests/ApiAuth/v1'),
        ], 'api-auth-requests');

        $this->publishes([
            __DIR__.'/../../src/Http/Resources/v1' => app_path('Http/Resources/ApiAuth/v1'),
        ], 'api-auth-resources');

        $this->publishes([
            __DIR__.'/../../src/Notifications' => app_path('Notifications/ApiAuth'),
            __DIR__.'/../../resources/views/emails' => resource_path('views/emails/api-auth'),
        ], 'api-auth-notifications');

        // Register commands
        $this->commands([
            \Martin6363\ApiAuth\Console\Commands\InstallApiAuthCommand::class,
            \Martin6363\ApiAuth\Console\Commands\UninstallApiAuthCommand::class,
        ]);
    }
}
