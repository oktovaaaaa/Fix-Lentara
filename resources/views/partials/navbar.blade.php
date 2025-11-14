{{-- resources/views/partials/navbar.blade.php --}}

@php
    // Mode island = kalau ada $selectedIsland (halaman pulau Sumatera, Jawa, dll)
    $isIslandMode       = isset($selectedIsland) && $selectedIsland;
    $currentIslandName  = $isIslandMode ? ($selectedIsland->title ?? $selectedIsland->name) : null;

    // daftar pulau untuk dropdown navbar
    $dropdownIslands = [
        ['name' => 'Sumatera',   'slug' => 'sumatera'],
        ['name' => 'Jawa',       'slug' => 'jawa'],
        ['name' => 'Kalimantan', 'slug' => 'kalimantan'],
        ['name' => 'Sulawesi',   'slug' => 'sulawesi'],
        ['name' => 'Papua',      'slug' => 'papua'],
    ];
@endphp

<header class="site-header" id="top">
    {{-- ===== NAVBAR UTAMA (desktop + trigger mobile) ===== --}}
    <nav class="nav-pill" role="navigation" aria-label="Navigasi utama">
        {{-- Tombol hamburger (mobile) --}}
        <button class="hamburger" id="hamburger" aria-label="Buka menu" aria-controls="drawer" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        {{-- Brand / Logo --}}
        <a class="brand" href="#home" data-nav="home">
            <span class="brand-icon" aria-hidden="true"></span>
            <span class="brand-text">Lentara</span>
        </a>

        {{-- Link navbar (desktop) --}}
        <div class="nav-links" id="navLinks">
            @if(!$isIslandMode)
                {{-- MODE HOME: Budaya Indonesia --}}
                <button class="nav-btn is-active" data-target="#home">
                    <span class="icon">🏠</span><span>Home</span>
                </button>

                <button class="nav-btn" data-target="#about">
                    <span class="icon">ℹ️</span><span>Tentang</span>
                </button>

                <button class="nav-btn" data-target="#history">
                    <span class="icon">📜</span><span>Sejarah</span>
                </button>

                <button class="nav-btn" data-target="#stats">
                    <span class="icon">📊</span><span>Statistik</span>
                </button>

                {{-- Pulau + dropdown daftar pulau (Sumatera, Jawa, dll) --}}
                <div
                    class="nav-dropdown"
                    data-dropdown="islands"
                    @if($currentIslandName)
                        data-current-island="{{ $currentIslandName }}"
                    @endif
                >
                    <button
                        class="nav-btn nav-dropdown-toggle"
                        type="button"
                        data-target="#islands"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <span class="icon">🗺️</span>
                        <span class="dropdown-label">
                            Pulau
                        </span>
                        <span class="chevron">▾</span>
                    </button>

                    <div class="nav-dropdown-menu" role="menu">
                        @foreach($dropdownIslands as $island)
                            @php
                                $url = url('/islands/'.$island['slug']);
                            @endphp
                            <a href="{{ $url }}"
                               class="dropdown-item"
                               role="menuitem"
                               data-island="{{ $island['name'] }}"
                               data-url="{{ $url }}">
                                {{ $island['name'] }}
                                {{-- contoh kalau mau pakai route name:
                                     href="{{ route('islands.show', $island['slug']) }}" --}}
                            </a>
                        @endforeach
                    </div>
                </div>

                <button class="nav-btn" data-target="#quiz">
                    <span class="icon">❓</span><span>Kuis</span>
                </button>
            @else
                {{-- MODE ISLAND: Halaman Pulau (Sumatera, Jawa, dll) --}}

                {{-- Home: balik ke Budaya Indonesia (landing) --}}
                <button class="nav-btn" data-url="{{ route('home') }}">
                    <span class="icon">🏠</span><span>Home</span>
                </button>

                {{-- default aktif: Tentang pulau --}}
                <button class="nav-btn is-active" data-target="#about">
                    <span class="icon">ℹ️</span><span>Tentang</span>
                </button>

                {{-- Cerita daerah --}}
                <button class="nav-btn" data-target="#history">
                    <span class="icon">📜</span><span>Cerita</span>
                </button>

                {{-- Statistik pulau
                <button class="nav-btn" data-target="#stats">
                    <span class="icon">📊</span><span>Statistik</span>
                </button> --}}

                {{-- DROPDOWN PULAU JUGA TAMPIL DI MODE ISLAND --}}
                <div
                    class="nav-dropdown"
                    data-dropdown="islands"
                    @if($currentIslandName)
                        data-current-island="{{ $currentIslandName }}"
                    @endif
                >
                    <button
                        class="nav-btn nav-dropdown-toggle"
                        type="button"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <span class="icon">🗺️</span>
                        <span class="dropdown-label">
                            {{ $currentIslandName ?? 'Pulau' }}
                        </span>
                        <span class="chevron">▾</span>
                    </button>

                    <div class="nav-dropdown-menu" role="menu">
                        @foreach($dropdownIslands as $island)
                            @php
                                $url = url('/islands/'.$island['slug']);
                            @endphp
                            <a href="{{ $url }}"
                               class="dropdown-item"
                               role="menuitem"
                               data-island="{{ $island['name'] }}"
                               data-url="{{ $url }}">
                                {{ $island['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Destinasi pulau --}}
                <button class="nav-btn" data-target="#destinations">
                    <span class="icon">🗺️</span><span>Destinasi</span>
                </button>

                {{-- Makanan khas pulau --}}
                <button class="nav-btn" data-target="#foods">
                    <span class="icon">🍽️</span><span>Makanan</span>
                </button>

                {{-- Kuis pulau --}}
                <button class="nav-btn" data-target="#quiz">
                    <span class="icon">❓</span><span>Kuis</span>
                </button>
            @endif

            {{-- indikator kapsul aktif (garis/shape bergerak di belakang tombol) --}}
            <span class="active-indicator" aria-hidden="true"></span>
        </div>

        {{-- Kanan: tombol Admin + toggle tema --}}
        <div class="flex items-center gap-2 ml-auto">
            {{-- Link Admin (desktop) --}}
            <a href="{{ route('login') }}"
               class="hidden sm:inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold rounded-full border border-amber-300/60 bg-amber-400/90 text-slate-900 shadow-sm hover:bg-amber-300 transition">
                <span>🛠️</span><span>Admin</span>
            </a>

            {{-- Tombol ganti tema (light/dark) --}}
            <button class="theme-toggle" id="themeToggle" aria-label="Ubah tema">
                <span class="sun">☀️</span>
                <span class="moon">🌙</span>
            </button>
        </div>
    </nav>

    {{-- ===== DRAWER / SIDEBAR MOBILE ===== --}}
    <aside class="drawer" id="drawer" aria-hidden="true">
        <div class="drawer-header">
            <div class="drawer-brand">Lentara</div>
            {{-- Tombol X untuk menutup drawer --}}
            <button id="closeDrawer" class="close-drawer" aria-label="Tutup menu">✕</button>
        </div>

        <div class="drawer-links">
            @if(!$isIslandMode)
                {{-- MODE HOME: Budaya Indonesia --}}
                <a href="#home"    data-target="#home"    class="drawer-link">🏠 Home</a>
                <a href="#about"   data-target="#about"   class="drawer-link">ℹ️ Tentang</a>
                <a href="#history" data-target="#history" class="drawer-link">📜 Sejarah</a>
                <a href="#stats"   data-target="#stats"   class="drawer-link">📊 Statistik</a>

                {{-- Pulau + sub menu pulau-pulau (mobile) --}}
                <a href="#islands" data-target="#islands" class="drawer-link">🗺️ Pulau</a>
                <div class="drawer-subgroup">
                    @foreach($dropdownIslands as $island)
                        @php
                            $url = url('/islands/'.$island['slug']);
                        @endphp
                        <a href="{{ $url }}"
                           class="drawer-link drawer-sublink"
                           data-url="{{ $url }}"
                           data-island="{{ $island['name'] }}">
                            • {{ $island['name'] }}
                        </a>
                    @endforeach
                </div>

                <a href="#quiz"    data-target="#quiz"    class="drawer-link">❓ Kuis</a>
            @else
                {{-- MODE ISLAND: Pulau --}}
                <a href="{{ route('home') }}" class="drawer-link">🏠 Home</a>
                <a href="#about"   data-target="#about"   class="drawer-link">ℹ️ Tentang</a>
                <a href="#history" data-target="#history" class="drawer-link">📜 Cerita</a>
                <a href="#stats"   data-target="#stats"   class="drawer-link">📊 Statistik</a>
                <a href="#destinations" data-target="#destinations" class="drawer-link">🗺️ Destinasi</a>
                <a href="#foods" data-target="#foods" class="drawer-link">🍽️ Makanan</a>
                <a href="#quiz" data-target="#quiz" class="drawer-link">❓ Kuis</a>

                {{-- Submenu pulau di bawah juga boleh ditambah kalau mau konsisten --}}
                <div class="drawer-subgroup">
                    @foreach($dropdownIslands as $island)
                        @php
                            $url = url('/islands/'.$island['slug']);
                        @endphp
                        <a href="{{ $url }}"
                           class="drawer-link drawer-sublink"
                           data-url="{{ $url }}"
                           data-island="{{ $island['name'] }}">
                            • {{ $island['name'] }}
                        </a>
                    @endforeach
                </div>
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
