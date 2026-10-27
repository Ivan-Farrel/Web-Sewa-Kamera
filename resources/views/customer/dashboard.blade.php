@extends('layouts.app')
@section('title', 'Dashboard Pelanggan - CamHub')
@section('content')
<main class="dashboard">
    <aside class="sidebar"><div class="sidebar-brand">▣ CamHub</div><div class="sidebar-user"><strong>{{ auth()->user()->name }}</strong><p>Pelanggan</p></div><nav><a class="active" href="{{ route('camhub.dashboard-pelanggan') }}">⌂ &nbsp; Beranda</a><a href="{{ route('camhub.dashboard-pelanggan') }}">▤ &nbsp; Pesanan Saya</a><a href="{{ route('products.index') }}">▣ &nbsp; Sewa Kamera</a><a href="{{ route('products.index') }}">◇ &nbsp; Jual Beli Saya</a><a href="{{ route('products.index') }}">↗ &nbsp; Titip Jual Saya</a><a href="{{ route('camhub.kyc') }}">▤ &nbsp; Dokumen KYC</a><a href="{{ route('camhub.kyc') }}">⚙ &nbsp; Pengaturan</a></nav></aside>
    <section class="dashboard-main"><h1>Halo, {{ auth()->user()->name }}!</h1><p>Nikmati pengalaman terbaik di CamHub.</p>
    <div class="stats-grid"><div class="stat-card"><p>Pesanan aktif</p><h2>{{ $bookings->where('status', '!=', 'Selesai')->count() }}</h2><a href="#orders">Lihat detail →</a></div><div class="stat-card"><p>Sewa mendatang</p><h2>{{ $bookings->where('status', '!=', 'Selesai')->count() }}</h2><a href="#orders">Lihat detail →</a></div><div class="stat-card"><p>Status identitas</p><h2>{{ auth()->user()->kycSubmissions()->exists() ? 'Dikirim' : 'Belum ada' }}</h2><a href="{{ route('camhub.kyc') }}">Lengkapi KYC →</a></div></div>
    <section class="panel" id="orders"><div class="section-heading"><h2>Pesanan terbaru</h2><a href="{{ route('products.index') }}">Cari kamera</a></div>
        @forelse ($bookings as $booking)
            <div class="list-row"><div class="mini-camera">📷</div><div class="list-row-info"><strong>{{ $booking->product_name }}</strong><p>{{ $booking->start_date->format('d M Y') }} – {{ $booking->end_date->format('d M Y') }} · {{ $booking->days }} hari</p></div><div><span class="badge">{{ $booking->status }}</span><p>Rp {{ number_format($booking->total, 0, ',', '.') }}</p></div></div>
        @empty
            <p>Belum ada booking. <a href="{{ route('products.index') }}">Jelajahi kamera</a> untuk memulai.</p>
        @endforelse
    </section></section>
</main>
@endsection
