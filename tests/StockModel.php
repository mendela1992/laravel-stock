<?php

namespace Mendela92\Stock\Tests;

use Mendela92\Stock\HasStock;
use Illuminate\Database\Eloquent\Model;

class StockModel extends Model
{
    use HasStock;

    protected $table = 'stock_models';

    protected $guarded = [];

    public $timestamps = false;
}
