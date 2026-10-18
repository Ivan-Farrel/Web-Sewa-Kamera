
@extends('layouts.camhub')

@section('title', 'CamHub — Beranda')

@section('navbar')
<header class="main-navbar">
    <a href="{{ route('camhub.home') }}" class="brand">CamHub</a>

    <nav class="nav-links">
        <a href="{{ route('camhub.home') }}">Beranda</a>
        <a href="{{ route('camhub.katalog') }}">Kategori</a>
        <a href="{{ route('camhub.katalog') }}">Sewa</a>
        <a href="{{ route('camhub.login') }}">Jual Beli</a>
        <a href="{{ route('camhub.login') }}">Titip Jual</a>
    </nav>

    <form action="{{ route('camhub.katalog') }}" method="GET"
          class="nav-search">
        <input name="q" value="{{ request('q') }}"
               placeholder="Cari kamera atau lensa...">
    </form>
</header>
@endsection

@section('content')
<div class="home-container">

    <section class="hero">
        <span class="eyebrow">CAMERA MARKETPLACE</span>

        <h1>Sewa kamera, abadikan cerita.</h1>

        <p>
            Sewa atau beli perlengkapan fotografi dari komunitas terpercaya.
        </p>

        <form action="{{ route('camhub.katalog') }}" method="GET"
              class="hero-search">
            <input name="q" placeholder="Cari kamera, lensa, lighting...">
            <button type="submit" aria-label="Cari">
                Cari
            </button>
        </form>
    </section>

    <section class="section-block">
        <h2>Kamera populer</h2>
        <p class="section-description">
            Pilihan favorit untuk kebutuhan fotografi kamu.
        </p>

        <div class="home-products">
            @foreach([
                ['Sony A7 III', 250000],
                ['Canon EOS R50', 165000],
                ['Fujifilm X-T30', 200000],
                ['Sony Alpha 6400', 170000]
            ] as $product)
                <article class="product-card">
                    <a class="product-image"
                       href="{{ route('camhub.katalog') }}">
                        <span>📷</span>
                    </a>

                    <h3>{{ $product[0] }}</h3>

                    <p class="product-price">
                        Rp {{ number_format($product[1], 0, ',', '.') }}
                        <small>/ hari</small>
                    </p>

                    <span class="product-type">SEWA</span>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection
