@extends('layouts.app')
@section('title', 'Dashboard Mitra - CamHub')
@section('content')
<main class="dashboard">
    <!-- Sidebar Mitra -->
    <aside class="sidebar">
        <div class="sidebar-brand">▣ CamHub</div>
        <div class="sidebar-user">
            <strong>{{ auth()->user()->name }}</strong>
            <p>Mitra / Penjual Rental</p>
        </div>
        <nav>
            <a class="active" href="{{ route('vendor.dashboard') }}">⌂ &nbsp; Beranda Mitra</a>
            <a href="#kamera-saya">📷 &nbsp; Kamera Saya</a>
            <a href="#pesanan-sewa">📦 &nbsp; Pesanan Sewa</a>
            <a href="#tambah-kamera">➕ &nbsp; Titip Kamera Baru</a>
            <a href="{{ route('camhub.kyc') }}">🪪 &nbsp; Verifikasi KYC</a>
            <a href="{{ route('products.index') }}">🌐 &nbsp; Katalog Publik</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <section class="dashboard-main">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1>Halo, {{ auth()->user()->name }}! 👋</h1>
                <p style="color: #64748b; margin-top: 4px;">Kelola unit sewa, pantau pergerakan saldo Rekber, dan konfirmasi unit sewa kamu.</p>
            </div>
            <div>
                @if ($kyc && $kyc->status === 'Disetujui')
                    <span style="display: inline-flex; align-items: center; gap: 6px; background: #dcfce7; color: #15803d; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 0.85rem; border: 1px solid #86efac;">
                        ✓ Mitra Terverifikasi KYC
                    </span>
                @elseif ($kyc)
                    <span style="display: inline-flex; align-items: center; gap: 6px; background: #fef3c7; color: #92400e; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 0.85rem; border: 1px solid #fde68a;">
                        ⏳ Dokumen KYC Dalam Peninjauan
                    </span>
                @else
                    <a href="{{ route('camhub.kyc') }}" style="display: inline-flex; align-items: center; gap: 6px; background: #fee2e2; color: #b91c1c; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 0.85rem; border: 1px solid #fca5a5; text-decoration: none;">
                        ⚠️ Lengkapi KYC Mitra →
                    </a>
                @endif
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2rem;">
            <div class="stat-card">
                <p style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Saldo Siap Tarik</p>
                <h2 style="color: #16a34a; font-size: 1.6rem; font-weight: 800; margin-top: 4px;">Rp {{ number_format($saldoSiapTarik, 0, ',', '.') }}</h2>
                <a href="#penarikan-saldo" style="color: #16a34a; font-weight: 600; font-size: 0.8rem; display: inline-block; margin-top: 8px;">Tarik ke Rekening →</a>
            </div>

            <div class="stat-card">
                <p style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Saldo di Rekber (Aktif)</p>
                <h2 style="color: #2563eb; font-size: 1.6rem; font-weight: 800; margin-top: 4px;">Rp {{ number_format($saldoTertahan, 0, ',', '.') }}</h2>
                <span style="color: #64748b; font-size: 0.75rem; display: inline-block; margin-top: 8px;">Cair saat kamera kembali</span>
            </div>

            <div class="stat-card">
                <p style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Unit Kamera Saya</p>
                <h2 style="font-size: 1.6rem; font-weight: 800; margin-top: 4px;">{{ $myProducts->count() }} Unit</h2>
                <a href="#kamera-saya" style="color: #2563eb; font-weight: 600; font-size: 0.8rem; display: inline-block; margin-top: 8px;">Kelola Stok →</a>
            </div>

            <div class="stat-card">
                <p style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Sewa Selesai</p>
                <h2 style="font-size: 1.6rem; font-weight: 800; margin-top: 4px;">{{ $totalSelesai }} Transaksi</h2>
                <span style="color: #64748b; font-size: 0.75rem; display: inline-block; margin-top: 8px;">100% Sukses</span>
            </div>
        </div>

        <!-- Section 1: Pesanan Sewa Masuk & Konfirmasi Pengembalian -->
        <section class="panel" id="pesanan-sewa" style="margin-bottom: 2rem;">
            <div class="section-heading" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <div>
                    <h2>Pesanan Sewa Masuk &amp; Pelepasan Dana Rekber</h2>
                    <p style="color: #64748b; font-size: 0.85rem; margin-top: 2px;">Pantau unit yang sedang disewa. Jika kamera sudah dikembalikan dan dicek, konfirmasi untuk mencairkan dana.</p>
                </div>
            </div>

            @if ($bookings->isEmpty())
                <div style="text-align: center; padding: 2rem; color: #94a3b8;">
                    <p>Belum ada pesanan sewa masuk saat ini.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e2e8f0; color: #475569;">
                                <th style="padding: 10px 8px;">Kode</th>
                                <th style="padding: 10px 8px;">Penyewa</th>
                                <th style="padding: 10px 8px;">Unit Kamera</th>
                                <th style="padding: 10px 8px;">Periode Sewa</th>
                                <th style="padding: 10px 8px;">Dana Bersih Mitra</th>
                                <th style="padding: 10px 8px;">Status Rekber</th>
                                <th style="padding: 10px 8px; text-align: center;">Aksi Mitra</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $b)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px 8px; font-weight: 700; color: #2563eb;">#{{ $b->booking_code }}</td>
                                    <td style="padding: 12px 8px;">
                                        <strong>{{ $b->customer_name }}</strong><br>
                                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $b->customer_phone) }}" target="_blank" style="color: #16a34a; font-weight: 600; text-decoration: none; font-size: 0.8rem;">
                                            💬 {{ $b->customer_phone }}
                                        </a>
                                    </td>
                                    <td style="padding: 12px 8px;">{{ $b->product_name }}</td>
                                    <td style="padding: 12px 8px;">
                                        {{ $b->start_date ? $b->start_date->format('d M') : '-' }} s/d {{ $b->end_date ? $b->end_date->format('d M Y') : '-' }}
                                    </td>
                                    <td style="padding: 12px 8px; font-weight: 700; color: #0f172a;">
                                        Rp {{ number_format($b->daily_rate, 0, ',', '.') }}
                                    </td>
                                    <td style="padding: 12px 8px;">
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; background: {{ str_contains($b->status, 'Selesai') ? '#dcfce7' : (str_contains($b->status, 'Lunas') ? '#dbeafe' : '#fef3c7') }}; color: {{ str_contains($b->status, 'Selesai') ? '#15803d' : (str_contains($b->status, 'Lunas') ? '#1e40af' : '#92400e') }};">
                                            {{ $b->status }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px 8px; text-align: center;">
                                        @if (!str_contains($b->status, 'Selesai'))
                                            <form method="POST" action="{{ route('bookings.complete', $b->booking_code) }}" onsubmit="return confirm('Apakah Anda sudah mengecek fisik kamera dan memastikan unit kembali dalam keadaan baik? Dana akan langsung dicairkan ke saldo Anda.');">
                                                @csrf
                                                <button type="submit" class="btn" style="background: #16a34a; color: white; padding: 6px 12px; font-size: 0.75rem; font-weight: 700; border-radius: 6px; border: none; cursor: pointer;">
                                                    ✓ Kamera Kembali &amp; Cairkan Dana
                                                </button>
                                            </form>
                                        @else
                                            <span style="color: #16a34a; font-weight: 700; font-size: 0.8rem;">✓ Dana Dicairkan</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <!-- Section 2: Form Titip Kamera / Tambah Unit Baru -->
        <section class="panel" id="tambah-kamera" style="margin-bottom: 2rem;">
            <div class="section-heading" style="margin-bottom: 1.25rem;">
                <h2>Titip Sewa / Tambah Kamera Baru</h2>
                <p style="color: #64748b; font-size: 0.85rem;">Daftarkan kamera Anda untuk disewakan atau dititip jual di platform CamHub.</p>
            </div>

            <form method="POST" action="{{ route('vendor.products.store') }}" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                @csrf
                <div>
                    <label style="font-weight: 600; font-size: 0.85rem; color: #334155;">Nama Kamera / Lensa</label>
                    <input type="text" name="name" placeholder="Contoh: Sony FX30 Cinema" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 4px;">
                </div>

                <div>
                    <label style="font-weight: 600; font-size: 0.85rem; color: #334155;">Kategori</label>
                    <select name="category" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 4px;">
                        <option value="Mirrorless">Mirrorless</option>
                        <option value="DSLR">DSLR</option>
                        <option value="Lensa">Lensa</option>
                        <option value="Audio">Audio</option>
                        <option value="Lighting">Lighting</option>
                        <option value="Aksesoris">Aksesoris</option>
                    </select>
                </div>

                <div>
                    <label style="font-weight: 600; font-size: 0.85rem; color: #334155;">Tarif Paket 8 Jam (Rp)</label>
                    <input type="number" name="price_8h" placeholder="Contoh: 150000" min="10000" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 4px;">
                </div>

                <div>
                    <label style="font-weight: 600; font-size: 0.85rem; color: #334155;">Tarif Paket 12 Jam (Rp)</label>
                    <input type="number" name="price_12h" placeholder="Contoh: 200000" min="10000" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 4px;">
                </div>

                <div>
                    <label style="font-weight: 600; font-size: 0.85rem; color: #334155;">Tarif Paket 24 Jam (Rp)</label>
                    <input type="number" name="price_24h" placeholder="Contoh: 250000" min="10000" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 4px;">
                </div>

                <div>
                    <label style="font-weight: 600; font-size: 0.85rem; color: #334155;">Tarif Paket 2 Hari (Rp)</label>
                    <input type="number" name="price_2d" placeholder="Contoh: 450000" min="10000" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 4px;">
                </div>

                <div>
                    <label style="font-weight: 600; font-size: 0.85rem; color: #334155;">Tarif Denda Overtime per Jam (Rp)</label>
                    <input type="number" name="overtime_per_hour" placeholder="Contoh: 25000" min="1000" required style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 4px;">
                </div>

                <div>
                    <label style="font-weight: 600; font-size: 0.85rem; color: #334155;">Foto Unit Kamera</label>
                    <input type="file" name="image" accept="image/*" style="width: 100%; padding: 0.5rem; font-size: 0.85rem; margin-top: 4px;">
                </div>

                <div style="grid-column: span 2; margin-top: 0.5rem;">
                    <button type="submit" class="btn" style="background: #2563eb; color: white; padding: 0.75rem 1.5rem; font-weight: 700; border-radius: 8px; border: none; cursor: pointer;">
                        ➕ Daftarkan Kamera Sekarang
                    </button>
                </div>
            </form>
        </section>

        <!-- Section 3: Daftar Kamera Saya -->
        <section class="panel" id="kamera-saya">
            <div class="section-heading" style="margin-bottom: 1rem;">
                <h2>Katalog Kamera Milik Saya</h2>
                <p style="color: #64748b; font-size: 0.85rem;">Daftar unit yang aktif di katalog dan siap disewa pelanggan.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem;">
                @foreach ($myProducts as $prod)
                    <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; background: #fafafa;">
                        @if ($prod->image)
                            <img src="{{ asset('images/' . $prod->image) }}" alt="{{ $prod->name }}" style="width: 100%; height: 120px; object-fit: cover; border-radius: 6px; margin-bottom: 8px;">
                        @else
                            <div style="width: 100%; height: 120px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 32px; border-radius: 6px; margin-bottom: 8px;">📷</div>
                        @endif
                        <span style="font-size: 0.7rem; background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; font-weight: 600;">{{ $prod->category }}</span>
                        <h4 style="font-size: 0.95rem; margin: 6px 0 4px; color: #0f172a;">{{ $prod->name }}</h4>
                        <div style="font-size: 0.85rem; font-weight: 700; color: #2563eb;">
                            Rp {{ number_format($prod->price_24h, 0, ',', '.') }}<small style="color: #64748b; font-weight: 400;">/24 jam</small>
                        </div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px;">
                            OT: Rp {{ number_format($prod->overtime_per_hour, 0, ',', '.') }}/jam
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </section>
</main>
@endsection
