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
@endphp

<header class="site-header" id="top">
    {{-- ===== NAVBAR UTAMA (desktop + trigger mobile) ===== --}}
    <nav class="nav-pill" role="navigation" aria-label="Navigasi utama">

        {{-- Tombol hamburger (mobile) --}}
        <button class="hamburger" id="hamburger"
                aria-label="Buka menu"
                aria-controls="drawer"
                aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        {{-- Brand / Logo --}}
        <a class="brand" href="{{ route('home') }}" data-nav="home">
            <img src="{{ asset('images/icon/icon_lentara.png') }}"
                 alt="Lentara"
                 class="brand-logo">
        </a>

        {{-- ================= DESKTOP NAV ================= --}}
        <div class="nav-links" id="navLinks">

            @if (!$isIslandMode)
                {{-- ================= MODE HOME ================= --}}

                <button class="nav-btn is-active" data-target="#home">
                    <span class="icon">🏠</span><span>Home</span>
                </button>

                {{-- Pulau + dropdown daftar pulau --}}
                <div class="nav-dropdown" data-dropdown="islands">
                    <button class="nav-btn nav-dropdown-toggle"
                            type="button"
                            data-target="#islands"
                            aria-haspopup="true"
                            aria-expanded="false">
                        <span class="icon">🗺️</span>
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
                    <span class="icon">ℹ️</span><span>Tentang</span>
                </button>

                <button class="nav-btn" data-target="#stats">
                    <span class="icon">📊</span><span>Statistik</span>
                </button>

                {{-- ✅ FITUR BARU: Kamera AR (HOME / GENERAL SAJA) --}}
                <button class="nav-btn" data-target="#camera-ar">
                    <span class="icon">📷</span><span>Kamera AR</span>
                </button>

                <button class="nav-btn" data-target="#quiz">
                    <span class="icon">❓</span><span>Kuis</span>
                </button>

                {{-- ================= TESTIMONI (HOME ONLY) ================= --}}
                <button class="nav-btn" data-target="#testimoni">
                    <span class="icon">💬</span><span>Testimoni</span>
                </button>

            @else
                {{-- ================= MODE ISLAND ================= --}}

                {{-- Home: balik ke Budaya Indonesia (landing) --}}
                <button class="nav-btn" data-url="{{ route('home') }}">
                    <span class="icon">🏠</span><span>Home</span>
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
                        <span class="icon">🗺️</span>
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
                    <span class="icon">ℹ️</span><span>Tentang</span>
                </button>

                {{-- Destinasi pulau --}}
                <button class="nav-btn" data-target="#destinations">
                    <span class="icon">🗺️</span><span>Destinasi</span>
                </button>

                {{-- Kuliner khas pulau --}}
                <button class="nav-btn" data-target="#foods">
                    <span class="icon">🍽️</span><span>Kuliner</span>
                </button>

                {{-- Warisan daerah --}}
                <button class="nav-btn" data-target="#warisan">
                    <span class="icon">📜</span><span>Warisan</span>
                </button>

                {{-- Kuis pulau --}}
                <button class="nav-btn" data-target="#quiz">
                    <span class="icon">❓</span><span>Kuis</span>
                </button>
            @endif

            {{-- indikator kapsul aktif (garis/shape bergerak di belakang tombol) --}}
            <span class="active-indicator" aria-hidden="true"></span>
        </div>

        {{-- ================= KANAN: ADMIN + THEME ================= --}}
        <div class="flex items-center gap-2 ml-auto">

            {{-- Link Admin (desktop) --}}
            <a href="{{ route('login') }}"
               class="hidden sm:inline-flex items-center gap-2 px-3 py-2
                      text-sm font-semibold rounded-full
                      border border-amber-300/60
                      bg-amber-400/90 text-slate-900
                      shadow-sm hover:bg-amber-300 transition">
                <span>🛠️</span><span>Admin</span>
            </a>

            {{-- Toggle Tema --}}
            <button class="theme-toggle" id="themeToggle" aria-label="Ubah tema">
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
            </div>
            <button id="closeDrawer"
                    class="close-drawer"
                    aria-label="Tutup menu">✕</button>
        </div>

        <div class="drawer-links">
            @if (!$isIslandMode)
                {{-- MODE HOME --}}
                <a href="#home" data-target="#home" class="drawer-link">🏠 Home</a>
                <a href="#about" data-target="#about" class="drawer-link">ℹ️ Tentang</a>
                <a href="#history" data-target="#history" class="drawer-link">📜 Sejarah</a>
                <a href="#stats" data-target="#stats" class="drawer-link">📊 Statistik</a>

                {{-- Pulau + sub menu --}}
                <a href="#islands" data-target="#islands" class="drawer-link">🗺️ Pulau</a>
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

                {{-- ✅ FITUR BARU: Kamera AR (HOME / GENERAL SAJA) --}}
                <a href="#camera-ar" data-target="#camera-ar" class="drawer-link">📷 Kamera AR</a>

                <a href="#quiz" data-target="#quiz" class="drawer-link">❓ Kuis</a>

                {{-- TESTIMONI (MOBILE HOME) --}}
                <a href="#testimoni" data-target="#testimoni" class="drawer-link">
                    💬 Testimoni
                </a>
            @else
                {{-- MODE ISLAND --}}
                <a href="{{ route('home') }}" class="drawer-link">🏠 Home</a>
                <a href="#about" data-target="#about" class="drawer-link">ℹ️ Tentang</a>
                <a href="#history" data-target="#history" class="drawer-link">📜 Warisan</a>
                <a href="#stats" data-target="#stats" class="drawer-link">📊 Statistik</a>
                <a href="#destinations" data-target="#destinations" class="drawer-link">🗺️ Destinasi</a>
                <a href="#foods" data-target="#foods" class="drawer-link">🍽️ Kuliner</a>

                {{-- ✅ FIX: kasih parent "Pulau" dulu, biar list pulau tidak kelihatan nempel ke Kuis --}}
                <a href="#islands" data-target="#islands" class="drawer-link">🗺️ Pulau</a>
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

                <a href="#quiz" data-target="#quiz" class="drawer-link">❓ Kuis</a>
            @endif
        </div>

        <div class="drawer-footer">
            <button class="btn full" id="drawerTheme">Ganti Tema</button>

            {{-- Link Admin (mobile) --}}
            <a href="{{ route('login') }}" class="btn full mt-2">
                🛠️ Admin
            </a>
        </div>
    </aside>

    {{-- Overlay gelap saat drawer terbuka --}}
    <div id="drawerOverlay" class="drawer-overlay" aria-hidden="true"></div>
    
</header>
