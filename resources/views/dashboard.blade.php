@extends('layouts.app')
@section('title', 'Dashboard')
@section('eyebrow', 'GAMBARAN UMUM')
@section('description', 'Selamat datang kembali. Ini ringkasan persediaan gudang Anda.')
@section('actions')<span class="date-pill">
<x-icon name="calendar" size="16"/>{{ today()->translatedFormat('d F Y') }}</span>
<a class="btn btn-primary" href="{{ route('movements.create') }}">
<x-icon name="plus" size="18"/>Catat transaksi</a>@endsection
@section('content')
<div class="welcome-banner">
<div class="banner-orbit orbit-one">
</div>
<div class="banner-orbit orbit-two">
</div>
<div class="welcome-copy">
<span class="banner-tag">
<span>
</span> INVENTORI LEBIH TERATUR</span>
<h2>Persediaan terpantau.<br>Operasional tetap lancar.</h2>
<p>Satu tempat untuk setiap barang dan pergerakannya.</p>
<a href="{{ route('items.index') }}">Jelajahi inventori <x-icon name="arrow" size="17"/>
</a>
</div>
<div class="banner-visual" aria-hidden="true">
<div class="visual-grid">
</div>
<div class="visual-card card-back">
<x-icon name="layers" size="32"/>
</div>
<div class="visual-card card-front">
<span class="visual-box">
<x-icon name="box" size="43"/>
</span>
<span class="visual-line">
</span>
<span class="visual-line short">
</span>
<span class="visual-check">
<x-icon name="check" size="16"/>
</span>
</div>
<div class="visual-float">
<span class="online-dot">
</span> Semua tercatat</div>
</div>
</div>
<div class="section-heading">
<h2>Ringkasan gudang</h2>
<form method="GET" class="period-form">
<label for="period" class="visually-hidden">Periode ringkasan</label>
<select id="period" name="period" class="form-select form-select-sm" data-auto-submit>@foreach([7,14,30] as $period)<option value="{{ $period }}" @selected($days === $period)>{{ $period }} hari terakhir</option>@endforeach</select>
<noscript>
<button class="btn btn-sm btn-light">Terapkan</button>
</noscript>
</form>
</div>
<div class="stats-grid">
@foreach([
 ['label'=>'Total jenis barang','value'=>$stats['items'],'icon'=>'box','color'=>'green','note'=>'Dalam '.count($categories).' kategori','link'=>route('items.index')],
 ['label'=>'Barang masuk','value'=>$stats['incoming'],'icon'=>'in','color'=>'blue','note'=>$days.' hari terakhir · seluruh satuan','link'=>route('movements.index',['type'=>'in','from'=>today()->subDays($days-1)->toDateString()])],
 ['label'=>'Barang keluar','value'=>$stats['outgoing'],'icon'=>'out','color'=>'purple','note'=>$days.' hari terakhir · seluruh satuan','link'=>route('movements.index',['type'=>'out','from'=>today()->subDays($days-1)->toDateString()])],
 ['label'=>'Perlu restok','value'=>$stats['low'],'icon'=>'alert','color'=>'orange','note'=>'Stok mencapai batas minimum','link'=>route('items.index',['status'=>'low'])],
] as $stat)
<a class="stat-card" href="{{ $stat['link'] }}">
<div class="stat-top">
<span>{{ $stat['label'] }}</span>
<span class="stat-icon color-{{ $stat['color'] }}">
<x-icon :name="$stat['icon']" size="20"/>
</span>
</div>
<div class="stat-value">{{ number_format($stat['value'],0,',','.') }}<x-icon name="arrow-up" size="18"/>
</div>
<div class="stat-note">{{ $stat['note'] }}</div>
</a>
@endforeach
</div>
<div class="dashboard-middle">
<section class="panel chart-panel">
<div class="panel-heading">
<div>
<h2>Aktivitas persediaan</h2>
<p>Jumlah barang masuk dan keluar · {{ $days }} hari terakhir</p>
</div>
<div class="chart-legend">
<span>
<i class="legend-in">
</i>Masuk</span>
<span>
<i class="legend-out">
</i>Keluar</span>
</div>
</div>
@php
$max = max(1, $series->max('in'), $series->max('out'));
$incomingPoints = []; $outgoingPoints = [];
foreach($series as $i => $point) { $x = 48 + ($i / max(1, count($series)-1)) * 592; $incomingPoints[] = round($x,2).','.round(190-($point['in']/$max)*145,2); $outgoingPoints[] = round($x,2).','.round(190-($point['out']/$max)*145,2); }
@endphp
<div class="chart-wrap">
<svg viewBox="0 0 680 235" role="img" aria-labelledby="chartTitle">
<title id="chartTitle">Grafik jumlah barang masuk dan keluar selama {{ $days }} hari terakhir</title>
<defs>
<linearGradient id="areaIn" x1="0" y1="0" x2="0" y2="1">
<stop offset="0%" stop-color="#61b995" stop-opacity=".22"/>
<stop offset="100%" stop-color="#61b995" stop-opacity="0"/>
</linearGradient>
</defs>@foreach([0,1,2,3] as $level)@php($y=190-$level/3*145)<line x1="48" y1="{{ $y }}" x2="640" y2="{{ $y }}" stroke="#e9eeeb" stroke-dasharray="4 5"/>
<text x="32" y="{{ $y+4 }}" text-anchor="end" class="chart-label">{{ round($max*$level/3) }}</text>@endforeach<polygon points="48,190 {{ implode(' ', $incomingPoints) }} 640,190" fill="url(#areaIn)"/>
<polyline points="{{ implode(' ', $outgoingPoints) }}" fill="none" stroke="#a7b6ca" stroke-width="2.5" stroke-dasharray="5 5" stroke-linejoin="round"/>
<polyline points="{{ implode(' ', $incomingPoints) }}" fill="none" stroke="#28856c" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>@foreach($series as $i=>$point)@if($i===0 || $i===count($series)-1 || $i % max(1,(int) ceil(count($series)/7)) === 0)<text x="{{ 48+$i/max(1,count($series)-1)*592 }}" y="222" text-anchor="middle" class="chart-label">{{ $point['label'] }}</text>@endif
@endforeach</svg>
</div>
<div class="chart-footer">
<span>
<x-icon name="history" size="14"/> Ringkasan berdasarkan tanggal transaksi</span>
<a href="{{ route('movements.index') }}">Lihat detail <x-icon name="arrow" size="14"/>
</a>
</div>
</section>
<section class="panel stock-watch">
<div class="panel-heading">
<div>
<h2>Perlu perhatian <span class="count-pill">{{ $stats['low'] }}</span>
</h2>
<p>Prioritaskan pengisian stok berikut.</p>
</div>
<span class="attention-icon">
<x-icon name="alert" size="19"/>
</span>
</div>
<div class="watch-list">@forelse($lowItems as $item)<a class="watch-item" href="{{ route('items.show',$item) }}">
<span class="item-symbol color-{{ $item->category->color }}">
<x-icon name="box" size="20"/>
</span>
<div>
<strong>{{ $item->name }}</strong>
<small>Minimum {{ $item->minimum_stock }} {{ $item->unit }}</small>
</div>
<span class="watch-quantity {{ $item->stock===0 ? 'is-empty' : '' }}">{{ $item->stock }}<small>{{ $item->unit }}</small>
</span>
</a>@empty<div class="empty-state small">
<span class="empty-icon">
<x-icon name="check-circle" size="25"/>
</span>
<h3>Persediaan aman</h3>
<p>Semua barang berada di atas batas minimum.</p>
</div>@endforelse</div>
<a href="{{ route('items.index',['status'=>'low']) }}" class="watch-link">Lihat semua stok minimum <x-icon name="arrow" size="15"/>
</a>
</section>
</div>
<div class="dashboard-bottom">
<section class="panel recent-panel">
<div class="panel-heading">
<div>
<h2>Transaksi terbaru</h2>
<p>Pergerakan terakhir yang tercatat di gudang.</p>
</div>
<a href="{{ route('movements.index') }}" class="text-link">Lihat semua <x-icon name="arrow" size="15"/>
</a>
</div>
<x-movement-table :movements="$recent" :compact="true"/>
</section>
<section class="panel category-summary">
<div class="panel-heading">
<div>
<h2>Komposisi inventori</h2>
<p>Jumlah jenis barang per kategori.</p>
</div>
</div>
<div class="category-bars">@forelse($categories as $category)<div class="category-bar-row">
<div>
<span class="color-dot dot-{{ $category->color }}">
</span>
<span>{{ $category->name }}</span>
<strong>{{ $category->items_count }}</strong>
</div>
<div class="category-track">
<span class="bar-{{ $category->color }}" style="width:{{ $stats['items'] ? round($category->items_count/$stats['items']*100) : 0 }}%">
</span>
</div>
</div>@empty<p class="text-muted">Kategori belum tersedia.</p>@endforelse</div>
<div class="inventory-value">
<span>
<x-icon name="wallet" size="18"/> Estimasi nilai persediaan</span>
<strong>Rp {{ number_format($stats['value'],0,',','.') }}</strong>
<small>Berdasarkan stok × harga satuan saat ini</small>
</div>
</section>
</div>
@endsection