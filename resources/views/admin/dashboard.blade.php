@extends('layouts.app')
@section('title', 'Dashboard Admin - CamHub')
@section('content')
<main class="dashboard">
    <!-- Sidebar Admin -->
    <aside class="sidebar">
        <div class="sidebar-brand">▣ CamHub Admin</div>
        <div class="sidebar-user">
            <strong>{{ auth()->user()->name }}</strong>
            <p>Super Administrator</p>
        </div>
        <nav>
            <a class="active" href="{{ route('admin.dashboard') }}">⌂ &nbsp; Overview Platform</a>
            <a href="#verifikasi-kyc">🪪 &nbsp; Verifikasi KYC Mitra</a>
            <a href="#transaksi-rekber">💰 &nbsp; Rekber &amp; Pesanan Sewa</a>
            <a href="#manajemen-kamera">📷 &nbsp; Katalog Kamera</a>
            <a href="#manajemen-pengguna">👥 &nbsp; Pengguna Terdaftar</a>
            <a href="{{ route('products.index') }}">🌐 &nbsp; Katalog Publik</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <section class="dashboard-main">
        <div style="margin-bottom: 1.5rem;">
            <h1>Panel Administrator CamHub</h1>
            <p style="color: #64748b; margin-top: 4px;">Kelola verifikasi identitas mitra, pantau perputaran dana Rekber, dan kontrol semua transaksi platform.</p>
        </div>

        <!-- Metric Cards -->
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2rem;">
            <div class="stat-card">
                <p style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Total Pengguna</p>
                <h2 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $totalUsers }} User</h2>
                <span style="color: #64748b; font-size: 0.75rem; display: inline-block; margin-top: 6px;">{{ $totalMitra }} Mitra · {{ $totalCustomer }} Pembeli</span>
            </div>

            <div class="stat-card">
                <p style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Total Transaksi Sewa</p>
                <h2 style="font-size: 1.75rem; font-weight: 800; color: #2563eb; margin-top: 4px;">{{ $totalBookings }} Sewa</h2>
                <a href="#transaksi-rekber" style="color: #2563eb; font-weight: 600; font-size: 0.8rem; display: inline-block; margin-top: 6px;">Lihat Pesanan →</a>
            </div>

            <div class="stat-card">
                <p style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Volume Dana Rekber</p>
                <h2 style="font-size: 1.75rem; font-weight: 800; color: #16a34a; margin-top: 4px;">Rp {{ number_format($totalEscrow, 0, ',', '.') }}</h2>
                <span style="color: #16a34a; font-size: 0.75rem; display: inline-block; margin-top: 6px;">100% Aman Terproteksi</span>
            </div>

            <div class="stat-card">
                <p style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Pengajuan KYC Mitra</p>
                <h2 style="font-size: 1.75rem; font-weight: 800; color: #d97706; margin-top: 4px;">{{ $totalKyc }} Berkas</h2>
                <a href="#verifikasi-kyc" style="color: #d97706; font-weight: 600; font-size: 0.8rem; display: inline-block; margin-top: 6px;">Tinjau Dokumen →</a>
            </div>
        </div>

        <!-- Section 1: Verifikasi KYC Mitra -->
        <section class="panel" id="verifikasi-kyc" style="margin-bottom: 2rem;">
            <div class="section-heading" style="margin-bottom: 1.25rem;">
                <h2>Verifikasi Dokumen Identitas (KYC) Mitra</h2>
                <p style="color: #64748b; font-size: 0.85rem;">Periksa data KTP dan setujui mitra yang mendaftar agar dapat menyewakan &amp; titip jual kamera.</p>
            </div>

            @if ($kycSubmissions->isEmpty())
                <div style="text-align: center; padding: 2rem; color: #94a3b8;">
                    <p>Belum ada berkas KYC mitra yang masuk.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e2e8f0; color: #475569;">
                                <th style="padding: 10px 8px;">Mitra</th>
                                <th style="padding: 10px 8px;">NIK</th>
                                <th style="padding: 10px 8px;">Tgl Lahir / Gender</th>
                                <th style="padding: 10px 8px;">Dokumen KTP</th>
                                <th style="padding: 10px 8px;">Status KYC</th>
                                <th style="padding: 10px 8px; text-align: center;">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kycSubmissions as $k)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px 8px;">
                                        <strong>{{ $k->nama_lengkap }}</strong><br>
                                        <span style="color: #64748b; font-size: 0.8rem;">{{ $k->user->email ?? '-' }}</span>
                                    </td>
                                    <td style="padding: 12px 8px; font-family: monospace;">{{ $k->nik }}</td>
                                    <td style="padding: 12px 8px;">
                                        {{ $k->tanggal_lahir ? date('d/m/Y', strtotime($k->tanggal_lahir)) : '-' }} · {{ $k->jenis_kelamin }}
                                    </td>
                                    <td style="padding: 12px 8px;">
                                        <span style="color: #2563eb; font-weight: 600;">🪪 Dokumen Terlampir</span>
                                    </td>
                                    <td style="padding: 12px 8px;">
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; background: {{ $k->status === 'Disetujui' ? '#dcfce7' : ($k->status === 'Ditolak' ? '#fee2e2' : '#fef3c7') }}; color: {{ $k->status === 'Disetujui' ? '#15803d' : ($k->status === 'Ditolak' ? '#b91c1c' : '#92400e') }};">
                                            {{ $k->status ?? 'Menunggu Review' }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px 8px; text-align: center;">
                                        <div style="display: flex; gap: 6px; justify-content: center;">
                                            <form method="POST" action="{{ route('admin.kyc.update', $k->id) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="Disetujui">
                                                <button type="submit" class="btn" style="background: #16a34a; color: white; padding: 4px 10px; font-size: 0.75rem; font-weight: 600; border-radius: 4px; border: none; cursor: pointer;">
                                                    ✓ Setujui
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.kyc.update', $k->id) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="Ditolak">
                                                <button type="submit" class="btn" style="background: #ef4444; color: white; padding: 4px 10px; font-size: 0.75rem; font-weight: 600; border-radius: 4px; border: none; cursor: pointer;">
                                                    ✕ Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <!-- Section 2: Manajemen Transaksi Sewa & Rekber -->
        <section class="panel" id="transaksi-rekber" style="margin-bottom: 2rem;">
            <div class="section-heading" style="margin-bottom: 1.25rem;">
                <h2>Semua Transaksi Sewa &amp; Kontrol Rekber</h2>
                <p style="color: #64748b; font-size: 0.85rem;">Pantau status pembayaran, foto KTP jaminan penyewa, dan kontrol pencairan dana ke mitra.</p>
            </div>

            @if ($bookings->isEmpty())
                <div style="text-align: center; padding: 2rem; color: #94a3b8;">
                    <p>Belum ada transaksi sewa yang tercatat di sistem.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e2e8f0; color: #475569;">
                                <th style="padding: 10px 8px;">Kode</th>
                                <th style="padding: 10px 8px;">Penyewa</th>
                                <th style="padding: 10px 8px;">Unit &amp; Paket</th>
                                <th style="padding: 10px 8px;">Periode Sewa</th>
                                <th style="padding: 10px 8px;">Total Biaya</th>
                                <th style="padding: 10px 8px;">KTP Jaminan</th>
                                <th style="padding: 10px 8px;">Status Rekber</th>
                                <th style="padding: 10px 8px; text-align: center;">Kontrol Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $b)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px 8px; font-weight: 700; color: #2563eb;">#{{ $b->booking_code }}</td>
                                    <td style="padding: 12px 8px;">
                                        <strong>{{ $b->customer_name }}</strong><br>
                                        <span style="color: #64748b; font-size: 0.8rem;">{{ $b->customer_phone }}</span>
                                    </td>
                                    <td style="padding: 12px 8px;">{{ $b->product_name }}</td>
                                    <td style="padding: 12px 8px;">
                                        {{ $b->start_date ? $b->start_date->format('d/m/Y') : '-' }} s/d {{ $b->end_date ? $b->end_date->format('d/m/Y') : '-' }}
                                    </td>
                                    <td style="padding: 12px 8px; font-weight: 700; color: #0f172a;">
                                        Rp {{ number_format($b->total, 0, ',', '.') }}
                                    </td>
                                    <td style="padding: 12px 8px;">
                                        @if ($b->ktp_photo)
                                            <a href="{{ asset($b->ktp_photo) }}" target="_blank" style="color: #2563eb; text-decoration: underline; font-weight: 600;">Lihat KTP 🔍</a>
                                        @else
                                            <span style="color: #94a3b8;">-</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 8px;">
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; background: {{ str_contains($b->status, 'Selesai') ? '#dcfce7' : (str_contains($b->status, 'Lunas') ? '#dbeafe' : '#fef3c7') }}; color: {{ str_contains($b->status, 'Selesai') ? '#15803d' : (str_contains($b->status, 'Lunas') ? '#1e40af' : '#92400e') }};">
                                            {{ $b->status }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px 8px; text-align: center;">
                                        @if (!str_contains($b->status, 'Lunas') && !str_contains($b->status, 'Selesai'))
                                            <form method="POST" action="{{ route('bookings.pay', $b->booking_code) }}">
                                                @csrf
                                                <button type="submit" class="btn" style="background: #2563eb; color: white; padding: 4px 10px; font-size: 0.75rem; font-weight: 600; border-radius: 4px; border: none; cursor: pointer;">
                                                    Konfirmasi Bayar
                                                </button>
                                            </form>
                                        @elseif (!str_contains($b->status, 'Selesai'))
                                            <form method="POST" action="{{ route('bookings.complete', $b->booking_code) }}">
                                                @csrf
                                                <button type="submit" class="btn" style="background: #16a34a; color: white; padding: 4px 10px; font-size: 0.75rem; font-weight: 600; border-radius: 4px; border: none; cursor: pointer;">
                                                    Cairkan ke Mitra
                                                </button>
                                            </form>
                                        @else
                                            <span style="color: #16a34a; font-weight: 700; font-size: 0.8rem;">✓ Tuntas</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <!-- Section 3: Pengguna Platform -->
        <section class="panel" id="manajemen-pengguna">
            <div class="section-heading" style="margin-bottom: 1.25rem;">
                <h2>Pengguna Terdaftar di Platform</h2>
                <p style="color: #64748b; font-size: 0.85rem;">Daftar akun Administrator, Mitra Penjual, dan Pembeli.</p>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; color: #475569;">
                            <th style="padding: 10px 8px;">Nama</th>
                            <th style="padding: 10px 8px;">Email</th>
                            <th style="padding: 10px 8px;">No. HP</th>
                            <th style="padding: 10px 8px;">Peran / Role</th>
                            <th style="padding: 10px 8px;">Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 10px 8px; font-weight: 600;">{{ $u->name }}</td>
                                <td style="padding: 10px 8px; color: #64748b;">{{ $u->email }}</td>
                                <td style="padding: 10px 8px;">{{ $u->phone ?? '-' }}</td>
                                <td style="padding: 10px 8px;">
                                    <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; background: {{ $u->role === 'admin' ? '#f3e8ff' : ($u->role === 'mitra' ? '#dbeafe' : '#f1f5f9') }}; color: {{ $u->role === 'admin' ? '#7e22ce' : ($u->role === 'mitra' ? '#1e40af' : '#475569') }};">
                                        {{ strtoupper($u->role) }}
                                    </span>
                                </td>
                                <td style="padding: 10px 8px; color: #94a3b8; font-size: 0.8rem;">
                                    {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </section>
</main>
@endsection
