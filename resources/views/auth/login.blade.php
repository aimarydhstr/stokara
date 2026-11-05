<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk · Stokara</title>
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<script src="{{ asset('js/app.js') }}" defer>
</script>
</head>
<body class="login-page">
<main class="login-shell">
<section class="login-story">
<a class="brand" href="{{ route('login') }}">
<span class="brand-mark">
<x-icon name="layers" size="25"/>
</span>
<span>stokara<span class="brand-dot">.</span>
</span>
</a>
<div class="login-story-content">
<span class="banner-tag">
<span>
</span> YOUR INVENTORY, SIMPLIFIED</span>
<h1>Setiap barang.<br>Setiap pergerakan.<br>
<em>Dalam kendali.</em>
</h1>
<p>Ruang kerja yang lebih tertata untuk mengelola persediaan dan menjaga operasional tetap berjalan.</p>
<div class="login-illustration" aria-hidden="true">
<div class="login-stock-card">
<div>
<span class="stat-icon color-green">
<x-icon name="box" size="25"/>
</span>
<span>Persediaan terpantau<small>Semua dalam satu tempat</small>
</span>
<span class="login-check">
<x-icon name="check" size="17"/>
</span>
</div>
<div class="illustration-bars">
<span>
</span>
<span>
</span>
<span>
</span>
<span>
</span>
<span>
</span>
<span>
</span>
<span>
</span>
<span>
</span>
<span>
</span>
</div>
</div>
<span class="login-floating-tag">
<x-icon name="check-circle" size="17"/> Lebih rapi. Lebih efisien.</span>
</div>
</div>
<div class="login-story-footer">DIRANCANG UNTUK OPERASIONAL YANG LEBIH BAIK <span>© {{ date('Y') }} STOKARA</span>
</div>
</section>
<section class="login-form-section">
<div class="login-form-wrap">
<span class="login-kicker">
<span class="online-dot">
</span> WORKSPACE GUDANG ATK</span>
<h2>Selamat datang kembali.</h2>
<p class="login-intro">Masuk untuk melanjutkan pengelolaan inventori Anda.</p>@if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif<form method="POST" action="{{ route('login.store') }}">@csrf<div class="mb-4">
<label for="email" class="form-label">Alamat email</label>
<input id="email" name="email" type="email" class="form-control" placeholder="nama@perusahaan.com" value="{{ old('email') }}" maxlength="255" required autofocus autocomplete="username">
</div>
<div class="mb-3">
<label for="password" class="form-label">Kata sandi</label>
<div class="password-field">
<input id="password" name="password" type="password" class="form-control" placeholder="Masukkan kata sandi Anda" required autocomplete="current-password">
<button type="button" class="password-toggle" data-password-toggle aria-label="Tampilkan kata sandi" aria-pressed="false">
<x-icon name="eye" size="19"/>
</button>
</div>
</div>
<div class="form-check mb-4">
<input type="checkbox" class="form-check-input" name="remember" value="1" id="remember">
<label class="form-check-label" for="remember">Ingat saya di perangkat ini</label>
</div>
<button type="submit" class="btn btn-primary login-submit" data-loading-text="Memverifikasi...">Masuk ke workspace <x-icon name="arrow" size="18"/>
</button>
</form>
<div class="login-security">
<x-icon name="lock" size="15"/> Akses terbatas untuk petugas gudang.</div>
</div>
<div class="login-bottom">Stokara Inventory Management <span>v1.0</span>
</div>
</section>
</main>
</body>
</html>