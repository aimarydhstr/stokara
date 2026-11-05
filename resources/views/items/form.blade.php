@extends('layouts.app')
@section('title', $item->exists ? 'Edit barang' : 'Tambah barang')
@section('description', 'Lengkapi informasi barang agar persediaan mudah dikenali dan dipantau.')
@section('actions')<a class="btn btn-light" href="{{ route('items.index') }}">
<x-icon name="back" size="17"/>Kembali</a>@endsection
@section('content')
<div class="form-layout">
<section class="panel form-panel">
<div class="panel-heading">
<div>
<h2>Informasi barang</h2>
<p>Kolom bertanda <span class="text-danger">*</span> wajib diisi.</p>
</div>
<span class="section-icon color-green">
<x-icon name="box" size="23"/>
</span>
</div>
<form method="POST" action="{{ $item->exists ? route('items.update',$item) : route('items.store') }}" class="p-4">@csrf @if($item->exists)@method('PUT')@endif
<div class="row g-4">
<div class="col-md-6">
<label for="sku" class="form-label">Kode barang <span>*</span>
</label>
<input id="sku" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku',$item->sku) }}" placeholder="Contoh: ATK-019" maxlength="30" required>
<div class="form-text">Gunakan kode unik, huruf, angka, atau tanda hubung.</div>
</div>
<div class="col-md-6">
<label for="name" class="form-label">Nama barang <span>*</span>
</label>
<input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$item->name) }}" placeholder="Contoh: Kertas A4 80 gsm" maxlength="120" required>
</div>
<div class="col-md-6">
<label for="category_id" class="form-label">Kategori <span>*</span>
</label>
<select id="category_id" name="category_id" class="form-select" required>
<option value="">Pilih kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id',$item->category_id)==$category->id)>{{ $category->name }}</option>@endforeach</select>@if($categories->isEmpty())<div class="form-text">Belum ada kategori. <a href="{{ route('categories.index') }}">Tambahkan kategori terlebih dahulu.</a>
</div>@endif</div>
<div class="col-md-6">
<label for="unit" class="form-label">Satuan <span>*</span>
</label>
<select id="unit" name="unit" class="form-select" required>@foreach(['Pcs','Rim','Box','Pack','Botol','Roll','Set','Lusin'] as $unit)<option @selected(old('unit',$item->unit)===$unit)>{{ $unit }}</option>@endforeach</select>
</div>
<div class="col-md-6">
<label for="minimum_stock" class="form-label">Batas stok minimum <span>*</span>
</label>
<input id="minimum_stock" type="number" name="minimum_stock" class="form-control" value="{{ old('minimum_stock',$item->minimum_stock) }}" min="0" max="1000000000" required>
<div class="form-text">Peringatan tampil saat stok mencapai batas ini.</div>
</div>
<div class="col-md-6">
<label for="unit_price" class="form-label">Harga satuan <span>*</span>
</label>
<div class="input-group">
<span class="input-group-text">Rp</span>
<input id="unit_price" type="number" name="unit_price" class="form-control" value="{{ old('unit_price',$item->unit_price) }}" min="0" max="999999999999.99" step="0.01" required>
</div>
</div>
<div class="col-12">
<label for="description" class="form-label">Deskripsi <small>Opsional</small>
</label>
<textarea id="description" name="description" class="form-control" rows="4" maxlength="1000" placeholder="Tambahkan spesifikasi atau catatan barang...">{{ old('description',$item->description) }}</textarea>
</div>
</div>
<div class="form-actions">
<a href="{{ route('items.index') }}" class="btn btn-light">Batal</a>
<button class="btn btn-primary" type="submit" data-loading-text="Menyimpan...">
<x-icon name="check" size="18"/>{{ $item->exists ? 'Simpan perubahan' : 'Simpan barang' }}</button>
</div>
</form>
</section>
<aside>
<div class="info-card">
<span class="section-icon color-green">
<x-icon name="layers" size="25"/>
</span>
<h3>Data rapi, stok terkendali.</h3>
<p>Kategori dan satuan yang konsisten membuat pencatatan lebih mudah.</p>
<hr>
<div class="info-point">
<x-icon name="check-circle" size="18"/>
<span>Stok barang baru dimulai dari nol.</span>
</div>
<div class="info-point">
<x-icon name="check-circle" size="18"/>
<span>Gunakan transaksi barang masuk untuk menambah persediaan.</span>
</div>
<div class="info-point">
<x-icon name="check-circle" size="18"/>
<span>Perubahan informasi barang tidak mengubah stok.</span>
</div>
</div>
</aside>
</div>
@endsection