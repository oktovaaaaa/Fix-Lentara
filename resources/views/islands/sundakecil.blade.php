{{-- resources/views/islands/sundakecil.blade.php --}}
@extends('layouts.app')

@section('title', 'Pulau Sunda Kecil – Lentara')

@section('content')
    {{-- HERO shared --}}
    @include('partials.landing-hero')

    @php
        // dikirim dari controller, tapi jaga-jaga kalau belum ada
        $historiesByTribe = $historiesByTribe ?? collect();
        $baliHistories    = $historiesByTribe['Bali'] ?? collect();
        $sasakHistories   = $historiesByTribe['Sasak'] ?? collect();
        $atoniHistories   = $historiesByTribe['Atoni'] ?? collect();
    @endphp

    {{-- WRAPPER SUNDA KECIL --}}
    <section
        class="relative z-[10] py-12 sm:py-16 px-4 sm:px-6 bg-[var(--bg-body)] text-[var(--txt-body)]">

        {{-- CSS kecil khusus tab suku + timeline history (shared) --}}
        @include('islands.partials.tribe-styles')

        <div class="max-w-5xl mx-auto space-y-10">

            {{-- PILIHAN SUKU (TABS) --}}
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
                    <button type="button"
                            class="tribe-tab is-active"
                            data-tribe-tab="bali">
                        Bali
                    </button>
                    <button type="button"
                            class="tribe-tab"
                            data-tribe-tab="sasak">
                        Sasak
                    </button>
                    <button type="button"
                            class="tribe-tab"
                            data-tribe-tab="atoni">
                        Atoni
                    </button>
                </div>
            </div>

            {{-- SEMUA SECTION DIBUNGKUS, ISINYA GANTI BERDASARKAN SUKU --}}
            <div class="space-y-12" id="suku-wrapper">

                {{-- ABOUT + HISTORY DINAMIS (PENTING) --}}
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

                        {{-- TIMELINE HISTORY: BALI --}}
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
                                                        @if(!empty($item->year_label))
                                                            <div class="timeline-badge">
                                                                {{ $item->year_label }}
                                                            </div>
                                                        @else
                                                            <div class="timeline-badge">
                                                                Jejak Sejarah
                                                            </div>
                                                        @endif

                                                        <h3 class="timeline-heading">{{ $item->title }}</h3>
                                                        <p class="timeline-text">
                                                            {{ $item->content }}
                                                        </p>

                                                        @if($item->more_link)
                                                            <a href="{{ $item->more_link }}" target="_blank" rel="noopener"
                                                               class="timeline-link">
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
                            <p class="history-empty">
                                Belum ada data sejarah Bali yang diinput dari admin.
                            </p>
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

                        {{-- TIMELINE HISTORY: SASAK --}}
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
                                                        @if(!empty($item->year_label))
                                                            <div class="timeline-badge">
                                                                {{ $item->year_label }}
                                                            </div>
                                                        @else
                                                            <div class="timeline-badge">
                                                                Jejak Sejarah
                                                            </div>
                                                        @endif

                                                        <h3 class="timeline-heading">{{ $item->title }}</h3>
                                                        <p class="timeline-text">
                                                            {{ $item->content }}
                                                        </p>

                                                        @if($item->more_link)
                                                            <a href="{{ $item->more_link }}" target="_blank" rel="noopener"
                                                               class="timeline-link">
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
                            <p class="history-empty">
                                Belum ada data sejarah Sasak yang diinput dari admin.
                            </p>
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

                        {{-- TIMELINE HISTORY: ATONI --}}
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
                                                        @if(!empty($item->year_label))
                                                            <div class="timeline-badge">
                                                                {{ $item->year_label }}
                                                            </div>
                                                        @else
                                                            <div class="timeline-badge">
                                                                Jejak Sejarah
                                                            </div>
                                                        @endif

                                                        <h3 class="timeline-heading">{{ $item->title }}</h3>
                                                        <p class="timeline-text">
                                                            {{ $item->content }}
                                                        </p>

                                                        @if($item->more_link)
                                                            <a href="{{ $item->more_link }}" target="_blank" rel="noopener"
                                                               class="timeline-link">
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
                            <p class="history-empty">
                                Belum ada data sejarah Atoni yang diinput dari admin.
                            </p>
                        @endif
                    </div>
                </section>

                {{-- ====== SECTION LAIN: HANYA KERANGKA / KOLOM KOSONG ====== --}}

                {{-- STATISTIK (kosong, siap diisi nanti) --}}
                <section id="stats" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Statistik Singkat
                    </h2>

                    <div data-tribe-panel="bali"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        {{-- Kolom statistik Bali (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="sasak"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm hidden">
                        {{-- Kolom statistik Sasak (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="atoni"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm hidden">
                        {{-- Kolom statistik Atoni (belum diisi) --}}
                    </div>

                    <p class="text-xs sm:text-sm text-[var(--muted)]">
                        Data statistik Sunda Kecil akan ditambahkan kemudian.
                    </p>
                </section>

                {{-- DESTINASI (kosong) --}}
                <section id="destinations" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Destinasi Budaya
                    </h2>

                    <div data-tribe-panel="bali"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Destinasi Bali (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="sasak"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        {{-- Destinasi Sasak (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="atoni"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        {{-- Destinasi Atoni (belum diisi) --}}
                    </div>
                </section>

                {{-- KULINER (kosong) --}}
                <section id="foods" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Kuliner Khas
                    </h2>

                    <div data-tribe-panel="bali"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Kuliner Bali (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="sasak"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        {{-- Kuliner Sasak (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="atoni"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        {{-- Kuliner Atoni (belum diisi) --}}
                    </div>
                </section>

                {{-- WARISAN (kosong) --}}
                <section id="warisan" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Warisan & Sejarah
                    </h2>

                    <div data-tribe-panel="bali" class="space-y-2">
                        {{-- Warisan Bali (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="sasak" class="space-y-2 hidden">
                        {{-- Warisan Sasak (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="atoni" class="space-y-2 hidden">
                        {{-- Warisan Atoni (belum diisi) --}}
                    </div>
                </section>

                {{-- KUIS (kosong) --}}
                <section id="quiz" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Kuis Mini
                    </h2>

                    <div data-tribe-panel="bali" class="space-y-4">
                        {{-- Kuis Bali (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="sasak" class="space-y-4 hidden">
                        {{-- Kuis Sasak (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="atoni" class="space-y-4 hidden">
                        {{-- Kuis Atoni (belum diisi) --}}
                    </div>
                </section>
            </div>
        </div>

        {{-- SCRIPT KECIL UNTUK GANTI TAB SUKU (shared) --}}
        @include('islands.partials.tribe-tabs-script')

    </section>
@endsection
