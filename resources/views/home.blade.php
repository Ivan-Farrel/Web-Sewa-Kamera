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
        <div class="section-heading">
            <div>
                <h2>Kamera populer</h2>
                <p>Pilihan kamera favorit untuk setiap momen.</p>
            </div>
            <a href="{{ route('products.index') }}">Lihat semua →</a>
        </div>

        <div class="product-grid">
            {{-- Mengambil gambar langsung dari public/images/ --}}
            @foreach ($products as $product)
                <a href="{{ route('products.show', $product->id) }}" class="product-card">
                    <img 
                        src="{{ $product->image ? asset('images/' . $product->image) : 'https://placehold.co/400x300?text=No+Image' }}" 
                        alt="{{ $product->name }}" 
                        class="camera-image"
                        style="object-fit: cover; width: 100%; height: 180px; border-radius: 8px 8px 0 0;"
                    >
                    <span class="badge">{{ $product->category }}</span>
                    <h3>{{ $product->name }}</h3>
                    <div class="price">
                        Rp {{ number_format($product->price_24h, 0, ',', '.') }}<small>/24 jam</small>
                    </div>
                    <p class="stars">★ 5.0 <span>(Baru)</span></p>
                </a>
            @endforeach
        </div>
    </section>
</main>
@endsection