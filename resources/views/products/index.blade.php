@extends('layouts.app')
@section('title', 'Katalog Sewa & Jual Beli - CamHub')

@section('content')
@php
    use Illuminate\Support\Facades\Route;

    // Ambil semua produk agar filter Kategori, Tipe Transaksi, Harga, Search & Sort selalu akurat
    $rawProducts = \App\Models\Product::latest()->get();

    // Tangkap parameter filter dari URL (GET)
    $searchQuery = trim((string) request('q', ''));
    $selectedCategory = strtolower(trim((string) request('category', 'all')));
    if ($selectedCategory === '' || $selectedCategory === 'semua') {
        $selectedCategory = 'all';
    }

    // Normalisasi jika datang dari link home (?category=Lensa dll)
    $categoryAlias = [
        'kamera body' => 'kamera',
        'kamera dslr' => 'kamera',
        'kamera mirrorless' => 'kamera',
        'dslr' => 'kamera',
        'mirrorless' => 'kamera',
        'kamera' => 'kamera',
        'lensa' => 'lensa',
        'lens' => 'lensa',
        'audio' => 'audio',
        'audio & mic' => 'audio',
        'lighting' => 'lighting',
        'aksesoris' => 'aksesoris',
        'lighting & aksesoris' => 'aksesoris',
        'stabilizer / gimbal' => 'aksesoris',
    ];
    $selectedCategory = $categoryAlias[$selectedCategory] ?? $selectedCategory;

    $selectedTypes = array_values(array_filter((array) request('types', [])));
    $minPrice = request('min_price');
    $maxPrice = request('max_price');
    $sortBy = request('sort', 'latest');

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
        $name = strtolower(trim((string) ($p->name ?? '')));
        $desc = strtolower(trim((string) ($p->description ?? '')));
        $scheme = strtolower(trim((string) ($p->listing_type ?? '')));

        $sellPrice = (float) ($p->sell_price ?? $p->price_sell ?? 0);
        $rentPrice = (float) ($p->price_24h ?? $p->price ?? 0);

        // Deteksi Tipe Transaksi
        $isTitip = $scheme === 'titip_jual_c2b'
            || str_contains($desc, 'titip jual')
            || str_contains($desc, 'buyback (c2b)');

        $isJual = !$isTitip && (
            in_array($scheme, ['jual_putus', 'hybrid_c2c'])
            || $sellPrice > 0
            || str_contains($desc, 'jual putus')
        );

        $isSewa = !$isTitip && ($scheme === '' || in_array($scheme, ['sewa_c2c', 'hybrid_c2c']) || $rentPrice > 0);
        if ($scheme === 'jual_putus') {
            $isSewa = false;
        }

        // Deteksi Kategori Produk
        if (str_contains($cat, 'lensa') || str_contains($cat, 'lens')) {
            $group = 'lensa';
        } elseif (str_contains($cat, 'audio') || str_contains($cat, 'mic') || str_contains($name, 'mic') || str_contains($name, 'rode')) {
            $group = 'audio';
        } elseif (str_contains($cat, 'lighting') || str_contains($cat, 'lampu') || str_contains($cat, 'flash') || str_contains($name, 'godox')) {
            $group = 'lighting';
        } elseif (str_contains($cat, 'aksesoris') || str_contains($cat, 'gimbal') || str_contains($cat, 'stabilizer') || str_contains($cat, 'tripod')) {
            $group = 'aksesoris';
        } else {
            $group = 'kamera';
        }

        $displayPrice = $isSewa && $rentPrice > 0 ? $rentPrice : ($sellPrice > 0 ? $sellPrice : $rentPrice);

        return compact('group', 'isSewa', 'isJual', 'isTitip', 'rentPrice', 'sellPrice', 'displayPrice', 'scheme');
    };

    // Hitung jumlah per kategori (untuk info di sidebar)
    $catCounts = [
        'all' => $rawProducts->count(),
        'kamera' => 0,
        'lensa' => 0,
        'audio' => 0,
        'lighting' => 0,
        'aksesoris' => 0,
    ];
    foreach ($rawProducts as $rp) {
        $info = $classify($rp);
        if (isset($catCounts[$info['group']])) {
            $catCounts[$info['group']]++;
        }
    }

    // Terapkan seluruh filter
    $filteredProducts = $rawProducts->filter(function ($p) use ($classify, $searchQuery, $selectedCategory, $selectedTypes, $minPrice, $maxPrice) {
        $c = $classify($p);

        // 1. Filter pencarian kata kunci (?q=)
        if ($searchQuery !== '') {
            $haystack = strtolower(($p->name ?? '') . ' ' . ($p->category ?? '') . ' ' . ($p->description ?? ''));
            if (!str_contains($haystack, strtolower($searchQuery))) {
                return false;
            }
        }

        // 2. Filter kategori
        if ($selectedCategory !== 'all' && $c['group'] !== $selectedCategory) {
            return false;
        }

        // 3. Filter tipe transaksi (OR logic jika centang lebih dari satu)
        if (!empty($selectedTypes)) {
            $matchedType = false;
            if (in_array('sewa', $selectedTypes) && $c['isSewa']) {
                $matchedType = true;
            }
            if (in_array('jual', $selectedTypes) && $c['isJual']) {
                $matchedType = true;
            }
            if (in_array('titip', $selectedTypes) && $c['isTitip']) {
                $matchedType = true;
            }
            if (!$matchedType) {
                return false;
            }
        }

        // 4. Filter rentang harga
        $priceToCheck = $c['displayPrice'];
        if ($minPrice !== null && $minPrice !== '' && is_numeric($minPrice)) {
            if ($priceToCheck < (float) $minPrice) {
                return false;
            }
        }
        if ($maxPrice !== null && $maxPrice !== '' && is_numeric($maxPrice)) {
            if ($priceToCheck > (float) $maxPrice) {
                return false;
            }
        }

        return true;
    })->values();

    // Urutkan hasil
    if ($sortBy === 'price_asc') {
        $filteredProducts = $filteredProducts->sortBy(fn ($p) => $classify($p)['displayPrice'])->values();
    } elseif ($sortBy === 'price_desc') {
        $filteredProducts = $filteredProducts->sortByDesc(fn ($p) => $classify($p)['displayPrice'])->values();
    } elseif ($sortBy === 'name_asc') {
        $filteredProducts = $filteredProducts->sortBy(fn ($p) => strtolower((string) ($p->name ?? '')))->values();
    }

    // Judul dinamis sesuai kategori/pencarian aktif
    $categoryTitles = [
        'all' => 'Semua Koleksi Kamera & Peralatan',
        'kamera' => 'Kamera Body (DSLR & Mirrorless)',
        'lensa' => 'Lensa Pilihan (Fix, Zoom & Wide)',
        'audio' => 'Audio & Mikrofon',
        'lighting' => 'Lighting & Studio',
        'aksesoris' => 'Gimbal, Stabilizer & Aksesoris',
    ];
    $pageHeading = $searchQuery !== ''
        ? 'Hasil Pencarian: "' . $searchQuery . '"'
        : ($categoryTitles[$selectedCategory] ?? 'Katalog Produk');

    $hasActiveFilter = $searchQuery !== ''
        || $selectedCategory !== 'all'
        || !empty($selectedTypes)
        || ($minPrice !== null && $minPrice !== '')
        || ($maxPrice !== null && $maxPrice !== '');
