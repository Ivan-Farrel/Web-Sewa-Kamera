@extends('layouts.app')
@section('title', 'Katalog Produk - CamHub')

@section('content')
<main class="page catalog-page" style="background-color: #f8fafc; min-height: 100vh;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 2rem 1rem; display: flex; gap: 2rem;">
        
        <!-- Sidebar Filter -->
        <aside class="sidebar-filter" style="width: 240px; flex-shrink: 0;">
            <div style="background: #ffffff; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                
                <!-- Kategori -->
                <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">Kategori</h3>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach(['Semua', 'Kamera Body', 'Lensa', 'Audio', 'Lighting', 'Aksesoris'] as $index => $cat)
                        <label style="display: flex !important; align-items: center !important; justify-content: flex-start !important; gap: 10px !important; cursor: pointer; width: 100%; margin: 0;">
                            <input type="radio" name="category" value="{{ strtolower($cat) }}" {{ $index === 0 ? 'checked' : '' }} style="margin: 0; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;">
                            <span style="font-size: 0.9rem; color: #334155; font-weight: 500; white-space: nowrap;">{{ $cat }}</span>
                        </label>
                    @endforeach
                </div>

                <hr style="margin: 1.25rem 0; border: none; border-top: 1px solid #e2e8f0;">

                <!-- Tipe Transaksi -->
                <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">Tipe transaksi</h3>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <label style="display: flex !important; align-items: center !important; justify-content: flex-start !important; gap: 10px !important; cursor: pointer; width: 100%; margin: 0;">
                        <input type="checkbox" name="type[]" value="sewa" checked style="margin: 0; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;">
                        <span style="font-size: 0.9rem; color: #334155; font-weight: 500; white-space: nowrap;">Sewa</span>
                    </label>
                    <label style="display: flex !important; align-items: center !important; justify-content: flex-start !important; gap: 10px !important; cursor: pointer; width: 100%; margin: 0;">
                        <input type="checkbox" name="type[]" value="jual_beli" style="margin: 0; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;">
                        <span style="font-size: 0.9rem; color: #334155; font-weight: 500; white-space: nowrap;">Jual beli</span>
                    </label>
                    <label style="display: flex !important; align-items: center !important; justify-content: flex-start !important; gap: 10px !important; cursor: pointer; width: 100%; margin: 0;">
                        <input type="checkbox" name="type[]" value="titip_jual" style="margin: 0; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0;">
                        <span style="font-size: 0.9rem; color: #334155; font-weight: 500; white-space: nowrap;">Titip jual</span>
                    </label>
                </div>

                <hr style="margin: 1.25rem 0; border: none; border-top: 1px solid #e2e8f0;">

                <!-- Harga per hari -->
                <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">Harga per hari</h3>
                <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 1rem;">
                    <input type="number" placeholder="Min" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; outline: none;">
                    <span style="color: #94a3b8;">—</span>
                    <input type="number" placeholder="Maks" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; outline: none;">
                </div>
                <button type="button" style="width: 100%; background: #2563eb; color: white; padding: 0.6rem; border: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer;">Terapkan</button>
            </div>
        </aside>

        <!-- Main Content Katalog -->
        <section class="catalog-content" style="flex-grow: 1;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem;">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: #2563eb; letter-spacing: 0.05em; text-transform: uppercase;">JELAJAHI KOLEKSI</span>
                    <h1 style="font-size: 1.875rem; font-weight: 800; color: #0f172a; margin-top: 0.25rem;">Kamera Body</h1>
                    <p style="color: #64748b; font-size: 0.9rem; margin-top: 0.25rem;">{{ count($products) }} produk tersedia</p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: #64748b;">
                    <span>Urutkan</span>
                    <select style="padding: 0.4rem 0.8rem; border: 1px solid #cbd5e1; border-radius: 6px; background: white; font-size: 0.9rem; color: #334155; outline: none;">
                        <option>Terbaru</option>
                        <option>Harga Terendah</option>
                        <option>Harga Tertinggi</option>
                    </select>
                </div>
            </div>

            <!-- Grid Produk Dinamis -->
            <div class="product-grid">
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

    </div>
</main>
@endsection