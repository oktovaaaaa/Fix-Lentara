{{-- resources/views/landing.blade.php --}}
@extends('layouts.app')

@section('title', 'Lentara Islands')

@php
    $hasSelected    = isset($selectedIsland) && $selectedIsland;
    $featuresByType = $featuresByType ?? [];
@endphp

@section('content')
    {{-- CSS khusus landing (sementara di sini, nanti bisa dipindah ke file CSS sendiri) --}}
    <style>
        /* Scope ke landing saja, supaya tidak mengganggu layout lain */
        .landing-root * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .landing-root {
            font-family: "Inter", sans-serif;
        }

        .card {
            position: absolute;
            background-size: cover;
            background-position: center; /* default (desktop / tablet) */
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            color: #ffffffdd;
            cursor: pointer;
        }

        /* HP & layar kecil: geser gambar ke kanan (rata kanan) */
        @media (max-width: 768px) {
            .card {
                background-position: right center;
            }
        }

        .indicator {
            position: absolute;
            bottom: 80px;
            left: 0;
            width: 100%;
            height: 4px;
            background: #ecad29;
            transform: translateX(-100%);
        }

        .cover {
            position: absolute;
            top: 0;
            left: 0;
            width: 200%;
            height: 100%;
            background: #1a1a1a;
            z-index: 99;
        }

        .content-title-1,
        .content-title-2 {
            font-family: "Oswald", sans-serif;
        }

        /* ===== POSITIONING JUDUL BESAR (TIDAK GERAK) ===== */
        .hero-title-wrapper {
            position: absolute;
            inset-inline: 0;       /* left: 0 + right: 0 */
            z-index: 40;           /* di bawah navbar (navbar z-index 60) */
            pointer-events: none;  /* klik tetap ke kartu / konten di belakang */
            display: flex;
        }

        /* HP & layar kecil: judul di tengah (sedikit di atas) */
        @media (max-width: 768px) {
            .hero-title-wrapper {
                position: absolute;
                top: 28%;                      /* sekitar tengah */
                transform: translateY(-50%);   /* benar-benar center vertical */
                justify-content: center;
                padding-inline: 1rem;
                text-align: center;
            }
        }

        /* Tablet / Desktop: judul di kiri, di sekitar tengah layar */
        @media (min-width: 769px) {
            .hero-title-wrapper {
                position: absolute;
                top: 50%;
                transform: translateY(-45%);   /* sedikit di atas tengah */
                justify-content: flex-start;
                padding-inline: 3rem;
                text-align: left;
            }
        }
    </style>

    {{-- =================== SECTION 1: HERO FULLSCREEN =================== --}}
    <section
        id="home"
        class="landing-root landing-hero relative h-screen w-full overflow-hidden bg-[#1a1a1a] text-white {{ $hasSelected ? '' : 'overflow-hidden' }}"
    >
        <div class="cover"></div>

        {{-- JUDUL BESAR (tetap, tidak ikut scroll) --}}
        @if($hasSelected)
            {{-- Judul besar untuk HALAMAN PULAU --}}
            <div class="hero-title-wrapper">
                <div class="max-w-3xl space-y-2">
                    @if($selectedIsland->place_label)
                        <p class="hero-title-line text-[9px] sm:text-[10px] md:text-xs tracking-[0.3em] uppercase text-white/80">
                            {{ $selectedIsland->place_label }}
                        </p>
                    @endif

                    <h1
                        class="hero-title-line content-title-1
                               text-3xl sm:text-4xl md:text-6xl lg:text-7xl
                               font-bold leading-tight sm:leading-none drop-shadow-[0_0_20px_rgba(0,0,0,0.7)]">
                        {{ strtoupper($selectedIsland->title ?? $selectedIsland->name) }}
                    </h1>

                    @if($selectedIsland->subtitle)
                        <p
                            class="hero-title-line content-title-2
                                   text-lg sm:text-2xl md:text-3xl lg:text-4xl
                                   font-semibold text-white drop-shadow-[0_0_20px_rgba(0,0,0,0.7)]">
                            {{ strtoupper($selectedIsland->subtitle) }}
                        </p>
                    @endif
                </div>
            </div>
        @else
            {{-- Judul besar untuk HOME: Budaya Indonesia --}}
            <div class="hero-title-wrapper">
                <div class="max-w-3xl space-y-2 text-center md:text-left">
                    <p class="hero-title-line text-[9px] sm:text-[10px] md:text-xs tracking-[0.3em] uppercase text-white/80">
                        Nusantara • Pulau • Cerita • Tradisi
                    </p>
                    <h1
                        class="hero-title-line content-title-1
                               text-3xl sm:text-4xl md:text-6xl lg:text-7xl
                               font-bold leading-tight sm:leading-none drop-shadow-[0_0_20px_rgba(0,0,0,0.7)]">
                        BUDAYA INDONESIA
                    </h1>
                    <p
                        class="hero-title-line content-title-2
                               text-base sm:text-xl md:text-2xl lg:text-3xl
                               font-semibold text-white/90 drop-shadow-[0_0_20px_rgba(0,0,0,0.7)]">
                        Jelajahi pulau, cerita daerah, dan tradisi Nusantara.
                    </p>
                </div>
            </div>
        @endif

        {{-- AREA ANIMASI KARTU (isi seluruh hero) --}}
        <div id="demo" class="absolute inset-0 overflow-hidden"></div>

        {{-- PAGINATION & NOMOR SLIDE --}}
        <div
            id="pagination"
            class="absolute bottom-16 sm:bottom-12 left-1/2 -translate-x-1/2 flex gap-2 text-white text-xs sm:text-sm"
        >
            <div class="progress-sub-foreground"></div>
        </div>

        <div
            id="slide-numbers"
            class="absolute bottom-8 sm:bottom-6 left-1/2 -translate-x-1/2 flex gap-2 text-[14px] sm:text-[18px]"
        ></div>

        <div class="indicator"></div>
    </section>

    {{-- =================== SECTION 2A: HOME CONTENT (Budaya Indonesia) =================== --}}
    @if(!$hasSelected)
        <section class="relative z-[10] bg-[#050505] text-white py-12 sm:py-16 px-4 sm:px-6">
            <div class="max-w-5xl mx-auto space-y-12">

                {{-- ABOUT INDONESIA --}}
                <section id="about">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                        Tentang Budaya Indonesia
                    </h2>
                    <p class="text-sm sm:text-base text-white/80 leading-relaxed">
                        Indonesia adalah negara kepulauan dengan ratusan suku, bahasa, dan tradisi. Halaman ini
                        mengajakmu menjelajahi keragaman budaya dari Sabang sampai Merauke melalui pulau-pulau utama.
                    </p>
                </section>

                {{-- HISTORY INDONESIA --}}
                <section id="history">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                        Sejarah Singkat Nusantara
                    </h2>
                    <p class="text-sm sm:text-base text-white/80 leading-relaxed">
                        Dari kerajaan-kerajaan kuno hingga masa modern, Nusantara tumbuh sebagai titik temu budaya,
                        perdagangan, dan kepercayaan. Kamu bisa memperkaya konten ini dari database nanti.
                    </p>
                </section>

                {{-- STATISTIK INDONESIA (SIMPLE IMAGE DULU) --}}
                <section id="stats">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                        Statistik Budaya
                    </h2>
                    <img
                        src="https://www.finereport.com/en/wp-content/uploads/2020/03/2019071010A.png"
                        alt="Contoh grafik statistik"
                        class="max-w-3xl w-full rounded-xl border border-white/10 shadow-lg"
                    >
                    <p class="text-sm sm:text-base text-white/80 leading-relaxed mt-3">
                        Placeholder statistik: jumlah pulau, jumlah suku, bahasa daerah, dan lain-lain. Nantinya bisa
                        dihubungkan ke data dinamis jika diperlukan.
                    </p>
                </section>

                {{-- ⚠️ BAGIAN PULAU (ISLANDS) DIHAPUS SESUAI PERMINTAAN --}}

                {{-- QUIZ INDONESIA --}}
                <section id="quiz">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                        Kuis Budaya Indonesia
                    </h2>
                    <p class="text-sm sm:text-base text-white/80">
                        Di sini nanti bisa jadi area kuis umum tentang Nusantara — misalnya pilihan ganda tentang
                        pulau, suku, atau rumah adat.
                    </p>
                </section>
            </div>
        </section>
    @endif

    {{-- =================== SECTION 2B: DETAIL PULAU (ABOUT / HISTORY / DESTINASI / DLL) =================== --}}
    @if($hasSelected)
        <section class="relative z-[10] bg-[#050505] text-white py-12 sm:py-16 px-4 sm:px-6">
            <div class="max-w-5xl mx-auto space-y-12">

                {{-- ABOUT PULAU --}}
                <section id="about">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                        Tentang {{ $selectedIsland->title ?? $selectedIsland->name }}
                    </h2>
                    <p class="text-sm sm:text-base text-white/80 leading-relaxed">
                        {{ $selectedIsland->short_description ?? 'Belum ada deskripsi singkat. Tambahkan konten about di database.' }}
                    </p>

                    @if(!empty($featuresByType['about']) && $featuresByType['about']->count())
                        <div class="mt-4 space-y-3">
                            @foreach($featuresByType['about'] as $about)
                                <div>
                                    <h3 class="text-sm sm:text-base font-semibold mb-1">{{ $about->title }}</h3>
                                    <p class="text-xs sm:text-sm text-white/70 leading-relaxed">
                                        {{ $about->description }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                {{-- HISTORY / CERITA DAERAH --}}
                @if(!empty($featuresByType['history']) && $featuresByType['history']->count())
                    <section id="history">
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                            Sejarah & Cerita Daerah
                        </h2>
                        <div class="space-y-4">
                            @foreach($featuresByType['history'] as $history)
                                <div class="border border-white/10 rounded-2xl p-4">
                                    <h3 class="text-sm sm:text-base font-semibold mb-1">{{ $history->title }}</h3>
                                    <p class="text-xs sm:text-sm text-white/70 leading-relaxed">
                                        {{ $history->description }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- STATISTIK ISLAND: DASHBOARD COMPACT --}}
                <div id="stats" class="mt-10">
                    <div class="bg-white/5 border border-white/10 rounded-3xl p-6 sm:p-8 shadow-[0_18px_45px_rgba(0,0,0,0.45)] space-y-6">

                        {{-- HEADER --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                                    Statistik {{ $selectedIsland->title ?? $selectedIsland->name }}
                                </h2>
                                <p class="text-xs sm:text-sm text-white/60 mt-1">
                                    Gambaran singkat penduduk, agama, suku, dan bahasa di pulau ini.
                                </p>
                            </div>
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-black/30 border border-white/10 text-xs sm:text-sm">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Data budaya aktif</span>
                            </div>
                        </div>

                        {{-- TOP METRICS --}}
                        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            {{-- Penduduk --}}
                            <div class="rounded-2xl bg-black/40 border border-white/10 p-4 flex flex-col justify-between">
                                <p class="text-[10px] uppercase tracking-[0.22em] text-white/50">
                                    Jumlah Penduduk
                                </p>
                                <p class="mt-2 text-2xl font-semibold">
                                    {{ $selectedIsland->population ? number_format($selectedIsland->population, 0, ',', '.') : '—' }}
                                </p>
                                <p class="mt-1 text-[11px] text-white/40">
                                    Perkiraan total penduduk di pulau ini.
                                </p>
                            </div>

                            {{-- Banyak Agama --}}
                            <div class="rounded-2xl bg-black/40 border border-white/10 p-4 flex flex-col justify-between">
                                <p class="text-[10px] uppercase tracking-[0.22em] text-white/50">
                                    Ragam Agama
                                </p>
                                <p class="mt-2 text-2xl font-semibold">
                                    {{ !empty($demographics['religion']) ? $demographics['religion']->count() : 0 }}
                                </p>
                                <p class="mt-1 text-[11px] text-white/40">
                                    Jumlah agama yang tercatat di statistik.
                                </p>
                            </div>

                            {{-- Banyak Suku --}}
                            <div class="rounded-2xl bg-black/40 border border-white/10 p-4 flex flex-col justify-between">
                                <p class="text-[10px] uppercase tracking-[0.22em] text-white/50">
                                    Ragam Suku
                                </p>
                                <p class="mt-2 text-2xl font-semibold">
                                    {{ !empty($demographics['ethnicity']) ? $demographics['ethnicity']->count() : 0 }}
                                </p>
                                <p class="mt-1 text-[11px] text-white/40">
                                    Suku utama yang mewakili kebudayaan lokal.
                                </p>
                            </div>

                            {{-- Banyak Bahasa --}}
                            <div class="rounded-2xl bg-black/40 border border-white/10 p-4 flex flex-col justify-between">
                                <p class="text-[10px] uppercase tracking-[0.22em] text-white/50">
                                    Bahasa Daerah
                                </p>
                                <p class="mt-2 text-2xl font-semibold">
                                    {{ !empty($demographics['language']) ? $demographics['language']->count() : 0 }}
                                </p>
                                <p class="mt-1 text-[11px] text-white/40">
                                    Bahasa daerah yang umum digunakan.
                                </p>
                            </div>
                        </div>

                        {{-- CHART GRID (3 KOTAK) --}}
                        <div class="grid lg:grid-cols-3 gap-4">

                            {{-- AGAMA (PIE) --}}
                            <div class="rounded-2xl bg-black/40 border border-white/10 p-4 flex flex-col">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-semibold">Komposisi Agama</h3>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300">
                                        Pie chart
                                    </span>
                                </div>
                                <div class="flex-1 flex gap-3">
                                    <div class="flex-1 h-40">
                                        <canvas id="islandReligionChart"></canvas>
                                    </div>
                                    <div class="w-28 text-[11px] space-y-1 hidden sm:block">
                                        @forelse($demographics['religion'] as $row)
                                            <div class="flex justify-between gap-1">
                                                <span class="truncate">{{ $row->label }}</span>
                                                <span>{{ $row->percentage }}%</span>
                                            </div>
                                        @empty
                                            <p class="text-white/50">Belum ada data.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            {{-- SUKU (DOUGHNUT) --}}
                            <div class="rounded-2xl bg-black/40 border border-white/10 p-4 flex flex-col">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-semibold">Sebaran Suku</h3>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300">
                                        Doughnut
                                    </span>
                                </div>
                                <div class="flex-1 flex gap-3">
                                    <div class="flex-1 h-40">
                                        <canvas id="islandEthnicityChart"></canvas>
                                    </div>
                                    <div class="w-28 text-[11px] space-y-1 hidden sm:block">
                                        @forelse($demographics['ethnicity'] as $row)
                                            <div class="flex justify-between gap-1">
                                                <span class="truncate">{{ $row->label }}</span>
                                                <span>{{ $row->percentage }}%</span>
                                            </div>
                                        @empty
                                            <p class="text-white/50">Belum ada data.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            {{-- BAHASA (BAR) --}}
                            <div class="rounded-2xl bg-black/40 border border-white/10 p-4 flex flex-col">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-semibold">Bahasa Daerah</h3>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-sky-500/20 text-sky-300">
                                        Bar chart
                                    </span>
                                </div>
                                <div class="flex-1 h-40">
                                    <canvas id="islandLanguageChart"></canvas>
                                </div>
                                <div class="mt-2 text-[11px] text-white/50">
                                    @if(!empty($demographics['language']) && $demographics['language']->count())
                                        <span class="hidden sm:inline">
                                            Menampilkan persentase beberapa bahasa utama.
                                        </span>
                                    @else
                                        Belum ada data bahasa.
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- DESTINASI --}}
                @if(!empty($featuresByType['destination']) && $featuresByType['destination']->count())
                    <section id="destinations">
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                            Destinasi Populer
                        </h2>
                        <div class="grid sm:grid-cols-2 gap-4">
                            @foreach($featuresByType['destination'] as $dest)
                                <div class="border border-white/10 rounded-2xl p-4 flex gap-3">
                                    @if($dest->image_url)
                                        <img
                                            src="{{ $dest->image_url }}"
                                            alt="{{ $dest->title }}"
                                            class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg border border-white/10 flex-shrink-0"
                                        >
                                    @endif
                                    <div>
                                        <h3 class="text-sm sm:text-base font-semibold mb-1">
                                            {{ $dest->title }}
                                        </h3>
                                        @if($dest->description)
                                            <p class="text-xs sm:text-sm text-white/70 leading-relaxed">
                                                {{ $dest->description }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- MAKANAN KHAS --}}
                @if(!empty($featuresByType['food']) && $featuresByType['food']->count())
                    <section id="foods">
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                            Makanan Khas
                        </h2>
                        <div class="grid sm:grid-cols-2 gap-4">
                            @foreach($featuresByType['food'] as $food)
                                <div class="border border-white/10 rounded-2xl p-4 flex gap-3">
                                    @if($food->image_url)
                                        <img
                                            src="{{ $food->image_url }}"
                                            alt="{{ $food->title }}"
                                            class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg border border-white/10 flex-shrink-0"
                                        >
                                    @endif
                                    <div>
                                        <h3 class="text-sm sm:text-base font-semibold mb-1">
                                            {{ $food->title }}
                                        </h3>
                                        <p class="text-xs sm:text-sm text-white/70 leading-relaxed">
                                            {{ $food->description }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- BUDAYA & TRADISI (opsional, tidak di navbar tapi tetap ada) --}}
                @if(!empty($featuresByType['culture']) && $featuresByType['culture']->count())
                    <section id="cultures">
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                            Budaya & Tradisi
                        </h2>
                        <div class="space-y-4">
                            @foreach($featuresByType['culture'] as $culture)
                                <div class="border border-white/10 rounded-2xl p-4">
                                    <h3 class="text-sm sm:text-base font-semibold mb-1">
                                        {{ $culture->title }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-white/70 leading-relaxed">
                                        {{ $culture->description }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- QUIZ PULAU --}}
                <section id="quiz">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                        Kuis {{ $selectedIsland->title ?? $selectedIsland->name }}
                    </h2>
                    <p class="text-sm sm:text-base text-white/80">
                        Area ini bisa berisi kuis khusus tentang {{ $selectedIsland->title ?? $selectedIsland->name }}
                        — misalnya tentang destinasi, makanan khas, atau budaya setempat.
                    </p>
                </section>

            </div>
        </section>
    @endif
