<?php

namespace Mendela92\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMutation extends Model
{
    protected $fillable = [
        'stockable_type',
        'stockable_id',
        'amount',
        'details',
    ];

    protected $casts = [
        'details' => 'array'
    ];
    /**
     * StockMutation constructor.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('stock.table', 'stock_mutations'));
    }

    /**
     * Relation.
     *
     * @return MorphTo
     */
    public function stockable()
    {
        return $this->morphTo();
    }
}
