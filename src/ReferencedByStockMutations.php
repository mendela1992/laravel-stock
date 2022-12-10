<?php

namespace Mendela92\Stock;

trait ReferencedByStockMutations
{
    /**
     * Relation with StockMutation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function stockMutations()
    {
        return $this->morphMany(StockMutation::class, 'reference');
    }
}
