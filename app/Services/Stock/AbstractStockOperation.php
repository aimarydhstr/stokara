<?php

namespace App\Services\Stock;

use App\Contracts\StockOperation;
use Illuminate\Validation\ValidationException;

abstract class AbstractStockOperation implements StockOperation
{
    public const MAX_STOCK = 1000000000;

    protected function validateQuantity(int $quantity): void
    {
        if ($quantity < 1 || $quantity > self::MAX_STOCK) {
            throw ValidationException::withMessages(['quantity' => 'Jumlah harus antara 1 dan 1.000.000.000.']);
        }
    }
}