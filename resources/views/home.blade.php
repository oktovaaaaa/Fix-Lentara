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
<section id="islands" class="mt-10">
    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3 text-[var(--txt-body)]">
        Jelajahi Pulau-Pulau Indonesia
    </h2>

    <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed mb-6">
        Pilih salah satu pulau untuk melihat budaya, suku, tradisi, destinasi, dan kuliner khasnya.
        Konten tiap pulau bisa kamu kembangkan dari 3 suku utama yang kamu pilih.
    </p>

    {{-- CARD + MODAL STYLE (theme-safe: light/dark) --}}
    <style>
        /* ===== Card Look (mirip screenshot) ===== */
        #islands .nus-card {
            border-radius: 1.25rem;
            padding: 1.1rem 1.2rem;
            background:
                radial-gradient(1200px circle at 10% 0%, rgba(255,255,255,.06), transparent 45%),
                linear-gradient(180deg, rgba(255,255,255,.06), rgba(0,0,0,.05));
            box-shadow: 0 20px 55px rgba(0,0,0,.35);
            border: 1px solid rgba(255,255,255,.06);
            overflow: hidden;
        }

        #islands .nus-card-title {
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--txt-body);
        }

        #islands .nus-card-link {
            color: #ef4444;
            font-weight: 800;
        }
        #islands .nus-card-link:hover { text-decoration: underline; }

        /* ===== Island image ===== */
        #islands .island-thumb {
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid color-mix(in oklab, var(--line) 70%, transparent);
            background: color-mix(in oklab, var(--card) 80%, transparent);
            cursor: zoom-in;
            position: relative;
        }

        #islands .island-thumb::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255,255,255,.12), transparent 55%);
            opacity: 0;
            transition: opacity .2s ease;
            pointer-events: none;
        }
        #islands .island-thumb:hover::after { opacity: 1; }

        #islands .island-thumb img {
            width: 100%;
            height: 160px;
            object-fit: contain; /* aman untuk PNG pulau */
            display: block;
            transform: scale(1);
            transition: transform .22s ease;
            padding: 10px;
        }
        #islands .island-thumb:hover img { transform: scale(1.02); }

        #islands .thumb-hint {
            position: absolute;
            right: 10px;
            bottom: 10px;
            font-size: 11px;
            padding: 5px 10px;
            border-radius: 999px;
            border: 1px solid color-mix(in oklab, var(--line) 70%, transparent);
            background: color-mix(in oklab, var(--card) 85%, transparent);
            color: var(--muted);
            backdrop-filter: blur(6px);
        }

        /* ===== Modal (popup) ===== */
        #island-modal-backdrop { display: none; }
        #island-modal-backdrop.is-open { display: flex; }

        #island-modal {
            background: var(--card);
            color: var(--txt-body);
            border: 1px solid color-mix(in oklab, var(--line) 85%, transparent);
            box-shadow: 0 25px 70px rgba(0,0,0,.55);
            border-radius: 20px;
            transform: translateY(12px) scale(.97);
            opacity: 0;
            transition: all .22s ease-out;
            overflow: hidden;
        }
        #island-modal-backdrop.is-open #island-modal {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        #island-modal-title { color: var(--txt-body); }
        #island-modal-subtitle { color: var(--muted); }

        #island-modal-image {
            width: 100%;
            height: min(62vh, 520px);
            object-fit: contain;
            background:
                radial-gradient(800px circle at 10% 0%, rgba(255,255,255,.05), transparent 50%),
                color-mix(in oklab, var(--bg-body) 70%, var(--card) 30%);
            display: block;
            padding: 18px;
        }

        .island-modal-close {
            width: 40px;
            height: 40px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            border: 1px solid color-mix(in oklab, var(--line) 85%, transparent);
            background: color-mix(in oklab, var(--card) 92%, transparent);
            color: var(--txt-body);
            transition: transform .15s ease, filter .15s ease;
        }
        .island-modal-close:hover { transform: translateY(-1px); filter: brightness(1.08); }
    </style>

    @php
        $islandCards = [
            [
                'key'  => 'sumatera',
                'name' => 'Sumatera',
                'desc' => 'Jejak kerajaan maritim, ragam adat, dan kuliner rempah yang kuat—dari pesisir hingga dataran tinggi.',
                'href' => url('/islands/sumatera'),
                'img'  => asset('images/islands/sumatera.png'),
            ],
            [
                'key'  => 'jawa',
                'name' => 'Jawa',
                'desc' => 'Pusat sejarah & kebudayaan: keraton, batik, seni pertunjukan, serta ragam bahasa daerah yang hidup.',
                'href' => url('/islands/jawa'),
                'img'  => asset('images/islands/jawa.png'),
            ],
            [
                'key'  => 'kalimantan',
                'name' => 'Kalimantan',
                'desc' => 'Bentang hutan tropis dan sungai besar, dengan tradisi Dayak yang beragam dan kaya simbol.',
                'href' => url('/islands/kalimantan'),
                'img'  => asset('images/islands/kalimantan.png'),
            ],
            [
                'key'  => 'sulawesi',
                'name' => 'Sulawesi',
                'desc' => 'Persimpangan budaya maritim & pegunungan—ritual, rumah adat, dan tradisi pelayaran yang kuat.',
                'href' => url('/islands/sulawesi'),
                'img'  => asset('images/islands/sulawesi.png'),
            ],
            [
                'key'  => 'bali-nusa-tenggara',
                'name' => 'Bali & Nusa Tenggara',
                'desc' => 'Ritual dan seni yang kuat, lanskap vulkanik, pesisir, hingga savana—ragam budaya pulau-pulau kecil.',
                'href' => url('/islands/bali-nusa-tenggara'),
                'img'  => asset('images/islands/bali-nusa-tenggara.png'),
            ],
            [
                'key'  => 'papua-maluku',
                'name' => 'Papua & Maluku',
                'desc' => 'Kawasan timur dengan kekayaan bahasa, tradisi, dan bentang alam ikonik—dari kepulauan rempah hingga pegunungan.',
                'href' => url('/islands/papua-maluku'),
                'img'  => asset('images/islands/papua-maluku.png'),
            ],
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach ($islandCards as $c)
            <div class="nus-card text-[var(--txt-body)]">
                {{-- THUMB (klik -> modal) --}}
                <button type="button"
                        class="island-thumb w-full mb-4"
                        data-island-modal="1"
                        data-title="{{ $c['name'] }}"
                        data-desc="{{ $c['desc'] }}"
                        data-img="{{ $c['img'] }}"
                        aria-label="Lihat gambar {{ $c['name'] }}">
                    <img src="{{ $c['img'] }}"
                         alt="Peta 3D {{ $c['name'] }}"
                         loading="lazy">
                    <span class="thumb-hint">Klik untuk zoom</span>
                </button>

                <h3 class="nus-card-title text-lg sm:text-xl mb-2">
                    {{ $c['name'] }}
                </h3>

                <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed mb-3">
                    {{ $c['desc'] }}
                </p>

                <a href="{{ $c['href'] }}" class="nus-card-link text-sm">
                    Selengkapnya &gt;
                </a>
            </div>
        @endforeach
    </div>

    {{-- MODAL POPUP (gambar pulau) --}}
    <div id="island-modal-backdrop"
         class="fixed inset-0 z-50 bg-black/60 items-center justify-center px-4"
         aria-hidden="true">
        <div id="island-modal" class="w-full max-w-3xl">
            <div class="flex items-start justify-between gap-4 p-4 sm:p-5 border-b"
                 style="border-color: color-mix(in oklab, var(--line) 85%, transparent);">
                <div>
                    <h3 id="island-modal-title" class="text-lg sm:text-xl font-extrabold">Detail Pulau</h3>
                    <p id="island-modal-subtitle" class="text-xs sm:text-sm mt-1"></p>
                </div>

                <button type="button"
                        class="island-modal-close"
                        id="island-modal-close"
                        aria-label="Tutup">
                    ✕
                </button>
            </div>

            <img id="island-modal-image" src="" alt="Gambar Pulau" />

            <div class="p-4 sm:p-5">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs text-[var(--muted)]">
                        Tip: tekan <strong>Esc</strong> atau klik area gelap untuk menutup.
                    </p>
                    <button type="button"
                            id="island-modal-open-new"
                            class="text-xs font-extrabold underline"
                            style="color: color-mix(in oklab, var(--brand) 75%, #f59e0b 25%);">
                        Buka gambar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT MODAL (ringan, no library) --}}
    <script>
        (function () {
            const backdrop = document.getElementById('island-modal-backdrop');
            const modalImg = document.getElementById('island-modal-image');
            const modalTitle = document.getElementById('island-modal-title');
            const modalSub = document.getElementById('island-modal-subtitle');
            const closeBtn = document.getElementById('island-modal-close');
            const openNewBtn = document.getElementById('island-modal-open-new');

            if (!backdrop || !modalImg || !modalTitle || !modalSub || !closeBtn || !openNewBtn) return;

            function openModal({ title, desc, img }) {
                modalTitle.textContent = title || 'Detail Pulau';
                modalSub.textContent = desc || '';
                modalImg.src = img || '';
                modalImg.alt = title ? ('Peta 3D ' + title) : 'Gambar Pulau';
                openNewBtn.onclick = () => {
                    if (img) window.location.href = img; // tab sama (sesuai request kamu)
                };

                backdrop.classList.add('is-open');
                document.body.classList.add('overflow-hidden');
            }

            function closeModal() {
                backdrop.classList.remove('is-open');
                document.body.classList.remove('overflow-hidden');

                // reset src biar hemat memory kalau gambar besar
                modalImg.src = '';
            }

            document.querySelectorAll('#islands [data-island-modal="1"]').forEach(btn => {
                btn.addEventListener('click', () => {
                    openModal({
                        title: btn.getAttribute('data-title'),
                        desc: btn.getAttribute('data-desc'),
                        img: btn.getAttribute('data-img'),
                    });
                });
            });

            closeBtn.addEventListener('click', closeModal);
            backdrop.addEventListener('click', (e) => {
                if (e.target === backdrop) closeModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeModal();
            });
        })();
    </script>
