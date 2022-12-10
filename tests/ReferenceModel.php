<?php

namespace Mendela92\Stock\Tests;

use Mendela92\Stock\ReferencedByStockMutations;
use Illuminate\Database\Eloquent\Model;

class ReferenceModel extends Model
{
    use ReferencedByStockMutations;

    protected $table = 'reference_models';

    protected $guarded = [];

    public $timestamps = false;
}
