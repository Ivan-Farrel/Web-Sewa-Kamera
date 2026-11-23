@extends('layouts.app')
@section('title', 'Dashboard Mitra Lapak (C2C & C2B) - CamHub')

@section('content')
@php
    $vendorProducts = $products ?? collect();
    $vendorBookings = $bookings ?? collect();
    $escrowSaldo = $escrowBalance ?? $saldoTertahan ?? 0;
    $readySaldo = $availableBalance ?? $saldoSiapTarik ?? 0;
    $latestKyc = $kyc ?? $latestKyc ?? null;

    $rawKycStatus = strtolower(trim($latestKyc->status ?? ''));
    $kycApproved = in_array($rawKycStatus, ['disetujui', 'approved', 'verified']);
    $kycRejected = in_array($rawKycStatus, ['ditolak', 'rejected']);
    $kycPending = !empty($latestKyc) && !$kycApproved && !$kycRejected;

    $kycBadgeText = $kycApproved
        ? 'Terverifikasi'
        : ($kycRejected ? 'Ditolak (Upload Ulang)' : ($kycPending ? 'Menunggu Verifikasi' : 'Belum Unggah KYC'));

    $kycBadgeBg = $kycApproved ? '#dcfce7' : ($kycRejected ? '#fee2e2' : '#fef3c7');
    $kycBadgeColor = $kycApproved ? '#15803d' : ($kycRejected ? '#b91c1c' : '#92400e');

    $storeProductAction = \Illuminate\Support\Facades\Route::has('vendor.products.store')
        ? route('vendor.products.store')
        : (\Illuminate\Support\Facades\Route::has('vendor.product.store')
            ? route('vendor.product.store')
            : url('/vendor/products'));

    $withdrawAction = \Illuminate\Support\Facades\Route::has('vendor.withdraw')
        ? route('vendor.withdraw')
        : url('/vendor/withdraw');
@endphp

<style>
    .vendor-wrap {
        max-width: 1220px;
        margin: 0 auto;
        padding: 2rem 1.25rem 3.5rem;
    }
    .vendor-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .vendor-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.15rem;
        margin-bottom: 1.75rem;
    }
    .v-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.35rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    .vendor-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
        gap: 1.5rem;
        align-items: start;
    }
    @media (max-width: 960px) {
        .vendor-grid {
            grid-template-columns: 1fr;
        }
    }
    .icon-row {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .icon-row svg {
        flex-shrink: 0;
    }
    .v-label {
        display: block;
        font-size: 0.84rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.4rem;
    }
    .v-input {
        width: 100%;
        padding: 0.68rem 0.85rem;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.9rem;
        background: #ffffff;
        color: #0f172a;
        outline: none;
        box-sizing: border-box;
    }
    .v-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }
    .v-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }
    @media (max-width: 640px) {
        .v-form-grid {
            grid-template-columns: 1fr;
        }
    }
    .scheme-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1.05rem;
    }
    .scheme-box-title {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 0.86rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.9rem;
    }
    .c2b-notice {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        border-radius: 8px;
        padding: 0.75rem 0.9rem;
        font-size: 0.81rem;
        line-height: 1.5;
        margin-bottom: 0.95rem;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }
    .rules-card {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 14px;
        padding: 1.35rem;
    }
</style>

