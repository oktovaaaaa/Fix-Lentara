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
            {{-- THEME TOGGLE (REPLACE SEARCH) --}}
            <li class="theme-toggle-item">
                <button type="button" class="theme-toggle-btn" id="themeToggle" aria-label="Ganti tema">
                    <i class="bx bx-moon" id="themeIcon"></i>
                    <span class="links_name" id="themeText">Tema</span>
                </button>
                <span class="tooltip" id="themeTooltip">Tema: Light</span>
            </li>

            <li>
                <a href="{{ route('admin.about_stats.index') }}">
                    <i class='bx bx-info-circle'></i>
                    <span class="links_name">About Pulau + Statistik</span>
                </a>
                <span class="tooltip">About Pulau + Statistik</span>
            </li>

            <li>
                <a href="{{ route('admin.destinations.index') }}">
                    <i class="bx bx-map-alt"></i>
                    <span class="links_name">Destinasi</span>
                </a>
                <span class="tooltip">Destinasi</span>
            </li>

            <li>
                <a href="{{ route('admin.abouts.index') }}">
                    <i class='bx bx-info-circle'></i>
                    <span class="links_name">About Suku</span>
                </a>
                <span class="tooltip">About Suku</span>
            </li>


            {{-- History Pulau & Suku --}}
            <li>
                <a href="{{ route('admin.histories.index') }}">
                    {{-- ikon jam / timeline --}}
                    <i class='bx bx-time-five'></i>
                    <span class="links_name">History Pulau &amp; Suku</span>
                </a>
                <span class="tooltip">History Pulau &amp; Suku</span>
            </li>





            {{-- Makanan --}}

            <li>
                <a href="{{ route('admin.testimonials.index') }}">
                    <i class='bx bx-message-rounded-dots'></i>
                    <span class="links_name">Testimoni</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.testimonial-reports.index') }}">
                    <i class='bx bx-flag'></i>
                    <span class="links_name">Report Testimoni</span>
                </a>
            </li>

            {{-- ===== QUIZ (BARU) ===== --}}
            <li>
                <a href="{{ route('admin.quizzes.index') }}">
                    <i class='bx bx-joystick'></i>
                    <span class="links_name">Quiz</span>
                </a>
                <span class="tooltip">Quiz</span>
            </li>

            <li>
                <a href="{{ route('admin.game-levels.index') }}">
                    <i class='bx bx-joystick'></i>
                    <span class="links_name">Game Levels</span>
                </a>
                <span class="tooltip">Game Levels</span>
            </li>

            {{-- Warisan --}}
            <li>
                <a href="{{ route('admin.heritages.index') }}">
                    <i class="bx bx-landmark"></i>
                    <span class="links_name">Warisan</span>
                </a>
                <span class="tooltip">Warisan</span>
            </li>


            {{-- Profile + Logout --}}
            <li class="profile">
                <div class="profile-details">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}"
                         alt="profileImg" />
                    <div class="name_job">
                        <div class="name">{{ optional(auth()->user())->name ?? 'Admin' }}</div>
                        <div class="job">Administrator</div>
                    </div>
                </div>

                {{-- Form logout: POST ke route('logout') --}}
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            style="all:unset;cursor:pointer;width:100%;display:block;"
                            title="Keluar">
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
