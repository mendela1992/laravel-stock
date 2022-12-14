<?php

namespace Mendela92\Stock\Tests;

use Illuminate\Foundation\Application;
use Mendela92\Stock\StockServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as BaseTest;

abstract class TestCase extends BaseTest
{
    protected $stockModel;

    /**
     * Define environment setup.
     *
     * @param  Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');

        $app['config']->set(
            'database.connections.testbench', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]
        );
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->stockModel = StockModel::first();
        $this->orderRow = OrderRow::first();
    }

    /**
     * Setup database.
     */
    protected function setUpDatabase($app)
    {
        $builder = $app['db']->connection()->getSchemaBuilder();

        // Create tables
        $builder->create('stock_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        $builder->create('order_rows', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('stock_model_id');
            $table->string('name');
            $table->string('amount');
        });

        // Create models
        StockModel::query()->create(['name' => 'StockModel']);
        OrderRow::query()->create(['stock_model_id' => 1, 'name' => 'OrderRow', 'amount' => 0]);
    }

    /**
     * Get package providers.
     *
     * @param  Application $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            StockServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param  Application $app
     * @return array
     */
    protected function getPackageAliases($app): array
    {
        return [
            //
        ];
    }
}

