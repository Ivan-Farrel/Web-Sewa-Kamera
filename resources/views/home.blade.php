@extends('layouts.app')
@section('title', 'CamHub - Sewa, Jual Beli & Titip Jual Kamera')

@section('content')
@php
    use Illuminate\Pagination\AbstractPaginator;
    use Illuminate\Support\Facades\Route;

    $allProducts = isset($products)
        ? ($products instanceof AbstractPaginator ? collect($products->items()) : collect($products))
        : \App\Models\Product::latest()->get();

    $resolveImage = function ($p) {
        $raw = $p->image ?? $p->image_url ?? $p->image_path ?? $p->photo ?? null;
        if (!$raw) {
            return null;
        }
        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://') || str_starts_with($raw, '/')) {
            return $raw;
        }
        if (file_exists(public_path($raw))) {
            return asset($raw);
        }
        if (file_exists(public_path('images/' . $raw))) {
            return asset('images/' . $raw);
        }
        if (file_exists(public_path('storage/' . $raw))) {
            return asset('storage/' . $raw);
        }
        return asset($raw);
    };

    $resolveDetailUrl = function ($p) {
        return Route::has('products.show')
            ? route('products.show', $p->id)
            : url('/products/' . $p->id);
    };

    $classify = function ($p) {
        $cat = strtolower(trim((string) ($p->category ?? '')));
        $desc = strtolower(trim((string) ($p->description ?? '')));
        $scheme = strtolower(trim((string) ($p->listing_type ?? '')));
        $sellPrice = (float) ($p->sell_price ?? $p->price_sell ?? 0);
        $rentPrice = (float) ($p->price_24h ?? $p->price ?? 0);

        $isC2B = $scheme === 'titip_jual_c2b'
            || str_contains($desc, 'titip jual')
            || str_contains($desc, 'buyback (c2b)');

        $isJualPutus = !$isC2B && (
            in_array($scheme, ['jual_putus', 'hybrid_c2c'])
            || $sellPrice > 0
            || str_contains($desc, 'jual putus')
        );

        $isPureSale = $isC2B || $scheme === 'jual_putus' || ($rentPrice <= 0 && $isJualPutus);
        $isLensa = str_contains($cat, 'lensa') || str_contains($cat, 'lens');
        $isAksesoris = str_contains($cat, 'audio')
            || str_contains($cat, 'mic')
            || str_contains($cat, 'gimbal')
            || str_contains($cat, 'stabilizer')
            || str_contains($cat, 'lighting')
            || str_contains($cat, 'aksesoris');
        $isKamera = !$isLensa && !$isAksesoris;

        return compact('isC2B', 'isJualPutus', 'isPureSale', 'isLensa', 'isAksesoris', 'isKamera', 'sellPrice', 'rentPrice');
    };

    $kameraList = $allProducts->filter(function ($p) use ($classify) {
        $c = $classify($p);
        return !$c['isPureSale'] && $c['isKamera'];
    })->values();

    $lensaList = $allProducts->filter(function ($p) use ($classify) {
        $c = $classify($p);
        return !$c['isPureSale'] && $c['isLensa'];
    })->values();

    $aksesorisList = $allProducts->filter(function ($p) use ($classify) {
        $c = $classify($p);
        return !$c['isPureSale'] && $c['isAksesoris'];
    })->values();

    $jualPutusList = $allProducts->filter(function ($p) use ($classify) {
        $c = $classify($p);
        return $c['isJualPutus'];
    })->values();

    $titipJualList = $allProducts->filter(function ($p) use ($classify) {
        $c = $classify($p);
        return $c['isC2B'];
    })->values();

    $ctaMitraUrl = auth()->check() && in_array(auth()->user()->role ?? '', ['vendor', 'mitra', 'admin'])
        ? (Route::has('vendor.dashboard') ? route('vendor.dashboard') : url('/vendor'))
        : (Route::has('camhub.login') ? route('camhub.login') . '#register-form' : url('/login#register-form'));
@endphp

