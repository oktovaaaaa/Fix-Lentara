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

        {{-- =========================
           ABOUT UI ONLY
           RULES DARI KAMU:
           1) CARD + NEON HANYA KALAU ADA GAMBAR.
           2) KALAU TIDAK ADA GAMBAR:
              - kalau ada points/link => 2 kolom (md):
                deskripsi di kiri, points(+link) di kanan. TANPA CARD.
              - kalau hanya deskripsi saja (tanpa points & link) => full lebar, TANPA CARD.
           3) Warna card: normal, sedikit transparan (bukan gradient).
           4) Neon border mengikuti style conic-gradient muter seperti referensi history.
           5) Semua teks/warna menyesuaikan theme vars (light/dark) dari :root dan html[data-theme="dark"].
        ========================= --}}
        <style>
            /* ========= ABOUT ITEMS WRAP ========= */
            #about .about-items {
                display: flex;
                flex-direction: column;
                gap: 1.35rem;
            }

            #about .about-item-shell {
                border-radius: 22px;
                padding: .25rem 0; /* bukan card belakang */
            }

            /* ========= TYPO (theme-aware) ========= */
            #about .about-item-title {
                font-size: 1.15rem;
                font-weight: 900;
                color: var(--txt-body);
                margin: 0 0 .6rem 0;
                line-height: 1.25;
                overflow-wrap: anywhere;
                word-break: break-word;
            }

            #about .about-item-desc {
                font-size: 1rem;
                line-height: 1.85;
                color: color-mix(in srgb, var(--txt-body) 74%, transparent);
                margin: 0;
                white-space: pre-line;
                overflow-wrap: anywhere;
                word-break: break-word;
            }

            /* ========= NEON BORDER (SAMA FEEL DENGAN HISTORY) ========= */
            @property --about-angle {
                syntax: "<angle>";
                inherits: false;
                initial-value: 0deg;
            }

            #about .about-neon-wrap {
                position: relative;
                border-radius: 22px;
            }

            /* lapisan neon */
            #about .about-neon-glow {
                position: absolute;
                inset: -5px;
                border-radius: inherit;
                padding: 10px;
                z-index: 0;
                pointer-events: none;

                background: conic-gradient(from var(--about-angle),
                    rgba(255, 107, 0, 0),
                    rgba(255, 140, 66, 0.20) 30deg,
                    var(--brand) 80deg,
                    color-mix(in srgb, var(--brand) 70%, white) 120deg,
                    rgba(255, 140, 66, 0.18) 180deg,
                    rgba(255, 107, 0, 0) 240deg,
                    rgba(255, 140, 66, 0.22) 300deg,
                    var(--brand) 330deg,
                    rgba(255, 107, 0, 0) 360deg
                );

                -webkit-mask:
                    linear-gradient(#000 0 0) content-box,
                    linear-gradient(#000 0 0);
                -webkit-mask-composite: xor;
                mask-composite: exclude;

                filter: blur(4px);
                opacity: 0.92;
                animation: about-neon-spin 8.5s linear infinite;
            }

            @keyframes about-neon-spin {
                to { --about-angle: 360deg; }
            }

            #about .about-neon-inner {
                position: relative;
                z-index: 1;
                border-radius: 20px;
                overflow: hidden;
            }

            /* ========= IMAGE FRAME (NEON) ========= */
            #about .about-img {
                width: 100%;
                height: 330px;
                object-fit: cover;
                display: block;
                transform: scale(1.001);
                transition: transform .75s ease;
            }

            #about .about-neon-inner:hover .about-img {
                transform: scale(1.05);
            }

            /* overlay tipis, theme-aware (gelap di dark, halus di light) */
            #about .about-img-overlay {
                position: absolute;
                inset: 0;
                pointer-events: none;
                background: linear-gradient(
                    45deg,
                    color-mix(in srgb, var(--bg-body) 20%, transparent),
                    transparent 55%
                );
                opacity: .9;
            }

            /* ========= CARD (HANYA KALAU ADA GAMBAR) ========= */
            #about .about-card {
                position: relative;
                border-radius: 20px;
                padding: 1.25rem 1.25rem;
                overflow: hidden;

                /* NORMAL (bukan gradient) + sedikit transparan, theme-aware */
                background: color-mix(in srgb, var(--card) 82%, transparent);

                /* garis halus */
                border: 1px solid color-mix(in srgb, var(--line) 85%, transparent);

                box-shadow: var(--shadow);
                backdrop-filter: blur(10px);
            }

            html[data-theme="dark"] #about .about-card {
                background: color-mix(in srgb, var(--card) 72%, transparent);
                border-color: color-mix(in srgb, var(--line) 70%, transparent);
            }

            /* accent glow lembut di dalam card (bukan gradient) */
            #about .about-card::before {
                content: "";
                position: absolute;
                inset: -40px;
                background: radial-gradient(closest-side,
                    color-mix(in srgb, var(--brand) 35%, transparent),
                    transparent 70%);
                opacity: 0.55;
                filter: blur(10px);
                pointer-events: none;
                transform: translate(18px, -14px);
            }

            #about .about-card > * {
                position: relative;
                z-index: 1;
            }

            /* ========= POINTS (theme-aware) ========= */
            #about .about-points {
                margin-top: .95rem;
                display: grid;
                gap: .65rem;
            }

            #about .about-point {
                display: flex;
                gap: .7rem;
                align-items: flex-start;
                font-size: .95rem;
                line-height: 1.65;
                color: color-mix(in srgb, var(--txt-body) 80%, transparent);
                overflow-wrap: anywhere;
                word-break: break-word;
            }

            #about .about-check {
                width: 22px;
                height: 22px;
                min-width: 22px;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-top: 2px;

                background: color-mix(in srgb, var(--brand) 18%, transparent);
                border: 1px solid color-mix(in srgb, var(--brand) 28%, transparent);
                color: var(--brand);
                font-weight: 900;
            }

            /* ========= LINK (theme-aware, tidak gradient) ========= */
            #about .about-link {
                margin-top: 1rem;
                display: inline-flex;
                align-items: center;
                gap: .55rem;

                padding: .85rem 1.15rem;
                border-radius: 999px;

                border: 1px solid color-mix(in srgb, var(--brand) 45%, transparent);
                background: color-mix(in srgb, var(--brand) 12%, transparent);
                color: var(--txt-body);

                font-weight: 900;
                font-size: .95rem;
                text-decoration: none;

                transition: transform .18s ease, background .18s ease, border-color .18s ease, box-shadow .18s ease;
                width: fit-content;
            }

            #about .about-link:hover {
                transform: translateY(-2px);
                background: color-mix(in srgb, var(--brand) 18%, transparent);
                border-color: color-mix(in srgb, var(--brand) 62%, transparent);
                box-shadow: 0 16px 44px rgba(0,0,0,0.18);
            }

            #about .about-link .about-link-icon {
                color: var(--brand);
                font-weight: 900;
            }

            /* ========= NO IMAGE MODE (2 kolom: desc kiri, points+link kanan) ========= */
            #about .about-split {
                display: grid;
                grid-template-columns: 1fr;
                gap: 1.1rem;
                align-items: start;
            }

            @media (min-width: 768px) {
                #about .about-split {
                    grid-template-columns: 1fr 1fr;
                    gap: 1.75rem;
                }
            }

            /* kolom kanan “rapih” tanpa card, hanya divider halus */
            #about .about-right-panel {
                padding-left: 0;
                border-left: none;
            }

            @media (min-width: 768px) {
                #about .about-right-panel {
                    padding-left: 1.25rem;
                    border-left: 1px solid color-mix(in srgb, var(--line) 85%, transparent);
                }
            }

            /* kecilin sedikit di mobile biar padat */
            @media (max-width: 480px) {
                #about .about-card { padding: 1rem 1rem; }
                #about .about-img { height: 260px; }
            }
        </style>

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
                <p class="neon-subtitle whitespace-pre-line break-words">
                    {!! nl2br(e($heroDesc)) !!}
                </p>
            @else
                {{-- kalau tidak ada subtitle dari admin, kita buat default supaya konsisten dengan HOME --}}
                <p class="neon-subtitle">
                    Pulau ini merupakan bagian dari kekayaan Nusantara yang memiliki keberagaman budaya dan alam, tercermin dari suku-suku yang hidup dan berkembang, kuliner khas daerah, destinasi wisata, serta flora dan fauna yang menjadi identitas pulau ini.
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
        <div class="about-items">
            @forelse($aboutIslandItems as $it)
                @php
                    $title = $it->title ?: null;
                    $desc  = $it->description ?? '';
                    $img   = $it->image ?: null;
                    $link  = $it->more_link ?: null;

                    $pointsArr = method_exists($it, 'pointsArray')
                        ? $it->pointsArray()
                        : (is_string($it->points ?? null) ? preg_split("/\r\n|\n|\r/", trim($it->points)) : []);
                    $pointsArr = array_values(array_filter(array_map('trim', (array)$pointsArr), fn($x)=>$x!==''));;
                    $hasPoints = !empty($pointsArr);
                    $hasImage  = !empty($img);

                    // mode: hanya deskripsi (no image, no points, no link) => full
                    $plainOnly = (!$hasImage && !$hasPoints && empty($link));
                @endphp

                <div class="about-item-shell">
                    {{-- ======================================
                       CASE A: ADA GAMBAR
                       - KIRI: gambar (NEON)
                       - KANAN: CARD (NEON + BG NORMAL TRANSPARAN)
                       - points + link tetap di dalam card (kanan)
                    ======================================= --}}
                    @if($hasImage)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-start">
                            {{-- IMAGE (NEON) --}}
                            <div class="about-neon-wrap">
                                <div class="about-neon-glow"></div>
                                <div class="about-neon-inner">
                                    <img src="{{ $img }}" alt="{{ $title ?? 'Gambar' }}" class="about-img">
                                    <div class="about-img-overlay"></div>
                                </div>
                            </div>

                            {{-- CARD (NEON) --}}
                            <div class="about-neon-wrap">
                                <div class="about-neon-glow"></div>
                                <div class="about-neon-inner">
                                    <div class="about-card">
                                        @if($title)
                                            <h3 class="about-item-title">{{ $title }}</h3>
                                        @endif

                                        <p class="about-item-desc">{{ $desc }}</p>

                                        @if($hasPoints)
                                            <div class="about-points">
                                                @foreach($pointsArr as $p)
                                                    <div class="about-point">
                                                        <span class="about-check">✓</span>
                                                        <span class="text-[var(--txt-body)]/80">{{ $p }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($link)
                                            <a href="{{ $link }}" target="_blank" rel="noopener" class="about-link">
                                                Baca Cerita <span class="about-link-icon" aria-hidden="true">↗</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                    @else
                        {{-- ======================================
                           CASE B: TIDAK ADA GAMBAR
                           - Kalau hanya deskripsi => FULL (tanpa card)
                           - Kalau ada points dan/atau link => 2 kolom:
                             kiri deskripsi, kanan points + (link)
                           - TANPA CARD.
                        ======================================= --}}
                        @if($plainOnly)
                            <div>
                                @if($title)
                                    <h3 class="about-item-title">{{ $title }}</h3>
                                @endif
                                <p class="about-item-desc">{{ $desc }}</p>
                            </div>
                        @else
                            <div class="about-split">
                                {{-- LEFT: DESKRIPSI --}}
                                <div>
                                    @if($title)
                                        <h3 class="about-item-title">{{ $title }}</h3>
                                    @endif
                                    <p class="about-item-desc">{{ $desc }}</p>
                                </div>

                                {{-- RIGHT: POINTS + LINK --}}
                                <div class="about-right-panel">
                                    @if($hasPoints)
                                        <div class="about-points" style="margin-top: 0;">
                                            @foreach($pointsArr as $p)
                                                <div class="about-point">
                                                    <span class="about-check">✓</span>
                                                    <span class="text-[var(--txt-body)]/80">{{ $p }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($link)
                                        <a href="{{ $link }}" target="_blank" rel="noopener" class="about-link">
                                            Baca Cerita <span class="about-link-icon" aria-hidden="true">↗</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
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
                Informasi kependudukan dan keberagaman Nusantara pada pulau ini, termasuk suku, bahasa, dan agama, yang membantu mengenal budaya Indonesia secara lebih dekat.
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
                                Perkiraan jumlah penduduk yang tinggal di pulau ini sebagai gambaran skala kependudukan Nusantara.

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
                                Data suku yang tercatat, disajikan untuk menggambarkan keberagaman suku Nusantara yang membentuk identitas budaya.
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
                                Menampilkan komposisi bahasa yang digunakan di pulau ini sebagai bagian dari kekayaan bahasa Nusantara.
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
                        <p class="chart-title">Keberagaman Suku di Indonesia</p>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="ethnicChart"></canvas>
                    </div>
                    <p class="mt-3 text-sm text-[var(--muted)]">
                        Data visual ini membantu memahami persebaran dan keberagaman suku Nusantara yang terdapat di pulau ini.
                    </p>
                </div>

                {{-- 2. BAHASA – DONUT --}}
                <div class="chart-card">
                    <div class="flex items-center justify-between mb-3">
                        <p class="chart-title">Keberagaman Bahasa Nusantara</p>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="languageChart"></canvas>
                    </div>
                    <p class="mt-3 text-sm text-[var(--muted)]">
                        Data visual ini membantu memahami keragaman bahasa Nusantara yang ada di pulau ini.
                    </p>
                </div>

                {{-- 3. AGAMA – PIE --}}
                <div class="chart-card">
                    <div class="flex items-center justify-between mb-3">
                        <p class="chart-title">Keberagaman Agama di Indonesia</p>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="religionChart"></canvas>
                    </div>
                    <p class="mt-3 text-sm text-[var(--muted)]">
                        Data visual ini membantu memahami keberagaman agama Nusantara yang ada di pulau ini.
                    </p>
                </div>
            </div>

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
                </div>
            </div>

            <style>
                /* Saat modal statistik pulau terbuka, navbar jangan menimpa */
                html.stats-modal-open .site-header {
                    z-index: 10 !important;
                }
            </style>

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
 <p>
    Data kependudukan ini memberikan gambaran umum tentang jumlah penduduk yang
    menetap di <strong>${islandName}</strong> sebagai bagian dari Nusantara.
  </p>

  <p>
    <strong>Perkiraan jumlah penduduk:</strong>
    <strong>${popValue > 0 ? fmtNumber(popValue) : '-'}</strong>
  </p>

  <p class="mt-3">
    Data ditampilkan dalam bentuk perkiraan untuk membantu memahami kondisi
    kependudukan dan keragaman masyarakat di <strong>${islandName}</strong>.
  </p>
                            `
                        },
                        ethnicity: {
                            title: `Komposisi Suku di ${islandName}`,
                            body: `
                               <p>
      <strong>${islandName}</strong> memiliki keberagaman suku yang membentuk
      identitas budaya Nusantara di wilayah ini.
    </p>

    <p>
      Data ini merangkum <strong>${rawEthCount}</strong> suku yang tercatat
      untuk memberikan gambaran umum tentang komposisi suku.
    </p>

    <p class="mt-3">
      Suku dengan persentase terbesar ditampilkan secara utama,
      sedangkan sisanya digabung sebagai <strong>Lainnya</strong>.
    </p>
                            `
                        },
                        language: {
                            title: `Komposisi Bahasa di ${islandName}`,
                            body: `
<p>
  Bagian ini menampilkan keberagaman bahasa yang digunakan di
  <strong>${islandName}</strong> sebagai bagian dari kekayaan bahasa Nusantara.
</p>

<p>
  <strong>Total bahasa tercatat:</strong> ${rawLangCount}.
  Data disajikan dalam bentuk ringkasan untuk memudahkan pemahaman.
</p>

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
                        document.documentElement.classList.add('stats-modal-open');

                    }
                    function closeModal() {
                        backdrop.classList.remove('is-open');
                        document.body.classList.remove('overflow-hidden');
                        document.documentElement.classList.remove('stats-modal-open');

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
