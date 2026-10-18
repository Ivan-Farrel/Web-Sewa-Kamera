@extends('layouts.camhub')

@section('title', 'CamHub — Login & Register')

@section('content')
<div class="auth-layout">
    <section class="auth-promo">
        <a href="{{ route('camhub.home') }}" class="auth-brand">📷 CamHub</a>
        <h2>Sewa, Jual, Beli, Titip<br>Semua Kebutuhan Fotografi<br>Dalam Satu Platform</h2>
        <div class="auth-benefits"><p>✓ Transaksi Aman (Escrow)</p><p>✓ Verifikasi KYC</p><p>✓ Pilihan Kamera & Lensa</p></div>
    </section>
    <section class="auth-form-area">
        <div class="auth-form">
            <div class="auth-tabs">
                <button type="button" class="auth-tab is-active" data-auth-tab="login">Masuk</button>
                <button type="button" class="auth-tab" data-auth-tab="register">Daftar</button>
            </div>
            <div id="login-panel" class="auth-panel">
                <h1>Masuk ke CamHub</h1><p class="muted">Lanjutkan untuk mengakses akun Anda.</p>
                <form method="POST" action="{{ route('camhub.login.store') }}">
                    @csrf
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" placeholder="Masukkan password" required>
                    <label class="remember-label"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
                    <button class="btn btn-block" type="submit">Masuk</button>
                </form>
            </div>
            <div id="register-panel" class="auth-panel" hidden>
                <h1>Buat akun CamHub</h1><p class="muted">Daftar untuk mulai menyewa perlengkapan.</p>
                <form method="POST" action="{{ route('camhub.register') }}">
                    @csrf
                    <label for="name">Nama lengkap</label><input id="name" name="name" value="{{ old('name') }}" required>
                    <label for="register-email">Email</label><input id="register-email" type="email" name="email" value="{{ old('email') }}" required>
                    <label for="register-password">Password (minimal 8 karakter)</label><input id="register-password" type="password" name="password" minlength="8" required>
                    <label for="password_confirmation">Konfirmasi password</label><input id="password_confirmation" type="password" name="password_confirmation" required>
                    <button class="btn btn-block" type="submit">Daftar</button>
                </form>
            </div>
            @if ($errors->any())<div class="form-errors" role="alert">{{ $errors->first() }}</div>@endif
        </div>
    </section>
</div>
<script>
document.querySelectorAll('[data-auth-tab]').forEach((tab) => tab.addEventListener('click', () => {
    document.querySelectorAll('[data-auth-tab]').forEach((item) => item.classList.toggle('is-active', item === tab));
    document.querySelectorAll('.auth-panel').forEach((panel) => panel.hidden = panel.id !== `${tab.dataset.authTab}-panel`);
}));
if (@json($errors->has('name') || $errors->has('password_confirmation'))) document.querySelector('[data-auth-tab="register"]').click();
</script>
@endsection