</section>
    

{{-- ABOUT INDONESIA --}}
<section id="about" class="mt-14">
    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
        Tentang Indonesia
    </h2>

    <div class="space-y-3">
        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
            Indonesia dikenal sebagai negara kepulauan. Pembaruan data Badan Informasi Geospasial (BIG) pada tahun 2024
            mencatat <strong>17.380 pulau</strong> yang sudah bernama dan memiliki koordinat.
            Dari sisi kebahasaan, Peta Bahasa Kemendikbud mencatat <strong>718 bahasa daerah</strong>.
            Sementara itu, IndonesiaBaik merangkum bahwa Indonesia memiliki <strong>1.340 suku bangsa</strong>.
        </p>

        <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed">
            Identitas kebangsaan Indonesia tumbuh lewat perjalanan sejarah panjang, dan puncaknya ditegaskan melalui
            Proklamasi Kemerdekaan pada 17 Agustus 1945.
        </p>

        {{-- sumber: biar juri bisa klik --}}
        <div class="text-xs text-[var(--muted)] leading-relaxed">
            <span class="font-semibold">Sumber resmi:</span>
            <a class="underline hover:text-[var(--accent,#f97316)]" target="_blank" rel="noopener"
               href="https://sipulau.big.go.id/news/11">BIG – SI PULAU (17.380 pulau)</a>
            <span class="mx-2">•</span>
            <a class="underline hover:text-[var(--accent,#f97316)]" target="_blank" rel="noopener"
               href="https://petabahasa.kemdikbud.go.id/">Peta Bahasa Kemendikbud (718 bahasa)</a>
            <span class="mx-2">•</span>
            <a class="underline hover:text-[var(--accent,#f97316)]" target="_blank" rel="noopener"
               href="https://indonesiabaik.id/infografis/sebaran-jumlah-suku-di-indonesia">IndonesiaBaik (1.340 suku)</a>
            <span class="mx-2">•</span>
            <a class="underline hover:text-[var(--accent,#f97316)]" target="_blank" rel="noopener"
               href="https://www.setneg.go.id/baca/index/membuka_catatan_sejarah_detik_detik_proklamasi_17_agustus_1945">Setneg (Proklamasi 1945)</a>
        </div>
    </div>

    {{-- MAP (hanya di Tentang, sesuai request kamu) --}}
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
                    <button type="button" class="stat-card stat-card--green text-left text-white"
                        data-stat="population">
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
<section id="quiz" class="py-10">
  <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3 text-[var(--txt-body)]">
    Kuis Budaya Indonesia
  </h2>
  <p class="text-sm sm:text-base text-[var(--muted)] mb-4">
    Jawab pertanyaan seputar Nusantara. Soal & opsi bisa berupa teks atau gambar.
  </p>

  @include('partials.quiz-section', ['quiz' => $quiz ?? null])
