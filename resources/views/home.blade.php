@extends('layouts.app')
@section('title', 'Sewa Kamera dengan Mudah - CamHub')
@section('content')
<main class="page home-page">
    <section class="home-hero">
        <div class="hero-content">
            <span class="hero-eyebrow">Marketplace kamera terpercaya</span>
            <h1>Kamera impianmu,<br>lebih dekat di CamHub</h1>
            <p>Sewa, jual, beli, atau titip kamera dan lensa dengan aman, mudah, dan terpercaya.</p>
        </div>
    </section>
    <section class="service-strip">
        <div><span class="service-icon">▣</span><span><strong>Sewa Kamera</strong><small>Mulai dari kebutuhanmu</small></span></div>
        <div><span class="service-icon">◉</span><span><strong>Jual Beli</strong><small>Kamera &amp; lensa berkualitas</small></span></div>
        <div><span class="service-icon">↗</span><span><strong>Titip Jual</strong><small>Jangkau lebih banyak</small></span></div>
        <div><span class="service-icon">✓</span><span><strong>Aman &amp; Terpercaya</strong><small>Transaksi terlindungi</small></span></div>
    </section>
    <section class="popular-section">
        <div class="section-heading"><div><h2>Produk populer</h2><p>Pilihan kamera favorit untuk setiap momen.</p></div><a href="{{ route('products.index') }}">Lihat semua →</a></div>
        <div class="product-grid">
            @foreach ([['Sony A7 III',250000,'4.8'],['Canon EOS R50',180000,'4.7'],['Fujifilm X-T30',200000,'4.8'],['Sony Alpha 6400',170000,'4.7']] as [$name,$price,$rating])
                <a href="{{ route('products.show') }}" class="product-card"><div class="camera-image"></div><span class="badge">Sewa</span><h3>{{ $name }}</h3><div class="price">Rp {{ number_format($price, 0, ',', '.') }}<small>/hari</small></div><p class="stars">★ {{ $rating }} <span>(120 ulasan)</span></p></a>
            @endforeach
        </div>
    </section>
</main>
@endsection
