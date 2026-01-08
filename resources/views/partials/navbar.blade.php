{{-- resources/views/partials/navbar.blade.php --}}

@php
    use App\Models\Island;

    // Mode island = kalau ada $selectedIsland (halaman pulau Sumatera, Jawa, dll)
    $isIslandMode = isset($selectedIsland) && $selectedIsland;
    $currentIslandName = $isIslandMode ? $selectedIsland->title ?? $selectedIsland->name : null;

    // daftar pulau untuk dropdown navbar (dari DB supaya lengkap)
    $navbarIslands = Island::query()
        ->where('is_active', true)
        ->orderBy('order')
        ->orderBy('name')
        ->get();

    // ===== GAME (PLAYER) =====
    $playerLoggedIn = auth()->guard('player')->check();
    $gameUrl = $playerLoggedIn ? route('game.learn') : route('player.login');
    $gameLabel = $playerLoggedIn ? 'Game' : 'Game';
@endphp

<header class="site-header" id="top">
    {{-- ===== ICON LINGKARAN GLASS HANYA UNTUK MOBILE ===== --}}
    <div class="circle-logo-container mobile-only" id="circleLogoContainer">
        {{-- ✅ FIX: jangan href ke home, biar tidak balik ke home ketika JS salah deteksi mobile --}}
        <a class="circle-logo"
           href="#"
           data-home-url="{{ route('home') }}"
           id="mobileMenuToggle"
           aria-label="Buka menu">
            <img src="{{ asset('images/icon/icon_lentara.png') }}"
                 alt="Lentara"
                 class="circle-logo-img">
        </a>
    </div>

    {{-- ===== NAVBAR UTAMA (desktop + trigger mobile) ===== --}}
    <nav class="nav-pill" role="navigation" aria-label="Navigasi utama">

        {{-- Brand / Logo - NORMAL UNTUK DESKTOP --}}
        <a class="brand desktop-only" href="{{ route('home') }}" data-nav="home">
            <img src="{{ asset('images/icon/icon_lentara.png') }}"
                 alt="Lentara"
                 class="brand-logo">
        </a>

        {{-- ================= DESKTOP NAV ================= --}}
        <div class="nav-links" id="navLinks">

            @if (!$isIslandMode)
                {{-- ================= MODE HOME ================= --}}

                <button class="nav-btn is-active" data-target="#home">
                    <span>Beranda</span>
                </button>

                {{-- Pulau + dropdown daftar pulau --}}
                <div class="nav-dropdown" data-dropdown="islands">
                    <button class="nav-btn nav-dropdown-toggle"
                            type="button"
                            data-target="#islands"
                            aria-haspopup="true"
                            aria-expanded="false">
                        <span class="dropdown-label">Pulau</span>
                        <span class="chevron">▾</span>
                    </button>

                    <div class="nav-dropdown-menu" role="menu">
                        @foreach ($navbarIslands as $island)
                            @php
                                $url = route('islands.show', $island->slug);
                                $label = $island->subtitle ?: $island->name;
                            @endphp
                            <a href="{{ $url }}"
                               class="dropdown-item"
                               role="menuitem"
                               data-island="{{ $label }}"
                               data-url="{{ $url }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <button class="nav-btn" data-target="#about">
                    <span>Tentang</span>
                </button>

                <button class="nav-btn" data-target="#stats">
                    <span>Statistik</span>
                </button>

                {{-- ✅ FITUR BARU: Kamera AR (HOME / GENERAL SAJA) --}}
                <button class="nav-btn" data-target="#camera-ar">
                    <span>Kamera AR</span>
                </button>

                <button class="nav-btn" data-target="#quiz">
                    <span>Kuis</span>
                </button>

                {{-- ===== GAME / BELAJAR (SETELAH KUIS, SEBELUM TESTIMONI) ===== --}}
                <button class="nav-btn" data-url="{{ $gameUrl }}">
                    <span>{{ $gameLabel }}</span>
                </button>

                {{-- ================= TESTIMONI (HOME ONLY) ================= --}}
                <button class="nav-btn" data-target="#testimoni">
                    <span>Testimoni</span>
                </button>

            @else
                {{-- ================= MODE ISLAND ================= --}}

                {{-- Home: balik ke Budaya Indonesia (landing) --}}
                <button class="nav-btn" data-url="{{ route('home') }}">
                    <span>Beranda</span>
                </button>

                {{-- Dropdown Pulau --}}
                <div class="nav-dropdown"
                     data-dropdown="islands"
                     @if ($currentIslandName)
                         data-current-island="{{ $currentIslandName }}"
                     @endif>
                    <button class="nav-btn nav-dropdown-toggle"
                            type="button"
                            aria-haspopup="true"
                            aria-expanded="false">
                        <span class="dropdown-label">
                            {{ $currentIslandName ?? 'Pulau' }}
                        </span>
                        <span class="chevron">▾</span>
                    </button>

                    <div class="nav-dropdown-menu" role="menu">
                        @foreach ($navbarIslands as $island)
                            @php
                                $url = route('islands.show', $island->slug);
                                $label = $island->subtitle ?: $island->name;
                            @endphp
                            <a href="{{ $url }}"
                               class="dropdown-item"
                               role="menuitem"
                               data-island="{{ $label }}"
                               data-url="{{ $url }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- default aktif: Tentang pulau --}}
                <button class="nav-btn is-active" data-target="#about">
                    <span>Tentang</span>
                </button>

                {{-- Destinasi pulau --}}
                <button class="nav-btn" data-target="#destinations">
                    <span>Destinasi</span>
                </button>

                {{-- Kuliner khas pulau --}}
                <button class="nav-btn" data-target="#foods">
                    <span>Kuliner</span>
                </button>

                {{-- Warisan daerah --}}
                <button class="nav-btn" data-target="#warisan">
                    <span>Warisan</span>
                </button>

                {{-- Kuis pulau --}}
                <button class="nav-btn" data-target="#quiz">
                    <span>Kuis</span>
                </button>

                {{-- ===== GAME / BELAJAR (SETELAH KUIS) ===== --}}
                <button class="nav-btn" data-url="{{ $gameUrl }}">
                    <span>{{ $gameLabel }}</span>
                </button>
            @endif

            {{-- indikator kapsul aktif (garis/shape bergerak di belakang tombol) --}}
            <span class="active-indicator" aria-hidden="true"></span>
        </div>

        {{-- ================= KANAN: ADMIN + THEME ================= --}}
        <div class="flex items-center gap-2 ml-auto">
            {{-- Toggle Tema (DESKTOP ONLY) --}}
            <button class="theme-toggle hidden md:flex" id="themeToggle" aria-label="Ubah tema">
                <span class="sun">☀️</span>
                <span class="moon">🌙</span>
            </button>
        </div>
    </nav>

    {{-- ================= MOBILE DRAWER ================= --}}
    <aside class="drawer" id="drawer" aria-hidden="true">

        <div class="drawer-header">
            <div class="drawer-brand">
                <img src="{{ asset('images/icon/icon_lentara.png') }}"
                     alt="Lentara"
                     class="drawer-logo">
                <span class="drawer-title">Menu</span>
            </div>
            <button id="closeDrawer"
                    class="close-drawer"
                    aria-label="Tutup menu">✕</button>
        </div>

        <div class="drawer-links">
            @if (!$isIslandMode)
                {{-- MODE HOME --}}
                <a href="#home" data-target="#home" class="drawer-link">Beranda</a>
                <a href="#about" data-target="#about" class="drawer-link">Tentang</a>
                <a href="#history" data-target="#history" class="drawer-link">Sejarah</a>
                <a href="#stats" data-target="#stats" class="drawer-link">Statistik</a>

                {{-- Pulau + sub menu --}}
                <a href="#islands" data-target="#islands" class="drawer-link">Pulau</a>
                <div class="drawer-subgroup">
                    @foreach ($navbarIslands as $island)
                        @php
                            $url = route('islands.show', $island->slug);
                            $label = $island->subtitle ?: $island->name;
                        @endphp
                        <a href="{{ $url }}"
                           class="drawer-link drawer-sublink"
                           data-url="{{ $url }}"
                           data-island="{{ $label }}">
                            • {{ $label }}
                        </a>
                    @endforeach
                </div>

                {{-- Kamera AR --}}
                <a href="#camera-ar" data-target="#camera-ar" class="drawer-link">Kamera AR</a>

                <a href="#quiz" data-target="#quiz" class="drawer-link">Kuis</a>

                {{-- ===== GAME / BELAJAR (SETELAH KUIS, SEBELUM TESTIMONI) ===== --}}
                <a href="{{ $gameUrl }}" class="drawer-link">{{ $gameLabel }}</a>

                {{-- TESTIMONI --}}
                <a href="#testimoni" data-target="#testimoni" class="drawer-link">Testimoni</a>
            @else
                {{-- MODE ISLAND --}}
                <a href="{{ route('home') }}" class="drawer-link">Beranda</a>
                <a href="#about" data-target="#about" class="drawer-link">Tentang</a>
                <a href="#history" data-target="#history" class="drawer-link">Warisan</a>
                <a href="#stats" data-target="#stats" class="drawer-link">Statistik</a>
                <a href="#destinations" data-target="#destinations" class="drawer-link">Destinasi</a>
                <a href="#foods" data-target="#foods" class="drawer-link">Kuliner</a>

                {{-- Pulau --}}
                <a href="#islands" data-target="#islands" class="drawer-link">Pulau</a>
                <div class="drawer-subgroup">
                    @foreach ($navbarIslands as $island)
                        @php
                            $url = route('islands.show', $island->slug);
                            $label = $island->subtitle ?: $island->name;
                        @endphp
                        <a href="{{ $url }}"
                           class="drawer-link drawer-sublink"
                           data-url="{{ $url }}"
                           data-island="{{ $label }}">
                            • {{ $label }}
                        </a>
                    @endforeach
                </div>

                <a href="#quiz" data-target="#quiz" class="drawer-link">Kuis</a>

                {{-- ===== GAME / BELAJAR (SETELAH KUIS) ===== --}}
                <a href="{{ $gameUrl }}" class="drawer-link">{{ $gameLabel }}</a>
            @endif
        </div>

        <div class="drawer-footer">
            {{-- Toggle Tema (MOBILE) --}}
            <button class="btn full" id="drawerTheme">
                <span class="drawer-theme-icon">🌓</span>
                <span>Ganti Tema</span>
            </button>
        </div>
    </aside>

    {{-- Overlay gelap saat drawer terbuka --}}
    <div id="drawerOverlay" class="drawer-overlay" aria-hidden="true"></div>

</header>
