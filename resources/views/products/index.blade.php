@extends('layouts.app')
@section('title', 'Katalog Produk - CamHub')
@section('content')
<main class="page product-layout">
    <form class="panel filters" method="GET" action="{{ route('products.index') }}">
        <h3>Kategori</h3>
        <div class="category-options"><label><input type="radio" name="kategori" value="" @checked(empty($filters['kategori']))> Semua</label>@foreach (['Kamera Body','Lensa','Audio','Lighting','Aksesoris'] as $category)<label><input type="radio" name="kategori" value="{{ $category }}" @checked(($filters['kategori'] ?? '') === $category)> {{ $category }}</label>@endforeach</div>
        <h3 class="filter-title">Tipe transaksi</h3><label class="check-option"><input type="checkbox" checked> Sewa</label><label class="check-option"><input type="checkbox"> Jual beli</label><label class="check-option"><input type="checkbox"> Titip jual</label>
        <h3 class="filter-title">Harga per hari</h3><div class="price-range"><input aria-label="Harga minimum" type="number" name="harga_min" min="0" value="{{ $filters['harga_min'] ?? '' }}" placeholder="Min"><span>—</span><input aria-label="Harga maksimum" type="number" name="harga_max" min="0" value="{{ $filters['harga_max'] ?? '' }}" placeholder="Maks"></div>
        <button class="btn" type="submit">Terapkan</button>
    </form>
    <section class="catalog-content">
        <div class="catalog-heading"><div><p class="eyebrow">Jelajahi koleksi</p><h1>Kamera Body</h1><span>{{ $products->count() }} produk tersedia</span></div><label class="sort-control">Urutkan <select><option>Terbaru</option><option>Harga terendah</option></select></label></div>
        <div class="product-grid">
            @forelse ($products as $product)
                <a href="{{ route('products.show') }}" class="product-card"><div class="camera-image"></div><h3>{{ $product['name'] }}</h3><div class="price">Rp {{ number_format($product['price'], 0, ',', '.') }}<small>/hari</small></div><p class="stars">★ {{ $product['rating'] }}</p><span class="badge">Sewa</span></a>
            @empty
                <p>Tidak ada produk yang sesuai filter. <a href="{{ route('products.index') }}">Hapus filter</a></p>
            @endforelse
        </div>
    </section>
</main>
@endsection
