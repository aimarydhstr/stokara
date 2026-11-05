@props(['movements', 'compact' => false])
<div class="table-responsive">
<table class="table app-table mb-0">
<thead>
<tr>
<th>BARANG</th>
<th>JENIS</th>
<th class="text-end">JUMLAH</th>@unless($compact)<th class="text-end">STOK AKHIR</th>
<th>PETUGAS</th>@endunless<th>TANGGAL</th>
</tr>
</thead>
<tbody>
@forelse($movements as $movement)
<tr>
<td>
<div class="item-cell">
<span class="item-symbol color-{{ $movement->item->category->color }}">
<x-icon name="box" size="19"/>
</span>
<div>
<a class="item-name" href="{{ route('items.show', $movement->item) }}">{{ $movement->item->name }}</a>
<small>{{ $compact ? $movement->item->sku : $movement->reference }}</small>@unless($compact)<small class="movement-note">{{ $movement->notes ?: 'Tanpa keterangan' }}</small>@endunless</div>
</div>
</td>
<td>
<span @class(['movement-badge', 'movement-in' => $movement->type === 'in', 'movement-out' => $movement->type === 'out'])>
<x-icon :name="$movement->type" size="13"/>{{ $movement->type === 'in' ? 'Masuk' : 'Keluar' }}</span>
</td>
<td class="text-end">
<strong class="{{ $movement->type === 'in' ? 'text-green' : 'text-dark' }}">{{ $movement->type === 'in' ? '+' : '−' }}{{ number_format($movement->quantity, 0, ',', '.') }}</strong>
<small class="cell-unit">{{ $movement->item->unit }}</small>
</td>@unless($compact)<td class="text-end">
<strong>{{ number_format($movement->stock_after, 0, ',', '.') }}</strong>
<small class="cell-unit">dari {{ number_format($movement->stock_before, 0, ',', '.') }}</small>
</td>
<td>
<span class="mini-avatar">{{ mb_substr($movement->user->name,0,1) }}</span>{{ $movement->user->name }}</td>@endunless<td class="date-cell">{{ $movement->occurred_at->translatedFormat('d M Y') }}<small>{{ $movement->reference }}</small>
</td>
</tr>
@empty<tr>
<td colspan="{{ $compact ? 4 : 6 }}">
<div class="empty-state">
<span class="empty-icon">
<x-icon name="history" size="28"/>
</span>
<h3>Belum ada transaksi</h3>
<p>Transaksi yang Anda catat akan muncul di sini.</p>
<a href="{{ route('movements.create') }}" class="btn btn-primary btn-sm">Catat barang masuk</a>
</div>
</td>
</tr>@endforelse
</tbody>
</table>
</div>