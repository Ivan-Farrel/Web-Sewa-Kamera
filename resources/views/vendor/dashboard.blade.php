@extends('layouts.app')
@section('title', 'Dashboard Mitra Lapak (C2C & C2B) - CamHub')

@section('content')
<main style="background: #f8fafc; min-height: 100vh; padding: 2.5rem 1.5rem;">
    <div style="max-width: 1200px; margin: 0 auto;">

        {{-- HEADER MITRA --}}
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 2rem; background: #0f172a; color: #fff; padding: 1.75rem 2rem; border-radius: 16px;">
            <div>
                <span style="background: #2563eb; color: #fff; padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">PORTAL MITRA LAPAK (C2C & C2B)</span>
                <h1 style="font-size: 1.65rem; font-weight: 800; margin: 0.5rem 0 0.25rem;">Halo, {{ $user->name }}! 👋</h1>
                <p style="color: #94a3b8; font-size: 0.9rem; margin: 0;">Kelola etalase sewa & jual kamera, konfirmasi pengembalian unit, dan pantau dana Rekening Bersama (Escrow).</p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <a href="{{ url('/products') }}" style="background: #1e293b; color: #fff; padding: 0.65rem 1.1rem; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 600; border: 1px solid #334155;">Lihat Katalog Publik</a>
            </div>
        </div>

        {{-- NOTIFIKASI --}}
        @if (session('success'))
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; font-weight: 600;">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- RINGKASAN METRIK SESUAI LAPORAN --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
            <div style="background: #fff; padding: 1.35rem; border-radius: 14px; border: 1px solid #e2e8f0;">
                <div style="color: #64748b; font-size: 0.8rem; font-weight: 600;">TOTAL UNIT ETALASE</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-top: 0.35rem;">{{ $products->count() }} Unit</div>
                <div style="color: #16a34a; font-size: 0.78rem; margin-top: 0.25rem;">Siap disewa / dijual</div>
            </div>

            <div style="background: #fff; padding: 1.35rem; border-radius: 14px; border: 1px solid #e2e8f0;">
                <div style="color: #64748b; font-size: 0.8rem; font-weight: 600;">TRANSAKSI MASUK</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: #2563eb; margin-top: 0.35rem;">{{ $bookings->count() }} Pesanan</div>
                <div style="color: #64748b; font-size: 0.78rem; margin-top: 0.25rem;">Kalender ketersediaan aktif</div>
            </div>

            <div style="background: #fff; padding: 1.35rem; border-radius: 14px; border: 1px solid #e2e8f0;">
                <div style="color: #64748b; font-size: 0.8rem; font-weight: 600;">STATUS REKBER (ESCROW)</div>
                <div style="font-size: 1.35rem; font-weight: 800; color: #0f172a; margin-top: 0.35rem;">Terlindungi 🛡️</div>
                <div style="color: #64748b; font-size: 0.78rem; margin-top: 0.25rem;">Cair otomatis saat unit kembali</div>
            </div>

            <div style="background: #fff; padding: 1.35rem; border-radius: 14px; border: 1px solid #e2e8f0;">
                <div style="color: #64748b; font-size: 0.8rem; font-weight: 600;">VERIFIKASI IDENTITAS (KYC)</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: {{ $kyc ? '#16a34a' : '#d97706' }}; margin-top: 0.35rem;">
                    {{ $kyc ? strtoupper($kyc->status ?? 'TERKIRIM') : 'BELUM UPLOAD KTP' }}
                </div>
                <div style="color: #64748b; font-size: 0.78rem; margin-top: 0.25rem;">Syarat pencairan Rekber Mitra</div>
            </div>
        </div>

        {{-- GRID UTAMA: FORM UPLOAD KAMERA & VERIFIKASI KYC --}}
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2.5rem;">

            {{-- FORM UPLOAD KAMERA / LENSA (DUAL-LISTING C2C & C2B) --}}
            <div style="background: #fff; padding: 1.75rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                <h2 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">📸 Upload Kamera / Lensa ke Etalase</h2>
                <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.25rem;">
                    Mendukung Dual-Listing: Sewa Harian (C2C), Jual Putus, maupun Pengajuan Titip Jual / Konsinyasi ke Toko (C2B).
                </p>

                <form action="{{ route('vendor.products.store') }}" method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    @csrf
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Nama Kamera / Lensa / Alat</label>
                        <input type="text" name="name" placeholder="Contoh: Sony A7 III Body Only / Lensa Canon EF 50mm f/1.8" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Kategori Produk</label>
                        <select name="category" required style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff;">
                            <option value="DSLR">Kamera DSLR</option>
                            <option value="Mirrorless">Kamera Mirrorless</option>
                            <option value="Lensa">Lensa Kamera</option>
                            <option value="Audio">Audio / Mic</option>
                            <option value="Lighting">Lighting / Flash</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Skema Listing (C2C / C2B)</label>
                        <select name="listing_type" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff;">
                            <option value="sewa">Sewa Harian (Mitra C2C)</option>
                            <option value="hybrid">Hybrid: Sewa & Jual Putus (C2C)</option>
                            <option value="c2b">Ajukan Titip Jual / Buyback ke Toko (C2B)</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Tarif Sewa 8 Jam (Rp)</label>
                        <input type="number" name="price_8h" value="50000" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Tarif Sewa 12 Jam (Rp)</label>
                        <input type="number" name="price_12h" value="75000" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Tarif Sewa 24 Jam / Harian (Rp)</label>
                        <input type="number" name="price_24h" value="110000" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Tarif Paket 2 Hari (Rp)</label>
                        <input type="number" name="price_2d" value="200000" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Denda Overtime per Jam (Rp)</label>
                        <input type="number" name="overtime_per_hour" value="15000" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Foto Produk (JPG/PNG)</label>
                        <input type="file" name="image_file" accept="image/*"
                            style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #f8fafc;">
                    </div>

                    <div style="grid-column: span 2; margin-top: 0.5rem;">
                        <button type="submit" style="width: 100%; background: #2563eb; color: #fff; padding: 0.8rem; border: none; border-radius: 10px; font-weight: 700; font-size: 0.95rem; cursor: pointer;">
                            + Tayangkan Produk ke Katalog CamHub
                        </button>
                    </div>
                </form>
            </div>

            {{-- PANEL KYC MITRA & INFO C2B --}}
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div style="background: #fff; padding: 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 0.35rem;">🪪 Verifikasi KYC Mitra</h3>
                    <p style="color: #64748b; font-size: 0.82rem; margin-bottom: 1rem;">
                        Unggah foto KTP/KTM asli untuk mengaktifkan pencairan dana Rekening Bersama (Escrow).
                    </p>
                    <form action="{{ route('vendor.kyc.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="ktp_image" accept="image/*" required
                            style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 8px; margin-bottom: 0.75rem; font-size: 0.82rem;">
                        <button type="submit" style="width: 100%; background: #0f172a; color: #fff; padding: 0.65rem; border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                            Upload KTP / KTM
                        </button>
                    </form>
                </div>

                <div style="background: #eff6ff; padding: 1.35rem; border-radius: 16px; border: 1px solid #bfdbfe;">
                    <h4 style="font-size: 0.95rem; font-weight: 800; color: #1e3a8a; margin: 0 0 0.4rem;">💡 Aturan Denda & Rekber CamHub</h4>
                    <ul style="margin: 0; padding-left: 1.15rem; color: #1e40af; font-size: 0.8rem; line-height: 1.5;">
                        <li>Dana penyewa ditahan di <b>Rekening Bersama (Escrow)</b>.</li>
                        <li>Keterlambatan pengembalian dikenakan <b>Denda Otomatis 150% x Tarif Harian x Hari Telat</b>.</li>
                        <li>Dana cair otomatis setelah inspeksi fisik pengembalian selesai.</li>
                    </ul>
                </div>
            </div>

        </div>

        {{-- TABEL DAFTAR KAMERA / LENSA MILIK MITRA --}}
        <div style="background: #fff; padding: 1.75rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem;">📦 Etalase Kamera & Lensa Saya</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569;">
                            <th style="padding: 0.75rem;">Produk</th>
                            <th style="padding: 0.75rem;">Kategori</th>
                            <th style="padding: 0.75rem;">Sewa 8J / 12J</th>
                            <th style="padding: 0.75rem;">Sewa 24 Jam</th>
                            <th style="padding: 0.75rem;">Overtime/Jam</th>
                            <th style="padding: 0.75rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $item)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 0.75rem; font-weight: 700; color: #0f172a;">{{ $item->name }}</td>
                                <td style="padding: 0.75rem;"><span style="background: #f1f5f9; padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.78rem;">{{ $item->category }}</span></td>
                                <td style="padding: 0.75rem;">Rp {{ number_format($item->price_8h, 0, ',', '.') }} / Rp {{ number_format($item->price_12h, 0, ',', '.') }}</td>
                                <td style="padding: 0.75rem; font-weight: 700; color: #2563eb;">Rp {{ number_format($item->price_24h, 0, ',', '.') }}</td>
                                <td style="padding: 0.75rem;">Rp {{ number_format($item->overtime_per_hour, 0, ',', '.') }}</td>
                                <td style="padding: 0.75rem;">
                                    <form action="{{ route('vendor.products.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus unit ini dari katalog?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #fee2e2; color: #dc2626; border: none; padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.78rem;">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 1.5rem; text-align: center; color: #94a3b8;">Belum ada kamera/lensa yang Anda unggah. Gunakan formulir di atas untuk menambah unit pertama!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL KONFIRMASI PENYEWAAN & PENGEMBALIAN BARANG (DENDA 150%) --}}
        <div style="background: #fff; padding: 1.75rem; border-radius: 16px; border: 1px solid #e2e8f0;">
            <h2 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 0.35rem;">🔄 Konfirmasi Penyewaan & Pengembalian Unit</h2>
            <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.25rem;">
                Konfirmasi penyerahan unit ke penyewa dan lakukan inspeksi fisik saat unit dikembalikan untuk mencairkan dana Rekber.
            </p>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569;">
                            <th style="padding: 0.75rem;">ID Booking</th>
                            <th style="padding: 0.75rem;">Penyewa</th>
                            <th style="padding: 0.75rem;">Status</th>
                            <th style="padding: 0.75rem;">Konfirmasi Ambil</th>
                            <th style="padding: 0.75rem;">Inspeksi Pengembalian & Hitung Denda (150%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 0.75rem; font-weight: 700;">#{{ $booking->id }}</td>
                                <td style="padding: 0.75rem;">{{ $booking->guest_name ?? ($booking->user->name ?? 'Penyewa') }}</td>
                                <td style="padding: 0.75rem;">
                                    <span style="background: #dbeafe; color: #1e40af; padding: 0.25rem 0.6rem; border-radius: 99px; font-weight: 700; font-size: 0.75rem;">
                                        {{ strtoupper($booking->status ?? 'PENDING') }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <form action="{{ route('vendor.bookings.confirm', $booking->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" style="background: #2563eb; color: #fff; border: none; padding: 0.45rem 0.8rem; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.78rem;">
                                            Serahkan Unit (Disewa)
                                        </button>
                                    </form>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <form action="{{ route('vendor.bookings.return', $booking->id) }}" method="POST" style="display: flex; gap: 0.4rem; align-items: center;">
                                        @csrf
                                        <input type="number" name="late_days" placeholder="Hari telat (0)" min="0" value="0" style="width: 90px; padding: 0.35rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.78rem;" title="Jumlah Hari Terlambat">
                                        <input type="number" name="damage_fee" placeholder="Biaya rusak (Rp)" min="0" value="0" style="width: 120px; padding: 0.35rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.78rem;" title="Denda Kerusakan Fisik">
                                        <button type="submit" style="background: #16a34a; color: #fff; border: none; padding: 0.45rem 0.8rem; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.78rem;">
                                            Selesai & Cairkan Rekber
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 1.5rem; text-align: center; color: #94a3b8;">Belum ada pesanan sewa yang masuk untuk unit Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>
@endsection