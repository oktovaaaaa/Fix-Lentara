{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('title', 'Lentara Islands')

@php
    // di home tidak ada selectedIsland
    $featuresByType = $featuresByType ?? [];
@endphp

@section('content')
    {{-- HERO + ANIMASI KARTU (anchor #home untuk navbar) --}}
    <section id="home">
        @include('partials.landing-hero')
    </section>

    {{-- SECTION KONTEN HOME (Budaya Indonesia) --}}
    <section class="relative z-[10] bg-[#050505] text-white py-12 sm:py-16 px-4 sm:px-6">
        <div class="max-w-5xl mx-auto space-y-12">

            {{-- ISLANDS LIST / JELAJAH PULAU (anchor #islands untuk navbar "Pulau") --}}
            <section id="islands">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Jelajahi Pulau-Pulau Indonesia
                </h2>
                <p class="text-sm sm:text-base text-white/80 leading-relaxed mb-4">
                    Pilih salah satu pulau dari menu di atas atau dari daftar berikut untuk menjelajahi budaya,
                    destinasi, kuliner, dan warisan khas masing-masing pulau.
                </p>

                {{-- Di sini nanti bisa diisi daftar kartu pulau dari database --}}
                {{-- Contoh placeholder sederhana --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="border border-white/10 rounded-xl p-4 bg-white/5">
                        <h3 class="font-semibold text-base mb-1">Sumatera</h3>
                        <p class="text-xs text-white/70">
                            Pulau dengan beragam budaya dan kuliner khas seperti rendang.
                        </p>
                    </div>
                    <div class="border border-white/10 rounded-xl p-4 bg-white/5">
                        <h3 class="font-semibold text-base mb-1">Jawa</h3>
                        <p class="text-xs text-white/70">
                            Pusat sejarah dan kebudayaan dengan beragam tradisi dan bahasa.
                        </p>
                    </div>
                    <div class="border border-white/10 rounded-xl p-4 bg-white/5">
                        <h3 class="font-semibold text-base mb-1">Kalimantan</h3>
                        <p class="text-xs text-white/70">
                            Pulau besar dengan kekayaan hutan tropis dan budaya Dayak.
                        </p>
                    </div>
                    {{-- dst: nanti bisa diganti loop @foreach islands --}}
                </div>
            </section>

            {{-- ABOUT INDONESIA --}}
            <section id="about">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Tentang Budaya Indonesia
                </h2>
                <p class="text-sm sm:text-base text-white/80 leading-relaxed">
                    Indonesia adalah negara kepulauan dengan ratusan suku, bahasa, dan tradisi. Halaman ini
                    mengajakmu menjelajahi keragaman budaya dari Sabang sampai Merauke melalui pulau-pulau utama.
                </p>
            </section>

            {{-- HISTORY INDONESIA --}}
            <section id="history">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Sejarah Singkat Nusantara
                </h2>
                <p class="text-sm sm:text-base text-white/80 leading-relaxed">
                    Dari kerajaan-kerajaan kuno hingga masa modern, Nusantara tumbuh sebagai titik temu budaya,
                    perdagangan, dan kepercayaan. Kamu bisa memperkaya konten ini dari database nanti.
                </p>
            </section>

           {{-- STATISTIK INDONESIA --}}
{{-- STATISTIK INDONESIA --}}
<section id="stats" class="mt-10">
    {{-- CSS khusus section ini --}}
    <style>
        #stats .stat-card {
            position: relative;
            border-radius: 18px;
            padding: 18px 18px 16px 18px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.55);
            cursor: pointer;
            overflow: hidden;

            opacity: 0;
            transform: translateY(16px);
            animation: statsFadeUp 0.7s ease-out forwards;
        }

        /* varian warna kartu */
        #stats .stat-card--red {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
        }

        #stats .stat-card--purple {
            background: linear-gradient(135deg, #7c3aed, #4c1d95);
        }

        #stats .stat-card--green {
            background: linear-gradient(135deg, #059669, #065f46);
        }

        #stats .stat-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.12), transparent 55%);
            opacity: 0;
            transition: opacity 0.25s ease-out;
        }

        #stats .stat-card:hover::after {
            opacity: 1;
        }

        #stats .stat-number {
            font-size: 2.5rem;
            line-height: 1;
            font-weight: 800;
        }

        #stats .stat-label {
            font-size: 0.95rem;
            font-weight: 500;
        }

        #stats .stat-more {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        #stats .stat-more-icon {
            transition: transform 0.2s ease-out;
        }

        #stats .stat-card:hover .stat-more-icon {
            transform: translateX(4px);
        }

        /* kartu chart */
        #stats .chart-card {
            position: relative;
            border-radius: 18px;
            padding: 16px 16px 12px 16px;
            background: radial-gradient(circle at top left, #111827, #020617);
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 18px 40px rgba(0,0,0,0.55);
            overflow: hidden;

            opacity: 0;
            transform: translateY(16px);
            animation: statsFadeUp 0.7s ease-out forwards;
        }

        #stats .chart-title {
            font-size: 0.95rem;
            font-weight: 600;
        }

        #stats .chart-subtitle {
            font-size: 0.75rem;
        }

        #stats .chart-wrapper {
            position: relative;
            width: 100%;
            height: 220px;
        }

        /* stagger animasi */
        #stats .stat-card[data-stat="islands"]     { animation-delay: 0.05s; }
        #stats .stat-card[data-stat="unesco"]      { animation-delay: 0.12s; }
        #stats .stat-card[data-stat="population"]  { animation-delay: 0.19s; }
        #stats .chart-card:nth-child(1)            { animation-delay: 0.26s; }
        #stats .chart-card:nth-child(2)            { animation-delay: 0.33s; }
        #stats .chart-card:nth-child(3)            { animation-delay: 0.40s; }

        @keyframes statsFadeUp {
            from { opacity: 0; transform: translateY(18px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ===== Modal (popup) ===== */
        #stats-modal-backdrop {
            display: none;
        }

        #stats-modal-backdrop.is-open {
            display: flex;
        }

        #stats-modal {
            transform: translateY(12px) scale(0.96);
            opacity: 0;
            transition: all 0.22s ease-out;
        }

        #stats-modal-backdrop.is-open #stats-modal {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        @media (max-width: 640px) {
            #stats .stat-number { font-size: 2.1rem; }
            #stats .chart-wrapper { height: 210px; }
        }
    </style>

    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3 text-white">
        Statistik Budaya Indonesia
    </h2>
    <p class="text-sm sm:text-base text-white/70 mb-4 max-w-3xl">
        Ringkasan keragaman Indonesia: jumlah pulau, warisan budaya takbenda yang diakui
        UNESCO, serta dinamika jumlah penduduk.
    </p>

    {{-- TIGA CARD UTAMA --}}
    <div class="grid gap-4 lg:grid-cols-3 mb-6">
        {{-- Pulau di Indonesia --}}
        <button
            type="button"
            class="stat-card stat-card--red text-left text-white"
            data-stat="islands"
        >
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="stat-number">17.380</div>
                    <div class="stat-label mt-1">Pulau di Indonesia (2024)</div>
                    <p class="mt-2 text-xs text-white/80 max-w-[260px]">
                        Jumlah pulau bernama dan berkoordinat menurut BIG. Angka ini
                        terus diperbarui karena dinamika geografis dan verifikasi lapangan.
                    </p>
                </div>
                <div class="opacity-80">
                    <svg viewBox="0 0 24 24" class="w-10 h-10">
                        <path fill="#fecaca" d="M11 3a9 9 0 1 0 9 9h-9z" />
                        <path fill="#fee2e2" d="M13 3.055V11h7.945A9.002 9.002 0 0 0 13 3.055z" />
                    </svg>
                </div>
            </div>
            <div class="stat-more text-white/90">
                More info
                <span class="stat-more-icon">➜</span>
            </div>
        </button>

        {{-- Warisan Budaya Takbenda UNESCO --}}
        <button
            type="button"
            class="stat-card stat-card--purple text-left text-white"
            data-stat="unesco"
        >
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="stat-number">16</div>
                    <div class="stat-label mt-1">WBTb Indonesia diakui UNESCO</div>
                    <p class="mt-2 text-xs text-white/80 max-w-[260px]">
                        Termasuk Keris, Batik, Angklung, Tari Saman, Gamelan, Reog
                        Ponorogo, Kebaya, dan lainnya yang tercatat hingga 2024.
                    </p>
                </div>
                <div class="opacity-80">
                    <svg viewBox="0 0 24 24" class="w-10 h-10">
                        <circle cx="12" cy="12" r="9" fill="#ddd6fe" />
                        <path fill="#a855f7" d="M12 3v9l7.8 4.5A9 9 0 0 0 12 3z" />
                    </svg>
                </div>
            </div>
            <div class="stat-more text-white/90">
                More info
                <span class="stat-more-icon">➜</span>
            </div>
        </button>

        {{-- Jumlah Penduduk Indonesia --}}
        <button
            type="button"
            class="stat-card stat-card--green text-left text-white"
            data-stat="population"
        >
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="stat-number">287,6 Jt</div>
                    <div class="stat-label mt-1">Perkiraan penduduk (Nov 2025)</div>
                    <p class="mt-2 text-xs text-white/80 max-w-[260px]">
                        Berbasis data Kemendagri dan BPS, penduduk Indonesia terus
                        bertambah sejak Sensus 2020 yang mencatat 270,20 juta jiwa.
                    </p>
                </div>
                <div class="opacity-80">
                    <svg viewBox="0 0 24 24" class="w-10 h-10">
                        <rect x="3" y="10" width="4" height="9" rx="1" fill="#bbf7d0" />
                        <rect x="10" y="7" width="4" height="12" rx="1" fill="#6ee7b7" />
                        <rect x="17" y="4" width="4" height="15" rx="1" fill="#22c55e" />
                    </svg>
                </div>
            </div>
            <div class="stat-more text-white/90">
                More info
                <span class="stat-more-icon">➜</span>
            </div>
        </button>
    </div>

    {{-- TIGA CHART: SUKU (BAR), BAHASA (DONUT), AGAMA (PIE) --}}
    <div class="grid gap-4 lg:grid-cols-3">
        {{-- 1. SUKU BANGSA – BAR CHART --}}
        <div class="chart-card text-white">
            <div class="flex items-center justify-between mb-2">
                <p class="chart-title">14 Suku Terbesar (persentase penduduk)</p>
                <span class="chart-subtitle text-white/60">Bar chart</span>
            </div>
            <div class="chart-wrapper">
                <canvas id="ethnicChart"></canvas>
            </div>
            <p class="mt-2 text-[11px] text-white/55">
                Data diadaptasi dari sensus: Jawa &gt;40%, diikuti Sunda, Melayu, Batak,
                dan suku-suku besar lainnya. Slice terakhir = <em>Lainnya</em>.
            </p>
        </div>

        {{-- 2. BAHASA SEHARI-HARI – DONUT CHART --}}
        <div class="chart-card text-white">
            <div class="flex items-center justify-between mb-2">
                <p class="chart-title">Bahasa yang Paling Banyak Digunakan</p>
                <span class="chart-subtitle text-white/60">Donut chart</span>
            </div>
            <div class="chart-wrapper">
                <canvas id="languageChart"></canvas>
            </div>
            <p class="mt-2 text-[11px] text-white/55">
                14 bahasa dengan penutur terbanyak, berdasarkan persentase penggunaan
                sehari-hari. Sisanya digabung sebagai <em>Lainnya</em>.
            </p>
        </div>

        {{-- 3. AGAMA – PIE CHART --}}
        <div class="chart-card text-white">
            <div class="flex items-center justify-between mb-2">
                <p class="chart-title">Komposisi Agama di Indonesia (±2021)</p>
                <span class="chart-subtitle text-white/60">Pie chart</span>
            </div>
            <div class="chart-wrapper">
                <canvas id="religionChart"></canvas>
            </div>
            <p class="mt-2 text-[11px] text-white/55">
                Islam mendominasi populasi, diikuti Protestan, Katolik, Hindu, Buddha,
                Konghucu, dan agama lainnya.
            </p>
        </div>
    </div>

    <p class="mt-3 text-[11px] text-white/50">
        *Angka dibulatkan. Persentase dan jumlah bisa sedikit berbeda antar sumber resmi,
        tetapi kisaran nilainya tetap sama.
    </p>

    {{-- POPUP DETAIL UNTUK SEMUA CARD --}}
    <div
        id="stats-modal-backdrop"
        class="fixed inset-0 z-40 bg-black/60 items-center justify-center px-4"
        aria-hidden="true"
    >
        <div
            id="stats-modal"
            class="max-w-lg w-full bg-[#020617] text-white rounded-2xl border border-white/10 p-5 sm:p-6 relative"
        >
            <button
                type="button"
                id="stats-modal-close"
                class="absolute right-4 top-3 text-white/60 hover:text-white text-xl leading-none"
                aria-label="Tutup"
            >
                ×
            </button>

            <h3 id="stats-modal-title" class="text-lg sm:text-xl font-semibold mb-2">
                Detail Statistik
            </h3>

            <div id="stats-modal-body" class="text-sm text-white/80 space-y-2 leading-relaxed">
                {{-- konten diisi via JS --}}
            </div>

            <p class="mt-4 text-[11px] text-white/40">
                Ringkasan berdasarkan data lembaga resmi Indonesia, UNESCO, dan publikasi terkait.
            </p>
        </div>
    </div>

    {{-- SCRIPT POPUP + CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        (function () {
            /* ================= DETAIL MODAL UNTUK 3 CARD ================= */
            const detailMap = {
                islands: {
                    title: 'Jumlah Pulau di Indonesia',
                    body: `
                        <p>Menurut penelahaan <strong>Badan Informasi Geospasial (BIG)</strong>,
                        jumlah pulau di Indonesia pada tahun 2024 mencapai
                        <strong>17.380 pulau</strong>. Angka ini mencakup pulau yang memiliki
                        <em>nama</em> dan <em>koordinat</em> resmi.</p>
                        <p>Jika dibandingkan dengan tahun 2023 (17.374 pulau), terdapat
                        penambahan pulau baru akibat dinamika geografis dan verifikasi di
                        lapangan, misalnya di <strong>Kepulauan Bangka Belitung</strong>,
                        <strong>Sulawesi Tenggara</strong>, <strong>Maluku Utara</strong>, dan
                        <strong>Kalimantan Barat</strong>.</p>
                        <ul class="mt-2 list-disc list-inside space-y-1">
                            <li>Pulau harus berupa daratan yang terbentuk alami.</li>
                            <li>Dikelilingi air dan tetap muncul saat pasang tertinggi.</li>
                            <li>Pulau bisa hilang atau menyatu karena abrasi, sedimentasi,
                                dan perubahan ekosistem.</li>
                        </ul>
                        <p class="mt-2">Data pulau Indonesia terus dimutakhirkan setiap tahun
                        melalui platform resmi BIG seperti <em>sipulau.big.go.id</em> dan
                        <em>Gazeter Republik Indonesia</em>.</p>
                    `
                },
                unesco: {
                    title: 'Warisan Budaya Takbenda Indonesia',
                    body: `
                        <p>Hingga Desember 2024, terdapat <strong>16 Warisan Budaya Takbenda
                        (WBTb) Indonesia</strong> yang telah diakui UNESCO, antara lain:</p>
                        <ul class="mt-2 list-disc list-inside space-y-1">
                            <li><strong>2008</strong>: Keris; Pertunjukan Wayang.</li>
                            <li><strong>2009</strong>: Batik; Pendidikan dan Pelatihan Batik.</li>
                            <li><strong>2010</strong>: Angklung.</li>
                            <li><strong>2011</strong>: Tari Saman.</li>
                            <li><strong>2012</strong>: Noken (tas tradisional Papua).</li>
                            <li><strong>2017</strong>: Tiga genre tari tradisional Bali;
                                Seni Pembuatan Kapal Pinisi.</li>
                            <li><strong>2019</strong>: Tradisi Pencak Silat.</li>
                            <li><strong>2020</strong>: Pantun.</li>
                            <li><strong>2021</strong>: Gamelan.</li>
                            <li><strong>2023</strong>: Jamu.</li>
                            <li><strong>2024</strong>: Kesenian Reog Ponorogo; Kebaya.</li>
                        </ul>
                        <p class="mt-3">Di tingkat nasional, Kementerian Pendidikan, Kebudayaan,
                        Riset, dan Teknologi mencatat dan menetapkan WBTb Indonesia:</p>
                        <ul class="mt-2 list-disc list-inside space-y-1">
                            <li><strong>Nov 2022</strong>: 11.622 warisan budaya dicatat,
                                <strong>1.728</strong> telah ditetapkan.</li>
                            <li><strong>2023</strong>: bertambah 213 menjadi
                                <strong>1.941</strong> WBTb yang ditetapkan.</li>
                            <li><strong>2024</strong>: bertambah 272 menjadi
                                <strong>2.213</strong> penetapan.</li>
                            <li><strong>2025</strong>: penambahan 514, sehingga total
                                <strong>2.727 WBTb</strong> Indonesia yang ditetapkan
                                (periode 2013–2025).</li>
                        </ul>
                        <p class="mt-2">UNESCO sendiri tidak mengelompokkan WBTb dunia per kategori
                        secara global, tetapi Indonesia aktif mengusulkan dan memperkuat
                        perlindungan warisan budaya, baik yang bersifat <em>takbenda</em> maupun
                        yang berupa <em>warisan benda</em> seperti situs budaya dan alam.</p>
                    `
                },
                population: {
                    title: 'Jumlah Penduduk Indonesia',
                    body: `
                        <p>Jumlah penduduk Indonesia terus meningkat setiap tahun:</p>
                        <ul class="mt-2 list-disc list-inside space-y-1">
                            <li><strong>Sensus Penduduk 2020</strong>:
                                sekitar <strong>270,20 juta jiwa</strong>.</li>
                            <li><strong>Data Kemendagri (30 Juni 2025)</strong>:
                                <strong>286.693.693 jiwa</strong>.</li>
                            <li><strong>Estimasi BPS (13 November 2025)</strong>:
                                sekitar <strong>287,6 juta jiwa</strong>.</li>
                        </ul>
                        <p class="mt-2">Perbedaan angka antara Kemendagri dan BPS wajar terjadi
                        karena perbedaan waktu rujukan dan metode penghitungan. Namun, semuanya
                        menunjukkan tren yang sama: penduduk Indonesia terus bertambah sejak 2020.</p>
                        <p class="mt-2">Data ini penting untuk perencanaan kebijakan, mulai dari
                        pendidikan, kesehatan, hingga pembangunan infrastruktur di seluruh
                        wilayah Indonesia.</p>
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

            document.querySelectorAll('#stats .stat-card[data-stat]').forEach(function (card) {
                card.addEventListener('click', function () {
                    const key = card.getAttribute('data-stat');
                    openModal(key);
                });
            });

            closeBtn.addEventListener('click', closeModal);
            backdrop.addEventListener('click', function (e) {
                if (e.target === backdrop) closeModal();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeModal();
            });

            /* ================= CHART: DATA (SAMA SEPERTI SEBELUMNYA) ================= */
            const ethnicLabels = [
                'Jawa', 'Sunda', 'Melayu', 'Batak', 'Madura',
                'Betawi', 'Minangkabau', 'Bugis', 'Banten', 'Banjar',
                'Bali', 'Makassar', 'Aceh', 'Sasak', 'Lainnya'
            ];
            const ethnicData = [
                40.06, 15.51, 3.70, 3.58, 3.03,
                2.88, 2.73, 2.71, 1.96, 1.74,
                1.50, 1.40, 1.30, 1.10, 17.50
            ];

            const languageLabels = [
                'Jawa', 'Indonesia', 'Sunda', 'Melayu', 'Madura',
                'Minangkabau', 'Banjar', 'Bugis', 'Bali',
                'Bahasa Batak', 'Cirebon', 'NTT lainnya',
                'Sasak', 'Aceh', 'Lainnya'
            ];
            const languageData = [
                31.79, 19.94, 15.14, 3.69, 3.62,
                1.98, 1.71, 1.64, 1.57,
                1.55, 1.44, 1.40,
                1.26, 12.08
            ];

            const religionLabels = [
                'Islam', 'Protestan', 'Katolik',
                'Hindu', 'Buddha', 'Konghucu', 'Agama lainnya'
            ];
            const religionData = [86.93, 7.47, 3.08, 1.71, 0.74, 0.05, 0.03];

            const palette = [
                '#60a5fa', '#f97316', '#a855f7', '#22c55e', '#eab308',
                '#f97373', '#0ea5e9', '#6366f1', '#ec4899', '#14b8a6',
                '#f59e0b', '#84cc16', '#fb7185', '#2dd4bf', '#9ca3af'
            ];

            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 900, easing: 'easeOutQuart' },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const label = ctx.label || '';
                                const value = ctx.parsed;
                                return label + ': ' + value.toFixed(2) + '%';
                            }
                        }
                    },
                    legend: { labels: { color: '#e5e7eb', font: { size: 11 } } }
                }
            };

            const ethnicCtx = document.getElementById('ethnicChart').getContext('2d');
            new Chart(ethnicCtx, {
                type: 'bar',
                data: {
                    labels: ethnicLabels,
                    datasets: [{
                        data: ethnicData,
                        backgroundColor: palette,
                        borderRadius: 8
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: { ...commonOptions.plugins, legend: { display: false } },
                    scales: {
                        x: {
                            ticks: { color: '#9ca3af', font: { size: 10 } },
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#9ca3af',
                                callback: value => value + '%'
                            },
                            grid: { color: 'rgba(148, 163, 184, 0.15)' }
                        }
                    }
                }
            });

            const languageCtx = document.getElementById('languageChart').getContext('2d');
            new Chart(languageCtx, {
                type: 'doughnut',
                data: {
                    labels: languageLabels,
                    datasets: [{
                        data: languageData,
                        backgroundColor: palette,
                        borderWidth: 1
                    }]
                },
                options: {
                    ...commonOptions,
                    cutout: '55%'
                }
            });

            const religionCtx = document.getElementById('religionChart').getContext('2d');
            new Chart(religionCtx, {
                type: 'pie',
                data: {
                    labels: religionLabels,
                    datasets: [{
                        data: religionData,
                        backgroundColor: [
                            '#22c55e', '#60a5fa', '#4b5563',
                            '#eab308', '#f97316', '#f97373', '#a855f7'
                        ],
                        borderWidth: 1
                    }]
                },
                options: commonOptions
            });
        })();
    </script>
</section>


            {{-- QUIZ INDONESIA --}}
            <section id="quiz">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Kuis Budaya Indonesia
                </h2>
                <p class="text-sm sm:text-base text-white/80">
                    Di sini nanti bisa jadi area kuis umum tentang Nusantara — misalnya pilihan ganda tentang
                    pulau, suku, atau rumah adat.
                    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                </p>
            </section>
        </div>
    </section>
@endsection
