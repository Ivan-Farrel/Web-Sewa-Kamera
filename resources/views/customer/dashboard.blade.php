@extends('layouts.app')
@section('title', 'Dashboard Pembeli / Pelanggan - CamHub')
@section('content')
<main class="dashboard">
    <!-- Sidebar Pelanggan -->
    <aside class="sidebar">
        <div class="sidebar-brand">▣ CamHub</div>
        <div class="sidebar-user">
            <strong>{{ auth()->user()->name }}</strong>
            <p>Pembeli &amp; Pelanggan</p>
        </div>
        <nav>
            <a class="active" href="{{ route('camhub.dashboard-pelanggan') }}">⌂ &nbsp; Beranda Saya</a>
            <a href="#orders">📦 &nbsp; Pesanan Sewa Saya</a>
            <a href="{{ route('products.index') }}">📷 &nbsp; Katalog Sewa Kamera</a>
            <a href="{{ route('camhub.kyc') }}">🪪 &nbsp; Verifikasi Akun</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <section class="dashboard-main">
        <div style="margin-bottom: 1.5rem;">
            <h1>Halo, {{ auth()->user()->name }}! 👋</h1>
            <p style="color: #64748b; margin-top: 4px;">Selamat datang di dashboard akun CamHub kamu.</p>
        </div>

        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-bottom: 2rem;">
            <div class="stat-card">
                <p style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Pesanan Aktif</p>
                <h2 style="font-size: 1.75rem; font-weight: 800; color: #2563eb; margin-top: 4px;">{{ $bookings->where('status', '!=', 'Selesai')->count() }}</h2>
                <a href="#orders" style="color: #2563eb; font-weight: 600; font-size: 0.8rem; display: inline-block; margin-top: 6px;">Lihat Rincian →</a>
            </div>

            <div class="stat-card">
                <p style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Sewa Selesai</p>
                <h2 style="font-size: 1.75rem; font-weight: 800; color: #16a34a; margin-top: 4px;">{{ $bookings->where('status', 'like', '%Selesai%')->count() }}</h2>
                <span style="color: #64748b; font-size: 0.75rem; display: inline-block; margin-top: 6px;">Riwayat Transaksi</span>
            </div>

            <div class="stat-card">
                <p style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Status Identitas (KYC)</p>
                <h2 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-top: 8px;">
                    {{ auth()->user()->kycSubmissions()->exists() ? '✓ Terverifikasi' : 'Belum Ada' }}
                </h2>
                <a href="{{ route('camhub.kyc') }}" style="color: #2563eb; font-weight: 600; font-size: 0.8rem; display: inline-block; margin-top: 6px;">Kelola Profil →</a>
            </div>
        </div>

        <section class="panel" id="orders">
            <div class="section-heading" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <div>
                    <h2>Pesanan Sewa Saya</h2>
                    <p style="color: #64748b; font-size: 0.85rem; margin-top: 2px;">Daftar unit kamera yang sedang kamu sewa atau riwayat pemesanan sebelumnya.</p>
                </div>
                <a href="{{ route('products.index') }}" class="btn" style="font-size: 0.85rem; padding: 0.5rem 1rem;">Cari Kamera Lain</a>
            </div>

            @forelse ($bookings as $booking)
                <div class="list-row" style="display: flex; justify-content: space-between; align-items: center; padding: 14px; border: 1px solid #f1f5f9; border-radius: 8px; margin-bottom: 10px; background: #fafafa;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div class="mini-camera" style="font-size: 28px;">📷</div>
                        <div class="list-row-info">
                            <div>
                                <strong style="font-size: 1rem; color: #0f172a;">{{ $booking->product_name }}</strong>
                                @if ($booking->booking_code)
                                    <a href="{{ route('bookings.success', $booking->booking_code) }}" style="margin-left: 6px; font-size: 0.8rem; color: #2563eb; font-weight: 700; text-decoration: underline;">#{{ $booking->booking_code }}</a>
                                @endif
                            </div>
                            <p style="color: #64748b; font-size: 0.85rem; margin-top: 3px;">
                                {{ $booking->start_date->format('d M Y') }} – {{ $booking->end_date->format('d M Y') }} · {{ $booking->days }} hari sewa
                            </p>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <span class="badge" style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; background: {{ str_contains($booking->status, 'Selesai') ? '#dcfce7' : (str_contains($booking->status, 'Lunas') ? '#dbeafe' : '#fef3c7') }}; color: {{ str_contains($booking->status, 'Selesai') ? '#15803d' : (str_contains($booking->status, 'Lunas') ? '#1e40af' : '#92400e') }};">
                            {{ $booking->status }}
                        </span>
                        <p style="font-weight: 800; margin-top: 4px; font-size: 0.95rem; color: #0f172a;">Rp {{ number_format($booking->total, 0, ',', '.') }}</p>
                        @if ($booking->booking_code)
                            <a href="{{ route('bookings.success', $booking->booking_code) }}" style="font-size: 0.75rem; color: #2563eb; font-weight: 600; display: inline-block; margin-top: 2px;">Lihat Tiket &amp; VA →</a>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 2.5rem 1rem; color: #94a3b8;">
                    <p style="font-size: 1rem; margin-bottom: 8px;">Belum ada riwayat booking sewa kamera.</p>
                    <a href="{{ route('products.index') }}" class="btn" style="display: inline-block; margin-top: 6px;">Jelajahi Katalog Kamera</a>
                </div>
            @endforelse
        </section>
    </section>
</main>
@endsection
