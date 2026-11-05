<?php

namespace App\Services\Stock;

use Illuminate\Validation\ValidationException;

final class StockOut extends AbstractStockOperation
{
    public function type(): string
    {
        return 'out';
    }

    public function calculate(int $currentStock, int $quantity): int
    {
        $this->validateQuantity($quantity);
        if ($quantity > $currentStock) {
            throw ValidationException::withMessages(['quantity' => "Stok tidak mencukupi. Tersedia {$currentStock} unit."]);
        }
        return $currentStock - $quantity;
    }
}