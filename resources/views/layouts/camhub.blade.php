
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CamHub')</title>
    <link rel="stylesheet" href="{{ asset('css/camhub.css') }}">
</head>
<body>
    @yield('navbar')

    @auth
        <div class="account-bar">
            <span>{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('camhub.logout') }}">@csrf<button type="submit">Keluar</button></form>
        </div>
    @endauth

    <main>
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if(session('status'))
            <div class="alert-success">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
