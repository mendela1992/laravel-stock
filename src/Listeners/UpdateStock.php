<?php

namespace Mendela92\Stock\Listeners;

use Illuminate\Support\Facades\Notification;
use Mendela92\Stock\Events\StockCreated;

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
        // Send notification when the notification is set to active
        if ($stockCreated->model->getStockNotificationStatus()) {

            // Send notification when model's stock reached defined alert value
            if ($stockCreated->model->stock <= $stockCreated->model->getStockAlertAt()) {

                // Notification class
                $className = config('stock.alert.notification_model', "Mendela92\Stock\Notifications\LowStockLevelNotification");

                // Send notification
                Notification::route('mail', $stockCreated->model->getStockAlertTo())
                    ->notify(new $className($stockCreated->model));
            }
        }
    }
}
