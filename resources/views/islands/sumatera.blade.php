{{-- resources/views/islands/sumatera.blade.php --}}
@extends('layouts.app')

@section('title', ($selectedIsland->title ?? $selectedIsland->name ?? 'Pulau Sumatera') . ' – Lentara')

@section('content')
    {{-- HERO shared --}}
    @include('partials.landing-hero')

    {{-- Section utama Sumatera: sekarang pakai CSS variable (bg & text) --}}
    <section
        class="relative z-[10] py-12 sm:py-16 px-4 sm:px-6 bg-[var(--bg-body)] text-[var(--txt-body)]">
        <div class="max-w-5xl mx-auto space-y-12">

            {{-- ABOUT PULAU --}}
            <section id="about">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Tentang Pulau Sumatera
                </h2>
                <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                    Sumatera adalah salah satu pulau terbesar di Indonesia yang terkenal dengan kekayaan alam,
                    budaya, serta kuliner khasnya. Dari Aceh hingga Lampung, setiap daerah memiliki tradisi dan
                    cerita yang unik.
                </p>
            </section>

            {{-- STATISTIK (STATIS) --}}
            <section id="stats">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Statistik Pulau Sumatera
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                        <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Provinsi</p>
                        <p class="text-lg font-semibold mt-1">10+</p>
                    </div>
                    <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                        <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Suku Bangsa</p>
                        <p class="text-lg font-semibold mt-1">Puluhan suku</p>
                    </div>
                    <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                        <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Bahasa Daerah</p>
                        <p class="text-lg font-semibold mt-1">Beragam bahasa lokal</p>
                    </div>
                    <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                        <p class="text-xs uppercase tracking-wide text-[var(--muted)]">Ikon Wisata</p>
                        <p class="text-lg font-semibold mt-1">Danau Toba, dsb.</p>
                    </div>
                </div>

                <p class="text-xs sm:text-sm text-[var(--muted)] mt-3">
                    Angka di atas hanya contoh statis. Nanti bisa diganti dengan data asli jika sudah siap.
                </p>
            </section>

            {{-- DESTINASI --}}
            <section id="destinations">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Destinasi Populer di Sumatera
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                        <h3 class="text-sm sm:text-base font-semibold mb-1">Danau Toba</h3>
                        <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                            Danau vulkanik terbesar di dunia yang dikelilingi pemandangan indah dan budaya Batak.
                        </p>
                        <p class="text-[11px] text-[var(--muted)]">📍 Sumatera Utara</p>
                    </div>

                    <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                        <h3 class="text-sm sm:text-base font-semibold mb-1">Bukittinggi</h3>
                        <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                            Kota sejuk dengan Jam Gadang, Ngarai Sianok, dan kekayaan budaya Minangkabau.
                        </p>
                        <p class="text-[11px] text-[var(--muted)]">📍 Sumatera Barat</p>
                    </div>

                    <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                        <h3 class="text-sm sm:text-base font-semibold mb-1">Sabang</h3>
                        <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                            Destinasi bahari dengan laut jernih dan titik nol kilometer Indonesia.
                        </p>
                        <p class="text-[11px] text-[var(--muted)]">📍 Aceh</p>
                    </div>
                </div>
            </section>

            {{-- KULINER --}}
            <section id="foods">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Kuliner Khas Sumatera
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                        <h3 class="text-sm sm:text-base font-semibold mb-1">Rendang</h3>
                        <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                            Masakan daging khas Minangkabau yang dimasak dengan bumbu rempah kaya rasa.
                        </p>
                        <p class="text-[11px] text-[var(--muted)]">📍 Sumatera Barat</p>
                    </div>
                    <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                        <h3 class="text-sm sm:text-base font-semibold mb-1">Mie Aceh</h3>
                        <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                            Mie dengan bumbu rempah kuat, disajikan dengan daging atau seafood.
                        </p>
                        <p class="text-[11px] text-[var(--muted)]">📍 Aceh</p>
                    </div>
                    <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                        <h3 class="text-sm sm:text-base font-semibold mb-1">Pempek</h3>
                        <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                            Olahan ikan dan tepung yang disajikan dengan kuah cuko khas Palembang.
                        </p>
                        <p class="text-[11px] text-[var(--muted)]">📍 Sumatera Selatan</p>
                    </div>
                </div>
            </section>

            {{-- WARISAN --}}
            <section id="warisan">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Warisan & Sejarah Sumatera
                </h2>
                <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed mb-4">
                    Sejak masa kerajaan Sriwijaya hingga kolonial, Sumatera menjadi salah satu pusat perdagangan
                    dan penyebaran budaya di Nusantara. Banyak tradisi lisan, tarian, dan upacara adat yang masih
                    dilestarikan hingga sekarang.
                </p>

                <ul class="text-sm text-[var(--muted)] list-disc pl-5 space-y-1">
                    <li>Kerajaan Sriwijaya di Sumatera Selatan</li>
                    <li>Sejarah Minangkabau dengan sistem kekerabatan matrilineal</li>
                    <li>Warisan budaya Aceh sebagai Serambi Mekkah</li>
                </ul>
            </section>

            {{-- KUIS (STATIS) --}}
            <section id="quiz">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Kuis Pulau Sumatera
                </h2>

                <div class="space-y-4 text-sm">
                    <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                        <p class="font-semibold mb-2">
                            1. Danau vulkanik terbesar di dunia yang ada di Sumatera adalah...
                        </p>
                        <ul class="space-y-1 text-xs sm:text-sm text-[var(--muted)]">
                            <li>A. Danau Toba</li>
                            <li>B. Danau Maninjau</li>
                            <li>C. Danau Singkarak</li>
                            <li>D. Danau Ranau</li>
                        </ul>
                    </div>

                    <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                        <p class="font-semibold mb-2">
                            2. Rendang berasal dari budaya...
                        </p>
                        <ul class="space-y-1 text-xs sm:text-sm text-[var(--muted)]">
                            <li>A. Batak</li>
                            <li>B. Minangkabau</li>
                            <li>C. Melayu</li>
                            <li>D. Aceh</li>
                        </ul>
                    </div>
                </div>
            </section>

        </div>
    </section>
@endsection
