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
@endphp

<section id="warisan" class="space-y-16 py-10">

    <style>
        /* =========================================================
           NEON ORANGE COVERFLOW - CLEAN HORIZONTAL LAYOUT
           Seperti gambar kedua dengan efek neon dari about section
        ========================================================= */

        @property --neon-orange-angle {
            syntax: "<angle>";
            inherits: false;
            initial-value: 0deg;
        }

        #warisan{
            /* Warna utama mengikuti theme */
            --wf-bg: var(--bg-body);
            --wf-txt: var(--txt-body);
            --wf-muted: var(--muted);
            --wf-line: var(--line);

            /* Warna neon orange dari about section */
            --neon-primary: #f97316;
            --neon-secondary: #fb923c;
            --neon-glow: rgba(249, 115, 22, 0.7);

            /* coverflow sizing */
            --wf-card-w: 280px;
            --wf-card-h: 380px;
            --wf-gap-x: 220px;     /* jarak antar posisi kartu */
            --wf-tilt: 20deg;      /* kemiringan samping */
            --wf-depth: 160px;     /* efek 3D */
            --wf-scale-step: .15;  /* scaling per jarak */
            --wf-blur-step: 0.8px; /* blur halus untuk samping */

            --wf-radius: 24px;
            --wf-shadow: 0 25px 50px rgba(0,0,0,.25);
            --wf-shadow-active: 0 0 40px rgba(249, 115, 22, 0.4);
        }

        /* ================= SECTION TITLE ================= */
        #warisan .wf-title-wrap{
            text-align:center;
            position:relative;
            padding: 2rem 0 1rem;
        }
        #warisan .wf-title{
            font-size: 3rem;
            font-weight: 900;
            letter-spacing: -0.03em;
            color: var(--wf-txt);
            margin: 0;
            line-height: 1;
            position: relative;
            display: inline-block;
            padding-bottom: 15px;
        }

        /* Neon underline effect */
        #warisan .wf-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 180px;
            height: 4px;
            background: linear-gradient(90deg,
                transparent,
                var(--neon-primary),
                var(--neon-secondary),
                var(--neon-primary),
                transparent
            );
            border-radius: 2px;
            animation: neonPulse 3s ease-in-out infinite;
        }

        @keyframes neonPulse {
            0%, 100% { opacity: 0.8; box-shadow: 0 0 20px var(--neon-glow); }
            50% { opacity: 1; box-shadow: 0 0 35px var(--neon-glow), 0 0 50px rgba(249, 115, 22, 0.3); }
        }

        #warisan .wf-desc{
            margin: 1.5rem auto 0;
            max-width: 48rem;
            color: var(--wf-muted);
            font-size: 1.1rem;
            line-height: 1.7;
            padding: 0 1rem;
        }

        /* ================= ROW CONTAINER ================= */
        #warisan .wf-row{
            width: 100%;
            margin: 3rem auto;
            padding: 0 2rem;
            position: relative;
        }

        /* Row header dengan efek neon */
        #warisan .wf-row-head{
            display:flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 2.5rem;
            padding: 1rem 1.5rem;
            border-radius: 16px;
            background: rgba(249, 115, 22, 0.05);
            border: 1px solid rgba(249, 115, 22, 0.15);
            position: relative;
            overflow: hidden;
        }

        #warisan .wf-row-head::before {
            content: "";
            position: absolute;
            inset: -2px;
            border-radius: 18px;
            padding: 2px;
            pointer-events: none;
            background: conic-gradient(
                from var(--neon-orange-angle),
                rgba(249, 115, 22, 0) 0deg,
                rgba(249, 115, 22, 0.3) 45deg,
                #f97316 90deg,
                #fb923c 180deg,
                #f97316 270deg,
                rgba(249, 115, 22, 0.3) 315deg,
                rgba(249, 115, 22, 0) 360deg
            );
            -webkit-mask:
                linear-gradient(#000 0 0) content-box,
                linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            filter: blur(6px);
            opacity: 0.6;
            animation: orange-neon-spin 8s linear infinite;
        }

        @keyframes orange-neon-spin {
            to { --neon-orange-angle: 360deg; }
        }

        #warisan .wf-row-title{
            margin:0;
            font-size: 1.4rem;
            font-weight: 900;
            color: var(--wf-txt);
            display:flex;
            align-items:center;
            gap: .8rem;
        }

        #warisan .wf-row-pill{
            display:inline-flex;
            align-items:center;
            gap: .5rem;
            padding: .5rem 1rem;
            border-radius: 999px;
            border: 1px solid rgba(249, 115, 22, 0.3);
            background: rgba(249, 115, 22, 0.1);
            color: var(--neon-primary);
            font-size: .85rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            white-space: nowrap;
            backdrop-filter: blur(10px);
        }

        #warisan .wf-dot{
            width:10px;
            height:10px;
            border-radius:999px;
            background: linear-gradient(135deg, var(--neon-primary), var(--neon-secondary));
            box-shadow: 0 0 15px rgba(249,115,22,.8);
            animation: pulseDot 2s ease-in-out infinite;
        }

        @keyframes pulseDot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.8; }
        }

        /* ================= COVERFLOW CONTAINER ================= */
        #warisan .wf-flow{
            position: relative;
            border-radius: 20px;
            padding: 0;
            background: transparent;
            overflow: visible;
        }

        /* ================= VIEWPORT ================= */
        #warisan .wf-viewport{
            position: relative;
            height: calc(var(--wf-card-h) + 40px);
            display:flex;
            align-items:center;
            justify-content:center;
            perspective: 1200px;
            overflow: visible;
        }

        /* ================= TRACK & CARD POSITIONS ================= */
        #warisan .wf-track{
            position: relative;
            width: 100%;
            height: 100%;
        }

        /* Kartu dengan efek neon border */
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

            /* base transform (diupdate via --off, --abs) */
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

        /* Efek neon border untuk kartu aktif */
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
            position:absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            z-index: 1;
        }

        /* Overlay dengan gradien neon */
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

        /* Title overlay dengan efek neon */
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

        /* fallback gradient with neon touch */
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

        /* ================= NAV BUTTONS WITH NEON ================= */
        #warisan .wf-nav{
            position:absolute;
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

        /* ================= RESPONSIVE ================= */
        @media (max-width: 1200px){
            #warisan{
                --wf-card-w: 250px;
                --wf-card-h: 350px;
                --wf-gap-x: 190px;
            }
            #warisan .wf-nav {
                width: 46px;
                height: 46px;
            }
            #warisan .wf-prev { left: -15px; }
            #warisan .wf-next { right: -15px; }
        }

        @media (max-width: 992px){
            #warisan{
                --wf-card-w: 220px;
                --wf-card-h: 320px;
                --wf-gap-x: 165px;
            }
            #warisan .wf-title {
                font-size: 2.5rem;
            }
            #warisan .wf-row {
                padding: 0 1.5rem;
            }
        }

        @media (max-width: 768px){
            #warisan{
                --wf-card-w: 200px;
                --wf-card-h: 300px;
                --wf-gap-x: 150px;
                --wf-tilt: 15deg;
                --wf-depth: 120px;
            }
            #warisan .wf-title {
                font-size: 2.2rem;
            }
            #warisan .wf-row-head {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
            #warisan .wf-nav {
                width: 42px;
                height: 42px;
            }
            #warisan .wf-nav svg {
                width: 20px;
                height: 20px;
            }
        }

        @media (max-width: 640px){
            #warisan{
                --wf-card-w: 180px;
                --wf-card-h: 270px;
                --wf-gap-x: 130px;
            }
            #warisan .wf-title {
                font-size: 2rem;
            }
            #warisan .wf-row {
                padding: 0 1rem;
            }
            #warisan .wf-prev { left: -10px; }
            #warisan .wf-next { right: -10px; }
        }

        @media (max-width: 480px){
            #warisan{
                --wf-card-w: 160px;
                --wf-card-h: 240px;
                --wf-gap-x: 120px;
            }
            #warisan .wf-title {
                font-size: 1.8rem;
            }
            #warisan .wf-caption {
                padding: 12px;
                left: 12px;
                right: 12px;
                bottom: 12px;
            }
            #warisan .wf-caption h4 {
                font-size: 1rem;
            }
            #warisan .wf-caption p {
                font-size: 0.75rem;
            }
        }
    </style>

    {{-- TITLE SECTION --}}
    <div class="wf-title-wrap">
        <h2 class="wf-title">Warisan Budaya</h2>
        <p class="wf-desc">
            Jelajahi kekayaan budaya {{ $heroTitle }} melalui pakaian tradisional, rumah adat, dan alat musik khas.
        </p>
    </div>

    {{-- 3 ROWS: PAKAIAN / RUMAH+TRADISI / SENJATA+MUSIK --}}
    @foreach($labels as $key => $label)
        @php
            $items = $itemsByCategory[$key] ?? collect();
            $rowId = 'wf_'.$key;
        @endphp

        <div class="wf-row" id="{{ $rowId }}" data-wf-row="{{ $key }}">
            <div class="wf-row-head">
                <h3 class="wf-row-title">
                    <span class="wf-dot"></span>
                    {{ $label }}
                </h3>
                <div class="wf-row-pill" aria-hidden="true">
                    <span>Mode Coverflow</span>
                </div>
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
                @endif
            </div>
        </div>
    @endforeach

    <script>
        /**
         * COVERFLOW dengan efek neon orange
         * - prev/next buttons dengan animasi
         * - click card to focus
         * - keyboard navigation
         * - swipe support
         */
        (function () {
            const rows = document.querySelectorAll('#warisan [data-wf-row]');
            if (!rows.length) return;

            function setupRow(row) {
                const track = row.querySelector('[data-wf-track]');
                const cards = row.querySelectorAll('[data-wf-card]');
                const prevBtn = row.querySelector('[data-wf-prev]');
                const nextBtn = row.querySelector('[data-wf-next]');
                if (!track || !cards.length) return;

                let active = Math.floor(cards.length / 2);
                let isAnimating = false;

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

                        // Cari jalur terpendek untuk animasi natural
                        const alt1 = off + cards.length;
                        const alt2 = off - cards.length;
                        if (Math.abs(alt1) < Math.abs(off)) off = alt1;
                        if (Math.abs(alt2) < Math.abs(off)) off = alt2;

                        const abs = Math.abs(off);

                        card.style.setProperty('--off', off);
                        card.style.setProperty('--abs', abs);
                        card.dataset.active = (idx === active) ? "1" : "0";
                        card.style.zIndex = String(100 - abs);

                        // Tambah tabindex hanya untuk kartu aktif
                        card.tabIndex = (idx === active) ? 0 : -1;
                    });

                    // Reset animation flag setelah delay
                    setTimeout(() => {
                        isAnimating = false;
                    }, 450);
                }

                function go(delta) {
                    active = clampIndex(active + delta);
                    render();

                    // Fokus ke kartu yang aktif
                    const activeCard = cards[active];
                    if (activeCard) {
                        activeCard.focus();
                    }
                }

                // Event listeners untuk tombol navigasi
                prevBtn && prevBtn.addEventListener('click', () => go(-1));
                nextBtn && nextBtn.addEventListener('click', () => go(1));

                // Click card untuk fokus
                cards.forEach((card, idx) => {
                    card.addEventListener('click', () => {
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

                // Keyboard navigation untuk row
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

                // Swipe support untuk mobile
                let isDown = false;
                let startX = 0;
                let lastX = 0;

                const viewport = row.querySelector('[data-wf-viewport]');
                if (viewport) {
                    viewport.addEventListener('pointerdown', (e) => {
                        isDown = true;
                        startX = e.clientX;
                        lastX = e.clientX;
                        viewport.setPointerCapture(e.pointerId);
                    });

                    viewport.addEventListener('pointermove', (e) => {
                        if (!isDown) return;
                        lastX = e.clientX;
                    });

                    viewport.addEventListener('pointerup', (e) => {
                        if (!isDown) return;
                        isDown = false;
                        const dx = lastX - startX;

                        // Threshold untuk swipe
                        if (Math.abs(dx) > 40) {
                            if (dx > 0) go(-1);
                            else go(1);
                        }
                    });

                    viewport.addEventListener('pointercancel', () => {
                        isDown = false;
                    });
                }

                // Initial render
                render();

                // Fokus ke kartu tengah saat pertama kali
                if (cards[active]) {
                    cards[active].tabIndex = 0;
                }
            }

            // Setup semua rows
            rows.forEach(setupRow);

            // Tambah event listener untuk tombol keyboard global
            document.addEventListener('keydown', (e) => {
                // Hanya handle jika ada row yang sedang aktif
                if (document.activeElement.closest('#warisan [data-wf-row]')) {
                    const row = document.activeElement.closest('[data-wf-row]');
                    if (row) {
                        if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                            e.preventDefault();
                        }
                    }
                }
            });
        })();
    </script>
</section>
