
@extends('layouts.app')

@section('title', 'Booking & Checkout - CamHub')

@section('content')
<div class="page">
    <h1>Booking &amp; Checkout</h1>
    <p>Selesaikan informasi penyewaan kamera kamu.</p>

    <div class="steps">
        <span class="active">1. Data Sewa</span>
        <span>2. Pembayaran</span>
        <span>3. Konfirmasi</span>
    </div>

    <div class="checkout-grid">
        <section class="panel">
            <h3>Informasi Penyewa</h3>

            <form id="bookingForm" method="POST" action="{{ route('bookings.store') }}">
                @csrf
                <label for="bookingStartDate">Tanggal mulai sewa</label>
                <input type="date" name="tanggal_mulai" value="{{ $start ?? '' }}" id="bookingStartDate" min="{{ now()->toDateString() }}" required>
                <input type="hidden" name="durasi" value="{{ $days }}" id="bookingDuration">
                @if ($errors->any()) <div class="notice" role="alert">{{ $errors->first() }}</div> @endif
                <label>Nama Lengkap</label>
                <input type="text" name="nama" value="{{ old('nama', auth()->user()->name) }}" required>

                <label>No. HP</label>
                <input type="tel" name="telepon" placeholder="081234567890" required>

                <label>Alamat Pengiriman / Ambil di Lokasi</label>

                <label class="radio-row">
                    <input type="radio" name="pengambilan" value="pickup" checked>
                    Ambil di Lokasi (Pickup)
                </label>

                <input type="text" name="alamat" placeholder="Jl. Merdeka No. 12, Kediri" required>

                <label class="radio-row">
                    <input type="radio" name="pengambilan" value="kirim">
                    Kirim ke Alamat
                </label>

                <label>Metode Pembayaran</label>

                <label class="radio-row">
                    <input type="radio" name="pembayaran" value="escrow" checked>
                    Rekening Bersama (Escrow)
                </label>

                <p class="muted">Aman, dana ditahan hingga transaksi selesai.</p>

                <label class="radio-row">
                    <input type="radio" name="pembayaran" value="transfer">
                    Transfer Bank
                </label>

                <label class="radio-row">
                    <input type="radio" name="pembayaran" value="ewallet">
                    E-Wallet (OVO, GoPay, Dana, ShopeePay)
                </label>

                <button type="submit" class="btn" style="width:100%">
                    Lanjutkan Pembayaran
                </button>
            </form>
        </section>

        <aside class="panel">
            <h3>Ringkasan Pesanan</h3>

            <div class="list-row">
                <div class="mini-camera">📷</div>
                <div class="list-row-info">
                    <strong>Sony A7 III</strong>
                    <p>Rp 250.000 × <span id="bookingDays">3 hari</span></p>
                </div>
            </div>

            <div class="summary-row">
                <span>Subtotal</span>
                <strong id="bookingSubtotal">Rp 750.000</strong>
            </div>

            <div class="summary-row">
                <span>Biaya Admin</span>
                <strong>Rp 5.000</strong>
            </div>

            <hr>

            <div class="summary-row">
                <strong>Total</strong>
                <strong class="price" id="bookingTotal">Rp 755.000</strong>
            </div>
        </aside>
    </div>
</div>
@endsection
