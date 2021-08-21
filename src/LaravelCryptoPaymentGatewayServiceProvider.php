<?php

namespace Victorybiz\LaravelCryptoPaymentGateway;

use Illuminate\Support\ServiceProvider;

class LaravelCryptoPaymentGatewayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-crypto-payment-gateway');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-crypto-payment-gateway');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-crypto-payment-gateway.php'),
            ], 'laravel-crypto-payment-gateway:config');

            // Publishing the views.
            // $this->publishes([
            //     __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-crypto-payment-gateway'),
            // ], 'laravel-crypto-payment-gateway:views');

            // Publishing the migrations.
            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations')
            ], 'laravel-crypto-payment-gateway:migrations');

            // Publishing assets.
            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-crypto-payment-gateway'),
            ], 'laravel-crypto-payment-gateway:assets');
            $this->publishes([
                __DIR__.'/cryptoapi_php/images' => public_path('vendor/laravel-crypto-payment-gateway/images'),
            ], 'laravel-crypto-payment-gateway:assets');

            // Publishing the translation files.
            // $this->publishes([
            //     __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-crypto-payment-gateway'),
            // ], 'laravel-crypto-payment-gateway:lang');

            // Registering package commands.
            // $this->commands([]);
        }

        \View::composer(['laravel-crypto-payment-gateway::paymentbox-gourl-*'], function ($view) {
            $view->jsPath = __DIR__.'/cryptoapi_php/js/support.min.js';
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-crypto-payment-gateway');

        // Register the main class to use with the facade
        $this->app->singleton('laravel-crypto-payment-gateway', function () {
            return new LaravelCryptoPaymentGateway;
        });
    }
}
