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

    {{-- ================= SECTION TITLE (DARI DATABASE) ================= --}}
    <h2 class="neon-title">{{ $heroTitle }}</h2>
    <div class="title-decoration"></div>

    @if($heroDescription)
        <p class="wf-hero-desc">{{ $heroDescription }}</p>
    @endif

    <style>
        /* =========================================================
           WARISAN SLIDER (DESKTOP 3, MOBILE 1) + AUTO SHIFT 1/3s
           - Card: pure foto (tanpa glass/background)
           - Overlay orange: FIXED #f97316 (tidak berubah light/dark)
           - Theme mengikuti global vars: --bg-body, --txt-body, --card, --line, --muted
           - Empty category: tidak dirender (handled di blade)
           - Modal: selaras + navbar ditimpa (non-interaktif saat modal)
           - Reveal animation saat masuk viewport (atas/bawah)
           - ✅ NEW: lokasi + url detail opsional
        ========================================================= */

        #warisan{
            --wf-bg: var(--bg-body);
            --wf-txt: var(--txt-body);
            --wf-muted: var(--muted);
            --wf-line: var(--line);
            --wf-card-radius: 22px;

            /* ACCENT ORANGE (TETAP) */
            --wf-accent: #f97316;

            /* sizes */
            --wf-gap: 20px;
            --wf-card-h: 430px;

            /* nav */
            --wf-nav-size: 52px;
        }

        #warisan .wf-hero-desc{
            max-width: 58rem;
            margin: 12px auto 0;
            padding: 0 1rem;
            text-align: center;
            color: var(--wf-muted);
            line-height: 1.7;
            font-size: 1.05rem;
        }

        /* ================= KATEGORI TITLE ================= */
        #warisan .wf-category-title-wrap {
            text-align: center;
            margin: 2.2rem 0 1.2rem;
            position: relative;
            padding: 0 1rem;
        }

        #warisan .wf-category-title {
            font-size: clamp(1.35rem, 2.2vw, 2rem);
            font-weight: 900;
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
            width: 88px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--wf-accent), transparent);
            transform: translateY(-50%);
            opacity: .9;
        }

        #warisan .wf-category-title::before { right: 100%; margin-right: 1rem; }
        #warisan .wf-category-title::after { left: 100%; margin-left: 1rem; }

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
            margin: 0 auto 3.2rem;
            padding: 0 2rem;
            position: relative;
        }

        /* ================= SLIDER WRAP ================= */
        #warisan .wf-flow{
            position: relative;
            width: 100%;
        }

        /* viewport = area yang terlihat */
        #warisan .wf-viewport{
            position: relative;
            width: 100%;
            overflow: hidden;
            border-radius: 26px;
            padding: 0; /* pure */
        }

        /* track = container geser */
        #warisan .wf-track{
            display: flex;
            gap: var(--wf-gap);
            align-items: stretch;
            will-change: transform;
            transform: translate3d(0,0,0);
            transition: transform .55s cubic-bezier(.2,.9,.2,1);
            padding: 6px; /* biar nav gak “kepotong” glow */
        }

        /* ================= CARD ================= */
        #warisan .wf-card{
            flex: 0 0 calc((100% - (var(--wf-gap) * 2)) / 3);
            height: var(--wf-card-h);
            border-radius: var(--wf-card-radius);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            user-select: none;
            outline: none;
            border: 1px solid color-mix(in oklab, var(--wf-line) 78%, transparent);
            box-shadow: var(--shadow);
            background: transparent; /* penting: no background */
            transform: translateY(0);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease, filter .25s ease;
        }

        #warisan .wf-card:hover{
            transform: translateY(-6px);
            border-color: color-mix(in oklab, var(--wf-accent) 55%, transparent);
            box-shadow: var(--shadow), 0 0 35px rgba(249,115,22,.22);
            filter: saturate(1.03);
        }

        #warisan .wf-card:focus-visible{
            box-shadow: 0 0 0 4px rgba(249,115,22,.18), var(--shadow);
            border-color: rgba(249,115,22,.55);
        }

        /* media = background image full */
        #warisan .wf-media{
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* fallback jika tidak ada image */
        #warisan .wf-media.wf-fallback{
            background:
                radial-gradient(80% 70% at 20% 20%, rgba(249,115,22,.35), transparent 55%),
                radial-gradient(80% 70% at 70% 30%, rgba(253,186,116,.25), transparent 55%),
                linear-gradient(135deg, var(--card), var(--bg-body), var(--card));
        }

        /* overlay orange bottom (mirip contoh merah) */
        #warisan .wf-media::after{
            content:"";
            position:absolute;
            inset:0;
            background: linear-gradient(
                to top,
                rgba(249,115,22,.96) 0%,
                rgba(249,115,22,.72) 34%,
                rgba(249,115,22,.35) 58%,
                rgba(249,115,22,.0) 78%
            );
            pointer-events:none;
        }

        /* caption text (tanpa glass) */
        #warisan .wf-caption{
            position: absolute;
            left: 18px;
            right: 18px;
            bottom: 78px;
            z-index: 2;
            color: #fff;
            text-shadow: 0 10px 25px rgba(0,0,0,.28);
        }

        #warisan .wf-caption h4{
            margin: 0 0 8px 0;
            font-size: 1.35rem;
            font-weight: 950;
            letter-spacing: -0.02em;
            line-height: 1.15;
        }

        #warisan .wf-caption p{
            margin: 0;
            font-size: .98rem;
            line-height: 1.55;
            opacity: .95;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ✅ NEW: lokasi di card (opsional) */
        #warisan .wf-loc{
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 850;
            font-size: .92rem;
            line-height: 1.2;
            color: rgba(255,255,255,.96);
            max-width: 100%;
        }

        #warisan .wf-loc svg{
            width: 16px;
            height: 16px;
            flex: 0 0 auto;
            opacity: .98;
            filter: drop-shadow(0 8px 18px rgba(0,0,0,.28));
        }

        #warisan .wf-loc span{
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100%;
        }

        /* CTA button */
        #warisan .wf-cta{
            position: absolute;
            left: 18px;
            right: 18px;
            bottom: 18px;
            z-index: 3;

            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;

            border: 0;
            width: calc(100% - 36px);
            padding: 14px 14px;
            border-radius: 16px;

            background: rgba(255,255,255,.95);
            color: var(--wf-accent);
            font-weight: 950;
            font-size: 1rem;
            cursor: pointer;

            box-shadow: 0 18px 36px rgba(0,0,0,.22);
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
            text-decoration: none;
        }

        #warisan .wf-cta:hover{
            transform: translateY(-2px);
            box-shadow: 0 22px 44px rgba(0,0,0,.28), 0 0 30px rgba(249,115,22,.18);
            filter: saturate(1.04);
        }

        #warisan .wf-cta:active{ transform: translateY(0); }

        #warisan .wf-cta svg{
            width: 18px;
            height: 18px;
            opacity: .95;
        }

        /* ================= NAV (desktop) ================= */
        #warisan .wf-nav{
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: var(--wf-nav-size);
            height: var(--wf-nav-size);
            border-radius: 999px;
            border: 2px solid rgba(249,115,22,.55);
            background: rgba(255,255,255,.92);
            color: var(--wf-accent);
            display:flex;
            align-items:center;
            justify-content:center;
            box-shadow: 0 18px 40px rgba(0,0,0,.20);
            cursor: pointer;
            z-index: 50;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
        }

        html[data-theme="dark"] #warisan .wf-nav{
            background: rgba(2,6,23,.62);
            border-color: rgba(249,115,22,.45);
            box-shadow: 0 20px 44px rgba(0,0,0,.45);
        }

        #warisan .wf-nav:hover{
            transform: translateY(-50%) scale(1.08);
            box-shadow: 0 24px 48px rgba(0,0,0,.28), 0 0 30px rgba(249,115,22,.20);
        }

        #warisan .wf-nav svg{
            width: 24px;
            height: 24px;
            stroke-width: 2.7;
        }

        #warisan .wf-prev{ left: -18px; }
        #warisan .wf-next{ right: -18px; }

        /* ================= MOBILE NAV ================= */
        #warisan .wf-mobile-nav{
            display: none;
            margin-top: 12px;
            justify-content: center;
            gap: 14px;
        }

        #warisan .wf-mobile-btn{
            width: 52px;
            height: 52px;
            border-radius: 999px;
            border: 2px solid rgba(249,115,22,.55);
            background: rgba(255,255,255,.92);
            color: var(--wf-accent);
            display:flex;
            align-items:center;
            justify-content:center;
            box-shadow: 0 16px 34px rgba(0,0,0,.18);
            cursor:pointer;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        html[data-theme="dark"] #warisan .wf-mobile-btn{
            background: rgba(2,6,23,.62);
            border-color: rgba(249,115,22,.45);
            box-shadow: 0 18px 40px rgba(0,0,0,.45);
        }

        #warisan .wf-mobile-btn:hover{ transform: scale(1.06); }

        #warisan .wf-mobile-btn svg{
            width: 22px;
            height: 22px;
            stroke-width: 2.7;
        }

        /* ================= REVEAL ANIMATION (section enter) ================= */
        #warisan .wf-reveal{
            opacity: 0;
            transform: translateY(26px);
            transition: opacity .65s ease, transform .65s cubic-bezier(.2,.9,.2,1);
        }

        /* ✅ FIX: wf-inview nempel di element yang sama */
        #warisan .wf-reveal.wf-inview{
            opacity: 1;
            transform: translateY(0);
        }

        /* ================= MODAL ================= */
        #warisan .wf-modal-overlay{
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: none;
            opacity: 0;
            transition: opacity .25s ease;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }

        #warisan .wf-modal-overlay.active{
            display: flex;
            opacity: 1;
        }

        #warisan .wf-modal-backdrop{
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,.55);
            backdrop-filter: blur(10px);
        }

        #warisan .wf-modal-container{
            position: relative;
            width: min(980px, 100%);
            max-height: min(92vh, 940px);
            border-radius: 24px;
            overflow: hidden;
            background: var(--card);
            border: 1px solid var(--line);
            box-shadow: 0 40px 120px rgba(0,0,0,.45);
            z-index: 2;
            display: flex;
            flex-direction: column;
        }

        #warisan .wf-modal-close{
            position: absolute;
            top: 14px;
            right: 14px;
            width: 46px;
            height: 46px;
            border-radius: 999px;
            border: 1px solid rgba(249,115,22,.45);
            background: rgba(255,255,255,.92);
            color: var(--wf-accent);
            cursor: pointer;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size: 18px;
            font-weight: 900;
            z-index: 10;
            transition: transform .18s ease, background .18s ease, color .18s ease, box-shadow .18s ease;
        }

        html[data-theme="dark"] #warisan .wf-modal-close{
            background: rgba(2,6,23,.72);
            border-color: rgba(249,115,22,.35);
            color: #ff8c42;
        }

        #warisan .wf-modal-close:hover{
            background: rgba(249,115,22,.95);
            color: #fff;
            transform: rotate(90deg);
            box-shadow: 0 18px 40px rgba(0,0,0,.25), 0 0 28px rgba(249,115,22,.22);
        }

        #warisan .wf-modal-grid{
            display: grid;
            grid-template-columns: 1.06fr 1fr;
            min-height: 0;
            height: 100%;
        }

        #warisan .wf-modal-image{
            position: relative;
            min-height: 320px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