</section>




            <section id="testimoni" class="mt-12">
    @php
        $testimonials = $testimonials ?? collect();
        $testimonialStats = $testimonialStats ?? ['counts'=>[1=>0,2=>0,3=>0,4=>0,5=>0],'total'=>0,'avg'=>0];

        $counts = $testimonialStats['counts'];
        $total  = (int) $testimonialStats['total'];
        $avg    = (float) $testimonialStats['avg'];

        // helper % bar
        $pct = function($n) use ($total) {
            return $total > 0 ? round(($n / $total) * 100) : 0;
        };
    @endphp

    <style>
        /* ===== TESTIMONI THEME SAFE (LIGHT/DARK) ===== */
        .t-wrap { color: var(--txt-body); }
        .t-card {
            background: color-mix(in oklab, var(--card) 88%, transparent);
            border: 1px solid color-mix(in oklab, var(--line) 85%, transparent);
            box-shadow: var(--shadow);
            border-radius: 18px;
        }
        .t-soft {
            background: color-mix(in oklab, var(--bg-body) 75%, var(--card) 25%);
            border: 1px solid color-mix(in oklab, var(--line) 85%, transparent);
            border-radius: 16px;
        }
        .t-muted { color: var(--muted); }

        .t-input, .t-textarea {
            width: 100%;
            border-radius: 12px;
            border: 1px solid color-mix(in oklab, var(--line) 85%, transparent);
            background: color-mix(in oklab, var(--card) 92%, transparent);
            color: var(--txt-body);
            padding: 10px 12px;
            outline: none;
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        }
        .t-input::placeholder, .t-textarea::placeholder { color: color-mix(in oklab, var(--muted) 75%, transparent); }
        .t-input:focus, .t-textarea:focus {
            border-color: color-mix(in oklab, var(--brand) 70%, transparent);
            box-shadow: 0 0 0 4px color-mix(in oklab, var(--brand) 18%, transparent);
        }

        /* Rating Stars (click) */
        .star-row { display: inline-flex; gap: 6px; align-items: center; }
        .star-btn {
            width: 30px; height: 30px;
            display: grid; place-items: center;
            border-radius: 999px;
            border: 1px solid color-mix(in oklab, var(--line) 85%, transparent);
            background: color-mix(in oklab, var(--card) 92%, transparent);
            cursor: pointer;
            transition: transform .12s ease, box-shadow .18s ease, border-color .18s ease;
            user-select: none;
        }
        .star-btn:hover { transform: translateY(-1px) scale(1.03); box-shadow: 0 8px 18px rgba(0,0,0,.12); }
        .star {
            font-size: 18px;
            line-height: 1;
            color: color-mix(in oklab, var(--muted) 40%, transparent);
            transition: transform .12s ease, color .18s ease;
        }
        .star.is-on { color: #f59e0b; transform: scale(1.05); } /* amber */

        /* Progress bars */
        .t-bar {
            height: 10px;
            border-radius: 999px;
            background: color-mix(in oklab, var(--line) 55%, transparent);
            overflow: hidden;
        }
        .t-bar > span {
            display: block;
            height: 100%;
            width: 0%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f59e0b, #f97316);
            transition: width .5s ease;
        }

        /* Scroll area */
        .t-scroll {
            max-height: 460px;
            overflow: auto;
            padding-right: 6px;
        }
        .t-scroll::-webkit-scrollbar { width: 10px; }
        .t-scroll::-webkit-scrollbar-thumb {
            background: color-mix(in oklab, var(--line) 70%, transparent);
            border-radius: 999px;
        }

        /* File input nicer */
        .t-file {
            width: 100%;
            border-radius: 12px;
            border: 1px dashed color-mix(in oklab, var(--line) 85%, transparent);
            background: color-mix(in oklab, var(--card) 92%, transparent);
            color: var(--txt-body);
            padding: 10px 12px;
        }

        .t-btn {
            border-radius: 14px;
            padding: 12px 16px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--brand), #f59e0b);
            color: #fff;
            border: 0;
            box-shadow: 0 18px 30px rgba(249,115,22,.18);
            transition: transform .18s ease, filter .18s ease;
        }
        .t-btn:hover { transform: translateY(-1px); filter: brightness(1.05); }
        .t-btn:active { transform: translateY(0px); }

        /* small meta in card */
        .t-chip {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid color-mix(in oklab, var(--line) 85%, transparent);
            background: color-mix(in oklab, var(--card) 92%, transparent);
            color: var(--muted);
        }
    </style>

    <div class="t-wrap max-w-6xl mx-auto px-4 sm:px-6">
        <h2 class="text-2xl sm:text-3xl font-extrabold mb-2">Testimoni Pengunjung</h2>
        <p class="t-muted text-sm mb-6">Bagikan pengalamanmu, bantu kami jadi lebih baik.</p>

        {{-- ===== SUMMARY (TOP) ===== --}}
        <div class="grid gap-4 lg:grid-cols-3 mb-6">
            {{-- Left: distribution --}}
            <div class="t-card p-5 lg:col-span-2">
                <div class="flex items-center justify-between mb-3">
                    <div class="font-bold">Ringkasan Rating</div>
                    <div class="t-chip">{{ $total }} Rating</div>
                </div>

                @for($r = 5; $r >= 1; $r--)
                    @php $p = $pct($counts[$r]); @endphp
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-16 text-xs font-bold tracking-wide uppercase t-muted">
                            {{ $r }} ★
                        </div>
                        <div class="flex-1 t-bar">
                            <span style="width: {{ $p }}%"></span>
                        </div>
                        <div class="w-14 text-right text-xs t-muted">
                            {{ $counts[$r] }}
                        </div>
                    </div>
                @endfor
            </div>

            {{-- Right: average --}}
            <div class="t-card p-5 flex flex-col items-center justify-center text-center">
                <div class="text-4xl font-extrabold" style="color: color-mix(in oklab, var(--brand) 70%, #f59e0b 30%);">
                    {{ number_format($avg, 1) }}
                </div>

                <div class="mt-2 flex items-center justify-center gap-1">
                    @php
                        $rounded = (int) round($avg);
                    @endphp
                    @for($i=1; $i<=5; $i++)
                        <span class="text-2xl" style="color: {{ $i <= $rounded ? '#f59e0b' : 'color-mix(in oklab, var(--line) 85%, transparent)' }};">★</span>
                    @endfor
                </div>

                <div class="mt-2 t-muted text-sm">
                    Dari {{ $total }} rating
                </div>
            </div>
        </div>

        {{-- ===== MAIN (BOTTOM): left list + right form ===== --}}
        <div class="grid gap-4 lg:grid-cols-2">
            {{-- LEFT: recent feedbacks --}}
            <div class="t-card p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="font-bold text-lg">Recent Feedbacks</div>
                    <div class="t-chip">Terbaru</div>
                </div>

                <div class="t-scroll space-y-3">
                    @forelse($testimonials as $t)
                        <div class="t-soft p-4">
                            <div class="flex items-start gap-3">
                                <img
                                    class="w-14 h-14 rounded-full object-cover border border-[var(--line)]"
                                    src="{{ $t->photo ? asset('storage/'.$t->photo) : asset('images/avatar.png') }}"
                                    alt="Avatar {{ $t->name }}"
                                />

                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <div class="font-extrabold">{{ $t->name }}</div>
                                            <div class="t-muted text-xs">
                                                {{ $t->created_at->translatedFormat('d F Y') }}
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <div class="flex justify-end gap-0.5">
                                                @for($i=1; $i<=5; $i++)
                                                    <span style="color: {{ $i <= $t->rating ? '#f59e0b' : 'color-mix(in oklab, var(--line) 85%, transparent)' }};">★</span>
                                                @endfor
                                            </div>
                                            <div class="t-muted text-[11px] mt-1">
                                                {{ $t->created_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>

                                    <p class="mt-2 text-sm leading-relaxed">
                                        {{ $t->message }}
                                    </p>

                                    <div class="mt-3 flex justify-end">
                                        <button
                                            type="button"
                                            onclick="openReportModal('{{ route('testimonials.report', $t) }}')"
                                            class="text-xs font-bold text-red-500 hover:underline">
                                            Laporkan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="t-muted text-sm">Belum ada testimoni. Jadilah yang pertama 😊</div>
                    @endforelse
                </div>
            </div>

            {{-- RIGHT: add a review --}}
            <div class="t-card p-5">
                <div class="font-bold text-lg mb-3">Add a Review</div>

                {{-- flash message --}}
                @if(session('success'))
                    <div class="mb-3 t-soft p-3 text-sm" style="border-color: color-mix(in oklab, var(--brand) 45%, var(--line) 55%);">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                {{-- error bag --}}
                @if($errors->any())
                    <div class="mb-3 t-soft p-3 text-sm" style="border-color: rgba(239,68,68,.5);">
                        <div class="font-bold mb-1">Gagal mengirim:</div>
                        <ul class="list-disc list-inside t-muted">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('testimonials.store') }}"
                      enctype="multipart/form-data"
                      class="space-y-3"
                      id="testimonialForm">
                    @csrf

                    {{-- Honeypot --}}
                    <input type="text" name="website" value="" autocomplete="off" tabindex="-1"
                           style="position:absolute;left:-9999px;top:-9999px;height:1px;width:1px;opacity:0;">

                    {{-- Rating --}}
                    <div>
                        <label class="text-sm font-bold">Rating <span class="text-red-500">*</span></label>
                        <input type="hidden" name="rating" id="ratingValue" value="{{ old('rating', 0) }}">

                        <div class="mt-2 star-row" id="starRow">
                            @for($i=1; $i<=5; $i++)
                                <button type="button" class="star-btn" data-star="{{ $i }}" aria-label="Pilih {{ $i }} bintang">
                                    <span class="star">★</span>
                                </button>
                            @endfor
                        </div>
                        <div class="t-muted text-xs mt-1">Klik bintang untuk memilih rating.</div>
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label class="text-sm font-bold">Nama <span class="text-red-500">*</span></label>
                        <input class="t-input" name="name" value="{{ old('name') }}" placeholder="Nama kamu">
                    </div>

                    {{-- Pesan --}}
                    <div>
                        <label class="text-sm font-bold">Pesan <span class="text-red-500">*</span></label>
                        <textarea class="t-textarea" name="message" rows="5" placeholder="Tulis pengalamanmu...">{{ old('message') }}</textarea>
                    </div>

                    {{-- Foto (opsional) --}}
                    <div>
                        <label class="text-sm font-bold">Foto Profil <span class="t-muted text-xs">(opsional, max 5MB)</span></label>
                        <input class="t-file" type="file" name="photo" accept="image/png,image/jpeg,image/jpg">
                        <div class="t-muted text-xs mt-1">Format: JPG / JPEG / PNG.</div>
                    </div>

                    <button class="t-btn w-full mt-2">Submit</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ================= MODAL REPORT (tetap) ================= --}}
    <div id="reportModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4">
        <div class="t-card w-full max-w-md p-6"
             style="animation: scaleIn .18s ease-out;">
            <h3 class="text-lg font-extrabold mb-1">Laporkan Testimoni</h3>
            <p class="t-muted text-xs mb-4">Pilih alasan laporan. Admin akan meninjau laporan ini.</p>

            <form id="reportForm" method="POST">
                @csrf

                <label class="text-sm font-bold">Alasan</label>
                <select name="reason" required class="t-input mt-1 mb-3">
                    <option value="Spam">Spam</option>
                    <option value="Ujaran kebencian">Ujaran kebencian</option>
                    <option value="Tidak pantas">Tidak pantas</option>
                    <option value="Penipuan">Penipuan</option>
                    <option value="Lainnya">Lainnya</option>
                </select>

                <label class="text-sm font-bold">
                    Catatan tambahan <span class="t-muted text-xs">(opsional)</span>
                </label>
                <textarea name="note" rows="3" class="t-textarea mt-1"
                          placeholder="Tulis catatan tambahan bila perlu..."></textarea>

                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" onclick="closeReportModal()"
                            class="t-input !w-auto !px-4 !py-2 font-bold">
                        Batal
                    </button>

                    <button class="t-btn !w-auto !px-4 !py-2">
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ===== modal report =====
        function openReportModal(actionUrl) {
            const modal = document.getElementById('reportModal');
            const form  = document.getElementById('reportForm');
            form.action = actionUrl;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeReportModal() {
            const modal = document.getElementById('reportModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        document.getElementById('reportModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'reportModal') closeReportModal();
        });

        // ===== rating stars =====
        (function () {
            const row = document.getElementById('starRow');
            const input = document.getElementById('ratingValue');
            if (!row || !input) return;

            function paint(v) {
                const stars = row.querySelectorAll('.star-btn .star');
                stars.forEach((el, idx) => {
                    const n = idx + 1;
                    el.classList.toggle('is-on', n <= v);
                });
            }

            // init (old value)
            const initial = parseInt(input.value || '0', 10);
            paint(initial);

            row.querySelectorAll('.star-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const v = parseInt(btn.dataset.star, 10);
                    input.value = v;
                    paint(v);
                });
            });

            // prevent submit rating=0
            document.getElementById('testimonialForm')?.addEventListener('submit', (e) => {
                const v = parseInt(input.value || '0', 10);
                if (!v || v < 1) {
                    e.preventDefault();
                    alert('Silakan pilih rating bintang dulu.');
                }
            });
        })();

        // ===== anim keyframes =====
        const style = document.createElement('style');
        style.textContent = `
            @keyframes scaleIn {
                from { opacity:0; transform: scale(.94); }
                to   { opacity:1; transform: scale(1); }
            }
        `;
        document.head.appendChild(style);
    </script>
</section>


        </div>
    </section>
@endsection
