@extends('layouts.app')
@section('title', 'Masuk atau Daftar - CamHub')
@section('bodyClass', 'auth-body')
@section('content')
<main class="auth-layout">
    <section class="auth-promo"><a class="auth-brand" href="{{ route('home') }}"><span>▣</span> CamHub</a><div class="promo-copy"><h1>Sewa, jual, beli, titip<br>semua kebutuhan fotografi<br>dalam satu platform.</h1></div><div class="promo-points"><span>✓ Transaksi aman (Escrow)</span><span>✓ Verifikasi KYC</span><span>✓ Pilihan lengkap kamera &amp; lensa</span></div></section>
    <section class="auth-forms"><div class="auth-form-inner"><p class="eyebrow">Selamat datang kembali</p><h1>Masuk ke CamHub</h1><p class="muted">Lanjutkan untuk mengakses akun Anda.</p>
        @if ($errors->any()) <div class="notice" role="alert">{{ $errors->first() }}</div> @endif
        <form method="POST" action="{{ route('login') }}">@csrf<label for="login-email">Email atau No. HP</label><input id="login-email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autocomplete="email"><label for="login-password">Password</label><input id="login-password" type="password" name="password" placeholder="Masukkan password" required autocomplete="current-password"><div class="auth-options"><label class="radio-row"><input type="checkbox" name="remember" value="1"> Ingat saya</label><a href="#register-name">Lupa password?</a></div><button class="btn" type="submit">Masuk</button></form>
        <div class="auth-divider"><span>atau</span></div><button class="social-button" type="button"><span class="google-mark">G</span> Lanjut dengan Google</button><p class="register-prompt">Belum punya akun? <a href="#register-name">Daftar sekarang</a></p>
        <form class="register-form" method="POST" action="{{ route('register') }}" id="register-name">@csrf<h2>Buat akun baru</h2><label for="register-fullname">Nama lengkap</label><input id="register-fullname" type="text" name="name" value="{{ old('name') }}" required autocomplete="name"><label for="register-email">Email</label><input id="register-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"><label for="register-password">Password (minimal 8 karakter)</label><input id="register-password" type="password" name="password" required minlength="8" autocomplete="new-password"><label for="register-confirmation">Ulangi password</label><input id="register-confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"><button class="btn" type="submit">Daftar</button></form>
    </div></section>
</main>
@endsection
