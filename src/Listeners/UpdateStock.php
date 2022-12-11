<?php

namespace Mendela92\Stock\Listeners;

use Illuminate\Support\Facades\Notification;
use Mendela92\Stock\Events\StockCreated;
use Mendela92\Stock\Notifications\LowStockLevelNotification;

class UpdateStock
{
    /**
     * Handle the event.
     *
     * @param StockCreated $stockCreated
     * @return void
     */
    public function handle(StockCreated $stockCreated)
    {
        if (config('stock.alert.notification', true)) {
            if ($stockCreated->model->stock <= $stockCreated->model->getStockAlertAt()) {
                Notification::route('mail', $stockCreated->model->getStockAlertTo())
                    ->notify(new LowStockLevelNotification($stockCreated->model));
            }
        }
    }
}
