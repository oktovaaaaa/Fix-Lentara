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
        ========================================================= */

        @property --neon-orange-angle {
            syntax: "<angle>";
            inherits: false;
            initial-value: 0deg;
        }

        #warisan{
            /* Warna utama */
            --wf-bg: var(--bg-body);
            --wf-txt: var(--txt-body);
            --wf-muted: var(--muted);
            --wf-line: var(--line);

            /* Warna neon orange */
            --neon-primary: #f97316;
            --neon-secondary: #fb923c;
            --neon-glow: rgba(249, 115, 22, 0.7);

            /* coverflow sizing - OPTIMIZED FOR RESPONSIVE */
            --wf-card-w: 280px;
            --wf-card-h: 380px;
            --wf-gap-x: 180px;
            --wf-tilt: 20deg;
            --wf-depth: 140px;
            --wf-scale-step: .15;
            --wf-blur-step: 0.8px;

            --wf-radius: 24px;
            --wf-shadow: 0 25px 50px rgba(0,0,0,.25);
            --wf-shadow-active: 0 0 40px rgba(249, 115, 22, 0.4);

            /* Mobile visibility settings */
            --visible-cards: 3; /* Tampilkan 3 kartu di mobile */
        }

        /* Animation untuk efek neon */
        @keyframes orange-neon-spin {
            0% { --neon-orange-angle: 0deg; }
            100% { --neon-orange-angle: 360deg; }
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

        /* Garis dekoratif */
        #warisan .wf-category-title::before,
        #warisan .wf-category-title::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 80px;
            height: 2px;
            background: linear-gradient(90deg,
                transparent,
                var(--neon-primary),
                var(--neon-secondary),
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

        /* ================= VIEWPORT - IMPROVED ================= */
        #warisan .wf-viewport{
            position: relative;
            height: calc(var(--wf-card-h) + 40px); /* Space for mobile buttons */
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1200px;
            overflow: visible;
            width: 100%;
        }

        /* ================= TRACK - IMPROVED ================= */
        #warisan .wf-track{
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Kartu dengan posisi yang lebih terpusat */
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
            background: #0a0f1a;
            cursor: pointer;
            user-select: none;
            transition: all 0.5s cubic-bezier(0.2, 0.8, 0.2, 1.2);

            /* Transform untuk distribusi yang lebih baik */
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
                var(--neon-primary),
                var(--neon-secondary),
                var(--neon-primary),
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
            opacity: 0.8;
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
            z-index: 1;
        }

        /* Overlay */
        #warisan .wf-media::after{
            content:"";
            position:absolute;
            inset:0;
            background:
                linear-gradient(to bottom,
                    transparent 0%,
                    rgba(0,0,0,0.1) 30%,
                    rgba(0,0,0,0.6) 70%,
                    rgba(0,0,0,0.8) 100%),
                linear-gradient(to right,
                    rgba(249, 115, 22, 0.05),
                    transparent 30%,
                    transparent 70%,
                    rgba(251, 146, 60, 0.05));
        }

        /* Caption */
        #warisan .wf-caption{
            position:absolute;
            left: 16px;
            right: 16px;
            bottom: 16px;
            padding: 16px;
            border-radius: 16px;
            background: rgba(10, 15, 26, 0.85);
            border: 1px solid rgba(249, 115, 22, 0.3);
            backdrop-filter: blur(15px);
            z-index: 2;
            transform: translateY(5px);
            transition: all 0.3s ease;
        }

        #warisan .wf-card[data-active="1"] .wf-caption {
            border-color: var(--neon-primary);
            box-shadow: 0 0 20px rgba(249, 115, 22, 0.3);
        }

        #warisan .wf-caption h4{
            margin:0 0 6px 0;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        #warisan .wf-caption p{
            margin: 0;
            color: rgba(255,255,255,0.85);
            font-size: .85rem;
            line-height: 1.5;
        }

        /* Fallback gradient */
        #warisan .wf-fallback{
            background:
                radial-gradient(70% 60% at 30% 20%,
                    rgba(249, 115, 22, 0.4),
                    transparent 55%),
                radial-gradient(70% 60% at 70% 30%,
                    rgba(251, 146, 60, 0.3),
                    transparent 55%),
                linear-gradient(135deg, #0a0f1a, #111827, #0a0f1a);
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
            border: 2px solid rgba(249, 115, 22, 0.5);
            background: rgba(10, 15, 26, 0.9);
            color: var(--neon-primary);
            display:flex;
            align-items:center;
            justify-content:center;
            box-shadow:
                0 15px 35px rgba(0,0,0,0.3),
                0 0 25px rgba(249, 115, 22, 0.4);
            backdrop-filter: blur(15px);
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
                var(--neon-primary),
                var(--neon-secondary),
                var(--neon-primary),
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
            border-color: rgba(249,115,22,0.8);
            background: rgba(249,115,22,0.15);
            box-shadow:
                0 20px 40px rgba(0,0,0,0.4),
                0 0 40px rgba(249, 115, 22, 0.6);
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
            filter: drop-shadow(0 0 8px rgba(249, 115, 22, 0.8));
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
            border: 2px solid rgba(249, 115, 22, 0.6);
            background: rgba(10, 15, 26, 0.9);
            color: var(--neon-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                0 10px 25px rgba(0,0,0,0.3),
                0 0 20px rgba(249, 115, 22, 0.4);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        #warisan .wf-mobile-btn:hover {
            transform: scale(1.1);
            border-color: rgba(249,115,22,0.9);
            background: rgba(249,115,22,0.2);
            box-shadow:
                0 15px 30px rgba(0,0,0,0.4),
                0 0 30px rgba(249, 115, 22, 0.6);
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
            border: 2px dashed rgba(249, 115, 22, 0.4);
            background: rgba(249, 115, 22, 0.08);
            backdrop-filter: blur(10px);
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
                rgba(249, 115, 22, 0.3),
                transparent
            );
            -webkit-mask:
                linear-gradient(#000 0 0) content-box,
                linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            filter: blur(8px);
            opacity: 0.6;
            animation: orange-neon-spin 10s linear infinite;
        }

        #warisan .wf-empty-ico{
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--neon-primary);
            filter: drop-shadow(0 0 15px rgba(249, 115, 22, 0.5));
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
                --wf-gap-x: 40px; /* DIKURANGI DRASTIS untuk 3 kartu */
                --wf-tilt: 10deg; /* Kurangi tilt di mobile */
                --wf-depth: 60px;
                --wf-scale-step: .08; /* Kurangi scale difference */
                --wf-blur-step: 0.3px; /* Kurangi blur */
            }

            #warisan .wf-viewport {
                height: calc(var(--wf-card-h) + 60px); /* Extra space for mobile */
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

            /* Adjust card opacity untuk visibility yang lebih baik */
            #warisan .wf-card {
                opacity: calc(0.9 - (var(--abs) * .2));
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

            #warisan .wf-caption {
                padding: 12px;
                left: 10px;
                right: 10px;
                bottom: 10px;
            }

            #warisan .wf-caption h4 {
                font-size: 1rem;
            }

            #warisan .wf-caption p {
                font-size: 0.75rem;
            }

            #warisan .wf-mobile-btn {
                width: 55px;
                height: 55px;
            }

            #warisan .wf-mobile-btn svg {
                width: 24px;
                height: 24px;
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
        }

        @media (max-width: 360px){
            #warisan{
                --wf-card-w: 140px;
                --wf-card-h: 220px;
                --wf-gap-x: 25px;
            }

            #warisan .wf-caption h4 {
                font-size: 0.9rem;
            }

            #warisan .wf-caption p {
                font-size: 0.7rem;
                line-height: 1.4;
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
                                    $img = $item->image_path ? asset('storage/'.$item->image_path) : null;
                                    $desc = $item->description ? Str::limit($item->description, 70) : null;
                                @endphp

                                <article
                                    class="wf-card"
                                    data-wf-card
                                    data-index="{{ $loop->index }}"
                                    style="--off: 0; --abs: 0;"
                                    role="button"
                                    tabindex="0"
                                    aria-label="Lihat {{ $item->title }}"
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
 */
