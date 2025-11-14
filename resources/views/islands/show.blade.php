{{-- resources/views/islands/show.blade.php --}}
@extends('layouts.app')

@section('title', ($selectedIsland->title ?? $selectedIsland->name) . ' – Lentara')

@php
    $featuresByType = $featuresByType ?? [];
@endphp

@section('content')
    {{-- HERO + ANIMASI KARTU (mode pulau, karena $selectedIsland di-pass) --}}
    @include('partials.landing-hero')

    {{-- DETAIL PULAU --}}
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

            {{-- STATISTIK ISLAND --}}
            <div id="stats" class="mt-10">
                <div class="bg-white/5 border border-white/10 rounded-3xl p-6 sm:p-8 shadow-[0_18px_45px_rgba(0,0,0,0.45)] space-y-6">

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                                Statistik {{ $selectedIsland->title ?? $selectedIsland->name }}
                            </h2>
                            <p class="text-xs sm:text-sm text-white/60 mt-1">
                                Gambaran singkat penduduk, agama, suku, dan bahasa di pulau ini.
                            </p>
                        </div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-black/30 border border-white/10 text-xs sm:text-sm">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Data budaya aktif</span>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="rounded-2xl bg-black/40 border border-white/10 p-4 flex flex-col justify-between">
                            <p class="text-[10px] uppercase tracking-[0.22em] text-white/50">
                                Jumlah Penduduk
                            </p>
                            <p class="mt-2 text-2xl font-semibold">
                                {{ $selectedIsland->population ? number_format($selectedIsland->population, 0, ',', '.') : '—' }}
                            </p>
                            <p class="mt-1 text-[11px] text-white/40">
                                Perkiraan total penduduk di pulau ini.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-black/40 border border-white/10 p-4 flex flex-col justify-between">
                            <p class="text-[10px] uppercase tracking-[0.22em] text-white/50">
                                Ragam Agama
                            </p>
                            <p class="mt-2 text-2xl font-semibold">
                                {{ !empty($demographics['religion']) ? $demographics['religion']->count() : 0 }}
                            </p>
                            <p class="mt-1 text-[11px] text-white/40">
                                Jumlah agama yang tercatat di statistik.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-black/40 border border-white/10 p-4 flex flex-col justify-between">
                            <p class="text-[10px] uppercase tracking-[0.22em] text-white/50">
                                Ragam Suku
                            </p>
                            <p class="mt-2 text-2xl font-semibold">
                                {{ !empty($demographics['ethnicity']) ? $demographics['ethnicity']->count() : 0 }}
                            </p>
                            <p class="mt-1 text-[11px] text-white/40">
                                Suku utama yang mewakili kebudayaan lokal.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-black/40 border border-white/10 p-4 flex flex-col justify-between">
                            <p class="text-[10px] uppercase tracking-[0.22em] text-white/50">
                                Bahasa Daerah
                            </p>
                            <p class="mt-2 text-2xl font-semibold">
                                {{ !empty($demographics['language']) ? $demographics['language']->count() : 0 }}
                            </p>
                            <p class="mt-1 text-[11px] text-white/40">
                                Bahasa daerah yang umum digunakan.
                            </p>
                        </div>
                    </div>

                    <div class="grid lg:grid-cols-3 gap-4">
                        <div class="rounded-2xl bg-black/40 border border-white/10 p-4 flex flex-col">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-semibold">Komposisi Agama</h3>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300">
                                    Pie chart
                                </span>
                            </div>
                            <div class="flex-1 flex gap-3">
                                <div class="flex-1 h-40">
                                    <canvas id="islandReligionChart"></canvas>
                                </div>
                                <div class="w-28 text-[11px] space-y-1 hidden sm:block">
                                    @forelse($demographics['religion'] as $row)
                                        <div class="flex justify-between gap-1">
                                            <span class="truncate">{{ $row->label }}</span>
                                            <span>{{ $row->percentage }}%</span>
                                        </div>
                                    @empty
                                        <p class="text-white/50">Belum ada data.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-black/40 border border-white/10 p-4 flex flex-col">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-semibold">Sebaran Suku</h3>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300">
                                    Doughnut
                                </span>
                            </div>
                            <div class="flex-1 flex gap-3">
                                <div class="flex-1 h-40">
                                    <canvas id="islandEthnicityChart"></canvas>
                                </div>
                                <div class="w-28 text-[11px] space-y-1 hidden sm:block">
                                    @forelse($demographics['ethnicity'] as $row)
                                        <div class="flex justify-between gap-1">
                                            <span class="truncate">{{ $row->label }}</span>
                                            <span>{{ $row->percentage }}%</span>
                                        </div>
                                    @empty
                                        <p class="text-white/50">Belum ada data.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-black/40 border border-white/10 p-4 flex flex-col">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-semibold">Bahasa Daerah</h3>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-sky-500/20 text-sky-300">
                                    Bar chart
                                </span>
                            </div>
                            <div class="flex-1 h-40">
                                <canvas id="islandLanguageChart"></canvas>
                            </div>
                            <div class="mt-2 text-[11px] text-white/50">
                                @if(!empty($demographics['language']) && $demographics['language']->count())
                                    <span class="hidden sm:inline">
                                        Menampilkan persentase beberapa bahasa utama.
                                    </span>
                                @else
                                    Belum ada data bahasa.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DESTINASI --}}
            @if(!empty($featuresByType['destination']) && $featuresByType['destination']->count())
                <section id="destinations">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                        Destinasi Populer
                    </h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach($featuresByType['destination'] as $dest)
                            <div class="border border-white/10 rounded-2xl p-4 flex gap-3">
                                @if($dest->image_url)
                                    <img
                                        src="{{ $dest->image_url }}"
                                        alt="{{ $dest->title }}"
                                        class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg border border-white/10 flex-shrink-0"
                                    >
                                @endif
                                <div>
                                    <h3 class="text-sm sm:text-base font-semibold mb-1">
                                        {{ $dest->title }}
                                    </h3>
                                    @if($dest->description)
                                        <p class="text-xs sm:text-sm text-white/70 leading-relaxed">
                                            {{ $dest->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- MAKANAN KHAS --}}
            @if(!empty($featuresByType['food']) && $featuresByType['food']->count())
                <section id="foods">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                        Makanan Khas
                    </h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach($featuresByType['food'] as $food)
                            <div class="border border-white/10 rounded-2xl p-4 flex gap-3">
                                @if($food->image_url)
                                    <img
                                        src="{{ $food->image_url }}"
                                        alt="{{ $food->title }}"
                                        class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg border border-white/10 flex-shrink-0"
                                    >
                                @endif
                                <div>
                                    <h3 class="text-sm sm:text-base font-semibold mb-1">
                                        {{ $food->title }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-white/70 leading-relaxed">
                                        {{ $food->description }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- BUDAYA & TRADISI --}}
            @if(!empty($featuresByType['culture']) && $featuresByType['culture']->count())
                <section id="cultures">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                        Budaya & Tradisi
                    </h2>
                    <div class="space-y-4">
                        @foreach($featuresByType['culture'] as $culture)
                            <div class="border border-white/10 rounded-2xl p-4">
                                <h3 class="text-sm sm:text-base font-semibold mb-1">
                                    {{ $culture->title }}
                                </h3>
                                <p class="text-xs sm:text-sm text-white/70 leading-relaxed">
                                    {{ $culture->description }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- QUIZ PULAU --}}
            <section id="quiz">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Kuis {{ $selectedIsland->title ?? $selectedIsland->name }}
                </h2>
                <p class="text-sm sm:text-base text-white/80">
                    Area ini bisa berisi kuis khusus tentang {{ $selectedIsland->title ?? $selectedIsland->name }}
                    — misalnya tentang destinasi, makanan khas, atau budaya setempat.
                </p>
            </section>

        </div>
    </section>