@endsection

@push('scripts')
    {{-- GSAP untuk hero cards --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    {{-- Chart.js untuk statistik --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // ================== GSAP CAROUSEL ==================
        document.addEventListener('DOMContentLoaded', () => {
            // ===== DATA DARI LARAVEL =====
            const data        = @json($carouselData);
            const initialSlug = @json(optional($selectedIsland)->slug);

            if (!Array.isArray(data) || data.length === 0) {
                console.warn('Tidak ada data island di database.');
                return;
            }

            const _ = (id) => document.getElementById(id);
            const coverEl = document.querySelector('.cover');

            // Bangun HTML card dari data
            const cards = data.map((i, index) => `
              <div
                class="card"
                id="card${index}"
                data-slug="${i.slug}"
                style="background-image:url(${i.image})"
              >
                <div class="card-content bg-gradient-to-t from-black/60 to-transparent p-4 sm:p-5">
                  <div class="content-place text-[11px] sm:text-[13px] tracking-[1px] opacity-80 uppercase">
                    ${i.place ?? ''}
                  </div>
                  <div class="content-title-1 text-[22px] sm:text-[26px] leading-none">
                    ${i.title ?? ''}
                  </div>
                  <div class="content-title-2 text-[18px] sm:text[22px] leading-none">
                    ${i.title2 ?? ''}
                  </div>
                </div>
              </div>
            `).join('');

            const demoEl = _('demo');
            if (!demoEl) {
                console.warn('#demo container tidak ditemukan');
                return;
            }
            demoEl.innerHTML = cards;

            // Nomor slide
            const slideNumbers = data
                .map((_, index) => `<div class="item" id="slide-item-${index}">${index + 1}</div>`)
                .join('');
            const slideNumbersEl = _('slide-numbers');
            if (slideNumbersEl) {
                slideNumbersEl.innerHTML = slideNumbers;
            }

            // Klik kartu → pindah ke halaman pulau (tab yang sama)
            function attachCardClicks() {
                const cardElements = document.querySelectorAll('.card');

                cardElements.forEach((card, index) => {
                    const slugFromData = data[index]?.slug || card.dataset.slug;
                    if (!slugFromData) return;

                    card.addEventListener('click', () => {
                        window.location.href = `/islands/${slugFromData}`;
                    });
                });
            }

            // ===== LOGIKA ANIMASI GSAP =====
            const range = (n) => Array(n).fill(0).map((_, j) => j);
            const set   = gsap.set;
            const ease  = "sine.inOut";

            function getCard(index)       { return `#card${index}`; }
            function getSliderItem(index) { return `#slide-item-${index}`; }

            // default order: [0,1,2,...]
            let order = range(data.length);

            // kalau ada pulau yang dipilih, jadikan dia index pertama
            if (initialSlug) {
                const idx = data.findIndex(item => item.slug === initialSlug);
                if (idx > 0) {
                    order = [
                        ...order.slice(idx),
                        ...order.slice(0, idx),
                    ];
                }
            }

            let offsetTop   = 200;
            let offsetLeft  = 700;
            let cardWidth   = 200;
            let cardHeight  = 300;
            let gap         = 40;
            let numberSize  = 50;

            function animate(target, duration, properties) {
                return new Promise((resolve) => {
                    gsap.to(target, {
                        ...properties,
                        duration,
                        onComplete: resolve
                    });
                });
            }

            function setupLayout() {
                const { innerHeight: height, innerWidth: width } = window;

                // layout desktop / tablet
                if (width >= 768) {
                    cardWidth  = 220;
                    cardHeight = 320;
                    offsetTop  = height - 430;
                    offsetLeft = width - 830;
                    gap        = 40;
                } else {
                    // layout mobile / tablet kecil (portrait)
                    cardWidth  = Math.min(width * 0.75, 260);
                    cardHeight = Math.min(height * 0.45, 280);
                    offsetTop  = height - (cardHeight + 110);
                    offsetLeft = width - (cardWidth * 1.9);
                    gap        = 24;
                }
            }

            function init() {
                setupLayout();

                const [active, ...rest] = order;
                const { innerHeight: height, innerWidth: width } = window;

                if (coverEl) {
                    // awalnya tutup full
                    gsap.set(coverEl, { x: 0 });
                }

                gsap.set(getCard(active), {
                    x: 0,
                    y: 0,
                    width: width,
                    height: height,
                    borderRadius: 0
                });

                rest.forEach((i, index) => {
                    gsap.set(getCard(i), {
                        x: offsetLeft + 400 + index * (cardWidth + gap),
                        y: offsetTop,
                        width: cardWidth,
                        height: cardHeight,
                        zIndex: 30,
                        borderRadius: 10
                    });
                });

                // geser "tirai" cover keluar layar → baru mulai loop
                if (coverEl) {
                    gsap.to(coverEl, {
                        x: width + 400,
                        duration: 1.2,
                        ease,
                        onComplete: () => {
                            if (order.length > 1) loop();
                        }
                    });
                } else {
                    if (order.length > 1) loop();
                }
            }

            function step() {
                return new Promise((resolve) => {
                    order.push(order.shift());
                    const [active, ...rest] = order;
                    const prv = rest[rest.length - 1];

                    gsap.set(getCard(prv),    { zIndex: 10 });
                    gsap.set(getCard(active), { zIndex: 20 });
                    gsap.to(getCard(prv),     { scale: 1.5, ease });

                    gsap.to(getCard(active), {
                        x: 0,
                        y: 0,
                        ease,
                        width: window.innerWidth,
                        height: window.innerHeight,
                        borderRadius: 0,
                        onComplete: () => {
                            const xNew = offsetLeft + (rest.length - 1) * (cardWidth + gap);
                            gsap.set(getCard(prv), {
                                x: xNew,
                                y: offsetTop,
                                width: cardWidth,
                                height: cardHeight,
                                zIndex: 30,
                                scale: 1,
                                borderRadius: 10
                            });
                            if (slideNumbersEl) {
                                gsap.set(getSliderItem(prv), { x: rest.length * numberSize });
                            }
                            resolve();
                        }
                    });

                    rest.forEach((i, index) => {
                        if (i !== prv) {
                            const xNew = offsetLeft + index * (cardWidth + gap);
                            gsap.to(getCard(i), {
                                x: xNew,
                                y: offsetTop,
                                width: cardWidth,
                                height: cardHeight,
                                ease,
                                delay: 0.1 * (index + 1)
                            });
                        }
                    });
                });
            }

            async function loop() {
                // kalau cuma 1 card, nggak perlu loop
                if (order.length <= 1) return;

                await animate(".indicator", 2,   { x: 0 });
                await animate(".indicator", 0.8, { x: window.innerWidth, delay: 0.3 });
                set(".indicator", { x: -window.innerWidth });
                await step();
                loop();
            }

            function loadImage(src) {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.onload  = () => resolve(img);
                    img.onerror = reject;
                    img.src     = src;
                });
            }

            async function loadImages() {
                const promises = data.map(({ image }) => loadImage(image));
                return Promise.all(promises);
            }

            function animateHeroTitle() {
                const heroLines = document.querySelectorAll('.hero-title-line');
                if (!heroLines.length) return;

                gsap.from(heroLines, {
                    y: 40,
                    opacity: 0,
                    duration: 1.1,
                    ease: "power3.out",
                    stagger: 0.15
                });
            }

            async function start() {
                try {
                    await loadImages();
                    attachCardClicks();
                    init();              // cover digeser + mulai loop
                    animateHeroTitle();
                } catch (e) {
                    console.error("Image failed to load", e);
                }
            }

            // Re-init layout ketika resize
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    init();
                }, 150);
            });

            start();
        });
    </script>

    @if($hasSelected)
    <script>
        // ================== CHART STATISTIK PULAU ==================
        document.addEventListener('DOMContentLoaded', () => {
            const religionRows  = @json($demographics['religion'] ?? []);
            const ethnicityRows = @json($demographics['ethnicity'] ?? []);
            const languageRows  = @json($demographics['language'] ?? []);

            // chart lingkaran (bisa pie / doughnut)
            function makeCircleChart(canvasId, rows, type = 'pie') {
                const canvas = document.getElementById(canvasId);
                if (!canvas || !rows.length) return;

                const ctx = canvas.getContext('2d');
                const labels = rows.map(r => r.label);
                const data   = rows.map(r => r.percentage);

                new Chart(ctx, {
                    type: type, // 'pie' atau 'doughnut'
                    data: {
                        labels,
                        datasets: [{
                            data,
                            backgroundColor: [
                                '#FF6384', '#36A2EB', '#FFCE56',
                                '#4BC0C0', '#9966FF', '#FF9F40',
                                '#FFCD56', '#C9CBCF'
                            ],
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: type === 'doughnut' ? '60%' : 0, // bolong tengah kalau doughnut
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: { usePointStyle: true }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => `${ctx.label}: ${ctx.parsed}%`
                                }
                            }
                        }
                    }
                });
            }

            // bar chart bahasa
            function makeBarChart(canvasId, rows) {
                const canvas = document.getElementById(canvasId);
                if (!canvas || !rows.length) return;

                const ctx = canvas.getContext('2d');
                const labels = rows.map(r => r.label);
                const data   = rows.map(r => r.percentage);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            data,
                            label: 'Persentase',
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 10 }
                            }
                        }
                    }
                });
            }

            // AGAMA → PIE
            if (religionRows.length) {
                makeCircleChart('islandReligionChart', religionRows, 'pie');
            }

            // SUKU → DOUGHNUT (DONAT)
            if (ethnicityRows.length) {
                makeCircleChart('islandEthnicityChart', ethnicityRows, 'doughnut');
            }

            // BAHASA → BAR
            if (languageRows.length) {
                makeBarChart('islandLanguageChart', languageRows);
            }
        });
    </script>
    @endif
@endpush
