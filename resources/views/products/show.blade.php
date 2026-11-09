@extends('layouts.app')
@section('title', 'Detail ' . $product->name . ' - CamHub')
@section('content')
<main class="page detail-page">
    <p class="muted">Beranda / {{ $product->category }} / {{ $product->name }}</p>

    <section class="product-detail">
        <div class="detail-image camera-image" style="{{ $product->image ? "background-image: url('" . asset('images/' . $product->image) . "'); background-size: cover; background-position: center; border-radius: 12px;" : "" }}"></div>

        <div class="detail-info">
            <p class="eyebrow">{{ strtoupper($product->category) }}</p>
            <h1>{{ $product->name }}</h1>
            <p class="stars">★ 4.8 (120 ulasan) · Terjual 50+</p>

            <h2 class="price" id="displayedPrice">Rp {{ number_format($product->price_24h, 0, ',', '.') }}<small id="displayedDuration">/ 24 jam</small></h2>

            <div style="margin: 6px 0 14px; font-size: 0.9rem; color: #475569;">
                <span>Overtime: <strong style="color: #e11d48;">Rp {{ number_format($product->overtime_per_hour, 0, ',', '.') }}</strong> / jam</span>
            </div>

            <div style="margin-bottom: 16px;">
                <span class="badge">Sewa</span> 
                <span class="badge badge-green">{{ ucfirst($product->status ?? 'Tersedia') }}</span>
                <span class="badge" style="background:#e0f2fe; color:#0369a1; border-color:#bae6fd;">⚡ Sewa Tanpa Login</span>
            </div>

            <h4>Durasi sewa</h4>
            <div class="duration-options">
                <button type="button" data-duration="8h" data-price="{{ $product->price_8h }}" data-add-days="0" data-label="8 Jam">8 Jam</button>
                <button type="button" data-duration="12h" data-price="{{ $product->price_12h }}" data-add-days="0" data-label="12 Jam">12 Jam</button>
                <button type="button" class="active" data-duration="24h" data-price="{{ $product->price_24h }}" data-add-days="1" data-label="24 Jam">24 Jam</button>
                <button type="button" data-duration="2d" data-price="{{ $product->price_2d }}" data-add-days="2" data-label="2 Hari">2 Hari</button>
            </div>

            <div class="form-row" style="margin-top: 16px;">
                <div>
                    <label>Tanggal mulai</label>
                    <input type="date" id="startDate" min="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}">
                </div>
                <div>
                    <label>Tanggal selesai</label>
                    <input type="date" id="endDate" min="{{ now()->toDateString() }}" value="{{ now()->addDay()->toDateString() }}" readonly style="background-color: #f8fafc;">
                </div>
            </div>
            <div id="dateHint" style="font-size: 0.8rem; color: #2563eb; margin-top: 6px; font-weight: 500;">
                📅 Pengembalian 1 hari setelah sewa (durasi 24 jam).
            </div>

            <a href="{{ route('bookings.create', ['product_id' => $product->id, 'duration' => '24h', 'start' => now()->toDateString()]) }}" id="bookingLink" class="btn" style="width:100%;margin-top:16px;display:flex;align-items:center;justify-content:center;text-decoration:none;">
                Booking &amp; Sewa Sekarang (Tanpa Login)
            </a>

            <div style="margin-top: 14px; padding: 10px 14px; background: #f8fafc; border-left: 3px solid #2563eb; border-radius: 6px; font-size: 0.75rem; color: #64748b; line-height: 1.6;">
                <div>• Cukup isi data pemesan &amp; lampirkan foto KTP / identitas saat checkout.</div>
                <div>• Sewa kamera body only harga akan disesuaikan, silahkan chat admin.</div>
                <div>• Over time perjam sesuai dengan syarat dan ketentuan.</div>
            </div>

            <div class="feature-grid" style="margin-top: 16px;">
                <div class="feature">◉ Rental terpercaya</div>
                <div class="feature">✓ Aman &amp; siap</div>
            </div>
        </div>
    </section>

    <section class="panel detail-description">
        <div class="tabs">
            <a href="#deskripsi">Deskripsi</a>
            <a href="#spesifikasi">Spesifikasi</a>
            <a href="#ulasan">Ulasan</a>
        </div>
        <div id="deskripsi">
            {{ $product->name }} adalah perlengkapan kamera terbaik untuk fotografer maupun videografer profesional maupun pemula. Unit selalu dicek berkala, bersih, dan siap digunakan untuk kebutuhan dokumentasi Anda.
        </div>
        <div id="spesifikasi">
            <strong>Spesifikasi &amp; Paket Tarif</strong>
            <p>Kategori: {{ $product->category }}</p>
            <p>Daftar Paket: 8 Jam (Rp {{ number_format($product->price_8h, 0, ',', '.') }}), 12 Jam (Rp {{ number_format($product->price_12h, 0, ',', '.') }}), 24 Jam (Rp {{ number_format($product->price_24h, 0, ',', '.') }}), 2 Hari (Rp {{ number_format($product->price_2d, 0, ',', '.') }}).</p>
            <p>Denda overtime: Rp {{ number_format($product->overtime_per_hour, 0, ',', '.') }} / jam keterlambatan pengembalian unit.</p>
        </div>
        <div id="ulasan">
            <strong>Ulasan pengguna</strong>
            <p>★ 4.8 dari 5 berdasarkan 120 ulasan pelanggan.</p>
        </div>
    </section>
</main>
@endsection
