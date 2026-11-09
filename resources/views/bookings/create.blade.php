@extends('layouts.app')

@section('title', 'Sewa Kamera & Checkout - CamHub')

@section('content')
<div class="page" style="max-width: 1040px; margin: 0 auto; padding: 2rem 1rem;">
    <div style="margin-bottom: 1.5rem;">
        <h1 style="font-size: 1.875rem; font-weight: 800; color: #0f172a;">Sewa Kamera &amp; Checkout</h1>
        <p style="color: #64748b; font-size: 0.95rem; margin-top: 4px;">Sewa langsung tanpa ribet membuat akun. Cukup lampirkan foto identitas sebagai jaminan.</p>
    </div>

    <!-- Notice Sewa Tanpa Login -->
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-left: 5px solid #2563eb; padding: 12px 16px; border-radius: 8px; margin-bottom: 2rem; display: flex; align-items: center; gap: 12px;">
        <span style="font-size: 1.5rem;">⚡</span>
        <div>
            <strong style="color: #1e40af; font-size: 0.95rem;">Sewa Cepat Tanpa Perlu Login!</strong>
            <p style="color: #3b82f6; font-size: 0.85rem; margin: 2px 0 0;">Kamu tidak wajib mendaftar akun untuk menyewa kamera. Cukup isi kontak dan unggah foto jaminan (KTP/SIM/KTM) di bawah ini.</p>
        </div>
    </div>

    <div class="checkout-grid" style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 2rem; align-items: start;">
        <section class="panel" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-bottom: 1.25rem;">Formulir Penyewa</h3>

            <form id="bookingForm" method="POST" action="{{ route('bookings.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="durasi" value="{{ $duration }}" id="bookingDuration">

                @if ($errors->any()) 
                    <div class="notice" role="alert" style="margin-bottom: 1.25rem; background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; padding: 12px 14px; border-radius: 6px; font-size: 0.9rem;">
                        <strong>Perhatian:</strong> {{ $errors->first() }}
                    </div> 
                @endif

                <label for="bookingStartDate" style="font-weight: 600; font-size: 0.9rem; color: #334155;">Tanggal Mulai Sewa</label>
                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $start) }}" id="bookingStartDate" min="{{ now()->toDateString() }}" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 4px; margin-bottom: 1rem;">

                <label style="font-weight: 600; font-size: 0.9rem; color: #334155;">Nama Lengkap Penyewa</label>
                <input type="text" name="nama" value="{{ old('nama', auth()->user()->name ?? '') }}" placeholder="Contoh: Krisna Bayu" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 4px; margin-bottom: 1rem;">

                <label style="font-weight: 600; font-size: 0.9rem; color: #334155;">Nomor WhatsApp / HP Aktif</label>
                <input type="tel" name="telepon" value="{{ old('telepon', auth()->user()->phone ?? '') }}" placeholder="Contoh: 081234567890" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 4px; margin-bottom: 1rem;">

                <!-- Bagian Jaminan Foto KTP -->
                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 1.25rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <span style="font-size: 1.2rem;">🪪</span>
                        <strong style="color: #0f172a; font-size: 0.95rem;">Foto Jaminan Identitas (Wajib)</strong>
                        <span style="background: #fee2e2; color: #b91c1c; font-size: 0.7rem; font-weight: 700; padding: 2px 6px; border-radius: 4px;">WAJIB</span>
                    </div>
                    <p style="color: #64748b; font-size: 0.8rem; margin-bottom: 10px;">Unggah foto asli <strong>KTP / SIM / Kartu Pelajar</strong> yang masih berlaku. Foto harus jelas dan tidak buram sebagai jaminan sewa.</p>
                    
                    <input type="file" name="ktp_photo" id="ktpInput" accept="image/*" required style="font-size: 0.85rem; width: 100%;">
                    <div style="margin-top: 8px;">
                        <img id="ktpPreview" src="#" alt="Pratinjau KTP" style="display: none; max-width: 200px; max-height: 120px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1; margin-top: 6px;">
                    </div>

                    <hr style="margin: 14px 0; border: none; border-top: 1px solid #e2e8f0;">

                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <span style="font-size: 1.2rem;">📸</span>
                        <strong style="color: #0f172a; font-size: 0.95rem;">Dokumen Pendukung / Selfie KTP (Opsional)</strong>
                        <span style="background: #e2e8f0; color: #475569; font-size: 0.7rem; font-weight: 600; padding: 2px 6px; border-radius: 4px;">OPSIONAL</span>
                    </div>
                    <p style="color: #64748b; font-size: 0.8rem; margin-bottom: 10px;">Foto selfie memegang KTP atau dokumen tambahan (SIM / KTM / NPWP / KK) untuk mempercepat verifikasi.</p>
                    <input type="file" name="guarantee_photo" id="guaranteeInput" accept="image/*" style="font-size: 0.85rem; width: 100%;">
                    <div style="margin-top: 8px;">
                        <img id="guaranteePreview" src="#" alt="Pratinjau Jaminan Kedua" style="display: none; max-width: 200px; max-height: 120px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1; margin-top: 6px;">
                    </div>
                </div>

                <label style="font-weight: 600; font-size: 0.9rem; color: #334155;">Metode Pengambilan Kamera</label>
                <div style="display: flex; flex-direction: column; gap: 8px; margin: 6px 0 1rem;">
                    <label class="radio-row" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="pengambilan" value="pickup" {{ old('pengambilan', 'pickup') === 'pickup' ? 'checked' : '' }}>
                        <span>Ambil di Lokasi Rental CamHub (Pickup)</span>
                    </label>
                    <label class="radio-row" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="pengambilan" value="kirim" {{ old('pengambilan') === 'kirim' ? 'checked' : '' }}>
                        <span>Kirim ke Alamat Pemesan (Kurir Instan)</span>
                    </label>
                </div>

                <label style="font-weight: 600; font-size: 0.9rem; color: #334155;">Alamat Pengambilan / Pengiriman</label>
                <input type="text" name="alamat" value="{{ old('alamat') }}" placeholder="Contoh: Jl. Merdeka No. 12, Kediri" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 4px; margin-bottom: 1rem;">

                <label style="font-weight: 600; font-size: 0.9rem; color: #334155; margin-top: 8px;">Pilihan Pembayaran</label>
                <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 6px;">
                    <label class="radio-row" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="pembayaran" value="escrow" {{ old('pembayaran', 'escrow') === 'escrow' ? 'checked' : '' }}>
                        <span><strong>Rekening Bersama (Escrow CamHub)</strong> - Paling Aman</span>
                    </label>
                    <p class="muted" style="margin: 0 0 4px 24px; font-size: 0.8rem; color: #64748b;">Dana ditahan aman di sistem CamHub sampai unit kamera Anda terima dan dicek.</p>

                    <label class="radio-row" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="pembayaran" value="transfer" {{ old('pembayaran') === 'transfer' ? 'checked' : '' }}>
                        <span>Transfer Bank (BCA, Mandiri, BRI, BNI)</span>
                    </label>

                    <label class="radio-row" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="pembayaran" value="ewallet" {{ old('pembayaran') === 'ewallet' ? 'checked' : '' }}>
                        <span>E-Wallet (GoPay, OVO, DANA, ShopeePay)</span>
                    </label>
                </div>

                <button type="submit" class="btn" style="width: 100%; margin-top: 1.5rem; background: #2563eb; color: white; padding: 0.85rem; font-size: 1rem; font-weight: 700; border-radius: 8px; border: none; cursor: pointer;">
                    Ajukan Sewa &amp; Selesaikan Pemesanan
                </button>
            </form>
        </section>

        <!-- Sidebar Ringkasan -->
        <aside class="panel" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); position: sticky; top: 20px;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-bottom: 1.25rem;">Ringkasan Sewa</h3>

            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid #e2e8f0;">
                @if ($product->image)
                    <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" style="width: 75px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1; flex-shrink: 0;">
                @else
                    <div class="mini-camera" style="font-size: 32px;">📷</div>
                @endif
                <div>
                    <strong style="font-size: 1.05rem; color: #0f172a; display: block;">{{ $product->name }}</strong>
                    <span style="display: inline-block; background: #e0f2fe; color: #0369a1; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; margin-top: 4px;">Paket: {{ $durationLabel }}</span>
                </div>
            </div>

            <div style="margin-bottom: 1rem; background: #f8fafc; padding: 12px; border-radius: 8px; font-size: 0.85rem; color: #475569; display: flex; flex-direction: column; gap: 6px;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Mulai Pinjam:</span>
                    <strong style="color: #0f172a;">{{ date('d M Y', strtotime($start)) }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Selesai / Kembali:</span>
                    <strong style="color: #2563eb;">{{ date('d M Y', strtotime($end)) }}</strong>
                </div>
            </div>

            <div class="summary-row" style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; color: #475569;">
                <span>Tarif Sewa ({{ $durationLabel }})</span>
                <strong style="color: #0f172a;">Rp {{ number_format($rate, 0, ',', '.') }}</strong>
            </div>

            <div class="summary-row" style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; color: #475569;">
                <span>Biaya Layanan &amp; Asuransi</span>
                <strong style="color: #0f172a;">Rp {{ number_format($adminFee, 0, ',', '.') }}</strong>
            </div>

            <div class="summary-row" style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.8rem; color: #64748b;">
                <span>Tarif Overtime / Jam</span>
                <span>Rp {{ number_format($product->overtime_per_hour, 0, ',', '.') }}</span>
            </div>

            <hr style="margin: 14px 0; border: none; border-top: 1px solid #e2e8f0;">

            <div class="summary-row" style="display: flex; justify-content: space-between; align-items: baseline;">
                <strong style="font-size: 1rem; color: #0f172a;">Total Pembayaran</strong>
                <strong class="price" style="font-size: 1.35rem; color: #2563eb;">Rp {{ number_format($total, 0, ',', '.') }}</strong>
            </div>

            <div style="margin-top: 1.25rem; font-size: 0.75rem; color: #94a3b8; line-height: 1.5;">
                🔒 Data jaminan identitas Anda dijamin kerahasiaannya dan hanya digunakan untuk keperluan verifikasi keamanan sewa unit.
            </div>
        </aside>
    </div>
</div>
@endsection
