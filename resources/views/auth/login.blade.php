@extends('layouts.app')
@section('title', 'Masuk atau Daftar Akun - CamHub')
@section('bodyClass', 'auth-body')
@section('content')
<main class="auth-layout">
    <section class="auth-promo">
        <a class="auth-brand" href="{{ route('home') }}"><span>▣</span> CamHub</a>
        <div class="promo-copy">
            <h1>Platform Jual Beli,<br>Titip Jual &amp; Sewa Kamera<br>Terlengkap dan Terpercaya.</h1>
        </div>
        <div class="promo-points">
            <span>✓ Transaksi Jual Beli Aman (Escrow)</span>
            <span>✓ Titip Jual &amp; Mitra Terverifikasi KYC</span>
            <span>✓ Sewa Cepat Langsung Tanpa Perlu Akun</span>
        </div>
    </section>

    <section class="auth-forms">
        <div class="auth-form-inner">
            <!-- Info Khusus untuk Penyewa -->
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #16a34a; padding: 10px 14px; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.85rem; color: #166534; line-height: 1.5;">
                <strong>💡 Mau Sewa Kamera?</strong> Anda <strong>tidak perlu login atau mendaftar</strong>. Langsung pilih kamera di <a href="{{ route('products.index') }}" style="color: #15803d; text-decoration: underline; font-weight: 700;">Katalog Sewa</a> dan lengkapi foto jaminan saat checkout.
            </div>

            <p class="eyebrow">PORTAL KHUSUS PEMBELI, TITIP JUAL &amp; ADMIN</p>
            <h1 style="font-size: 1.6rem; font-weight: 800; color: #0f172a; margin-top: 4px;">Masuk ke Akun CamHub</h1>
            <p class="muted" style="margin-bottom: 1rem;">Khusus untuk Pembeli unit, Penjual / Titip Jual, dan Administrator.</p>

            <!-- Akun Demo Cepat untuk Uji Coba -->
            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 10px 12px; margin-bottom: 1.25rem;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">⚡ 1-Klik Isi Akun Demo (Uji Coba):</span>
                <div style="display: flex; gap: 8px; margin-top: 6px; flex-wrap: wrap;">
                    <button type="button" onclick="fillLogin('admin@camhub.com', 'password123')" style="background: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe; border-radius: 4px; padding: 4px 10px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                        👑 Admin
                    </button>
                    <button type="button" onclick="fillLogin('mitra@camhub.com', 'password123')" style="background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; border-radius: 4px; padding: 4px 10px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                        🏪 Mitra (Penjual / Titip Sewa)
                    </button>
                    <button type="button" onclick="fillLogin('pembeli@camhub.com', 'password123')" style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; border-radius: 4px; padding: 4px 10px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                        🛍️ Pembeli
                    </button>
                </div>
            </div>

            @if ($errors->any()) 
                <div class="notice" role="alert" style="margin-bottom: 1rem; background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; padding: 10px 14px; border-radius: 6px; font-size: 0.85rem;">
                    {{ $errors->first() }}
                </div> 
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <label for="login-email">Email</label>
                <input id="login-email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autocomplete="email">

                <label for="login-password">Password</label>
                <input id="login-password" type="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">

                <div class="auth-options">
                    <label class="radio-row">
                        <input type="checkbox" name="remember" value="1"> Ingat saya
                    </label>
                    <a href="#register-name">Lupa password?</a>
                </div>

                <button class="btn" type="submit" style="width: 100%; margin-top: 10px;">Masuk ke Akun</button>
            </form>

            <div class="auth-divider"><span>atau</span></div>
            <p class="register-prompt">Belum punya akun? <a href="#register-name">Daftar Akun Baru</a></p>

            <form class="register-form" method="POST" action="{{ route('register') }}" id="register-name" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 2px dashed #e2e8f0;">
                @csrf
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a;">Daftar Akun Baru</h2>
                <p style="font-size: 0.8rem; color: #64748b; margin: 4px 0 1rem;">Pendaftaran untuk Pembeli Kamera atau Penjual / Mitra Titip Jual.</p>

                <label for="register-fullname">Nama Lengkap</label>
                <input id="register-fullname" type="text" name="name" value="{{ old('name') }}" placeholder="Nama sesuai identitas" required autocomplete="name">

                <label for="register-email">Email</label>
                <input id="register-email" type="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com" required autocomplete="email">

                <label for="register-phone">No. HP / WhatsApp</label>
                <input id="register-phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="081234567890" required autocomplete="tel">

                <label for="register-role">Tujuan Pendaftaran (Jenis Akun)</label>
                <select id="register-role" name="role" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 6px; margin: 4px 0 8px;">
                    <option value="customer" @selected(old('role', 'customer') === 'customer')>Pembeli Kamera (Jual Beli Escrow)</option>
                    <option value="mitra" @selected(old('role') === 'mitra')>Penjual / Titip Jual Kamera (Mitra CamHub)</option>
                </select>
                <p class="muted" style="font-size: 0.75rem; color: #64748b; margin-bottom: 1rem;">Mitra penjual wajib mengunggah verifikasi KTP (KYC) setelah registrasi.</p>

                <label for="register-password">Password (minimal 8 karakter)</label>
                <input id="register-password" type="password" name="password" required minlength="8" autocomplete="new-password">

                <label for="register-confirmation">Ulangi Password</label>
                <input id="register-confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">

                <button class="btn" type="submit" style="width: 100%; margin-top: 12px; background: #0f172a;">Daftar Akun Sekarang</button>
            </form>
        </div>
    </section>
</main>

<script>
function fillLogin(email, pass) {
    document.getElementById('login-email').value = email;
    document.getElementById('login-password').value = pass;
}
</script>
@endsection