@endsection

@push('scripts')
    {{-- Chart.js untuk statistik --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const religionRows  = @json($demographics['religion'] ?? []);
            const ethnicityRows = @json($demographics['ethnicity'] ?? []);
            const languageRows  = @json($demographics['language'] ?? []);

            function makeCircleChart(canvasId, rows, type = 'pie') {
                const canvas = document.getElementById(canvasId);
                if (!canvas || !rows.length) return;

                const ctx = canvas.getContext('2d');
                const labels = rows.map(r => r.label);
                const data   = rows.map(r => r.percentage);

                new Chart(ctx, {
                    type: type,
                    data: {
                        labels,
                        datasets: [{
                            data,
                            backgroundColor: [
                                '#FF6384', '#36A2EB', '#FFCE56',
                                '#4BC0C0', '#9966FF', '#FF9F40',
                                '#FFCD56', '#C9CBCF'
                            ],
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: type === 'doughnut' ? '60%' : 0,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: { usePointStyle: true }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => `${ctx.label}: ${ctx.parsed}%`
                                }
                            }
                        }
                    }
                });
            }

            function makeBarChart(canvasId, rows) {
                const canvas = document.getElementById(canvasId);
                if (!canvas || !rows.length) return;

                const ctx = canvas.getContext('2d');
                const labels = rows.map(r => r.label);
                const data   = rows.map(r => r.percentage);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            data,
                            label: 'Persentase',
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 10 }
                            }
                        }
                    }
                });
            }

            if (religionRows.length) {
                makeCircleChart('islandReligionChart', religionRows, 'pie');
            }

            if (ethnicityRows.length) {
                makeCircleChart('islandEthnicityChart', ethnicityRows, 'doughnut');
            }

            if (languageRows.length) {
                makeBarChart('islandLanguageChart', languageRows);
            }
        });
    </script>
@endpush
