<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = ['category_id', 'sku', 'name', 'unit', 'minimum_stock', 'unit_price', 'description'];

    protected function casts(): array
    {
        return ['stock' => 'integer', 'minimum_stock' => 'integer', 'unit_price' => 'decimal:2'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeLowStock(Builder $query): void
    {
        $query->whereColumn('stock', '<=', 'minimum_stock');
    }

    public function getStockStatusAttribute(): string
    {
        return $this->stock === 0 ? 'Habis' : ($this->stock <= $this->minimum_stock ? 'Menipis' : 'Tersedia');
    }
}