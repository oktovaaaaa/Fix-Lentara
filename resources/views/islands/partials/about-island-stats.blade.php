{{-- resources/views/islands/partials/about-islands-stats.blade.php --}}
@php
    // SAFETY DEFAULTS
    $selectedIsland     = $selectedIsland ?? null;

    $aboutIslandPage    = $aboutIslandPage ?? null;
    $aboutIslandItems   = $aboutIslandItems ?? collect();

    $demographics       = $demographics ?? ['religion'=>collect(),'ethnicity'=>collect(),'language'=>collect()];

    $labelSmall = $aboutIslandPage->label_small ?? ('MENGENAL ' . strtoupper($selectedIsland->subtitle ?? $selectedIsland->name ?? 'PULAU'));
    $heroTitle  = $aboutIslandPage->hero_title ?? ('Tentang ' . ($selectedIsland->subtitle ?? $selectedIsland->name ?? 'Pulau'));
    $heroDesc   = $aboutIslandPage->hero_description ?? null;
    $headerLink = $aboutIslandPage->more_link ?? null;

    $religions  = ($demographics['religion'] ?? collect())->sortByDesc('percentage')->values();
    $ethnicities= ($demographics['ethnicity'] ?? collect())->sortByDesc('percentage')->values();
    $languages  = ($demographics['language'] ?? collect())->sortByDesc('percentage')->values();

    // ---------- helper: build top-N + "Lainnya" ----------
    $buildTop = function($rows, $max = 12) {
        $rows = collect($rows)->filter(function($r){
            return isset($r->label) && $r->label !== '' && isset($r->percentage);
        })->values();

        if ($rows->count() <= $max) {
            return [
                'labels' => $rows->pluck('label')->values(),
                'data'   => $rows->pluck('percentage')->map(fn($v)=> (float)$v)->values(),
            ];
        }

        $top = $rows->take($max);
        $rest = $rows->slice($max);
        $restSum = (float) $rest->sum('percentage');

        return [
            'labels' => $top->pluck('label')->push('Lainnya')->values(),
            'data'   => $top->pluck('percentage')->map(fn($v)=> (float)$v)->push($restSum)->values(),
        ];
    };

    $ethTop = $buildTop($ethnicities, 12);
    $langTop= $buildTop($languages, 12);
    $relTop = $buildTop($religions, 8);

    $population = (int) ($selectedIsland?->population ?? 0);

    $hasEth = !empty($ethTop['labels']) && count($ethTop['labels']) > 0;
    $hasLang= !empty($langTop['labels']) && count($langTop['labels']) > 0;
    $hasRel = !empty($relTop['labels']) && count($relTop['labels']) > 0;
@endphp

<section id="about" class="py-12">
    <div class="max-w-6xl mx-auto space-y-10 px-4">

        {{-- HEADER (ABOUT PULAU) --}}
        <div class="text-center">
            <div class="inline-block text-xs tracking-[0.18em] uppercase font-semibold px-3 py-1 rounded"
                 style="color: var(--brand); background: color-mix(in srgb, var(--brand) 10%, transparent);">
                {{ $labelSmall }}
            </div>

            {{-- TITLE + DECORATION (HARUS SAMA PERSIS DENGAN HOME SYSTEM) --}}
            <div class="mt-3">
                <h2 class="neon-title">{{ $heroTitle }}</h2>
                <div class="title-decoration"></div>
            </div>

            @if($heroDesc)
                <p class="neon-subtitle">
                    {{ $heroDesc }}
                </p>
            @else
                {{-- kalau tidak ada subtitle dari admin, kita buat default supaya konsisten dengan HOME --}}
                <p class="neon-subtitle">
                    Ringkasan tentang pulau ini, termasuk gambaran umum dan poin-poin penting yang kamu input di admin.
                </p>
            @endif

            @if($headerLink)
                <div class="mt-4">
                    <a href="{{ $headerLink }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-full border"
                       style="border-color: color-mix(in srgb, var(--brand) 45%, transparent); color: var(--brand);">
                        Selengkapnya <span aria-hidden="true">→</span>
                    </a>
                </div>
            @endif
        </div>

        {{-- ITEMS --}}
        <div class="space-y-6">
            @forelse($aboutIslandItems as $it)
                @php
                    $title = $it->title ?: null;
                    $desc  = $it->description ?? '';
                    $img   = $it->image ?: null;
                    $link  = $it->more_link ?: null;

                    $pointsArr = method_exists($it, 'pointsArray') ? $it->pointsArray() : (is_string($it->points ?? null) ? preg_split("/\r\n|\n|\r/", trim($it->points)) : []);
                    $pointsArr = array_values(array_filter(array_map('trim', (array)$pointsArr), fn($x)=>$x!==''));
                    $hasPoints = !empty($pointsArr);
                    $hasImage  = !empty($img);
                @endphp