(function () {
    const rows = document.querySelectorAll('#warisan [data-wf-row]');
    if (!rows.length) return;

    function setupRow(row) {
        const track = row.querySelector('[data-wf-track]');
        const cards = row.querySelectorAll('[data-wf-card]');
        const prevBtn = row.querySelector('[data-wf-prev]');
        const nextBtn = row.querySelector('[data-wf-next]');
        const mobilePrevBtn = row.querySelector('[data-wf-mobile-prev]');
        const mobileNextBtn = row.querySelector('[data-wf-mobile-next]');
        const viewport = row.querySelector('[data-wf-viewport]');
        if (!track || !cards.length) return;

        // Start from center
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

                // Cari jalur terpendek dengan wrap
                const wrapDistance = Math.abs(off);
                const wrapLeft = Math.abs(off + cards.length);
                const wrapRight = Math.abs(off - cards.length);

                if (wrapLeft < wrapDistance) off += cards.length;
                if (wrapRight < wrapDistance) off -= cards.length;

                const abs = Math.abs(off);

                card.style.setProperty('--off', off);
                card.style.setProperty('--abs', abs);
                card.dataset.active = (idx === active) ? "1" : "0";

                // Atur z-index untuk stacking
                card.style.zIndex = String(100 - abs * 10);

                // Tabindex hanya untuk kartu aktif
                card.tabIndex = (idx === active) ? 0 : -1;

                // Di mobile, sembunyikan kartu yang terlalu jauh
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

            // Fokus ke kartu aktif
            const activeCard = cards[active];
            if (activeCard) {
                setTimeout(() => {
                    activeCard.focus({ preventScroll: true });
                }, 50);
            }
        }

        /* =========================
           DESKTOP NAVIGATION
        ========================= */
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

        /* =========================
           MOBILE NAVIGATION (dibawah)
        ========================= */
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

        /* =========================
           CLICK CARD TO FOCUS
        ========================= */
        cards.forEach((card, idx) => {
            card.addEventListener('click', (e) => {
                e.preventDefault();
                if (idx !== active) {
                    active = idx;
                    render();
                }
            });

            card.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    if (idx !== active) {
                        active = idx;
                        render();
                    }
                }
            });
        });

        /* =========================
           KEYBOARD NAVIGATION
        ========================= */
        row.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                go(-1);
            }
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                go(1);
            }
        });

        /* =========================
           SWIPE SUPPORT (MOBILE)
        ========================= */
        let swipeStartX = 0;
        let isSwiping = false;

        function handleTouchStart(e) {
            if (!e.touches) return;
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

            // Threshold swipe
            if (Math.abs(deltaX) > 50) {
                if (deltaX > 0) {
                    go(-1); // Swipe kanan -> prev
                } else {
                    go(1); // Swipe kiri -> next
                }
            }

            isSwiping = false;
        }

        if (viewport) {
            viewport.addEventListener('touchstart', handleTouchStart, { passive: true });
            viewport.addEventListener('touchmove', handleTouchMove, { passive: false });
            viewport.addEventListener('touchend', handleTouchEnd, { passive: true });
        }

        /* =========================
           RESIZE HANDLER
        ========================= */
        function handleResize() {
            updateMobileState();
            render(); // Re-render untuk menyesuaikan dengan ukuran layar
        }

        // Debounce resize untuk performa
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(handleResize, 150);
        });

        /* =========================
           INITIAL SETUP
        ========================= */
        updateMobileState();

        // Initial render dengan timeout untuk memastikan DOM siap
        setTimeout(() => {
            render();
            // Fokus ke kartu tengah
            if (cards[active]) {
                cards[active].tabIndex = 0;
            }
        }, 100);
    }

    // Setup semua rows
    rows.forEach(row => {
        setupRow(row);
    });

    // Global keyboard handler
    document.addEventListener('keydown', (e) => {
        const activeRow = document.activeElement?.closest('[data-wf-row]');
        if (!activeRow) return;

        if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
            e.preventDefault();
        }
    });

    // Fix untuk overflow di mobile
    function fixMobileOverflow() {
        const warisanSection = document.getElementById('warisan');
        if (!warisanSection) return;

        if (window.innerWidth <= 768) {
            // Pastikan section tidak menyebabkan horizontal scroll
            document.body.style.overflowX = 'hidden';
            warisanSection.style.width = '100vw';
            warisanSection.style.maxWidth = '100vw';
        } else {
            document.body.style.overflowX = '';
            warisanSection.style.width = '';
            warisanSection.style.maxWidth = '';
        }
    }

    // Jalankan saat load dan resize
    window.addEventListener('load', fixMobileOverflow);
    window.addEventListener('resize', fixMobileOverflow);

    // Initial call
    setTimeout(fixMobileOverflow, 100);
})();
</script>

</section>