#warisan .wf-modal-image::after{
    content: none !important;
}


        #warisan .wf-modal-image-fallback{
            display:flex;
            align-items:center;
            justify-content:center;
            font-size: 3.2rem;
            background:
                radial-gradient(70% 70% at 25% 20%, rgba(249,115,22,.32), transparent 60%),
                linear-gradient(135deg, var(--card), var(--bg-body));
            color: var(--wf-accent);
        }

        #warisan .wf-modal-body{
            padding: 22px 22px 18px;
            overflow: auto;
            min-height: 0;
        }

        #warisan .wf-modal-pill{
            display:inline-flex;
            align-items:center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(249,115,22,.10);
            border: 1px solid rgba(249,115,22,.20);
            color: var(--wf-txt);
            font-weight: 850;
            font-size: .9rem;
            margin-bottom: 12px;
            max-width: 100%;
        }

        #warisan .wf-modal-title{
            margin: 0;
            font-size: 1.8rem;
            line-height: 1.15;
            font-weight: 950;
            color: var(--wf-txt);
            letter-spacing: -0.02em;
        }

        #warisan .wf-modal-divider{
            width: 120px;
            height: 3px;
            margin: 12px 0 14px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--wf-accent), transparent);
            opacity: .9;
        }

        /* ✅ NEW: lokasi di modal (opsional) */
        #warisan .wf-modal-meta{
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin: 10px 0 2px;
        }

        #warisan .wf-meta-row{
            display: inline-flex;
            align-items: center;
            gap: 10px;
            max-width: 100%;
            color: color-mix(in oklab, var(--wf-txt) 86%, transparent);
            font-weight: 850;
            font-size: .95rem;
        }

        #warisan .wf-meta-row svg{
            width: 18px;
            height: 18px;
            flex: 0 0 auto;
            color: var(--wf-accent);
        }

        #warisan .wf-meta-row span{
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        #warisan .wf-modal-desc{
            margin: 0;
            color: var(--wf-muted);
            line-height: 1.75;
            font-size: 1rem;
            white-space: pre-wrap;
            word-break: break-word;
        }

        #warisan .wf-modal-actions{
            display:flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        #warisan .wf-modal-btn{
            border: 0;
            cursor: pointer;
            font-weight: 950;
            font-size: .95rem;
            color: #fff;
            padding: 11px 14px;
            border-radius: 14px;
            background: linear-gradient(135deg, #f97316, #ff8c42);
            box-shadow: 0 18px 40px rgba(0,0,0,.18);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
            text-decoration: none;
        }

        #warisan .wf-modal-btn:hover{
            transform: translateY(-2px);
            box-shadow: 0 24px 50px rgba(0,0,0,.25), 0 0 28px rgba(249,115,22,.18);
            filter: saturate(1.05);
        }

        /* ✅ NEW: tombol link detail (opsional) */
        #warisan .wf-modal-link{
            border: 0;
            cursor: pointer;
            font-weight: 950;
            font-size: .95rem;
            color: var(--wf-accent);
            padding: 11px 14px;
            border-radius: 14px;
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(249,115,22,.35);
            box-shadow: 0 18px 40px rgba(0,0,0,.14);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
            text-decoration: none;
        }

        html[data-theme="dark"] #warisan .wf-modal-link{
            background: rgba(2,6,23,.60);
            color: #ff8c42;
            border-color: rgba(249,115,22,.28);
            box-shadow: 0 18px 40px rgba(0,0,0,.35);
        }

        #warisan .wf-modal-link:hover{
            transform: translateY(-2px);
            box-shadow: 0 24px 50px rgba(0,0,0,.22), 0 0 28px rgba(249,115,22,.12);
            filter: saturate(1.03);
        }

        #warisan .wf-modal-note{
            margin-top: 14px;
            font-size: .82rem;
            color: var(--wf-muted);
        }

        /* Navbar ditimpa saat modal open (bukan hide total) */
        html.wf-warisan-modal-open #top{
            pointer-events: none !important;
            filter: blur(2px);
            opacity: .92;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 992px){
            #warisan{
                --wf-card-h: 410px;
                --wf-gap: 16px;
            }
            #warisan .wf-row{ padding: 0 1.2rem; }
            #warisan .wf-prev{ left: -10px; }
            #warisan .wf-next{ right: -10px; }
        }

        @media (max-width: 768px){
            /* MOBILE: 1 kartu */
            #warisan{
                --wf-card-h: 420px;
                --wf-gap: 14px;
            }

            #warisan .wf-card{
                flex: 0 0 100%;
            }

            /* hide desktop nav, show mobile nav */
            #warisan .wf-nav{ display: none !important; }
            #warisan .wf-mobile-nav{ display: flex; }

            /* caption: mobile lebih ringkas */
            #warisan .wf-caption{
                bottom: 74px;
                left: 16px;
                right: 16px;
            }

            #warisan .wf-caption h4{
                font-size: 1.25rem;
            }

            #warisan .wf-caption p{
                display: none !important; /* biar clean di mobile */
            }

            /* lokasi tetap tampil kalau ada, tapi ringkas */
            #warisan .wf-loc{
                font-size: .92rem;
            }

            /* CTA fix biar gak kepanjangan & aman di layar kecil */
            #warisan .wf-cta{
                left: 16px;
                right: 16px;
                width: calc(100% - 32px);
                padding: 12px 12px;
                font-size: .95rem;
                border-radius: 14px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* modal stack */
            #warisan .wf-modal-grid{
                grid-template-columns: 1fr;
            }

            #warisan .wf-modal-image{
                min-height: 260px;
            }
        }

        @media (max-width: 420px){
            #warisan .wf-category-title::before,
            #warisan .wf-category-title::after{
                display:none;
            }

            #warisan .wf-modal-title{
                font-size: 1.55rem;
            }
        }
    </style>

    {{-- ================= MODAL (1 untuk semua) ================= --}}
    <div class="wf-modal-overlay" id="wf-modal-overlay" aria-hidden="true">
        <div class="wf-modal-backdrop" data-wf-close></div>

        <div class="wf-modal-container" role="dialog" aria-modal="true" aria-labelledby="wfModalTitle">
            <button class="wf-modal-close" id="wf-modal-close" type="button" aria-label="Tutup" data-wf-close>✕</button>

            <div class="wf-modal-grid" id="wf-modal-content">
                {{-- diisi via JS --}}
            </div>
        </div>
    </div>

    {{-- ================= ROWS PER CATEGORY ================= --}}
    @foreach($labels as $key => $label)
        @php
            $items = $itemsByCategory[$key] ?? collect();
            $rowId = 'wf_'.$key;
        @endphp

        {{-- ✅ jika kosong: skip TOTAL (no empty state) --}}
        @if(!$items || $items->count() === 0)
            @continue
        @endif

        <div class="wf-row wf-reveal" id="{{ $rowId }}" data-wf-row="{{ $key }}">
            <div class="wf-category-title-wrap">
                <h3 class="wf-category-title">{{ $label }}</h3>
                @if($loop->first && $heroDescription)
                    <p class="wf-category-desc">{{ $heroDescription }}</p>
                @endif
            </div>

            <div class="wf-flow">
                <div class="wf-viewport" data-wf-viewport>
                    {{-- DESKTOP NAV --}}
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

                                $desc = $item->description ? Str::limit($item->description, 110) : null;

                                // ✅ opsional baru
                                $loc = $item->location ? trim((string) $item->location) : '';
                                $url = $item->detail_url ? trim((string) $item->detail_url) : '';
                            @endphp

                            <article
                                class="wf-card"
                                data-wf-card
                                data-item-title="{{ e($item->title) }}"
                                data-item-description="{{ e($item->description ?? '') }}"
                                data-item-image="{{ e($img ?? '') }}"
                                data-item-category="{{ e($label) }}"
                                data-item-location="{{ e($loc) }}"
                                data-item-url="{{ e($url) }}"
                                tabindex="0"
                                role="button"
                                aria-label="Baca selengkapnya: {{ $item->title }}"
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

                                    {{-- ✅ Lokasi (opsional) --}}
                                    @if($loc !== '')
                                        <div class="wf-loc" title="{{ $loc }}">
                                            {{-- icon location (bukan emoji) --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M12 22s7-4.35 7-11a7 7 0 10-14 0c0 6.65 7 11 7 11z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                <path d="M12 11.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" stroke="currentColor" stroke-width="2"/>
                                            </svg>
                                            <span>{{ $loc }}</span>
                                        </div>
                                    @endif
                                </div>

                                <button type="button" class="wf-cta" data-wf-open>
                                    Baca selengkapnya
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </article>
                        @endforeach
                    </div>

                    <button type="button" class="wf-nav wf-next" data-wf-next aria-label="Berikutnya">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                        </svg>
                    </button>
                </div>

                {{-- MOBILE NAV (below) --}}
                <div class="wf-mobile-nav" data-wf-mobile-nav>
                    <button type="button" class="wf-mobile-btn" data-wf-mobile-prev aria-label="Sebelumnya">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                        </svg>
                    </button>
                    <button type="button" class="wf-mobile-btn" data-wf-mobile-next aria-label="Berikutnya">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endforeach

<script>
(function () {
    const section = document.getElementById('warisan');
    if (!section) return;

    const rows = section.querySelectorAll('[data-wf-row]');
    const modalOverlay = document.getElementById('wf-modal-overlay');
    const modalContent = document.getElementById('wf-modal-content');
    const modalCloseBtn = document.getElementById('wf-modal-close');

    const headerEl = document.getElementById('top'); // navbar

    if (!rows.length) return;

    // ===== Reveal on enter viewport =====
    const io = new IntersectionObserver((entries) => {
        entries.forEach(ent => {
            if (ent.isIntersecting) {
                ent.target.classList.add('wf-inview');
            }
        });
    }, { threshold: 0.12 });

    rows.forEach(r => io.observe(r));

    // ===== Helpers =====
    function decodeHtmlEntities(str) {
        if (!str) return '';
        const txt = document.createElement('textarea');
        txt.innerHTML = str;
        return txt.value;
    }

    // ✅ allow only http/https URL (prevent weird schemes)
    function safeHttpUrl(raw) {
        const s = (raw || '').trim();
        if (!s) return '';
        try {
            const u = new URL(s, window.location.origin);
            if (u.protocol === 'http:' || u.protocol === 'https:') return u.href;
            return '';
        } catch {
            return '';
        }
    }

    let lastFocus = null;

    function lockScroll(){
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';
    }

    function unlockScroll(){
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
    }

    // Navbar ditimpa (bukan hide total)
    function modalNavOn(){
        document.documentElement.classList.add('wf-warisan-modal-open');
    }

    function modalNavOff(){
        document.documentElement.classList.remove('wf-warisan-modal-open');
    }

    function openModalFromCard(card){
        if (!card || !modalOverlay || !modalContent) return;

        lastFocus = document.activeElement;

        const title = card.dataset.itemTitle || 'Judul tidak tersedia';
        const description = decodeHtmlEntities(card.dataset.itemDescription || '');
        const imageUrl = decodeHtmlEntities(card.dataset.itemImage || '');
        const category = card.dataset.itemCategory || 'Kategori';

        const location = decodeHtmlEntities(card.dataset.itemLocation || '').trim();
        const detailUrl = safeHttpUrl(decodeHtmlEntities(card.dataset.itemUrl || ''));

        let leftHtml = '';
        if (imageUrl && imageUrl.trim() !== '') {
            leftHtml = `
                <div class="wf-modal-image" style="background-image:url('${imageUrl}')"></div>
            `;
        } else {
            leftHtml = `
                <div class="wf-modal-image wf-modal-image-fallback"><span>🏛️</span></div>
            `;
        }

        // lokasi row (opsional)
        const locHtml = location
            ? `
                <div class="wf-modal-meta">
                    <div class="wf-meta-row">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 22s7-4.35 7-11a7 7 0 10-14 0c0 6.65 7 11 7 11z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M12 11.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <span>${location}</span>
                    </div>
                </div>
            `
            : '';

        // tombol link (opsional)
        const linkBtnHtml = detailUrl
            ? `
                <a class="wf-modal-link" href="${detailUrl}" target="_blank" rel="noopener noreferrer">
                    Lihat selengkapnya
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H18m0 0v4.5M18 6l-9 9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 7.5H7.8A2.3 2.3 0 005.5 9.8v6.4A2.3 2.3 0 007.8 18.5h6.4a2.3 2.3 0 002.3-2.3V13.5"/>
                    </svg>
                </a>
            `
            : '';

        const rightHtml = `
            <div class="wf-modal-body">
                <div class="wf-modal-pill">🏷️ <span>${category}</span></div>
                <h3 class="wf-modal-title" id="wfModalTitle">${title}</h3>
                <div class="wf-modal-divider"></div>

                ${locHtml}

                <p class="wf-modal-desc">${description ? description : '—'}</p>

                <div class="wf-modal-actions">
                    ${linkBtnHtml}


                </div>

                <div class="wf-modal-note">Tip: klik area gelap atau tekan ESC untuk menutup.</div>
            </div>
        `;

        modalContent.innerHTML = leftHtml + rightHtml;

        // open
        modalOverlay.classList.add('active');
        modalOverlay.setAttribute('aria-hidden','false');
        lockScroll();
        modalNavOn();

        // focus close
        setTimeout(() => {
            if (modalCloseBtn) modalCloseBtn.focus();
        }, 50);

        // preload image fallback
        if (imageUrl && imageUrl.trim() !== '') {
            const img = new Image();
            img.onerror = function(){
                const imgDiv = modalContent.querySelector('.wf-modal-image');
                if (imgDiv) {
                    imgDiv.classList.add('wf-modal-image-fallback');
                    imgDiv.style.backgroundImage = 'none';
                    imgDiv.innerHTML = '<span>🏛️</span>';
                }
            };
            img.src = imageUrl;
        }
    }

    function closeModal(){
        if (!modalOverlay) return;
        modalOverlay.classList.remove('active');
        modalOverlay.setAttribute('aria-hidden','true');
        unlockScroll();
        modalNavOff();

        if (lastFocus && typeof lastFocus.focus === 'function') {
            setTimeout(() => lastFocus.focus(), 60);
        }
    }

    // close handlers
    if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
            const dialog = modalOverlay.querySelector('.wf-modal-container');
            if (dialog && dialog.contains(e.target) && !e.target.hasAttribute('data-wf-close')) return;
            closeModal();
        });
    }

    if (modalCloseBtn) modalCloseBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        closeModal();
    });

    document.addEventListener('keydown', (e) => {
        if (!modalOverlay || !modalOverlay.classList.contains('active')) return;
        if (e.key === 'Escape') closeModal();
    });

    // delegate close button inside modal
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-wf-close-btn]');
        if (!btn) return;
        if (!modalOverlay || !modalOverlay.classList.contains('active')) return;
        e.preventDefault();
        e.stopPropagation();
        closeModal();
    });

    // ===== Slider logic per row =====
    function setupRow(row){
        const track = row.querySelector('[data-wf-track]');
        const viewport = row.querySelector('[data-wf-viewport]');
        const cards = row.querySelectorAll('[data-wf-card]');
        const prevBtn = row.querySelector('[data-wf-prev]');
        const nextBtn = row.querySelector('[data-wf-next]');
        const mPrev = row.querySelector('[data-wf-mobile-prev]');
        const mNext = row.querySelector('[data-wf-mobile-next]');

        if (!track || !viewport || !cards.length) return;

        let index = 0;
        let isMobile = window.innerWidth <= 768;
        let timer = null;

        function getVisibleCount(){
            return (window.innerWidth <= 768) ? 1 : 3;
        }

        function maxIndex(){
            const vis = getVisibleCount();
            return Math.max(0, cards.length - vis);
        }

        function clamp(i){
            const max = maxIndex();
            if (i < 0) return max;
            if (i > max) return 0;
            return i;
        }

        function measureStep(){
            // jarak 1 card = lebar card + gap
            const first = cards[0];
            if (!first) return 0;
            const style = getComputedStyle(track);
            const gap = parseFloat(style.gap || '0') || 0;
            return first.getBoundingClientRect().width + gap;
        }

        function render(){
            const step = measureStep();
            const x = -(index * step);
            track.style.transform = `translate3d(${x}px,0,0)`;
        }

        function go(delta){
            index = clamp(index + delta);
            render();
        }

        function shouldAuto(){
            // desktop: auto hanya jika item > 3
            // mobile: auto hanya jika item > 1
            const vis = getVisibleCount();
            return cards.length > vis;
        }

        function startAuto(){
            stopAuto();
            if (!shouldAuto()) return;

            // desktop/mobile sama: shift 1 tiap 3 detik
            timer = setInterval(() => {
                go(1);
            }, 3000);
        }

        function stopAuto(){
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        }

        function updateMode(){
            isMobile = window.innerWidth <= 768;
            index = clamp(index);
            render();
            startAuto();
        }

        // nav buttons
        if (prevBtn) prevBtn.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); go(-1); });
        if (nextBtn) nextBtn.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); go(1); });
        if (mPrev) mPrev.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); go(-1); });
        if (mNext) mNext.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); go(1); });

        // swipe for mobile
        let sx = 0;
        let swiping = false;

        if (viewport) {
            viewport.addEventListener('touchstart', (e) => {
                if (!e.touches) return;
                sx = e.touches[0].clientX;
                swiping = true;
            }, {passive:true});

            viewport.addEventListener('touchend', (e) => {
                if (!swiping) return;
                const ex = e.changedTouches[0].clientX;
                const dx = ex - sx;
                if (Math.abs(dx) > 45) {
                    if (dx > 0) go(-1);
                    else go(1);
                }
                swiping = false;
            }, {passive:true});
        }

        // open modal handlers
        cards.forEach((card) => {
            card.addEventListener('click', (e) => {
                const openBtn = e.target.closest('[data-wf-open]');
                if (openBtn || e.target === card || card.contains(e.target)) {
                    e.preventDefault();
                    openModalFromCard(card);
                }
            });

            card.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    openModalFromCard(card);
                }
            });

            const btn = card.querySelector('[data-wf-open]');
            if (btn) {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    openModalFromCard(card);
                });
            }
        });

        // pause auto on hover desktop
        row.addEventListener('mouseenter', stopAuto);
        row.addEventListener('mouseleave', startAuto);

        // resize
        let rt = null;
        window.addEventListener('resize', () => {
            clearTimeout(rt);
            rt = setTimeout(updateMode, 120);
        });

        // init
        requestAnimationFrame(() => {
            render();
            startAuto();
        });
    }

    rows.forEach(setupRow);
})();
</script>

</section>
