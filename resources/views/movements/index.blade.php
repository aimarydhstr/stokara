@extends('layouts.app')
@section('title', 'Riwayat transaksi')
@section('description', 'Telusuri setiap barang yang masuk dan keluar dari gudang Anda.')
@section('actions')<a href="{{ route('movements.export',request()->only('q','type','from','to')) }}" class="btn btn-light">
<x-icon name="file" size="17"/>Unduh CSV</a>
<a href="{{ route('movements.create') }}" class="btn btn-primary">
<x-icon name="plus" size="18"/>Catat transaksi</a>@endsection
@section('content')
<div class="movement-summary">
<div>
<span class="stat-icon color-green">
<x-icon name="history"/>
</span>
<div>
<span>Transaksi ditemukan</span>
<strong>{{ number_format($movements->total(),0,',','.') }} <small>transaksi</small>
</strong>
</div>
</div>
<div>
<span class="stat-icon color-blue">
<x-icon name="in"/>
</span>
<div>
<span>Total barang masuk</span>
<strong>{{ number_format($summary['in']??0,0,',','.') }} <small>seluruh satuan</small>
</strong>
</div>
</div>
<div>
<span class="stat-icon color-purple">
<x-icon name="out"/>
</span>
<div>
<span>Total barang keluar</span>
<strong>{{ number_format($summary['out']??0,0,',','.') }} <small>seluruh satuan</small>
</strong>
</div>
</div>
</div>
<section class="panel">
<form class="filter-bar movement-filter" action="{{ route('movements.index') }}" method="GET">
<div class="input-with-icon">
<x-icon name="search" size="17"/>
<input class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari barang atau referensi..." aria-label="Cari transaksi" maxlength="120">
</div>
<select name="type" class="form-select" aria-label="Jenis transaksi">
<option value="">Semua jenis</option>
<option value="in" @selected(request('type')==='in')>Barang masuk</option>
<option value="out" @selected(request('type')==='out')>Barang keluar</option>
</select>
<div class="date-filter">
<input class="form-control" type="date" name="from" value="{{ request('from') }}" aria-label="Dari tanggal">
<span>—</span>
<input class="form-control" type="date" name="to" value="{{ request('to') }}" aria-label="Sampai tanggal">
</div>
<button class="btn btn-light" type="submit">
<x-icon name="filter" size="17"/>Filter</button>@if(request()->hasAny(['q','type','from','to']))<a class="reset-link" href="{{ route('movements.index') }}">Reset</a>@endif</form>
<x-movement-table :movements="$movements"/>
<div class="panel-pagination">{{ $movements->links() }}</div>
</section>
<div class="subtle-info">
<x-icon name="lock" size="15"/>Riwayat transaksi tersimpan untuk menjaga ketertelusuran perubahan stok.</div>
@endsection