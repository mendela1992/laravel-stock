<?php

use Mendela92\Stock\Notifications\LowStockLevelNotification;

return [

    /*
    |--------------------------------------------------------------------------
    | Default table name
    |--------------------------------------------------------------------------
    |
    | Table name to use to store mutations.
    |
    */

    'table' => 'stocks',

    /*
   |--------------------------------------------------------------------------
   | Default notification stock level
   |--------------------------------------------------------------------------
   |
   | Default stock alert configuration values
   |
   */
    'alert' => [
        'notification' => env("STOCK_NOTIFICATION", true),

        'at' => env("NOTIFICATION_STOCK_LEVEL", 10),

        'to' => ['email@example.com'],

        'model' => LowStockLevelNotification::class,
    ],
];
