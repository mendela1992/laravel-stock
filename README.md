# Laravel Stock

[//]: # ([![Latest Version on Packagist]&#40;https://img.shields.io/packagist/v/appstract/laravel-stock.svg?style=flat-square&#41;]&#40;https://packagist.org/packages/appstract/laravel-stock&#41;)

[//]: # ()

[//]: # ([![Total Downloads]&#40;https://img.shields.io/packagist/dt/appstract/laravel-stock.svg?style=flat-square&#41;]&#40;https://packagist.org/packages/appstract/laravel-stock&#41;)

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

[//]: # ([![Build Status]&#40;https://img.shields.io/travis/appstract/laravel-stock/master.svg?style=flat-square&#41;]&#40;https://travis-ci.org/appstract/laravel-stock&#41;)

Keep stock for Eloquent models. This package will track stock mutations for your models. You can increase, decrease,
clear and set stock. It's also possible to check if a model is in stock (on a certain date/time). But also watches the
level of stock of a model and sends a notification via Email

This package was originally a fork form [laravel-stock](https://github.com/appstract/laravel-stock)

## Functionality

* Increase stocks
* Decrease stocks
* Clear stocks
* Set stocks
* Check if model has stock or not (on a certain date/time).
* Send notification when a pre-defined stock level is reached

## Installation

You can install the package via composer:

``` bash
composer require mendela92/laravel-stock
```

By running `php artisan vendor:publish --provider="Mendela92\Stock\StockServiceProvider"`, it will publish migrations and config file. 

The configuration file looks this:
```php
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


```
Run `php artisan migrate` to migrate the table. There will now be a `stocks`
table in your database.

## Usage

Adding the `HasStock` trait will enable stock functionality on the Model.

``` php
use Mendela92\Stock\HasStock;

class Book extends Model
{
    use HasStock;
    
    ...
    
    public function getStockAlertAt(): mixed
    {
        return config('stock.alert.at', 10);
        // Change the value of the level of stock before being notification is sent.
    }

    public function getStockAlertTo(): mixed
    {
        // Array of emails that the notifications will be sent to.
    }

    public function getStockNotificationStatus(): mixed
    {
        // Conditioning notification of  status for each model instance
    }
}
```

### Basic mutations

```php
$book->increaseStock(10);
$book->decreaseStock(10);
$book->mutateStock(10);
$book->mutateStock(-10);
```

### Clearing stock

It's also possible to clear the stock and directly setting a new value.

```php
$book->clearStock();
$book->clearStock(10);
```

### Setting stock

It is possible to set stock. This will create a new mutation with the difference between the old and new value.

```php
$book->setStock(10);
```

### Check if model is in stock

It's also possible to check if a product is in stock (with a minimal value).

```php
$book->inStock();
$book->inStock(10);
```

### Current stock

Get the current stock value (on a certain date).

```php
$book->stock;
$book->stock(Carbon::now()->subDays(10));
```

### Stock arguments

Add a description and/or reference model to de StockMutation.

```php
$book->increaseStock(10, [
    'description' => 'This is a description',
    'reference' => $otherModel,
]);
```

### Query Scopes

It is also possible to query based on stock.

```php
Book::whereInStock()->get();
Book::whereOutOfStock()->get();
```

## Testing

``` bash
composer test
```

## Contributing

Contributions are welcome, [thanks to y'all](https://github.com/mendela92/laravel-stock/graphs/contributors) :)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