<style>
    html {
        scroll-behavior: smooth;
    }
    .ch-home {
        max-width: 1220px;
        margin: 0 auto;
        padding: 1.5rem 1.25rem 4rem;
    }
    .ch-hero {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        padding: 3.75rem 3rem;
        color: #ffffff;
        background:
            radial-gradient(circle at 80% 35%, rgba(37, 99, 235, 0.35), transparent 55%),
            linear-gradient(125deg, #0b1120 0%, #111827 52%, #1e293b 100%);
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.14);
        margin-bottom: 1.5rem;
    }
    .ch-hero-badge {
        display: inline-block;
        background: rgba(37, 99, 235, 0.28);
        border: 1px solid rgba(96, 165, 250, 0.45);
        color: #bfdbfe;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        margin-bottom: 1rem;
    }
    .ch-hero h1 {
        font-size: clamp(1.85rem, 3.6vw, 2.75rem);
        font-weight: 800;
        line-height: 1.18;
        margin: 0 0 0.85rem;
        max-width: 560px;
    }
    .ch-hero p {
        color: #cbd5e1;
        font-size: 1rem;
        line-height: 1.6;
        max-width: 500px;
        margin: 0 0 1.5rem;
    }
    .ch-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .ch-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #2563eb;
        color: #ffffff;
        padding: 0.72rem 1.25rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        text-decoration: none;
    }
    .ch-btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.22);
        color: #ffffff;
        padding: 0.72rem 1.25rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
    }
    .ch-shortcuts {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 2.75rem;
    }
    @media (max-width: 900px) {
        .ch-shortcuts {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .ch-hero {
            padding: 2.5rem 1.5rem;
        }
    }
    .ch-shortcut-card {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1rem 1.15rem;
        text-decoration: none;
        color: inherit;
        transition: border-color 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
    }
    .ch-shortcut-card:hover {
        border-color: #93c5fd;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.08);
    }
    .ch-shortcut-icon {
        width: 42px;
        height: 42px;
        border-radius: 11px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .ch-section {
        margin-bottom: 3rem;
        scroll-margin-top: 1.5rem;
    }
    .ch-section-head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-end;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }
    .ch-section-title {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .ch-section-sub {
        color: #64748b;
        font-size: 0.88rem;
        margin: 0.25rem 0 0;
    }
    .ch-see-all {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #2563eb;
        font-weight: 700;
        font-size: 0.88rem;
        text-decoration: none;
    }
    .ch-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1.15rem;
    }
    @media (max-width: 1024px) {
        .ch-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
    @media (max-width: 720px) {
        .ch-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 460px) {
        .ch-grid {
            grid-template-columns: 1fr;
        }
    }
    .ch-card {
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
        transition: border-color 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
    }
    .ch-card:hover {
        border-color: #93c5fd;
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }
    .ch-card-media {
        position: relative;
        height: 185px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        overflow: hidden;
    }
    .ch-card-media img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .ch-fallback-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        color: #94a3b8;
        font-size: 0.76rem;
        font-weight: 600;
        text-align: center;
    }
    .ch-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 700;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #dbeafe;
    }
    .ch-badge-green {
        background: #dcfce7;
        color: #15803d;
        border-color: #bbf7d0;
    }
    .ch-badge-amber {
        background: #fef3c7;
        color: #92400e;
        border-color: #fde68a;
    }
    .ch-card-body {
        padding: 0.95rem 1rem 1.05rem;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        flex: 1;
    }
    .ch-card-name {
        font-size: 0.94rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        line-height: 1.35;
    }
    .ch-card-price {
        font-size: 0.98rem;
        font-weight: 800;
        color: #2563eb;
        margin: 0;
    }
    .ch-card-price span {
        font-size: 0.78rem;
        font-weight: 500;
        color: #64748b;
    }
    .ch-card-meta {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.78rem;
        color: #64748b;
        margin-top: auto;
        padding-top: 0.3rem;
    }
    .ch-empty-banner {
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        padding: 1.75rem;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1.25rem;
    }
    .ch-c2b-banner {
        background: linear-gradient(120deg, #eff6ff 0%, #f8fafc 100%);
        border: 1px solid #bfdbfe;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.25rem;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1.25rem;
    }
    .ch-trust-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1.15rem;
    }
    @media (max-width: 820px) {
        .ch-trust-grid {
            grid-template-columns: 1fr;
        }
    }
    .ch-trust-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem;
    }
</style>

