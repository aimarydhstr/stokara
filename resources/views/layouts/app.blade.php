<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#142824">
    <title>@yield('title', 'Dashboard') · Stokara</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}" defer>
</script>
    <script src="{{ asset('js/app.js') }}" defer>
</script>
</head>
<body>
<a class="skip-link" href="#main-content">Lewati navigasi</a>
<div class="sidebar-scrim" data-sidebar-close>
</div>
<aside class="sidebar" id="sidebar" aria-label="Navigasi utama">
    <a class="brand" href="{{ route('dashboard') }}">
<span class="brand-mark">
<x-icon name="layers" size="25"/>
</span>
<span>stokara<span class="brand-dot">.</span>
</span>
</a>
    <div class="workspace-label">
<span class="workspace-symbol">G</span>
<div>Gudang ATK<small>Workspace utama</small>
</div>
<span class="online-dot">
</span>
</div>
    <div class="nav-section-label">WORKSPACE</div>
    <nav class="side-nav">
        <a href="{{ route('dashboard') }}" @class(['nav-item', 'active' => request()->routeIs('dashboard')])>
<x-icon name="grid"/>
<span>Dashboard</span>
</a>
        <a href="{{ route('items.index') }}" @class(['nav-item', 'active' => request()->routeIs('items.*')])>
<x-icon name="box"/>
<span>Data barang</span>
</a>
        <a href="{{ route('categories.index') }}" @class(['nav-item', 'active' => request()->routeIs('categories.*')])>
<x-icon name="folder"/>
<span>Kategori</span>
</a>
    </nav>
    <div class="nav-section-label mt-4">TRANSAKSI</div>
    <nav class="side-nav">
        <a href="{{ route('movements.create', ['type'=>'in']) }}" @class(['nav-item', 'active' => request()->routeIs('movements.create') && request('type','in') === 'in'])>
<x-icon name="in"/>
<span>Barang masuk</span>
</a>
        <a href="{{ route('movements.create', ['type'=>'out']) }}" @class(['nav-item', 'active' => request()->routeIs('movements.create') && request('type') === 'out'])>
<x-icon name="out"/>
<span>Barang keluar</span>
</a>
        <a href="{{ route('movements.index') }}" @class(['nav-item', 'active' => request()->routeIs('movements.index')])>
<x-icon name="history"/>
<span>Riwayat transaksi</span>
</a>
    </nav>
    <div class="sidebar-bottom">
        <div class="sidebar-tip">
<span class="tip-icon">
<x-icon name="spark" size="22"/>
</span>
<strong>Lebih rapi, lebih terkendali.</strong>
<p>Kelola persediaan dengan satu alur yang sederhana.</p>
<button class="tip-link" data-bs-toggle="modal" data-bs-target="#guideModal">Lihat panduan <x-icon name="arrow" size="16"/>
</button>
</div>
        <div class="sidebar-footer">
<span class="online-dot">
</span> Sistem operasional <span>v1.0</span>
</div>
    </div>
</aside>
<div class="app-shell">
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
<button class="icon-button mobile-menu" type="button" data-sidebar-toggle aria-label="Buka navigasi" aria-controls="sidebar" aria-expanded="false">
<x-icon name="menu"/>
</button>
<div class="breadcrumb-line">Workspace <x-icon name="chevron" size="13"/>
<span>@yield('title', 'Dashboard')</span>
</div>
</div>
        <div class="topbar-right">
            <form action="{{ route('items.index') }}" method="GET" class="global-search">
<x-icon name="search" size="17"/>
<input type="search" name="q" placeholder="Cari barang..." aria-label="Cari barang" maxlength="120">
<span class="search-shortcut">/</span>
</form>
            <button class="icon-button help-button" type="button" data-bs-toggle="modal" data-bs-target="#guideModal" aria-label="Panduan penggunaan">
<x-icon name="help"/>
</button>
            <span class="topbar-divider">
</span>
            <div class="dropdown">
<button class="profile-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
<span class="avatar">{{ mb_substr(auth()->user()->name,0,1) }}</span>
<span class="profile-copy">{{ auth()->user()->name }}<small>Administrator</small>
</span>
<x-icon name="down" size="14"/>
</button>
<div class="dropdown-menu dropdown-menu-end profile-dropdown">
<span class="dropdown-header">{{ auth()->user()->email }}</span>
<form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="dropdown-item text-danger">
<x-icon name="logout" size="16"/> Keluar akun</button>
</form>
</div>
</div>
        </div>
    </header>
    <main id="main-content" class="main-content">
        <div class="page-heading">
<div>
<div class="eyebrow">@yield('eyebrow', 'INVENTORY WORKSPACE')</div>
<h1>@yield('title', 'Dashboard')</h1>
<p>@yield('description', 'Pantau dan kelola persediaan gudang Anda.')</p>
</div>
<div class="page-actions">@yield('actions')</div>
</div>
        @if(session('success'))<div class="alert alert-success alert-dismissible fade show app-alert" role="status">
<x-icon name="check-circle"/> <span>{{ session('success') }}</span>
<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup pemberitahuan">
</button>
</div>@endif
        @if(session('error'))<div class="alert alert-danger alert-dismissible fade show app-alert" role="alert">
<x-icon name="alert"/> <span>{{ session('error') }}</span>
<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup pemberitahuan">
</button>
</div>@endif
        @if($errors->any())<div class="alert alert-danger app-alert align-items-start" role="alert">
<x-icon name="alert"/>
<div>
<strong>Periksa kembali isian Anda.</strong>
<ul class="mb-0 mt-1 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>
</div>@endif
        @yield('content')
        <footer class="main-footer">
<span>© {{ date('Y') }} Stokara. Semua stok, dalam kendali.</span>
<span>
<span class="online-dot">
</span> Gudang ATK · Workspace utama</span>
</footer>
    </main>
</div>
<div class="modal fade" id="guideModal" tabindex="-1" aria-labelledby="guideTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
<h2 class="modal-title fs-5" id="guideTitle">Mulai dengan Stokara</h2>
<button class="btn-close" data-bs-dismiss="modal" aria-label="Tutup panduan">
</button>
</div>
<div class="modal-body">
<ol class="guide-list">
<li>
<strong>Siapkan kategori dan barang.</strong>
<p>Kelompokkan barang, tentukan satuan, harga, dan batas stok minimum.</p>
</li>
<li>
<strong>Catat barang masuk.</strong>
<p>Stok bertambah setelah transaksi berhasil disimpan.</p>
</li>
<li>
<strong>Catat barang keluar.</strong>
<p>Pastikan jumlah sesuai pemakaian. Stok yang tidak cukup akan ditolak.</p>
</li>
<li>
<strong>Pantau persediaan.</strong>
<p>Periksa stok menipis dan unduh riwayat melalui menu transaksi.</p>
</li>
</ol>
<div class="soft-note">Stok berubah melalui transaksi. Riwayat yang sudah dicatat disimpan untuk menjaga ketertelusuran.</div>
</div>
</div>
</div>
</div>
</body>
</html>