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
    'alert' => [
        'notification' => env("STOCK_NOTIFICATION", true),

        'at' => env("NOTIFICATION_STOCK_LEVEL", 10),

        'to' => ['ndick@gmail.com']
    ],
];
