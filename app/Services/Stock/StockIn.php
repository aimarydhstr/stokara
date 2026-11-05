<?php

namespace App\Services\Stock;

use Illuminate\Validation\ValidationException;

final class StockIn extends AbstractStockOperation
{
    public function type(): string
    {
        return 'in';
    }

    public function calculate(int $currentStock, int $quantity): int
    {
        $this->validateQuantity($quantity);
        if ($currentStock + $quantity > self::MAX_STOCK) {
            throw ValidationException::withMessages(['quantity' => 'Jumlah melebihi kapasitas stok yang didukung.']);
        }
        return $currentStock + $quantity;
    }
}