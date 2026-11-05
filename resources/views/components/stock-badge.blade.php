@props(['item'])
<span @class(['status-badge', 'status-empty' => $item->stock === 0, 'status-low' => $item->stock > 0 && $item->stock <= $item->minimum_stock, 'status-good' => $item->stock > $item->minimum_stock])>
<span>
</span>{{ $item->stock_status }}</span>