<main class="vendor-wrap">
    {{-- Header & Saldo Rekber --}}
    <div class="vendor-header">
        <div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: #0f172a; margin: 0;">Dashboard Mitra Lapak</h1>
            <p style="color: #64748b; font-size: 0.9rem; margin-top: 0.3rem;">
                Kelola unit sewa C2C, jual putus, pengajuan titip jual C2B, serta verifikasi &amp; pencairan Rekber.
            </p>
        </div>

        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem;">
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.6rem 1rem;">
                <span style="display: block; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #64748b;">Saldo Tertahan (Escrow)</span>
                <strong style="font-size: 1.05rem; color: #d97706;">Rp {{ number_format($escrowSaldo, 0, ',', '.') }}</strong>
            </div>
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.6rem 1rem;">
                <span style="display: block; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #64748b;">Saldo Siap Tarik</span>
                <strong style="font-size: 1.05rem; color: #16a34a;">Rp {{ number_format($readySaldo, 0, ',', '.') }}</strong>
            </div>
            <form action="{{ $withdrawAction }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="icon-row"
                    style="background: #16a34a; color: #ffffff; border: none; border-radius: 10px; padding: 0.78rem 1.05rem; font-size: 0.85rem; font-weight: 700; cursor: pointer;">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/>
                        <path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>
                    </svg>
                    <span>Tarik Saldo</span>
                </button>
            </form>
        </div>
    </div>

    {{-- 4 Kartu Ringkasan Atas --}}
    <div class="vendor-stats">
        <div class="v-card">
            <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b;">Etalase Unit</span>
            <h2 style="font-size: 1.65rem; font-weight: 800; color: #0f172a; margin: 0.35rem 0 0.25rem;">
                {{ $vendorProducts->count() }} Unit
            </h2>
            <span style="font-size: 0.8rem; color: #64748b;">Aktif di Katalog Publik</span>
        </div>

        <div class="v-card">
            <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b;">Pesanan Masuk</span>
            <h2 style="font-size: 1.65rem; font-weight: 800; color: #2563eb; margin: 0.35rem 0 0.25rem;">
                {{ $vendorBookings->count() }} Pesanan
            </h2>
            <span style="font-size: 0.8rem; color: #64748b;">Menunggu Konfirmasi / Berjalan</span>
        </div>

        <div class="v-card">
            <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b;">Sistem Rekening Bersama</span>
            <h2 class="icon-row" style="font-size: 1.45rem; font-weight: 800; color: #0f172a; margin: 0.4rem 0 0.25rem;">
                <span>Terlindungi</span>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
            </h2>
            <span style="font-size: 0.8rem; color: #64748b;">Dana ditahan aman hingga unit kembali</span>
        </div>

        <div class="v-card">
            <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b;">Status Verifikasi KYC</span>
            <div style="margin: 0.55rem 0 0.4rem;">
                <span style="display: inline-block; padding: 0.3rem 0.75rem; border-radius: 999px; font-size: 0.82rem; font-weight: 700; background: {{ $kycBadgeBg }}; color: {{ $kycBadgeColor }};">
                    {{ $kycBadgeText }}
                </span>
            </div>
            <span style="font-size: 0.8rem; color: #64748b;">Syarat pencairan Rekening Mitra</span>
        </div>
    </div>

    {{-- Grid Utama: Kiri (Form Upload) & Kanan (KYC + Aturan) --}}
    <div class="vendor-grid">
        {{-- KOLOM KIRI: Form Upload Kamera Dinamis --}}
        <section class="v-card">
            <h2 class="icon-row" style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0 0 0.35rem;">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                    <circle cx="12" cy="13" r="3"/>
                </svg>
                <span>Upload Kamera / Lensa ke Etalase</span>
            </h2>
            <p style="color: #64748b; font-size: 0.86rem; margin: 0 0 1.35rem;">
                Mendukung Dual-Listing: Sewa Harian (C2C), Jual Putus, Hybrid, maupun Pengajuan Titip Jual / Konsinyasi ke Toko (C2B).
            </p>

            <form action="{{ $storeProductAction }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.1rem;">
                @csrf

                {{-- Nama Unit --}}
                <div>
                    <label class="v-label" for="product-name">Nama Kamera / Lensa / Alat</label>
                    <input id="product-name" type="text" name="name" value="{{ old('name') }}" required
                        placeholder="Contoh: Sony A7 III Body Only / Lensa Canon EF 50mm f/1.8" class="v-input">
                </div>

                {{-- Kategori & Skema Listing --}}
                <div class="v-form-grid">
                    <div>
                        <label class="v-label" for="product-category">Kategori Produk</label>
                        <select id="product-category" name="category" required class="v-input">
                            @php $oldCat = old('category', 'Kamera DSLR'); @endphp
                            <option value="Kamera DSLR" {{ $oldCat === 'Kamera DSLR' ? 'selected' : '' }}>Kamera DSLR</option>
                            <option value="Kamera Mirrorless" {{ $oldCat === 'Kamera Mirrorless' ? 'selected' : '' }}>Kamera Mirrorless</option>
                            <option value="Lensa" {{ $oldCat === 'Lensa' ? 'selected' : '' }}>Lensa</option>
                            <option value="Audio & Mic" {{ $oldCat === 'Audio & Mic' ? 'selected' : '' }}>Audio &amp; Mic</option>
                            <option value="Stabilizer / Gimbal" {{ $oldCat === 'Stabilizer / Gimbal' ? 'selected' : '' }}>Stabilizer / Gimbal</option>
                            <option value="Lighting & Aksesoris" {{ $oldCat === 'Lighting & Aksesoris' ? 'selected' : '' }}>Lighting &amp; Aksesoris</option>
                        </select>
                    </div>

                    <div>
                        <label class="v-label" for="listing-scheme">Skema Listing (C2C / C2B)</label>
                        @php $oldScheme = old('listing_type', 'sewa_c2c'); @endphp
                        <select id="listing-scheme" name="listing_type" required class="v-input">
                            <option value="sewa_c2c" {{ $oldScheme === 'sewa_c2c' ? 'selected' : '' }}>
                                Sewa Harian (Mitra C2C)
                            </option>
                            <option value="jual_putus" {{ $oldScheme === 'jual_putus' ? 'selected' : '' }}>
                                Jual Putus (Mitra C2C)
                            </option>
                            <option value="hybrid_c2c" {{ $oldScheme === 'hybrid_c2c' ? 'selected' : '' }}>
                                Hybrid: Sewa &amp; Jual Putus (C2C)
                            </option>
                            <option value="titip_jual_c2b" {{ $oldScheme === 'titip_jual_c2b' ? 'selected' : '' }}>
                                Ajukan Titip Jual / Buyback ke Toko (C2B)
                            </option>
                        </select>
                    </div>
                </div>

                {{-- BLOK 1: FORM TARIF SEWA (Hanya muncul di Sewa Harian & Hybrid) --}}
                <div id="section-rental" class="scheme-box">
                    <div class="scheme-box-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span>Pengaturan Tarif Sewa Bertingkat</span>
                    </div>

                    <div class="v-form-grid">
                        <div>
                            <label class="v-label" for="price-8h">Tarif Sewa 8 Jam (Rp)</label>
                            <input id="price-8h" type="number" name="price_8h" value="{{ old('price_8h', 50000) }}" min="0" required class="v-input">
                        </div>
                        <div>
                            <label class="v-label" for="price-12h">Tarif Sewa 12 Jam (Rp)</label>
                            <input id="price-12h" type="number" name="price_12h" value="{{ old('price_12h', 75000) }}" min="0" required class="v-input">
                        </div>
                        <div>
                            <label class="v-label" for="price-24h">Tarif Sewa 24 Jam / Harian (Rp)</label>
                            <input id="price-24h" type="number" name="price_24h" value="{{ old('price_24h', 110000) }}" min="0" required class="v-input">
                        </div>
                        <div>
                            <label class="v-label" for="price-2d">Tarif Paket 2 Hari (Rp)</label>
                            <input id="price-2d" type="number" name="price_2d" value="{{ old('price_2d', 200000) }}" min="0" required class="v-input">
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <label class="v-label" for="overtime-hour">Denda Overtime per Jam (Rp)</label>
                            <input id="overtime-hour" type="number" name="overtime_per_hour" value="{{ old('overtime_per_hour', 15000) }}" min="0" required class="v-input">
                        </div>
                    </div>
                </div>

                {{-- BLOK 2: FORM JUAL PUTUS / TITIP JUAL C2B (Muncul di Jual Putus, Hybrid, & C2B) --}}
                <div id="section-sale" class="scheme-box" style="display: none;">
                    <div class="scheme-box-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/>
                            <path d="M7 7h.01"/>
                        </svg>
                        <span id="sale-box-heading">Detail Harga Jual Putus &amp; Kondisi Unit</span>
                    </div>

                    {{-- Info khusus C2B Consignment --}}
                    <div id="c2b-info-box" class="c2b-notice" style="display: none;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:2px;" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4"/>
                            <path d="M12 8h.01"/>
                        </svg>
                        <span>
                            <strong>Skema Titip Jual / Buyback (C2B):</strong> Unit akan melalui tahap inspeksi fisik, uji sensor/lensa, dan penaksiran harga resmi oleh Admin CamHub sebelum tayang di etalase toko atau dibeli langsung.
                        </span>
                    </div>

                    <div class="v-form-grid">
                        <div>
                            <label class="v-label" for="sell-price-input" id="sale-price-label">Harga Jual Putus (Rp)</label>
                            <input id="sell-price-input" type="number" name="sell_price" value="{{ old('sell_price') }}" min="0"
                                placeholder="Contoh: 14500000" class="v-input">
                        </div>

                        <div>
                            <label class="v-label" for="condition-input">Kondisi Fisik &amp; Fungsi</label>
                            @php $oldCond = old('condition', 'Like New (95-99%)'); @endphp
                            <select id="condition-input" name="condition" class="v-input">
                                <option value="Like New (95-99%)" {{ $oldCond === 'Like New (95-99%)' ? 'selected' : '' }}>Like New (95-99%)</option>
                                <option value="Mulus Normal (85-94%)" {{ $oldCond === 'Mulus Normal (85-94%)' ? 'selected' : '' }}>Mulus Normal (85-94%)</option>
                                <option value="Pemakaian Wajar (75-84%)" {{ $oldCond === 'Pemakaian Wajar (75-84%)' ? 'selected' : '' }}>Pemakaian Wajar (75-84%)</option>
                                <option value="Ada Minus Minor" {{ $oldCond === 'Ada Minus Minor' ? 'selected' : '' }}>Ada Minus Minor (Jelaskan di catatan)</option>
                            </select>
                        </div>

                        {{-- Metode Penyerahan Khusus C2B --}}
                        <div id="c2b-handover-wrap" style="grid-column: 1 / -1; display: none;">
                            <label class="v-label" for="handover-input">Metode Penyerahan Unit untuk Inspeksi Toko</label>
                            @php $oldHandover = old('handover_method', 'Antar ke Toko CamHub'); @endphp
                            <select id="handover-input" name="handover_method" class="v-input">
                                <option value="Antar ke Toko CamHub" {{ $oldHandover === 'Antar ke Toko CamHub' ? 'selected' : '' }}>Antar Langsung ke Toko CamHub</option>
                                <option value="Penjemputan Kurir CamHub" {{ $oldHandover === 'Penjemputan Kurir CamHub' ? 'selected' : '' }}>Minta Penjemputan Kurir CamHub</option>
                            </select>
                        </div>

                        <div>
                            <label class="v-label" for="sc-input">Shutter Count (Opsional)</label>
                            <input id="sc-input" type="text" name="shutter_count" value="{{ old('shutter_count') }}"
                                placeholder="Contoh: 12.500 SC (kosongi jika lensa)" class="v-input">
                        </div>

                        <div>
                            <label class="v-label" for="completeness-input">Kelengkapan Unit</label>
                            <input id="completeness-input" type="text" name="completeness" value="{{ old('completeness') }}"
                                placeholder="Contoh: Fullset Box, Baterai Ori, Charger, Strap" class="v-input">
                        </div>

                        <div style="grid-column: 1 / -1;">
                            <label class="v-label" for="notes-input">Catatan Kondisi / Sensor / Garansi</label>
                            <textarea id="notes-input" name="inspection_notes" rows="2" class="v-input" style="resize: vertical;"
                                placeholder="Jelaskan riwayat pemakaian, kebersihan sensor/optik (bebas jamur/fog), atau minus pemakaian...">{{ old('inspection_notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Foto Produk (Selalu Muncul di Semua Skema) --}}
                <div>
                    <label class="v-label" for="product-image">Foto Produk (JPG/PNG/WEBP, maks 5 MB)</label>
                    <input id="product-image" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="v-input" style="padding: 0.5rem;">
                </div>

                {{-- Tombol Submit Dinamis --}}
                <button type="submit" id="btn-submit-product" class="icon-row"
                    style="width: 100%; justify-content: center; background: #2563eb; color: #ffffff; padding: 0.85rem 1rem; border: none; border-radius: 10px; font-weight: 700; font-size: 0.95rem; cursor: pointer;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14"/>
                        <path d="M12 5v14"/>
                    </svg>
                    <span id="btn-submit-label">Tayangkan Produk ke Katalog CamHub</span>
                </button>
            </form>
        </section>

        {{-- KOLOM KANAN: KYC & Aturan Rekber --}}
        <aside style="display: flex; flex-direction: column; gap: 1.25rem;">
            {{-- Card Verifikasi KYC Mitra --}}
            <section class="v-card">
                <h3 class="icon-row" style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0 0 0.4rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                        <circle cx="8" cy="12" r="2"/>
                        <path d="M14 10h4"/>
                        <path d="M14 14h4"/>
                    </svg>
                    <span>Verifikasi KYC Mitra</span>
                </h3>
                <p style="color: #64748b; font-size: 0.84rem; line-height: 1.5; margin: 0 0 1rem;">
                    Unggah foto KTP/KTM untuk mengaktifkan pencairan dana Rekening Bersama (Escrow).
                </p>

                @if ($kycApproved)
                    <div class="icon-row" style="width: 100%; box-sizing: border-box; background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; border-radius: 8px; padding: 0.75rem 0.9rem; font-size: 0.84rem; font-weight: 600;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <path d="m9 11 3 3L22 4"/>
                        </svg>
                        <span>Identitas KYC kamu sudah terverifikasi oleh Admin.</span>
                    </div>
                @else
                    <form action="{{ url('/vendor/kyc') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 0.85rem;">
                        @csrf
                        <input type="file" name="ktp_image" accept="image/jpeg,image/png,image/webp" required class="v-input" style="padding: 0.5rem;">
                        <button type="submit" class="icon-row"
                            style="width: 100%; justify-content: center; background: #0f172a; color: #ffffff; padding: 0.75rem 1rem; border: none; border-radius: 8px; font-weight: 700; font-size: 0.9rem; cursor: pointer;">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" x2="12" y1="3" y2="15"/>
                            </svg>
                            <span>Upload KTP / KTM</span>
                        </button>
                    </form>
                @endif
            </section>

            {{-- Card Aturan Denda & Rekber --}}
            <section class="rules-card">
                <h3 class="icon-row" style="font-size: 1rem; font-weight: 800; color: #1e3a8a; margin: 0 0 0.75rem;">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                        <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                        <path d="M7 21h10"/>
                        <path d="M12 3v18"/>
                        <path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/>
                    </svg>
                    <span>Aturan Denda &amp; Rekber CamHub</span>
                </h3>
                <ul style="margin: 0; padding-left: 1.15rem; color: #1e40af; font-size: 0.84rem; line-height: 1.65; display: flex; flex-direction: column; gap: 0.45rem;">
                    <li>Dana penyewa ditahan di <strong>Rekening Bersama (Escrow)</strong>.</li>
                    <li>Keterlambatan pengembalian dikenakan <strong>Denda Otomatis 150% &times; Tarif Harian &times; Hari Telat</strong>.</li>
                    <li>Dana cair setelah Mitra klik inspeksi fisik pengembalian selesai.</li>
                </ul>
            </section>
        </aside>
    </div>

    {{-- TABEL BAWAH 1: Daftar Etalase Produk Mitra --}}
    <section class="v-card" style="margin-top: 1.75rem;">
        <h2 class="icon-row" style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0 0 0.25rem;">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m7.5 4.27 9 5.15"/>
                <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                <path d="m3.3 7 8.7 5 8.7-5"/>
                <path d="M12 22V12"/>
            </svg>
            <span>Etalase Kamera &amp; Pengajuan Saya ({{ $vendorProducts->count() }} Unit)</span>
        </h2>
        <p style="color: #64748b; font-size: 0.84rem; margin: 0 0 1rem;">Daftar unit yang kamu tayangkan untuk sewa, jual putus, atau diajukan konsinyasi C2B.</p>

        @if ($vendorProducts->isEmpty())
            <p style="text-align: center; color: #94a3b8; padding: 1.5rem 0; margin: 0;">Belum ada unit di etalase kamu. Upload kamera pertamamu lewat form di atas.</p>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; color: #475569;">
                            <th style="padding: 10px 8px;">Unit</th>
                            <th style="padding: 10px 8px;">Kategori</th>
                            <th style="padding: 10px 8px;">Skema</th>
                            <th style="padding: 10px 8px;">Tarif Sewa (24J)</th>
                            <th style="padding: 10px 8px;">Harga Jual / Taksiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vendorProducts as $item)
                            @php
                                $schemeVal =$item->listing_type ?? 'sewa_c2c';
                                $schemeLabels = [
                                    'sewa_c2c'       => ['Sewa Harian (C2C)', '#dbeafe', '#1e40af'],
                                    'jual_putus'     => ['Jual Putus (C2C)', '#dcfce7', '#15803d'],
                                    'hybrid_c2c'     => ['Hybrid (Sewa & Jual)', '#f3e8ff', '#7e22ce'],
                                    'titip_jual_c2b' => ['Titip Jual / Buyback (C2B)', '#fef3c7', '#92400e'],
                                ];
                                $badge = $schemeLabels[$schemeVal] ?? ['Sewa Harian (C2C)', '#dbeafe', '#1e40af'];
                                $sellVal = $item->sell_price ?? $item->price_sell ?? 0;
                            @endphp
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 10px 8px; font-weight: 600; color: #0f172a;">{{ $item->name }}</td>
                                <td style="padding: 10px 8px; color: #475569;">{{ $item->category ?? '-' }}</td>
                                <td style="padding: 10px 8px;">
                                    <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; background: {{ $badge[1] }}; color: {{$badge[2] }};">
                                        {{ $badge[0] }}
                                    </span>
                                </td>
                                <td style="padding: 10px 8px; font-weight: 600; color: #2563eb;">
                                    {{ ($item->price_24h ?? 0) > 0 ? 'Rp ' . number_format($item->price_24h, 0, ',', '.') : '-' }}
                                </td>
                                <td style="padding: 10px 8px; font-weight: 600; color: #15803d;">
                                    {{ $sellVal > 0 ? 'Rp ' . number_format($sellVal, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- TABEL BAWAH 2: Pesanan Sewa Masuk --}}
    <section class="v-card" style="margin-top: 1.5rem;">
        <h2 class="icon-row" style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0 0 0.25rem;">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/>
                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                <path d="m9 14 2 2 4-4"/>
            </svg>
            <span>Pesanan Sewa Masuk</span>
        </h2>
        <p style="color: #64748b; font-size: 0.84rem; margin: 0 0 1rem;">Konfirmasi pesanan dan inspeksi pengembalian unit agar dana Rekber cair ke saldo siap tarik.</p>

        @if ($vendorBookings->isEmpty())
            <p style="text-align: center; color: #94a3b8; padding: 1.5rem 0; margin: 0;">Belum ada pesanan sewa yang masuk untuk unit lapak kamu.</p>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; color: #475569;">
                            <th style="padding: 10px 8px;">Kode</th>
                            <th style="padding: 10px 8px;">Penyewa</th>
                            <th style="padding: 10px 8px;">Unit</th>
                            <th style="padding: 10px 8px;">Total</th>
                            <th style="padding: 10px 8px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vendorBookings as $b)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 10px 8px; font-weight: 700; color: #2563eb;">#{{ $b->booking_code ?? $b->id }}</td>
                                <td style="padding: 10px 8px;">{{ $b->customer_name ?? $b->guest_name ?? 'Penyewa' }}</td>
                                <td style="padding: 10px 8px;">{{ $b->product_name ?? ($b->product->name ?? '-') }}</td>
                                <td style="padding: 10px 8px; font-weight: 700;">Rp {{ number_format($b->total ?? $b->total_price ?? 0, 0, ',', '.') }}</td>
                                <td style="padding: 10px 8px;">
                                    <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; background: #fef3c7; color: #92400e;">
                                        {{ $b->status ?? 'Menunggu konfirmasi' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const schemeSelect = document.getElementById('listing-scheme');
    const rentalSection = document.getElementById('section-rental');
    const saleSection = document.getElementById('section-sale');
    const c2bInfoBox = document.getElementById('c2b-info-box');
    const c2bHandoverWrap = document.getElementById('c2b-handover-wrap');
    const handoverInput = document.getElementById('handover-input');
    const saleHeading = document.getElementById('sale-box-heading');
    const salePriceLabel = document.getElementById('sale-price-label');
    const submitLabel = document.getElementById('btn-submit-label');
    const submitBtn = document.getElementById('btn-submit-product');

    const rentalInputs = rentalSection.querySelectorAll('input, select, textarea');
    const allSaleInputs = saleSection.querySelectorAll('input, select, textarea');
    const saleRequiredInputs = [
        document.getElementById('sell-price-input'),
        document.getElementById('condition-input'),
        document.getElementById('completeness-input')
    ];

    function toggleSchemeFields() {
        const val = schemeSelect.value;
        const showRental = (val === 'sewa_c2c' || val === 'hybrid_c2c');
        const showSale = (val === 'jual_putus' || val === 'hybrid_c2c' || val === 'titip_jual_c2b');
        const isC2B = (val === 'titip_jual_c2b');

        // Tampilkan / sembunyikan blok Tarif Sewa
        rentalSection.style.display = showRental ? 'block' : 'none';
        rentalInputs.forEach(el => {
            el.disabled = !showRental;
            el.required = showRental;
        });

        // Tampilkan / sembunyikan blok Jual Putus / C2B
        saleSection.style.display = showSale ? 'block' : 'none';
        allSaleInputs.forEach(el => {
            el.disabled = !showSale;
        });
        saleRequiredInputs.forEach(el => {
            if (el) el.required = showSale;
        });

        // Info & metode serah terima khusus C2B
        c2bInfoBox.style.display = isC2B ? 'flex' : 'none';
        c2bHandoverWrap.style.display = isC2B ? 'block' : 'none';
        if (handoverInput) {
            handoverInput.disabled = !isC2B;
            handoverInput.required = isC2B;
        }

        // Ubah teks label & warna tombol sesuai skema
        if (isC2B) {
            saleHeading.textContent = 'Detail Pengajuan Titip Jual / Buyback ke Toko (C2B)';
            salePriceLabel.textContent = 'Harga Ekspektasi / Taksiran Awal (Rp)';
            submitLabel.textContent = 'Ajukan Titip Jual / Buyback ke Admin CamHub';
            submitBtn.style.background = '#0f172a';
        } else if (val === 'jual_putus') {
            saleHeading.textContent = 'Detail Harga Jual Putus & Kondisi Unit';
            salePriceLabel.textContent = 'Harga Jual Putus (Rp)';
            submitLabel.textContent = 'Tayangkan Unit Jual Putus ke Katalog';
            submitBtn.style.background = '#16a34a';
        } else if (val === 'hybrid_c2c') {
            saleHeading.textContent = 'Tambahan Opsi Harga Jual Putus (Hybrid)';
            salePriceLabel.textContent = 'Harga Jual Putus (Rp)';
            submitLabel.textContent = 'Tayangkan Unit Sewa & Jual ke Katalog';
            submitBtn.style.background = '#2563eb';
        } else {
            submitLabel.textContent = 'Tayangkan Produk ke Katalog CamHub';
            submitBtn.style.background = '#2563eb';
        }
    }

    schemeSelect.addEventListener('change', toggleSchemeFields);
    toggleSchemeFields();
});
</script>
@endsection