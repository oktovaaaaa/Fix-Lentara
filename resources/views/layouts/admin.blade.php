{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <title>@yield('title', 'Admin Panel')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- CSS sidebar admin --}}
    <link rel="stylesheet" href="{{ asset('css/admin-sidebar.css') }}">

    {{-- Boxicons CDN --}}
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>

{{-- tambahkan class "open" kalau mau default terbuka --}}
<div class="sidebar open">
    <div class="logo-details">
        <i class="bx bxs-flag-alt icon"></i>
        <div class="logo_name">Budaya Nusantara</div>
        <i class="bx bx-menu" id="btn"></i>
    </div>

    <ul class="nav-list">
        {{-- Search --}}
        <li>
            <i class="bx bx-search"></i>
            <input type="text" placeholder="Search..." />
            <span class="tooltip">Search</span>
        </li>

        {{-- Dashboard --}}
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <i class="bx bx-grid-alt"></i>
                <span class="links_name">Dashboard</span>
            </a>
            <span class="tooltip">Dashboard</span>
        </li>

        {{-- Statistik Pulau --}}
        <li>
            <a href="{{ route('admin.stats.index') }}">
                <i class='bx bx-pie-chart-alt-2'></i>
                <span class="links_name">Statistik</span>
            </a>
            <span class="tooltip">Statistik</span>
        </li>

        {{-- Destinasi --}}
        <li>
            <a href="#">
                {{-- nanti ganti ke route('admin.destinations.index') --}}
                <i class="bx bx-map-alt"></i>
                <span class="links_name">Destinasi</span>
            </a>
            <span class="tooltip">Destinasi</span>
        </li>

        {{-- Makanan --}}
        <li>
            <a href="#">
                {{-- nanti ganti ke route('admin.foods.index') --}}
                <i class="bx bx-bowl-hot"></i>
                <span class="links_name">Makanan</span>
            </a>
            <span class="tooltip">Makanan</span>
        </li>

        {{-- Pengaturan --}}
        <li>
            <a href="#">
                <i class="bx bx-cog"></i>
                <span class="links_name">Pengaturan</span>
            </a>
            <span class="tooltip">Pengaturan</span>
        </li>

                {{-- Profile + Logout --}}
        <li class="profile">
            <div class="profile-details">
                <img
                    src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}"
                    alt="profileImg"
                />
                <div class="name_job">
                    <div class="name">{{ optional(auth()->user())->name ?? 'Admin' }}</div>
                    <div class="job">Administrator</div>
                </div>
            </div>

            {{-- Form logout: POST ke route('logout') --}}
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button
                    type="submit"
                    style="all:unset;cursor:pointer;width:100%;display:block;"
                    title="Keluar"
                >
                    <i class="bx bx-log-out" id="log_out"></i>
                </button>
            </form>
        </li>

    </ul>
</div>

<section class="home-section">
    <div class="text">
        @yield('page-title', 'Dashboard')
    </div>

    <div style="padding: 0 20px 40px;">
        @yield('content')
    </div>
</section>

{{-- JS sidebar --}}
<script src="{{ asset('js/admin-sidebar.js') }}"></script>

@stack('scripts')
</body>
</html>
