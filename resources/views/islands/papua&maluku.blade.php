{{-- resources/views/islands/papua-maluku.blade.php --}}
@extends('layouts.app')

@section('title', 'Pulau Papua & Maluku – Lentara')

@section('content')
    {{-- HERO shared --}}
    @include('partials.landing-hero')

    @php
        // dikirim dari controller, tapi jaga-jaga kalau belum ada
        $historiesByTribe = $historiesByTribe ?? collect();
        $asmatHistories   = $historiesByTribe['Asmat'] ?? collect();
        $amungmeHistories = $historiesByTribe['Amungme'] ?? collect();
        $ambonHistories   = $historiesByTribe['Ambon'] ?? collect();
    @endphp

    {{-- WRAPPER PAPUA & MALUKU --}}
    <section
        class="relative z-[10] py-12 sm:py-16 px-4 sm:px-6 bg-[var(--bg-body)] text-[var(--txt-body)]">

        {{-- CSS kecil khusus tab suku + timeline history (shared) --}}
        @include('islands.partials.tribe-styles')

        <div class="max-w-5xl mx-auto space-y-10">

            {{-- PILIHAN SUKU (TABS) --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--muted)] mb-1">
                        Pilih Suku di Papua & Maluku
                    </p>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Eksplorasi Budaya per Suku
                    </h2>
                </div>

                <div class="inline-flex gap-2 bg-[color-mix(in_srgb,var(--bg-body)_80%,#e5e7eb_20%)] p-1.5 rounded-full">
                    <button type="button"
                            class="tribe-tab is-active"
                            data-tribe-tab="asmat">
                        Asmat
                    </button>
                    <button type="button"
                            class="tribe-tab"
                            data-tribe-tab="amungme">
                        Amungme
                    </button>
                    <button type="button"
                            class="tribe-tab"
                            data-tribe-tab="ambon">
                        Ambon
                    </button>
                </div>
            </div>

            {{-- SEMUA SECTION DIBUNGKUS, ISINYA GANTI BERDASARKAN SUKU --}}
            <div class="space-y-12" id="suku-wrapper">

                {{-- ABOUT + HISTORY DINAMIS (PENTING) --}}
                <section id="about" class="space-y-3">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Tentang Suku di Papua & Maluku
                    </h2>

                    {{-- Asmat --}}
                    <div data-tribe-panel="asmat">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Asmat mendiami wilayah pesisir selatan Papua. Mereka dikenal dengan seni ukir kayu
                            yang sangat khas, dengan bentuk-bentuk manusia, leluhur, dan simbol-simbol alam yang penuh makna.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Kehidupan masyarakat Asmat sangat dekat dengan hutan rawa, sungai, dan laut. Upacara adat,
                            rumah-rumah laki-laki (<span class="italic">jeu</span>), dan ritual penghormatan leluhur
                            menjadi pusat dari identitas budaya mereka.
                        </p>

                        {{-- TIMELINE HISTORY: ASMAT --}}
                        @if($asmatHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Asmat</h3>
                                    <p class="history-subtitle">
                                        Jejak seni ukir, hubungan dengan alam, dan ritual leluhur di tanah Asmat.
                                    </p>

                                    <div class="timeline">
                                        @foreach($asmatHistories as $item)
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
                                Belum ada data sejarah Asmat yang diinput dari admin.
                            </p>
                        @endif
                    </div>

                    {{-- Amungme --}}
                    <div class="hidden" data-tribe-panel="amungme">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Amungme tinggal di wilayah pegunungan tengah Papua. Bagi mereka, gunung-gunung
                            tinggi dan lembah adalah ruang sakral yang menghubungkan manusia, alam, dan leluhur.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Sistem nilai dan adat Amungme menekankan penghormatan terhadap tanah dan ruang hidup,
                            yang tercermin dalam cerita-cerita lisan, pembagian wilayah adat, dan ritual keagamaan.
                        </p>

                        {{-- TIMELINE HISTORY: AMUNGME --}}
                        @if($amungmeHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Amungme</h3>
                                    <p class="history-subtitle">
                                        Kisah masyarakat pegunungan Papua dalam menjaga tanah dan identitas budaya mereka.
                                    </p>

                                    <div class="timeline">
                                        @foreach($amungmeHistories as $item)
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
                                Belum ada data sejarah Amungme yang diinput dari admin.
                            </p>
                        @endif
                    </div>

                    {{-- Ambon --}}
                    <div class="hidden" data-tribe-panel="ambon">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Masyarakat Ambon berada di wilayah Maluku, yang sejak lama dikenal sebagai daerah
                            penghasil rempah-rempah dan persilangan berbagai budaya: lokal, Eropa, Arab, hingga Nusantara.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Musik tradisional, tari-tarian, tradisi pela gandong, dan kehidupan maritim menjadi
                            bagian penting dari identitas budaya Ambon di kepulauan rempah.
                        </p>

                        {{-- TIMELINE HISTORY: AMBON --}}
                        @if($ambonHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Masyarakat Ambon</h3>
                                    <p class="history-subtitle">
                                        Jejak panjang Ambon sebagai simpul perdagangan rempah dan pertemuan budaya.
                                    </p>

                                    <div class="timeline">
                                        @foreach($ambonHistories as $item)
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
                                Belum ada data sejarah Ambon yang diinput dari admin.
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

                    <div data-tribe-panel="asmat"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        {{-- Kolom statistik Asmat (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="amungme"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm hidden">
                        {{-- Kolom statistik Amungme (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="ambon"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm hidden">
                        {{-- Kolom statistik Ambon (belum diisi) --}}
                    </div>

                    <p class="text-xs sm:text-sm text-[var(--muted)]">
                        Data statistik Papua & Maluku akan ditambahkan kemudian.
                    </p>
                </section>

                {{-- DESTINASI (kosong) --}}
                <section id="destinations" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Destinasi Budaya
                    </h2>

                    <div data-tribe-panel="asmat"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Destinasi Asmat (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="amungme"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        {{-- Destinasi Amungme (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="ambon"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        {{-- Destinasi Ambon (belum diisi) --}}
                    </div>
                </section>

                {{-- KULINER (kosong) --}}
                <section id="foods" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Kuliner Khas
                    </h2>

                    <div data-tribe-panel="asmat"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Kuliner Asmat (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="amungme"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        {{-- Kuliner Amungme (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="ambon"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        {{-- Kuliner Ambon (belum diisi) --}}
                    </div>
                </section>

                {{-- WARISAN (kosong) --}}
                <section id="warisan" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Warisan & Sejarah
                    </h2>

                    <div data-tribe-panel="asmat" class="space-y-2">
                        {{-- Warisan Asmat (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="amungme" class="space-y-2 hidden">
                        {{-- Warisan Amungme (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="ambon" class="space-y-2 hidden">
                        {{-- Warisan Ambon (belum diisi) --}}
                    </div>
                </section>

                {{-- KUIS (kosong) --}}
                <section id="quiz" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Kuis Mini
                    </h2>

                    <div data-tribe-panel="asmat" class="space-y-4">
                        {{-- Kuis Asmat (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="amungme" class="space-y-4 hidden">
                        {{-- Kuis Amungme (belum diisi) --}}
                    </div>

                    <div data-tribe-panel="ambon" class="space-y-4 hidden">
                        {{-- Kuis Ambon (belum diisi) --}}
                    </div>
                </section>
            </div>
        </div>

        {{-- SCRIPT KECIL UNTUK GANTI TAB SUKU (shared) --}}
        @include('islands.partials.tribe-tabs-script')

    </section>
@endsection
