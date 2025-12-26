{{-- resources/views/islands/sulawesi.blade.php --}}
@extends('layouts.app')

@section('title', 'Pulau Sulawesi – Lentara')

@section('content')
    @include('partials.landing-hero')

    @php
        // histories
        $historiesByTribe   = $historiesByTribe ?? collect();
        $bugisHistories     = $historiesByTribe['Bugis'] ?? collect();
        $makassarHistories  = $historiesByTribe['Makassar'] ?? collect();
        $torajaHistories    = $historiesByTribe['Toraja'] ?? collect();

        // ===== QUIZ PER SUKU (dari controller) =====
        $quizzesByTribe = $quizzesByTribe ?? collect();
        $generalIslandQuiz = $generalIslandQuiz ?? null;

        $bugisQuiz    = $quizzesByTribe['Bugis'] ?? null;
        $makassarQuiz = $quizzesByTribe['Makassar'] ?? null;
        $torajaQuiz   = $quizzesByTribe['Toraja'] ?? null;
    @endphp

    <section class="relative z-[10] py-12 sm:py-16 px-4 sm:px-6 bg-[var(--bg-body)] text-[var(--txt-body)]">

        @include('islands.partials.tribe-styles')

        <div class="max-w-5xl mx-auto space-y-10">

            {{-- TABS --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--muted)] mb-1">
                        Pilih Suku di Pulau Sulawesi
                    </p>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Eksplorasi Budaya per Suku
                    </h2>
                </div>

                <div class="inline-flex gap-2 bg-[color-mix(in_srgb,var(--bg-body)_80%,#e5e7eb_20%)] p-1.5 rounded-full">
                    <button type="button" class="tribe-tab is-active" data-tribe-tab="bugis">Bugis</button>
                    <button type="button" class="tribe-tab" data-tribe-tab="makassar">Makassar</button>
                    <button type="button" class="tribe-tab" data-tribe-tab="toraja">Toraja</button>
                </div>
            </div>

            <div class="space-y-12" id="suku-wrapper">

                {{-- ABOUT + HISTORY --}}
                <section id="about" class="space-y-3">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Tentang Suku di Pulau Sulawesi</h2>

                    {{-- Bugis --}}
                    <div data-tribe-panel="bugis">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Bugis banyak mendiami wilayah Sulawesi Selatan, khususnya sekitar pesisir.
                            Mereka dikenal sebagai pelaut ulung, pedagang yang ulet, dan masyarakat yang memiliki
                            sistem nilai <span class="italic">siri&rsquo; na pacce</span> tentang harga diri dan solidaritas.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Jejak budaya Bugis dapat dilihat dari naskah kuno Lontaraq, rumah panggung khas,
                            hingga perahu tradisional seperti pinisi yang mengarungi lautan Nusantara.
                        </p>

                        @if($bugisHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Bugis</h3>
                                    <p class="history-subtitle">
                                        Rangkaian peristiwa penting yang membentuk identitas maritim dan budaya Suku Bugis.
                                    </p>

                                    <div class="timeline">
                                        @foreach($bugisHistories as $item)
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
                            <p class="history-empty">Belum ada data sejarah Bugis yang diinput dari admin.</p>
                        @endif
                    </div>

                    {{-- Makassar --}}
                    <div class="hidden" data-tribe-panel="makassar">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Makassar berpusat di wilayah Kota Makassar dan sekitarnya. Sejak masa lalu,
                            pelabuhan Makassar menjadi titik penting perdagangan rempah, menjadikan masyarakat
                            Makassar terbuka terhadap berbagai pengaruh budaya.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Budaya Makassar tercermin dalam bahasa, upacara adat, serta kisah-kisah kepahlawanan
                            dari kerajaan-kerajaan di Sulawesi Selatan.
                        </p>

                        @if($makassarHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Makassar</h3>
                                    <p class="history-subtitle">
                                        Jejak kerajaan pesisir, perdagangan rempah, dan perlawanan terhadap kolonialisme.
                                    </p>

                                    <div class="timeline">
                                        @foreach($makassarHistories as $item)
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
                            <p class="history-empty">Belum ada data sejarah Makassar yang diinput dari admin.</p>
                        @endif
                    </div>

                    {{-- Toraja --}}
                    <div class="hidden" data-tribe-panel="toraja">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Toraja mendiami wilayah pegunungan di Sulawesi Selatan. Mereka dikenal
                            dengan upacara pemakaman <span class="italic">Rambu Solo&rsquo;</span> dan rumah adat
                            <span class="italic">Tongkonan</span> yang beratap melengkung seperti perahu.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Kepercayaan tradisional, seni ukir, dan tata ruang desa adat Toraja menyimpan
                            banyak cerita tentang hubungan manusia dengan leluhur dan alam.
                        </p>

                        @if($torajaHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Toraja</h3>
                                    <p class="history-subtitle">
                                        Perkembangan kepercayaan, adat pemakaman, dan perubahan sosial di tanah Toraja.
                                    </p>

                                    <div class="timeline">
                                        @foreach($torajaHistories as $item)
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
                            <p class="history-empty">Belum ada data sejarah Toraja yang diinput dari admin.</p>
                        @endif
                    </div>
                </section>

                {{-- STATISTIK (kosong) --}}
                <section id="stats" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Statistik Singkat</h2>

                    <div data-tribe-panel="bugis" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm"></div>
                    <div data-tribe-panel="makassar" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm hidden"></div>
                    <div data-tribe-panel="toraja" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm hidden"></div>

                    <p class="text-xs sm:text-sm text-[var(--muted)]">Data statistik Sulawesi akan ditambahkan kemudian.</p>
                </section>

                {{-- DESTINASI (kosong) --}}
                <section id="destinations" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Destinasi Budaya</h2>

                    <div data-tribe-panel="bugis" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                    <div data-tribe-panel="makassar" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden"></div>
                    <div data-tribe-panel="toraja" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden"></div>
                </section>

                {{-- KULINER (kosong) --}}
                <section id="foods" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Kuliner Khas</h2>

                    <div data-tribe-panel="bugis" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                    <div data-tribe-panel="makassar" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden"></div>
                    <div data-tribe-panel="toraja" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden"></div>
                </section>

                {{-- WARISAN (kosong) --}}
                <section id="warisan" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Warisan & Sejarah</h2>

                    <div data-tribe-panel="bugis" class="space-y-2"></div>
                    <div data-tribe-panel="makassar" class="space-y-2 hidden"></div>
                    <div data-tribe-panel="toraja" class="space-y-2 hidden"></div>
                </section>

                {{-- QUIZ (SAMA SEPERTI HOME) --}}
                <section id="quiz" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Kuis Mini</h2>

                    <div data-tribe-panel="bugis" class="space-y-4">
                        @include('partials.quiz-section', ['quiz' => $bugisQuiz ?: $generalIslandQuiz])
                    </div>

                    <div data-tribe-panel="makassar" class="space-y-4 hidden">
                        @include('partials.quiz-section', ['quiz' => $makassarQuiz ?: $generalIslandQuiz])
                    </div>

                    <div data-tribe-panel="toraja" class="space-y-4 hidden">
                        @include('partials.quiz-section', ['quiz' => $torajaQuiz ?: $generalIslandQuiz])
                    </div>
                </section>

            </div>
        </div>

        @include('islands.partials.tribe-tabs-script')
    </section>
@endsection
