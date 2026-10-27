@extends('layouts.app')
@section('title', 'Dashboard Mitra - CamHub')
@section('content')
<main class="page">
    <h1>Dashboard Mitra</h1>
    <p>Halo, {{ auth()->user()->name }}. Kelola aktivitas penyewaan Anda di CamHub.</p>
    <div class="stats-grid">
        <div class="stat-card"><p>Akun mitra</p><h2>{{ auth()->user()->email }}</h2></div>
        <div class="stat-card"><p>Katalog kamera</p><h2><a href="{{ route('products.index') }}">Lihat produk</a></h2></div>
        <div class="stat-card"><p>Pesanan</p><h2><a href="{{ route('camhub.dashboard-pelanggan') }}">Buka dashboard</a></h2></div>
    </div>
    <section class="panel"><h2>Produk tersedia</h2><p>Daftar produk dan harga sewa tersedia di katalog publik.</p><a class="btn" href="{{ route('products.index') }}">Buka katalog</a></section>
</main>
@endsection
