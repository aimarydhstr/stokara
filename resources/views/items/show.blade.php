@extends('layouts.app')
@section('title', 'Detail barang')
@section('description', 'Informasi persediaan dan jejak pergerakan barang dalam satu tempat.')
@section('actions')<a href="{{ route('items.index') }}" class="btn btn-light">
<x-icon name="back" size="17"/>Kembali</a>
<a href="{{ route('items.edit',$item) }}" class="btn btn-primary">
<x-icon name="edit" size="17"/>Edit barang</a>@endsection
@section('content')
<section class="panel item-detail">
<div class="detail-identity">
<span class="detail-symbol color-{{ $item->category->color }}">
<x-icon name="box" size="36"/>
</span>
<div>
<div class="eyebrow">{{ $item->sku }}</div>
<h2>{{ $item->name }}</h2>
<span class="category-label">
<span class="color-dot dot-{{ $item->category->color }}">
</span>{{ $item->category->name }}</span>
</div>
<div class="ms-auto">
<x-stock-badge :item="$item"/>
</div>
</div>
<div class="detail-metrics">
<div>
<span>Stok saat ini</span>
<strong>{{ number_format($item->stock,0,',','.') }} <small>{{ $item->unit }}</small>
</strong>
</div>
<div>
<span>Batas minimum</span>
<strong>{{ $item->minimum_stock }} <small>{{ $item->unit }}</small>
</strong>
</div>
<div>
<span>Harga satuan</span>
<strong>Rp {{ number_format($item->unit_price,0,',','.') }}</strong>
</div>
<div>
<span>Nilai persediaan</span>
<strong>Rp {{ number_format($item->stock*$item->unit_price,0,',','.') }}</strong>
</div>
</div>
<div class="detail-description">
<p>{{ $item->description ?: 'Belum ada deskripsi untuk barang ini.' }}</p>
<div class="d-flex flex-wrap gap-2">
<a href="{{ route('movements.create',['type'=>'in','item'=>$item->id]) }}" class="btn btn-primary btn-sm">
<x-icon name="in" size="16"/>Barang masuk</a>
<a href="{{ route('movements.create',['type'=>'out','item'=>$item->id]) }}" class="btn btn-light btn-sm">
<x-icon name="out" size="16"/>Barang keluar</a>
<form action="{{ route('items.destroy',$item) }}" method="POST" class="ms-auto" data-confirm="Hapus barang ini? Hanya barang tanpa stok dan riwayat yang dapat dihapus.">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm" type="submit">
<x-icon name="trash" size="15"/>Hapus barang</button>
</form>
</div>
</div>
</section>
<section class="panel mt-4">
<div class="panel-heading">
<div>
<h2>Riwayat pergerakan</h2>
<p>Setiap perubahan stok barang ini tercatat di sini.</p>
</div>
<span class="count-pill">{{ $movements->total() }} transaksi</span>
</div>
<div class="table-responsive">
<table class="table app-table mb-0">
<thead>
<tr>
<th>REFERENSI / TANGGAL</th>
<th>JENIS</th>
<th class="text-end">JUMLAH</th>
<th class="text-end">PERUBAHAN STOK</th>
<th>KETERANGAN</th>
</tr>
</thead>
<tbody>@forelse($movements as $movement)<tr>
<td>
<strong class="reference-text">{{ $movement->reference }}</strong>
<small class="cell-unit">{{ $movement->occurred_at->translatedFormat('d M Y') }}</small>
</td>
<td>
<span class="movement-badge movement-{{ $movement->type }}">
<x-icon :name="$movement->type" size="13"/>{{ $movement->type==='in' ? 'Masuk' : 'Keluar' }}</span>
</td>
<td class="text-end">{{ $movement->quantity }} {{ $item->unit }}</td>
<td class="text-end">{{ $movement->stock_before }} <span class="text-muted">→</span> <strong>{{ $movement->stock_after }}</strong>
</td>
<td class="note-cell">{{ $movement->notes ?: '—' }}<small class="cell-unit">{{ $movement->user->name }}</small>
</td>
</tr>@empty<tr>
<td colspan="5">
<div class="empty-state">
<x-icon name="history" size="30"/>
<h3>Belum ada pergerakan</h3>
<p>Mulai dengan mencatat barang masuk.</p>
</div>
</td>
</tr>@endforelse</tbody>
</table>
</div>
<div class="panel-pagination">{{ $movements->links() }}</div>
</section>
@endsection