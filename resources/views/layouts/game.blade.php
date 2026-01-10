{{-- resources/views/layouts/game.blade.php --}}

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Belajar') — Lentara Nusantara</title>
<link rel="icon" type="image/png" href="{{ asset('images/icon/icon_lentara.png') }}">
<link rel="shortcut icon" type="image/png" href="{{ asset('images/icon/icon_lentara.png') }}">

    {{-- ✅ PAKAI THEME YANG SAMA DENGAN HOME --}}
    <script>
        (function () {
            const saved = localStorage.getItem('piforrr-theme') || 'light';
            document.documentElement.setAttribute('data-theme', saved);
        })();
    </script>

    {{-- Kalau game kamu butuh feel/theme variable yang sama dengan home --}}
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

    {{-- style khusus halaman --}}
    @stack('styles')
</head>
<body class="antialiased">

    {{-- KONTEN GAME --}}
    @yield('content')

    {{-- scripts khusus halaman --}}
    @stack('scripts')

    {{-- OPTIONAL: kalau kamu mau game ikut update saat theme toggle di tab lain --}}
    <script>
      window.addEventListener('storage', function(e){
        if(e.key === 'piforrr-theme'){
          document.documentElement.setAttribute('data-theme', e.newValue || 'light');
        }
      });
    </script>

</body>
</html>
