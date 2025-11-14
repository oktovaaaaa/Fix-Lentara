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
    <section class="relative z-[10] py-12 sm:py-16 px-4 sm:px-6 bg-[var(--bg-body)] text-[var(--txt-body)]">
        <div class="max-w-5xl mx-auto space-y-12">

            {{-- ISLANDS LIST / JELAJAH PULAU (anchor #islands untuk navbar "Pulau") --}}
            <section id="islands">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Jelajahi Pulau-Pulau Indonesia
                </h2>
                <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed mb-4">
                    Pilih salah satu pulau dari menu di atas atau dari daftar berikut untuk menjelajahi budaya,
                    destinasi, kuliner, dan warisan khas masing-masing pulau.

                    {{-- @section('content') --}}

                {{-- Leaflet CSS & JS (CDN) --}}
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

                <style>
                    #indo-map {
                        height: 500px;
                        border-radius: 1.5rem;
                        overflow: hidden;
                    }

                    /* Styling popup biar mirip card */
                    .island-popup {
                        font-family: "Inter", system-ui, sans-serif;
                        min-width: 220px;
                    }

                    .island-popup h3 {
                        margin: 0 0 0.25rem;
                        font-size: 1.1rem;
                        font-weight: 700;
                    }

                    .island-popup .subtitle {
                        display: inline-block;
                        width: 60px;
                        font-weight: 600;
                    }

                    .island-popup hr {
                        border: none;
                        border-top: 3px solid #f97373;
                        /* garis merah */
                        width: 40px;
                        margin: .4rem 0 .8rem;
                    }
                </style>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        // 1. Inisialisasi map (posisi Indonesia)
                        const map = L.map('indo-map').setView([-2, 118], 4.7);

                        // 2. Tambah tile layer (background peta)
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 18,
                            attribution: '&copy; OpenStreetMap'
                        }).addTo(map);

                        // 3. Data pulau dalam bentuk GeoJSON (CONTOH SEDERHANA)
                        // Koordinat di sini hanya kira-kira supaya ada yang muncul.
                        const islandsGeoJson = {
                            "type": "FeatureCollection",
                            "features": [{
                                    "type": "Feature",
                                    "properties": {
                                        "name": "Sumatera",
                                        "aksara": "—",
                                        "kata_khas": "Contoh kata",
                                        "makna": "Deskripsi singkat tentang Sumatera.",
                                        "digunakan_di": "Provinsi-provinsi di Sumatera."
                                    },
                                    "geometry": {
                                        "type": "Polygon",
                                        "coordinates": [
                                            [
                                                [95, 6], // barat laut
                                                [105, 6], // timur laut
                                                [105, -6], // tenggara
                                                [95, -6], // barat daya
                                                [95, 6] // tutup polygon
                                            ]
                                        ]
                                    }
                                },
                                {
                                    "type": "Feature",
                                    "properties": {
                                        "name": "Jawa",
                                        "aksara": "—",
                                        "kata_khas": "Contoh kata",
                                        "makna": "Deskripsi singkat tentang Jawa.",
                                        "digunakan_di": "Provinsi-provinsi di Jawa."
                                    },
                                    "geometry": {
                                        "type": "Polygon",
                                        "coordinates": [
                                            [
                                                [105, -5], // barat
                                                [115, -5], // timur
                                                [115, -9], // tenggara
                                                [105, -9], // barat daya
                                                [105, -5] // tutup polygon
                                            ]
                                        ]
                                    }
                                }
                                // TODO: Tambahkan Kalimantan, Sulawesi, Sunda Kecil, Maluku, Papua
                            ]
                        };

                        // 4. Style default dan style highlight
                        function style(feature) {
                            return {
                                color: '#ffffff',
                                weight: 2,
                                fillColor: '#f97316', // oranye
                                fillOpacity: 0.6
                            };
                        }

                        function highlightStyle(feature) {
                            return {
                                color: '#ffffff',
                                weight: 3,
                                fillColor: '#fb923c',
                                fillOpacity: 0.8
                            };
                        }

                        // 5. Event untuk tiap feature
                        function onEachFeature(feature, layer) {
                            const props = feature.properties;

                            // Tooltip saat hover (nama pulau)
                            layer.bindTooltip(props.name, {
                                sticky: true,
                                direction: 'top'
                            });

                            // Popup/kartu saat diklik
                            const popupHtml = `
                <div class="island-popup">
                    <h3>${props.name}</h3>
                    <hr />
                    <p><span class="subtitle">Aksara:</span> ${props.aksara}</p>
                    <p><span class="subtitle">Kata:</span> ${props.kata_khas}</p>
                    <p><span class="subtitle">Makna:</span> ${props.makna}</p>
                    <p><span class="subtitle">Wilayah:</span> ${props.digunakan_di}</p>
                </div>
            `;
                            layer.bindPopup(popupHtml);

                            // Efek hover
                            layer.on({
                                mouseover: function(e) {
                                    e.target.setStyle(highlightStyle());
                                    // optional: bawa ke depan
                                    e.target.bringToFront();
                                },
                                mouseout: function(e) {
                                    geojson.resetStyle(e.target);
                                },
                                click: function(e) {
                                    map.fitBounds(e.target.getBounds(), {
                                        padding: [20, 20]
                                    });
                                }
                            });
                        }

                        // 6. Tambahkan layer GeoJSON ke map
                        const geojson = L.geoJSON(islandsGeoJson, {
                            style: style,
                            onEachFeature: onEachFeature
                        }).addTo(map);
                    });
                </script>
                {{-- @endsection --}}

                </p>

                {{-- Di sini nanti bisa diisi daftar kartu pulau dari database --}}
                {{-- Contoh placeholder sederhana --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="border border-[var(--line)] rounded-xl p-4 bg-[var(--card)] shadow-sm">
                        <h3 class="font-semibold text-base mb-1">Sumatera</h3>
                        <p class="text-xs text-[var(--muted)]">
                            Pulau dengan beragam budaya dan kuliner khas seperti rendang.
                        </p>
                    </div>
                    <div class="border border-[var(--line)] rounded-xl p-4 bg-[var(--card)] shadow-sm">
                        <h3 class="font-semibold text-base mb-1">Jawa</h3>
                        <p class="text-xs text-[var(--muted)]">
                            Pusat sejarah dan kebudayaan dengan beragam tradisi dan bahasa.
                        </p>
                    </div>
                    <div class="border border-[var(--line)] rounded-xl p-4 bg-[var(--card)] shadow-sm">
                        <h3 class="font-semibold text-base mb-1">Kalimantan</h3>
                        <p class="text-xs text-[var(--muted)]">
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
                <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
                    Indonesia adalah negara kepulauan dengan ratusan suku, bahasa, dan tradisi. Halaman ini
                    mengajakmu menjelajahi keragaman budaya dari Sabang sampai Merauke melalui pulau-pulau utama.
                </p>
                    @include('partials.map-indonesia')
            </section>

            {{-- HISTORY SECTION: Sejarah Nama Pulau di Indonesia --}}
            <section id="history" class="history-section">
                <style>
                    /* ====== WRAPPER (PAKAI BG PARENT) ====== */
                    .history-section {
                        padding: 4rem 1.5rem;
                        background: transparent;
                        display: flex;
                        justify-content: center;
                    }

                    .history-container {
                        width: 100%;
                        max-width: 1100px;
                        text-align: center;
                        font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                        color: var(--txt-body);
                    }

                    .history-title {
                        font-size: clamp(1.75rem, 3vw, 2.25rem);
                        font-weight: 700;
                        margin-bottom: .5rem;
                    }

                    .history-subtitle {
                        font-size: .95rem;
                        max-width: 640px;
                        margin: 0 auto 3rem auto;
                        color: var(--muted);
                    }

                    /* ====== TIMELINE ====== */
                    .timeline {
                        position: relative;
                        padding: 2rem 0;
                        margin: 0 auto;
                    }

                    /* garis tengah */
                    .timeline::before {
                        content: "";
                        position: absolute;
                        top: 0;
                        bottom: 0;
                        left: 50%;
                        width: 4px;
                        transform: translateX(-50%);
                        border-radius: 999px;
                        background: linear-gradient(to bottom, #fef3c7, #f97316);
                    }

                    .timeline-item {
                        position: relative;
                        width: 100%;
                        margin-bottom: 2.5rem;
                        display: flex;
                    }

                    /* titik di tengah */
                    .timeline-item::before {
                        content: "";
                        position: absolute;
                        top: 26px;
                        left: 50%;
                        transform: translateX(-50%);
                        width: 14px;
                        height: 14px;
                        border-radius: 999px;
                        background: var(--bg-body);
                        border: 3px solid #f97316;
                        box-shadow: 0 0 10px rgba(249, 115, 22, 0.6);
                        z-index: 2;
                    }

                    .timeline-card {
                        position: relative;
                        max-width: 520px;
                        border-radius: 20px;
                    }

                    /* ===== NEON BORDER SMOOTH MUTER DI SEPANJANG GARIS CARD ===== */

                    /* Custom property supaya angle bisa dianimasikan smooth */
                    @property --border-angle {
                        syntax: "<angle>";
                        inherits: false;
                        initial-value: 0deg;
                    }

                    .timeline-card-glow {
                        position: absolute;
                        inset: -5px;
                        /* sedikit keluar dari card */
                        border-radius: inherit;
                        padding: 10px;
                        /* ketebalan garis neon */
                        z-index: 0;
                        pointer-events: none;

                        background: conic-gradient(from var(--border-angle),
                                rgba(249, 115, 22, 0),
                                rgba(249, 115, 22, 0.1) 30deg,
                                #f97316 80deg,
                                #fdba74 120deg,
                                rgba(249, 115, 22, 0.1) 180deg,
                                rgba(249, 115, 22, 0) 240deg,
                                rgba(249, 115, 22, 0.15) 300deg,
                                #f97316 330deg,
                                rgba(249, 115, 22, 0) 360deg);

                        /* hanya sisakan garis border, tengahnya bolong */
                        -webkit-mask:
                            linear-gradient(#000 0 0) content-box,
                            linear-gradient(#000 0 0);
                        -webkit-mask-composite: xor;
                        mask-composite: exclude;

                        filter: blur(4px);
                        opacity: 0.95;

                        /* pelan & smooth */
                        animation: neon-border-spin 8s linear infinite;
                    }

                    @keyframes neon-border-spin {
                        to {
                            --border-angle: 360deg;
                        }
                    }

                    /* isi card di dalam ring neon */
                    .timeline-card-inner {
                        position: relative;
                        border-radius: 18px;
                        background: var(--card);
                        padding: 1.6rem 1.8rem;
                        box-shadow:
                            0 14px 32px rgba(0, 0, 0, 0.18),
                            0 0 0 1px rgba(255, 255, 255, 0.12);
                        z-index: 1;
                        text-align: left;
                    }

                    .timeline-badge {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        font-size: .8rem;
                        font-weight: 600;
                        padding: .2rem .75rem;
                        margin-bottom: .5rem;
                        border-radius: 999px;
                        background: linear-gradient(to right, #fef3c7, #f97316);
                        color: #7c2d12;
                    }

                    .timeline-heading {
                        font-size: 1.1rem;
                        margin-bottom: .35rem;
                        color: var(--txt-body);
                    }

                    .timeline-text {
                        font-size: .95rem;
                        line-height: 1.6;
                        color: var(--muted);
                    }

                    .timeline-link {
                        margin-top: .3rem;
                        display: inline-block;
                        font-size: .85rem;
                        font-weight: 600;
                        color: var(--brand, #f97316);
                        text-decoration: none;
                    }

                    .timeline-link:hover {
                        text-decoration: underline;
                    }

                    /* ===== RESPONSIVE ===== */
                    @media (max-width: 767px) {
                        .timeline::before {
                            left: 14px;
                            transform: none;
                        }

                        .timeline-item {
                            padding-left: 2.8rem;
                        }

                        .timeline-item::before {
                            left: 14px;
                            transform: none;
                        }

                        .history-container {
                            text-align: left;
                        }
                    }

                    @media (min-width: 768px) {
                        .timeline-item:nth-child(odd) {
                            justify-content: flex-start;
                            padding-right: 50%;
                        }

                        .timeline-item:nth-child(even) {
                            justify-content: flex-end;
                            padding-left: 50%;
                        }

                        .timeline-item:nth-child(odd) .timeline-card {
                            margin-right: 2.2rem;
                        }

                        .timeline-item:nth-child(even) .timeline-card {
                            margin-left: 2.2rem;
                        }
                    }
                </style>

                <div class="history-container">
                    <h2 class="history-title">Sejarah Nama-Nama Pulau Besar di Indonesia</h2>
                    <p class="history-subtitle">
                        Banyak nama pulau di Indonesia berasal dari bahasa Sanskerta, bahasa lokal, hingga catatan para
                        pelaut
                        dan penjelajah asing. Berikut beberapa kisah singkat di balik namanya.
                    </p>

                    <div class="timeline">
                        {{-- SUMATERA --}}
                        <div class="timeline-item">
                            <div class="timeline-card">
                                <div class="timeline-card-glow"></div>
                                <div class="timeline-card-inner">
                                    <div class="timeline-badge">Sumatera</div>
                                    <h3 class="timeline-heading">Dari Samudera Pasai Menjadi Sumatera</h3>
                                    <p class="timeline-text">
                                        Nama <strong>Sumatera</strong> diyakini berawal dari nama kerajaan <em>Samudera</em>
                                        di pesisir
                                        Aceh. Pengelana Maroko Ibn Battuta (abad ke-14) menuliskan nama itu sebagai
                                        <em>Samatrah</em>. Dalam peta Portugis abad ke-16, bentuknya bergeser menjadi
                                        <em>Sumatra</em>, lalu dikenal luas sebagai nama seluruh pulau.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- JAWA --}}
                        <div class="timeline-item">
                            <div class="timeline-card">
                                <div class="timeline-card-glow"></div>
                                <div class="timeline-card-inner">
                                    <div class="timeline-badge">Jawa</div>
                                    <h3 class="timeline-heading">Yavadvipa: Pulau Gandum dan Padi</h3>
                                    <p class="timeline-text">
                                        Nama <strong>Jawa</strong> sering dikaitkan dengan istilah Sanskerta
                                        <em>Yavadvipa</em> — <em>yava</em> berarti biji-bijian (gandum, jawawut, atau padi),
                                        <em>dvip(a)</em> berarti pulau. Teks India kuno menyebut pulau subur ini sebagai
                                        “pulau tempat tumbuhnya biji-bijian”, yang kemudian diserap menjadi <em>Java /
                                            Jawa</em>.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- KALIMANTAN --}}
                        <div class="timeline-item">
                            <div class="timeline-card">
                                <div class="timeline-card-glow"></div>
                                <div class="timeline-card-inner">
                                    <div class="timeline-badge">Kalimantan</div>
                                    <h3 class="timeline-heading">Kalamanthana: Cuaca yang Membara</h3>
                                    <p class="timeline-text">
                                        Nama <strong>Kalimantan</strong> diturunkan dari istilah Sanskerta
                                        <em>Kalamanthana</em>, yang dapat dimaknai “cuaca yang membakar/panas”. Penduduk
                                        lokal
                                        menyebutnya <em>Pulu K’lemantan</em>, yang kemudian dicatat para pelaut Eropa dan
                                        melekat
                                        sebagai nama resmi wilayah Indonesia di pulau Borneo.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- SULAWESI --}}
                        <div class="timeline-item">
                            <div class="timeline-card">
                                <div class="timeline-card-glow"></div>
                                <div class="timeline-card-inner">
                                    <div class="timeline-badge">Sulawesi</div>
                                    <h3 class="timeline-heading">Pulau Besi dari Timur Nusantara</h3>
                                    <p class="timeline-text">
                                        Nama <strong>Sulawesi</strong> kemungkinan berasal dari kata <em>sula</em> (pulau)
                                        dan
                                        <em>besi</em>, merujuk pada perdagangan bijih besi di kawasan Danau Matano dan
                                        sekitarnya.
                                        Di era kolonial, pulau ini dikenal sebagai <em>Celebes</em>, sebelum nama Sulawesi
                                        dipakai lagi
                                        setelah kemerdekaan Indonesia.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- PAPUA --}}
                        <div class="timeline-item">
                            <div class="timeline-card">
                                <div class="timeline-card-glow"></div>
                                <div class="timeline-card-inner">
                                    <div class="timeline-badge">Papua</div>
                                    <h3 class="timeline-heading">Dari Papo Ua sampai Tanah Timur</h3>
                                    <p class="timeline-text">
                                        Asal-usul nama <strong>Papua</strong> punya beberapa teori. Salah satunya
                                        mengaitkannya
                                        dengan ungkapan Tidore <em>Papo Ua Gam Sio</em>, “sembilan negeri yang belum
                                        bersatu”.
                                        Ada juga yang menghubungkannya dengan istilah lokal yang menggambarkan wilayah di
                                        ujung timur, “tanah di bawah matahari terbenam” bagi masyarakat di baratnya.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- BALI & NUSA TENGGARA --}}
                        <div class="timeline-item">
                            <div class="timeline-card">
                                <div class="timeline-card-glow"></div>
                                <div class="timeline-card-inner">
                                    <div class="timeline-badge">Bali &amp; Nusa Tenggara</div>
                                    <h3 class="timeline-heading">Pulau Persembahan dan Kepulauan Tenggara</h3>
                                    <p class="timeline-text">
                                        Nama <strong>Bali</strong> kerap dikaitkan dengan kata <em>wali</em> (persembahan /
                                        upacara),
                                        sejalan dengan tradisi ritual yang kuat di pulau ini. Sementara <strong>Nusa
                                            Tenggara</strong>
                                        secara harfiah berarti “kepulauan di tenggara” (<em>nusa</em> = pulau,
                                        <em>tenggara</em> = arah
                                        tenggara), merujuk gugusan pulau dari Lombok sampai Timor.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- MALUKU --}}
                        <div class="timeline-item">
                            <div class="timeline-card">
                                <div class="timeline-card-glow"></div>
                                <div class="timeline-card-inner">
                                    <div class="timeline-badge">Maluku</div>
                                    <h3 class="timeline-heading">Tanah Rempah dan Pulau Raja-Raja</h3>
                                    <p class="timeline-text">
                                        Kepulauan <strong>Maluku</strong> sejak lama dikenal sebagai pusat pala dan cengkih
                                        dunia.
                                        Salah satu tafsir menyebut namanya berkaitan dengan ungkapan “pulau raja-raja” dalam
                                        bahasa lokal dan catatan pedagang Arab, merujuk banyaknya kerajaan kecil di gugusan
                                        pulau ini.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- NUSANTARA --}}
                        <div class="timeline-item">
                            <div class="timeline-card">
                                <div class="timeline-card-glow"></div>
                                <div class="timeline-card-inner">
                                    <div class="timeline-badge">Nusantara</div>
                                    <h3 class="timeline-heading">Dari Sumpah Palapa ke Simbol Persatuan</h3>
                                    <p class="timeline-text">
                                        Istilah <strong>Nusantara</strong> sudah muncul dalam naskah Jawa kuna, seperti
                                        Sumpah
                                        Palapa Gajah Mada, untuk menyebut gugusan pulau di luar Jawa yang ingin
                                        dipersatukan.
                                        Di era modern, “Nusantara” menjadi sebutan puitis bagi seluruh kepulauan Indonesia
                                        dan
                                        bahkan dipilih sebagai nama ibu kota negara yang baru di Kalimantan Timur.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

