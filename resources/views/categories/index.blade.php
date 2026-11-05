@extends('layouts.app')
@section('title', 'Kategori')
@section('description', 'Kelompokkan barang agar inventori lebih terstruktur dan mudah ditemukan.')
@section('actions')<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryCreate">
<x-icon name="plus" size="18"/>Tambah kategori</button>@endsection
@section('content')
<div class="category-grid">@forelse($categories as $category)<section class="panel category-card">
<div class="d-flex align-items-start justify-content-between">
<span class="detail-symbol color-{{ $category->color }}">
<x-icon name="folder" size="27"/>
</span>
<div class="dropdown">
<button class="icon-button category-more" data-bs-toggle="dropdown" aria-label="Aksi kategori {{ $category->name }}">•••</button>
<div class="dropdown-menu dropdown-menu-end">
<button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#categoryEdit{{ $category->id }}">Edit kategori</button>
<form method="POST" action="{{ route('categories.destroy',$category) }}" data-confirm="Hapus kategori ini? Kategori yang masih digunakan tidak dapat dihapus.">@csrf @method('DELETE')<button class="dropdown-item text-danger" type="submit">Hapus kategori</button>
</form>
</div>
</div>
</div>
<h2>{{ $category->name }}</h2>
<p>{{ $category->description ?: 'Belum ada deskripsi kategori.' }}</p>
<div class="category-card-footer">
<span>
<strong>{{ $category->items_count }}</strong> jenis barang</span>
<a href="{{ route('items.index',['category'=>$category->id]) }}" aria-label="Lihat barang kategori {{ $category->name }}">
<x-icon name="arrow" size="19"/>
</a>
</div>
</section>@empty<section class="panel empty-state w-100">
<span class="empty-icon">
<x-icon name="folder" size="30"/>
</span>
<h3>Mulai dengan kategori pertama</h3>
<p>Kelompokkan persediaan sesuai kebutuhan gudang.</p>
<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#categoryCreate">Tambah kategori</button>
</section>@endforelse</div>
<div class="mt-4">{{ $categories->links() }}</div>
@php($modalCategories = collect([null])->merge($categories->items()))
@foreach($modalCategories as $category)
@php($modalId = $category ? 'categoryEdit'.$category->id : 'categoryCreate')
<div class="modal fade" id="{{ $modalId }}" @if($errors->any() && old('_category_form') === $modalId) data-reopen-modal @endif tabindex="-1" aria-labelledby="{{ $modalId }}Title" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<form method="POST" action="{{ $category ? route('categories.update',$category) : route('categories.store') }}">
<input type="hidden" name="_category_form" value="{{ $modalId }}">@csrf @if($category)@method('PUT')@endif<div class="modal-header">
<h2 id="{{ $modalId }}Title" class="modal-title fs-5">{{ $category ? 'Edit kategori' : 'Tambah kategori' }}</h2>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup formulir">
</button>
</div>
<div class="modal-body">
@if($errors->any() && old('_category_form') === $modalId)
<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
@endif
<div class="mb-3">
<label class="form-label" for="{{ $modalId }}Name">Nama kategori <span>*</span>
</label>
<input id="{{ $modalId }}Name" name="name" class="form-control" maxlength="80" value="{{ old('_category_form') === $modalId ? old('name') : $category?->name }}" required placeholder="Contoh: Perlengkapan Kantor">
</div>
<div class="mb-3">
<label class="form-label" for="{{ $modalId }}Description">Deskripsi</label>
<textarea id="{{ $modalId }}Description" name="description" class="form-control" rows="3" maxlength="255" placeholder="Jelaskan kelompok barang ini...">{{ old('_category_form') === $modalId ? old('description') : $category?->description }}</textarea>
</div>
<label class="form-label" for="{{ $modalId }}Color">Warna kategori</label>
<select class="form-select" name="color" id="{{ $modalId }}Color">@foreach(['green'=>'Hijau','blue'=>'Biru','purple'=>'Ungu','orange'=>'Oranye','pink'=>'Merah muda','slate'=>'Abu-abu'] as $key=>$label)<option value="{{ $key }}" @selected((old('_category_form') === $modalId ? old('color') : ($category?->color ?? 'green'))===$key)>{{ $label }}</option>@endforeach</select>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
<button type="submit" class="btn btn-primary" data-loading-text="Menyimpan...">{{ $category ? 'Simpan perubahan' : 'Simpan kategori' }}</button>
</div>
</form>
</div>
</div>
</div>
@endforeach
@endsection