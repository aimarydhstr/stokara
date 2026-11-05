<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = ['reference', 'request_token', 'item_id', 'user_id', 'type', 'quantity', 'stock_before', 'stock_after', 'occurred_at', 'notes'];

    protected function casts(): array
    {
        return ['occurred_at' => 'date', 'quantity' => 'integer', 'stock_before' => 'integer', 'stock_after' => 'integer'];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}