{{-- resources/views/islands/kalimantan.blade.php --}}
@extends('layouts.app')

@section('title', 'Pulau Kalimantan – Lentara')

@section('content')
    @include('partials.landing-hero')

    @php
        // histories
        $historiesByTribe  = $historiesByTribe ?? collect();
        $dayakHistories    = $historiesByTribe['Dayak'] ?? collect();
        $banjarHistories   = $historiesByTribe['Banjar'] ?? collect();
        $kutaiHistories    = $historiesByTribe['Kutai'] ?? collect();

        // ===== QUIZ PER SUKU (dari controller) =====
        $quizzesByTribe = $quizzesByTribe ?? collect();
        $generalIslandQuiz = $generalIslandQuiz ?? null;

        $dayakQuiz  = $quizzesByTribe['Dayak'] ?? null;
        $banjarQuiz = $quizzesByTribe['Banjar'] ?? null;
        $kutaiQuiz  = $quizzesByTribe['Kutai'] ?? null;
    @endphp

    <section class="relative z-[10] py-12 sm:py-16 px-4 sm:px-6 bg-[var(--bg-body)] text-[var(--txt-body)]">

        @include('islands.partials.tribe-styles')

        <div class="max-w-5xl mx-auto space-y-10">

            {{-- TABS --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--muted)] mb-1">
                        Pilih Suku di Pulau Kalimantan
                    </p>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Eksplorasi Budaya per Suku
                    </h2>
                </div>

                <div class="inline-flex gap-2 bg-[color-mix(in_srgb,var(--bg-body)_80%,#e5e7eb_20%)] p-1.5 rounded-full">
                    <button type="button" class="tribe-tab is-active" data-tribe-tab="dayak">Dayak</button>
                    <button type="button" class="tribe-tab" data-tribe-tab="banjar">Banjar</button>
                    <button type="button" class="tribe-tab" data-tribe-tab="kutai">Kutai</button>
                </div>
            </div>

            <div class="space-y-12" id="suku-wrapper">

                {{-- ABOUT + HISTORY --}}
                <section id="about" class="space-y-3">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Tentang Suku di Pulau Kalimantan</h2>

                    {{-- Dayak --}}
                    <div data-tribe-panel="dayak">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Dayak adalah kelompok etnis asli Kalimantan yang mendiami wilayah pedalaman
                            dan tepi sungai. Mereka terdiri dari banyak sub-suku dengan bahasa, adat, dan
                            kepercayaan yang beragam, namun sama-sama dekat dengan hutan dan sungai.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Budaya Dayak tercermin pada rumah panjang, seni ukir, tato tradisional, hingga
                            upacara adat yang menghormati leluhur dan alam sebagai sumber kehidupan.
                        </p>

                        @if($dayakHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Dayak</h3>
                                    <p class="history-subtitle">
                                        Rangkaian peristiwa penting yang menggambarkan perjalanan masyarakat Dayak di Kalimantan.
                                    </p>

                                    <div class="timeline">
                                        @foreach($dayakHistories as $item)
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
                            <p class="history-empty">Belum ada data sejarah Dayak yang diinput dari admin.</p>
                        @endif
                    </div>

                    {{-- Banjar --}}
                    <div class="hidden" data-tribe-panel="banjar">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Banjar banyak mendiami wilayah Kalimantan Selatan dan kawasan sekitarnya.
                            Mereka dikenal sebagai masyarakat sungai dan pesisir dengan tradisi perdagangan
                            yang kuat di pasar-pasar terapung.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Identitas Banjar tampak dalam bahasa, rumah panggung di tepi sungai, dan
                            adat istiadat yang dipengaruhi nilai-nilai Islam serta budaya Melayu.
                        </p>

                        @if($banjarHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Banjar</h3>
                                    <p class="history-subtitle">
                                        Jejak kerajaan Banjar, jaringan perdagangan, dan perkembangan budaya sungai Kalimantan.
                                    </p>

                                    <div class="timeline">
                                        @foreach($banjarHistories as $item)
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
                            <p class="history-empty">Belum ada data sejarah Banjar yang diinput dari admin.</p>
                        @endif
                    </div>

                    {{-- Kutai --}}
                    <div class="hidden" data-tribe-panel="kutai">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Kutai mendiami wilayah Kalimantan Timur dan sekitarnya. Di daerah ini pernah
                            berdiri salah satu kerajaan tertua di Nusantara yang dikenal melalui prasasti Yupa.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Warisan sejarah dan budaya Kutai dapat dijumpai pada situs-situs arkeologi, tradisi
                            lisan, dan upacara adat yang masih dijalankan masyarakat setempat.
                        </p>

                        @if($kutaiHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Kutai</h3>
                                    <p class="history-subtitle">
                                        Catatan awal kerajaan di Nusantara dan perkembangan masyarakat Kutai dari masa ke masa.
                                    </p>

                                    <div class="timeline">
                                        @foreach($kutaiHistories as $item)
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
                            <p class="history-empty">Belum ada data sejarah Kutai yang diinput dari admin.</p>
                        @endif
                    </div>
                </section>

                {{-- STATISTIK (kosong) --}}
                <section id="stats" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Statistik Singkat</h2>

                    <div data-tribe-panel="dayak" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm"></div>
                    <div data-tribe-panel="banjar" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm hidden"></div>
                    <div data-tribe-panel="kutai" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm hidden"></div>

                    <p class="text-xs sm:text-sm text-[var(--muted)]">Data statistik Kalimantan akan ditambahkan kemudian.</p>
                </section>

                {{-- DESTINASI (kosong) --}}
                <section id="destinations" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Destinasi Budaya</h2>

                    <div data-tribe-panel="dayak" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                    <div data-tribe-panel="banjar" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden"></div>
                    <div data-tribe-panel="kutai" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden"></div>
                </section>

                {{-- KULINER (kosong) --}}
                <section id="foods" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Kuliner Khas</h2>

                    <div data-tribe-panel="dayak" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                    <div data-tribe-panel="banjar" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden"></div>
                    <div data-tribe-panel="kutai" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden"></div>
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

                    <div data-tribe-panel="dayak" class="space-y-4">
                        @include('partials.quiz-section', ['quiz' => $dayakQuiz ?: $generalIslandQuiz])
                    </div>

                    <div data-tribe-panel="banjar" class="space-y-4 hidden">
                        @include('partials.quiz-section', ['quiz' => $banjarQuiz ?: $generalIslandQuiz])
                    </div>

                    <div data-tribe-panel="kutai" class="space-y-4 hidden">
                        @include('partials.quiz-section', ['quiz' => $kutaiQuiz ?: $generalIslandQuiz])
                    </div>
                </section>

            </div>
        </div>

        @include('islands.partials.tribe-tabs-script')
    </section>
@endsection
