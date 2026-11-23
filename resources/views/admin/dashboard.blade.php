@extends('layouts.app')
@section('title', 'Dashboard Admin - CamHub')
@section('content')
<style>
    html {
        scroll-behavior: smooth;
    }
    .dashboard {
        overflow: visible !important;
        align-items: stretch;
    }
    .dashboard .sidebar {
        overflow: visible !important;
        position: relative;
    }
    .sidebar-sticky {
        position: sticky;
        top: 1.25rem;
        max-height: calc(100vh - 2.5rem);
        overflow-y: auto;
    }
    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .sidebar nav a {
        display: flex !important;
        align-items: center;
        gap: 10px;
        transition: background-color 0.18s ease, color 0.18s ease;
    }
    .sidebar nav a svg,
    .icon-inline {
        flex-shrink: 0;
        vertical-align: middle;
    }
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    .link-icon {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .dashboard-main [id] {
        scroll-margin-top: 1.5rem;
    }
</style>

<main class="dashboard">
    <!-- Sidebar Admin -->
    <aside class="sidebar">
        <div class="sidebar-sticky">
            <div class="sidebar-brand">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
                <span>CamHub Admin</span>
            </div>

            <div class="sidebar-user">
                <strong>{{ auth()->user()->name }}</strong>
                <p>Super Administrator</p>
            </div>

            <nav id="admin-sidebar-nav">
                <a class="active" href="#overview-platform" data-section="overview-platform">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="7" height="9" x="3" y="3" rx="1"/>
                        <rect width="7" height="5" x="14" y="3" rx="1"/>
                        <rect width="7" height="9" x="14" y="12" rx="1"/>
                        <rect width="7" height="5" x="3" y="16" rx="1"/>
                    </svg>
                    <span>Overview Platform</span>
                </a>

                <a href="#verifikasi-kyc" data-section="verifikasi-kyc">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                        <circle cx="8" cy="12" r="2"/>
                        <path d="M14 10h4"/>
                        <path d="M14 14h4"/>
                    </svg>
                    <span>Verifikasi KYC Mitra</span>
                </a>

                <a href="#transaksi-rekber" data-section="transaksi-rekber">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/>
                        <path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>
                    </svg>
                    <span>Rekber &amp; Pesanan Sewa</span>
                </a>

                <a href="#manajemen-kamera" data-section="manajemen-kamera">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                        <circle cx="12" cy="13" r="3"/>
                    </svg>
                    <span>Katalog Kamera</span>
                </a>

                <a href="#manajemen-pengguna" data-section="manajemen-pengguna">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span>Pengguna Terdaftar</span>
                </a>

                <a href="{{ route('products.index') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
                        <path d="M2 12h20"/>
                    </svg>
                    <span>Katalog Publik</span>
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <section class="dashboard-main">
        <div id="overview-platform">
            <div style="margin-bottom: 1.5rem;">
                <h1>Panel Administrator CamHub</h1>
                <p style="color: #64748b; margin-top: 4px;">Kelola verifikasi identitas mitra, pantau perputaran dana Rekber, dan kontrol semua transaksi platform.</p>
            </div>

            <!-- Metric Cards -->
            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
                <div class="stat-card">
                    <p style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Total Pengguna</p>
                    <h2 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $totalUsers }} User</h2>
                    <span style="color: #64748b; font-size: 0.75rem; display: inline-block; margin-top: 6px;">{{ $totalMitra }} Mitra · {{ $totalCustomer }} Pembeli</span>
                </div>

                <div class="stat-card">
                    <p style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Total Transaksi Sewa</p>
                    <h2 style="font-size: 1.75rem; font-weight: 800; color: #2563eb; margin-top: 4px;">{{ $totalBookings }} Sewa</h2>
                    <a href="#transaksi-rekber" class="link-icon" style="color: #2563eb; font-weight: 600; font-size: 0.8rem; margin-top: 6px;">
                        <span>Lihat Pesanan</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14"/>
                            <path d="m12 5 7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <div class="stat-card">
                    <p style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Volume Dana Rekber</p>
                    <h2 style="font-size: 1.75rem; font-weight: 800; color: #16a34a; margin-top: 4px;">Rp {{ number_format($totalEscrow, 0, ',', '.') }}</h2>
                    <span style="color: #16a34a; font-size: 0.75rem; display: inline-block; margin-top: 6px;">100% Aman Terproteksi</span>
                </div>

                <div class="stat-card">
                    <p style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Pengajuan KYC Mitra</p>
                    <h2 style="font-size: 1.75rem; font-weight: 800; color: #d97706; margin-top: 4px;">{{ $totalKyc }} Berkas</h2>
                    <a href="#verifikasi-kyc" class="link-icon" style="color: #d97706; font-weight: 600; font-size: 0.8rem; margin-top: 6px;">
                        <span>Tinjau Dokumen</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14"/>
                            <path d="m12 5 7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Section 1: Verifikasi KYC Mitra -->
        <section class="panel" id="verifikasi-kyc" style="margin-bottom: 2rem;">
            <div class="section-heading" style="margin-bottom: 1.25rem;">
                <h2>Verifikasi Dokumen Identitas (KYC) Mitra</h2>
                <p style="color: #64748b; font-size: 0.85rem;">Periksa data KTP/KTM dan setujui mitra yang mendaftar agar dapat menyewakan &amp; titip jual kamera.</p>
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
                                <th style="padding: 10px 8px;">Dokumen KTP / KTM</th>
                                <th style="padding: 10px 8px;">Status KYC</th>
                                <th style="padding: 10px 8px; text-align: center;">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kycSubmissions as $k)
                                @php
                                    $docPath = $k->dokumen_path ?? $k->ktp_path ?? null;
                                    $rawStatus = strtolower(trim($k->status ?? 'pending'));
                                    $isApproved = in_array($rawStatus, ['disetujui', 'approved', 'verified']);
                                    $isRejected = in_array($rawStatus, ['ditolak', 'rejected']);
                                    $statusLabel = $isApproved ? 'Disetujui' : ($isRejected ? 'Ditolak' : 'Menunggu Review');
                                @endphp
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px 8px;">
                                        <strong>{{ $k->nama_lengkap ?? ($k->user->name ?? 'Mitra') }}</strong><br>
                                        <span style="color: #64748b; font-size: 0.8rem;">{{ $k->user->email ?? '-' }}</span>
                                    </td>
                                    <td style="padding: 12px 8px; font-family: monospace;">{{ $k->nik ?? '-' }}</td>
                                    <td style="padding: 12px 8px;">
                                        {{ !empty($k->tanggal_lahir) ? date('d/m/Y', strtotime($k->tanggal_lahir)) : '-' }} · {{ $k->jenis_kelamin ?? '-' }}
                                    </td>
                                    <td style="padding: 12px 8px;">
                                        @if ($docPath)
                                            <a href="{{ asset($docPath) }}" target="_blank" class="link-icon" style="color: #2563eb; text-decoration: underline; font-weight: 600;">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                                <span>Lihat Dokumen</span>
                                            </a>
                                        @else
                                            <span style="color: #94a3b8;">Tidak ada berkas</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 8px;">
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; background: {{ $isApproved ? '#dcfce7' : ($isRejected ? '#fee2e2' : '#fef3c7') }}; color: {{ $isApproved ? '#15803d' : ($isRejected ? '#b91c1c' : '#92400e') }};">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px 8px; text-align: center;">
                                        <div style="display: flex; gap: 6px; justify-content: center;">
                                            <form method="POST" action="{{ route('admin.kyc.update', $k->id) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="Disetujui">
                                                <button type="submit" class="btn btn-icon" {{ $isApproved ? 'disabled' : '' }} style="background: {{ $isApproved ? '#86efac' : '#16a34a' }}; color: white; padding: 5px 10px; font-size: 0.75rem; font-weight: 600; border-radius: 4px; border: none; cursor: {{ $isApproved ? 'not-allowed' : 'pointer' }};">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M20 6 9 17l-5-5"/>
                                                    </svg>
                                                    <span>{{ $isApproved ? 'Disetujui' : 'Setujui' }}</span>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.kyc.update', $k->id) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="Ditolak">
                                                <button type="submit" class="btn btn-icon" {{ $isRejected ? 'disabled' : '' }} style="background: {{ $isRejected ? '#fca5a5' : '#ef4444' }}; color: white; padding: 5px 10px; font-size: 0.75rem; font-weight: 600; border-radius: 4px; border: none; cursor: {{ $isRejected ? 'not-allowed' : 'pointer' }};">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M18 6 6 18"/>
                                                        <path d="m6 6 12 12"/>
                                                    </svg>
                                                    <span>{{ $isRejected ? 'Ditolak' : 'Tolak' }}</span>
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
                                @php
                                    $bookingCode = $b->booking_code ?? $b->id;
                                    $bookingStatus = $b->status ?? 'Menunggu verifikasi jaminan';
                                    $bookingTotal = $b->total ?? $b->total_price ?? $b->daily_rate ?? 0;
                                    $customerName = $b->customer_name ?? $b->guest_name ?? ($b->user->name ?? 'Penyewa');
                                    $customerPhone = $b->customer_phone ?? $b->guest_phone ?? '-';
                                    $startDate = !empty($b->start_date) ? date('d/m/Y', strtotime($b->start_date)) : '-';
                                    $endDate = !empty($b->end_date) ? date('d/m/Y', strtotime($b->end_date)) : '-';
                                @endphp
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px 8px; font-weight: 700; color: #2563eb;">#{{ $bookingCode }}</td>
                                    <td style="padding: 12px 8px;">
                                        <strong>{{ $customerName }}</strong><br>
                                        <span style="color: #64748b; font-size: 0.8rem;">{{ $customerPhone }}</span>
                                    </td>
                                    <td style="padding: 12px 8px;">{{ $b->product_name ?? ($b->product->name ?? '-') }}</td>
                                    <td style="padding: 12px 8px;">
                                        {{ $startDate }} s/d {{ $endDate }}
                                    </td>
                                    <td style="padding: 12px 8px; font-weight: 700; color: #0f172a;">
                                        Rp {{ number_format($bookingTotal, 0, ',', '.') }}
                                    </td>
                                    <td style="padding: 12px 8px;">
                                        @if (!empty($b->ktp_photo))
                                            <a href="{{ asset($b->ktp_photo) }}" target="_blank" class="link-icon" style="color: #2563eb; text-decoration: underline; font-weight: 600;">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                                <span>Lihat KTP</span>
                                            </a>
                                        @else
                                            <span style="color: #94a3b8;">-</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 8px;">
                                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; background: {{ str_contains($bookingStatus, 'Selesai') ? '#dcfce7' : (str_contains($bookingStatus, 'Lunas') || str_contains($bookingStatus, 'Disewa') ? '#dbeafe' : '#fef3c7') }}; color: {{ str_contains($bookingStatus, 'Selesai') ? '#15803d' : (str_contains($bookingStatus, 'Lunas') || str_contains($bookingStatus, 'Disewa') ? '#1e40af' : '#92400e') }};">
                                            {{ $bookingStatus }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px 8px; text-align: center;">
                                        @if ($bookingStatus === 'Menunggu verifikasi jaminan' || strtolower($bookingStatus) === 'pending')
                                            <form method="POST" action="{{ route('bookings.pay', $bookingCode) }}">
                                                @csrf
                                                <button type="submit" class="btn" style="background: #2563eb; color: white; padding: 5px 10px; font-size: 0.75rem; font-weight: 600; border-radius: 4px; border: none; cursor: pointer;">
                                                    Konfirmasi Bayar
                                                </button>
                                            </form>
                                        @elseif (!str_contains($bookingStatus, 'Selesai') && strtolower($bookingStatus) !== 'completed')
                                            <form method="POST" action="{{ route('bookings.complete', $bookingCode) }}">
                                                @csrf
                                                <button type="submit" class="btn" style="background: #16a34a; color: white; padding: 5px 10px; font-size: 0.75rem; font-weight: 600; border-radius: 4px; border: none; cursor: pointer;">
                                                    Cairkan ke Mitra
                                                </button>
                                            </form>
                                        @else
                                            <span class="link-icon" style="color: #16a34a; font-weight: 700; font-size: 0.8rem; justify-content: center;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                    <path d="m9 11 3 3L22 4"/>
                                                </svg>
                                                <span>Tuntas</span>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <!-- Section 3: Katalog Kamera -->
        <section class="panel" id="manajemen-kamera" style="margin-bottom: 2rem;">
            <div class="section-heading" style="margin-bottom: 1.25rem;">
                <h2>Katalog Kamera Terbaru ({{ $totalProducts }} Unit)</h2>
                <p style="color: #64748b; font-size: 0.85rem;">Ringkasan unit kamera &amp; lensa terbaru yang tersedia di etalase platform CamHub.</p>
            </div>

            @if ($products->isEmpty())
                <div style="text-align: center; padding: 2rem; color: #94a3b8;">
                    <p>Belum ada unit kamera di katalog.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e2e8f0; color: #475569;">
                                <th style="padding: 10px 8px;">Nama Unit</th>
                                <th style="padding: 10px 8px;">Kategori</th>
                                <th style="padding: 10px 8px;">Tarif 8J / 12J</th>
                                <th style="padding: 10px 8px;">Tarif 24 Jam</th>
                                <th style="padding: 10px 8px;">Overtime / Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $p)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 10px 8px; font-weight: 600; color: #0f172a;">{{ $p->name }}</td>
                                    <td style="padding: 10px 8px;">
                                        <span style="background: #f1f5f9; color: #334155; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                            {{ $p->category ?? '-' }}
                                        </span>
                                    </td>
                                    <td style="padding: 10px 8px;">
                                        Rp {{ number_format($p->price_8h ?? 0, 0, ',', '.') }} / Rp {{ number_format($p->price_12h ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td style="padding: 10px 8px; font-weight: 700; color: #2563eb;">
                                        Rp {{ number_format($p->price_24h ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td style="padding: 10px 8px; color: #64748b;">
                                        Rp {{ number_format($p->overtime_per_hour ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <!-- Section 4: Pengguna Platform -->
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
                            @php
                                $isAdmin = $u->role === 'admin';
                                $isMitra = in_array($u->role, ['mitra', 'vendor']);
                            @endphp
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 10px 8px; font-weight: 600;">{{ $u->name }}</td>
                                <td style="padding: 10px 8px; color: #64748b;">{{ $u->email }}</td>
                                <td style="padding: 10px 8px;">{{ $u->phone ?? '-' }}</td>
                                <td style="padding: 10px 8px;">
                                    <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; background: {{ $isAdmin ? '#f3e8ff' : ($isMitra ? '#dbeafe' : '#f1f5f9') }}; color: {{ $isAdmin ? '#7e22ce' : ($isMitra ? '#1e40af' : '#475569') }};">
                                        {{ $isMitra ? 'MITRA' : strtoupper($u->role ?? 'CUSTOMER') }}
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('#admin-sidebar-nav a[data-section]');
    const sections = Array.from(navLinks)
        .map(link => document.getElementById(link.getAttribute('data-section')))
        .filter(Boolean);

    function setActiveLink(sectionId) {
        navLinks.forEach(link => {
            link.classList.toggle('active', link.getAttribute('data-section') === sectionId);
        });
    }

    // Langsung pindah highlight saat menu diklik
    navLinks.forEach(link => {
        link.addEventListener('click', function () {
            setActiveLink(this.getAttribute('data-section'));
        });
    });

    // Ikut pindah otomatis saat halaman di-scroll
    function onScroll() {
        const scrollPos = window.scrollY;
        const nearBottom = window.innerHeight + scrollPos >= document.documentElement.scrollHeight - 40;

        if (nearBottom && sections.length > 0) {
            setActiveLink(sections[sections.length - 1].id);
            return;
        }

        let currentId = sections[0]?.id;
        sections.forEach(section => {
            const top = section.getBoundingClientRect().top + window.scrollY - 120;
            if (scrollPos >= top) {
                currentId = section.id;
            }
        });

        if (currentId) {
            setActiveLink(currentId);
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });

    // Sinkronkan juga kalau halaman dibuka langsung dengan hash (#verifikasi-kyc, dll)
    if (window.location.hash) {
        const hashId = window.location.hash.replace('#', '');
        if (document.getElementById(hashId)) {
            setActiveLink(hashId);
        }
    }
});
</script>
@endsection