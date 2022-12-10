<?php

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
   | Default stock value before notification is set to
   |
   */
    'notification_stock_level' => env("NOTIFICATION_STOCK_LEVEL", 2),

    'notification' => env("STOCK_NOTIFICATION", true),

    'notification_channel' => [
//        \Illuminate\Support\Facades\Mail::class,
//        \Illuminate\Notifications\DatabaseNotification::class
    ]

];
