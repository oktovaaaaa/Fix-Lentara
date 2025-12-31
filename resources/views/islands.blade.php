{{-- resources/views/islands/show.blade.php --}}
@extends('layouts.app')

@section('title', ($selectedIsland->title ?? $selectedIsland->name ?? 'Pulau') . ' – Lentara')

@section('content')
    {{-- HERO shared --}}
    @include('partials.landing-hero')

    @php
        // ===============================
        // SAFETY DEFAULTS (JANGAN HAPUS)
        // ===============================
        $selectedIsland    = $selectedIsland ?? null;

        $featuresByType    = $featuresByType ?? [];
        $demographics      = $demographics ?? ['religion'=>collect(),'ethnicity'=>collect(),'language'=>collect()];

        $historiesByTribe  = $historiesByTribe ?? collect();
        $availableTribes   = $availableTribes ?? [];

        $quizzesByTribe    = $quizzesByTribe ?? collect();
        $globalQuiz        = $globalQuiz ?? null;

        // Warisan payload dari controller
        $tribeKey          = $tribeKey ?? request()->query('tribe', '');
        $tribeKey          = trim((string) $tribeKey);
        if ($tribeKey === '' && !empty($availableTribes)) {
            $tribeKey = (string) $availableTribes[0];
        }

        $tribePage         = $tribePage ?? null;
        $itemsByCategory   = $itemsByCategory ?? [
            'pakaian' => collect(),
            'rumah_tradisi' => collect(),
            'senjata_alatmusik' => collect(),
        ];

        // Helper URL untuk pindah suku (server-driven)
        $tribeUrl = function(string $t) {
            $base = request()->url();
            return $base . '?tribe=' . urlencode($t);
        };

        // Active tab helper
        $isActiveTribe = function(string $t) use ($tribeKey) {
            return strcasecmp(trim($tribeKey), trim($t)) === 0;
        };

        // Histories per suku aktif
        $currentTribeHistories = $historiesByTribe[$tribeKey] ?? collect();

        // Quiz per suku aktif
        $currentTribeQuiz = $quizzesByTribe[$tribeKey] ?? null;

        // Feature collections (pulau-level, dari admin Features)
        $aboutFeatures       = $featuresByType['about'] ?? collect();
        $historyFeatures     = $featuresByType['history'] ?? collect();
        $destinationFeatures = $featuresByType['destination'] ?? collect();
        $foodFeatures        = $featuresByType['food'] ?? collect();
        $cultureFeatures     = $featuresByType['culture'] ?? collect();

        // judul header pulau
        $islandNameTitle = $selectedIsland->name ?? $selectedIsland->title ?? 'Pulau';
        $islandPretty    = $selectedIsland->subtitle ?? $selectedIsland->name ?? 'Indonesia';
    @endphp

    {{-- WRAPPER UNIVERSAL --}}
    <section class="relative z-[10] py-12 sm:py-16 px-4 sm:px-6 bg-[var(--bg-body)] text-[var(--txt-body)]">

        {{-- Styles untuk tabs + timeline (punyamu yang sudah ada) --}}
        @include('islands.partials.tribe-styles')

        <div class="max-w-5xl mx-auto space-y-10">

            {{-- =========================
               TABS SUKU (SERVER DRIVEN)
               ========================= --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--muted)] mb-1">
                        Pilih Suku di {{ $islandPretty }}
                    </p>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Eksplorasi Budaya per Suku
                    </h2>
                </div>

                <div class="inline-flex flex-wrap gap-2 bg-[color-mix(in_srgb,var(--bg-body)_80%,#e5e7eb_20%)] p-1.5 rounded-full">
                    @forelse($availableTribes as $t)
                        @php $t = (string) $t; @endphp
                        <a href="{{ $tribeUrl($t) }}"
                           class="tribe-tab {{ $isActiveTribe($t) ? 'is-active' : '' }}"
                           aria-current="{{ $isActiveTribe($t) ? 'page' : 'false' }}">
                            {{ $t }}
                        </a>
                    @empty
                        <span class="text-xs text-[var(--muted)] px-3 py-2">
                            Belum ada suku (cek config/tribes.php atau histories).
                        </span>
                    @endforelse
                </div>
            </div>

            <div class="space-y-12" id="suku-wrapper">

                {{-- ===================================================
                   ABOUT SUKU (judul template, isi nanti dari CRUD)
                   Sementara: fallback ke feature about pulau-level
                   =================================================== --}}
{{-- ===================================================
   ABOUT SUKU (DINAMIS: TribeAboutPage + TribeAboutItem)
   =================================================== --}}
@include('islands.partials.about', [
    'tribeKey'   => $tribeKey,
    'aboutPage'  => $aboutPage,
    'aboutItems' => $aboutItems,
])

                {{-- ===================================================
                   SEJARAH SUKU (DINAMIS: IslandHistory per tribe)
                   DIPINDAH KE PARTIAL + CSS DI DALAM PARTIAL
                   =================================================== --}}
                @include('islands.partials.history', [
                    'tribeKey' => $tribeKey,
                    'currentTribeHistories' => $currentTribeHistories,
                    'historyFeatures' => $historyFeatures,
                ])

                {{-- ===================================================
                   DESTINASI SUKU (judul template, isi nanti dari CRUD)
                   Sementara: fallback ke feature destination pulau-level
                   =================================================== --}}
                <section id="destinations" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Destinasi Budaya Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}
                    </h2>

                    @if($destinationFeatures->count())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($destinationFeatures as $f)
                                <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                                    <h3 class="text-sm sm:text-base font-semibold mb-1">{{ $f->title }}</h3>
                                    <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                        {{ $f->content }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-[var(--muted)]">
                            Konten destinasi untuk {{ $tribeKey }} belum diinput dari admin.
                        </p>
                    @endif
                </section>

                {{-- ===================================================
                   KULINER SUKU (judul template, isi nanti dari CRUD)
                   Sementara: fallback ke feature food pulau-level
                   =================================================== --}}
                <section id="foods" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Kuliner Khas Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}
                    </h2>

                    @if($foodFeatures->count())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($foodFeatures as $f)
                                <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                                    <h3 class="text-sm sm:text-base font-semibold mb-1">{{ $f->title }}</h3>
                                    <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                        {{ $f->content }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-[var(--muted)]">
                            Konten kuliner untuk {{ $tribeKey }} belum diinput dari admin.
                        </p>
                    @endif
                </section>

                {{-- OPTIONAL: Budaya (pulau-level) --}}
                @if($cultureFeatures->count())
                    <section id="culture" class="space-y-4">
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                            Budaya & Tradisi (Umum)
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($cultureFeatures as $f)
                                <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                                    <h3 class="text-sm sm:text-base font-semibold mb-1">{{ $f->title }}</h3>
                                    <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                        {{ $f->content }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- ===================================================
                   WARISAN (DINAMIS DARI ADMIN)
                   =================================================== --}}
                @include('islands.partials.heritage.section', [
                    'tribeKey' => $tribeKey,
                    'tribePage' => $tribePage,
                    'itemsByCategory' => $itemsByCategory,
                ])

                {{-- ===================================================
                   QUIZ (DINAMIS PER SUKU, fallback global)
                   =================================================== --}}
                <section id="quiz" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Kuis Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}
                    </h2>

                    @include('partials.quiz-section', [
                        'quiz' => $currentTribeQuiz ?: $globalQuiz
                    ])
                </section>

            </div>
        </div>
    </section>
@endsection
