<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CamHub')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="@yield('bodyClass')">
<header class="navbar">
    <a href="{{ route('home') }}" class="logo"><span class="logo-icon">◉</span> CamHub</a>
    <form class="nav-search" action="{{ route('products.index') }}" method="GET">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari kamera, lensa, aksesoris...">
        <button type="submit">Cari</button>
    </form>
    <nav class="nav-links">
        <a href="{{ route('home') }}">Beranda</a>
        <a href="{{ route('products.index') }}">Katalog</a>
        @auth <a href="{{ route('camhub.dashboard-pelanggan') }}">Pesanan saya</a> @else <a href="{{ route('camhub.login') }}">Sewa</a> @endauth
    </nav>
    <div class="nav-actions">
        @auth
            <a href="{{ route('camhub.dashboard-pelanggan') }}">{{ auth()->user()->name }}</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-small" type="submit">Keluar</button></form>
        @else
            <a href="{{ route('camhub.login') }}">Masuk</a>
            <a href="{{ route('camhub.login') }}#register-name" class="btn btn-small">Daftar</a>
        @endauth
    </div>
</header>
@if(session('success')) <div class="notice" role="status">{{ session('success') }}</div> @endif
@if ($errors->any()) <div class="notice" role="alert">{{ $errors->first() }}</div> @endif
@yield('content')
<script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