<div class="rounded-2xl p-4 sm:p-6 bg-transparent border-0">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-start">
                        {{-- Gambar (opsional) --}}
                        @if($hasImage)
                            <div class="rounded-xl overflow-hidden border border-[var(--line)]">
                                <img src="{{ $img }}" alt="{{ $title ?? 'Gambar' }}" class="w-full h-64 object-cover">
                            </div>
                        @endif

                        <div class="{{ $hasImage ? '' : 'md:col-span-2' }}">
                            @if($title)
                                <h3 class="text-lg font-semibold mb-2">{{ $title }}</h3>
                            @endif

                            <p class="text-sm text-[var(--muted)] leading-relaxed whitespace-pre-line">
                                {{ $desc }}
                            </p>

                            @if($hasPoints)
                                <div class="mt-4 space-y-2">
                                    @foreach($pointsArr as $p)
                                        <div class="flex gap-2 items-start text-sm">
                                            <span class="mt-0.5 inline-flex w-5 h-5 items-center justify-center rounded-full"
                                                  style="background: color-mix(in srgb, var(--brand) 25%, transparent); color: var(--brand);">
                                                ✓
                                            </span>
                                            <span class="text-[var(--txt-body)]/80">{{ $p }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($link)
                                <div class="mt-4">
                                    <a href="{{ $link }}" target="_blank" rel="noopener"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-full border"
                                       style="border-color: color-mix(in srgb, var(--brand) 45%, transparent); color: var(--brand);">
                                        Selengkapnya <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="border border-[var(--line)] rounded-2xl bg-[var(--card)] p-6 text-center">
                    <p class="text-sm text-[var(--muted)]">
                        Konten About untuk pulau ini belum diinput.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- ================= STATISTIK PULAU ================= --}}
        <section id="stats" class="py-4">

            {{-- TITLE + DECORATION + SUBTITLE (SAMA PERSIS DENGAN HOME SYSTEM) --}}
            <h2 class="neon-title">Statistik Pulau</h2>
            <div class="title-decoration"></div>
            <p class="neon-subtitle">
                Ringkasan demografi pulau: jumlah penduduk, serta komposisi suku, bahasa, dan agama berdasarkan data yang kamu input di admin.
            </p>

            {{-- 3 CARD UTAMA --}}
            <div class="grid gap-6 lg:grid-cols-3 mb-8 mt-6">
                {{-- POPULATION --}}
                <button type="button" class="stat-card stat-card--green" data-stat="population">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <div class="stat-number">
                                {{ $population > 0 ? number_format($population, 0, ',', '.') : '—' }}
                            </div>
                            <div class="stat-label">Jumlah Penduduk (perkiraan)</div>
                            <p class="mt-2">
                                Nilai ini kamu isi dari panel admin. Gunakan angka perkiraan terbaru untuk pulau ini.
                            </p>
                        </div>
                        <div class="opacity-90">
                            <svg viewBox="0 0 24 24" class="w-12 h-12" fill="white" opacity="0.9">
                                <rect x="3" y="10" width="4" height="9" rx="1" />
                                <rect x="10" y="7" width="4" height="12" rx="1" opacity="0.7" />
                                <rect x="17" y="4" width="4" height="15" rx="1" opacity="0.9" />
                            </svg>
                        </div>
                    </div>
                    <div class="stat-more">
                        Detail Info <span class="stat-more-icon">➜</span>
                    </div>
                </button>

                {{-- ETHNICITY COUNT --}}
                <button type="button" class="stat-card stat-card--purple" data-stat="ethnicity">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <div class="stat-number">{{ $ethnicities->count() }}</div>
                            <div class="stat-label">Data Suku Tercatat</div>
                            <p class="mt-2">
                                Komposisi suku (persentase) yang kamu input. Chart akan menampilkan top data + “Lainnya”.
                            </p>
                        </div>
                        <div class="opacity-90">
                            <svg viewBox="0 0 24 24" class="w-12 h-12" fill="white" opacity="0.9">
                                <circle cx="12" cy="7" r="3" />
                                <path d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6" opacity="0.75" />
                                <path d="M6 13c-1.2-1-2-2.3-2-4 0-3 2.4-5 5.4-5" opacity="0.6" />
                            </svg>
                        </div>
                    </div>
                    <div class="stat-more">
                        Detail Info <span class="stat-more-icon">➜</span>
                    </div>
                </button>

                {{-- LANGUAGE COUNT --}}
                <button type="button" class="stat-card stat-card--red" data-stat="language">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <div class="stat-number">{{ $languages->count() }}</div>
                            <div class="stat-label">Data Bahasa Tercatat</div>
                            <p class="mt-2">
                                Komposisi bahasa (persentase) yang kamu input. Cocok buat ringkasan demografi pulau.
                            </p>
                        </div>
                        <div class="opacity-90">
                            <svg viewBox="0 0 24 24" class="w-12 h-12" fill="white" opacity="0.9">
                                <path d="M4 5h16v10H7l-3 3V5z" />
                                <path d="M8 8h8" opacity="0.7"/>
                                <path d="M8 11h6" opacity="0.7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-more">
                        Detail Info <span class="stat-more-icon">➜</span>
                    </div>
                </button>
            </div>

            {{-- 3 CHART --}}
            <div class="grid gap-6 lg:grid-cols-3 mb-6">
                {{-- 1. SUKU – BAR --}}
                <div class="chart-card">
                    <div class="flex items-center justify-between mb-3">
                        <p class="chart-title">Komposisi Suku (Top + Lainnya)</p>
                        <span class="chart-subtitle">Bar chart</span>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="ethnicChart"></canvas>
                    </div>
                    <p class="mt-3 text-sm text-[var(--muted)]">
                        Menampilkan suku dengan persentase terbesar. Jika datanya banyak, sisanya digabung sebagai <em>Lainnya</em>.
                    </p>
                </div>

                {{-- 2. BAHASA – DONUT --}}
                <div class="chart-card">
                    <div class="flex items-center justify-between mb-3">
                        <p class="chart-title">Komposisi Bahasa (Top + Lainnya)</p>
                        <span class="chart-subtitle">Donut chart</span>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="languageChart"></canvas>
                    </div>
                    <p class="mt-3 text-sm text-[var(--muted)]">
                        Bahasa yang paling banyak digunakan di pulau ini berdasarkan input admin.
                    </p>
                </div>

                {{-- 3. AGAMA – PIE --}}
                <div class="chart-card">
                    <div class="flex items-center justify-between mb-3">
                        <p class="chart-title">Komposisi Agama (Top + Lainnya)</p>
                        <span class="chart-subtitle">Pie chart</span>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="religionChart"></canvas>
                    </div>
                    <p class="mt-3 text-sm text-[var(--muted)]">
                        Komposisi agama berdasarkan input admin. Jika data banyak, sisanya digabung sebagai <em>Lainnya</em>.
                    </p>
                </div>
            </div>

            <p class="mt-4 text-xs text-[var(--muted)] opacity-70 text-center">
                *Persentase mengikuti data yang kamu masukkan. Kalau total tidak 100% itu tidak masalah (tetap ditampilkan apa adanya).
            </p>

            {{-- POPUP DETAIL --}}
            <div id="stats-modal-backdrop" class="fixed inset-0 z-50 items-center justify-center px-4" aria-hidden="true">
                <div id="stats-modal" class="relative">
                    <button type="button" id="stats-modal-close" aria-label="Tutup">×</button>

                    <h3 id="stats-modal-title" class="text-xl sm:text-2xl font-semibold mb-4">
                        Detail Statistik
                    </h3>

                    <div id="stats-modal-body" class="space-y-4 leading-relaxed">
                        {{-- konten diisi via JS --}}
                    </div>

                    <p class="mt-6 text-xs text-[var(--muted)] opacity-50">
                        Data bersumber dari input admin Lentara (pulau ini).
                    </p>
                </div>
            </div>

            {{-- SCRIPT: Chart.js --}}
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <script>
                (function() {
                    const islandName = @json($selectedIsland->name ?? 'Pulau');

                    // ===== DATA FROM DB (Blade -> JS) =====
                    const popValue = @json($population);

                    const ethLabels = @json($ethTop['labels'] ?? []);
                    const ethData   = @json($ethTop['data'] ?? []);

                    const langLabels = @json($langTop['labels'] ?? []);
                    const langData   = @json($langTop['data'] ?? []);

                    const relLabels = @json($relTop['labels'] ?? []);
                    const relData   = @json($relTop['data'] ?? []);

                    const rawEthCount = @json($ethnicities->count());
                    const rawLangCount = @json($languages->count());
                    const rawRelCount = @json($religions->count());

                    // ===== MODAL DETAILS =====
                    function fmtNumber(n) {
                        try { return new Intl.NumberFormat('id-ID').format(n); } catch(e) { return String(n); }
                    }

                    function listHtml(labels, data, unitSuffix = '%') {
                        if (!labels || !labels.length) {
                            return `<p>Belum ada data yang diinput untuk pulau ini.</p>`;
                        }
                        const items = labels.map((lb, i) => {
                            const v = (data && typeof data[i] !== 'undefined') ? data[i] : 0;
                            const vv = (typeof v === 'number') ? v : parseFloat(v || 0);
                            const show = Number.isFinite(vv) ? vv.toFixed(2).replace(/\.00$/, '') : v;
                            return `<li><strong>${lb}</strong>: ${show}${unitSuffix}</li>`;
                        }).join('');
                        return `<ul class="mt-3 list-disc list-inside space-y-2">${items}</ul>`;
                    }

                    const detailMap = {
                        population: {
                            title: `Jumlah Penduduk ${islandName}`,
                            body: `
                                <p>Data penduduk untuk <strong>${islandName}</strong> diisi melalui panel admin.</p>
                                <p>Nilai saat ini: <strong>${popValue > 0 ? fmtNumber(popValue) : '—'}</strong></p>
                                <p class="mt-3">Gunakan angka perkiraan terbaru agar statistik pulau tetap relevan.</p>
                            `
                        },
                        ethnicity: {
                            title: `Komposisi Suku di ${islandName}`,
                            body: `
                                <p>Total entri suku yang tersimpan: <strong>${rawEthCount}</strong>.</p>
                                <p>Ringkasan chart menampilkan <strong>Top data + Lainnya</strong> (kalau datanya banyak).</p>
                                ${listHtml(ethLabels, ethData, '%')}
                            `
                        },
                        language: {
                            title: `Komposisi Bahasa di ${islandName}`,
                            body: `
                                <p>Total entri bahasa yang tersimpan: <strong>${rawLangCount}</strong>.</p>
                                <p>Ringkasan chart menampilkan <strong>Top data + Lainnya</strong> (kalau datanya banyak).</p>
                                ${listHtml(langLabels, langData, '%')}
                            `
                        }
                    };

                    const backdrop = document.getElementById('stats-modal-backdrop');
                    const modalTitle = document.getElementById('stats-modal-title');
                    const modalBody = document.getElementById('stats-modal-body');
                    const closeBtn = document.getElementById('stats-modal-close');

                    function openModal(statKey) {
                        const data = detailMap[statKey];
                        if (!data) return;
                        modalTitle.textContent = data.title;
                        modalBody.innerHTML = data.body;
                        backdrop.classList.add('is-open');
                        document.body.classList.add('overflow-hidden');
                    }
                    function closeModal() {
                        backdrop.classList.remove('is-open');
                        document.body.classList.remove('overflow-hidden');
                    }

                    document.querySelectorAll('#stats .stat-card[data-stat]').forEach(function(card) {
                        card.addEventListener('click', function() {
                            openModal(card.getAttribute('data-stat'));
                        });
                    });
                    closeBtn.addEventListener('click', closeModal);
                    backdrop.addEventListener('click', function(e) { if (e.target === backdrop) closeModal(); });
                    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });

                    // ===== CHARTS =====
                    const neonPalette = [
                        '#f97316', '#22d3ee', '#34d399', '#fb923c', '#0ea5e9',
                        '#84cc16', '#8b5cf6', '#ef4444', '#f59e0b', '#06b6d4',
                        '#10b981', '#6366f1', '#ec4899', '#14b8a6', '#9ca3af'
                    ];

                    function themeIsDark() {
                        return document.documentElement.getAttribute('data-theme') === 'dark';
                    }

                    function chartTextColor() {
                        return themeIsDark() ? '#d1d5db' : '#475569';
                    }

                    function gridColor() {
                        return themeIsDark() ? 'rgba(156, 163, 175, 0.12)' : 'rgba(100, 116, 139, 0.12)';
                    }

                    const commonOptions = {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 900, easing: 'easeOutQuart' },
                        plugins: {
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.92)',
                                borderColor: 'rgba(249, 115, 22, 0.45)',
                                borderWidth: 1,
                                titleColor: '#f9fafb',
                                bodyColor: '#e5e7eb',
                                callbacks: {
                                    label: function(ctx) {
                                        const label = ctx.label || '';
                                        const value = (typeof ctx.parsed === 'number') ? ctx.parsed : (ctx.parsed?.y ?? 0);
                                        const v = Number(value);
                                        const show = Number.isFinite(v) ? v.toFixed(2).replace(/\.00$/, '') : value;
                                        return label + ': ' + show + '%';
                                    }
                                }
                            },
                            legend: {
                                labels: {
                                    color: chartTextColor(),
                                    font: { size: 11 }
                                }
                            }
                        }
                    };

                    let ethnicChart, languageChart, religionChart;

                    function safeCanvas(id) {
                        const el = document.getElementById(id);
                        if (!el) return null;
                        const ctx = el.getContext('2d');
                        return ctx || null;
                    }

                    function destroyCharts() {
                        if (ethnicChart) { ethnicChart.destroy(); ethnicChart = null; }
                        if (languageChart) { languageChart.destroy(); languageChart = null; }
                        if (religionChart) { religionChart.destroy(); religionChart = null; }
                    }

                    function renderCharts() {
                        destroyCharts();

                        // 1) Ethnic - BAR
                        const ectx = safeCanvas('ethnicChart');
                        if (ectx) {
                            const has = Array.isArray(ethLabels) && ethLabels.length && Array.isArray(ethData) && ethData.length;
                            ethnicChart = new Chart(ectx, {
                                type: 'bar',
                                data: {
                                    labels: has ? ethLabels : ['Belum ada data'],
                                    datasets: [{
                                        data: has ? ethData : [0],
                                        backgroundColor: has ? neonPalette : ['rgba(148,163,184,0.4)'],
                                        borderRadius: 6,
                                        borderWidth: 0
                                    }]
                                },
                                options: {
                                    ...commonOptions,
                                    plugins: { ...commonOptions.plugins, legend: { display: false } },
                                    scales: {
                                        x: {
                                            ticks: { color: chartTextColor(), font: { size: 10 } },
                                            grid: { display: false }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            ticks: { color: chartTextColor(), callback: v => v + '%' },
                                            grid: { color: gridColor() }
                                        }
                                    }
                                }
                            });
                        }

                        // 2) Language - DOUGHNUT
                        const lctx = safeCanvas('languageChart');
                        if (lctx) {
                            const has = Array.isArray(langLabels) && langLabels.length && Array.isArray(langData) && langData.length;
                            languageChart = new Chart(lctx, {
                                type: 'doughnut',
                                data: {
                                    labels: has ? langLabels : ['Belum ada data'],
                                    datasets: [{
                                        data: has ? langData : [1],
                                        backgroundColor: has ? neonPalette : ['rgba(148,163,184,0.4)'],
                                        borderWidth: 0
                                    }]
                                },
                                options: {
                                    ...commonOptions,
                                    cutout: '55%',
                                    plugins: {
                                        ...commonOptions.plugins,
                                        legend: {
                                            position: 'right',
                                            labels: { color: chartTextColor(), padding: 14, font: { size: 11 } }
                                        }
                                    }
                                }
                            });
                        }

                        // 3) Religion - PIE
                        const rctx = safeCanvas('religionChart');
                        if (rctx) {
                            const has = Array.isArray(relLabels) && relLabels.length && Array.isArray(relData) && relData.length;
                            religionChart = new Chart(rctx, {
                                type: 'pie',
                                data: {
                                    labels: has ? relLabels : ['Belum ada data'],
                                    datasets: [{
                                        data: has ? relData : [1],
                                        backgroundColor: has ? neonPalette : ['rgba(148,163,184,0.4)'],
                                        borderWidth: 0
                                    }]
                                },
                                options: {
                                    ...commonOptions,
                                    plugins: {
                                        ...commonOptions.plugins,
                                        legend: {
                                            position: 'right',
                                            labels: { color: chartTextColor(), padding: 14, font: { size: 11 } }
                                        }
                                    }
                                }
                            });
                        }
                    }

                    renderCharts();

                    // Re-render when theme toggles (data-theme change)
                    const obs = new MutationObserver(function(muts) {
                        for (const m of muts) {
                            if (m.type === 'attributes' && m.attributeName === 'data-theme') {
                                renderCharts();
                                break;
                            }
                        }
                    });
                    obs.observe(document.documentElement, { attributes: true });
                })();
            </script>
        </section>
    </div>
</section>
