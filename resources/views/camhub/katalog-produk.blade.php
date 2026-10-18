@extends('layouts.camhub')

@section('title', 'CamHub — Katalog Produk')

@section('navbar')
<header class="main-navbar catalog-navbar">
    <a href="{{ route('camhub.home') }}" class="brand">CamHub</a>
    <nav class="nav-links">
        <a href="{{ route('camhub.home') }}">Beranda</a>
        <a href="{{ route('camhub.katalog') }}">Kategori</a>
        <a href="{{ route('camhub.katalog') }}">Sewa</a>
        <a href="{{ route('camhub.login') }}">Jual Beli</a>
        <a href="{{ route('camhub.login') }}">Titip Jual</a>
    </nav>
</header>
@endsection

@section('content')
<div class="catalog-layout">
    <aside class="filter-sidebar">
        <h3>Kategori</h3>
        <form id="catalog-filters">
            <input type="hidden" name="q" value="{{ request('q') }}">
            @foreach(['' => 'Semua', 'Kamera Body' => 'Kamera Body', 'Lensa' => 'Lensa', 'Audio' => 'Audio', 'Lighting' => 'Lighting', 'Aksesoris' => 'Aksesoris'] as $value => $label)
                <label class="filter-option"><input type="radio" name="kategori" value="{{ $value }}" {{ request('kategori', '') === $value ? 'checked' : '' }}> {{ $label }}</label>
            @endforeach
            <h3 class="filter-title">Harga sewa per hari</h3>
            <div class="price-filter">
                <input type="number" name="harga_min" value="{{ request('harga_min') }}" placeholder="Min">
                <input type="number" name="harga_max" value="{{ request('harga_max') }}" placeholder="Maks">
            </div>
            <button class="btn btn-block" type="submit">Terapkan</button>
        </form>
    </aside>
    <section class="catalog-content">
        <div class="catalog-heading"><h1>Kamera Body</h1><p class="muted">Temukan kamera untuk kebutuhanmu.</p></div>
        <div class="catalog-grid" id="catalog-grid" data-endpoint="{{ route('camhub.api.katalog') }}" aria-live="polite"><p class="muted">Memuat katalog…</p></div>
    </section>
</div>
<script>
const catalogGrid = document.getElementById('catalog-grid');
const catalogForm = document.getElementById('catalog-filters');
let catalogParams = new URLSearchParams(window.location.search);
const rupiah = new Intl.NumberFormat('id-ID');
async function loadCatalog() {
    catalogGrid.innerHTML = '<p class="muted">Memuat katalog…</p>';
    const response = await fetch(`${catalogGrid.dataset.endpoint}?${catalogParams.toString()}`, { headers: { Accept: 'application/json' } });
    if (!response.ok) throw new Error('Catalog request failed');
    const { data } = await response.json();
    catalogGrid.innerHTML = data.length ? data.map((product) => `
        <article class="catalog-card">
            <a href="/camhub/produk/sony-a7-iii" class="catalog-image" aria-label="Lihat ${product.name}"><span>📷</span></a>
            <h3>${product.name}</h3>
            <p class="product-price">Rp ${rupiah.format(product.price)} <small>/hari</small></p>
            <p class="rating">★ ${product.rating}</p>
            <a href="/camhub/produk/sony-a7-iii" class="btn btn-small">Lihat produk</a>
        </article>`).join('') : '<p class="empty-state">Produk tidak ditemukan. Coba ubah filter.</p>';
}
catalogForm.addEventListener('submit', (event) => {
    event.preventDefault();
    const nextParams = new URLSearchParams();
    for (const [key, value] of new FormData(catalogForm)) if (value) nextParams.set(key, value);
    history.replaceState(null, '', `${location.pathname}?${nextParams.toString()}`);
    catalogParams = nextParams;
    loadCatalog().catch(showCatalogError);
});
function showCatalogError() { catalogGrid.innerHTML = '<p class="form-errors">Katalog gagal dimuat. Silakan muat ulang halaman.</p>'; }
loadCatalog().catch(showCatalogError);
</script>
@endsection
