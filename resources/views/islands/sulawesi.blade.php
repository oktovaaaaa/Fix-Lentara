@extends('layouts.app')

@section('title', ($selectedIsland->title ?? $selectedIsland->name) . ' – Lentara')

@php
    // variabel ini sudah dikirim dari controller
    $featuresByType = $featuresByType ?? [];
@endphp

@section('content')
    {{-- HERO + ANIMASI KARTU (shared) --}}
    @include('partials.landing-hero')

    {{-- CONTOH: kamu bisa tambah section khusus Sumatera di sini --}}
    {{-- <section class="bg-red-500 ...">Khusus Sumatera</section> --}}

    {{-- =================== DETAIL PULAU (boleh sama dengan default) =================== --}}
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

            {{-- … copy semua blok STATISTIK / DESTINASI / MAKANAN / BUDAYA / QUIZ
                 yang tadi aku buat di islands.show, atau versi kamu yang sekarang …
            --}}
            {{-- (boleh sama persis; bedanya sekarang file-nya khusus Sumatera) --}}

        </div>
    </section>
@endsection

{{-- script Chart.js sama seperti default --}}
@push('scripts')
    {{-- Chart.js + inisialisasi chart, sama seperti yang sudah kamu punya --}}
@endpush