{{-- STATISTIK INDONESIA --}}
<section id="stats" class="mt-10">
    {{-- CSS khusus section ini --}}
    <style>
        /* ================= THEME VAR UNTUK SECTION STATS ================= */
        :root[data-theme='light'] {
            --stats-card-red: linear-gradient(135deg, #fecaca, #f97373);
            --stats-card-purple: linear-gradient(135deg, #ede9fe, #a855f7);
            --stats-card-green: linear-gradient(135deg, #bbf7d0, #22c55e);

            --stats-card-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);

            --stats-chart-bg: radial-gradient(circle at top left, #f9fafb, #e5e7eb);
            --stats-chart-border: rgba(15, 23, 42, 0.08);
        }

        :root[data-theme='dark'] {
            --stats-card-red: linear-gradient(135deg, #dc2626, #7f1d1d);
            --stats-card-purple: linear-gradient(135deg, #7c3aed, #4c1d95);
            --stats-card-green: linear-gradient(135deg, #059669, #065f46);

            --stats-card-shadow: 0 18px 40px rgba(0, 0, 0, 0.55);

            --stats-chart-bg: radial-gradient(circle at top left, #111827, #020617);
            --stats-chart-border: rgba(255, 255, 255, 0.08);
        }

        #stats .stat-card {
            position: relative;
            border-radius: 18px;
            padding: 18px 18px 16px 18px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: var(--stats-card-shadow);
            cursor: pointer;
            overflow: hidden;

            opacity: 0;
            transform: translateY(16px);
            animation: statsFadeUp 0.7s ease-out forwards;
        }

        /* varian warna kartu – diambil dari CSS variable di atas */
        #stats .stat-card--red {
            background: var(--stats-card-red);
        }

        #stats .stat-card--purple {
            background: var(--stats-card-purple);
        }

        #stats .stat-card--green {
            background: var(--stats-card-green);
        }

        #stats .stat-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.16), transparent 55%);
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

        /* kartu chart: pakai var theme supaya cocok di light & dark */
        #stats .chart-card {
            position: relative;
            border-radius: 18px;
            padding: 16px 16px 12px 16px;
            background: var(--stats-chart-bg);
            border: 1px solid var(--stats-chart-border);
            box-shadow: var(--stats-card-shadow);
            overflow: hidden;

            opacity: 0;
            transform: translateY(16px);
            animation: statsFadeUp 0.7s ease-out forwards;
            color: var(--txt-body);
        }

        #stats .chart-title {
            font-size: 0.95rem;
            font-weight: 600;
        }

        #stats .chart-subtitle {
            font-size: 0.75rem;
            color: var(--muted);
        }

        #stats .chart-wrapper {
            position: relative;
            width: 100%;
            height: 220px;
        }

        /* stagger animasi */
        #stats .stat-card[data-stat="islands"] {
            animation-delay: 0.05s;
        }

        #stats .stat-card[data-stat="unesco"] {
            animation-delay: 0.12s;
        }

        #stats .stat-card[data-stat="population"] {
            animation-delay: 0.19s;
        }

        #stats .chart-card:nth-child(1) {
            animation-delay: 0.26s;
        }

        #stats .chart-card:nth-child(2) {
            animation-delay: 0.33s;
        }

        #stats .chart-card:nth-child(3) {
            animation-delay: 0.40s;
        }

        @keyframes statsFadeUp {
            from {
                opacity: 0;
                transform: translateY(18px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
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
            background: var(--card);
            color: var(--txt-body);
            border-color: var(--line);
        }

        #stats-modal-title {
            color: var(--txt-body);
        }

        #stats-modal-body {
            color: var(--muted);
        }

        @media (max-width: 640px) {
            #stats .stat-number {
                font-size: 2.1rem;
            }

            #stats .chart-wrapper {
                height: 210px;
            }
        }
    </style>

    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3 text-[var(--txt-body)]">
        Statistik Budaya Indonesia
    </h2>
    <p class="text-sm sm:text-base text-[var(--muted)] mb-4 max-w-3xl">
        Ringkasan keragaman Indonesia: jumlah pulau, warisan budaya takbenda yang diakui
        UNESCO, serta dinamika jumlah penduduk.
    </p>

    {{-- TIGA CARD UTAMA --}}
    <div class="grid gap-4 lg:grid-cols-3 mb-6">
        {{-- Pulau di Indonesia --}}
        <button type="button" class="stat-card stat-card--red text-left text-white" data-stat="islands">
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
        <button type="button" class="stat-card stat-card--purple text-left text-white" data-stat="unesco">
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
        <button type="button" class="stat-card stat-card--green text-left text-white" data-stat="population">
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
        <div class="chart-card">
            <div class="flex items-center justify-between mb-2">
                <p class="chart-title">14 Suku Terbesar (persentase penduduk)</p>
                <span class="chart-subtitle">Bar chart</span>
            </div>
            <div class="chart-wrapper">
                <canvas id="ethnicChart"></canvas>
            </div>
            <p class="mt-2 text-[11px] text-[var(--muted)]">
                Data diadaptasi dari sensus: Jawa &gt;40%, diikuti Sunda, Melayu, Batak,
                dan suku-suku besar lainnya. Slice terakhir = <em>Lainnya</em>.
            </p>
        </div>

        {{-- 2. BAHASA SEHARI-HARI – DONUT CHART --}}
        <div class="chart-card">
            <div class="flex items-center justify-between mb-2">
                <p class="chart-title">Bahasa yang Paling Banyak Digunakan</p>
                <span class="chart-subtitle">Donut chart</span>
            </div>
            <div class="chart-wrapper">
                <canvas id="languageChart"></canvas>
            </div>
            <p class="mt-2 text-[11px] text-[var(--muted)]">
                14 bahasa dengan penutur terbanyak, berdasarkan persentase penggunaan
                sehari-hari. Sisanya digabung sebagai <em>Lainnya</em>.
            </p>
        </div>

        {{-- 3. AGAMA – PIE CHART --}}
        <div class="chart-card">
            <div class="flex items-center justify-between mb-2">
                <p class="chart-title">Komposisi Agama di Indonesia (±2021)</p>
                <span class="chart-subtitle">Pie chart</span>
            </div>
            <div class="chart-wrapper">
                <canvas id="religionChart"></canvas>
            </div>
            <p class="mt-2 text-[11px] text-[var(--muted)]">
                Islam mendominasi populasi, diikuti Protestan, Katolik, Hindu, Buddha,
                Konghucu, dan agama lainnya.
            </p>
        </div>
    </div>

    <p class="mt-3 text-[11px] text-[var(--muted)] opacity-80">
        *Angka dibulatkan. Persentase dan jumlah bisa sedikit berbeda antar sumber resmi,
        tetapi kisaran nilainya tetap sama.
    </p>

    {{-- POPUP DETAIL UNTUK SEMUA CARD --}}
    <div id="stats-modal-backdrop" class="fixed inset-0 z-40 bg-black/60 items-center justify-center px-4"
        aria-hidden="true">
        <div id="stats-modal" class="max-w-lg w-full rounded-2xl border p-5 sm:p-6 relative">
            <button type="button" id="stats-modal-close"
                class="absolute right-4 top-3 text-[var(--muted)] hover:opacity-100 text-xl leading-none"
                aria-label="Tutup">
                ×
            </button>

            <h3 id="stats-modal-title" class="text-lg sm:text-xl font-semibold mb-2">
                Detail Statistik
            </h3>

            <div id="stats-modal-body" class="text-sm space-y-2 leading-relaxed">
                {{-- konten diisi via JS --}}
            </div>

            <p class="mt-4 text-[11px] text-[var(--muted)] opacity-70">
                Ringkasan berdasarkan data lembaga resmi Indonesia, UNESCO, dan publikasi terkait.
            </p>
        </div>
    </div>

    {{-- SCRIPT POPUP + CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        (function() {
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

            document.querySelectorAll('#stats .stat-card[data-stat]').forEach(function(card) {
                card.addEventListener('click', function() {
                    const key = card.getAttribute('data-stat');
                    openModal(key);
                });
            });

            closeBtn.addEventListener('click', closeModal);
            backdrop.addEventListener('click', function(e) {
                if (e.target === backdrop) closeModal();
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeModal();
            });

            /* ================= CHART: DATA ================= */
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
                animation: {
                    duration: 900,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                const label = ctx.label || '';
                                const value = ctx.parsed;
                                return label + ': ' + value.toFixed(2) + '%';
                            }
                        }
                    },
                    legend: {
                        labels: {

                            font: {
                                size: 11
                            }
                        }
                    }
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
                    plugins: {
                        ...commonOptions.plugins,
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#9ca3af',
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#9ca3af',
                                callback: value => value + '%'
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.15)'
                            }
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
                <p class="text-sm sm:text-base text-[var(--muted)]">
                    Di sini nanti bisa jadi area kuis umum tentang Nusantara — misalnya pilihan ganda tentang
                    pulau, suku, atau rumah adat.
                    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                </p>
            </section>
        </div>
    </section>
@endsection
