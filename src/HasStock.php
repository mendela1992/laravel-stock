<?php

namespace Mendela92\Stock;

use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\morphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * @property mixed $stock
 */
trait HasStock
{
    /*
     |--------------------------------------------------------------------------
     | Accessors
     |--------------------------------------------------------------------------
     */

    /**
     * Stock accessor.
     *
     * @return
     */
    public function getStockAttribute()
    {
        return $this->stock();
    }

    /*
     |--------------------------------------------------------------------------
     | Methods
     |--------------------------------------------------------------------------
     */

    /**
     * Get model's stock value
     *
     * @param $date
     * @return float|int
     */
    public function stock($date = null): float|int
    {
        $date = $date ?: Carbon::now();

        if (!$date instanceof DateTimeInterface) {
            $date = Carbon::create($date);
        }

        return $this->num($this->stockMutations()
            ->where('created_at', '<=', $date->format('Y-m-d H:i:s'))
            ->sum('amount'));
    }

    /**
     * Increase the model's stock
     * @param float|int $amount
     * @param array $arguments
     * @return Model
     * @throws Exception
     */
    public function increaseStock(float|int $amount = 1, array $arguments = []): Model
    {
        return $this->createStockMutation($amount, $arguments);
    }

    /**
     * Decrease the model's stock
     * @param float|int $amount
     * @param array $arguments
     * @return Model
     * @throws Exception
     */
    public function decreaseStock(float|int $amount = 1, array $arguments = []): Model
    {
        return $this->createStockMutation(-1 * abs($amount), $arguments);
    }

    /**
     * @param float|int $amount
     * @param array $arguments
     * @return Model
     * @throws Exception
     */
    public function mutateStock(float|int $amount = 1, array $arguments = []): Model
    {
        return $this->createStockMutation($amount, $arguments);
    }

    /**
     * Clear model's stock
     *
     * @param float|int|null $newAmount
     * @param array $arguments
     * @return bool
     * @throws Exception
     */
    public function clearStock(float|int $newAmount = null, array $arguments = []): bool
    {
        $this->stockMutations()->delete();

        if (!is_null($newAmount)) {
            $this->createStockMutation($newAmount, $arguments);
        }

        return true;
    }

    /**
     * Set model stock
     *
     * @param $newAmount
     * @param array $arguments
     * @return Model|void
     * @throws Exception
     */
    public function setStock($newAmount, array $arguments = [])
    {
        $currentStock = $this->stock;

        if ($deltaStock = $newAmount - $currentStock) {
            return $this->createStockMutation($deltaStock, $arguments);
        }
    }

    /**
     * Check if model has stock
     *
     * @param float|int $amount
     * @return bool
     */
    public function inStock(float|int $amount = 1): bool
    {
        return $this->stock > 0.0 && $this->stock >= $amount;
    }


    /**
     * Check if model is out of stock
     *
     * @return bool
     */
    public function outOfStock()
    {
        return $this->stock <= 0.0;
    }

    /**
     * Function to handle mutations (increase, decrease).
     *
     * @param float|int $amount
     * @param array $arguments
     * @return Model
     * @throws Exception
     */
    protected function createStockMutation(float|int $amount, array $arguments = []): Model
    {
        if ($this->getKey() === null)
            throw new Exception("Instance of " . class_basename($this->getMorphClass()) . " model has not been persisted.");

        $reference = Arr::get($arguments, 'reference');

        $createArguments = collect([
            'amount' => $amount,
            'details' => Arr::except($arguments, 'reference'),
        ])->when($reference, function ($collection) use ($reference) {
            return $collection
                ->put('reference_type', $reference->getMorphClass())
                ->put('reference_id', $reference->getKey());
        })->toArray();

        return $this->stockMutations()->create($createArguments);
    }

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |--------------------------------------------------------------------------
     */

    public function scopeWhereInStock($query)
    {
        return $query->where(function ($query) {
            return $query->whereHas('stockMutations', function ($query) {
                return $query->select('stockable_id')
                    ->groupBy('stockable_id')
                    ->havingRaw('SUM(amount) > 0.0');
            });
        });
    }

    public function scopeWhereOutOfStock($query)
    {
        return $query->where(function ($query) {
            return $query->whereHas('stockMutations', function ($query) {
                return $query->select('stockable_id')
                    ->groupBy('stockable_id')
                    ->havingRaw('SUM(amount) <= 0.0');
            })->orWhereDoesntHave('stockMutations');
        });
    }

    /*
     |--------------------------------------------------------------------------
     | Relations
     |--------------------------------------------------------------------------
     */

    /**
     * Relation with StockMutation.
     *
     * @return morphMany
     */
    public function stockMutations(): morphMany
    {
        return $this->morphMany(StockMutation::class, 'stockable');
    }

    public function num($num)
    {
        return intval($num) == ($num) ? intval($num) : $num;
    }
}
