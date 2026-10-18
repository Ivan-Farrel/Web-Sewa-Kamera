@extends('layouts.camhub')

@section('title', 'Dashboard Pelanggan | CamHub')

@section('content')
<div class="dashboard">
    <aside class="sidebar">
        <h2>CamHub</h2>
        <p>Pelanggan</p>

        <a class="active" href="{{ route('camhub.dashboard-pelanggan') }}">Beranda</a>
        <a href="#">Pesanan Saya</a>
        <a href="#">Sewa Saya</a>
        <a href="#">Jual Beli</a>
        <a href="#">Dokumen KYC</a>
        <a href="#">Pengaturan</a>
    </aside>

    <section class="dashboard-content">
        <h1>Halo, {{ auth()->user()->name }}!</h1>
        <p class="muted">Nikmati pengalaman terbaik di CamHub.</p>

        <div class="stats-grid">
            <div class="stat-card">
                <span>Pesanan Aktif</span>
                <strong>2</strong>
                <a href="#">Lihat detail →</a>
            </div>

            <div class="stat-card">
                <span>Sewa Mendatang</span>
                <strong>1</strong>
                <a href="#">Lihat detail →</a>
            </div>

            <div class="stat-card">
                <span>Saldo Escrow</span>
                <strong>Rp 1.250.000</strong>
                <a href="#">Lihat detail →</a>
            </div>
        </div>

        <h2>Pesanan Terbaru</h2>

        @foreach([
            ['Sony A7 III', 'Sewa 3 hari', 'Dalam proses'],
            ['Canon EOS R50', 'Beli produk', 'Dikirim'],
            ['Fujifilm X-T30', 'Sewa 7 hari', 'Selesai']
        ] as $pesanan)
            <div class="order-item">
                <span class="camera-thumb">📷</span>
                <div>
                    <strong>{{ $pesanan[0] }}</strong>
                    <small>{{ $pesanan[1] }}</small>
                </div>
                <span class="status">{{ $pesanan[2] }}</span>
            </div>
        @endforeach
    </section>
</div>
@endsection
