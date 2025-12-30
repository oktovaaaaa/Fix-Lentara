{{-- resources/views/islands/sundakecil.blade.php --}}
@extends('layouts.app')

@section('title', 'Pulau Sunda Kecil – Lentara')

@section('content')
    {{-- HERO shared --}}
    @include('partials.landing-hero')

    @php
        // histories
        $historiesByTribe = $historiesByTribe ?? collect();
        $baliHistories    = $historiesByTribe['Bali'] ?? collect();
        $sasakHistories   = $historiesByTribe['Sasak'] ?? collect();
        $atoniHistories   = $historiesByTribe['Atoni'] ?? collect();

        // ===== QUIZ PER SUKU (dari controller) =====
        // $quizzesByTribe: Collection map [ 'Bali' => Quiz, 'Sasak' => Quiz, 'Atoni' => Quiz, '__general__' => Quiz ]
        // $generalIslandQuiz: Quiz fallback utk pulau jika tribe spesifik belum ada
        $quizzesByTribe = $quizzesByTribe ?? collect();
        $generalIslandQuiz = $generalIslandQuiz ?? null;

        $baliQuiz  = $quizzesByTribe['Bali'] ?? null;
        $sasakQuiz = $quizzesByTribe['Sasak'] ?? null;
        $atoniQuiz = $quizzesByTribe['Atoni'] ?? null;
    @endphp

    <section class="relative z-[10] py-12 sm:py-16 px-4 sm:px-6 bg-[var(--bg-body)] text-[var(--txt-body)]">

        @include('islands.partials.tribe-styles')

        <div class="max-w-5xl mx-auto space-y-10">

            {{-- TABS --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--muted)] mb-1">
                        Pilih Suku di Sunda Kecil (Bali & Nusa Tenggara)
                    </p>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Eksplorasi Budaya per Suku
                    </h2>
                </div>

                <div class="inline-flex gap-2 bg-[color-mix(in_srgb,var(--bg-body)_80%,#e5e7eb_20%)] p-1.5 rounded-full">
                    <button type="button" class="tribe-tab is-active" data-tribe-tab="bali">Bali</button>
                    <button type="button" class="tribe-tab" data-tribe-tab="sasak">Sasak</button>
                    <button type="button" class="tribe-tab" data-tribe-tab="atoni">Atoni</button>
                </div>
            </div>

            <div class="space-y-12" id="suku-wrapper">

                {{-- ABOUT + HISTORY --}}
                <section id="about" class="space-y-3">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Tentang Suku di Pulau Sunda Kecil
                    </h2>

                    {{-- Bali --}}
                    <div data-tribe-panel="bali">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Bali mendiami Pulau Bali dan tersebar di beberapa wilayah lain di Indonesia.
                            Mereka dikenal dengan sistem kepercayaan Hindu Bali, upacara adat yang kaya simbol,
                            serta seni tari, musik, dan arsitektur pura yang sangat khas.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Kehidupan masyarakat Bali diatur oleh sistem adat desa pakraman dan kalender sakral,
                            yang membentuk ritme upacara dari hari ke hari. Dari upacara <span class="italic">Ngaben</span>
                            hingga <span class="italic">Galungan</span>, setiap momen memiliki makna spiritual yang mendalam.
                        </p>

                        @if($baliHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Bali</h3>
                                    <p class="history-subtitle">
                                        Perjalanan panjang budaya Bali dari kerajaan kuno hingga menjadi pusat seni Nusantara.
                                    </p>

                                    <div class="timeline">
                                        @foreach($baliHistories as $item)
                                            <div class="timeline-item">
                                                <div class="timeline-card">
                                                    <div class="timeline-card-glow"></div>
                                                    <div class="timeline-card-inner">
                                                        <div class="timeline-badge">
                                                            {{ !empty($item->year_label) ? $item->year_label : 'Jejak Sejarah' }}
                                                        </div>

                                                        <h3 class="timeline-heading">{{ $item->title }}</h3>
                                                        <p class="timeline-text">{{ $item->content }}</p>

                                                        @if($item->more_link)
                                                            <a href="{{ $item->more_link }}" target="_blank" rel="noopener" class="timeline-link">
                                                                Lihat selengkapnya →
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        @else
                            <p class="history-empty">Belum ada data sejarah Bali yang diinput dari admin.</p>
                        @endif
                    </div>

                    {{-- Sasak --}}
                    <div class="hidden" data-tribe-panel="sasak">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Sasak adalah penduduk asli Pulau Lombok. Mereka memiliki tradisi yang unik,
                            menggabungkan pengaruh Islam, adat lokal, serta warisan budaya kuno Nusantara.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Dari desa adat Sade, anyaman, hingga musik tradisional dan upacara keagamaan,
                            budaya Sasak menyimpan banyak cerita tentang hubungan manusia, alam, dan keyakinan.
                        </p>

                        @if($sasakHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Sasak</h3>
                                    <p class="history-subtitle">
                                        Kisah perjalanan masyarakat Sasak dalam menjaga adat, tanah, dan keyakinan mereka.
                                    </p>

                                    <div class="timeline">
                                        @foreach($sasakHistories as $item)
                                            <div class="timeline-item">
                                                <div class="timeline-card">
                                                    <div class="timeline-card-glow"></div>
                                                    <div class="timeline-card-inner">
                                                        <div class="timeline-badge">
                                                            {{ !empty($item->year_label) ? $item->year_label : 'Jejak Sejarah' }}
                                                        </div>

                                                        <h3 class="timeline-heading">{{ $item->title }}</h3>
                                                        <p class="timeline-text">{{ $item->content }}</p>

                                                        @if($item->more_link)
                                                            <a href="{{ $item->more_link }}" target="_blank" rel="noopener" class="timeline-link">
                                                                Lihat selengkapnya →
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        @else
                            <p class="history-empty">Belum ada data sejarah Sasak yang diinput dari admin.</p>
                        @endif
                    </div>

                    {{-- Atoni --}}
                    <div class="hidden" data-tribe-panel="atoni">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Atoni (sering disebut Atoni Meto) merupakan salah satu suku besar di wilayah Timor,
                            terutama di Nusa Tenggara Timur. Mereka memiliki sistem adat, simbol, dan struktur sosial
                            yang kuat, yang tercermin dalam rumah adat, pakaian, dan upacara tradisional.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Dalam kehidupan sehari-hari, relasi dengan tanah, leluhur, dan komunitas menjadi pusat
                            dari praktik budaya Atoni yang terus dipertahankan hingga sekarang.
                        </p>

                        @if($atoniHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Atoni</h3>
                                    <p class="history-subtitle">
                                        Jejak perjalanan masyarakat Atoni dalam menjaga adat dan tanah leluhur.
                                    </p>

                                    <div class="timeline">
                                        @foreach($atoniHistories as $item)
                                            <div class="timeline-item">
                                                <div class="timeline-card">
                                                    <div class="timeline-card-glow"></div>
                                                    <div class="timeline-card-inner">
                                                        <div class="timeline-badge">
                                                            {{ !empty($item->year_label) ? $item->year_label : 'Jejak Sejarah' }}
                                                        </div>

                                                        <h3 class="timeline-heading">{{ $item->title }}</h3>
                                                        <p class="timeline-text">{{ $item->content }}</p>

                                                        @if($item->more_link)
                                                            <a href="{{ $item->more_link }}" target="_blank" rel="noopener" class="timeline-link">
                                                                Lihat selengkapnya →
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        @else
                            <p class="history-empty">Belum ada data sejarah Atoni yang diinput dari admin.</p>
                        @endif
                    </div>
                </section>

                {{-- STATISTIK (kosong) --}}
                <section id="stats" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Statistik Singkat</h2>

                    <div data-tribe-panel="bali" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm"></div>
                    <div data-tribe-panel="sasak" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm hidden"></div>
                    <div data-tribe-panel="atoni" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm hidden"></div>

                    <p class="text-xs sm:text-sm text-[var(--muted)]">Data statistik Sunda Kecil akan ditambahkan kemudian.</p>
                </section>

                {{-- DESTINASI (kosong) --}}
                <section id="destinations" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Destinasi Budaya</h2>

                    <div data-tribe-panel="bali" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                    <div data-tribe-panel="sasak" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden"></div>
                    <div data-tribe-panel="atoni" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden"></div>
                </section>

                {{-- KULINER (kosong) --}}
                <section id="foods" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Kuliner Khas</h2>

                    <div data-tribe-panel="bali" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                    <div data-tribe-panel="sasak" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden"></div>
                    <div data-tribe-panel="atoni" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden"></div>
                </section>

{{-- WARISAN (DINAMIS DARI ADMIN) --}}
@include('islands.partials.heritage.section', [
    'tribeKey' => $tribeKey,
    'tribePage' => $tribePage,
    'itemsByCategory' => $itemsByCategory,
])

                {{-- QUIZ (SAMA SEPERTI HOME) --}}
                <section id="quiz" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Kuis Mini</h2>

                    <div data-tribe-panel="bali" class="space-y-4">
                        @include('partials.quiz-section', [
                            'quiz' => $baliQuiz ?: $generalIslandQuiz
                        ])
                    </div>

                    <div data-tribe-panel="sasak" class="space-y-4 hidden">
                        @include('partials.quiz-section', [
                            'quiz' => $sasakQuiz ?: $generalIslandQuiz
                        ])
                    </div>

                    <div data-tribe-panel="atoni" class="space-y-4 hidden">
                        @include('partials.quiz-section', [
                            'quiz' => $atoniQuiz ?: $generalIslandQuiz
                        ])
                    </div>
                </section>

            </div>
        </div>

        @include('islands.partials.tribe-tabs-script')
    </section>
@endsection
