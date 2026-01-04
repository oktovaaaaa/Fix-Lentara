{{-- resources/views/islands.blade.php --}}
@extends('layouts.app')

@section('title', ($selectedIsland->title ?? $selectedIsland->name ?? 'Pulau') . ' – Lentara')

@section('content')
    {{-- HERO shared --}}
    @include('partials.landing-hero')

    @php
        // ===============================
        // SAFETY DEFAULTS (JANGAN HAPUS)
        // ===============================
        $selectedIsland    = $selectedIsland ?? null;

        $featuresByType    = $featuresByType ?? [];
        $demographics      = $demographics ?? ['religion'=>collect(),'ethnicity'=>collect(),'language'=>collect()];

        $historiesByTribe  = $historiesByTribe ?? collect();
        $availableTribes   = $availableTribes ?? [];

        $quizzesByTribe    = $quizzesByTribe ?? collect();
        $globalQuiz        = $globalQuiz ?? null;

        // ABOUT PULAU (BARU) - dari controller
        $aboutIslandPage   = $aboutIslandPage ?? null;
        $aboutIslandItems  = $aboutIslandItems ?? collect();

        // Warisan payload dari controller
        $tribeKey          = $tribeKey ?? request()->query('tribe', '');
        $tribeKey          = trim((string) $tribeKey);
        if ($tribeKey === '' && !empty($availableTribes)) {
            $tribeKey = (string) $availableTribes[0];
        }

        $tribePage         = $tribePage ?? null;
        $itemsByCategory   = $itemsByCategory ?? [
            'pakaian' => collect(),
            'rumah_tradisi' => collect(),
            'senjata_alatmusik' => collect(),
        ];

        // Helper URL untuk pindah suku (server-driven)
        $tribeUrl = function(string $t) {
            $base = request()->url();
            return $base . '?tribe=' . urlencode($t);
        };

        // Active tab helper
        $isActiveTribe = function(string $t) use ($tribeKey) {
            return strcasecmp(trim($tribeKey), trim($t)) === 0;
        };

        // Histories per suku aktif
        $currentTribeHistories = $historiesByTribe[$tribeKey] ?? collect();

        // Quiz per suku aktif
        $currentTribeQuiz = $quizzesByTribe[$tribeKey] ?? null;

        // Feature collections (pulau-level, dari admin Features)
        $aboutFeatures       = $featuresByType['about'] ?? collect();
        $historyFeatures     = $featuresByType['history'] ?? collect();
        $destinationFeatures = $featuresByType['destination'] ?? collect();
        $foodFeatures        = $featuresByType['food'] ?? collect();
        $cultureFeatures     = $featuresByType['culture'] ?? collect();

        // judul header pulau
        $islandNameTitle = $selectedIsland->name ?? $selectedIsland->title ?? 'Pulau';
        $islandPretty    = $selectedIsland->subtitle ?? $selectedIsland->name ?? 'Indonesia';
    @endphp

    {{-- WRAPPER UNIVERSAL --}}
    <section class="relative z-[10] py-12 sm:py-16 px-4 sm:px-6 bg-[var(--bg-body)] text-[var(--txt-body)]">

        {{-- =========================================================
           GLOBAL CSS (GABUNGAN DARI PARTIALS)
           - tribe-styles
           - about-tribe
           - history
           - stats (about-island-stats)
           - + standard title/decoration/subtitle style (HOME STYLE)
        ========================================================= --}}
        <style>
            /* =========================================================
               HOME TITLE SYSTEM (STANDARD)
               - Dipakai biar semua section title + decoration + subtitle MATCH HOME
               - Kita samakan class yg beda-beda agar tampilannya seragam
            ========================================================= */

            /* Title (neon gradient) - basis dari yang kamu pakai di History & Stats */
    .neon-title {
        text-align: center !important;
        display: block !important;
        width: 100% !important;
        margin-left: auto !important;
        margin-right: auto !important;

        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        margin-bottom: 0.5rem;

        position: relative;
        background: linear-gradient(
            90deg,
            #ff6b00 0%,
            #ff8c42 25%,
            #ffaa6b 50%,
            #ff8c42 75%,
            #ff6b00 100%
        );
        background-size: 200% auto;
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: neon-glow 3s ease-in-out infinite;
    }

    .title-decoration,
    .mi-title-decoration {
        width: 120px;
        height: 4px;
        margin: 0.8rem auto 1.5rem;
        background: linear-gradient(90deg, transparent, #ff6b00, transparent);
        border-radius: 2px;
        position: relative;
    }

    .title-decoration::before,
    .title-decoration::after,
    .mi-title-decoration::before,
    .mi-title-decoration::after {
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

    .title-decoration::before,
    .mi-title-decoration::before {
        left: 0;
    }

    .title-decoration::after,
    .mi-title-decoration::after {
        right: 0;
    }

    .neon-subtitle,
    #history .history-subtitle,
    #about-tribe .about-desc {
        font-size: 1.1rem;
        color: var(--muted);
        max-width: 760px;
        margin: 0 auto 2rem;
        line-height: 1.6;
        text-align: center;
    }

    @keyframes neon-glow {
        0%, 100% {
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

            /* Khusus About-tribe subtitle: jangan terlalu besar (biar match home) */
            #about-tribe .about-desc {
                font-weight: 400;
            }

            /* =========================================================
               TRIBE TABS STYLE (dari islands/partials/tribe-styles.blade.php)
               (Aku pindahin ke sini tanpa mengubah isi CSS-nya)
            ========================================================= */
            .tribe-tab {
                background: rgba(148, 163, 184, 0.12); /* abu soft, masih kelihatan di dark / light */
                color: var(--txt-body, #020617);
                border-radius: 999px;
                font-weight: 600;
                font-size: 0.85rem;
                padding: 0.55rem 1.5rem;
                border: 1px solid transparent;
                transition: all 0.18s ease-out;
            }

            .tribe-tab:hover {
                transform: translateY(-1px);
                box-shadow: 0 14px 30px rgba(15, 23, 42, 0.25);
            }

            .tribe-tab.is-active {
                background-image: linear-gradient(90deg, #fb923c, #f97316, #fb7185);
                color: #f9fafb;
                border-color: rgba(248, 250, 252, 0.45);
                box-shadow:
                        0 0 0 1px rgba(248, 250, 252, 0.25),
                        0 20px 40px rgba(0, 0, 0, 0.55);
            }

            [data-tribe-panel].hidden {
                display: none;
            }

            .history-empty {
                margin-top: 1rem;
                font-size: 0.8rem;
                color: var(--muted, #9ca3af);
            }

            /* =========================================================
               ABOUT SUKU (dari islands/partials/about.blade.php)
               (Aku pindahin ke sini tanpa memotong isi)
            ========================================================= */

            #about-tribe.about-tribe-section{
                padding: 5rem 1.5rem;
                background: transparent;
                font-family: 'Inter', system-ui, -apple-system, sans-serif;
            }

            #about-tribe .about-wrap{
                max-width: 1200px;
                margin: 0 auto;
            }

            /* ===== HEADER ===== */
            #about-tribe .about-head{
                margin-bottom: 3rem;
                text-align: center;
                position: relative;
            }

            #about-tribe .about-label{
                display: inline-block;
                font-size: 0.875rem;
                letter-spacing: 0.15em;
                text-transform: uppercase;
                color: var(--brand);
                font-weight: 600;
                margin-bottom: 1rem;
                padding: 0.375rem 1rem;
                /* background: color-mix(in srgb, var(--brand) 10%, transparent); */
                border-radius: 4px;
            }

            /* NOTE: about-title sudah distandardkan di HOME TITLE SYSTEM di atas */

            #about-tribe .about-head-actions{
                margin-top: 1.5rem;
            }

            #about-tribe .btn-more{
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.75rem 1.5rem;
                border-radius: 6px;
                border: 2px solid var(--brand);
                background: var(--brand);
                color: white;
                text-decoration: none;
                font-weight: 600;
                font-size: 0.9375rem;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            #about-tribe .btn-more:hover{
                background: transparent;
                color: var(--brand);
                transform: translateY(-2px);
                box-shadow: 0 8px 24px color-mix(in srgb, var(--brand) 20%, transparent);
            }

            /* ===== ITEM LIST ===== */
            #about-tribe .items{
                display: flex;
                flex-direction: column;
                gap: 4rem;
            }

            /* Layout utama - gambar di kiri, konten di kanan */
            #about-tribe .about-item{
                display: grid;
                grid-template-columns: 1fr 1.2fr;
                gap: 4rem;
                align-items: start;
            }

            /* Alternating layout untuk item berikutnya */
            #about-tribe .about-item:nth-child(even) {
                grid-template-columns: 1.2fr 1fr;
            }

            #about-tribe .about-item:nth-child(even) .image-content {
                order: 2;
            }

            #about-tribe .about-item:nth-child(even) .text-content {
                order: 1;
            }

            /* Bagian gambar kiri */
            #about-tribe .image-content{
                position: relative;
                height: 100%;
            }

            #about-tribe .imgbox{
                border-radius: 12px;
                overflow: hidden;
                /* background: var(--card); */
                /* box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); */
                height: 400px;
                width: 100%;
                position: relative;
                border: none;
            }

            #about-tribe .imgbox img{
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                transition: transform 0.8s ease;
            }

            #about-tribe .imgbox:hover img{
                transform: scale(1.05);
            }

            /* Efek overlay pada gambar */
            #about-tribe .imgbox::after{
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(
                    45deg,
                    rgba(183, 65, 14, 0.1) 0%,
                    rgba(183, 65, 14, 0.05) 50%,
                    transparent 100%
                );
                pointer-events: none;
            }

            /* Bagian konten kanan */
            #about-tribe .text-content{
                display: flex;
                flex-direction: column;
                justify-content: center;
                padding: 1rem 0;
            }

            #about-tribe .item-title{
                margin: 0 0 1.5rem 0;
                font-size: 1.75rem;
                font-weight: 700;
                color: var(--txt-body);
                line-height: 1.3;
                position: relative;
                padding-bottom: 1rem;
            }

            #about-tribe .item-title::after{
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 60px;
                height: 3px;
                background: var(--brand);
                border-radius: 2px;
            }

            #about-tribe .item-desc{
                margin: 0 0 2rem 0;
                font-size: 1.0625rem;
                line-height: 1.8;
                color: color-mix(in srgb, var(--txt-body) 70%, transparent);
                white-space: pre-line;
            }

            /* Poin-poin checklist */
            #about-tribe .points{
                margin-top: 1.5rem;
                display: grid;
                gap: 0.875rem;
            }

            #about-tribe .point{
                display: flex;
                gap: 0.875rem;
                align-items: flex-start;
                font-size: 1rem;
                line-height: 1.6;
                color: color-mix(in srgb, var(--txt-body) 80%, transparent);
                padding: 0.5rem 0;
            }

            #about-tribe .check{
                width: 24px;
                height: 24px;
                min-width: 24px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, var(--brand), color-mix(in srgb, var(--brand) 80%, black));
                color: white;
                font-size: 0.875rem;
                font-weight: 600;
                flex-shrink: 0;
                margin-top: 0.125rem;
            }

            /* Bagian poin untuk layout tanpa gambar */
            #about-tribe .points-only{
                padding: 2.5rem;
                /* background: linear-gradient(135deg,
                    color-mix(in srgb, var(--brand) 8%, transparent) 0%,
                    color-mix(in srgb, var(--brand) 4%, transparent) 100%); */
                border-radius: 12px;
                /* border: 1px solid color-mix(in srgb, var(--brand) 15%, transparent); */
                margin-top: 1.5rem;
            }

            #about-tribe .points-only .points{
                margin-top: 0;
            }

            /* Link item */
            #about-tribe .item-link{
                margin-top: 2rem;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                text-decoration: none;
                font-weight: 600;
                color: var(--brand);
                font-size: 0.9375rem;
                transition: all 0.3s ease;
                padding: 0.75rem 1.5rem;
                border: 2px solid color-mix(in srgb, var(--brand) 30%, transparent);
                border-radius: 6px;
                width: fit-content;
            }

            #about-tribe .item-link:hover{
                background: var(--brand);
                color: white;
                border-color: var(--brand);
                gap: 0.75rem;
                transform: translateY(-2px);
                box-shadow: 0 8px 24px color-mix(in srgb, var(--brand) 20%, transparent);
            }

            /* Responsive */
            @media (max-width: 1024px){
                #about-tribe .about-item{
                    gap: 3rem;
                }

                #about-tribe .imgbox{
                    height: 350px;
                }
            }

            @media (max-width: 768px){
                #about-tribe.about-tribe-section{
                    padding: 3rem 1rem;
                }

                #about-tribe .about-head{
                    margin-bottom: 2.5rem;
                }

                #about-tribe .about-desc{
                    font-size: 1rem;
                }

                #about-tribe .items{
                    gap: 3rem;
                }

                #about-tribe .about-item,
                #about-tribe .about-item:nth-child(even) {
                    grid-template-columns: 1fr;
                    gap: 2rem;
                }

                #about-tribe .about-item:nth-child(even) .image-content,
                #about-tribe .about-item:nth-child(even) .text-content {
                    order: unset;
                }

                #about-tribe .imgbox{
                    height: 300px;
                }

                #about-tribe .item-title{
                    font-size: 1.5rem;
                }

                #about-tribe .points-only{
                    padding: 2rem;
                }
            }

            @media (max-width: 480px){
                #about-tribe .about-label{
                    font-size: 0.75rem;
                }

                #about-tribe .imgbox{
                    height: 250px;
                }

                #about-tribe .points-only{
                    padding: 1.5rem;
                }
            }

            /* Animasi */
            #about-tribe .about-item {
                opacity: 0;
                transform: translateY(30px);
                animation: fadeInUp 0.6s ease-out forwards;
            }

            #about-tribe .about-item:nth-child(1) { animation-delay: 0.1s; }
            #about-tribe .about-item:nth-child(2) { animation-delay: 0.2s; }
            #about-tribe .about-item:nth-child(3) { animation-delay: 0.3s; }
            #about-tribe .about-item:nth-child(4) { animation-delay: 0.4s; }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* =========================================================
               HISTORY (dari islands/partials/history.blade.php)
               (Aku pindahin ke sini tanpa memotong isi)
            ========================================================= */

            #history.history-section {
                padding: 4rem 1.5rem;
                background: transparent;
                display: flex;
                justify-content: center;
                overflow: hidden; /* aman dari glow yang melebar */
            }

            #history .history-container {
                width: 100%;
                max-width: 1100px;
                text-align: center;
                font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                color: var(--txt-body);
            }

            /* neon-title sudah distandardkan di HOME TITLE SYSTEM */

            #history .history-subtitle {
                font-size: 1rem;
                max-width: 640px;
                margin: 0 auto 3rem auto;
                color: var(--muted);
                line-height: 1.6;
            }

            /* ====== TIMELINE ====== */
            #history .timeline {
                position: relative;
                padding: 2rem 0;
                margin: 0 auto;
            }

            /* garis tengah (desktop) / nanti di mobile digeser kiri */
            #history .timeline::before {
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

            #history .timeline-item {
                position: relative;
                width: 100%;
                margin-bottom: 2.5rem;
                display: flex;
            }

            /* titik di tengah garis */
            #history .timeline-item::before {
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

            #history .timeline-card {
                position: relative;
                width: 100%;
                max-width: 520px;
                border-radius: 20px;
            }

            /* ===== NEON BORDER SMOOTH MUTER DI SEPANJANG GARIS CARD ===== */
            @property --border-angle {
                syntax: "<angle>";
                inherits: false;
                initial-value: 0deg;
            }

            #history .timeline-card-glow {
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
                to { --border-angle: 360deg; }
            }

            /* isi card */
            #history .timeline-card-inner {
                position: relative;
                border-radius: 18px;

                /* fallback aman kalau --card-bg-dark belum ada */
                background: linear-gradient(
                    145deg,
                    var(--card),
                    color-mix(in oklab, var(--card) 82%, #020617 18%)
                );

                padding: 1.8rem 2rem;

                box-shadow:
                    0 14px 32px rgba(0, 0, 0, 0.25),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1);

                z-index: 1;
                text-align: left;
                border: 1px solid rgba(255, 107, 0, 0.1);

                /* FIX: pastikan konten tidak keluar karena glitch */
                overflow: hidden;
            }

            /* Dark/Light mode adjustment (samakan home) */
            html[data-theme="dark"] #history .timeline-card-inner {
                background: linear-gradient(145deg, #111827, #020617);
            }
            html[data-theme="light"] #history .timeline-card-inner {
                background: linear-gradient(145deg, #ffffff, #f8fafc);
            }

            #history .timeline-badge {
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
                max-width: 100%;
            }

            #history .timeline-heading {
                font-size: 1.2rem;
                margin-bottom: .5rem;
                color: var(--txt-body);
                font-weight: 700;

                /* FIX overflow heading */
                overflow-wrap: anywhere;
                word-break: break-word;
                hyphens: auto;
                max-width: 100%;
            }

            #history .timeline-text {
                font-size: .95rem;
                line-height: 1.7;
                color: var(--muted);

                /* FIX UTAMA: long string TANPA SPASI */
                overflow-wrap: anywhere;
                word-break: break-word;
                hyphens: auto;
                max-width: 100%;
            }
            #history .timeline-text * {
                overflow-wrap: anywhere;
                word-break: break-word;
                hyphens: auto;
            }

            #history .timeline-link {
                display: inline-flex;
                align-items: center;
                gap: .35rem;
                margin-top: .9rem;
                font-weight: 800;
                color: var(--brand);
                text-decoration: none;

                overflow-wrap: anywhere;
                word-break: break-word;
                max-width: 100%;
            }
            #history .timeline-link:hover {
                text-decoration: underline;
            }

            /* ===== OPTIONAL: Title kecil di atas timeline ===== */
            #history .history-mini-title {
                font-size: 1rem;
                font-weight: 700;
                margin: 0 0 .25rem 0;
                color: var(--txt-body);
                text-align: left;
            }
            #history .history-mini-subtitle {
                font-size: .95rem;
                color: var(--muted);
                margin: 0 0 1.25rem 0;
                text-align: left;
            }

            /* ===== RESPONSIVE ===== */
            @media (max-width: 767px) {
                #history .timeline::before {
                    left: 14px;
                    transform: none;
                }

                #history .timeline-item {
                    padding-left: 2.8rem;
                }

                #history .timeline-item::before {
                    left: 14px;
                    transform: none;
                }

                #history .history-container {
                    text-align: left;
                }

                #history .timeline-card-inner {
                    padding: 1.25rem 1.25rem;
                }

                #history .timeline-heading {
                    font-size: 1.05rem;
                }

                #history .timeline-text {
                    font-size: .92rem;
                }
            }

            @media (min-width: 768px) {
                #history .timeline-item:nth-child(odd) {
                    justify-content: flex-start;
                    padding-right: 50%;
                }

                #history .timeline-item:nth-child(even) {
                    justify-content: flex-end;
                    padding-left: 50%;
                }

                #history .timeline-item:nth-child(odd) .timeline-card {
                    margin-right: 2.2rem;
                }

                #history .timeline-item:nth-child(even) .timeline-card {
                    margin-left: 2.2rem;
                }
            }

            /* Empty state */
            #history .history-empty {
                font-size: 1rem;
                color: var(--muted);
                padding: .5rem 0;
                text-align: left;
            }

            /* Optional features cards (pulau umum) */
            #history .feature-card {
                border: 1px solid var(--line);
                border-radius: 20px;
                padding: 1rem 1.1rem;
                background: var(--card);
                box-shadow: 0 10px 24px rgba(0,0,0,.08);
                text-align: left;
                overflow: hidden;
            }
            #history .feature-card h4 {
                font-size: 1rem;
                font-weight: 800;
                margin: 0 0 .35rem 0;
                color: var(--txt-body);

                overflow-wrap: anywhere;
                word-break: break-word;
                hyphens: auto;
            }
            #history .feature-card p {
                font-size: .92rem;
                color: var(--muted);
                line-height: 1.65;

                overflow-wrap: anywhere;
                word-break: break-word;
                hyphens: auto;
            }

            /* =========================================================
               STATS (dari islands/partials/about-island-stats.blade.php)
               (Aku pindahin ke sini tanpa memotong isi style stats)
            ========================================================= */

            @property --neon-angle {
                syntax: "<angle>";
                inherits: false;
                initial-value: 0deg;
            }

            #stats .neon-title {
                font-size: 1.8rem;
                font-weight: 900;
                text-align: center;
                letter-spacing: 0.02em;
                margin-bottom: .25rem;
                color: var(--txt-body);
            }

            #stats .title-decoration {
                width: 120px;
                height: 6px;
                margin: .4rem auto 0;
                border-radius: 999px;
                background: linear-gradient(90deg, #f97316, #22d3ee, #34d399);
                filter: blur(.2px);
                opacity: .9;
            }

            #stats .neon-subtitle {
                max-width: 48rem;
                margin: 1rem auto 0;
                text-align: center;
                font-size: .95rem;
                line-height: 1.7;
                color: var(--muted);
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
                border: 1px solid rgba(255, 255, 255, 0.1);

                opacity: 0;
                transform: translateY(20px);
                animation: statsFadeUp 0.7s ease-out forwards;
                text-align: left;
            }

            #stats .stat-card::before {
                content: "";
                position: absolute;
                inset: -6px;
                border-radius: inherit;
                padding: 10px;
                pointer-events: none;
                z-index: 0;
                background: conic-gradient(
                    from var(--neon-angle),
                    rgba(249, 115, 22, 0) 0deg,
                    rgba(249, 115, 22, 0.20) 22deg,
                    #f97316 55deg,
                    #22d3ee 110deg,
                    #34d399 165deg,
                    rgba(34, 211, 238, 0.20) 220deg,
                    #f97316 300deg,
                    rgba(249, 115, 22, 0) 360deg
                );
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

            @keyframes neon-spin { to { --neon-angle: 360deg; } }

            #stats .stat-card > * { position: relative; z-index: 1; }

            #stats .stat-card:hover {
                transform: translateY(-10px) scale(1.02);
                box-shadow:
                    0 30px 80px rgba(0, 0, 0, 0.4),
                    0 0 40px rgba(249, 115, 22, 0.3);
            }

            #stats .stat-card:active { animation: stat-click 0.3s ease-out; }
            @keyframes stat-click {
                0% { transform: translateY(-10px) scale(1.02); }
                50% { transform: translateY(-10px) scale(0.98); box-shadow: 0 40px 100px rgba(249,115,22,.4), 0 0 60px rgba(249,115,22,.5); }
                100% { transform: translateY(-10px) scale(1.02); }
            }

            #stats .stat-card--red {
                background: linear-gradient(135deg,
                    rgba(249, 115, 22, 0.92),
                    rgba(220, 38, 38, 0.78),
                    rgba(251, 146, 60, 0.92)
                );
                color: #fff;
            }

            #stats .stat-card--purple {
                background: linear-gradient(135deg,
                    rgba(124, 58, 237, 0.9),
                    rgba(139, 92, 246, 0.8),
                    rgba(168, 85, 247, 0.9)
                );
                color: #fff;
            }

            #stats .stat-card--green {
                background: linear-gradient(135deg,
                    rgba(5, 150, 105, 0.9),
                    rgba(16, 185, 129, 0.8),
                    rgba(34, 197, 94, 0.9)
                );
                color: #fff;
            }

            #stats .stat-number {
                font-size: 2.6rem;
                line-height: 1;
                font-weight: 900;
                color: #fff;
                text-shadow: 0 2px 10px rgba(0,0,0,0.3);
                margin-bottom: 0.5rem;
                word-break: break-word;
            }

            #stats .stat-label {
                font-size: 1.05rem;
                font-weight: 800;
                color: rgba(255,255,255,.95);
                margin-bottom: 0.9rem;
            }

            #stats .stat-card p {
                font-size: 0.95rem;
                line-height: 1.6;
                color: rgba(255,255,255,.85);
                margin-bottom: 1.2rem;
            }

            #stats .stat-more {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-size: 0.85rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                color: #fff;
                padding: 8px 14px;
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

            #stats .stat-more-icon { transition: transform 0.3s ease; }
            #stats .stat-card:hover .stat-more-icon { transform: translateX(4px) rotate(45deg); }

            /* ================= CHART CARD ================= */
            #stats .chart-card {
                position: relative;
                border-radius: 26px;
                padding: 1.5rem;
                background: linear-gradient(145deg,
                    color-mix(in oklab, var(--card) 92%, transparent),
                    color-mix(in oklab, var(--card) 78%, transparent)
                );
                border: 1px solid rgba(249, 115, 22, 0.18);
                box-shadow:
                    0 20px 60px rgba(0, 0, 0, 0.35),
                    0 0 0 1px rgba(255, 255, 255, 0.06);
                overflow: hidden;
                transition: all 0.3s ease;

                opacity: 0;
                transform: translateY(20px);
                animation: statsFadeUp 0.7s ease-out forwards;
                color: var(--txt-body);
            }

            html[data-theme="dark"] #stats .chart-card {
                background: linear-gradient(145deg, #111827, #020617);
                color: #fff;
                border-color: rgba(249, 115, 22, 0.22);
            }

            html[data-theme="light"] #stats .chart-card {
                background: linear-gradient(145deg, #ffffff, #f8fafc);
                color: #0f172a;
            }

            #stats .chart-card::before {
                content: "";
                position: absolute;
                inset: -6px;
                border-radius: inherit;
                padding: 10px;
                pointer-events: none;
                z-index: 0;
                background: conic-gradient(
                    from var(--neon-angle),
                    rgba(249, 115, 22, 0) 0deg,
                    rgba(249, 115, 22, 0.15) 22deg,
                    #f97316 55deg,
                    #22d3ee 110deg,
                    #34d399 165deg,
                    rgba(34, 211, 238, 0.15) 220deg,
                    #f97316 300deg,
                    rgba(249, 115, 22, 0) 360deg
                );
                -webkit-mask:
                    linear-gradient(#000 0 0) content-box,
                    linear-gradient(#000 0 0);
                -webkit-mask-composite: xor;
                mask-composite: exclude;
                filter: blur(4px);
                opacity: 0.72;
                animation: neon-spin 10s linear infinite;
            }

            #stats .chart-card > * { position: relative; z-index: 1; }

            #stats .chart-card:hover {
                transform: translateY(-8px);
                box-shadow:
                    0 30px 80px rgba(0, 0, 0, 0.5),
                    0 0 40px rgba(249, 115, 22, 0.25);
            }

            #stats .chart-title {
                font-size: 1.02rem;
                font-weight: 900;
                margin-bottom: 0.5rem;
                color: var(--txt-body);
            }

            html[data-theme="dark"] #stats .chart-title { color: #fff; }
            html[data-theme="light"] #stats .chart-title { color: #0f172a; }

            #stats .chart-subtitle {
                font-size: 0.8rem;
                color: var(--muted);
                background: rgba(249, 115, 22, 0.18);
                padding: 4px 10px;
                border-radius: 20px;
                font-weight: 800;
            }

            #stats .chart-wrapper {
                position: relative;
                width: 100%;
                height: 240px;
                margin: 1rem 0;
            }

            @keyframes statsFadeUp {
                from { opacity: 0; transform: translateY(20px) scale(0.98); }
                to { opacity: 1; transform: translateY(0) scale(1); }
            }

            #stats .stat-card[data-stat="population"] { animation-delay: 0.12s; }
            #stats .stat-card[data-stat="ethnicity"]  { animation-delay: 0.22s; }
            #stats .stat-card[data-stat="language"]   { animation-delay: 0.32s; }

            #stats .chart-card:nth-child(1) { animation-delay: 0.42s; }
            #stats .chart-card:nth-child(2) { animation-delay: 0.52s; }
            #stats .chart-card:nth-child(3) { animation-delay: 0.62s; }

            /* ================= MODAL ================= */
            #stats-modal-backdrop {
                display: none;
                backdrop-filter: blur(12px);
                background: rgba(0, 0, 0, 0.82);
            }
            #stats-modal-backdrop.is-open { display: flex; }

            #stats-modal {
                position: relative;
                border-radius: 26px;
                background: linear-gradient(145deg,
                    color-mix(in oklab, var(--card) 95%, transparent),
                    color-mix(in oklab, var(--card) 82%, transparent)
                );
                color: var(--txt-body);
                border: 1px solid rgba(249, 115, 22, 0.26);
                box-shadow:
                    0 30px 80px rgba(0, 0, 0, 0.6),
                    0 0 0 1px rgba(255, 255, 255, 0.06);
                transform: translateY(20px) scale(0.97);
                opacity: 0;
                transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
                overflow: hidden;
                max-width: 860px;
                width: 92%;
                padding: 2rem;
            }

            html[data-theme="dark"] #stats-modal { background: linear-gradient(145deg, #111827, #020617); color: #fff; }
            html[data-theme="light"] #stats-modal { background: linear-gradient(145deg, #ffffff, #f8fafc); color: #0f172a; }

            #stats-modal::before {
                content: "";
                position: absolute;
                inset: -6px;
                border-radius: inherit;
                padding: 10px;
                pointer-events: none;
                z-index: 0;
                background: conic-gradient(
                    from var(--neon-angle),
                    rgba(249, 115, 22, 0) 0deg,
                    rgba(249, 115, 22, 0.20) 22deg,
                    #f97316 55deg,
                    #22d3ee 110deg,
                    #34d399 165deg,
                    rgba(34, 211, 238, 0.20) 220deg,
                    #f97316 300deg,
                    rgba(249, 115, 22, 0) 360deg
                );
                -webkit-mask:
                    linear-gradient(#000 0 0) content-box,
                    linear-gradient(#000 0 0);
                -webkit-mask-composite: xor;
                mask-composite: exclude;
                filter: blur(6px);
                opacity: 0.82;
                animation: neon-spin 7.5s linear infinite;
            }

            #stats-modal > * { position: relative; z-index: 1; }

            #stats-modal-backdrop.is-open #stats-modal {
                transform: translateY(0) scale(1);
                opacity: 1;
            }

            #stats-modal-title {
                font-size: 1.7rem;
                font-weight: 950;
                margin-bottom: 1.1rem;
                background: linear-gradient(90deg, #f97316, #22d3ee, #34d399);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-size: 200% auto;
                animation: neon-glow 3s ease-in-out infinite;
            }
            @keyframes neon-glow {
                0%, 100% { background-position: 0% 50%; filter: drop-shadow(0 0 10px rgba(249,115,22,0.22)); }
                50% { background-position: 100% 50%; filter: drop-shadow(0 0 18px rgba(34,211,238,0.22)); }
            }

            #stats-modal-body {
                color: color-mix(in oklab, var(--txt-body) 82%, transparent);
                font-size: 1rem;
                line-height: 1.75;
            }
            html[data-theme="dark"] #stats-modal-body { color: #d1d5db; }
            html[data-theme="light"] #stats-modal-body { color: #374151; }

            #stats-modal-body strong { color: var(--txt-body); font-weight: 800; }
            html[data-theme="dark"] #stats-modal-body strong { color: #fff; }
            html[data-theme="light"] #stats-modal-body strong { color: #111827; }

            #stats-modal-body ul { margin: 1rem 0; padding-left: 1.25rem; }
            #stats-modal-body li { margin-bottom: 0.5rem; color: var(--muted); }

            #stats-modal-close {
                position: absolute;
                right: 1.25rem;
                top: 1.25rem;
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

            html[data-theme="dark"] #stats-modal-close { background: rgba(255, 255, 255, 0.1); }
            html[data-theme="light"] #stats-modal-close { background: rgba(0, 0, 0, 0.05); color: #b7410e; border-color: rgba(183, 65, 14, 0.3); }

            #stats-modal-close:hover {
                background: rgba(249, 115, 22, 0.9);
                color: #fff;
                transform: rotate(90deg);
                border-color: #f97316;
            }

            @media (max-width: 768px) {
                #stats .stat-number { font-size: 2.25rem; }
                #stats .chart-wrapper { height: 210px; }
                #stats-modal { padding: 1.35rem; }
            }



        </style>

        {{-- Styles untuk tabs + timeline (punyamu yang sudah ada) --}}
        @include('islands.partials.tribe-styles')

        <div class="max-w-5xl mx-auto space-y-10">

            {{-- ===================================================
               ABOUT PULAU + STATISTIK (BARU)
               - tampil di bawah HERO
               - tampil sebelum picker suku
               =================================================== --}}
            @include('islands.partials.about-island-stats', [
                'selectedIsland'    => $selectedIsland,
                'aboutIslandPage'   => $aboutIslandPage,
                'aboutIslandItems'  => $aboutIslandItems,
                'demographics'      => $demographics,
            ])

            {{-- =========================
               TABS SUKU (SERVER DRIVEN)
               ========================= --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-[var(--muted)] mb-1">
                        Pilih Suku di {{ $islandPretty }}
                    </p>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">
                        Eksplorasi Budaya per Suku
                    </h2>
                </div>

                <div class="inline-flex flex-wrap gap-2 bg-[color-mix(in_srgb,var(--bg-body)_80%,#e5e7eb_20%)] p-1.5 rounded-full">
                    @forelse($availableTribes as $t)
                        @php $t = (string) $t; @endphp
                        <a href="{{ $tribeUrl($t) }}"
                           class="tribe-tab {{ $isActiveTribe($t) ? 'is-active' : '' }}"
                           aria-current="{{ $isActiveTribe($t) ? 'page' : 'false' }}">
                            {{ $t }}
                        </a>
                    @empty
                        <span class="text-xs text-[var(--muted)] px-3 py-2">
                            Belum ada suku (cek config/tribes.php atau histories).
                        </span>
                    @endforelse
                </div>
            </div>

            <div class="space-y-12" id="suku-wrapper">

                {{-- ===================================================
                   ABOUT SUKU (DINONAKTIFKAN)
                   Karena kamu mau About GENERAL per pulau.
                   =================================================== --}}
                {{--
                @include('islands.partials.about', [
                    'tribeKey'   => $tribeKey,
                    'aboutPage'  => $aboutPage,
                    'aboutItems' => $aboutItems,
                ])
                --}}

                {{-- ===================================================
                   SEJARAH SUKU (DINAMIS: IslandHistory per tribe)
                   DIPINDAH KE PARTIAL + CSS DI DALAM PARTIAL
                   =================================================== --}}
                @include('islands.partials.history', [
                    'tribeKey' => $tribeKey,
                    'currentTribeHistories' => $currentTribeHistories,
                    'historyFeatures' => $historyFeatures,
                ])

                {{-- ===================================================
                   DESTINASI SUKU
                   =================================================== --}}
<section id="destinations" class="py-12">
    <h2 class="neon-title">
        Destinasi Budaya Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}
    </h2>
    <div class="title-decoration"></div>
    <p class="neon-subtitle">
        Rekomendasi tempat dan pengalaman budaya yang berkaitan dengan Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}.
    </p>

    @if($destinationFeatures->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($destinationFeatures as $f)
                <div class="border border-[var(--line)] rounded-2xl p-4 bg-[var(--card)] shadow-sm">
                    <h3 class="text-sm sm:text-base font-semibold mb-1">{{ $f->title }}</h3>
                    <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-1">
                        {{ $f->content }}
                    </p>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-[var(--muted)]">
            Konten destinasi untuk {{ $tribeKey }} belum diinput dari admin.
        </p>
    @endif
</section>


                {{-- ===================================================
                   KULINER SUKU
                   =================================================== --}}
@include('islands.partials.ai-foods', [
    'tribeKey' => $tribeKey,
    'aiFoodRecommendation' => $aiFoodRecommendation ?? null,
])



                {{-- ===================================================
                   WARISAN (DINAMIS DARI ADMIN)
                   =================================================== --}}
                @include('islands.partials.heritage.section', [
                    'tribeKey' => $tribeKey,
                    'tribePage' => $tribePage,
                    'itemsByCategory' => $itemsByCategory,
                ])

                {{-- ===================================================
                   QUIZ (DINAMIS PER SUKU, fallback global)
                   =================================================== --}}
<section id="quiz" class="py-12">
    <h2 class="neon-title">
        Kuis Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}
    </h2>
    <div class="title-decoration"></div>
    <p class="neon-subtitle">
        Uji pengetahuanmu tentang budaya dan keunikan Suku {{ $tribeKey !== '' ? $tribeKey : '—' }} lewat kuis singkat.
    </p>

    @include('partials.quiz-section', [
        'quiz' => $currentTribeQuiz ?: $globalQuiz
    ])
</section>


            </div>
        </div>
    </section>
@endsection
