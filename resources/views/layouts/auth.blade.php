{{-- resources/views/layouts/auth.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- penting buat fetch POST --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Auth')</title>

    {{-- SET THEME PALING AWAL (default: light) --}}
    <script>
        (function () {
            const saved = localStorage.getItem('piforrr-theme') || 'light';
            document.documentElement.setAttribute('data-theme', saved);
        })();
    </script>

    {{-- Tailwind via CDN (tanpa Vite) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- OPTIONAL: kalau kamu butuh warna theme dari navbar.css (misal variable --bg-body, --txt-body, dst) --}}
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

    @stack('head')
</head>

<body class="antialiased min-h-screen bg-[var(--bg-body)] text-[var(--txt-body)]">
    @yield('content')

    @stack('scripts')
</body>
</html>