@endphp

<style>
    .cat-wrap {
        max-width: 1220px;
        margin: 0 auto;
        padding: 1.75rem 1.25rem 4rem;
    }
    .cat-layout {
        display: grid;
        grid-template-columns: 260px minmax(0, 1fr);
        gap: 1.75rem;
        align-items: start;
    }
    @media (max-width: 900px) {
        .cat-layout {
            grid-template-columns: 1fr;
        }
    }
    .cat-sidebar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem;
        position: sticky;
        top: 1.25rem;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
    }
    @media (max-width: 900px) {
        .cat-sidebar {
            position: static;
        }
    }
    .cat-filter-group {
        padding-bottom: 1.1rem;
        margin-bottom: 1.1rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .cat-filter-group:last-of-type {
        border-bottom: none;
        padding-bottom: 0;
    }
    .cat-filter-title {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 0.75rem;
    }
    .cat-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 0.35rem 0.45rem;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.86rem;
        color: #334155;
        transition: background-color 0.15s ease;
    }
    .cat-option:hover {
        background: #f8fafc;
    }
    .cat-option-left {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .cat-option input {
        accent-color: #2563eb;
        width: 15px;
        height: 15px;
        cursor: pointer;
    }
    .cat-count {
        font-size: 0.74rem;
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        padding: 1px 7px;
        border-radius: 999px;
    }
    .cat-price-row {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 6px;
    }
    .cat-price-input {
        width: 100%;
        padding: 0.55rem 0.65rem;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.82rem;
        color: #0f172a;
        box-sizing: border-box;
        outline: none;
    }
    .cat-price-input:focus {
        border-color: #2563eb;
    }
    .cat-btn-apply {
        width: 100%;
        background: #2563eb;
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 0.72rem 1rem;
        font-weight: 700;
        font-size: 0.88rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .cat-btn-apply:hover {
        background: #1d4ed8;
    }
    .cat-btn-reset {
        width: 100%;
        margin-top: 0.55rem;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.6rem 1rem;
        font-weight: 600;
        font-size: 0.83rem;
        text-align: center;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        box-sizing: border-box;
    }
    .cat-btn-reset:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .cat-head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-end;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    .cat-eyebrow {
        display: inline-block;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #2563eb;
        margin-bottom: 0.2rem;
    }
    .cat-head h1 {
        font-size: 1.6rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.25;
    }
    .cat-sub {
        color: #64748b;
        font-size: 0.86rem;
        margin: 0.3rem 0 0;
    }
    .cat-sort-wrap {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #475569;
    }
    .cat-sort-select {
        padding: 0.52rem 0.8rem;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        background: #ffffff;
        color: #0f172a;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        outline: none;
    }
    .cat-chips {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.15rem;
    }
    .cat-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        padding: 0.28rem 0.7rem;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 600;
    }
    .cat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1.15rem;
    }
    @media (max-width: 1120px) {
        .cat-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 520px) {
        .cat-grid {
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
        height: 190px;
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
    .ch-badge-stack {
        position: absolute;
        top: 10px;
        left: 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    .ch-badge {
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 0.69rem;
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
    .ch-badge-purple {
        background: #f3e8ff;
        color: #7e22ce;
        border-color: #e9d5ff;
    }
    .ch-card-body {
        padding: 0.95rem 1rem 1.1rem;
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
    .ch-card-subprice {
        font-size: 0.78rem;
        font-weight: 600;
        color: #15803d;
        margin: 0;
    }
    .ch-card-meta {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.78rem;
        color: #64748b;
        margin-top: auto;
        padding-top: 0.35rem;
    }
    .cat-empty {
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        padding: 3rem 1.5rem;
        text-align: center;
    }
</style>

<main class="cat-wrap">
    <div class="cat-layout">
        {{-- ==================== SIDEBAR FILTER ==================== --}}
        <aside class="cat-sidebar">
            <form id="catalog-filter-form" method="GET" action="{{ route('products.index') }}">
                @if ($searchQuery !== '')
                    <input type="hidden" name="q" value="{{ $searchQuery }}">
                @endif
                <input type="hidden" name="sort" id="hidden-sort-input" value="{{ $sortBy }}">

                {{-- Filter Kategori --}}
                <div class="cat-filter-group">
                    <h2 class="cat-filter-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect width="7" height="7" x="3" y="3" rx="1"/>
                            <rect width="7" height="7" x="14" y="3" rx="1"/>
                            <rect width="7" height="7" x="14" y="14" rx="1"/>
                            <rect width="7" height="7" x="3" y="14" rx="1"/>
                        </svg>
                        <span>Kategori</span>
                    </h2>

                    @php
                        $categoryOptions = [
                            'all' => 'Semua',
                            'kamera' => 'Kamera Body',
                            'lensa' => 'Lensa',
                            'audio' => 'Audio',
                            'lighting' => 'Lighting',
                            'aksesoris' => 'Aksesoris',
                        ];
                    @endphp

                    @foreach ($categoryOptions as $catKey => $catLabel)
                        <label class="cat-option">
                            <span class="cat-option-left">
                                <input type="radio" name="category" value="{{ $catKey }}"
                                    {{ $selectedCategory === $catKey ? 'checked' : '' }}
                                    onchange="document.getElementById('catalog-filter-form').submit();">
                                <span>{{ $catLabel }}</span>
                            </span>
                            <span class="cat-count">{{ $catCounts[$catKey] ?? 0 }}</span>
                        </label>
                    @endforeach
                </div>

                {{-- Filter Tipe Transaksi --}}
                <div class="cat-filter-group">
                    <h2 class="cat-filter-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/>
                            <path d="M7 7h.01"/>
                        </svg>
                        <span>Tipe Transaksi</span>
                    </h2>

                    <label class="cat-option">
                        <span class="cat-option-left">
                            <input type="checkbox" name="types[]" value="sewa"
                                {{ in_array('sewa', $selectedTypes) ? 'checked' : '' }}>
                            <span>Sewa Harian (C2C)</span>
                        </span>
                    </label>
                    <label class="cat-option">
                        <span class="cat-option-left">
                            <input type="checkbox" name="types[]" value="jual"
                                {{ in_array('jual', $selectedTypes) ? 'checked' : '' }}>
                            <span>Jual Putus (C2C)</span>
                        </span>
                    </label>
                    <label class="cat-option">
                        <span class="cat-option-left">
                            <input type="checkbox" name="types[]" value="titip"
                                {{ in_array('titip', $selectedTypes) ? 'checked' : '' }}>
                            <span>Titip Jual (C2B)</span>
                        </span>
                    </label>
                </div>

                {{-- Filter Harga --}}
                <div class="cat-filter-group">
                    <h2 class="cat-filter-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                            <path d="M12 18V6"/>
                        </svg>
                        <span>Rentang Harga (Rp)</span>
                    </h2>

                    <div class="cat-price-row">
                        <input type="number" name="min_price" value="{{ $minPrice }}" min="0" placeholder="Min" class="cat-price-input">
                        <span style="color: #94a3b8;">-</span>
                        <input type="number" name="max_price" value="{{ $maxPrice }}" min="0" placeholder="Maks" class="cat-price-input">
                    </div>
                </div>

                <button type="submit" class="cat-btn-apply">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    <span>Terapkan Filter</span>
                </button>

                @if ($hasActiveFilter)
                    <a href="{{ route('products.index') }}" class="cat-btn-reset">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                        </svg>
                        <span>Reset Semua Filter</span>
                    </a>
                @endif
            </form>
        </aside>

        {{-- ==================== DAFTAR PRODUK ==================== --}}
        <section>
            <div class="cat-head">
                <div>
                    <span class="cat-eyebrow">Jelajahi Koleksi</span>
                    <h1>{{ $pageHeading }}</h1>
                    <p class="cat-sub">{{ $filteredProducts->count() }} produk tersedia</p>
                </div>

                <div class="cat-sort-wrap">
                    <label for="sort-select">Urutkan</label>
                    <select id="sort-select" class="cat-sort-select"
                        onchange="document.getElementById('hidden-sort-input').value = this.value; document.getElementById('catalog-filter-form').submit();">
                        <option value="latest" {{ $sortBy === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="price_asc" {{ $sortBy === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price_desc" {{ $sortBy === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="name_asc" {{ $sortBy === 'name_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                    </select>
                </div>
            </div>

            {{-- Active Filter Chips --}}
            @if ($hasActiveFilter)
                <div class="cat-chips">
                    @if ($selectedCategory !== 'all')
                        <span class="cat-chip">Kategori: {{ $categoryOptions[$selectedCategory] ?? ucfirst($selectedCategory) }}</span>
                    @endif
                    @foreach ($selectedTypes as $t)
                        <span class="cat-chip">
                            Tipe: {{ $t === 'sewa' ? 'Sewa Harian' : ($t === 'jual' ? 'Jual Putus' : 'Titip Jual C2B') }}
                        </span>
                    @endforeach
                    @if ($minPrice !== null && $minPrice !== '')
                        <span class="cat-chip">Min: Rp {{ number_format((float) $minPrice, 0, ',', '.') }}</span>
                    @endif
                    @if ($maxPrice !== null && $maxPrice !== '')
                        <span class="cat-chip">Maks: Rp {{ number_format((float) $maxPrice, 0, ',', '.') }}</span>
                    @endif
                </div>
            @endif

            @if ($filteredProducts->isEmpty())
                <div class="cat-empty">
                    <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.75rem;" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                    <h3 style="margin: 0 0 0.35rem; font-size: 1.05rem; color: #0f172a;">Produk belum ditemukan</h3>
                    <p style="margin: 0 0 1.15rem; font-size: 0.86rem; color: #64748b;">
                        Belum ada unit yang cocok dengan kombinasi kategori, tipe transaksi, atau rentang harga yang kamu pilih.
                    </p>
                    <a href="{{ route('products.index') }}" class="cat-btn-apply" style="width: auto; padding: 0.65rem 1.2rem; text-decoration: none;">
                        Tampilkan Semua Produk
                    </a>
                </div>
            @else
                <div class="cat-grid">
                    @foreach ($filteredProducts as $item)
                        @php
                            $imgSrc = $resolveImage($item);
                            $info = $classify($item);
                        @endphp
                        <a href="{{ $resolveDetailUrl($item) }}" class="ch-card">
                            <div class="ch-card-media">
                                <div class="ch-badge-stack">
                                    <span class="ch-badge">{{ $item->category ?? 'Unit' }}</span>
                                    @if ($info['isTitip'])
                                        <span class="ch-badge ch-badge-amber">Titip Jual C2B</span>
                                    @elseif ($info['isJual'] && $info['isSewa'])
                                        <span class="ch-badge ch-badge-purple">Sewa &amp; Jual</span>
                                    @elseif ($info['isJual'])
                                        <span class="ch-badge ch-badge-green">Jual Putus</span>
                                    @endif
                                </div>

                                @if ($imgSrc)
                                    <img src="{{ $imgSrc }}" alt="{{ $item->name }}"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                @endif
                                <div class="ch-fallback-box" style="display: {{ $imgSrc ? 'none' : 'flex' }};">
                                    @if ($info['group'] === 'lensa')
                                        <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10"/>
                                            <circle cx="12" cy="12" r="4"/>
                                            <line x1="21.17" x2="12" y1="8" y2="8"/>
                                            <line x1="3.95" x2="8.54" y1="6.06" y2="14"/>
                                            <line x1="10.88" x2="15.46" y1="21.94" y2="14"/>
                                        </svg>
                                    @else
                                        <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                                            <circle cx="12" cy="13" r="3"/>
                                        </svg>
                                    @endif
                                    <span>{{ $item->name }}</span>
                                </div>
                            </div>

                            <div class="ch-card-body">
                                <h3 class="ch-card-name">{{ $item->name }}</h3>

                                @if ($info['isTitip'])
                                    <p class="ch-card-price" style="color: #b45309;">
                                        {{ $info['sellPrice'] > 0 ? 'Rp ' . number_format($info['sellPrice'], 0, ',', '.') : 'Taksiran Toko' }}
                                        <span>(Titip Jual)</span>
                                    </p>
                                @elseif ($info['isJual'] && !$info['isSewa'])
                                    <p class="ch-card-price" style="color: #15803d;">
                                        Rp {{ number_format($info['sellPrice'], 0, ',', '.') }} <span>(Beli Putus)</span>
                                    </p>
                                @else
                                    <p class="ch-card-price">
                                        Rp {{ number_format($info['rentPrice'], 0, ',', '.') }} <span>/ 24 jam</span>
                                    </p>
                                    @if ($info['isJual'] && $info['sellPrice'] > 0)
                                        <p class="ch-card-subprice">
                                            Beli Putus: Rp {{ number_format($info['sellPrice'], 0, ',', '.') }}
                                        </p>
                                    @endif
                                @endif

                                <div class="ch-card-meta">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" stroke-width="1.5" aria-hidden="true">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                    <span>{{ $item->rating ?? '5.0' }} ({{ $info['isTitip'] ? 'Kurasi C2B' : ($info['isJual'] && !$info['isSewa'] ? 'Siap Beli' : 'Siap Sewa') }})</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</main>
@endsection