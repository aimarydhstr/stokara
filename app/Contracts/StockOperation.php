<?php

namespace App\Contracts;

interface StockOperation
{
    public function type(): string;

    public function calculate(int $currentStock, int $quantity): int;
}