<main class="ch-home">
    {{-- ==================== HERO BANNER ==================== --}}
    <section class="ch-hero">
        <span class="ch-hero-badge">Marketplace Kamera Terpercaya</span>
        <h1>Kamera impianmu, lebih dekat di CamHub</h1>
        <p>Sewa durasi fleksibel, jual beli C2C, atau titip jual kamera dan lensa (C2B) dengan proteksi Rekening Bersama dan verifikasi KYC.</p>
        <div class="ch-hero-actions">
            <a href="#sewa-kamera" class="ch-btn-primary">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                    <circle cx="12" cy="13" r="3"/>
                </svg>
                <span>Mulai Sewa Sekarang</span>
            </a>
            <a href="#titip-jual" class="ch-btn-ghost">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/>
                    <path d="M7 7h.01"/>
                </svg>
                <span>Titip Jual / Jual Kamera</span>
            </a>
        </div>
    </section>

    {{-- ==================== 4 SHORTCUT KATEGORI ==================== --}}
    <section class="ch-shortcuts">
        <a href="#sewa-kamera" class="ch-shortcut-card">
            <div class="ch-shortcut-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                    <circle cx="12" cy="13" r="3"/>
                </svg>
            </div>
            <div>
                <strong style="display: block; font-size: 0.92rem; color: #0f172a;">Sewa Kamera</strong>
                <span style="font-size: 0.78rem; color: #64748b;">{{ $kameraList->count() }} unit DSLR &amp; Mirrorless</span>
            </div>
        </a>

        <a href="#sewa-lensa" class="ch-shortcut-card">
            <div class="ch-shortcut-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <circle cx="12" cy="12" r="4"/>
                    <line x1="21.17" x2="12" y1="8" y2="8"/>
                    <line x1="3.95" x2="8.54" y1="6.06" y2="14"/>
                    <line x1="10.88" x2="15.46" y1="21.94" y2="14"/>
                </svg>
            </div>
            <div>
                <strong style="display: block; font-size: 0.92rem; color: #0f172a;">Sewa Lensa</strong>
                <span style="font-size: 0.78rem; color: #64748b;">{{ $lensaList->count() }} pilihan lensa tajam</span>
            </div>
        </a>

        <a href="#jual-putus" class="ch-shortcut-card">
            <div class="ch-shortcut-icon" style="background: #f0fdf4; color: #16a34a;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                    <path d="M3 6h18"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
            </div>
            <div>
                <strong style="display: block; font-size: 0.92rem; color: #0f172a;">Jual Beli (C2C)</strong>
                <span style="font-size: 0.78rem; color: #64748b;">Kamera &amp; lensa berkualitas</span>
            </div>
        </a>

        <a href="#titip-jual" class="ch-shortcut-card">
            <div class="ch-shortcut-icon" style="background: #fffbeb; color: #d97706;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
            </div>
            <div>
                <strong style="display: block; font-size: 0.92rem; color: #0f172a;">Titip Jual (C2B)</strong>
                <span style="font-size: 0.78rem; color: #64748b;">Inspeksi &amp; garansi toko</span>
            </div>
        </a>
    </section>

    {{-- ==================== SECTION 1: KAMERA POPULER (DSLR & MIRRORLESS) ==================== --}}
    <section class="ch-section" id="sewa-kamera">
        <div class="ch-section-head">
            <div>
                <h2 class="ch-section-title">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                        <circle cx="12" cy="13" r="3"/>
                    </svg>
                    <span>Kamera Populer (DSLR &amp; Mirrorless)</span>
                </h2>
                <p class="ch-section-sub">Pilihan bodi kamera siap pakai untuk wisuda, event, pre-wedding, hingga produksi konten.</p>
            </div>
            <a href="{{ route('products.index') }}" class="ch-see-all">
                <span>Lihat semua kamera</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if ($kameraList->isEmpty())
            <div class="ch-empty-banner">
                <p style="margin: 0; color: #64748b;">Belum ada unit kamera sewa yang tersedia saat ini.</p>
            </div>
        @else
            <div class="ch-grid">
                @foreach ($kameraList->take(8) as $item)
                    @php
                        $imgSrc = $resolveImage($item);
                        $price24 = $item->price_24h ?? $item->price ?? 0;
                    @endphp
                    <a href="{{ $resolveDetailUrl($item) }}" class="ch-card">
                        <div class="ch-card-media">
                            <span class="ch-badge">{{ $item->category ?? 'Kamera' }}</span>
                            @if ($imgSrc)
                                <img src="{{ $imgSrc }}" alt="{{ $item->name }}"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="ch-fallback-box" style="display: {{ $imgSrc ? 'none' : 'flex' }};">
                                <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                                    <circle cx="12" cy="13" r="3"/>
                                </svg>
                                <span>Foto unit kamera</span>
                            </div>
                        </div>
                        <div class="ch-card-body">
                            <h3 class="ch-card-name">{{ $item->name }}</h3>
                            <p class="ch-card-price">
                                Rp {{ number_format($price24, 0, ',', '.') }} <span>/ 24 jam</span>
                            </p>
                            <div class="ch-card-meta">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" stroke-width="1.5" aria-hidden="true">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                                <span>{{ $item->rating ?? '5.0' }} (Siap Sewa)</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ==================== SECTION 2: LENSA PILIHAN ==================== --}}
    <section class="ch-section" id="sewa-lensa">
        <div class="ch-section-head">
            <div>
                <h2 class="ch-section-title">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <circle cx="12" cy="12" r="4"/>
                        <line x1="21.17" x2="12" y1="8" y2="8"/>
                        <line x1="3.95" x2="8.54" y1="6.06" y2="14"/>
                        <line x1="10.88" x2="15.46" y1="21.94" y2="14"/>
                    </svg>
                    <span>Lensa Pilihan (Fix, Zoom &amp; Wide)</span>
                </h2>
                <p class="ch-section-sub">Koleksi lensa Canon EF/RF, Sony FE/E, Sigma Art, hingga Tamron untuk hasil gambar lebih tajam.</p>
            </div>
            <a href="{{ route('products.index', ['category' => 'Lensa']) }}" class="ch-see-all">
                <span>Lihat semua lensa ({{ $lensaList->count() }})</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if ($lensaList->isEmpty())
            <div class="ch-empty-banner">
                <p style="margin: 0; color: #64748b;">Belum ada unit lensa yang tersedia saat ini.</p>
            </div>
        @else
            <div class="ch-grid">
                @foreach ($lensaList->take(8) as $item)
                    @php
                        $imgSrc = $resolveImage($item);
                        $price24 = $item->price_24h ?? $item->price ?? 0;
                    @endphp
                    <a href="{{ $resolveDetailUrl($item) }}" class="ch-card">
                        <div class="ch-card-media">
                            <span class="ch-badge">Lensa</span>
                            @if ($imgSrc)
                                <img src="{{ $imgSrc }}" alt="{{ $item->name }}"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="ch-fallback-box" style="display: {{ $imgSrc ? 'none' : 'flex' }};">
                                <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"/>
                                    <circle cx="12" cy="12" r="4"/>
                                    <line x1="21.17" x2="12" y1="8" y2="8"/>
                                    <line x1="3.95" x2="8.54" y1="6.06" y2="14"/>
                                    <line x1="10.88" x2="15.46" y1="21.94" y2="14"/>
                                </svg>
                                <span>{{ $item->name }}</span>
                            </div>
                        </div>
                        <div class="ch-card-body">
                            <h3 class="ch-card-name">{{ $item->name }}</h3>
                            <p class="ch-card-price">
                                Rp {{ number_format($price24, 0, ',', '.') }} <span>/ 24 jam</span>
                            </p>
                            <div class="ch-card-meta">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" stroke-width="1.5" aria-hidden="true">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                                <span>{{ $item->rating ?? '5.0' }} (Siap Sewa)</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ==================== SECTION 3 (OPSIONAL): AUDIO, GIMBAL & AKSESORIS ==================== --}}
    @if ($aksesorisList->isNotEmpty())
        <section class="ch-section" id="sewa-aksesoris">
            <div class="ch-section-head">
                <div>
                    <h2 class="ch-section-title">
                        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
                            <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                            <line x1="12" x2="12" y1="19" y2="22"/>
                        </svg>
                        <span>Audio, Gimbal &amp; Aksesoris</span>
                    </h2>
                    <p class="ch-section-sub">Perlengkapan pendukung produksi video &amp; pencahayaan profesional.</p>
                </div>
                <a href="{{ route('products.index') }}" class="ch-see-all">
                    <span>Lihat katalog</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="ch-grid">
                @foreach ($aksesorisList->take(4) as $item)
                    @php
                        $imgSrc = $resolveImage($item);
                        $price24 = $item->price_24h ?? $item->price ?? 0;
                    @endphp
                    <a href="{{ $resolveDetailUrl($item) }}" class="ch-card">
                        <div class="ch-card-media">
                            <span class="ch-badge">{{ $item->category ?? 'Aksesoris' }}</span>
                            @if ($imgSrc)
                                <img src="{{ $imgSrc }}" alt="{{ $item->name }}"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="ch-fallback-box" style="display: {{ $imgSrc ? 'none' : 'flex' }};">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect width="18" height="18" x="3" y="3" rx="2"/>
                                    <circle cx="9" cy="9" r="2"/>
                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                </svg>
                                <span>{{ $item->name }}</span>
                            </div>
                        </div>
                        <div class="ch-card-body">
                            <h3 class="ch-card-name">{{ $item->name }}</h3>
                            <p class="ch-card-price">
                                Rp {{ number_format($price24, 0, ',', '.') }} <span>/ 24 jam</span>
                            </p>
                            <div class="ch-card-meta">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" stroke-width="1.5" aria-hidden="true">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                                <span>{{ $item->rating ?? '5.0' }} (Siap Sewa)</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ==================== SECTION 4: BURSA JUAL PUTUS (MITRA C2C) ==================== --}}
    <section class="ch-section" id="jual-putus">
        <div class="ch-section-head">
            <div>
                <h2 class="ch-section-title">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                        <path d="M3 6h18"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    <span>Bursa Jual Putus (Mitra C2C)</span>
                </h2>
                <p class="ch-section-sub">Kamera &amp; lensa seken terawat dari mitra terverifikasi, bisa dibeli langsung dengan proteksi Rekber.</p>
            </div>
            <a href="{{ $ctaMitraUrl }}" class="ch-see-all" style="color: #16a34a;">
                <span>Jual kameramu di sini</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if ($jualPutusList->isEmpty())
            <div class="ch-empty-banner">
                <div>
                    <strong style="display: block; font-size: 1rem; color: #0f172a; margin-bottom: 0.25rem;">Belum ada unit Jual Putus (C2C) yang tayang</strong>
                    <p style="margin: 0; font-size: 0.86rem; color: #64748b;">Mitra yang mengunggah unit dengan skema <strong>Jual Putus</strong> atau <strong>Hybrid</strong> akan otomatis tampil di etalase ini.</p>
                </div>
                <a href="{{ $ctaMitraUrl }}" class="ch-btn-primary" style="background: #16a34a;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14"/><path d="M12 5v14"/>
                    </svg>
                    <span>Pasang Unit Jual Putus</span>
                </a>
            </div>
        @else
            <div class="ch-grid">
                @foreach ($jualPutusList->take(4) as $item)
                    @php
                        $imgSrc = $resolveImage($item);
                        $sellPrice = $item->sell_price ?? $item->price_sell ?? 0;
                    @endphp
                    <a href="{{ $resolveDetailUrl($item) }}" class="ch-card">
                        <div class="ch-card-media">
                            <span class="ch-badge ch-badge-green">Jual Putus</span>
                            @if ($imgSrc)
                                <img src="{{ $imgSrc }}" alt="{{ $item->name }}"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="ch-fallback-box" style="display: {{ $imgSrc ? 'none' : 'flex' }};">
                                <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                                    <circle cx="12" cy="13" r="3"/>
                                </svg>
                                <span>{{ $item->name }}</span>
                            </div>
                        </div>
                        <div class="ch-card-body">
                            <h3 class="ch-card-name">{{ $item->name }}</h3>
                            <p class="ch-card-price" style="color: #15803d;">
                                {{ $sellPrice > 0 ? 'Rp ' . number_format($sellPrice, 0, ',', '.') : 'Hubungi Mitra' }}
                                <span>(Beli Putus)</span>
                            </p>
                            <div class="ch-card-meta">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    <path d="m9 12 2 2 4-4"/>
                                </svg>
                                <span>{{ $item->condition ?? 'Terproteksi Rekber' }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ==================== SECTION 5: TITIP JUAL & KURASI TOKO (C2B CONSIGNMENT) ==================== --}}
    <section class="ch-section" id="titip-jual">
        <div class="ch-section-head">
            <div>
                <h2 class="ch-section-title">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/>
                        <path d="M7 7h.01"/>
                    </svg>
                    <span>Titip Jual &amp; Kurasi Toko (C2B Consignment)</span>
                </h2>
                <p class="ch-section-sub">Unit titipan mitra yang telah lulus inspeksi fisik, uji sensor, dan penaksiran harga oleh tim Admin CamHub.</p>
            </div>
        </div>

        <div class="ch-c2b-banner">
            <div style="max-width: 680px;">
                <strong style="display: block; font-size: 1.05rem; color: #0f172a; margin-bottom: 0.3rem;">
                    Punya kamera atau lensa nganggur? Titip jual atau Buyback langsung ke CamHub
                </strong>
                <p style="margin: 0; font-size: 0.87rem; color: #334155; line-height: 1.55;">
                    Ajukan lewat Dashboard Mitra dengan skema <strong>C2B Consignment</strong>. Tim kami bantu inspeksi unit, foto katalog profesional, penaksiran harga kompetitif, dan pencairan dana aman.
                </p>
            </div>
            <a href="{{ $ctaMitraUrl }}" class="ch-btn-primary" style="background: #0f172a;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" x2="12" y1="3" y2="15"/>
                </svg>
                <span>Ajukan Titip Jual (C2B)</span>
            </a>
        </div>

        @if ($titipJualList->isNotEmpty())
            <div class="ch-grid">
                @foreach ($titipJualList->take(4) as $item)
                    @php
                        $imgSrc = $resolveImage($item);
                        $sellPrice = $item->sell_price ?? $item->price_sell ?? 0;
                    @endphp
                    <a href="{{ $resolveDetailUrl($item) }}" class="ch-card">
                        <div class="ch-card-media">
                            <span class="ch-badge ch-badge-amber">Kurasi C2B</span>
                            @if ($imgSrc)
                                <img src="{{ $imgSrc }}" alt="{{ $item->name }}"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="ch-fallback-box" style="display: {{ $imgSrc ? 'none' : 'flex' }};">
                                <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                                    <circle cx="12" cy="13" r="3"/>
                                </svg>
                                <span>{{ $item->name }}</span>
                            </div>
                        </div>
                        <div class="ch-card-body">
                            <h3 class="ch-card-name">{{ $item->name }}</h3>
                            <p class="ch-card-price" style="color: #b45309;">
                                {{ $sellPrice > 0 ? 'Rp ' . number_format($sellPrice, 0, ',', '.') : 'Taksiran Toko' }}
                                <span>(Titip Jual)</span>
                            </p>
                            <div class="ch-card-meta">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <path d="m9 11 3 3L22 4"/>
                                </svg>
                                <span>{{ $item->status ?? 'Inspeksi CamHub' }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ==================== SECTION 6: JAMINAN KEAMANAN & REKBER ==================== --}}
    <section class="ch-section" id="keamanan-rekber" style="margin-bottom: 0;">
        <div class="ch-trust-grid">
            <div class="ch-trust-card">
                <div class="ch-shortcut-icon" style="margin-bottom: 0.75rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <strong style="display: block; font-size: 0.96rem; color: #0f172a; margin-bottom: 0.3rem;">Sewa Fleksibel Tanpa Ribet</strong>
                <p style="margin: 0; font-size: 0.84rem; color: #64748b; line-height: 1.55;">Tersedia paket 8 jam, 12 jam, 24 jam, hingga 2 hari. Penyewa bisa langsung pesan (Guest Rental) cukup dengan verifikasi KTP.</p>
            </div>

            <div class="ch-trust-card">
                <div class="ch-shortcut-icon" style="background: #f0fdf4; color: #16a34a; margin-bottom: 0.75rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
                <strong style="display: block; font-size: 0.96rem; color: #0f172a; margin-bottom: 0.3rem;">Rekening Bersama (Escrow)</strong>
                <p style="margin: 0; font-size: 0.84rem; color: #64748b; line-height: 1.55;">Dana pembayaran ditahan aman oleh platform dan baru dicairkan ke Mitra setelah unit diterima serta dikembalikan dengan baik.</p>
            </div>

            <div class="ch-trust-card">
                <div class="ch-shortcut-icon" style="background: #fffbeb; color: #d97706; margin-bottom: 0.75rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                        <circle cx="8" cy="12" r="2"/>
                        <path d="M14 10h4"/>
                        <path d="M14 14h4"/>
                    </svg>
                </div>
                <strong style="display: block; font-size: 0.96rem; color: #0f172a; margin-bottom: 0.3rem;">Verifikasi Identitas KYC</strong>
                <p style="margin: 0; font-size: 0.84rem; color: #64748b; line-height: 1.55;">Baik penyewa maupun mitra lapak wajib melewati pengecekan berkas KTP/KTM oleh Administrator demi keamanan transaksi.</p>
            </div>
        </div>
    </section>
</main>
@endsection