
@extends('layouts.camhub')

@section('title', 'CamHub — Dashboard Mitra Lapak')

@section('content')
<div class="dashboard-layout">

    <aside class="dashboard-sidebar">
        <a href="{{ route('camhub.home') }}" class="dashboard-brand">
            CamHub
        </a>

        <p class="sidebar-caption">Yogi Camera</p>

        <nav class="dashboard-menu">
            <a class="active" href="#">▣ Beranda</a>
            <a href="#">▣ Kelola Produk</a>
            <a href="#">▣ Jadwal Sewa</a>
            <a href="#">▣ Transaksi</a>
            <a href="#">▣ Saldo & Pencairan</a>
            <a href="#">⚙ Pengaturan</a>
        </nav>
    </aside>

    <section class="dashboard-main">
        <div class="dashboard-heading">
            <h1>Dashboard Mitra Lapak</h1>
            <p class="muted">Pantau produk dan transaksi lapak kamu.</p>
        </div>

        <div class="stats-grid">
            <article class="stat-card">
                <span>Total Produk</span>
                <strong>12</strong>
            </article>

            <article class="stat-card">
                <span>Pesanan Aktif</span>
                <strong>5</strong>
            </article>

            <article class="stat-card">
                <span>Saldo Tersedia</span>
                <strong>Rp 2.450.000</strong>
            </article>
        </div>

        <div class="products-heading">
            <h2>Produk Saya</h2>
            <a href="#" class="btn btn-small">+ Tambah Produk</a>
        </div>

        <div class="merchant-products">
            @foreach([
                ['Sony A7 III', 'Sewa & Jual', 'Rp 250.000/hari'],
                ['Canon EOS R50', 'Sewa & Jual', 'Rp 180.000/hari'],
                ['Fujifilm X-T30', 'Sewa & Jual', 'Rp 200.000/hari']
            ] as $product)
                <article class="merchant-product">
                    <div class="merchant-image">📷</div>

                    <div class="merchant-info">
                        <h3>{{ $product[0] }}</h3>
                        <p class="muted">
                            {{ $product[1] }} · {{ $product[2] }}
                        </p>
                    </div>

                    <span class="available-status">Tersedia</span>
                </article>
            @endforeach
        </div>
    </section>

</div>
@endsection
