<?php

namespace Mendela92\Stock;

use DateTimeInterface;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\morphMany;
use Illuminate\Support\Carbon;
use Mendela92\Stock\Events\StockCreated;

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
    public function getStockAttribute(): float|int
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
     * Sum all stock records of a model.
     * @param $date
     * @return float|int
     */
    public function stock($date = null): float|int
    {
        $date = $date ?: Carbon::now();

        if (!$date instanceof DateTimeInterface) {
            $date = Carbon::create($date);
        }

        return $this->decimalValueWhenNeeded($this->stockMutations()
            ->where('created_at', '<=', $date->format('Y-m-d H:i:s'))
            ->sum('amount'));
    }

    /**
     * Increase the model's stock
     *
     * Create a new stock record with positive stock value.
     * @param float|int $amount
     * @param array $arguments
     * @return Model
     * @throws Exception
     */
    public function increaseStock(float|int $amount, array $arguments = []): Model
    {
        return $this->createStockMutation(abs($amount), $arguments);
    }

    /**
     * Decrease the model's stock
     *
     * Create a new stock record with negative stock value.
     * @param float|int $amount
     * @param array $arguments
     * @return Model
     * @throws Exception
     */
    public function decreaseStock(float|int $amount, array $arguments = []): Model
    {
        return $this->createStockMutation(-1 * abs($amount), $arguments);
    }

    /**
     * Delete model's stock previous set and set new amount if defined.
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
     * Set model's stock by creating a new mutation with the difference between the old and new value
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
     * Check if model has stock greater or equal to a certain amount. (default: amount = 1)
     *
     * @param float|int $amount
     * @return bool
     */
    public function inStock(float|int $amount = 1): bool
    {
        return $this->stock > 0.0 && $this->stock >= $amount;
    }


    /**
     * Check if model is out of stock.
     *
     * @return bool
     */
    public function outOfStock(): bool
    {
        return $this->stock <= 0.0;
    }

    /**
     * Handle mutations (increase, decrease).
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

        $createArguments = collect([
            'amount' => $amount,
            'details' => $arguments,
        ])->toArray();

        $createStock = $this->stockMutations()->create($createArguments);

        if ($amount < 0) {
            event(new StockCreated($this));
        }
        return $createStock;
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

    /*
    |--------------------------------------------------------------------------
    | helpers
    |--------------------------------------------------------------------------
    */
    /**
     * Format value to exclude decimal if is not a decimal values. eg: decimalValueWhenNeeded(20.0)
     * returns 20 and decimalValueWhenNeeded(18.25) returns 18.25.
     * @param $num
     * @return int|float
     */
    public function decimalValueWhenNeeded($num): float|int
    {
        return intval($num) == ($num) ? intval($num) : $num;
    }

    /**
     * Level of stock before being notified.
     *
     * @return Repository|Application|mixed
     */
    public function getStockAlertAt(): mixed
    {
        return config('stock.alert.at', 10);
    }

    /**
     * List of emails that the notifications will be sent to.
     * @return Repository|Application|mixed
     */
    public function getStockAlertTo(): mixed
    {
        return config('stock.alert.to');
    }
}
