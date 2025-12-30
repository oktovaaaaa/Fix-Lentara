{{-- resources/views/islands/jawa.blade.php --}}
@extends('layouts.app')

@section('title', 'Pulau Jawa – Lentara')

@section('content')
    {{-- HERO shared --}}
    @include('partials.landing-hero')

    @php
        // dikirim dari controller, tapi jaga-jaga kalau belum ada
        $historiesByTribe = $historiesByTribe ?? collect();
        $sundaHistories   = $historiesByTribe['Sunda'] ?? collect();
        $jawaHistories    = $historiesByTribe['Jawa'] ?? collect();
        $betawiHistories  = $historiesByTribe['Betawi'] ?? collect();

        // ===== QUIZ PER SUKU (sama seperti HOME) =====
        // dari controller: $quizzesByTribe (map tribe => Quiz) dan $generalIslandQuiz (fallback)
        $quizzesByTribe = $quizzesByTribe ?? collect();
        $generalIslandQuiz = $generalIslandQuiz ?? null;

        $sundaQuiz  = $quizzesByTribe['Sunda'] ?? null;
        $jawaQuiz   = $quizzesByTribe['Jawa'] ?? null;
        $betawiQuiz = $quizzesByTribe['Betawi'] ?? null;
    @endphp

    {{-- WRAPPER JAWA --}}
    <section class="relative z-[10] py-12 sm:py-16 px-4 sm:px-6 bg-[var(--bg-body)] text-[var(--txt-body)]">

        {{-- CSS kecil khusus tab suku + timeline history --}}
        @include('islands.partials.tribe-styles')

        <div class="max-w-5xl mx-auto space-y-10">

            {{-- PILIHAN SUKU (TABS) --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--muted)] mb-1">
                        Pilih Suku di Pulau Jawa
                    </p>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Eksplorasi Budaya per Suku
                    </h2>
                </div>

                <div class="inline-flex gap-2 bg-[color-mix(in_srgb,var(--bg-body)_80%,#e5e7eb_20%)] p-1.5 rounded-full">
                    <button type="button" class="tribe-tab is-active" data-tribe-tab="sunda">Sunda</button>
                    <button type="button" class="tribe-tab" data-tribe-tab="jawa">Jawa</button>
                    <button type="button" class="tribe-tab" data-tribe-tab="betawi">Betawi</button>
                </div>
            </div>

            {{-- SEMUA SECTION DIBUNGKUS, ISINYA GANTI BERDASARKAN SUKU --}}
            <div class="space-y-12" id="suku-wrapper">

                {{-- ABOUT + HISTORY DINAMIS --}}
                <section id="about" class="space-y-3">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Tentang Suku di Pulau Jawa
                    </h2>

                    {{-- Sunda --}}
                    <div data-tribe-panel="sunda">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Sunda banyak mendiami wilayah Jawa Barat dan Banten. Masyarakat Sunda dikenal
                            dengan karakter yang ramah, budaya yang halus, serta kedekatan dengan alam pegunungan
                            dan sawah yang hijau.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Identitas Sunda tampak melalui bahasa, kesenian seperti jaipongan dan degung,
                            hingga tradisi lembur (desa) yang penuh gotong royong dan kekeluargaan.
                        </p>

                        {{-- TIMELINE HISTORY: SUNDA --}}
                        @if($sundaHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Sunda</h3>
                                    <p class="history-subtitle">
                                        Rangkaian peristiwa penting dari kerajaan kuno hingga budaya Sunda hari ini.
                                    </p>

                                    <div class="timeline">
                                        @foreach($sundaHistories as $item)
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
                            <p class="history-empty">Belum ada data sejarah Sunda yang diinput dari admin.</p>
                        @endif
                    </div>

                    {{-- Jawa --}}
                    <div class="hidden" data-tribe-panel="jawa">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Jawa merupakan kelompok etnis terbesar di Indonesia. Mereka banyak mendiami
                            wilayah Jawa Tengah, DIY, dan Jawa Timur dengan budaya yang menekankan harmoni,
                            tata krama, serta filosofi hidup seperti <span class="italic">tepa selira</span>.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Kesenian Jawa mencakup wayang kulit, gamelan, batik, dan tradisi keraton yang
                            masih terjaga di Yogyakarta dan Surakarta.
                        </p>

                        {{-- TIMELINE HISTORY: JAWA --}}
                        @if($jawaHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Jawa</h3>
                                    <p class="history-subtitle">
                                        Jejak kerajaan-kerajaan besar, tradisi keraton, dan perjalanan panjang
                                        masyarakat Jawa.
                                    </p>

                                    <div class="timeline">
                                        @foreach($jawaHistories as $item)
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
                            <p class="history-empty">Belum ada data sejarah Jawa yang diinput dari admin.</p>
                        @endif
                    </div>

                    {{-- Betawi --}}
                    <div class="hidden" data-tribe-panel="betawi">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Betawi tumbuh dari percampuran berbagai etnis yang datang ke Batavia
                            pada masa kolonial. Budayanya banyak dijumpai di Jakarta dan sekitarnya dengan
                            logat khas dan gaya hidup yang dekat dengan kota besar.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Kesenian Betawi antara lain lenong, gambang kromong, dan ondel-ondel
                            yang kerap muncul pada perayaan dan pawai budaya.
                        </p>

                        {{-- TIMELINE HISTORY: BETAWI --}}
                        @if($betawiHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Betawi</h3>
                                    <p class="history-subtitle">
                                        Perjalanan masyarakat Betawi dari Batavia tempo dulu hingga Jakarta modern.
                                    </p>

                                    <div class="timeline">
                                        @foreach($betawiHistories as $item)
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
                            <p class="history-empty">Belum ada data sejarah Betawi yang diinput dari admin.</p>
                        @endif
                    </div>
                </section>

                {{-- STATISTIK --}}
                <section id="stats" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Statistik Singkat</h2>

                    {{-- (bagian statistik kamu biarkan seperti semula) --}}
                    {{-- Sunda --}}
                    <div data-tribe-panel="sunda" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Wilayah Utama</p>
                            <p class="text-lg font-semibold mt-1">Jawa Barat & Banten</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Bahasa</p>
                            <p class="text-lg font-semibold mt-1">Bahasa Sunda</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Ikon Budaya</p>
                            <p class="text-lg font-semibold mt-1">Angklung, Wayang Golek</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Pusat Budaya</p>
                            <p class="text-lg font-semibold mt-1">Bandung & sekitarnya</p>
                        </div>
                    </div>

                    {{-- Jawa --}}
                    <div data-tribe-panel="jawa" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm hidden">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Wilayah Utama</p>
                            <p class="text-lg font-semibold mt-1">Jawa Tengah, DIY, Jawa Timur</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Bahasa</p>
                            <p class="text-lg font-semibold mt-1">Bahasa Jawa (ngoko, krama)</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Ikon Budaya</p>
                            <p class="text-lg font-semibold mt-1">Wayang Kulit, Gamelan, Batik</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Pusat Budaya</p>
                            <p class="text-lg font-semibold mt-1">Yogyakarta & Surakarta</p>
                        </div>
                    </div>

                    {{-- Betawi --}}
                    <div data-tribe-panel="betawi" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm hidden">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Wilayah Utama</p>
                            <p class="text-lg font-semibold mt-1">Jakarta & sekitarnya</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Bahasa</p>
                            <p class="text-lg font-semibold mt-1">Bahasa Betawi</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Ikon Budaya</p>
                            <p class="text-lg font-semibold mt-1">Ondel-ondel, Lenong</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Julukan</p>
                            <p class="text-lg font-semibold mt-1">Putra daerah Jakarta</p>
                        </div>
                    </div>

                    <p class="text-xs sm:text-sm text-[var(--muted)]">
                        Angka dan informasi di atas masih contoh statis, bisa diganti dengan data resmi kapan saja.
                    </p>
                </section>

                {{-- DESTINASI --}}
                <section id="destinations" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Destinasi Budaya</h2>

                    {{-- (destinasi kamu biarkan seperti semula) --}}
                    {{-- Sunda --}}
                    <div data-tribe-panel="sunda" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Kampung Naga</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Desa adat yang masih mempertahankan tata ruang tradisional dan kehidupan sederhana.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Tasikmalaya, Jawa Barat</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Saung Angklung Udjo</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Pusat pertunjukan dan pendidikan angklung sebagai warisan budaya Sunda.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Bandung</p>
                        </div>
                    </div>

                    {{-- Jawa --}}
                    <div data-tribe-panel="jawa" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Keraton Yogyakarta</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Istana resmi Sultan yang menjadi pusat pelestarian budaya Jawa klasik.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Kota Yogyakarta</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Candi Prambanan</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Kompleks candi Hindu terbesar di Indonesia dengan relief cerita Ramayana.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Perbatasan DIY & Jawa Tengah</p>
                        </div>
                    </div>

                    {{-- Betawi --}}
                    <div data-tribe-panel="betawi" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Setu Babakan</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Kawasan cagar budaya Betawi dengan rumah adat, kuliner, dan pertunjukan seni.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Jakarta Selatan</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Kota Tua Jakarta</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Area bersejarah dengan bangunan kolonial yang menjadi ruang ekspresi budaya Betawi.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Jakarta Barat</p>
                        </div>
                    </div>
                </section>

                {{-- KULINER --}}
                <section id="foods" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Kuliner Khas</h2>

                    {{-- (kuliner kamu biarkan seperti semula) --}}
                    {{-- Sunda --}}
                    <div data-tribe-panel="sunda" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Nasi Timbel</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Nasi hangat dibungkus daun pisang, disajikan dengan lauk, lalapan, dan sambal.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Jawa Barat</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Lotek & Karedok</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Hidangan sayur dengan saus kacang khas Sunda, segar dan menyehatkan.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Bandung & sekitarnya</p>
                        </div>
                    </div>

                    {{-- Jawa --}}
                    <div data-tribe-panel="jawa" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Gudeg</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Nangka muda dimasak lama dengan santan dan bumbu manis gurih khas Yogyakarta.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Yogyakarta</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Rawon</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Sup daging berkuah hitam dari kluwek, menjadi ikon kuliner Jawa Timur.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Surabaya & sekitarnya</p>
                        </div>
                    </div>

                    {{-- Betawi --}}
                    <div data-tribe-panel="betawi" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Soto Betawi</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Soto dengan kuah santan atau susu yang kaya rempah, berisi daging atau jeroan.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Jakarta</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Kerak Telor</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Telur bebek dengan beras ketan dan serundeng, dimasak di atas bara arang.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Jakarta</p>
                        </div>
                    </div>
                </section>

                {{-- WARISAN --}}
                <section id="warisan" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Warisan & Sejarah</h2>

                {{-- WARISAN (DINAMIS DARI ADMIN) --}}
@include('islands.partials.heritage.section', [
    'tribeKey' => $tribeKey,
    'tribePage' => $tribePage,
    'itemsByCategory' => $itemsByCategory,
])



                {{-- KUIS (DIUBAH: SAMA SEPERTI HOME, DINAMIS) --}}
                <section id="quiz" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">Kuis Mini</h2>

                    <div data-tribe-panel="sunda" class="space-y-4">
                        @include('partials.quiz-section', [
                            'quiz' => $sundaQuiz ?: $generalIslandQuiz
                        ])
                    </div>

                    <div data-tribe-panel="jawa" class="space-y-4 hidden">
                        @include('partials.quiz-section', [
                            'quiz' => $jawaQuiz ?: $generalIslandQuiz
                        ])
                    </div>

                    <div data-tribe-panel="betawi" class="space-y-4 hidden">
                        @include('partials.quiz-section', [
                            'quiz' => $betawiQuiz ?: $generalIslandQuiz
                        ])
                    </div>
                </section>

            </div>
        </div>

        {{-- SCRIPT KECIL UNTUK GANTI TAB SUKU --}}
        @include('islands.partials.tribe-tabs-script')
    </section>
@endsection
