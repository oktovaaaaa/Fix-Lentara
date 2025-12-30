{{-- resources/views/partials/warisan/section.blade.php --}}
@php
    use App\Models\HeritageItem;
    use Illuminate\Support\Str;

    $labels = HeritageItem::CATEGORIES;

    // pastikan keys selalu ada
    $itemsByCategory = $itemsByCategory ?? [
        'pakaian' => collect(),
        'rumah_tradisi' => collect(),
        'senjata_alatmusik' => collect(),
    ];

    // Ambil heroTitle dari tribePage jika ada
    $heroTitle = $tribePage->hero_title ?? ("Warisan " . ($tribeKey ?? 'Budaya'));
    $heroDescription = $tribePage->hero_description ?? null;
@endphp

<section id="warisan" class="py-10 w-full overflow-x-hidden">

    <style>
        /* =========================================================
           NEON ORANGE COVERFLOW - IMPROVED LAYOUT
           Perbaikan untuk mobile & desktop

           FIX DI FILE INI:
           1) MODAL DESKTOP GAMBAR TIDAK MUNCUL: fix tinggi modal image (no height:100%)
           2) LIGHT MODE MODAL TERLALU GELAP: overlay modal dibuat light-friendly
           3) MOBILE CARD "KETUTUPAN": di mobile caption HANYA tampil JUDUL (desc disembunyikan)
           4) OPACITY DITURUNKAN: gambar lebih jelas (card overlay + caption + modal overlay)

           NOTES KUSTOMISASI:
           - Semua opacity pakai rgba(..., X). X makin kecil = makin bening.
        ========================================================= */

        @property --neon-orange-angle {
            syntax: "<angle>";
            inherits: false;
            initial-value: 0deg;
        }

        #warisan{
            /* Warna utama - menggunakan variabel dari root */
            --wf-bg: var(--bg-body);
            --wf-txt: var(--txt-body);
            --wf-muted: var(--muted);
            --wf-line: var(--line);

            /* Warna neon orange */
            --neon-primary: var(--brand);
            --neon-secondary: var(--brand-2);
            --neon-glow: rgba(var(--brand-rgb, 249, 115, 22), 0.7);

            /* Glass effect colors (OPACITY DITURUNKAN biar gambar lebih jelas) */
            --wf-caption-bg-light: rgba(255, 255, 255, 0.08);
            --wf-caption-bg-dark: rgba(20, 30, 45, 0.08);

            --wf-modal-bg-light: rgba(255, 255, 255, 0.18);
            --wf-modal-bg-dark: rgba(10, 15, 26, 0.20);

            --wf-caption-border-light: rgba(185, 65, 14, 0.18);
            --wf-caption-border-dark: rgba(249, 115, 22, 0.18);

            /* Overlay modal per theme (light lebih terang) */
            --wf-overlay-bg-light: rgba(255, 255, 255, 0.60);
            --wf-overlay-bg-dark: rgba(0, 0, 0, 0.82);

            /* coverflow sizing - OPTIMIZED FOR RESPONSIVE */
            --wf-card-w: 280px;
            --wf-card-h: 380px;
            --wf-gap-x: 180px;
            --wf-tilt: 20deg;
            --wf-depth: 140px;
            --wf-scale-step: .15;
            --wf-blur-step: 0.8px;

            --wf-radius: 24px;
            --wf-shadow: var(--shadow);
            --wf-shadow-active: 0 0 40px rgba(var(--brand-rgb, 249, 115, 22), 0.35);
        }

        /* Animation untuk efek neon */
        @keyframes orange-neon-spin {
            0% { --neon-orange-angle: 0deg; }
            100% { --neon-orange-angle: 360deg; }
        }

        /* ================= MODAL DETAIL STYLES ================= */
        .wf-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;

            /* FIX: light mode tidak gelap */
            background: var(--wf-overlay-bg-light);
            backdrop-filter: blur(14px) saturate(130%);
            -webkit-backdrop-filter: blur(14px) saturate(130%);

            z-index: 9999;
            display: none;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        html[data-theme="dark"] .wf-modal-overlay {
            background: var(--wf-overlay-bg-dark);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .wf-modal-overlay.active {
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
        }

        .wf-modal-container {
            width: 90%;
            max-width: 900px;
            max-height: 90vh;

            /* light mode: terang & glass */
            background: rgba(255, 255, 255, 0.30);
            backdrop-filter: blur(18px) saturate(160%);
            -webkit-backdrop-filter: blur(18px) saturate(160%);
            border: 1px solid rgba(var(--brand-rgb, 249, 115, 22), 0.16);

            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--shadow),
                        0 0 80px rgba(var(--brand-rgb, 249, 115, 22), 0.22);
            opacity: 0;
            transform: translateY(30px) scale(0.9);
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        html[data-theme="dark"] .wf-modal-container {
            background: rgba(10, 15, 26, 0.34);
            border: 1px solid rgba(var(--brand-rgb, 249, 115, 22), 0.20);
            box-shadow: var(--shadow),
                        0 0 80px rgba(var(--brand-rgb, 249, 115, 22), 0.32);
        }

        .wf-modal-overlay.active .wf-modal-container {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .wf-modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--card);
            border: 2px solid rgba(var(--brand-rgb, 249, 115, 22), 0.5);
            color: var(--txt-body);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 100;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.20);
        }

        .wf-modal-close:hover {
            border-color: var(--brand);
            background: rgba(var(--brand-rgb, 249, 115, 22), 0.10);
            transform: rotate(90deg) scale(1.1);
            box-shadow: 0 0 30px rgba(var(--brand-rgb, 249, 115, 22), 0.55);
        }

        .wf-modal-close svg {
            width: 24px;
            height: 24px;
            stroke-width: 2.5;
        }

        .wf-modal-content {
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
        }

        @media (min-width: 768px) {
            .wf-modal-content {
                flex-direction: row;
            }
        }

        /* ========== MODAL IMAGE (FIX desktop collapse + konsisten crop) ========== */
        .wf-modal-image {
            flex: 1;
            min-height: 300px;

            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;

            position: relative;
            overflow: hidden;
            border-radius: 20px 20px 0 0;
        }

        /* Kontrol posisi fokus gambar */
        .wf-modal-image.pos-center { background-position: center center; }
        .wf-modal-image.pos-top { background-position: center top; }
        .wf-modal-image.pos-bottom { background-position: center bottom; }

        @media (min-width: 768px) {
            /* FIX: desktop jangan collapse (jangan pakai height:100%) */
            .wf-modal-content { min-height: 440px; }
            .wf-modal-image {
                min-height: 440px;
                height: auto;
                border-radius: 20px 0 0 20px;
            }
        }

        /* Overlay di atas gambar modal (dibuat lebih tipis biar gambar lebih jelas) */
        .wf-modal-image::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom,
                rgba(0,0,0,0.05) 0%,
                rgba(0,0,0,0.03) 50%,
                transparent 100%);
            pointer-events: none;
        }

        .wf-modal-image-fallback {
            background: linear-gradient(135deg,
                rgba(var(--brand-rgb, 249, 115, 22), 0.10),
                rgba(var(--brand-2-rgb, 251, 146, 60), 0.06),
                rgba(10, 15, 26, 0.35));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--brand);
            font-size: 4rem;
            opacity: 0.85;
        }

        .wf-modal-info {
            flex: 1;
            padding: 2.5rem;
            overflow-y: auto;
            max-height: 60vh;

            background: var(--wf-modal-bg-light);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(var(--brand-rgb, 249, 115, 22), 0.15);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
            border-radius: 0 0 20px 20px;
        }

        @media (min-width: 768px) {
            .wf-modal-info {
                max-height: 90vh;
                border-radius: 0 20px 20px 0;
                border-left: none;
            }
        }

        .wf-modal-title {
            margin: 0 0 1.5rem 0;
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--txt-body);
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.10);
        }

        .wf-modal-description {
            margin: 0 0 2rem 0;
            color: var(--txt-body);
            line-height: 1.8;
            font-size: 1.15rem;
            opacity: 0.95;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .wf-modal-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2.5rem;
            padding: 1.2rem;
            border-radius: 16px;
            background: rgba(var(--brand-rgb, 249, 115, 22), 0.08);
            border: 1px solid rgba(var(--brand-rgb, 249, 115, 22), 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .wf-meta-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            color: var(--txt-body);
            font-weight: 600;
            opacity: 0.92;
        }

        .wf-meta-item svg {
            width: 20px;
            height: 20px;
            color: var(--brand);
        }

        /* ================= KATEGORI TITLE ================= */
        #warisan .wf-category-title-wrap {
            text-align: center;
            margin: 2rem 0 1rem;
            position: relative;
            padding: 0 1rem;
        }

        #warisan .wf-category-title {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--wf-txt);
            margin: 0;
            line-height: 1.2;
            display: inline-block;
            position: relative;
            padding: 0 2rem;
        }

        #warisan .wf-category-title::before,
        #warisan .wf-category-title::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 80px;
            height: 2px;
            background: linear-gradient(90deg,
                transparent,
                var(--brand),
                var(--brand-2),
                transparent
            );
            transform: translateY(-50%);
        }

        #warisan .wf-category-title::before {
            right: 100%;
            margin-right: 1rem;
        }

        #warisan .wf-category-title::after {
            left: 100%;
            margin-left: 1rem;
        }

        #warisan .wf-category-desc {
            margin: .6rem auto 0;
            max-width: 48rem;
            color: var(--wf-muted);
            font-size: 1rem;
            line-height: 1.6;
            padding: 0 1rem;
        }

        /* ================= ROW CONTAINER ================= */
        #warisan .wf-row{
            width: 100%;
            max-width: 1400px;
            margin: 0 auto 3rem;
            padding: 0 2rem;
            position: relative;
        }

        /* Hapus elemen lama */
        #warisan .wf-row-head,
        #warisan .wf-row-title,
        #warisan .wf-row-pill,
        #warisan .wf-dot {
            display: none !important;
        }

        /* ================= COVERFLOW CONTAINER ================= */
        #warisan .wf-flow{
            position: relative;
            padding: 0;
            background: transparent;
            overflow: visible;
            width: 100%;
        }

        /* ================= VIEWPORT ================= */
        #warisan .wf-viewport{
            position: relative;
            height: calc(var(--wf-card-h) + 40px);
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1200px;
            overflow: visible;
            width: 100%;
        }

        /* ================= TRACK ================= */
        #warisan .wf-track{
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ================= CARD ================= */
        #warisan .wf-card{
            position: absolute;
            top: 50%;
            left: 50%;
            width: var(--wf-card-w);
            height: var(--wf-card-h);
            transform-style: preserve-3d;
            border-radius: var(--wf-radius);
            overflow: hidden;
            border: none;
            background: var(--card);
            cursor: pointer;
            user-select: none;
            transition: all 0.5s cubic-bezier(0.2, 0.8, 0.2, 1.2);

            transform:
                translate(-50%, -50%)
                translateX(calc(var(--off) * var(--wf-gap-x)))
                translateZ(calc((1 - (var(--abs) * .4)) * var(--wf-depth)))
                rotateY(calc(var(--off) * (var(--wf-tilt) * -1)))
                scale(calc(1 - (var(--abs) * var(--wf-scale-step))));

            opacity: calc(0.8 - (var(--abs) * .3));
            filter: blur(calc(var(--abs) * var(--wf-blur-step)))
                    brightness(calc(1 - (var(--abs) * .2)));
            will-change: transform, opacity, filter, box-shadow;
        }

        /* Efek neon border */
        #warisan .wf-card::before {
            content: "";
            position: absolute;
            inset: -3px;
            border-radius: calc(var(--wf-radius) + 3px);
            background: conic-gradient(
                from var(--neon-orange-angle),
                transparent,
                var(--brand),
                var(--brand-2),
                var(--brand),
                transparent
            );
            padding: 3px;
            -webkit-mask:
                linear-gradient(#000 0 0) content-box,
                linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            filter: blur(8px);
            opacity: 0;
            transition: opacity 0.4s ease;
            animation: orange-neon-spin 6s linear infinite;
            z-index: 0;
        }

        /* Kartu aktif */
        #warisan .wf-card[data-active="1"]{
            opacity: 1;
            filter: none;
            box-shadow:
                var(--wf-shadow),
                var(--wf-shadow-active);
            z-index: 100;
        }

        #warisan .wf-card[data-active="1"]::before {
            opacity: 0.75;
        }

        /* Hover efek */
        #warisan .wf-card:hover {
            transform:
                translate(-50%, -50%)
                translateX(calc(var(--off) * var(--wf-gap-x)))
                translateZ(calc((1 - (var(--abs) * .4)) * var(--wf-depth) + 20px))
                rotateY(calc(var(--off) * (var(--wf-tilt) * -1)))
                scale(calc(1 - (var(--abs) * var(--wf-scale-step)) + 0.05));
        }

        /* ================= IMAGE/CARD CONTENT ================= */
        #warisan .wf-media{
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: 1;
        }

        /* Overlay gambar card (DITIPISKAN biar gambar jelas) */
        #warisan .wf-media::after{
            content:"";
            position:absolute;
            inset:0;
            background: linear-gradient(
                to bottom,
                transparent 60%,
                rgba(0,0,0,0.03) 78%,
                rgba(0,0,0,0.08) 100%
            );
            pointer-events: none;
        }

        /* Caption card (glass, opacity diturunkan) */
        #warisan .wf-caption{
            position:absolute;
            left: 16px;
            right: 16px;
            bottom: 16px;
            padding: 18px;
            border-radius: 16px;
            background: var(--wf-caption-bg-light);
            backdrop-filter: blur(18px) saturate(170%);
            -webkit-backdrop-filter: blur(18px) saturate(170%);
            border: 1px solid var(--wf-caption-border-light);
            z-index: 2;
            transform: translateY(5px);
            transition: all 0.3s ease;
        }

        /* glow aktif dibuat lebih halus */
        #warisan .wf-card[data-active="1"] .wf-caption {
            border-color: rgba(var(--brand-rgb, 249, 115, 22), 0.22);
            box-shadow: 0 0 16px rgba(var(--brand-rgb, 249, 115, 22), 0.18);
        }

        #warisan .wf-caption h4{
            margin:0 0 8px 0;
            color: var(--txt-body);
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            line-height: 1.2;
            text-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        #warisan .wf-caption p{
            margin: 0;
            color: var(--txt-body);
            font-size: .9rem;
            line-height: 1.5;
            opacity: 0.92;
        }

        /* Fallback gradient */
        #warisan .wf-fallback{
            background:
                radial-gradient(70% 60% at 30% 20%,
                    rgba(var(--brand-rgb, 249, 115, 22), 0.35),
                    transparent 55%),
                radial-gradient(70% 60% at 70% 30%,
                    rgba(var(--brand-2-rgb, 251, 146, 60), 0.25),
                    transparent 55%),
                linear-gradient(135deg, var(--card), var(--bg-body), var(--card));
        }

        /* ================= NAV BUTTONS DESKTOP ================= */
        #warisan .wf-nav{
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 200;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: 2px solid rgba(var(--brand-rgb, 249, 115, 22), 0.5);
            background: var(--card);
            color: var(--brand);
            display:flex;
            align-items:center;
            justify-content:center;
            box-shadow:
                0 15px 35px rgba(0,0,0,0.28),
                0 0 22px rgba(var(--brand-rgb, 249, 115, 22), 0.35);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        #warisan .wf-nav::before {
            content: "";
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background: conic-gradient(
                from var(--neon-orange-angle),
                transparent,
                var(--brand),
                var(--brand-2),
                var(--brand),
                transparent
            );
            padding: 3px;
            -webkit-mask:
                linear-gradient(#000 0 0) content-box,
                linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            filter: blur(6px);
            opacity: 0;
            transition: opacity 0.3s ease;
            animation: orange-neon-spin 4s linear infinite;
        }

        #warisan .wf-nav:hover{
            transform: translateY(-50%) scale(1.1);
            border-color: rgba(var(--brand-rgb, 249, 115, 22), 0.8);
            background: rgba(var(--brand-rgb, 249, 115, 22), 0.08);
            box-shadow:
                0 20px 40px rgba(0,0,0,0.35),
                0 0 35px rgba(var(--brand-rgb, 249, 115, 22), 0.55);
        }

        #warisan .wf-nav:hover::before {
            opacity: 1;
        }

        #warisan .wf-nav:active{
            transform: translateY(-50%) scale(0.95);
        }

        #warisan .wf-nav svg{
            width: 24px;
            height: 24px;
            stroke-width: 2.5;
            filter: drop-shadow(0 0 8px rgba(var(--brand-rgb, 249, 115, 22), 0.7));
        }

        #warisan .wf-prev{ left: -25px; }
        #warisan .wf-next{ right: -25px; }

        /* ================= NAV BUTTONS MOBILE ================= */
        #warisan .wf-mobile-nav {
            display: none;
            position: relative;
            margin-top: .6rem;
            z-index: 100;
        }

        #warisan .wf-mobile-nav-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
        }

        #warisan .wf-mobile-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid rgba(var(--brand-rgb, 249, 115, 22), 0.6);
            background: var(--card);
            color: var(--brand);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                0 10px 25px rgba(0,0,0,0.28),
                0 0 18px rgba(var(--brand-rgb, 249, 115, 22), 0.35);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        #warisan .wf-mobile-btn:hover {
            transform: scale(1.1);
            border-color: rgba(var(--brand-rgb, 249, 115, 22), 0.9);
            background: rgba(var(--brand-rgb, 249, 115, 22), 0.15);
            box-shadow:
                0 15px 30px rgba(0,0,0,0.35),
                0 0 28px rgba(var(--brand-rgb, 249, 115, 22), 0.55);
        }

        #warisan .wf-mobile-btn svg {
            width: 28px;
            height: 28px;
            stroke-width: 2.5;
        }

        /* ================= EMPTY STATE ================= */
        #warisan .wf-empty{
            min-height: calc(var(--wf-card-h) + 40px);
            display:flex;
            align-items:center;
            justify-content:center;
            padding: 2rem;
            text-align:center;
        }

        #warisan .wf-empty-box{
            max-width: 520px;
            width: 100%;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            border: 2px dashed rgba(var(--brand-rgb, 249, 115, 22), 0.35);
            background: rgba(var(--brand-rgb, 249, 115, 22), 0.06);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        #warisan .wf-empty-box::before {
            content: "";
            position: absolute;
            inset: -2px;
            border-radius: 22px;
            padding: 2px;
            background: conic-gradient(
                from var(--neon-orange-angle),
                transparent,
                rgba(var(--brand-rgb, 249, 115, 22), 0.25),
                transparent
            );
            -webkit-mask:
                linear-gradient(#000 0 0) content-box,
                linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            filter: blur(8px);
            opacity: 0.55;
            animation: orange-neon-spin 10s linear infinite;
        }

        #warisan .wf-empty-ico{
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--brand);
            filter: drop-shadow(0 0 15px rgba(var(--brand-rgb, 249, 115, 22), 0.45));
        }

        #warisan .wf-empty-title{
            margin: 0;
            font-weight: 900;
            color: var(--wf-txt);
            font-size: 1.4rem;
        }

        #warisan .wf-empty-desc{
            margin: 0.75rem 0 0;
            font-size: 1rem;
            color: var(--wf-muted);
        }

        /* ================= DARK MODE OVERRIDES ================= */
        html[data-theme="dark"] #warisan {
            --neon-glow: rgba(var(--brand-rgb, 249, 115, 22), 0.7);
            --wf-shadow-active: 0 0 40px rgba(var(--brand-rgb, 249, 115, 22), 0.35);
        }

        html[data-theme="dark"] .wf-modal-info {
            background: var(--wf-modal-bg-dark);
            border: 1px solid rgba(var(--brand-rgb, 249, 115, 22), 0.20);
        }

        html[data-theme="dark"] #warisan .wf-caption {
            background: var(--wf-caption-bg-dark);
            border: 1px solid var(--wf-caption-border-dark);
        }

        html[data-theme="dark"] .wf-modal-close {
            background: var(--card);
            color: var(--txt-body);
        }

        html[data-theme="dark"] #warisan .wf-nav,
        html[data-theme="dark"] #warisan .wf-mobile-btn {
            background: var(--card);
            color: var(--brand);
        }

        /* ================= RESPONSIVE - IMPROVED ================= */
        @media (max-width: 1200px){
            #warisan{
                --wf-card-w: 250px;
                --wf-card-h: 350px;
                --wf-gap-x: 160px;
            }
            #warisan .wf-category-title {
                font-size: 1.8rem;
            }
            #warisan .wf-prev { left: -15px; }
            #warisan .wf-next { right: -15px; }
        }

        @media (max-width: 992px){
            #warisan{
                --wf-card-w: 220px;
                --wf-card-h: 320px;
                --wf-gap-x: 140px;
            }
            #warisan .wf-category-title {
                font-size: 1.6rem;
            }
            #warisan .wf-category-title::before,
            #warisan .wf-category-title::after {
                width: 60px;
            }
            #warisan .wf-row {
                padding: 0 1.5rem;
            }
            #warisan .wf-prev { left: -10px; }
            #warisan .wf-next { right: -10px; }
        }

        @media (max-width: 768px){
            /* HIDE DESKTOP NAV, SHOW MOBILE NAV */
            #warisan .wf-nav {
                display: none !important;
            }

            #warisan .wf-mobile-nav {
                display: block;
            }

            #warisan{
                --wf-card-w: 200px;
                --wf-card-h: 300px;
                --wf-gap-x: 40px;
                --wf-tilt: 10deg;
                --wf-depth: 60px;
                --wf-scale-step: .08;
                --wf-blur-step: 0.3px;
            }

            #warisan .wf-viewport {
                height: calc(var(--wf-card-h) + 60px);
            }

            #warisan .wf-category-title {
                font-size: 1.5rem;
                padding: 0 1rem;
            }

            #warisan .wf-category-title::before,
            #warisan .wf-category-title::after {
                width: 30px;
            }

            #warisan .wf-row {
                padding: 0 0.5rem;
                margin: 0 auto 2rem;
            }

            #warisan .wf-card {
                opacity: calc(0.9 - (var(--abs) * .2));
            }

            /* ===========================
               FIX: MOBILE CAPTION JANGAN NUTUPI GAMBAR
               - DI MOBILE: tampilkan JUDUL SAJA
               - sembunyikan deskripsi (p)
               - kecilkan padding dan radius biar clean
            =========================== */
            #warisan .wf-caption{
                padding: 12px 14px;
                left: 12px;
                right: 12px;
                bottom: 12px;
                border-radius: 14px;
            }

            #warisan .wf-caption p{
                display: none !important; /* INI KUNCI: cuma judul yang tampil */
            }

            #warisan .wf-caption h4{
                margin: 0;
                font-size: 1.05rem;
                line-height: 1.2;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .wf-modal-container {
                width: 95%;
                max-height: 85vh;
            }

            .wf-modal-info {
                padding: 1.8rem;
            }

            .wf-modal-title {
                font-size: 1.7rem;
            }

            .wf-modal-description {
                font-size: 1.05rem;
            }
        }

        @media (max-width: 640px){
            #warisan{
                --wf-card-w: 180px;
                --wf-card-h: 270px;
                --wf-gap-x: 35px;
            }

            #warisan .wf-category-title {
                font-size: 1.4rem;
                padding: 0 0.5rem;
            }

            #warisan .wf-category-title::before,
            #warisan .wf-category-title::after {
                display: none;
            }

            #warisan .wf-mobile-btn {
                width: 55px;
                height: 55px;
            }

            #warisan .wf-mobile-btn svg {
                width: 24px;
                height: 24px;
            }

            .wf-modal-info {
                padding: 1.5rem;
            }

            .wf-modal-title {
                font-size: 1.5rem;
            }

            .wf-modal-description {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px){
            #warisan{
                --wf-card-w: 160px;
                --wf-card-h: 240px;
                --wf-gap-x: 30px;
            }

            #warisan .wf-category-title {
                font-size: 1.3rem;
            }

            #warisan .wf-mobile-nav-container {
                gap: 1.5rem;
            }

            #warisan .wf-mobile-btn {
                width: 50px;
                height: 50px;
            }

            #warisan .wf-mobile-btn svg {
                width: 22px;
                height: 22px;
            }

            .wf-modal-info {
                padding: 1.2rem;
            }

            .wf-modal-title {
                font-size: 1.4rem;
            }

            .wf-modal-description {
                font-size: 0.95rem;
                line-height: 1.7;
            }

            .wf-modal-meta {
                padding: 1rem;
                margin-bottom: 2rem;
            }
        }

        @media (max-width: 360px){
            #warisan{
                --wf-card-w: 140px;
                --wf-card-h: 220px;
                --wf-gap-x: 25px;
            }

            #warisan .wf-caption h4 {
                font-size: 0.95rem;
            }

            .wf-modal-info {
                padding: 1rem;
            }

            .wf-modal-title {
                font-size: 1.3rem;
            }
        }

        /* Fix untuk overflow di mobile */
        @media (max-width: 768px) {
            #warisan {
                padding-left: 0;
                padding-right: 0;
                margin-left: 0;
                margin-right: 0;
                width: 100vw;
                position: relative;
                left: 50%;
                right: 50%;
                margin-left: -50vw;
                margin-right: -50vw;
            }

            #warisan .wf-row {
                max-width: 100vw;
                width: 100vw;
                padding: 0 0.5rem;
            }

            #warisan .wf-category-title-wrap {
                padding: 0 1rem;
            }
        }
    </style>

    {{-- MODAL CONTAINER (hanya satu untuk semua) --}}
    <div class="wf-modal-overlay" id="wf-modal-overlay">
        <div class="wf-modal-container" id="wf-modal-container">
            <button class="wf-modal-close" id="wf-modal-close" aria-label="Tutup modal">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div class="wf-modal-content" id="wf-modal-content">
                <!-- Konten akan dimuat dinamis -->
            </div>
        </div>
    </div>

    {{-- 3 ROWS: PAKAIAN / RUMAH+TRADISI / SENJATA+MUSIK --}}
    @foreach($labels as $key => $label)
        @php
            $items = $itemsByCategory[$key] ?? collect();
            $rowId = 'wf_'.$key;
        @endphp

        <div class="wf-row" id="{{ $rowId }}" data-wf-row="{{ $key }}">
            {{-- TITLE KATEGORI DI TENGAH --}}
            <div class="wf-category-title-wrap">
                <h3 class="wf-category-title">{{ $label }}</h3>
                @if($loop->first && $heroDescription)
                    <p class="wf-category-desc">{{ $heroDescription }}</p>
                @endif
            </div>

            <div class="wf-flow">
                @if($items->count() === 0)
                    <div class="wf-empty">
                        <div class="wf-empty-box">
                            <div class="wf-empty-ico">📁</div>
                            <p class="wf-empty-title">Belum ada data untuk kategori ini</p>
                            <p class="wf-empty-desc">Admin dapat menambahkan data melalui panel admin.</p>
                        </div>
                    </div>
                @else
                    <div class="wf-viewport" data-wf-viewport>
                        {{-- DESKTOP NAV BUTTONS (kiri dan kanan) --}}
                        <button type="button" class="wf-nav wf-prev" data-wf-prev aria-label="Sebelumnya">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                            </svg>
                        </button>

                        <div class="wf-track" data-wf-track>
                            @foreach($items as $item)
                                @php
                                    $img = null;
                                    if ($item->image_path) {
                                        $img = asset('storage/'.$item->image_path);
                                    }
                                    $desc = $item->description ? Str::limit($item->description, 70) : null;
                                @endphp

                                <article
                                    class="wf-card"
                                    data-wf-card
                                    data-index="{{ $loop->index }}"
                                    data-item-id="{{ $item->id }}"
                                    data-item-title="{{ $item->title }}"
                                    data-item-description="{{ $item->description ?? '' }}"
                                    data-item-image="{{ $img ?? '' }}"
                                    data-item-category="{{ $label }}"
                                    data-item-focus="center"
                                    style="--off: 0; --abs: 0;"
                                    role="button"
                                    tabindex="0"
                                    aria-label="Lihat detail {{ $item->title }}"
                                >
                                    <div
                                        class="wf-media {{ $img ? '' : 'wf-fallback' }}"
                                        style="{{ $img ? "background-image:url('".$img."')" : '' }}"
                                        aria-label="Gambar {{ $item->title }}"
                                    ></div>

                                    <div class="wf-caption">
                                        <h4>{{ $item->title }}</h4>
                                        @if($desc)
                                            <p>{{ $desc }}</p>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <button type="button" class="wf-nav wf-next" data-wf-next aria-label="Berikutnya">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                            </svg>
                        </button>
                    </div>

                    {{-- MOBILE NAV BUTTONS (dibawah) --}}
                    <div class="wf-mobile-nav" data-wf-mobile-nav>
                        <div class="wf-mobile-nav-container">
                            <button type="button" class="wf-mobile-btn wf-mobile-prev" data-wf-mobile-prev aria-label="Sebelumnya">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                                </svg>
                            </button>
                            <button type="button" class="wf-mobile-btn wf-mobile-next" data-wf-mobile-next aria-label="Berikutnya">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

