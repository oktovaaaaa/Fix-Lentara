{{-- resources/views/islands/sumatera.blade.php --}}
@extends('layouts.app')

@section('title', 'Pulau Sumatera – Lentara')

@section('content')
    {{-- HERO shared --}}
    @include('partials.landing-hero')

    @php
        // dikirim dari controller, tapi jaga-jaga kalau belum ada
        $historiesByTribe = $historiesByTribe ?? collect();
        $acehHistories = $historiesByTribe['Aceh'] ?? collect();
        $batakHistories = $historiesByTribe['Batak'] ?? collect();
        $minangHistories = $historiesByTribe['Minangkabau'] ?? collect();

        // ===== QUIZ PER SUKU (dari controller) =====
        // $quizzesByTribe: Collection map [ 'Aceh' => Quiz, 'Batak' => Quiz, 'Minangkabau' => Quiz, '__general__' => Quiz ]
        // $generalIslandQuiz: Quiz fallback utk pulau jika tribe spesifik belum ada
        $quizzesByTribe = $quizzesByTribe ?? collect();
        $generalIslandQuiz = $generalIslandQuiz ?? null;

        $acehQuiz  = $quizzesByTribe['Aceh'] ?? null;
        $batakQuiz = $quizzesByTribe['Batak'] ?? null;
        $minangQuiz = $quizzesByTribe['Minangkabau'] ?? null;
    @endphp

    {{-- WRAPPER SUMATERA --}}
    <section class="relative z-[10] py-12 sm:py-16 px-4 sm:px-6 bg-[var(--bg-body)] text-[var(--txt-body)]">

        {{-- CSS kecil khusus tab suku + timeline history --}}
        @include('islands.partials.tribe-styles')

        <div class="max-w-5xl mx-auto space-y-10">

            {{-- PILIHAN SUKU (TABS) --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--muted)] mb-1">
                        Pilih Suku di Sumatera
                    </p>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Eksplorasi Budaya per Suku
                    </h2>
                </div>

                <div class="inline-flex gap-2 bg-[color-mix(in_srgb,var(--bg-body)_80%,#e5e7eb_20%)] p-1.5 rounded-full">
                    <button type="button" class="tribe-tab is-active" data-tribe-tab="aceh">
                        Aceh
                    </button>
                    <button type="button" class="tribe-tab" data-tribe-tab="batak">
                        Batak
                    </button>
                    <button type="button" class="tribe-tab" data-tribe-tab="minang">
                        Minangkabau
                    </button>
                </div>
            </div>

            {{-- SEMUA SECTION DIBUNGKUS, ISINYA GANTI BERDASARKAN SUKU --}}
            <div class="space-y-12" id="suku-wrapper">

                {{-- ABOUT + HISTORY DINAMIS --}}
                <section id="about" class="space-y-3">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Tentang Suku di Sumatera
                    </h2>

                    {{-- Aceh --}}
                    <div data-tribe-panel="aceh">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Aceh dikenal sebagai masyarakat pesisir yang religius dan tangguh. Mereka
                            banyak bermukim di wilayah Aceh yang disebut sebagai <span class="italic">Serambi Mekkah</span>,
                            dengan sejarah panjang perdagangan, pendidikan Islam, dan perlawanan terhadap kolonialisme.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Identitas Aceh tampak kuat melalui bahasa, seni tari seperti Saman dan Seudati,
                            hingga tradisi adat yang diatur dalam nilai-nilai keislaman.
                        </p>

                        {{-- TIMELINE HISTORY: ACEH (TEMPLATE BARU) --}}
                        @if($acehHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Aceh</h3>
                                    <p class="history-subtitle">
                                        Rangkaian peristiwa penting yang membentuk identitas budaya dan sejarah Suku Aceh.
                                    </p>

                                    <div class="timeline">
                                        @foreach($acehHistories as $item)
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
                            <p class="history-empty">
                                Belum ada data sejarah Aceh yang diinput dari admin.
                            </p>
                        @endif
                    </div>

                    {{-- Batak --}}
                    <div class="hidden" data-tribe-panel="batak">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Batak terutama mendiami kawasan Sumatera Utara, seperti sekitar Danau Toba.
                            Mereka memiliki sistem marga yang kuat serta tradisi kekeluargaan yang sangat erat.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Kebudayaan Batak dikenali lewat rumah adat beratap tinggi, gondang (musik tradisional),
                            serta aksara Batak yang unik dan terus diupayakan pelestariannya.
                        </p>

                        {{-- TIMELINE HISTORY: BATAK (TEMPLATE BARU) --}}
                        @if($batakHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Batak</h3>
                                    <p class="history-subtitle">
                                        Jejak kerajaan, adat istiadat, dan peristiwa penting yang membentuk budaya Batak.
                                    </p>

                                    <div class="timeline">
                                        @foreach($batakHistories as $item)
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
                            <p class="history-empty">
                                Belum ada data sejarah Batak yang diinput dari admin.
                            </p>
                        @endif
                    </div>

                    {{-- Minangkabau --}}
                    <div class="hidden" data-tribe-panel="minang">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Suku Minangkabau bermukim terutama di Sumatera Barat dan dikenal dengan sistem
                            kekerabatan matrilineal, di mana garis keturunan ditarik dari pihak ibu.
                        </p>
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Falsafah hidup <span class="italic">adat basandi syarak, syarak basandi Kitabullah</span>
                            menjadi dasar kehidupan sosial, adat, dan seni Minangkabau, termasuk arsitektur rumah gadang.
                        </p>

                        {{-- TIMELINE HISTORY: MINANGKABAU (TEMPLATE BARU) --}}
                        @if($minangHistories->count())
                            <section class="history-section">
                                <div class="history-container">
                                    <h3 class="history-title">Sejarah Suku Minangkabau</h3>
                                    <p class="history-subtitle">
                                        Perkembangan adat, falsafah hidup, dan peristiwa sejarah penting di Ranah Minang.
                                    </p>

                                    <div class="timeline">
                                        @foreach($minangHistories as $item)
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
                            <p class="history-empty">
                                Belum ada data sejarah Minangkabau yang diinput dari admin.
                            </p>
                        @endif
                    </div>
                </section>

                {{-- STATISTIK --}}
                <section id="stats" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Statistik Singkat
                    </h2>

                    {{-- Aceh --}}
                    <div data-tribe-panel="aceh" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Wilayah Utama</p>
                            <p class="text-lg font-semibold mt-1">Provinsi Aceh</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Bahasa</p>
                            <p class="text-lg font-semibold mt-1">Bahasa Aceh & dialek lokal</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Ikon Budaya</p>
                            <p class="text-lg font-semibold mt-1">Tari Saman, Seudati</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Julukan</p>
                            <p class="text-lg font-semibold mt-1">Serambi Mekkah</p>
                        </div>
                    </div>

                    {{-- Batak --}}
                    <div data-tribe-panel="batak" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm hidden">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Wilayah Utama</p>
                            <p class="text-lg font-semibold mt-1">Sekitar Danau Toba</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Sub-Suku</p>
                            <p class="text-lg font-semibold mt-1">Toba, Karo, Simalungun, dsb.</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Bahasa</p>
                            <p class="text-lg font-semibold mt-1">Bahasa Batak (beragam dialek)</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Sistem Sosial</p>
                            <p class="text-lg font-semibold mt-1">Marga & Dalihan Na Tolu</p>
                        </div>
                    </div>

                    {{-- Minangkabau --}}
                    <div data-tribe-panel="minang" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm hidden">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Wilayah Utama</p>
                            <p class="text-lg font-semibold mt-1">Sumatera Barat</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Sistem Kekerabatan</p>
                            <p class="text-lg font-semibold mt-1">Matrilineal</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Bahasa</p>
                            <p class="text-lg font-semibold mt-1">Bahasa Minangkabau</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Ikon Budaya</p>
                            <p class="text-lg font-semibold mt-1">Rumah Gadang, Randai</p>
                        </div>
                    </div>

                    <p class="text-xs sm:text-sm text-[var(--muted)]">
                        Angka dan informasi di atas masih contoh statis, bisa diganti dengan data resmi kapan saja.
                    </p>
                </section>

                {{-- DESTINASI --}}
                <section id="destinations" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Destinasi Budaya
                    </h2>

                    {{-- Aceh --}}
                    <div data-tribe-panel="aceh" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Banda Aceh & Masjid Raya Baiturrahman</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Pusat sejarah dan budaya Aceh dengan masjid ikonik yang menjadi simbol keteguhan masyarakat.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Kota Banda Aceh</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Sabang & Nol Kilometer</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Gugusan pulau dengan laut jernih dan titik nol kilometer Indonesia di Pulau Weh.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Sabang</p>
                        </div>
                    </div>

                    {{-- Batak --}}
                    <div data-tribe-panel="batak" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Danau Toba & Pulau Samosir</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Danau vulkanik raksasa dengan desa-desa Batak, makam raja, dan pertunjukan musik tradisional.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Sumatera Utara</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Berastagi</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Kawasan sejuk di kaki gunung dengan pasar buah, budaya Karo, dan pemandangan gunung berapi.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Kabupaten Karo</p>
                        </div>
                    </div>

                    {{-- Minangkabau --}}
                    <div data-tribe-panel="minang" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Bukittinggi & Jam Gadang</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Kota pegunungan dengan ikon Jam Gadang, Ngarai Sianok, dan jejak sejarah kolonial.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Sumatera Barat</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Tanah Datar & Rumah Gadang</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Daerah dengan banyak rumah gadang tradisional dan upacara adat Minangkabau.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Tanah Datar</p>
                        </div>
                    </div>
                </section>

                {{-- KULINER --}}
                <section id="foods" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Kuliner Khas
                    </h2>

                    {{-- Aceh --}}
                    <div data-tribe-panel="aceh" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Mie Aceh</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Mie tebal dengan bumbu rempah kuat, biasa disajikan dengan daging atau seafood.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Banda Aceh & sekitarnya</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Kopi Gayo</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Kopi arabika berkualitas tinggi dari dataran tinggi Gayo, terkenal hingga mancanegara.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Aceh Tengah</p>
                        </div>
                    </div>

                    {{-- Batak --}}
                    <div data-tribe-panel="batak" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Arsik</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Ikan mas bumbu kuning khas Batak yang kaya rempah dan biasa disajikan pada upacara adat.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Sekitar Danau Toba</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Naniura</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Ikan mentah yang dimarinasi bumbu asam dan rempah, sering disebut “sushi Batak”.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Tapanuli</p>
                        </div>
                    </div>

                    {{-- Minangkabau --}}
                    <div data-tribe-panel="minang" class="grid grid-cols-1 sm:grid-cols-2 gap-4 hidden">
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Rendang</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Masakan daging dengan santan dan rempah yang dimasak lama hingga kering dan pekat.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Minangkabau</p>
                        </div>
                        <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                            <h3 class="text-sm sm:text-base font-semibold mb-1">Sate Padang</h3>
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                                Sate dengan kuah kental berempah, berbeda dari sate pada umumnya di Indonesia.
                            </p>
                            <p class="text-[11px] text-[var(--muted)]">📍 Padang & sekitarnya</p>
                        </div>
                    </div>
                </section>

                {{-- WARISAN --}}
                <section id="warisan" class="space-y-4">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Warisan & Sejarah
                    </h2>

                    {{-- Aceh --}}
                    <div data-tribe-panel="aceh" class="space-y-2">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Sejarah Aceh dipenuhi kisah kerajaan Islam, hubungan dagang internasional, serta perlawanan
                            terhadap penjajahan. Banyak manuskrip keagamaan dan adat Aceh yang masih disimpan hingga kini.
                        </p>
                        <ul class="text-sm text-[var(--muted)] list-disc pl-5 space-y-1">
                            <li>Kesultanan Aceh Darussalam sebagai pusat ilmu dan perdagangan.</li>
                            <li>Tradisi tarekat dan pendidikan dayah (pesantren tradisional).</li>
                            <li>Tari Saman yang telah diakui sebagai Warisan Budaya oleh UNESCO.</li>
                        </ul>
                    </div>

                    {{-- Batak --}}
                    <div data-tribe-panel="batak" class="space-y-2 hidden">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Warisan Batak tercermin pada ukiran gorga, rumah adat, serta naskah-naskah kuno beraksara Batak
                            yang berisi doa, hukum adat, dan catatan sejarah.
                        </p>
                        <ul class="text-sm text-[var(--muted)] list-disc pl-5 space-y-1">
                            <li>Sistem Dalihan Na Tolu sebagai dasar hubungan sosial.</li>
                            <li>Upacara adat seperti mangokkal holi dan pesta pernikahan adat.</li>
                            <li>Alat musik tradisional gondang dan ulos sebagai simbol kasih sayang.</li>
                        </ul>
                    </div>

                    {{-- Minangkabau --}}
                    <div data-tribe-panel="minang" class="space-y-2 hidden">
                        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                            Warisan Minangkabau kaya akan sastra lisan seperti pantun dan kaba, serta arsitektur
                            rumah gadang yang unik dengan atap bergonjong.
                        </p>
                        <ul class="text-sm text-[var(--muted)] list-disc pl-5 space-y-1">
                            <li>Falsafah hidup adat basandi syarak, syarak basandi Kitabullah.</li>
                            <li>Tradisi merantau yang membentuk jaringan Minang di berbagai daerah.</li>
                            <li>Upacara adat seperti batagak penghulu dan perkawinan adat Minangkabau.</li>
                        </ul>
                    </div>
                </section>

<section id="quiz" class="space-y-4">
  <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold"> Kuis Mini </h2>

  <div data-tribe-panel="aceh" class="space-y-4">
    @include('islands.partials.quiz-by-tribe', [
      'tribeName' => 'Aceh',
      'quiz' => $acehQuiz,
      'fallback' => $generalIslandQuiz,
    ])
  </div>

  <div data-tribe-panel="batak" class="space-y-4 hidden">
    @include('islands.partials.quiz-by-tribe', [
      'tribeName' => 'Batak',
      'quiz' => $batakQuiz,
      'fallback' => $generalIslandQuiz,
    ])
  </div>

  <div data-tribe-panel="minang" class="space-y-4 hidden">
    @include('islands.partials.quiz-by-tribe', [
      'tribeName' => 'Minangkabau',
      'quiz' => $minangQuiz,
      'fallback' => $generalIslandQuiz,
    ])
  </div>
</section>



        {{-- SCRIPT KECIL UNTUK GANTI TAB SUKU --}}
        @include('islands.partials.tribe-tabs-script')

    </section>
@endsection
