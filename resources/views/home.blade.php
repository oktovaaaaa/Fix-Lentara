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
        <div class="max-w-6xl mx-auto">

            {{-- ================= JUDUL KONSISTEN NEON ORANGE ================= --}}
            <style>
                .neon-title {
                    text-align: center !important;
                    display: block !important;
                    width: 100% !important;
                    margin-left: auto !important;
                    margin-right: auto !important;
                    font-size: clamp(2rem, 4vw, 3rem);
                    font-weight: 800;
                    margin-bottom: 0.5rem;
                    display: inline-block;
                    position: relative;
                    background: linear-gradient(90deg,
                            #ff6b00 0%,
                            #ff8c42 25%,
                            #ffaa6b 50%,
                            #ff8c42 75%,
                            #ff6b00 100%);
                    background-size: 200% auto;
                    background-clip: text;
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    animation: neon-glow 3s ease-in-out infinite;
                }

                .title-decoration {
                    width: 120px;
                    height: 4px;
                    margin: 0.8rem auto 1.5rem;
                    background: linear-gradient(90deg, transparent, #ff6b00, transparent);
                    border-radius: 2px;
                    position: relative;
                }

                .title-decoration::before,
                .title-decoration::after {
                    content: '';
                    position: absolute;
                    width: 8px;
                    height: 8px;
                    background: #ff6b00;
                    border-radius: 50%;
                    top: 50%;
                    transform: translateY(-50%);
                    box-shadow: 0 0 12px #ff6b00;
                }

                .title-decoration::before {
                    left: 0;
                }

                .title-decoration::after {
                    right: 0;
                }

                .neon-subtitle {
                    font-size: 1.1rem;
                    color: var(--muted);
                    max-width: 760px;
                    margin: 0 auto 2rem;
                    line-height: 1.6;
                    text-align: center;
                }

                @keyframes neon-glow {

                    0%,
                    100% {
                        background-position: 0% 50%;
                        text-shadow: 0 0 20px rgba(255, 107, 0, 0.3),
                            0 0 40px rgba(255, 140, 66, 0.2);
                    }

                    50% {
                        background-position: 100% 50%;
                        text-shadow: 0 0 30px rgba(255, 107, 0, 0.5),
                            0 0 60px rgba(255, 140, 66, 0.3);
                    }
                }
            </style>



            {{-- ================= ABOUT INDONESIA (SINGLE CARD LAYOUT) ================= --}}
            <section id="about" class="py-12">
                <h2 class="neon-title">Tentang Indonesia</h2>
                <div class="title-decoration"></div>
                <style>
                    /* =========================================================
                           ORANGE NEON THEME - ABOUT (FIXED STRUCTURE + LIGHT MODE)
                        ========================================================= */
                    @property --neon-orange-angle {
                        syntax: "<angle>";
                        inherits: false;
                        initial-value: 0deg;
                    }

                    /* Lead paragraph under title */
                    #about .about-lead {
                        font-size: 1.02rem;
                        line-height: 1.85;
                        color: color-mix(in oklab, var(--txt-body) 90%, transparent);
                        opacity: .95;
                        text-align: center;
                    }

                    /* ================= SINGLE MAIN CARD ================= */
                    #about .main-card {
                        max-width: 1100px;
                        margin: 1.75rem auto 0;
                        padding: 2.25rem;
                        border-radius: 20px;
                        background: linear-gradient(145deg,
                                color-mix(in oklab, var(--card) 85%, transparent),
                                color-mix(in oklab, var(--card) 85%, rgba(0, 0, 0, .15)));
                        position: relative;
                        overflow: hidden;
                    }

                    /* ORANGE NEON BORDER EFFECT */
                    #about .main-card::before {
                        content: "";
                        position: absolute;
                        inset: -4px;
                        border-radius: 24px;
                        padding: 8px;
                        pointer-events: none;
                        z-index: 0;
                        background: conic-gradient(from var(--neon-orange-angle),
                                rgba(249, 115, 22, 0) 0deg,
                                rgba(249, 115, 22, .4) 45deg,
                                #f97316 90deg,
                                #fb923c 180deg,
                                #f97316 270deg,
                                rgba(249, 115, 22, .4) 315deg,
                                rgba(249, 115, 22, 0) 360deg);
                        -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
                        -webkit-mask-composite: xor;
                        mask-composite: exclude;
                        filter: blur(8px);
                        opacity: .7;
                        animation: orange-neon-spin 10s linear infinite;
                    }

                    @keyframes orange-neon-spin {
                        to {
                            --neon-orange-angle: 360deg;
                        }
                    }

                    #about .main-card>* {
                        position: relative;
                        z-index: 1;
                    }

                    /* ================= TOP DESCRIPTION INSIDE CARD ================= */
                    #about .desc-wrap {
                        text-align: center;
                        max-width: 900px;
                        margin: 0 auto;
                    }

                    #about .description-title {
                        font-size: 1.85rem;
                        font-weight: 900;
                        margin-bottom: 1rem;
                        background: linear-gradient(90deg, #f97316, #fb923c);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        line-height: 1.25;
                    }

                    #about .description-text {
                        font-size: 1.05rem;
                        line-height: 1.85;
                        color: color-mix(in oklab, var(--txt-body) 90%, transparent);
                        opacity: .95;
                        margin: .75rem 0 0;
                    }

                    /* ✅ FIX: highlight box harus beda di light/dark agar kontras aman */
                    #about .highlight-box {
                        padding: 1.1rem 1.2rem;
                        border-radius: 14px;
                        margin: 1.5rem auto 0;
                        max-width: 860px;
                    }

                    html[data-theme="dark"] #about .highlight-box {
                        background: rgba(249, 115, 22, .14);
                        border: 1px solid rgba(249, 115, 22, .28);
                    }

                    html[data-theme="dark"] #about .highlight-text {
                        font-style: italic;
                        color: #fde68a;
                        font-size: 1.02rem;
                        line-height: 1.65;
                        margin: 0;
                    }

                    html[data-theme="light"] #about .highlight-box {
                        background: color-mix(in oklab, #ffedd5 92%, white);
                        border: 1px solid color-mix(in oklab, #f97316 40%, #0000);
                    }

                    html[data-theme="light"] #about .highlight-text {
                        font-style: italic;
                        color: color-mix(in oklab, #7a2e00 92%, #111);
                        font-size: 1.02rem;
                        line-height: 1.65;
                        margin: 0;
                    }

                    /* ================= 4 STATS - GRID RESPONSIVE ================= */
                    #about .points-grid {
                        display: grid;
                        grid-template-columns: repeat(2, minmax(0, 1fr));
                        gap: 1.25rem;
                        margin-top: 2rem;
                    }

                    @media (max-width:640px) {
                        #about .points-grid {
                            grid-template-columns: 1fr;
                        }
                    }

                    /* ================= STAT CARD ITEM ================= */
                    #about .point-item-enhanced {
                        padding: 1.25rem;
                        border-radius: 14px;
                        transition: all .3s ease;
                        display: flex;
                        align-items: flex-start;
                        gap: 1rem;
                        height: 100%;
                    }

                    html[data-theme="dark"] #about .point-item-enhanced {
                        background: rgba(255, 255, 255, .05);
                        border: 1px solid rgba(255, 255, 255, .1);
                    }

                    html[data-theme="light"] #about .point-item-enhanced {
                        background: rgba(0, 0, 0, .02);
                        border: 1px solid rgba(0, 0, 0, .08);
                    }

                    #about .point-item-enhanced:hover {
                        background: rgba(249, 115, 22, .15);
                        border-color: rgba(249, 115, 22, .3);
                        transform: translateY(-3px);
                        box-shadow: 0 10px 25px rgba(249, 115, 22, .2);
                    }

                    /* ICON */
                    #about .point-icon-enhanced {
                        width: 52px;
                        height: 52px;
                        flex-shrink: 0;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 12px;
                        background: rgba(249, 115, 22, .2);
                        border: 2px solid rgba(249, 115, 22, .4);
                        position: relative;
                    }

                    #about .point-icon-enhanced svg {
                        width: 24px;
                        height: 24px;
                        stroke-width: 2;
                        color: #f97316;
                        filter: drop-shadow(0 0 10px rgba(249, 115, 22, .8));
                    }

                    /* CONTENT */
                    #about .point-content-enhanced {
                        flex: 1;
                        min-width: 0;
                    }

                    #about .point-header-enhanced {
                        display: flex;
                        align-items: baseline;
                        justify-content: space-between;
                        margin-bottom: .6rem;
                        gap: .75rem;
                        flex-wrap: wrap;
                    }

                    #about .point-title-enhanced {
                        font-size: 1.08rem;
                        font-weight: 900;
                        color: var(--txt-body);
                        margin: 0;
                        line-height: 1.2;
                    }

                    #about .point-number-enhanced {
                        font-size: 2.05rem;
                        font-weight: 900;
                        line-height: 1;
                        color: #f97316;
                        text-shadow: 0 0 20px rgba(249, 115, 22, .6);
                        white-space: nowrap;
                    }

                    #about .point-unit {
                        font-size: .95rem;
                        font-weight: 700;
                        color: var(--muted);
                        margin-left: .25rem;
                    }

                    #about .point-desc-enhanced {
                        font-size: .93rem;
                        line-height: 1.6;
                        color: color-mix(in oklab, var(--txt-body) 82%, transparent);
                        margin-bottom: .9rem;
                    }

                    /* SOURCE BADGE */
                    #about .source-minimal {
                        display: inline-flex;
                        align-items: center;
                        gap: 6px;
                        font-size: .85rem;
                        color: var(--muted);
                        text-decoration: none;
                        transition: all .3s ease;
                        padding: 6px 10px;
                        border-radius: 10px;
                        max-width: 100%;
                    }

                    html[data-theme="dark"] #about .source-minimal {
                        background: rgba(255, 255, 255, .05);
                        border: 1px solid rgba(255, 255, 255, .1);
                    }

                    html[data-theme="light"] #about .source-minimal {
                        background: rgba(0, 0, 0, .02);
                        border: 1px solid rgba(0, 0, 0, .08);
                    }

                    #about .source-minimal:hover {
                        color: #f97316;
                        background: rgba(249, 115, 22, .1);
                        border-color: rgba(249, 115, 22, .3);
                    }

                    #about .source-minimal svg {
                        width: 10px;
                        height: 10px;
                        flex-shrink: 0;
                    }

                    /* DIVIDER */
                    #about .divider {
                        height: 1px;
                        background: linear-gradient(90deg, transparent, rgba(249, 115, 22, .5), transparent);
                        margin: 2rem 0;
                    }

                    /* BHINNEKA SECTION */
                    #about .bhinneka-section {
                        padding: 1.75rem;
                        border-radius: 16px;
                        position: relative;
                        overflow: hidden;
                    }

                    html[data-theme="dark"] #about .bhinneka-section {
                        background: rgba(255, 255, 255, .05);
                        border: 1px solid rgba(255, 255, 255, .1);
                    }

                    html[data-theme="light"] #about .bhinneka-section {
                        background: rgba(0, 0, 0, .02);
                        border: 1px solid rgba(0, 0, 0, .08);
                    }

                    #about .bhinneka-title {
                        font-size: 1.55rem;
                        font-weight: 900;
                        margin-bottom: .9rem;
                        background: linear-gradient(90deg, #f97316, #fb923c);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        text-align: center;
                    }

                    #about .bhinneka-text {
                        font-size: 1.08rem;
                        line-height: 1.75;
                        color: color-mix(in oklab, var(--txt-body) 92%, transparent);
                        text-align: center;
                        max-width: 860px;
                        margin: 0 auto 1.25rem;
                    }

                    #about .bhinneka-highlight {
                        color: #fbbf24;
                        font-weight: 900;
                        text-shadow: 0 0 10px rgba(251, 191, 36, .5);
                    }

                    #about .sources-grid {
                        display: grid;
                        grid-template-columns: repeat(4, minmax(0, 1fr));
                        gap: .9rem;
                        margin-top: 1.25rem;
                    }

                    #about .source-item {
                        text-align: center;
                        padding: .9rem;
                        border-radius: 12px;
                        transition: all .3s ease;
                    }

                    html[data-theme="dark"] #about .source-item {
                        background: rgba(255, 255, 255, .05);
                        border: 1px solid rgba(255, 255, 255, .1);
                    }

                    html[data-theme="light"] #about .source-item {
                        background: rgba(0, 0, 0, .02);
                        border: 1px solid rgba(0, 0, 0, .08);
                    }

                    #about .source-item:hover {
                        background: rgba(249, 115, 22, .15);
                        border-color: rgba(249, 115, 22, .3);
                        transform: translateY(-2px);
                    }

                    #about .source-label {
                        font-size: .78rem;
                        color: var(--muted);
                        margin-bottom: .45rem;
                        text-transform: uppercase;
                        letter-spacing: .06em;
                    }

                    #about .source-link {
                        font-size: .92rem;
                        color: #f97316;
                        text-decoration: none;
                        font-weight: 800;
                    }

                    #about .source-link:hover {
                        color: #fb923c;
                        text-decoration: underline;
                    }

                    @media (max-width:1100px) {
                        #about .sources-grid {
                            grid-template-columns: repeat(2, minmax(0, 1fr));
                        }
                    }

                    @media (max-width:768px) {
                        #about .main-card {
                            padding: 1.6rem;
                        }

                        #about .point-item-enhanced {
                            flex-direction: column;
                            align-items: center;
                            text-align: center;
                        }

                        #about .point-header-enhanced {
                            justify-content: center;
                        }

                        #about .sources-grid {
                            grid-template-columns: 1fr;
                        }

                        #about .bhinneka-section {
                            padding: 1.35rem;
                        }
                    }

                    @media (max-width:480px) {
                        #about .point-number-enhanced {
                            font-size: 1.95rem;
                        }
                    }

                    <style>

                    /* Bold penting di paragraf about */
                    #about .about-strong {
                        font-weight: 900;
                        color: color-mix(in oklab, var(--txt-body) 92%, transparent);
                        text-shadow: 0 0 14px rgba(249, 115, 22, 0.22);
                    }

                    /* Sedikit aksen neon orange di light/dark biar tetap kebaca */
                    html[data-theme="dark"] #about .about-strong {
                        color: color-mix(in oklab, #fde68a 55%, var(--txt-body));
                        text-shadow: 0 0 18px rgba(249, 115, 22, 0.28);
                    }

                    html[data-theme="light"] #about .about-strong {
                        color: color-mix(in oklab, #7a2e00 80%, var(--txt-body));
                        text-shadow: 0 0 10px rgba(249, 115, 22, 0.18);
                    }
                </style>

                </style>

                {{-- ✅ DESKRIPSI (SEKARANG DI DALAM CARD) --}}
                <div class="desc-wrap">
                    <p class="description-text">
                        Indonesia adalah negara kepulauan yang kaya akan
                        <span class="about-strong">keanekaragaman alam dan budaya</span>,
                        membentang di sepanjang
                        <span class="about-strong">garis khatulistiwa</span>
                        dan terdiri dari banyak pulau yang saling terhubung dalam satu wilayah yang dikenal sebagai
                        <span class="about-strong">Nusantara</span>.
                        Pulau-pulau besar seperti
                        <span class="about-strong">Sumatera, Jawa, Kalimantan, Sulawesi, Sunda Kecil, Maluku, dan
                            Papua</span>,
                        bersama pulau-pulau lainnya, membentuk bentang alam yang beragam dengan
                        <span class="about-strong">karakter budaya yang unik</span>
                        di setiap wilayah.
                    </p>

                    <p class="description-text mt-3">
                        Indonesia juga dikenal memiliki
                        <span class="about-strong">keanekaragaman flora dan fauna</span>
                        yang khas, termasuk berbagai spesies
                        <span class="about-strong">endemik, langka, dan dilindungi</span>.
                        Kekayaan hayati ini hidup dan berkembang di
                        <span class="about-strong">hutan tropis, pegunungan, perairan laut</span>,
                        serta berbagai
                        <span class="about-strong">ekosistem alami</span>
                        yang tersebar di seluruh wilayah Indonesia.
                    </p>


                    <div class="highlight-box">
                        <p class="highlight-text">
                            Keragaman wilayah, bahasa, dan suku bangsa Indonesia tercermin dalam ringkasan data nasional
                            berikut.
                        </p>
                    </div>

                </div>


                {{-- ✅ SEMUA KONTEN UTAMA MASUK KE DALAM CARD --}}
                <div class="main-card">


                    {{-- ✅ 4 CARD STATISTIK --}}
                    <div class="points-grid">
                        {{-- PULAU --}}
                        <div class="point-item-enhanced">
                            <div class="point-icon-enhanced">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 21a9 9 0 01-9-9 9 9 0 019-9 9 9 0 019 9 9 9 0 01-9 9z" />
                                </svg>
                            </div>
                            <div class="point-content-enhanced">
                                <div class="point-header-enhanced">
                                    <h4 class="point-title-enhanced">Pulau Terdata</h4>
                                    <div class="point-number-enhanced">17.380<span class="point-unit">pulau</span></div>
                                </div>
                                <p class="point-desc-enhanced">
                                    Pulau bernama & berkoordinat menurut Badan Informasi Geospasial (BIG).
                                    Angka ini diperbarui tahun 2024 dan bisa berubah karena verifikasi lapangan.
                                </p>
                                <a href="https://sipulau.big.go.id/news/11" target="_blank" rel="noopener"
                                    class="source-minimal">
                                    Sumber: BIG - SI PULAU
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        {{-- BAHASA --}}
                        <div class="point-item-enhanced">
                            <div class="point-icon-enhanced">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.5 21l5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 016-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 01-3.827-5.802" />
                                </svg>
                            </div>
                            <div class="point-content-enhanced">
                                <div class="point-header-enhanced">
                                    <h4 class="point-title-enhanced">Bahasa Daerah</h4>
                                    <div class="point-number-enhanced">718<span class="point-unit">bahasa</span></div>
                                </div>
                                <p class="point-desc-enhanced">
                                    Bahasa daerah yang telah diidentifikasi & divalidasi melalui pemetaan nasional.
                                    Tidak termasuk dialek dan subdialek dalam penelitian linguistik.
                                </p>
                                <a href="https://petabahasa.kemdikbud.go.id/" target="_blank" rel="noopener"
                                    class="source-minimal">
                                    Sumber: Peta Bahasa
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        {{-- SUKU --}}
                        <div class="point-item-enhanced">
                            <div class="point-icon-enhanced">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>
                            </div>
                            <div class="point-content-enhanced">
                                <div class="point-header-enhanced">
                                    <h4 class="point-title-enhanced">Suku Bangsa</h4>
                                    <div class="point-number-enhanced">1.340<span class="point-unit">suku</span></div>
                                </div>
                                <p class="point-desc-enhanced">
                                    Kelompok suku bangsa yang tersebar di seluruh Indonesia, masing-masing dengan
                                    bahasa, adat istiadat, dan tradisi unik yang menjadi kekayaan budaya nasional.
                                </p>
                                <a href="https://indonesiabaik.id/infografis/sebaran-jumlah-suku-di-indonesia"
                                    target="_blank" rel="noopener" class="source-minimal">
                                    Sumber: IndonesiaBaik
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        {{-- PROVINSI --}}
                        <div class="point-item-enhanced">
                            <div class="point-icon-enhanced">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                                </svg>
                            </div>
                            <div class="point-content-enhanced">
                                <div class="point-header-enhanced">
                                    <h4 class="point-title-enhanced">Provinsi</h4>
                                    <div class="point-number-enhanced">38<span class="point-unit">provinsi</span></div>
                                </div>
                                <p class="point-desc-enhanced">
                                    Jumlah provinsi secara administratif setelah penambahan provinsi baru di Papua.
                                    Setiap provinsi memiliki pemerintahan daerah dan karakteristik budaya yang unik.
                                </p>
                                <a href="https://otda.kemendagri.go.id/berita/qKNfHVQrW9bGogWQxi29DUGKRTYouc/dpr-sahkan-uu-pembentukan-papua-barat-daya-jumlah-provinsi-jadi-38"
                                    target="_blank" rel="noopener" class="source-minimal">
                                    Sumber: Kemendagri
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- ✅ BHINNEKA TETAP DI DALAM CARD --}}
                    <div class="divider"></div>

                    <div class="bhinneka-section">
                        <h3 class="bhinneka-title">Bhinneka Tunggal Ika: Berbeda-beda, Tetap Satu</h3>

                        <p class="bhinneka-text">
                            Keberagaman pulau, bahasa, dan suku bukan hanya data statistik — tapi identitas yang membentuk
                            Indonesia.
                            Perjalanan kebangsaan yang panjang berpuncak pada
                            <span class="bhinneka-highlight">Proklamasi Kemerdekaan 17 Agustus 1945</span>, sebagai
                            penegasan berdirinya negara berdaulat yang
                            menghargai pluralitas dalam kesatuan.
                        </p>

                        <div class="sources-grid">
                            <div class="source-item">
                                <div class="source-label">Pulau (17.380)</div>
                                <a href="https://sipulau.big.go.id/news/11" target="_blank" rel="noopener"
                                    class="source-link">
                                    BIG – SI PULAU
                                </a>
                            </div>
                            <div class="source-item">
                                <div class="source-label">Bahasa (718)</div>
                                <a href="https://petabahasa.kemdikbud.go.id/" target="_blank" rel="noopener"
                                    class="source-link">
                                    Peta Bahasa
                                </a>
                            </div>
                            <div class="source-item">
                                <div class="source-label">Suku (1.340)</div>
                                <a href="https://indonesiabaik.id/infografis/sebaran-jumlah-suku-di-indonesia"
                                    target="_blank" rel="noopener" class="source-link">
                                    IndonesiaBaik
                                </a>
                            </div>
                            <div class="source-item">
                                <div class="source-label">Provinsi (38)</div>
                                <a href="https://otda.kemendagri.go.id/berita/qKNfHVQrW9bGogWQxi29DUGKRTYouc/dpr-sahkan-uu-pembentukan-papua-barat-daya-jumlah-provinsi-jadi-38"
                                    target="_blank" rel="noopener" class="source-link">
                                    Kemendagri OTDA
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MAP (tetap di luar card seperti sebelumnya) --}}
                <div class="mt-12">
                    @include('partials.map-indonesia')
                </div>
            </section>





            {{-- ================= HISTORY SECTION: Sejarah Nama Pulau di Indonesia ================= --}}
            <section id="history" class="history-section py-12">
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
                        background: linear-gradient(90deg, #ff6b00, #ff8c42);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                    }

                    .history-subtitle {
                        font-size: 1rem;
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
                        background: linear-gradient(to bottom, #ff6b00, #ff8c42);
                        box-shadow: 0 0 10px rgba(255, 107, 0, 0.5);
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
                        width: 18px;
                        height: 18px;
                        border-radius: 999px;
                        background: var(--bg-body);
                        border: 4px solid #ff6b00;
                        box-shadow: 0 0 15px rgba(255, 107, 0, 0.8);
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
                        border-radius: inherit;
                        padding: 10px;
                        z-index: 0;
                        pointer-events: none;
                        background: conic-gradient(from var(--border-angle),
                                rgba(255, 107, 0, 0),
                                rgba(255, 140, 66, 0.2) 30deg,
                                #ff6b00 80deg,
                                #ffaa6b 120deg,
                                rgba(255, 140, 66, 0.2) 180deg,
                                rgba(255, 107, 0, 0) 240deg,
                                rgba(255, 140, 66, 0.25) 300deg,
                                #ff6b00 330deg,
                                rgba(255, 107, 0, 0) 360deg);
                        -webkit-mask:
                            linear-gradient(#000 0 0) content-box,
                            linear-gradient(#000 0 0);
                        -webkit-mask-composite: xor;
                        mask-composite: exclude;
                        filter: blur(4px);
                        opacity: 0.95;
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
                        background: linear-gradient(145deg, var(--card), var(--card-bg-dark));
                        padding: 1.8rem 2rem;
                        box-shadow:
                            0 14px 32px rgba(0, 0, 0, 0.25),
                            inset 0 1px 0 rgba(255, 255, 255, 0.1);
                        z-index: 1;
                        text-align: left;
                        border: 1px solid rgba(255, 107, 0, 0.1);
                    }

                    /* Dark/Light mode adjustment */
                    html[data-theme="dark"] .timeline-card-inner {
                        background: linear-gradient(145deg, #111827, #020617);
                    }

                    html[data-theme="light"] .timeline-card-inner {
                        background: linear-gradient(145deg, #ffffff, #f8fafc);
                    }

                    .timeline-badge {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        font-size: .85rem;
                        font-weight: 700;
                        padding: .4rem 1rem;
                        margin-bottom: .8rem;
                        border-radius: 999px;
                        background: linear-gradient(135deg, #ff6b00, #ff8c42);
                        color: white;
                        box-shadow: 0 4px 12px rgba(255, 107, 0, 0.3);
                    }

                    .timeline-heading {
                        font-size: 1.2rem;
                        margin-bottom: .5rem;
                        color: var(--txt-body);
                        font-weight: 700;
                    }

                    .timeline-text {
                        font-size: .95rem;
                        line-height: 1.7;
                        color: var(--muted);
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
                    <h2 class="neon-title">Sejarah Nama-Nama Pulau Besar di Indonesia</h2>
                    <div class="mi-title-decoration"></div>
                    <p class="history-subtitle">
                        Banyak nama pulau di Indonesia berasal dari bahasa Sanskerta, bahasa lokal, hingga catatan para
                        pelaut dan penjelajah asing. Berikut beberapa kisah singkat di balik namanya.
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
                                        di pesisir Aceh. Pengelana Maroko Ibn Battuta (abad ke-14) menuliskan nama itu
                                        sebagai
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
                                        "pulau tempat tumbuhnya biji-bijian", yang kemudian diserap menjadi <em>Java /
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
                                        <em>Kalamanthana</em>, yang dapat dimaknai "cuaca yang membakar/panas". Penduduk
                                        lokal menyebutnya <em>Pulu K'lemantan</em>, yang kemudian dicatat para pelaut Eropa
                                        dan
                                        melekat sebagai nama resmi wilayah Indonesia di pulau Borneo.
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
                                        dan <em>besi</em>, merujuk pada perdagangan bijih besi di kawasan Danau Matano dan
                                        sekitarnya. Di era kolonial, pulau ini dikenal sebagai <em>Celebes</em>, sebelum
                                        nama Sulawesi
                                        dipakai lagi setelah kemerdekaan Indonesia.
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
                                        mengaitkannya dengan ungkapan Tidore <em>Papo Ua Gam Sio</em>, "sembilan negeri yang
                                        belum
                                        bersatu". Ada juga yang menghubungkannya dengan istilah lokal yang menggambarkan
                                        wilayah di
                                        ujung timur, "tanah di bawah matahari terbenam" bagi masyarakat di baratnya.
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
                                        secara harfiah berarti "kepulauan di tenggara" (<em>nusa</em> = pulau,
                                        <em>tenggara</em> = arah tenggara), merujuk gugusan pulau dari Lombok sampai Timor.
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
                                        Salah satu tafsir menyebut namanya berkaitan dengan ungkapan "pulau raja-raja" dalam
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
                                        Sumpah Palapa Gajah Mada, untuk menyebut gugusan pulau di luar Jawa yang ingin
                                        dipersatukan. Di era modern, "Nusantara" menjadi sebutan puitis bagi seluruh
                                        kepulauan Indonesia
                                        dan bahkan dipilih sebagai nama ibu kota negara yang baru di Kalimantan Timur.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ================= ISLANDS LIST / JELAJAH PULAU ================= --}}
            <section id="islands" class="py-12">
                <h2 class="neon-title">
                    Jelajahi Pulau-Pulau Indonesia
                </h2>
                <div class="title-decoration"></div>
                <div class="neon-subtitle">
                    <p>
                        Setiap pulau di Indonesia menyimpan keragaman suku, budaya, dan cara hidup yang berbeda. Di balik
                        bentang alamnya, terdapat sejarah, tradisi, dan kebiasaan yang tumbuh bersama masyarakatnya dari
                        generasi ke generasi. Nusantara bukan sekadar kumpulan pulau, melainkan ruang hidup yang kaya akan
                        identitas dan makna.
                    </p>

                    <p class="mt-5">
                        Melalui Lentara, kamu dapat menjelajahi pulau-pulau Nusantara untuk memahami suku-suku utama yang
                        membentuk tradisi, sejarah, destinasi, dan kuliner khas setiap daerah. Setiap pulau dikembangkan
                        sebagai ruang pembelajaran yang terus berkembang, membuka lapisan demi lapisan pengetahuan tentang
                        Nusantara secara utuh dan berkelanjutan.
                    </p>
                </div>


                {{-- CARD + MODAL STYLE dengan NEON seperti Quiz --}}
                <style>
                    /* =========================================================
                           NEON RING BORDER (ANIM) - SAMA PERSIS DENGAN QUIZ
                        ========================================================= */
                    @property --neon-angle {
                        syntax: "<angle>";
                        inherits: false;
                        initial-value: 0deg;
                    }

                    /* ===== Card Look ===== */
                    #islands .nus-card {
                        position: relative;
                        border-radius: 26px;
                        padding: 20px;
                        background: linear-gradient(145deg, var(--card-bg), var(--card-bg-dark));
                        box-shadow:
                            0 0 0 1px rgba(255, 255, 255, 0.06),
                            0 30px 60px rgba(0, 0, 0, 0.45);
                        border: 1px solid rgba(255, 107, 0, 0.2);
                        overflow: hidden;
                        transition: all 0.3s ease;
                        cursor: pointer;
                    }

                    /* Dark mode adjustment */
                    html[data-theme="dark"] #islands .nus-card {
                        background: linear-gradient(145deg, #111827, #020617);
                    }

                    html[data-theme="light"] #islands .nus-card {
                        background: linear-gradient(145deg, #ffffff, #f8fafc);
                    }

                    /* EFEK NEON BORDER SAMA PERSIS QUIZ */
                    #islands .nus-card::before {
                        content: "";
                        position: absolute;
                        inset: -6px;
                        border-radius: inherit;
                        padding: 10px;
                        pointer-events: none;
                        z-index: 0;
                        background: conic-gradient(from var(--neon-angle),
                                rgba(249, 115, 22, 0) 0deg,
                                rgba(249, 115, 22, 0.20) 22deg,
                                #f97316 55deg,
                                #22d3ee 110deg,
                                #34d399 165deg,
                                rgba(34, 211, 238, 0.20) 220deg,
                                #f97316 300deg,
                                rgba(249, 115, 22, 0) 360deg);
                        -webkit-mask:
                            linear-gradient(#000 0 0) content-box,
                            linear-gradient(#000 0 0);
                        -webkit-mask-composite: xor;
                        mask-composite: exclude;
                        filter: blur(6px);
                        opacity: 0;
                        transition: opacity 0.4s ease;
                        animation: neon-spin 7.5s linear infinite paused;
                    }

                    #islands .nus-card:hover::before {
                        opacity: 0.95;
                        animation-play-state: running;
                    }

                    @keyframes neon-spin {
                        to {
                            --neon-angle: 360deg;
                        }
                    }

                    /* pastikan semua konten di atas neon ring */
                    #islands .nus-card>* {
                        position: relative;
                        z-index: 1;
                    }

                    /* Efek hover card */
                    #islands .nus-card:hover {
                        transform: translateY(-8px);
                        box-shadow:
                            0 40px 80px rgba(0, 0, 0, 0.55),
                            0 0 0 1px rgba(255, 107, 0, 0.3);
                    }

                    /* Dark mode hover adjustment */
                    html[data-theme="dark"] #islands .nus-card:hover {
                        box-shadow:
                            0 40px 80px rgba(0, 0, 0, 0.7),
                            0 0 0 1px rgba(255, 107, 0, 0.4);
                    }

                    /* ANIMASI KLIK NEON */
                    #islands .nus-card:active::before {
                        opacity: 1;
                        filter: blur(8px);
                        background: conic-gradient(from var(--neon-angle),
                                rgba(249, 115, 22, 0) 0deg,
                                #f97316 30deg,
                                #22d3ee 90deg,
                                #34d399 150deg,
                                #22d3ee 210deg,
                                #f97316 270deg,
                                rgba(249, 115, 22, 0) 360deg);
                    }

                    #islands .nus-card:active {
                        animation: card-pulse 0.3s ease-out;
                    }

                    @keyframes card-pulse {
                        0% {
                            transform: translateY(-8px) scale(1);
                        }

                        50% {
                            transform: translateY(-8px) scale(0.98);
                            box-shadow:
                                0 50px 100px rgba(249, 115, 22, 0.3),
                                0 0 30px rgba(249, 115, 22, 0.4);
                        }

                        100% {
                            transform: translateY(-8px) scale(1);
                        }
                    }

                    #islands .nus-card-title {
                        font-weight: 800;
                        letter-spacing: -0.02em;
                        color: var(--txt-body);
                        font-size: 1.5rem;
                        margin-bottom: 0.75rem;
                        background: linear-gradient(90deg, var(--txt-body), color-mix(in srgb, var(--txt-body) 70%, transparent));
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                    }

                    #islands .nus-card-link {
                        color: #ff6b00;
                        font-weight: 700;
                        display: inline-flex;
                        align-items: center;
                        gap: 0.5rem;
                        transition: all 0.3s ease;
                    }

                    #islands .nus-card-link:hover {
                        color: #ff8c42;
                        transform: translateX(3px);
                        text-shadow: 0 0 10px rgba(255, 107, 0, 0.5);
                    }

                    /* ===== Island image dengan efek cahaya statis ===== */
                    #islands .island-thumb {
                        width: 100%;
                        border-radius: 16px;
                        overflow: hidden;
                        border: 1px solid color-mix(in oklab, var(--line) 70%, transparent);
                        background: linear-gradient(145deg, var(--card-bg), var(--card-bg-dark));
                        cursor: zoom-in;
                        position: relative;
                        margin-bottom: 1rem;
                        isolation: isolate;
                    }

                    /* Dark/Light mode adjustment for thumb */
                    html[data-theme="dark"] #islands .island-thumb {
                        background: linear-gradient(145deg, #111827, #020617);
                    }

                    html[data-theme="light"] #islands .island-thumb {
                        background: linear-gradient(145deg, #ffffff, #f8fafc);
                    }

                    /* CAHAYA STATIS TEBAL DARI BERBAGAI SISI */
                    #islands .island-thumb::before {
                        content: "";
                        position: absolute;
                        inset: -3px;
                        background:
                            radial-gradient(circle at top left, rgba(249, 115, 22, 0.4), transparent 55%),
                            radial-gradient(circle at top right, rgba(34, 211, 238, 0.3), transparent 55%),
                            radial-gradient(circle at bottom left, rgba(52, 211, 153, 0.25), transparent 55%),
                            radial-gradient(circle at bottom right, rgba(249, 115, 22, 0.3), transparent 55%),
                            radial-gradient(circle at center, rgba(255, 255, 255, 0.1), transparent 70%);
                        filter: blur(15px);
                        opacity: 0;
                        transition: opacity 0.4s ease;
                        z-index: 1;
                        border-radius: 19px;
                    }

                    #islands .island-thumb:hover::before {
                        opacity: 1;
                    }

                    #islands .island-thumb img {
                        width: 100%;
                        height: 200px;
                        object-fit: contain;
                        display: block;
                        transform: scale(1);
                        transition: transform 0.3s ease;
                        padding: 15px;
                        position: relative;
                        z-index: 2;
                        filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.3));
                    }

                    #islands .island-thumb:hover img {
                        transform: scale(1.08);
                    }

                    #islands .thumb-hint {
                        position: absolute;
                        right: 10px;
                        bottom: 10px;
                        font-size: 11px;
                        padding: 5px 10px;
                        border-radius: 999px;
                        border: 1px solid rgba(249, 115, 22, 0.3);
                        background: rgba(17, 24, 39, 0.85);
                        color: #f97316;
                        backdrop-filter: blur(6px);
                        z-index: 3;
                        transition: all 0.3s ease;
                    }

                    /* Dark/Light mode adjustment for hint */
                    html[data-theme="dark"] #islands .thumb-hint {
                        background: rgba(17, 24, 39, 0.85);
                    }

                    html[data-theme="light"] #islands .thumb-hint {
                        background: rgba(255, 255, 255, 0.85);
                        color: #b7410e;
                        border-color: rgba(183, 65, 14, 0.3);
                    }

                    #islands .island-thumb:hover .thumb-hint {
                        background: rgba(249, 115, 22, 0.9);
                        color: white;
                        border-color: #fb923c;
                        transform: translateY(-2px);
                        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
                    }

                    /* ANIMASI KLIK PADA THUMB */
                    #islands .island-thumb:active {
                        animation: thumb-click 0.3s ease;
                    }

                    @keyframes thumb-click {
                        0% {
                            transform: scale(1);
                        }

                        50% {
                            transform: scale(0.98);
                            box-shadow: 0 0 40px 20px rgba(249, 115, 22, 0.3);
                        }

                        100% {
                            transform: scale(1);
                        }
                    }

                    /* ===== Modal (popup) dengan efek neon sama ===== */
                    #island-modal-backdrop {
                        display: none;
                        backdrop-filter: blur(12px);
                        background: rgba(0, 0, 0, 0.8);
                    }

                    #island-modal-backdrop.is-open {
                        display: flex;
                    }

                    #island-modal {
                        position: relative;
                        border-radius: 26px;
                        background: linear-gradient(145deg, var(--card-bg), var(--card-bg-dark));
                        color: var(--txt-body);
                        box-shadow:
                            0 0 0 1px rgba(255, 255, 255, 0.06),
                            0 30px 60px rgba(0, 0, 0, 0.55);
                        border: 1px solid rgba(249, 115, 22, 0.2);
                        transform: translateY(12px) scale(.97);
                        opacity: 0;
                        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
                        overflow: hidden;
                        max-width: 900px;
                        width: 90%;
                    }

                    /* Dark/Light mode adjustment for modal */
                    html[data-theme="dark"] #island-modal {
                        background: linear-gradient(145deg, #111827, #020617);
                    }

                    html[data-theme="light"] #island-modal {
                        background: linear-gradient(145deg, #ffffff, #f8fafc);
                    }

                    /* NEON BORDER UNTUK MODAL JUGA */
                    #island-modal::before {
                        content: "";
                        position: absolute;
                        inset: -6px;
                        border-radius: inherit;
                        padding: 10px;
                        pointer-events: none;
                        z-index: 0;
                        background: conic-gradient(from var(--neon-angle),
                                rgba(249, 115, 22, 0) 0deg,
                                rgba(249, 115, 22, 0.20) 22deg,
                                #f97316 55deg,
                                #22d3ee 110deg,
                                #34d399 165deg,
                                rgba(34, 211, 238, 0.20) 220deg,
                                #f97316 300deg,
                                rgba(249, 115, 22, 0) 360deg);
                        -webkit-mask:
                            linear-gradient(#000 0 0) content-box,
                            linear-gradient(#000 0 0);
                        -webkit-mask-composite: xor;
                        mask-composite: exclude;
                        filter: blur(6px);
                        opacity: 0.8;
                        animation: neon-spin 7.5s linear infinite;
                    }

                    #island-modal-backdrop.is-open #island-modal {
                        transform: translateY(0) scale(1);
                        opacity: 1;
                    }

                    /* Konten modal di atas neon */
                    #island-modal>* {
                        position: relative;
                        z-index: 1;
                    }

                    #island-modal-title {
                        color: var(--txt-body);
                        font-size: 1.8rem;
                        font-weight: 800;
                        background: linear-gradient(90deg, #f97316, #22d3ee, #34d399);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        animation: neon-glow 3s ease-in-out infinite;
                        background-size: 200% auto;
                    }

                    #island-modal-subtitle {
                        color: var(--muted);
                        font-size: 1rem;
                    }

                    #island-modal-image {
                        width: 100%;
                        height: min(65vh, 500px);
                        object-fit: contain;
                        background:
                            linear-gradient(145deg, var(--card-bg), var(--card-bg-dark)),
                            radial-gradient(circle at top left, rgba(249, 115, 22, 0.2), transparent 40%),
                            radial-gradient(circle at bottom right, rgba(34, 211, 238, 0.2), transparent 40%);
                        display: block;
                        padding: 25px;
                        border-radius: 12px;
                        margin: 0 auto;
                        filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.4));
                    }

                    /* Dark/Light mode adjustment for modal image */
                    html[data-theme="dark"] #island-modal-image {
                        background: linear-gradient(145deg, #111827, #020617),
                            radial-gradient(circle at top left, rgba(249, 115, 22, 0.2), transparent 40%),
                            radial-gradient(circle at bottom right, rgba(34, 211, 238, 0.2), transparent 40%);
                    }

                    html[data-theme="light"] #island-modal-image {
                        background: linear-gradient(145deg, #ffffff, #f8fafc),
                            radial-gradient(circle at top left, rgba(249, 115, 22, 0.1), transparent 40%),
                            radial-gradient(circle at bottom right, rgba(34, 211, 238, 0.1), transparent 40%);
                    }

                    .island-modal-close {
                        width: 44px;
                        height: 44px;
                        border-radius: 999px;
                        display: grid;
                        place-items: center;
                        border: 1px solid rgba(249, 115, 22, 0.3);
                        background: rgba(17, 24, 39, 0.9);
                        color: #f97316;
                        transition: all 0.2s ease;
                        font-size: 1.2rem;
                        position: relative;
                        overflow: hidden;
                        z-index: 2;
                    }

                    /* Dark/Light mode adjustment for close button */
                    html[data-theme="dark"] .island-modal-close {
                        background: rgba(17, 24, 39, 0.9);
                    }

                    html[data-theme="light"] .island-modal-close {
                        background: rgba(255, 255, 255, 0.9);
                        color: #b7410e;
                        border-color: rgba(183, 65, 14, 0.3);
                    }

                    .island-modal-close:hover {
                        transform: translateY(-2px);
                        border-color: #f97316;
                        color: white;
                        background: rgba(249, 115, 22, 0.9);
                    }

                    /* Tombol Buka Gambar Lengkap dengan efek neon */
                    #island-modal-open-new {
                        position: relative;
                        overflow: hidden;
                    }

                    #island-modal-open-new::before {
                        content: "";
                        position: absolute;
                        inset: -2px;
                        border-radius: inherit;
                        background: conic-gradient(from var(--neon-angle),
                                rgba(249, 115, 22, 0) 0deg,
                                rgba(249, 115, 22, 0.30) 22deg,
                                #f97316 55deg,
                                #22d3ee 110deg,
                                rgba(34, 211, 238, 0.30) 220deg,
                                #f97316 300deg,
                                rgba(249, 115, 22, 0) 360deg);
                        z-index: -1;
                        opacity: 0.7;
                        animation: neon-spin 4s linear infinite;
                    }
                </style>

                @php
                    $islandCards = [
                        [
                            'key' => 'sumatera',
                            'name' => 'Sumatera',
                            'desc' =>
                                'Jejak kerajaan maritim, ragam adat, dan kuliner rempah yang kuat—dari pesisir hingga dataran tinggi.',
                            'href' => url('/islands/sumatera'),
                            'img' => asset('images/pulau/sumatera.PNG'),
                        ],
                        [
                            'key' => 'jawa',
                            'name' => 'Jawa',
                            'desc' =>
                                'Pusat sejarah & kebudayaan: keraton, batik, seni pertunjukan, serta ragam bahasa daerah yang hidup.',
                            'href' => url('/islands/jawa'),
                            'img' => asset('images/pulau/jawa.PNG'),
                        ],
                        [
                            'key' => 'kalimantan',
                            'name' => 'Kalimantan',
                            'desc' =>
                                'Bentang hutan tropis dan sungai besar, dengan tradisi Dayak yang beragam dan kaya simbol.',
                            'href' => url('/islands/kalimantan'),
                            'img' => asset('images/pulau/kalimantan.PNG'),
                        ],
                        [
                            'key' => 'sulawesi',
                            'name' => 'Sulawesi',
                            'desc' =>
                                'Persimpangan budaya maritim & pegunungan—ritual, rumah adat, dan tradisi pelayaran yang kuat.',
                            'href' => url('/islands/sulawesi'),
                            'img' => asset('images/pulau/sulawesi.PNG'),
                        ],
                        [
                            'key' => 'bali-nusa-tenggara',
                            'name' => 'Bali & Nusa Tenggara',
                            'desc' =>
                                'Ritual dan seni yang kuat, lanskap vulkanik, pesisir, hingga savana—ragam budaya pulau-pulau kecil.',
                            'href' => url('/islands/bali-nusa-tenggara'),
                            'img' => asset('images/pulau/sunda kecil.PNG'),
                        ],
                        [
                            'key' => 'papua-maluku',
                            'name' => 'Papua & Maluku',
                            'desc' =>
                                'Kawasan timur dengan kekayaan bahasa, tradisi, dan bentang alam ikonik—dari kepulauan rempah hingga pegunungan.',
                            'href' => url('/islands/papua-maluku'),
                            'img' => asset('images/pulau/papua dan maluku.PNG'),
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($islandCards as $c)
                        <div class="nus-card text-[var(--txt-body)]">
                            {{-- THUMB (klik -> modal) --}}
                            <button type="button" class="island-thumb w-full" data-island-modal="1"
                                data-title="{{ $c['name'] }}" data-desc="{{ $c['desc'] }}"
                                data-img="{{ $c['img'] }}" aria-label="Lihat gambar {{ $c['name'] }}">
                                <img src="{{ $c['img'] }}" alt="Peta 3D {{ $c['name'] }}" loading="lazy">
                                <span class="thumb-hint">Klik untuk zoom</span>
                            </button>

                            <h3 class="nus-card-title">
                                {{ $c['name'] }}
                            </h3>

                            <p class="text-sm sm:text-base text-[var(--muted)] leading-relaxed mb-4">
                                {{ $c['desc'] }}
                            </p>

                            <a href="{{ $c['href'] }}" class="nus-card-link text-sm">
                                Selengkapnya
                                <svg width="16" height="16" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M8 1v14M1 8h14" />
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- MODAL POPUP (gambar pulau) --}}
                <div id="island-modal-backdrop" class="fixed inset-0 z-50 items-center justify-center px-4"
                    aria-hidden="true">
                    <div id="island-modal" class="w-full">
                        <div class="flex items-start justify-between gap-4 p-6 border-b"
                            style="border-color: rgba(249, 115, 22, 0.2);">
                            <div>
                                <h3 id="island-modal-title" class="text-xl sm:text-2xl font-extrabold">Detail Pulau</h3>
                                <p id="island-modal-subtitle" class="text-sm mt-1"></p>
                            </div>

                            <button type="button" class="island-modal-close" id="island-modal-close"
                                aria-label="Tutup">
                                ✕
                            </button>
                        </div>

                        <img id="island-modal-image" src="" alt="Gambar Pulau" />

                        <div class="p-6">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-xs text-[var(--muted)]">
                                    Tip: tekan <strong>Esc</strong> atau klik area gelap untuk menutup.
                                </p>
                                <button type="button" id="island-modal-open-new"
                                    class="text-sm font-bold px-4 py-2 rounded-lg transition-all hover:scale-105 active:scale-95 relative overflow-hidden"
                                    style="background: linear-gradient(135deg, #f97316, #fb923c); color: white; box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);">
                                    <span class="relative z-2">Buka Gambar Lengkap</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SCRIPT MODAL (ringan, no library) --}}
                <script>
                    (function() {
                        const backdrop = document.getElementById('island-modal-backdrop');
                        const modalImg = document.getElementById('island-modal-image');
                        const modalTitle = document.getElementById('island-modal-title');
                        const modalSub = document.getElementById('island-modal-subtitle');
                        const closeBtn = document.getElementById('island-modal-close');
                        const openNewBtn = document.getElementById('island-modal-open-new');

                        if (!backdrop || !modalImg || !modalTitle || !modalSub || !closeBtn || !openNewBtn) return;

                        function openModal({
                            title,
                            desc,
                            img
                        }) {
                            modalTitle.textContent = title || 'Detail Pulau';
                            modalSub.textContent = desc || '';
                            modalImg.src = img || '';
                            modalImg.alt = title ? ('Peta 3D ' + title) : 'Gambar Pulau';
                            openNewBtn.onclick = () => {
                                if (img) window.open(img, '_blank');
                            };

                            backdrop.classList.add('is-open');
                            document.body.classList.add('overflow-hidden');
                        }

                        function closeModal() {
                            backdrop.classList.remove('is-open');
                            document.body.classList.remove('overflow-hidden');
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

            {{-- ================= STATISTIK INDONESIA (NEON THEME) ================= --}}
            <section id="stats" class="py-12">
                <h2 class="neon-title">
                    Statistik Budaya Indonesia
                </h2>
                <div class="title-decoration"></div>
                <p class="neon-subtitle">
                    Gambaran menyeluruh tentang keragaman Indonesia, mencakup sebaran pulau, warisan budaya takbenda yang
                    diakui dunia, serta komposisi penduduk berdasarkan suku, bahasa, dan agama. Data disajikan untuk
                    membantu memahami dinamika sosial dan budaya Nusantara secara utuh.
                </p>

                {{-- CSS STATISTIK DENGAN EFEK NEON --}}
                <style>
                    /* =========================================================
            NEON RING BORDER SAMA PERSIS DENGAN QUIZ DAN PULAU
            ========================================================= */
                    @property --neon-angle {
                        syntax: "<angle>";
                        inherits: false;
                        initial-value: 0deg;
                    }

                    /* ================= STAT CARD UTAMA ================= */
                    #stats .stat-card {
                        position: relative;
                        border-radius: 26px;
                        padding: 1.5rem;
                        overflow: hidden;
                        cursor: pointer;
                        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
                        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);

                        /* Base gradient background */
                        background: linear-gradient(135deg,
                                rgba(249, 115, 22, 0.9),
                                rgba(34, 211, 238, 0.8),
                                rgba(52, 211, 153, 0.9));
                        border: 1px solid rgba(255, 255, 255, 0.1);

                        opacity: 0;
                        transform: translateY(20px);
                        animation: statsFadeUp 0.7s ease-out forwards;
                    }

                    /* EFEK NEON BORDER SAMA DENGAN QUIZ */
                    #stats .stat-card::before {
                        content: "";
                        position: absolute;
                        inset: -6px;
                        border-radius: inherit;
                        padding: 10px;
                        pointer-events: none;
                        z-index: 0;
                        background: conic-gradient(from var(--neon-angle),
                                rgba(249, 115, 22, 0) 0deg,
                                rgba(249, 115, 22, 0.20) 22deg,
                                #f97316 55deg,
                                #22d3ee 110deg,
                                #34d399 165deg,
                                rgba(34, 211, 238, 0.20) 220deg,
                                #f97316 300deg,
                                rgba(249, 115, 22, 0) 360deg);
                        -webkit-mask:
                            linear-gradient(#000 0 0) content-box,
                            linear-gradient(#000 0 0);
                        -webkit-mask-composite: xor;
                        mask-composite: exclude;
                        filter: blur(6px);
                        opacity: 0;
                        transition: opacity 0.4s ease;
                        animation: neon-spin 7.5s linear infinite paused;
                    }

                    #stats .stat-card:hover::before {
                        opacity: 0.95;
                        animation-play-state: running;
                    }

                    @keyframes neon-spin {
                        to {
                            --neon-angle: 360deg;
                        }
                    }

                    /* Konten card di atas neon */
                    #stats .stat-card>* {
                        position: relative;
                        z-index: 1;
                    }

                    /* Efek hover card */
                    #stats .stat-card:hover {
                        transform: translateY(-10px) scale(1.02);
                        box-shadow:
                            0 30px 80px rgba(0, 0, 0, 0.4),
                            0 0 40px rgba(249, 115, 22, 0.3);
                    }

                    /* Animasi klik */
                    #stats .stat-card:active {
                        animation: stat-click 0.3s ease-out;
                    }

                    @keyframes stat-click {
                        0% {
                            transform: translateY(-10px) scale(1.02);
                        }

                        50% {
                            transform: translateY(-10px) scale(0.98);
                            box-shadow:
                                0 40px 100px rgba(249, 115, 22, 0.4),
                                0 0 60px rgba(249, 115, 22, 0.5);
                        }

                        100% {
                            transform: translateY(-10px) scale(1.02);
                        }
                    }

                    /* Variasi warna untuk setiap card */
                    #stats .stat-card--red {
                        background: linear-gradient(135deg,
                                rgba(249, 115, 22, 0.9),
                                rgba(220, 38, 38, 0.8),
                                rgba(251, 146, 60, 0.9));
                    }

                    #stats .stat-card--purple {
                        background: linear-gradient(135deg,
                                rgba(124, 58, 237, 0.9),
                                rgba(139, 92, 246, 0.8),
                                rgba(168, 85, 247, 0.9));
                    }

                    #stats .stat-card--green {
                        background: linear-gradient(135deg,
                                rgba(5, 150, 105, 0.9),
                                rgba(16, 185, 129, 0.8),
                                rgba(34, 197, 94, 0.9));
                    }

                    /* Stat number styling */
                    #stats .stat-number {
                        font-size: 3rem;
                        line-height: 1;
                        font-weight: 900;
                        color: white;
                        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
                        margin-bottom: 0.5rem;
                    }

                    #stats .stat-label {
                        font-size: 1.1rem;
                        font-weight: 700;
                        color: rgba(255, 255, 255, 0.95);
                        margin-bottom: 1rem;
                    }

                    #stats .stat-card p {
                        font-size: 0.95rem;
                        line-height: 1.6;
                        color: rgba(255, 255, 255, 0.85);
                        margin-bottom: 1.5rem;
                    }

                    #stats .stat-more {
                        display: inline-flex;
                        align-items: center;
                        gap: 8px;
                        font-size: 0.9rem;
                        font-weight: 700;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        color: white;
                        padding: 8px 16px;
                        border-radius: 12px;
                        background: rgba(255, 255, 255, 0.15);
                        backdrop-filter: blur(10px);
                        border: 1px solid rgba(255, 255, 255, 0.2);
                        transition: all 0.3s ease;
                    }

                    #stats .stat-card:hover .stat-more {
                        background: rgba(255, 255, 255, 0.25);
                        transform: translateX(5px);
                        border-color: rgba(255, 255, 255, 0.4);
                    }

                    #stats .stat-more-icon {
                        transition: transform 0.3s ease;
                    }

                    #stats .stat-card:hover .stat-more-icon {
                        transform: translateX(4px) rotate(45deg);
                    }

                    /* ================= CHART CARD ================= */
                    #stats .chart-card {
                        position: relative;
                        border-radius: 26px;
                        padding: 1.5rem;
                        background: linear-gradient(145deg,
                                color-mix(in oklab, var(--card) 90%, transparent),
                                color-mix(in oklab, var(--card-bg-dark) 90%, transparent));
                        border: 1px solid rgba(249, 115, 22, 0.2);
                        box-shadow:
                            0 20px 60px rgba(0, 0, 0, 0.4),
                            0 0 0 1px rgba(255, 255, 255, 0.06);
                        overflow: hidden;
                        transition: all 0.3s ease;

                        opacity: 0;
                        transform: translateY(20px);
                        animation: statsFadeUp 0.7s ease-out forwards;
                        color: var(--txt-body);

                        cursor: pointer;
                    }

                    /* Dark/Light mode adjustment */
                    html[data-theme="dark"] #stats .chart-card {
                        background: linear-gradient(145deg, #111827, #020617);
                        color: white;
                    }

                    html[data-theme="light"] #stats .chart-card {
                        background: linear-gradient(145deg, #ffffff, #f8fafc);
                        color: #0f172a;
                    }

                    /* Neon border untuk chart card */
                    #stats .chart-card::before {
                        content: "";
                        position: absolute;
                        inset: -6px;
                        border-radius: inherit;
                        padding: 10px;
                        pointer-events: none;
                        z-index: 0;
                        background: conic-gradient(from var(--neon-angle),
                                rgba(249, 115, 22, 0) 0deg,
                                rgba(249, 115, 22, 0.15) 22deg,
                                #f97316 55deg,
                                #22d3ee 110deg,
                                #34d399 165deg,
                                rgba(34, 211, 238, 0.15) 220deg,
                                #f97316 300deg,
                                rgba(249, 115, 22, 0) 360deg);
                        -webkit-mask:
                            linear-gradient(#000 0 0) content-box,
                            linear-gradient(#000 0 0);
                        -webkit-mask-composite: xor;
                        mask-composite: exclude;
                        filter: blur(4px);
                        opacity: 0.7;
                        animation: neon-spin 10s linear infinite;
                    }

                    #stats .chart-card>* {
                        position: relative;
                        z-index: 1;
                    }

                    #stats .chart-card:hover {
                        transform: translateY(-8px);
                        box-shadow:
                            0 30px 80px rgba(0, 0, 0, 0.5),
                            0 0 40px rgba(249, 115, 22, 0.25);
                    }

                    #stats .chart-title {
                        font-size: 1.1rem;
                        font-weight: 800;
                        margin-bottom: 0.5rem;
                        color: var(--txt-body);
                    }

                    html[data-theme="light"] #stats .chart-title {
                        color: #0f172a;
                    }

                    #stats .chart-subtitle {
                        font-size: 0.8rem;
                        color: var(--muted);
                        background: rgba(249, 115, 22, 0.2);
                        padding: 4px 10px;
                        border-radius: 20px;
                        font-weight: 700;
                    }

                    #stats .chart-wrapper {
                        position: relative;
                        width: 100%;
                        height: 240px;
                        margin: 1rem 0;
                    }

                    /* ================= ANIMASI ================= */
                    @keyframes statsFadeUp {
                        from {
                            opacity: 0;
                            transform: translateY(20px) scale(0.98);
                        }

                        to {
                            opacity: 1;
                            transform: translateY(0) scale(1);
                        }
                    }

                    /* Stagger animation delay */
                    #stats .stat-card[data-stat="islands"] {
                        animation-delay: 0.1s;
                    }

                    #stats .stat-card[data-stat="unesco"] {
                        animation-delay: 0.2s;
                    }

                    #stats .stat-card[data-stat="population"] {
                        animation-delay: 0.3s;
                    }

                    #stats .chart-card[data-chart="ethnic"] {
                        animation-delay: 0.4s;
                    }

                    #stats .chart-card[data-chart="language"] {
                        animation-delay: 0.5s;
                    }

                    #stats .chart-card[data-chart="religion"] {
                        animation-delay: 0.6s;
                    }

                    /* ================= MODAL ================= */
                    #stats-modal-backdrop {
                        display: none;
                        backdrop-filter: blur(12px);
                        background: rgba(0, 0, 0, 0.8);
                    }

                    #stats-modal-backdrop.is-open {
                        display: flex;
                    }

                    /* ✅ Saat modal open: turunkan z-index navbar agar close button tidak ketutup */
                    html.stats-modal-open .site-header {
                        z-index: 10 !important;
                    }

                    #stats-modal {
                        position: relative;
                        border-radius: 26px;
                        background: linear-gradient(145deg,
                                color-mix(in oklab, var(--card) 95%, transparent),
                                color-mix(in oklab, var(--card-bg-dark) 95%, transparent));
                        color: var(--txt-body);
                        border: 1px solid rgba(249, 115, 22, 0.3);
                        box-shadow:
                            0 30px 80px rgba(0, 0, 0, 0.6),
                            0 0 0 1px rgba(255, 255, 255, 0.06);
                        transform: translateY(20px) scale(0.97);
                        opacity: 0;
                        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
                        overflow: hidden;
                        max-width: 860px;
                        width: 90%;
                        padding: 2rem;
                    }

                    /* Dark/Light mode adjustment */
                    html[data-theme="dark"] #stats-modal {
                        background: linear-gradient(145deg, #111827, #020617);
                        color: white;
                    }

                    html[data-theme="light"] #stats-modal {
                        background: linear-gradient(145deg, #ffffff, #f8fafc);
                        color: #0f172a;
                    }

                    /* Neon border untuk modal */
                    #stats-modal::before {
                        content: "";
                        position: absolute;
                        inset: -6px;
                        border-radius: inherit;
                        padding: 10px;
                        pointer-events: none;
                        z-index: 0;
                        background: conic-gradient(from var(--neon-angle),
                                rgba(249, 115, 22, 0) 0deg,
                                rgba(249, 115, 22, 0.20) 22deg,
                                #f97316 55deg,
                                #22d3ee 110deg,
                                #34d399 165deg,
                                rgba(34, 211, 238, 0.20) 220deg,
                                #f97316 300deg,
                                rgba(249, 115, 22, 0) 360deg);
                        -webkit-mask:
                            linear-gradient(#000 0 0) content-box,
                            linear-gradient(#000 0 0);
                        -webkit-mask-composite: xor;
                        mask-composite: exclude;
                        filter: blur(6px);
                        opacity: 0.8;
                        animation: neon-spin 7.5s linear infinite;
                    }

                    #stats-modal>* {
                        position: relative;
                        z-index: 1;
                    }

                    #stats-modal-backdrop.is-open #stats-modal {
                        transform: translateY(0) scale(1);
                        opacity: 1;
                    }

                    #stats-modal-title {
                        font-size: 1.8rem;
                        font-weight: 900;
                        margin-bottom: 1.5rem;
                        background: linear-gradient(90deg, #f97316, #22d3ee, #34d399);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        animation: neon-glow 3s ease-in-out infinite;
                        background-size: 200% auto;
                    }

                    #stats-modal-body {
                        color: color-mix(in oklab, var(--txt-body) 80%, transparent);
                        font-size: 1rem;
                        line-height: 1.7;
                    }

                    html[data-theme="dark"] #stats-modal-body {
                        color: #d1d5db;
                    }

                    html[data-theme="light"] #stats-modal-body {
                        color: #374151;
                    }

                    #stats-modal-body strong {
                        color: var(--txt-body);
                        font-weight: 700;
                    }

                    #stats-modal-body ul {
                        margin: 1rem 0;
                        padding-left: 1.5rem;
                    }

                    #stats-modal-body li {
                        margin-bottom: 0.5rem;
                        color: var(--muted);
                    }

                    #stats-modal-body a {
                        color: #f97316;
                        text-decoration: underline;
                        text-underline-offset: 3px;
                    }

                    html[data-theme="dark"] #stats-modal-body a {
                        color: #fdba74;
                    }

                    #stats-modal-close {
                        position: absolute;
                        right: 1.5rem;
                        top: 1.5rem;
                        width: 44px;
                        height: 44px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        background: color-mix(in oklab, var(--card) 10%, transparent);
                        border: 1px solid rgba(249, 115, 22, 0.3);
                        color: #f97316;
                        font-size: 1.5rem;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        z-index: 2;
                    }

                    /* Dark/Light mode adjustment */
                    html[data-theme="dark"] #stats-modal-close {
                        background: rgba(255, 255, 255, 0.1);
                    }

                    html[data-theme="light"] #stats-modal-close {
                        background: rgba(0, 0, 0, 0.05);
                        color: #b7410e;
                        border-color: rgba(183, 65, 14, 0.3);
                    }

                    #stats-modal-close:hover {
                        background: rgba(249, 115, 22, 0.9);
                        color: white;
                        transform: rotate(90deg);
                        border-color: #f97316;
                    }

                    /* Responsive */
                    @media (max-width: 768px) {
                        #stats .stat-number {
                            font-size: 2.5rem;
                        }

                        #stats .chart-wrapper {
                            height: 200px;
                        }

                        #stats-modal {
                            padding: 1.5rem;
                        }
                    }
                </style>

                {{-- TIGA CARD UTAMA --}}
                <div class="grid gap-6 lg:grid-cols-3 mb-8">
                    {{-- Pulau di Indonesia --}}
                    <button type="button" class="stat-card stat-card--red text-left text-white" data-stat="islands">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <div class="stat-number">17.380</div>
                                <div class="stat-label">Pulau di Indonesia (2024)</div>
                                <p class="mt-2 text-white/80">
                                    Jumlah pulau bernama dan berkoordinat menurut BIG. Angka ini
                                    terus diperbarui karena dinamika geografis dan verifikasi di lapangan.
                                </p>
                            </div>
                            <div class="opacity-90">
                                <svg viewBox="0 0 24 24" class="w-12 h-12" fill="white" opacity="0.9">
                                    <path d="M11 3a9 9 0 1 0 9 9h-9z" />
                                    <path d="M13 3.055V11h7.945A9.002 9.002 0 0 0 13 3.055z" opacity="0.7" />
                                </svg>
                            </div>
                        </div>
                        <div class="stat-more">
                            Detail Info
                            <span class="stat-more-icon">➜</span>
                        </div>
                    </button>

                    {{-- Warisan Budaya Takbenda UNESCO --}}
                    <button type="button" class="stat-card stat-card--purple text-left text-white" data-stat="unesco">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <div class="stat-number">16</div>
                                <div class="stat-label">WBTb diakui UNESCO</div>
                                <p class="mt-2 text-white/80">
                                    Termasuk Wayang, Keris, Batik, Angklung, Tari Saman, Gamelan,
                                    Jamu, Kebaya, Reog Ponorogo, dan lainnya.
                                </p>
                            </div>
                            <div class="opacity-90">
                                <svg viewBox="0 0 24 24" class="w-12 h-12" fill="white" opacity="0.9">
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M12 3v9l7.8 4.5A9 9 0 0 0 12 3z" opacity="0.7" />
                                </svg>
                            </div>
                        </div>
                        <div class="stat-more">
                            Detail Info
                            <span class="stat-more-icon">➜</span>
                        </div>
                    </button>

                    {{-- Jumlah Penduduk Indonesia --}}
                    <button type="button" class="stat-card stat-card--green text-left text-white"
                        data-stat="population">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <div class="stat-number">286,69 Jt</div>
                                <div class="stat-label">Penduduk (Semester I 2025)</div>
                                <p class="mt-2 text-white/80">
                                    Berdasarkan rilis Dukcapil Kemendagri per 30 Juni 2025,
                                    penduduk Indonesia mencapai 286.693.693 jiwa.
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
                            Detail Info
                            <span class="stat-more-icon">➜</span>
                        </div>
                    </button>
                </div>

                {{-- TIGA CHART: SUKU (BAR), BAHASA (DONUT), AGAMA (PIE) --}}
                <div class="grid gap-6 lg:grid-cols-3 mb-6">
                    {{-- 1. SUKU BANGSA – BAR CHART --}}
                    <div class="chart-card" role="button" tabindex="0" data-chart="ethnic"
                        aria-label="Buka detail chart suku">
                        <div class="flex items-center justify-between mb-3">
                            <p class="chart-title">Komposisi Suku (SP2010)</p>
                            <span class="chart-subtitle">Bar chart</span>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="ethnicChart"></canvas>
                        </div>
                        <p class="mt-3 text-sm text-[var(--muted)]">
                            Persentase suku terbesar berdasarkan Sensus Penduduk 2010 (BPS).
                            Klik card untuk detail & sumber data.
                        </p>
                    </div>

                    {{-- 2. BAHASA SEHARI-HARI – DONUT CHART --}}
                    <div class="chart-card" role="button" tabindex="0" data-chart="language"
                        aria-label="Buka detail chart bahasa">
                        <div class="flex items-center justify-between mb-3">
                            <p class="chart-title">Bahasa Sehari-hari (SP2010)</p>
                            <span class="chart-subtitle">Donut chart</span>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="languageChart"></canvas>
                        </div>
                        <p class="mt-3 text-sm text-[var(--muted)]">
                            14 bahasa dengan penutur terbanyak berdasarkan publikasi BPS SP2010.
                            Klik card untuk detail & sumber data.
                        </p>
                    </div>

                    {{-- 3. AGAMA – PIE CHART --}}
                    <div class="chart-card" role="button" tabindex="0" data-chart="religion"
                        aria-label="Buka detail chart agama">
                        <div class="flex items-center justify-between mb-3">
                            <p class="chart-title">Komposisi Agama (Dukcapil 2021)</p>
                            <span class="chart-subtitle">Pie chart</span>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="religionChart"></canvas>
                        </div>
                        <p class="mt-3 text-sm text-[var(--muted)]">
                            Komposisi agama penduduk berdasarkan agregat Dukcapil (2021).
                            Tekan card untuk detail & sumber data.
                        </p>
                    </div>
                </div>


                {{-- POPUP DETAIL UNTUK SEMUA CARD + CHART --}}
                <div id="stats-modal-backdrop" class="fixed inset-0 z-50 items-center justify-center px-4"
                    aria-hidden="true">
                    <div id="stats-modal" class="relative" role="dialog" aria-modal="true"
                        aria-labelledby="stats-modal-title">
                        <button type="button" id="stats-modal-close" aria-label="Tutup">
                            ×
                        </button>

                        <h3 id="stats-modal-title" class="text-xl sm:text-2xl font-semibold mb-4">
                            Detail Statistik
                        </h3>

                        <div id="stats-modal-body" class="space-y-4 leading-relaxed">
                            {{-- konten diisi via JS --}}
                        </div>


                    </div>
                </div>

                {{-- SCRIPT POPUP + CHART.JS --}}
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                <script>
                    (function() {
                        /* ================= DETAIL MODAL UNTUK STAT CARD ================= */
                        const detailMap = {
                            islands: {
                                title: 'Jumlah Pulau di Indonesia (BIG, 2024)',
                                body: `
                        <p>Badan Informasi Geospasial (BIG) menyampaikan bahwa jumlah pulau Indonesia yang telah memiliki <strong>nama</strong> dan <strong>koordinat resmi</strong> pada tahun 2024 mencapai <strong>17.380 pulau</strong>.</p>

                        <p>Penambahan ini terjadi karena proses identifikasi, verifikasi, dan pembaruan data geospasial di lapangan (termasuk wilayah seperti Bangka Belitung, Sulawesi Tenggara, Maluku Utara, dan Kalimantan Barat).</p>

                        <ul class="mt-3 list-disc list-inside space-y-2">
                            <li>Pulau adalah daratan alami yang dikelilingi air dan tetap muncul saat pasang tertinggi.</li>
                            <li>Jumlah pulau bisa berubah karena abrasi, sedimentasi, dan pemutakhiran data.</li>
                            <li>Data diperkaya melalui citra satelit dan verifikasi pemerintah daerah.</li>
                        </ul>

                        <p class="mt-4"><strong>Sumber:</strong>
                            <a href="https://ramadhan.antaranews.com/video/4525867/big-ungkap-jumlah-pulau-di-indonesia-tahun-2024-capai-17380" target="_blank" rel="noopener">ANTARA (video) – Pernyataan BIG (12 Des 2024)</a>
                            •
                            <a href="https://nasional.kontan.co.id/news/resmi-dari-badan-geospasial-ini-jumlah-pulau-di-seluruh-indonesia-tahun-2024" target="_blank" rel="noopener">Kontan – Ringkasan berita jumlah pulau 2024</a>
                        </p>
                    `
                            },
                            unesco: {
                                title: 'Warisan Budaya Takbenda Indonesia di UNESCO (ICH)',
                                body: `
                        <p>UNESCO mencatat Indonesia memiliki <strong>16 elemen</strong> Warisan Budaya Takbenda (Intangible Cultural Heritage) yang terinskripsi (gabungan dari <em>Representative List</em> dan <em>Urgent Safeguarding List</em>).</p>

                        <p>Contoh elemen yang terkenal: <strong>Wayang</strong>, <strong>Keris</strong>, <strong>Batik</strong>, <strong>Angklung</strong>, <strong>Tari Saman</strong>, <strong>Noken</strong>, <strong>Gamelan</strong>, <strong>Jamu</strong>, <strong>Kebaya</strong>, dan <strong>Reog Ponorogo</strong>.</p>

                        <p class="mt-3">Daftar resmi & pembaruan status terbaik adalah halaman UNESCO ICH (negara: Indonesia).</p>

                        <p class="mt-4"><strong>Sumber (resmi):</strong>
                            <a href="https://ich.unesco.org/en/state/indonesia-ID" target="_blank" rel="noopener">UNESCO ICH – Indonesia (jumlah elemen)</a>
                            •
                            <a href="https://ich.unesco.org/en/state/indonesia-ID?info=elements-on-the-lists" target="_blank" rel="noopener">UNESCO ICH – Daftar elemen per tahun</a>
                        </p>
                    `
                            },
                            population: {
                                title: 'Jumlah Penduduk Indonesia (Dukcapil Kemendagri)',
                                body: `
                        <p>Menurut rilis Direktorat Jenderal Dukcapil Kementerian Dalam Negeri, jumlah penduduk Indonesia per <strong>30 Juni 2025</strong> adalah <strong>286.693.693 jiwa</strong>.</p>

                        <p>Angka ini merupakan data administrasi kependudukan (berbasis pencatatan Dukcapil) dan lazim digunakan untuk kebutuhan kebijakan publik serta layanan kependudukan.</p>

                        <p class="mt-4"><strong>Sumber:</strong>
                            <a href="https://www.cnnindonesia.com/ekonomi/20250711131422-532-1249573/penduduk-ri-tembus-286-juta-orang-pada-2025" target="_blank" rel="noopener">CNN Indonesia – Kutipan angka Dukcapil (11 Jul 2025)</a>
                            •
                            <a href="https://www.bps.go.id/id/statistics-table/2/MTk3NSMy/jumlah-penduduk-pertengahan-tahun--ribu-jiwa-.html" target="_blank" rel="noopener">BPS – Tabel jumlah penduduk pertengahan tahun (referensi statistik)</a>
                        </p>
                    `
                            }
                        };

                        /* ================= DETAIL MODAL UNTUK CHART ================= */
                        const chartDetailMap = {
                            ethnic: {
                                title: 'Chart: Komposisi Suku (Sensus Penduduk 2010)',
                                body: `
                        <p>Data suku/etnis pada chart ini mengacu pada ringkasan publikasi BPS terkait hasil <strong>Sensus Penduduk 2010 (SP2010)</strong>. Dalam rilis BPS, <strong>Suku Jawa</strong> adalah yang terbesar sekitar <strong>40%</strong>, disusul <strong>Suku Sunda</strong> sekitar <strong>15%</strong>.</p>

                        <p>Catatan: pengelompokan suku di SP2010 dilakukan melalui kerja sama BPS dan ISEAS, dan hasilnya juga digunakan dalam analisis demografi etnis.</p>

                        <p class="mt-4"><strong>Sumber (resmi/tepercaya):</strong>
                            <a href="https://www.bps.go.id/id/news/2015/11/18/127/mengulik-data-suku-di-indonesia.html" target="_blank" rel="noopener">BPS – “Mengulik Data Suku di Indonesia” (berbasis SP2010)</a>
                            •
                            <a href="https://sp2010.bps.go.id/" target="_blank" rel="noopener">BPS – Portal SP2010</a>
                        </p>
                    `
                            },
                            language: {
                                title: 'Chart: Bahasa Sehari-hari (Hasil SP2010)',
                                body: `
                        <p>Data bahasa sehari-hari pada chart ini merujuk pada publikasi BPS yang membahas <strong>kewarganegaraan, suku bangsa, agama, dan bahasa sehari-hari</strong> (hasil SP2010).</p>

                        <p>Secara umum, publikasi BPS menunjukkan penggunaan bahasa daerah masih dominan dalam komunikasi harian, dengan bahasa Jawa menjadi salah satu yang terbesar dalam jumlah penutur.</p>

                        <p class="mt-4"><strong>Sumber (resmi):</strong>
                            <a href="https://www.bps.go.id/id/publication/2012/05/23/55eca38b7fe0830834605b35/kewarganegaraan-suku-bangsa-agama-dan-bahasa-sehari-hari-penduduk-indonesia.html" target="_blank" rel="noopener">BPS – Publikasi “Kewarganegaraan, Suku Bangsa, Agama, dan Bahasa Sehari-hari…” (SP2010)</a>
                        </p>
                    `
                            },
                            religion: {
                                title: 'Chart: Komposisi Agama (Dukcapil, 2021)',
                                body: `
                        <p>Komposisi agama pada chart ini mengacu pada agregat data Dukcapil yang sering diringkas dalam berbagai publikasi data (misalnya ringkasan berbasis data Dukcapil per akhir 2021).</p>

                        <p>Mayoritas penduduk Indonesia beragama Islam (sekitar 86%), disusul Protestan dan Katolik, kemudian Hindu, Buddha, dan Konghucu.</p>

                        <p class="mt-4"><strong>Sumber (tepercaya):</strong>
                            <a href="https://databoks.katadata.co.id/demografi/statistik/e158869f40c2acf/sebanyak-8693-penduduk-indonesia-beragama-islam-pada-31-desember-2021" target="_blank" rel="noopener">Katadata Databoks – Ringkasan komposisi agama (data Dukcapil)</a>
                            •
                            <a href="https://id.wikipedia.org/wiki/Agama_di_Indonesia" target="_blank" rel="noopener">Wikipedia – Agama di Indonesia (menyebut sumber Dukcapil 2021)</a>
                        </p>
                    `
                            }
                        };

                        const backdrop = document.getElementById('stats-modal-backdrop');
                        const modalTitle = document.getElementById('stats-modal-title');
                        const modalBody = document.getElementById('stats-modal-body');
                        const closeBtn = document.getElementById('stats-modal-close');

                        function openModalByHtml(title, bodyHtml) {
                            modalTitle.textContent = title;
                            modalBody.innerHTML = bodyHtml;

                            backdrop.classList.add('is-open');
                            document.body.classList.add('overflow-hidden');
                            document.documentElement.classList.add('stats-modal-open');

                            // set aria
                            backdrop.setAttribute('aria-hidden', 'false');

                            // focus close for accessibility
                            setTimeout(() => {
                                if (closeBtn) closeBtn.focus();
                            }, 0);
                        }

                        function openModal(statKey) {
                            const data = detailMap[statKey];
                            if (!data) return;
                            openModalByHtml(data.title, data.body);
                        }

                        function openChartModal(chartKey) {
                            const data = chartDetailMap[chartKey];
                            if (!data) return;
                            openModalByHtml(data.title, data.body);
                        }

                        function closeModal() {
                            backdrop.classList.remove('is-open');
                            document.body.classList.remove('overflow-hidden');
                            document.documentElement.classList.remove('stats-modal-open');
                            backdrop.setAttribute('aria-hidden', 'true');
                        }

                        // Stat cards
                        document.querySelectorAll('#stats .stat-card[data-stat]').forEach(function(card) {
                            card.addEventListener('click', function() {
                                const key = card.getAttribute('data-stat');
                                openModal(key);
                            });
                        });

                        // Chart cards (klik card -> detail)
                        document.querySelectorAll('#stats .chart-card[data-chart]').forEach(function(card) {
                            const key = card.getAttribute('data-chart');

                            card.addEventListener('click', function(e) {
                                // kalau user klik canvas, tetap buka detail
                                openChartModal(key);
                            });

                            // Enter/Space accessibility
                            card.addEventListener('keydown', function(e) {
                                if (e.key === 'Enter' || e.key === ' ') {
                                    e.preventDefault();
                                    openChartModal(key);
                                }
                            });
                        });

                        // Close actions
                        if (closeBtn) closeBtn.addEventListener('click', closeModal);
                        if (backdrop) {
                            backdrop.addEventListener('click', function(e) {
                                if (e.target === backdrop) closeModal();
                            });
                        }
                        document.addEventListener('keydown', function(e) {
                            if (e.key === 'Escape') closeModal();
                        });

                        /* ================= CHART: DATA ================= */
                        // Palette warna sama dengan quiz
                        const neonPalette = [
                            '#f97316', '#22d3ee', '#34d399', '#fb923c', '#0ea5e9',
                            '#84cc16', '#8b5cf6', '#ef4444', '#f59e0b', '#06b6d4',
                            '#10b981', '#6366f1', '#ec4899', '#14b8a6', '#9ca3af'
                        ];

                        // Suku (ringkasan bergaya SP2010)
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

                        // Bahasa (ringkasan hasil SP2010)
                        const languageLabels = [
                            'Jawa', 'Indonesia', 'Sunda', 'Melayu', 'Madura',
                            'Minangkabau', 'Banjar', 'Bugis', 'Bali',
                            'Batak', 'Cirebon', 'NTT Lain',
                            'Sasak', 'Aceh', 'Lainnya'
                        ];
                        const languageData = [
                            31.79, 19.94, 15.14, 3.69, 3.62,
                            1.98, 1.71, 1.64, 1.57,
                            1.55, 1.44, 1.40,
                            1.26, 1.10, 12.08
                        ];

                        // Agama (ringkasan Dukcapil 2021)
                        const religionLabels = [
                            'Islam', 'Protestan', 'Katolik',
                            'Hindu', 'Buddha', 'Konghucu', 'Agama Lainnya'
                        ];
                        const religionData = [86.93, 7.47, 3.08, 1.71, 0.74, 0.05, 0.03];

                        const commonOptions = {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 1000,
                                easing: 'easeOutQuart'
                            },
                            plugins: {
                                tooltip: {
                                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                    borderColor: 'rgba(249, 115, 22, 0.5)',
                                    borderWidth: 1,
                                    titleColor: '#f9fafb',
                                    bodyColor: '#d1d5db',
                                    callbacks: {
                                        label: function(ctx) {
                                            const label = ctx.label || '';
                                            const value = ctx.parsed;
                                            return label + ': ' + Number(value).toFixed(2) + '%';
                                        }
                                    }
                                },
                                legend: {
                                    labels: {
                                        color: '#d1d5db',
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            }
                        };

                        // Ethnic Chart
                        const ethnicCanvas = document.getElementById('ethnicChart');
                        if (ethnicCanvas) {
                            const ethnicCtx = ethnicCanvas.getContext('2d');
                            new Chart(ethnicCtx, {
                                type: 'bar',
                                data: {
                                    labels: ethnicLabels,
                                    datasets: [{
                                        data: ethnicData,
                                        backgroundColor: neonPalette,
                                        borderRadius: 6,
                                        borderWidth: 0
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
                                                color: 'rgba(156, 163, 175, 0.1)'
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        // Language Chart
                        const languageCanvas = document.getElementById('languageChart');
                        if (languageCanvas) {
                            const languageCtx = languageCanvas.getContext('2d');
                            new Chart(languageCtx, {
                                type: 'doughnut',
                                data: {
                                    labels: languageLabels,
                                    datasets: [{
                                        data: languageData,
                                        backgroundColor: neonPalette,
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
                                            labels: {
                                                padding: 15
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        // Religion Chart
                        const religionCanvas = document.getElementById('religionChart');
                        if (religionCanvas) {
                            const religionCtx = religionCanvas.getContext('2d');
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
                                        borderWidth: 0
                                    }]
                                },
                                options: {
                                    ...commonOptions,
                                    plugins: {
                                        ...commonOptions.plugins,
                                        legend: {
                                            position: 'right'
                                        }
                                    }
                                }
                            });
                        }
                    })();
                </script>
            </section>



            @include('partials.camera-ar')

            {{-- ================= QUIZ INDONESIA ================= --}}
            <section id="quiz" class="py-12">
                <h2 class="neon-title">
                    Kuis Budaya Indonesia
                </h2>
                <div class="title-decoration"></div>
                <p class="neon-subtitle">
                    Kuis ini menyajikan pertanyaan seputar sejarah, dan warisan budaya Nusantara sebagai bagian dari upaya
                    memahami identitas budaya Indonesia.
                </p>

                @include('partials.quiz-section', ['quiz' => $quiz ?? null])
            </section>

            @include('partials.testimonial')
        </div>
    </section>


@endsection
