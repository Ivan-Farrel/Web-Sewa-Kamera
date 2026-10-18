@extends('layouts.camhub')

@section('title', 'Detail Produk | CamHub')

@section('content')
<div class="breadcrumb">Beranda / Katalog / Sony A7 III</div>

<section class="detail-layout">
    <div class="camera-preview">📷</div>

    <div class="detail-info">
        <span class="eyebrow">KAMERA MIRRORLESS</span>
        <h1>Sony A7 III</h1>

        <p class="muted">★ 4.8 · 120 ulasan</p>

        <div class="price">
            Rp 250.000 <small>/ hari</small>
        </div>

        <p class="available">● Kamera tersedia untuk disewa</p>

        <form action="{{ route('camhub.booking') }}" method="GET">
            <label>Durasi sewa</label>
            <select name="durasi">
                <option value="1">1 Hari</option>
                <option value="3">3 Hari</option>
                <option value="7">7 Hari</option>
                <option value="14">14 Hari</option>
            </select>

            <div class="form-row">
                <div>
                    <label>Tanggal mulai</label>
                    <input type="date" name="tanggal_mulai" required>
                </div>
                <div>
                    <label>Tanggal kembali</label>
                    <input type="date" name="tanggal_kembali" required>
                </div>
            </div>

            <button type="submit" class="btn btn-block">
                Booking Sekarang
            </button>
        </form>
    </div>
</section>

<section class="description">
    <h2>Deskripsi Produk</h2>
    <p class="muted">
        Sony A7 III merupakan kamera mirrorless full-frame
        yang cocok untuk fotografi dan videografi.
    </p>
</section>
@endsection