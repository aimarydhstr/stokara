<?php

namespace App\Services;

use App\Contracts\StockOperation;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class InventoryService
{
    public function record(StockOperation $operation, int $itemId, int $quantity, User $user, array $data): StockMovement
    {
        return DB::transaction(function () use ($operation, $itemId, $quantity, $user, $data) {
            $item = Item::query()->lockForUpdate()->findOrFail($itemId);
            if (StockMovement::where('request_token', $data['request_token'])->exists()) {
                throw ValidationException::withMessages(['request_token' => 'Transaksi ini sudah tersimpan. Silakan buat transaksi baru.']);
            }
            $before = $item->stock;
            $after = $operation->calculate($before, $quantity);
            $item->stock = $after;
            $item->save();

            return StockMovement::create([
                'reference' => strtoupper($operation->type()).'-'.now()->format('ymd').'-'.strtoupper(Str::random(8)),
                'request_token' => $data['request_token'],
                'item_id' => $item->id,
                'user_id' => $user->id,
                'type' => $operation->type(),
                'quantity' => $quantity,
                'stock_before' => $before,
                'stock_after' => $after,
                'occurred_at' => $data['occurred_at'],
                'notes' => $data['notes'] ?? null,
            ]);
        }, 3);
    }
}