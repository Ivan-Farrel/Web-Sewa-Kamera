@extends('layouts.app')
@section('title', 'Dashboard Admin - CamHub')
@section('content')
<main class="page">
    <h1>Dashboard Admin</h1>
    <p>Ringkasan data platform CamHub.</p>
    <div class="stats-grid">
        <div class="stat-card"><p>Pengguna terdaftar</p><h2>{{ \App\Models\User::count() }}</h2></div>
        <div class="stat-card"><p>Booking masuk</p><h2>{{ \App\Models\Booking::count() }}</h2></div>
        <div class="stat-card"><p>Pengajuan KYC</p><h2>{{ \App\Models\KycSubmission::count() }}</h2></div>
    </div>
    <section class="panel"><h2>Kelola platform</h2><p>Pesanan pelanggan dapat dilihat melalui dashboard pelanggan. Katalog produk tersedia untuk semua pengunjung.</p><a class="btn" href="{{ route('products.index') }}">Buka katalog</a></section>
</main>
@endsection
