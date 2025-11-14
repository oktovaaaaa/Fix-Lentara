{{-- resources/views/admin/stats/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Admin - Statistik Pulau')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4 text-slate-100">
    <h1 class="text-2xl font-bold mb-4">Statistik Pulau</h1>

    @if(session('status'))
        <div class="mb-4 rounded bg-emerald-600/80 px-3 py-2 text-sm">
            {{ session('status') }}
        </div>
    @endif

    {{-- PICKER PULAU --}}
    <div class="mb-6">
        <label class="block text-xs text-slate-400 mb-1">Pilih Pulau</label>
        <form method="GET" action="{{ route('admin.stats.index') }}">
            <select
                name="island"
                onchange="this.form.submit()"
                class="bg-slate-900 border border-slate-700 rounded px-3 py-2 text-sm"
            >
                @foreach($islands as $island)
                    <option value="{{ $island->slug }}" {{ $island->id === $activeIsland->id ? 'selected' : '' }}>
                        {{ $island->name }}
                    </option>
                @endforeach
            </select>
        </form>
        <p class="text-xs text-slate-500 mt-1">
            Pulau aktif: <span class="font-semibold">{{ $activeIsland->name }}</span>
        </p>
    </div>

    {{-- === CARD: JUMLAH PENDUDUK (COLLAPSIBLE) === --}}
    <div class="mb-6 border border-slate-700 rounded-xl bg-slate-900/80 overflow-hidden"
         data-card>
        <div class="flex items-center justify-between gap-4 px-4 py-3 bg-slate-800/70"
             data-card-header>
            <div>
                <h2 class="text-sm sm:text-base font-semibold">Jumlah Penduduk</h2>
                <p class="text-[11px] text-slate-400">
                    Angka ini akan tampil sebagai teks di halaman pulau.
                </p>
            </div>

            <button type="button"
                    class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-900/70 text-slate-200 text-xs hover:bg-slate-700"
                    aria-label="Collapse"
                    data-card-toggle>
                ▾
            </button>
        </div>

        <div class="px-4 py-4 flex items-center justify-between gap-4" data-card-body>
            <div class="text-sm">
                <p class="text-xs text-slate-400 mb-1">Total penduduk (perkiraan)</p>
                <p class="text-2xl font-semibold">
                    {{ $activeIsland->population ? number_format($activeIsland->population, 0, ',', '.') : '—' }}
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('admin.stats.population.update', $activeIsland) }}"
                class="flex items-end gap-2"
            >
                @csrf
                <div class="flex flex-col">
                    <label class="text-[11px] text-slate-400 mb-1" for="population_input">
                        Ubah jumlah penduduk
                    </label>
                    <input
                        id="population_input"
                        type="number"
                        name="population"
                        value="{{ old('population', $activeIsland->population) }}"
                        class="bg-slate-950 border border-slate-700 rounded px-2 py-1 text-sm w-40 text-right"
                        placeholder="0"
                    >
                </div>

                <button
                    type="submit"
                    class="text-xs px-3 py-1.5 rounded-lg bg-amber-500 text-black hover:bg-amber-400"
                >
                    Simpan
                </button>
            </form>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- === CARD: AGAMA === --}}
        <div class="border border-slate-700 rounded-xl bg-slate-900/80 overflow-hidden" data-card>
            {{-- header --}}
            <div class="flex items-center justify-between px-4 py-3 bg-slate-800/70" data-card-header>
                <div>
                    <h2 class="text-sm sm:text-base font-semibold">Agama (Pie Chart)</h2>
                    <p class="text-[11px] text-slate-400">
                        Distribusi pemeluk agama di pulau {{ $activeIsland->name }}.
                    </p>
                </div>
                <button type="button"
                        class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-900/70 text-slate-200 text-xs hover:bg-slate-700"
                        aria-label="Collapse"
                        data-card-toggle>
                    ▾
                </button>
            </div>

            {{-- body --}}
            <div class="px-4 pb-4 pt-3 space-y-3" data-card-body>
                <div class="space-y-2 mb-2 max-h-40 overflow-auto pr-1">
                    @forelse($religions as $row)
                        <div class="flex items-center justify-between text-xs bg-slate-800/80 rounded px-2 py-1.5">
                            <span>{{ $row->label }}</span>
                            <div class="flex items-center gap-2">
                                <span>{{ $row->percentage }}%</span>
                                <form method="POST"
                                      action="{{ route('admin.stats.demographics.destroy', [$activeIsland, $row]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500">Belum ada data agama.</p>
                    @endforelse
                </div>

                <form
                    method="POST"
                    action="{{ route('admin.stats.demographics.store', $activeIsland) }}"
                    class="space-y-2 text-xs"
                >
                    @csrf
                    <input type="hidden" name="type" value="religion">

                    <div class="flex gap-2">
                        <input
                            type="text"
                            name="label"
                            class="bg-slate-950 border border-slate-700 rounded px-2 py-1 flex-1"
                            placeholder="Nama agama (mis. Islam)"
                            required
                        >
                        <input
                            type="number"
                            step="0.01"
                            name="percentage"
                            class="bg-slate-950 border border-slate-700 rounded px-2 py-1 w-24 text-right"
                            placeholder="%"
                            required
                        >
                    </div>
                    <div class="flex justify-between items-center gap-2">
                        <input
                            type="number"
                            name="order"
                            class="bg-slate-950 border border-slate-700 rounded px-2 py-1 w-20 text-right"
                            placeholder="Urut"
                        >
                        <button
                            type="submit"
                            class="px-3 py-1.5 rounded bg-emerald-500 text-black hover:bg-emerald-400"
                        >
                            + Tambah Agama
                        </button>
                    </div>
                </form>

                <div class="mt-3 h-64">
                    <canvas id="religionChart"></canvas>
                </div>
            </div>
        </div>

        {{-- === CARD: SUKU === --}}
        <div class="border border-slate-700 rounded-xl bg-slate-900/80 overflow-hidden" data-card>
            <div class="flex items-center justify-between px-4 py-3 bg-slate-800/70" data-card-header>
                <div>
                    <h2 class="text-sm sm:text-base font-semibold">Suku (Donut Chart)</h2>
                    <p class="text-[11px] text-slate-400">
                        Komposisi suku di pulau {{ $activeIsland->name }}.
                    </p>
                </div>
                <button type="button"
                        class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-900/70 text-slate-200 text-xs hover:bg-slate-700"
                        aria-label="Collapse"
                        data-card-toggle>
                    ▾
                </button>
            </div>

            <div class="px-4 pb-4 pt-3 space-y-3" data-card-body>
                <div class="space-y-2 mb-2 max-h-40 overflow-auto pr-1">
                    @forelse($ethnicities as $row)
                        <div class="flex items-center justify-between text-xs bg-slate-800/80 rounded px-2 py-1.5">
                            <span>{{ $row->label }}</span>
                            <div class="flex items-center gap-2">
                                <span>{{ $row->percentage }}%</span>
                                <form method="POST"
                                      action="{{ route('admin.stats.demographics.destroy', [$activeIsland, $row]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500">Belum ada data suku.</p>
                    @endforelse
                </div>

                <form
                    method="POST"
                    action="{{ route('admin.stats.demographics.store', $activeIsland) }}"
                    class="space-y-2 text-xs"
                >
                    @csrf
                    <input type="hidden" name="type" value="ethnicity">

                    <div class="flex gap-2">
                        <input
                            type="text"
                            name="label"
                            class="bg-slate-950 border border-slate-700 rounded px-2 py-1 flex-1"
                            placeholder="Nama suku (mis. Batak)"
                            required
                        >
                        <input
                            type="number"
                            step="0.01"
                            name="percentage"
                            class="bg-slate-950 border border-slate-700 rounded px-2 py-1 w-24 text-right"
                            placeholder="%"
                            required
                        >
                    </div>
                    <div class="flex justify-between items-center gap-2">
                        <input
                            type="number"
                            name="order"
                            class="bg-slate-950 border border-slate-700 rounded px-2 py-1 w-20 text-right"
                            placeholder="Urut"
                        >
                        <button
                            type="submit"
                            class="px-3 py-1.5 rounded bg-emerald-500 text-black hover:bg-emerald-400"
                        >
                            + Tambah Suku
                        </button>
                    </div>
                </form>

                <div class="mt-3 h-64">
                    <canvas id="ethnicityChart"></canvas>
                </div>
            </div>
        </div>

        {{-- === CARD: BAHASA === --}}
        <div class="border border-slate-700 rounded-xl bg-slate-900/80 overflow-hidden md:col-span-2" data-card>
            <div class="flex items-center justify-between px-4 py-3 bg-slate-800/70" data-card-header>
                <div>
                    <h2 class="text-sm sm:text-base font-semibold">Bahasa Daerah (Bar Chart)</h2>
                    <p class="text-[11px] text-slate-400">
                        Persentase penutur bahasa daerah di pulau {{ $activeIsland->name }}.
                    </p>
                </div>
                <button type="button"
                        class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-900/70 text-slate-200 text-xs hover:bg-slate-700"
                        aria-label="Collapse"
                        data-card-toggle>
                    ▾
                </button>
            </div>

            <div class="px-4 pb-4 pt-3 space-y-3" data-card-body>
                <div class="space-y-2 mb-2 max-h-40 overflow-auto pr-1">
                    @forelse($languages as $row)
                        <div class="flex items-center justify-between text-xs bg-slate-800/80 rounded px-2 py-1.5">
                            <span>{{ $row->label }}</span>
                            <div class="flex items-center gap-2">
                                <span>{{ $row->percentage }}%</span>
                                <form method="POST"
                                      action="{{ route('admin.stats.demographics.destroy', [$activeIsland, $row]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500">Belum ada data bahasa.</p>
                    @endforelse
                </div>

                <form
                    method="POST"
                    action="{{ route('admin.stats.demographics.store', $activeIsland) }}"
                    class="space-y-2 text-xs"
                >
                    @csrf
                    <input type="hidden" name="type" value="language">

                    <div class="flex gap-2">
                        <input
                            type="text"
                            name="label"
                            class="bg-slate-950 border border-slate-700 rounded px-2 py-1 flex-1"
                            placeholder="Nama bahasa (mis. Bahasa Minang)"
                            required
                        >
                        <input
                            type="number"
                            step="0.01"
                            name="percentage"
                            class="bg-slate-950 border border-slate-700 rounded px-2 py-1 w-24 text-right"
                            placeholder="%"
                            required
                        >
                    </div>
                    <div class="flex justify-between items-center gap-2">
                        <input
                            type="number"
                            name="order"
                            class="bg-slate-950 border border-slate-700 rounded px-2 py-1 w-20 text-right"
                            placeholder="Urut"
                        >
                        <button
                            type="submit"
                            class="px-3 py-1.5 rounded bg-emerald-500 text-black hover:bg-emerald-400"
                        >
                            + Tambah Bahasa
                        </button>
                    </div>
                </form>

                <div class="mt-3 h-72">
                    <canvas id="languageChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ==== DATA DARI LARAVEL ====
        const religionData  = @json($religions->map(fn($r) => ['label' => $r->label, 'percentage' => (float)$r->percentage]));
        const ethnicityData = @json($ethnicities->map(fn($e) => ['label' => $e->label, 'percentage' => (float)$e->percentage]));
        const languageData  = @json($languages->map(fn($l) => ['label' => $l->label, 'percentage' => (float)$l->percentage]));

        function extractLabels(data) { return data.map(d => d.label); }
        function extractValues(data) { return data.map(d => d.percentage); }

        document.addEventListener('DOMContentLoaded', function () {
            // ==== COLLAPSIBLE CARD LOGIC ====
            document.querySelectorAll('[data-card]').forEach(card => {
                const toggleBtn = card.querySelector('[data-card-toggle]');
                const body      = card.querySelector('[data-card-body]');

                if (!toggleBtn || !body) return;

                toggleBtn.addEventListener('click', () => {
                    body.classList.toggle('hidden');
                    // ubah simbol panah: ▾ / ▸
                    toggleBtn.textContent = body.classList.contains('hidden') ? '▸' : '▾';
                });
            });

            // ==== CHARTS ====
            const pieOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 16,
                            color: '#e5e7eb'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return `${ctx.label}: ${ctx.parsed}%`;
                            }
                        }
                    }
                }
            };

            const donutOptions = Object.assign({}, pieOptions, { cutout: '55%' });

            // AGAMA - PIE
            if (religionData.length && document.getElementById('religionChart')) {
                new Chart(
                    document.getElementById('religionChart').getContext('2d'),
                    {
                        type: 'pie',
                        data: {
                            labels: extractLabels(religionData),
                            datasets: [{
                                data: extractValues(religionData),
                                backgroundColor: [
                                    '#f97373','#22c55e','#3b82f6','#eab308','#a855f7','#f97316'
                                ],
                                borderColor: '#020617',
                                borderWidth: 2
                            }]
                        },
                        options: pieOptions
                    }
                );
            }

            // SUKU - DONUT
            if (ethnicityData.length && document.getElementById('ethnicityChart')) {
                new Chart(
                    document.getElementById('ethnicityChart').getContext('2d'),
                    {
                        type: 'doughnut',
                        data: {
                            labels: extractLabels(ethnicityData),
                            datasets: [{
                                data: extractValues(ethnicityData),
                                backgroundColor: [
                                    '#3b82f6','#f97373','#22c55e','#eab308','#a855f7','#f97316'
                                ],
                                borderColor: '#020617',
                                borderWidth: 2
                            }]
                        },
                        options: donutOptions
                    }
                );
            }

            // BAHASA - BAR
            if (languageData.length && document.getElementById('languageChart')) {
                new Chart(
                    document.getElementById('languageChart').getContext('2d'),
                    {
                        type: 'bar',
                        data: {
                            labels: extractLabels(languageData),
                            datasets: [{
                                label: 'Persentase penutur',
                                data: extractValues(languageData),
                                backgroundColor: 'rgba(56,189,248,0.6)',
                                borderColor: 'rgba(56,189,248,1)',
                                borderWidth: 1,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                x: {
                                    ticks: { color: '#e5e7eb', font: { size: 10 } },
                                    grid: { display: false }
                                },
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    ticks: {
                                        color: '#e5e7eb',
                                        callback: value => value + '%'
                                    },
                                    grid: {
                                        color: 'rgba(148,163,184,0.2)'
                                    }
                                }
                            }
                        }
                    }
                );
            }
        });
    </script>
@endpush
