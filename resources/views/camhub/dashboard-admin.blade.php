@extends('layouts.camhub')

@section('title', 'Dashboard Admin | CamHub')

@section('content')
<div class="dashboard">
    <aside class="sidebar">
        <h2>CamHub</h2>
        <p>Administrator</p>

        <a class="active" href="#">Dashboard</a>
        <a href="{{ route('camhub.kyc') }}">Verifikasi KYC</a>
        <a href="#">Jadwal Unit</a>
        <a href="#">Transaksi</a>
        <a href="#">Manajemen Dana</a>
        <a href="#">Laporan</a>
        <a href="#">Pengaturan</a>
    </aside>

    <section class="dashboard-content">
        <h1>Dashboard Admin</h1>
        <p class="muted">Ringkasan aktivitas CamHub.</p>

        <div class="stats-grid admin-stats">
            @foreach([
                ['Menunggu Verifikasi KYC', 8],
                ['Unit Inspeksi', 5],
                ['Transaksi Hari Ini', 12],
                ['Denda Pending', 3]
            ] as $stat)
                <div class="stat-card">
                    <span>{{ $stat[0] }}</span>
                    <strong>{{ $stat[1] }}</strong>
                </div>
            @endforeach
        </div>

        <h2>Transaksi 7 Hari Terakhir</h2>

        <div class="chart">
            @foreach([58, 72, 88, 58, 72, 88, 58] as $index => $height)
                <div class="bar-column">
                    <div class="bar" style="height: {{ $height }}%"></div>
                    <small>{{ ['Sen','Sel','Rab','Kam','Jum','Sab','Min'][$index] }}</small>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection