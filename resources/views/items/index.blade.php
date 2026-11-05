@extends('layouts.app')
@section('title', 'Data barang')
@section('description', 'Satu daftar lengkap untuk seluruh persediaan gudang Anda.')
@section('actions')<a href="{{ route('items.create') }}" class="btn btn-primary">
<x-icon name="plus" size="18"/>Tambah barang</a>@endsection
@section('content')
<div class="inventory-tabs">
<a href="{{ route('items.index') }}" @class(['active'=>!request('status')])>Semua barang <span>{{ $counts['all'] }}</span>
</a>
<a href="{{ route('items.index',['status'=>'low']) }}" @class(['active'=>request('status')==='low'])>Stok minimum <span>{{ $counts['low'] }}</span>
</a>
<a href="{{ route('items.index',['status'=>'empty']) }}" @class(['active'=>request('status')==='empty'])>Stok habis <span>{{ $counts['empty'] }}</span>
</a>
</div>
<section class="panel">
<form class="filter-bar" method="GET" action="{{ route('items.index') }}">
<div class="input-with-icon flex-grow-1">
<x-icon name="search" size="18"/>
<input name="q" class="form-control" placeholder="Cari nama atau kode barang..." aria-label="Cari nama atau kode barang" value="{{ request('q') }}" maxlength="120">
</div>
<select class="form-select filter-select" name="category" aria-label="Filter kategori">
<option value="">Semua kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category')==$category->id)>{{ $category->name }}</option>@endforeach</select>
<select class="form-select filter-select" name="status" aria-label="Filter status stok">
<option value="">Semua status</option>
<option value="available" @selected(request('status')==='available')>Tersedia</option>
<option value="low" @selected(request('status')==='low')>Stok minimum</option>
<option value="empty" @selected(request('status')==='empty')>Habis</option>
</select>
<button class="btn btn-light" type="submit">
<x-icon name="filter" size="17"/>Filter</button>@if(request()->hasAny(['q','category','status']))<a class="reset-link" href="{{ route('items.index') }}">Reset</a>@endif</form>
<div class="table-responsive">
<table class="table app-table mb-0">
<thead>
<tr>
<th>BARANG</th>
<th>KATEGORI</th>
<th class="text-end">STOK</th>
<th class="text-end">HARGA SATUAN</th>
<th>STATUS</th>
<th class="text-end">AKSI</th>
</tr>
</thead>
<tbody>@forelse($items as $item)<tr>
<td>
<div class="item-cell">
<span class="item-symbol color-{{ $item->category->color }}">
<x-icon name="box" size="21"/>
</span>
<div>
<a class="item-name" href="{{ route('items.show',$item) }}">{{ $item->name }}</a>
<small>{{ $item->sku }}</small>
</div>
</div>
</td>
<td>
<span class="category-label">
<span class="color-dot dot-{{ $item->category->color }}">
</span>{{ $item->category->name }}</span>
</td>
<td class="text-end">
<strong>{{ number_format($item->stock,0,',','.') }}</strong>
<small class="cell-unit">{{ $item->unit }} · min. {{ $item->minimum_stock }}</small>
</td>
<td class="text-end price-cell">Rp {{ number_format($item->unit_price,0,',','.') }}</td>
<td>
<x-stock-badge :item="$item"/>
</td>
<td>
<div class="table-actions">
<a href="{{ route('items.show',$item) }}" class="icon-button" aria-label="Detail {{ $item->name }}">
<x-icon name="eye" size="17"/>
</a>
<a href="{{ route('items.edit',$item) }}" class="icon-button" aria-label="Edit {{ $item->name }}">
<x-icon name="edit" size="17"/>
</a>
</div>
</td>
</tr>@empty<tr>
<td colspan="6">
<div class="empty-state">
<span class="empty-icon">
<x-icon name="box" size="30"/>
</span>
<h3>{{ request()->hasAny(['q','category','status']) ? 'Barang tidak ditemukan' : 'Inventori siap diisi' }}</h3>
<p>{{ request()->hasAny(['q','category','status']) ? 'Coba ubah kata pencarian atau filter Anda.' : 'Tambahkan barang pertama untuk mulai mengelola persediaan.' }}</p>
<a href="{{ request()->hasAny(['q','category','status']) ? route('items.index') : route('items.create') }}" class="btn btn-primary btn-sm">{{ request()->hasAny(['q','category','status']) ? 'Tampilkan semua barang' : 'Tambah barang' }}</a>
</div>
</td>
</tr>@endforelse</tbody>
</table>
</div>
<div class="panel-pagination">{{ $items->links() }}</div>
</section>
@endsection