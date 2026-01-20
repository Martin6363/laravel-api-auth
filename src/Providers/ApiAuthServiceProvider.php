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
            __DIR__.'/../../src/Http/Controllers' => app_path('Http/Controllers/ApiAuth'),
            __DIR__.'/../../src/Services' => app_path('Services/ApiAuth'),
            __DIR__.'/../../src/Http/Requests' => app_path('Http/Requests/ApiAuth'),
        ], 'api-auth-logic');

        $this->publishes([
            __DIR__.'/../../src/Http/Requests' => app_path('Http/Requests/ApiAuth'),
        ], 'api-auth-requests');

        // Register commands
        $this->commands([
            \Martin6363\ApiAuth\Console\Commands\InstallApiAuthCommand::class,
        ]);
    }
}