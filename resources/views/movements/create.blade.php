@extends('layouts.app')
@section('title', $type==='in' ? 'Barang masuk' : 'Barang keluar')
@section('description', $type==='in' ? 'Catat penerimaan barang untuk memperbarui persediaan gudang.' : 'Catat pengeluaran barang sesuai kebutuhan operasional.')
@section('actions')<a href="{{ route('movements.index') }}" class="btn btn-light">
<x-icon name="history" size="17"/>Lihat riwayat</a>@endsection
@section('content')
<div class="form-layout">
<section class="panel form-panel">
<div class="transaction-switch">
<a href="{{ route('movements.create',['type'=>'in']) }}" @class(['active'=>$type==='in'])>
<x-icon name="in" size="18"/>Barang masuk</a>
<a href="{{ route('movements.create',['type'=>'out']) }}" @class(['active'=>$type==='out'])>
<x-icon name="out" size="18"/>Barang keluar</a>
</div>
<div class="panel-heading">
<div>
<h2>Detail transaksi</h2>
<p>Pastikan barang dan jumlah sudah sesuai.</p>
</div>
<span class="section-icon color-{{ $type==='in'?'blue':'purple' }}">
<x-icon :name="$type" size="23"/>
</span>
</div>
<form method="POST" action="{{ route('movements.store') }}" class="p-4" id="movementForm" data-movement-type="{{ $type }}">@csrf<input type="hidden" name="type" value="{{ $type }}">
<input type="hidden" name="request_token" value="{{ old('request_token', (string) Illuminate\Support\Str::uuid()) }}">
<div class="row g-4">
<div class="col-12">
<label for="item_id" class="form-label">Pilih barang <span>*</span>
</label>
<select id="item_id" name="item_id" class="form-select" required>
<option value="">Pilih barang dari inventori</option>@foreach($items as $item)<option value="{{ $item->id }}" data-stock="{{ $item->stock }}" data-unit="{{ $item->unit }}" data-name="{{ $item->name }}" data-sku="{{ $item->sku }}" @selected(old('item_id',request('item'))==$item->id)>{{ $item->sku }} · {{ $item->name }}</option>@endforeach</select>@if($items->isEmpty())<div class="form-text">Inventori masih kosong. <a href="{{ route('items.create') }}">Tambahkan barang terlebih dahulu.</a>
</div>@endif</div>
<div class="col-md-6">
<label for="quantity" class="form-label">Jumlah barang <span>*</span>
</label>
<div class="input-group">
<input id="quantity" type="number" name="quantity" min="1" max="1000000000" step="1" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" placeholder="0" required>
<span class="input-group-text" data-unit-label>Unit</span>
</div>
<div class="form-text" data-quantity-help>Gunakan jumlah sesuai satuan barang.</div>
</div>
<div class="col-md-6">
<label for="occurred_at" class="form-label">Tanggal transaksi <span>*</span>
</label>
<input id="occurred_at" name="occurred_at" type="date" class="form-control" value="{{ old('occurred_at',today()->toDateString()) }}" max="{{ today()->toDateString() }}" required>
</div>
<div class="col-12">
<label for="notes" class="form-label">Keterangan <small>Opsional</small>
</label>
<textarea id="notes" name="notes" class="form-control" rows="4" maxlength="500" placeholder="{{ $type==='in' ? 'Contoh: Pembelian dari pemasok untuk kebutuhan bulanan' : 'Contoh: Distribusi ke bagian administrasi' }}">{{ old('notes') }}</textarea>
<div class="form-text">Keterangan membantu Anda menelusuri asal atau penggunaan barang.</div>
</div>
</div>
<div class="transaction-note">
<x-icon name="lock" size="17"/>
<span>Stok diperbarui setelah transaksi berhasil disimpan.</span>
</div>
<div class="form-actions">
<a href="{{ route('movements.index') }}" class="btn btn-light">Batal</a>
<button class="btn btn-primary" type="submit" data-loading-text="Mencatat...">
<x-icon name="check" size="18"/>Simpan transaksi</button>
</div>
</form>
</section>
<aside>
<section class="panel transaction-preview">
<div class="panel-heading">
<div>
<h2>Ringkasan stok</h2>
<p>Pratinjau perubahan persediaan.</p>
</div>
</div>
<div class="preview-body">
<span class="preview-symbol color-{{ $type==='in'?'blue':'purple' }}">
<x-icon name="box" size="32"/>
</span>
<h3 data-preview-name>Belum ada barang dipilih</h3>
<p data-preview-sku>Pilih barang untuk melihat ringkasan.</p>
<div class="preview-row">
<span>Stok saat ini</span>
<strong data-preview-before>—</strong>
</div>
<div class="preview-row">
<span>{{ $type==='in'?'Jumlah masuk':'Jumlah keluar' }}</span>
<strong data-preview-quantity>—</strong>
</div>
<div class="preview-result">
<span>Perkiraan stok akhir</span>
<strong data-preview-after>—</strong>
</div>
<div class="preview-error" data-preview-error hidden role="status">Jumlah keluar melebihi stok tersedia.</div>
<small class="preview-footnote">Stok akhir mengikuti persediaan terbaru saat transaksi disimpan.</small>
</div>
</section>
<div class="soft-note mt-3">
<x-icon name="help" size="17"/>
<span>{{ $type==='in' ? 'Catat penerimaan saat barang sudah tersedia di gudang.' : 'Pastikan jumlah pengeluaran sesuai barang yang benar-benar digunakan.' }}</span>
</div>
</aside>
</div>
@endsection