<script>
/**
 * COVERFLOW dengan perbaikan untuk mobile & desktop
 * - 2 tombol nav (prev/next) di desktop (kiri dan kanan)
 * - 2 tombol nav di mobile (dibawah)
 * - Tampilkan 3 kartu di mobile
 * - Fixed overflow di mobile
 * - MODAL DETAIL ketika card diklik
 * - Support tema light/dark
 */
(function () {
    const rows = document.querySelectorAll('#warisan [data-wf-row]');
    const modalOverlay = document.getElementById('wf-modal-overlay');
    const modalContent = document.getElementById('wf-modal-content');
    const modalClose = document.getElementById('wf-modal-close');

    if (!rows.length) return;

    // Decode HTML entities (mis. &amp;)
    function decodeHtmlEntities(str) {
        if (!str) return '';
        const txt = document.createElement('textarea');
        txt.innerHTML = str;
        return txt.value;
    }

    // Fungsi untuk menampilkan modal detail
    function showDetailModal(card) {
        if (!card || !modalOverlay || !modalContent) return;

        const itemData = {
            title: card.dataset.itemTitle || 'Judul tidak tersedia',
            description: card.dataset.itemDescription || 'Deskripsi tidak tersedia',
            image: decodeHtmlEntities(card.dataset.itemImage || ''),
            category: card.dataset.itemCategory || 'Kategori'
        };

        // fokus crop modal image: top/center/bottom (default center)
        const focus = (card.dataset.itemFocus || 'center').toLowerCase();
        const focusClass = (focus === 'top') ? 'pos-top' : (focus === 'bottom' ? 'pos-bottom' : 'pos-center');

        let imageUrl = (itemData.image || '').trim();
        let imageHTML = '';

        if (imageUrl !== '') {
            // Pakai URL apa adanya (asset() dari Blade sudah final). Hanya rapikan spasi.
            imageHTML = `
                <div class="wf-modal-image ${focusClass}"
                     style="background-image:url('${imageUrl}')"
                     data-image-url="${imageUrl}">
                </div>
            `;
        } else {
            imageHTML = `
                <div class="wf-modal-image wf-modal-image-fallback">
                    <span>🏛️</span>
                </div>
            `;
        }

        const modalHTML = `
            ${imageHTML}
            <div class="wf-modal-info">
                <div class="wf-modal-meta">
                    <div class="wf-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <span>${itemData.category}</span>
                    </div>
                </div>
                <h2 class="wf-modal-title">${itemData.title}</h2>
                <div class="wf-modal-description">
                    ${String(itemData.description).replace(/\n/g, '<br>')}
                </div>
            </div>
        `;

        modalContent.innerHTML = modalHTML;

        modalOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Preload gambar (opsional) supaya bisa fallback kalau error
        if (imageUrl !== '') {
            const img = new Image();
            img.onload = function(){ /* ok */ };
            img.onerror = function(){
                const imageDiv = modalContent.querySelector('.wf-modal-image');
                if (imageDiv) {
                    imageDiv.classList.add('wf-modal-image-fallback');
                    imageDiv.innerHTML = '<span>🏛️</span>';
                    imageDiv.style.backgroundImage = 'none';
                }
            };
            img.src = imageUrl;
        }

        setTimeout(() => {
            if (modalClose) modalClose.focus();
        }, 100);
    }

    let lastClickedCard = null;

    function closeDetailModal() {
        if (!modalOverlay) return;
        modalOverlay.classList.remove('active');
        document.body.style.overflow = '';

        if (lastClickedCard) {
            setTimeout(() => lastClickedCard.focus(), 100);
        }
    }

    if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) closeDetailModal();
        });
    }

    if (modalClose) modalClose.addEventListener('click', closeDetailModal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modalOverlay && modalOverlay.classList.contains('active')) {
            closeDetailModal();
        }
    });

    function setupRow(row) {
        const track = row.querySelector('[data-wf-track]');
        const cards = row.querySelectorAll('[data-wf-card]');
        const prevBtn = row.querySelector('[data-wf-prev]');
        const nextBtn = row.querySelector('[data-wf-next]');
        const mobilePrevBtn = row.querySelector('[data-wf-mobile-prev]');
        const mobileNextBtn = row.querySelector('[data-wf-mobile-next]');
        const viewport = row.querySelector('[data-wf-viewport]');
        if (!track || !cards.length) return;

        let active = Math.min(Math.floor(cards.length / 2), cards.length - 1);
        let isAnimating = false;
        let isMobile = window.innerWidth <= 768;

        function updateMobileState() {
            isMobile = window.innerWidth <= 768;
        }

        function clampIndex(i) {
            if (i < 0) return cards.length - 1;
            if (i >= cards.length) return 0;
            return i;
        }

        function render() {
            if (isAnimating) return;
            isAnimating = true;

            cards.forEach((card, idx) => {
                let off = idx - active;

                const wrapDistance = Math.abs(off);
                const wrapLeft = Math.abs(off + cards.length);
                const wrapRight = Math.abs(off - cards.length);

                if (wrapLeft < wrapDistance) off += cards.length;
                if (wrapRight < wrapDistance) off -= cards.length;

                const abs = Math.abs(off);

                card.style.setProperty('--off', off);
                card.style.setProperty('--abs', abs);
                card.dataset.active = (idx === active) ? "1" : "0";

                card.style.zIndex = String(100 - abs * 10);

                card.tabIndex = (idx === active) ? 0 : -1;

                if (isMobile && abs > 2) {
                    card.style.opacity = '0';
                    card.style.pointerEvents = 'none';
                } else {
                    card.style.opacity = '';
                    card.style.pointerEvents = '';
                }
            });

            setTimeout(() => { isAnimating = false; }, 400);
        }

        function go(delta) {
            if (isAnimating) return;
            active = clampIndex(active + delta);
            render();

            const activeCard = cards[active];
            if (activeCard) {
                setTimeout(() => {
                    activeCard.focus({ preventScroll: true });
                }, 50);
            }
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                go(-1);
            });
            prevBtn.addEventListener('pointerdown', (e) => e.stopPropagation());
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                go(1);
            });
            nextBtn.addEventListener('pointerdown', (e) => e.stopPropagation());
        }

        if (mobilePrevBtn) {
            mobilePrevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                go(-1);
            });
        }

        if (mobileNextBtn) {
            mobileNextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                go(1);
            });
        }

        cards.forEach((card, idx) => {
            card.addEventListener('click', (e) => {
                e.preventDefault();
                lastClickedCard = card;

                if (idx !== active) {
                    active = idx;
                    render();
                    setTimeout(() => showDetailModal(card), 200);
                } else {
                    showDetailModal(card);
                }
            });

            card.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    lastClickedCard = card;

                    if (idx !== active) {
                        active = idx;
                        render();
                        setTimeout(() => showDetailModal(card), 200);
                    } else {
                        showDetailModal(card);
                    }
                }
            });
        });

        row.addEventListener('keydown', (e) => {
            if (modalOverlay && modalOverlay.classList.contains('active')) return;

            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                go(-1);
            }
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                go(1);
            }
        });

        let swipeStartX = 0;
        let isSwiping = false;

        function handleTouchStart(e) {
            if (!e.touches || (modalOverlay && modalOverlay.classList.contains('active'))) return;
            swipeStartX = e.touches[0].clientX;
            isSwiping = true;
        }

        function handleTouchMove(e) {
            if (!isSwiping) return;
            e.preventDefault();
        }

        function handleTouchEnd(e) {
            if (!isSwiping) return;

            const endX = e.changedTouches[0].clientX;
            const deltaX = endX - swipeStartX;

            if (Math.abs(deltaX) > 50) {
                if (deltaX > 0) go(-1);
                else go(1);
            }

            isSwiping = false;
        }

        if (viewport) {
            viewport.addEventListener('touchstart', handleTouchStart, { passive: true });
            viewport.addEventListener('touchmove', handleTouchMove, { passive: false });
            viewport.addEventListener('touchend', handleTouchEnd, { passive: true });
        }

        function handleResize() {
            updateMobileState();
            render();
        }

        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(handleResize, 150);
        });

        updateMobileState();
        setTimeout(() => {
            render();
            if (cards[active]) cards[active].tabIndex = 0;
        }, 100);
    }

    rows.forEach(row => setupRow(row));

    function fixMobileOverflow() {
        const warisanSection = document.getElementById('warisan');
        if (!warisanSection) return;

        if (window.innerWidth <= 768) {
            document.body.style.overflowX = 'hidden';
            warisanSection.style.width = '100vw';
            warisanSection.style.maxWidth = '100vw';
        } else {
            document.body.style.overflowX = '';
            warisanSection.style.width = '';
            warisanSection.style.maxWidth = '';
        }
    }

    window.addEventListener('load', fixMobileOverflow);
    window.addEventListener('resize', fixMobileOverflow);
    setTimeout(fixMobileOverflow, 100);
})();
</script>

</section>
