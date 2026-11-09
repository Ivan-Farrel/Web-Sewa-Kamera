@extends('layouts.app')

@section('title', 'Sewa Berhasil Diajukan - CamHub')

@section('content')
<div class="page" style="max-width: 760px; margin: 2rem auto; padding: 1rem;">
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 2.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); text-align: center;">
        
        <div style="width: 72px; height: 72px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px; margin: 0 auto 1.25rem;">
            ✓
        </div>

        <h1 style="font-size: 1.875rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">Sewa Berhasil Diajukan!</h1>
        <p style="color: #64748b; font-size: 1rem; max-width: 540px; margin: 0 auto 1.5rem;">
            Pengajuan sewa kamu telah diterima oleh sistem CamHub. Silakan lakukan pembayaran melalui Virtual Account / Rekber di bawah ini.
        </p>

        <!-- Kode Booking -->
        <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem; display: inline-block; width: 100%;">
            <span style="font-size: 0.85rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">KODE BOOKING ANDA</span>
            <div style="font-size: 1.75rem; font-weight: 800; color: #2563eb; letter-spacing: 0.05em; margin: 4px 0;">
                #{{ $booking->booking_code }}
            </div>
            <span style="display: inline-block; background: {{ str_contains($booking->status, 'Lunas') ? '#dcfce7' : '#fef3c7' }}; color: {{ str_contains($booking->status, 'Lunas') ? '#15803d' : '#92400e' }}; font-size: 0.85rem; font-weight: 700; padding: 4px 12px; border-radius: 9999px;">
                {{ $booking->status }}
            </span>
        </div>

        @php
            $cleanPhone = preg_replace('/\D/', '', $booking->customer_phone ?? '081234567890');
            if (strlen($cleanPhone) < 8) $cleanPhone = '081234567890';
            $suffix = substr($cleanPhone, -8);

            if ($booking->payment_method === 'escrow') {
                $bankName = 'BCA Rekening Bersama (Escrow CamHub)';
                $vaNumber = '8807 ' . substr($suffix, 0, 4) . ' ' . substr($suffix, 4);
                $rawVa = '8807' . $suffix;
                $accountName = 'PT CAMHUB INDONESIA (ESCROW)';
                $badgeText = '🛡️ Rekber Terproteksi';
                $badgeBg = '#dbeafe';
                $badgeColor = '#1e40af';
                $desc = 'Dana Anda aman disimpan di rekening escrow CamHub dan baru dilepas ke rental setelah kamera Anda terima & selesai masa sewa.';
            } elseif ($booking->payment_method === 'transfer') {
                $bankName = 'Virtual Account BCA';
                $vaNumber = '8277 ' . substr($suffix, 0, 4) . ' ' . substr($suffix, 4);
                $rawVa = '8277' . $suffix;
                $accountName = 'CAMHUB - ' . strtoupper($booking->customer_name ?? 'PENYEWA');
                $badgeText = '⚡ Virtual Account';
                $badgeBg = '#fef3c7';
                $badgeColor = '#92400e';
                $desc = 'Nomor Virtual Account khusus pesanan Anda. Verifikasi instan otomatis.';
            } else {
                $bankName = 'QRIS & E-Wallet (GoPay, OVO, DANA, ShopeePay)';
                $vaNumber = '9360 ' . substr($suffix, 0, 4) . ' ' . substr($suffix, 4);
                $rawVa = '9360' . $suffix;
                $accountName = 'CAMHUB PAY';
                $badgeText = '📱 Instant Settlement';
                $badgeBg = '#dcfce7';
                $badgeColor = '#166534';
                $desc = 'Scan QRIS atau transfer melalui nomor virtual e-wallet di atas.';
            }
        @endphp

        <!-- KARTU VIRTUAL ACCOUNT / PEMBAYARAN -->
        <div style="text-align: left; background: #ffffff; border: 2px solid #3b82f6; border-radius: 14px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Metode Pembayaran</span>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-top: 2px;">{{ $bankName }}</h3>
                </div>
                <span style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                    {{ $badgeText }}
                </span>
            </div>

            <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 1.25rem;">{{ $desc }}</p>

            <!-- Box Nomor VA -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1rem;">
                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Nomor Virtual Account / Rekening</span>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                    <strong id="vaText" style="font-size: 1.35rem; font-family: monospace; letter-spacing: 0.08em; color: #0f172a;">{{ $vaNumber }}</strong>
                    <button type="button" onclick="copyToClipboard('{{ $rawVa }}', this)" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; font-size: 0.8rem; font-weight: 700; padding: 6px 12px; border-radius: 6px; cursor: pointer;">
                        📋 Salin VA
                    </button>
                </div>
                <div style="font-size: 0.8rem; color: #475569; margin-top: 6px;">
                    Atas Nama: <strong>{{ $accountName }}</strong>
                </div>
            </div>

            <!-- Box Total Nominal -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.25rem;">
                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Total Tagihan Pembayaran</span>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                    <strong style="font-size: 1.35rem; color: #2563eb; font-weight: 800;">Rp {{ number_format($booking->total, 0, ',', '.') }}</strong>
                    <button type="button" onclick="copyToClipboard('{{ $booking->total }}', this)" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; font-size: 0.8rem; font-weight: 700; padding: 6px 12px; border-radius: 6px; cursor: pointer;">
                        📋 Salin Nominal
                    </button>
                </div>
                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;">
                    Batas waktu transfer: <strong>24 Jam</strong> dari sekarang.
                </div>
            </div>

            <!-- Tombol Simulasi Bayar untuk Demo -->
            @if (!str_contains($booking->status, 'Lunas'))
                <form method="POST" action="{{ route('bookings.pay', $booking->booking_code) }}">
                    @csrf
                    <button type="submit" class="btn" style="width: 100%; background: #16a34a; color: white; padding: 0.75rem; font-size: 0.95rem; font-weight: 700; border-radius: 8px; border: none; cursor: pointer;">
                        💳 Simulasi Bayar Sekarang (Demo Mode)
                    </button>
                    <p style="text-align: center; font-size: 0.75rem; color: #64748b; margin-top: 6px;">
                        *Klik tombol di atas untuk mengetes alur konfirmasi pembayaran otomatis pada capstone project Anda.
                    </p>
                </form>
            @else
                <div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 10px; border-radius: 8px; text-align: center; font-weight: 700; font-size: 0.9rem;">
                    ✓ Pembayaran Berhasil Dikonfirmasi (Lunas). Unit siap diambil / dikirim!
                </div>
            @endif
        </div>

        <!-- Rincian Pesanan -->
        <div style="text-align: left; background: #f8fafc; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;">
                Rincian Penyewaan
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; font-size: 0.9rem;">
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.8rem;">Unit Kamera</span>
                    <strong style="color: #0f172a;">{{ $booking->product_name }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.8rem;">Metode Pengambilan</span>
                    <strong style="color: #0f172a;">{{ $booking->delivery_method === 'pickup' ? 'Ambil di Lokasi (Pickup)' : 'Kirim ke Alamat' }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.8rem;">Tanggal Mulai Sewa</span>
                    <strong style="color: #0f172a;">{{ $booking->start_date->format('d M Y') }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.8rem;">Tanggal Selesai / Pengembalian</span>
                    <strong style="color: #2563eb;">{{ $booking->end_date->format('d M Y') }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.8rem;">Penyewa</span>
                    <strong style="color: #0f172a;">{{ $booking->customer_name }} ({{ $booking->customer_phone }})</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 0.8rem;">Alamat Pengiriman / Domisili</span>
                    <strong style="color: #0f172a;">{{ $booking->customer_address }}</strong>
                </div>
            </div>

            @if ($booking->ktp_photo)
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; font-size: 0.85rem; color: #475569; display: flex; align-items: center; gap: 8px;">
                    <span>✓ Foto Jaminan KTP tersimpan aman:</span>
                    <a href="{{ asset($booking->ktp_photo) }}" target="_blank" style="color: #2563eb; text-decoration: underline; font-weight: 600;">Lihat Foto KTP 🔍</a>
                </div>
            @endif
        </div>

        <!-- Tombol Aksi -->
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('products.index') }}" class="btn" style="background: #2563eb; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 8px; font-weight: 600;">
                Sewa Kamera Lainnya
            </a>
            @auth
                <a href="{{ route('camhub.dashboard-pelanggan') }}" class="btn" style="background: #f1f5f9; color: #334155; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 8px; font-weight: 600; border: 1px solid #cbd5e1;">
                    Buka Dashboard Pesanan
                </a>
            @endauth
        </div>
    </div>
</div>

<script>
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const originalText = btn.textContent;
        btn.textContent = '✓ Tersalin!';
        btn.style.background = '#dcfce7';
        btn.style.color = '#15803d';
        setTimeout(() => {
            btn.textContent = originalText;
            btn.style.background = '#eff6ff';
            btn.style.color = '#2563eb';
        }, 2000);
    });
}
</script>
@endsection
