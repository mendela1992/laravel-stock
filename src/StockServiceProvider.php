<?php

namespace Mendela92\Stock;

use Illuminate\Support\ServiceProvider;

class StockServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/stock.php' => config_path('stock.php'),
            ], 'stock-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'stock-migrations');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/stock.php', 'stock');
    }
}
