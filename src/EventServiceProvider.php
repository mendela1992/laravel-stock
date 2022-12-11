<?php

namespace Mendela92\Stock;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Mendela92\Stock\Events\StockCreated;
use Mendela92\Stock\Listeners\UpdateStock;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        StockCreated::class => [
            UpdateStock::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
