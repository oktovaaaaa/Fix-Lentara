{{-- resources/views/islands/partials/ai-foods.blade.php --}}

@php
    $tribeKey = $tribeKey ?? '';
    $aiFoodRecommendation = $aiFoodRecommendation ?? null;

    $payload = $aiFoodRecommendation?->payload ?? null;
    $items = is_array($payload) ? ($payload['items'] ?? []) : [];

    /**
     * Helper: clamp rating to 0..5 and return float (or null if empty)
     */
    $clampRating = function ($rating) {
        if ($rating === null || $rating === '') return null;
        $n = (float) $rating;
        if ($n < 0) $n = 0;
        if ($n > 5) $n = 5;
        return $n;
    };

    /**
     * Helper: format rating number for display (1 decimal max)
     */
    $fmtRating = function ($rating) use ($clampRating) {
        $n = $clampRating($rating);
        if ($n === null) return null;
        return rtrim(rtrim(number_format($n, 1, '.', ''), '0'), '.'); // 4.0 -> 4, 4.5 -> 4.5
    };

    /**
     * Helper: where_to_find -> string (max 3)
     */
    $whereToText = function ($where) {
        if (!is_array($where) || !count($where)) return '';
        return implode(', ', array_slice($where, 0, 3));
    };

    /**
     * Helper: convert rating into stars (full, half, empty) MAX TOTAL 5
     * Rule:
     * - decimal >= 0.75 => round up to next full
     * - 0.25..0.74 => half
     * - < 0.25 => none
     */
    $starParts = function (?float $ratingVal) {
        if ($ratingVal === null) return ['full'=>0,'half'=>0,'empty'=>5];

        $full = (int) floor($ratingVal);
        $dec  = $ratingVal - $full;

        $half = 0;
        if ($dec >= 0.75) {
            $full += 1;
        } elseif ($dec >= 0.25) {
            $half = 1;
        }

        if ($full > 5) $full = 5;

        $total = $full + $half;
        if ($total > 5) {
            $half = 0;
            $total = 5;
        }

        $empty = 5 - $total;
        if ($empty < 0) $empty = 0;

        return ['full'=>$full,'half'=>$half,'empty'=>$empty];
    };
@endphp

<section id="foods" class="py-12">
    {{-- IMPORTANT: neon-title/title-decoration/neon-subtitle pakai CSS GLOBAL dari islands.blade --}}
    <h2 class="neon-title">
        Kuliner Khas Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}
    </h2>
    <div class="title-decoration"></div>
    <p class="neon-subtitle">
        Rekomendasi kuliner khas yang dikurasi otomatis per minggu untuk Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}.
    </p>

    @if(!empty($items))
        <div class="foods-grid">
            @foreach($items as $idx => $it)
                @php
                    $name   = (string)($it['name'] ?? '');
                    $desc   = (string)($it['description'] ?? '');
                    $img    = (string)($it['image_url'] ?? '');
                    $price  = $it['price_range'] ?? null;
                    $rating = $it['rating_estimate'] ?? null;
                    $region = $it['region_hint'] ?? null;
                    $where  = $it['where_to_find'] ?? [];

                    $sources = $it['sources'] ?? [];
                    $wikiUrl = $it['wiki_url'] ?? null;
                    $wikiSummary = $it['wiki_summary'] ?? null;

                    // themes rotation (background boleh variasi, glow NEON tetap orange)
                    $themes = ['sunset','orange','mint','violet','sky','rose'];
                    $theme = $themes[$idx % count($themes)];

                    $ratingVal  = $clampRating($rating);
                    $ratingText = $fmtRating($rating);
                    $whereText  = $whereToText($where);

                    $stars = $starParts($ratingVal);
                    $fullStars  = $stars['full'];
                    $halfStars  = $stars['half'];
                    $emptyStars = $stars['empty'];

                    // safe strings for data-attrs
                    $dataName   = e($name);
                    $dataDesc   = e($desc);
                    $dataImg    = e($img);
                    $dataPrice  = e((string)($price ?? ''));
                    $dataRegion = e((string)($region ?? ''));
                    $dataWhere  = e((string)($whereText ?? ''));
                    $dataRatingText = e((string)($ratingText ?? ''));
                    $dataRatingRaw  = e((string)($ratingVal ?? ''));

                    // ✅ sumber spesifik per item: PRIORITAS wiki_url, baru sources[0]
                    $dataSource = '';
                    if (!empty($wikiUrl)) {
                        $dataSource = e((string)$wikiUrl);
                    } elseif (is_array($sources) && count($sources)) {
                        $dataSource = e((string)$sources[0]);
                    }

                    // ✅ NEW: data untuk modal asal-usul
                    $dataWikiUrl = e((string)($wikiUrl ?? ''));
                    $dataWikiSummary = e((string)($wikiSummary ?? ''));

                    // ✅ URL sumber untuk tombol di card
                    $sourceUrl = $wikiUrl ?: (is_array($sources) && count($sources) ? $sources[0] : null);
                @endphp

                <article
                    class="food-card food-theme-{{ $theme }}"
                    role="button"
                    tabindex="0"
                    data-food-card
                    data-name="{{ $dataName }}"
                    data-desc="{{ $dataDesc }}"
                    data-img="{{ $dataImg }}"
                    data-price="{{ $dataPrice }}"
                    data-region="{{ $dataRegion }}"
                    data-where="{{ $dataWhere }}"
                    data-rating="{{ $dataRatingText }}"
                    data-ratingraw="{{ $dataRatingRaw }}"
                    data-source="{{ $dataSource }}"
                    data-wikiurl="{{ $dataWikiUrl }}"
                    data-wikisummary="{{ $dataWikiSummary }}"
                    data-fullstars="{{ $fullStars }}"
                    data-halfstars="{{ $halfStars }}"
                    data-emptystars="{{ $emptyStars }}"
                >
                    <div class="food-card-top">
                        {{-- LEFT: RATING (TOP-LEFT, DI ATAS GAMBAR) + IMAGE --}}
                        <div class="food-thumb-col">
                            @if($ratingVal !== null)
                                <div class="food-rating-badge" aria-label="Rating {{ $ratingText }} dari 5">
                                    <div class="food-rating-stars" aria-hidden="true">
                                        {{-- full --}}
                                        @for($i=0; $i < $fullStars; $i++)
                                            <svg class="food-star food-star-full" viewBox="0 0 20 20" aria-hidden="true">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor

                                        {{-- half --}}
                                        @if($halfStars === 1)
                                            <span class="food-star-half" aria-hidden="true">
                                                <svg class="food-star food-star-empty" viewBox="0 0 20 20" aria-hidden="true">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                <span class="food-star-halfFill" aria-hidden="true">
                                                    <svg class="food-star food-star-full" viewBox="0 0 20 20" aria-hidden="true">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                </span>
                                            </span>
                                        @endif

                                        {{-- empty --}}
                                        @for($i=0; $i < $emptyStars; $i++)
                                            <svg class="food-star food-star-empty" viewBox="0 0 20 20" aria-hidden="true">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>

                                    <span class="food-rating-num">{{ $ratingText }}</span>
                                </div>
                            @endif

                            <div class="food-thumb-ring">
                                <img
                                    src="{{ $img }}"
                                    alt="{{ $name }}"
                                    class="food-thumb"
                                    loading="lazy"
                                >
                            </div>
                        </div>

                        {{-- RIGHT: META --}}
                        <div class="food-meta">
                            <h3 class="food-name">{{ $name }}</h3>

                            <div class="food-mini">
                                @if($price)
                                    <span class="food-pill food-pill-price">
                                        {{ $price }}
                                    </span>
                                @endif

                                @if($region)
                                    <span class="food-pill">
                                        <svg class="pill-ico" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $region }}
                                    </span>
                                @endif

                                @if(is_array($where) && count($where))
                                    <span class="food-pill">
                                        <svg class="pill-ico" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ implode(', ', array_slice($where, 0, 2)) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <p class="food-desc">
                        {{ $desc }}
                    </p>

                    <div class="food-actions">
                        <button type="button" class="food-cta" data-open-modal>
                            Lihat detail
                            <svg class="cta-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </button>

                        @if($sourceUrl)
                            <a class="food-src" href="{{ $sourceUrl }}" target="_blank" rel="noopener">
                                Sumber
                            </a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8 text-center">
            <div class="foods-update-pill">
                <svg class="w-4 h-4 mr-2 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                </svg>
                <span class="text-xs font-medium text-green-700 dark:text-green-300">
                    Update mingguan: {{ $payload['generated_at'] ?? '—' }} (Senin 00:00)
                </span>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <div class="inline-block p-6 rounded-2xl bg-gradient-to-br from-orange-50 to-green-50 dark:from-orange-900/10 dark:to-green-900/10 border border-[var(--line)]">
                <svg class="w-16 h-16 mx-auto mb-4 text-[var(--muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-sm text-[var(--muted)] mb-2">
                    Rekomendasi kuliner untuk {{ $tribeKey }} minggu ini belum tersedia.
                </p>
                <p class="text-xs text-[var(--muted)]">
                    Admin: jalankan scheduler atau dispatch job generate.
                </p>
            </div>
        </div>
    @endif

    {{-- =========================================================
       MODAL POPUP (DETAIL MENU)
       - Klik card / tombol "Lihat detail"
       - FIX: close "X" clickable + hover ORANGE
       - Tambah tombol "Tutup" (animasi) di dalam modal
       - Bintang modal MAX 5
    ========================================================= --}}
    <div id="foodModal" class="food-modal food-hidden" aria-hidden="true">
        <div class="food-modal-backdrop" data-close-modal></div>

        <div class="food-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="foodModalTitle">
            <button type="button" class="food-modal-close" data-close-modal aria-label="Tutup">✕</button>

            <div class="food-modal-grid">
                <div class="food-modal-media">
                    <div class="food-modal-imgWrap">
                        <img id="foodModalImg" src="" alt="" class="food-modal-img">
                    </div>
                </div>

                <div class="food-modal-body">
                    <div class="food-modal-head">
                        <h3 id="foodModalTitle" class="food-modal-title">—</h3>

                        <div class="food-modal-rating" aria-label="Rating">
                            <div id="foodModalStars" class="food-modal-stars" aria-hidden="true"></div>
                            <span id="foodModalRatingText" class="food-modal-ratingText"></span>
                        </div>
                    </div>

                    <p id="foodModalDesc" class="food-modal-desc"></p>

                    {{-- ✅ ASAL-USUL / SEJARAH SINGKAT (dari wiki_summary) --}}
                    <div id="foodModalOriginWrap" class="food-modal-origin food-hidden">
                        <h4 class="food-modal-originTitle">Asal-usul / Sejarah singkat</h4>
                        <p id="foodModalOrigin" class="food-modal-originText"></p>
                    </div>

                    <div class="food-modal-pills">
                        <span id="foodModalPrice" class="food-modal-pill food-modal-pill-price food-hidden"></span>
                        <span id="foodModalRegion" class="food-modal-pill food-hidden"></span>
                        <span id="foodModalWhere" class="food-modal-pill food-hidden"></span>
                    </div>

                    <div class="food-modal-actions">
                        <a id="foodModalSource" class="food-modal-source food-hidden" href="#" target="_blank" rel="noopener">
                            Lihat sumber
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>

                        {{-- BUTTON TUTUP (ANIMASI) --}}
                        <button type="button" class="food-modal-closeBtn" data-food-close="btn">
                            Tutup
                            <span class="food-modal-closeGlow" aria-hidden="true"></span>
                        </button>
                    </div>

                    <div class="food-modal-footnote">

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =========================================================
   STYLES (SCOPED) — supaya tidak ganggu navbar/chatbot
   - Semua selector diawali #foods
   - Neon title system TIDAK di sini (pakai global islands.blade)
========================================================= --}}
<style>
/* =========================================================
   FOODS GRID
========================================================= */
#foods .foods-grid{
    display:grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

/* =========================================================
   COLORFUL THEMES (card background boleh variasi),
   tapi efek glow/hover NEON tetap ORANGE saja.
========================================================= */
#foods .food-theme-sunset{
    --cardGrad: linear-gradient(135deg, rgba(255, 107, 0, .18), rgba(255, 170, 107, .10));
    --accent: #ff6b00;
    --accent2:#ffb38a;
}
#foods .food-theme-orange{
    --cardGrad: linear-gradient(135deg, rgba(249, 115, 22, .20), rgba(253, 186, 116, .10));
    --accent: #ff6b00;
    --accent2:#ffb38a;
}
#foods .food-theme-mint{
    --cardGrad: linear-gradient(135deg, rgba(16, 185, 129, .18), rgba(167, 243, 208, .10));
    --accent: #ff6b00;
    --accent2:#ffb38a;
}
#foods .food-theme-violet{
    --cardGrad: linear-gradient(135deg, rgba(99, 102, 241, .18), rgba(196, 181, 253, .10));
    --accent: #ff6b00;
    --accent2:#ffb38a;
}
#foods .food-theme-sky{
    --cardGrad: linear-gradient(135deg, rgba(14, 165, 233, .18), rgba(186, 230, 253, .10));
    --accent: #ff6b00;
    --accent2:#ffb38a;
}
#foods .food-theme-rose{
    --cardGrad: linear-gradient(135deg, rgba(244, 63, 94, .18), rgba(253, 164, 175, .10));
    --accent: #ff6b00;
    --accent2:#ffb38a;
}

/* =========================================================
   CARD + NEON BORDER ANIM (PINGGIR CARD) — sesuai feel history
========================================================= */
@property --food-border-angle {
    syntax: "<angle>";
    inherits: false;
    initial-value: 0deg;
}

#foods .food-card{
    border-radius: 22px;
    border: 1px solid color-mix(in oklab, var(--line) 78%, transparent);
    background:
        var(--cardGrad),
        linear-gradient(180deg, var(--card), color-mix(in oklab, var(--card) 88%, transparent));
    box-shadow: 0 10px 30px rgba(0,0,0,.06);
    padding: 16px 16px 14px;
    display:flex;
    flex-direction:column;
    gap: 12px;
    cursor:pointer;
    transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease, filter .25s ease;
    position: relative;
    overflow: hidden;
}

/* Layer glow lembut */
#foods .food-card::before{
    content:"";
    position:absolute;
    inset:-2px;
    background: radial-gradient(700px 180px at 15% 0%, rgba(255,107,0,.26), transparent 60%);
    pointer-events:none;
    opacity:.95;
}

/* ===== NEON BORDER MUTER (INI YANG KAMU MINTA MUNCUL) ===== */
#foods .food-card::after{
    content:"";
    position:absolute;
    inset:-6px;
    border-radius: inherit;
    padding: 10px;
    pointer-events:none;
    background: conic-gradient(
        from var(--food-border-angle),
        rgba(255,107,0,0) 0deg,
        rgba(255,140,66,.22) 40deg,
        rgba(255,107,0,.90) 90deg,
        rgba(255,170,107,.55) 140deg,
        rgba(255,140,66,.18) 200deg,
        rgba(255,107,0,.85) 280deg,
        rgba(255,107,0,0) 360deg
    );
    -webkit-mask:
        linear-gradient(#000 0 0) content-box,
        linear-gradient(#000 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    filter: blur(6px);
    opacity: 0;                 /* default hidden */
    transition: opacity .25s ease;
    animation: food-border-spin 8s linear infinite;
}

@keyframes food-border-spin {
    to { --food-border-angle: 360deg; }
}

#foods .food-card > * { position: relative; z-index: 1; }

#foods .food-card:hover{
    transform: translateY(-6px);
    box-shadow: 0 18px 48px rgba(0,0,0,.12), 0 0 34px rgba(255,107,0,.22);
    border-color: rgba(255,107,0,.55);
    filter: saturate(1.06);
}

/* Saat hover/focus: border neon terlihat */
#foods .food-card:hover::after,
#foods .food-card:focus::after{
    opacity: .95;
}

#foods .food-card:focus{
    outline: none;
    box-shadow: 0 0 0 4px rgba(255,107,0,.22), 0 18px 48px rgba(0,0,0,.12);
}

/* =========================================================
   TOP LAYOUT
========================================================= */
#foods .food-card-top{
    display:flex;
    align-items:flex-start;
    gap: 14px;
}

/* LEFT column: badge di atas gambar (tidak menimpa gambar) */
#foods .food-thumb-col{
    display:flex;
    flex-direction:column;
    align-items:flex-start;
    gap: 10px;
}

/* image ring */
#foods .food-thumb-ring{
    width: 104px;
    height: 104px;
    border-radius: 999px;
    padding: 4px;
    background: linear-gradient(135deg, rgba(255,107,0,.95), rgba(255,170,107,.75));
    box-shadow: 0 14px 28px rgba(0,0,0,.16), 0 0 22px rgba(255,107,0,.14);
}

#foods .food-thumb{
    width: 100%;
    height: 100%;
    border-radius: 999px;
    object-fit: cover;
    display:block;
    border: 3px solid rgba(255,255,255,.92);
}

/* =========================================================
   RATING BADGE
========================================================= */
#foods .food-rating-badge{
    width: fit-content;
    max-width: 100%;
    display:inline-flex;
    align-items:center;
    gap: 10px;
    padding: 8px 10px;
    border-radius: 999px;
    background: rgba(255,255,255,.92);
    border: 1px solid rgba(15,23,42,.08);
    box-shadow: 0 12px 26px rgba(0,0,0,.14);
}

html[data-theme="dark"] #foods .food-rating-badge{
    background: rgba(2,6,23,.72);
    border-color: rgba(148,163,184,.18);
}

#foods .food-rating-stars{
    display:inline-flex;
    align-items:center;
    gap: 3px;
}

#foods .food-star{
    width: 14px;
    height: 14px;
    display:block;
}

#foods .food-star-full{
    fill: #fbbf24;
    filter: drop-shadow(0 6px 12px rgba(251,191,36,.18));
}

#foods .food-star-empty{
    fill: rgba(148,163,184,.45);
}
html[data-theme="dark"] #foods .food-star-empty{
    fill: rgba(148,163,184,.35);
}

#foods .food-star-half{
    position: relative;
    width: 14px;
    height: 14px;
    display:inline-block;
}
#foods .food-star-half .food-star{
    position:absolute;
    inset:0;
}
#foods .food-star-halfFill{
    position:absolute;
    inset:0;
    width: 50%;
    overflow:hidden;
}

#foods .food-rating-num{
    font-size: .9rem;
    line-height: 1;
    font-weight: 900;
    color: var(--txt-body);
}

/* =========================================================
   TEXT
========================================================= */
#foods .food-meta{ flex:1; min-width:0; }

#foods .food-name{
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--txt-body);
    margin: 0 0 6px;
    line-height:1.2;
    letter-spacing: -.01em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

#foods .food-mini{
    display:flex;
    flex-wrap:wrap;
    gap: 8px;
}

#foods .food-pill{
    display:inline-flex;
    align-items:center;
    gap: 6px;
    font-size: .75rem;
    font-weight: 650;
    color: color-mix(in oklab, var(--txt-body) 75%, var(--muted));
    background: rgba(255,255,255,.55);
    border: 1px solid rgba(15,23,42,.06);
    padding: 5px 10px;
    border-radius: 999px;
    max-width: 100%;
}

html[data-theme="dark"] #foods .food-pill{
    background: rgba(2,6,23,.40);
    border-color: rgba(148,163,184,.18);
    color: color-mix(in oklab, var(--txt-body) 86%, var(--muted));
}

#foods .food-pill-price{
    background: rgba(255,107,0,.12);
    border-color: rgba(255,107,0,.22);
}

html[data-theme="dark"] #foods .food-pill-price{
    background: rgba(255,107,0,.14);
    border-color: rgba(148,163,184,.18);
}

#foods .pill-ico{
    width: 14px;
    height: 14px;
    opacity: .85;
}

#foods .food-desc{
    margin: 0;
    color: var(--muted);
    font-size: .9rem;
    line-height: 1.55;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* =========================================================
   ACTIONS
========================================================= */
#foods .food-actions{
    display:flex;
    align-items:center;
    justify-content: space-between;
    gap: 10px;
}

#foods .food-cta{
    border: 0;
    cursor:pointer;
    font-weight: 800;
    font-size: .85rem;
    color: #fff;
    background: linear-gradient(135deg, #ff6b00, #ff8c42);
    padding: 10px 12px;
    border-radius: 14px;
    box-shadow: 0 14px 28px rgba(0,0,0,.10);
    display:inline-flex;
    align-items:center;
    gap: 10px;
    transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
}

#foods .food-cta:hover{
    transform: translateY(-2px);
    box-shadow: 0 18px 40px rgba(0,0,0,.14), 0 0 24px rgba(255,107,0,.18);
    filter: saturate(1.05);
}

#foods .food-cta:active{ transform: translateY(0); }

#foods .cta-ico{
    width: 18px;
    height: 18px;
    opacity: .95;
}

#foods .food-src{
    font-size: .85rem;
    font-weight: 800;
    color: #ff6b00;
    text-decoration:none;
    padding: 8px 10px;
    border-radius: 12px;
    background: rgba(255,255,255,.55);
    border: 1px solid rgba(15,23,42,.06);
    transition: transform .2s ease, background .2s ease, border-color .2s ease;
}

html[data-theme="dark"] #foods .food-src{
    background: rgba(2,6,23,.40);
    border-color: rgba(148,163,184,.18);
    color: #ff8c42;
}

#foods .food-src:hover{
    transform: translateY(-1px);
    border-color: rgba(255,107,0,.28);
}

/* =========================================================
   UPDATE PILL
========================================================= */
#foods .foods-update-pill{
    display:inline-flex;
    align-items:center;
    padding: 10px 14px;
    border-radius: 999px;
    border: 1px solid var(--line);
    background: linear-gradient(to right, rgba(255,255,255,.55), rgba(255,255,255,.30));
}
html[data-theme="dark"] #foods .foods-update-pill{ background: rgba(2,6,23,.35); }

/* =========================================================
   MODAL
========================================================= */
#foods .food-hidden{ display:none !important; }

#foods .food-modal{
    position: fixed;
    inset: 0;
    z-index: 99999;
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 18px;
}

#foods .food-modal-backdrop{
    position:absolute;
    inset:0;
    background: rgba(0,0,0,.55);
    backdrop-filter: blur(8px);
    z-index: 1;
}

#foods .food-modal-dialog{
    position: relative;
    width: min(980px, 100%);
    max-height: min(92vh, 920px);
    border-radius: 22px;
    background: var(--card);
    border: 1px solid var(--line);
    box-shadow: 0 30px 90px rgba(0,0,0,.35);
    overflow:hidden;
    z-index: 2;
    display:flex;
    flex-direction:column;
    pointer-events: auto;
}

/* Close X (hover harus ada warna + bisa diklik) */
#foods .food-modal-close{
    position:absolute;
    top: 12px;
    right: 12px;
    width: 44px;
    height: 44px;
    border-radius: 999px;
    border: 1px solid rgba(255,107,0,.45);
    background: rgba(255,255,255,.86);
    cursor:pointer;
    font-size: 18px;
    font-weight: 900;
    color: #ff6b00;
    display:flex;
    align-items:center;
    justify-content:center;
    z-index: 999;
    pointer-events: auto;
    user-select: none;
    transition: all .2s ease;
}

html[data-theme="dark"] #foods .food-modal-close{
    background: rgba(2,6,23,.70);
    border-color: rgba(255,107,0,.35);
    color: #ff8c42;
}

#foods .food-modal-close:hover{
    background: rgba(255,107,0,.95);
    color: #fff;
    transform: rotate(90deg);
    border-color: rgba(255,107,0,.95);
    box-shadow: 0 14px 30px rgba(0,0,0,.28), 0 0 26px rgba(255,107,0,.22);
}

#foods .food-modal-grid{
    display:grid;
    grid-template-columns: 1.05fr 1fr;
    gap: 0;
    height: 100%;
    min-height: 0;
}

#foods .food-modal-media{
    background: linear-gradient(135deg, rgba(255,255,255,.55), rgba(255,255,255,.25));
    border-right: 1px solid var(--line);
    min-height: 0;
}
html[data-theme="dark"] #foods .food-modal-media{ background: rgba(2,6,23,.25); }

#foods .food-modal-imgWrap{
    padding: 18px;
    height: 100%;
    display:flex;
    align-items:center;
    justify-content:center;
}

#foods .food-modal-img{
    width: 100%;
    height: 100%;
    max-height: 100%;
    object-fit: cover;
    border-radius: 18px;
    border: 1px solid rgba(15,23,42,.06);
}

#foods .food-modal-body{
    padding: 20px 20px 18px;
    overflow:auto;
    min-height: 0;
}

#foods .food-modal-head{
    display:flex;
    align-items:flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 10px;
}

#foods .food-modal-title{
    font-size: 1.4rem;
    font-weight: 900;
    margin: 0;
    color: var(--txt-body);
    line-height:1.2;
}

#foods .food-modal-rating{
    display:flex;
    align-items:center;
    gap: 10px;
    background: rgba(255,255,255,.60);
    border: 1px solid rgba(255,107,0,.20);
    border-radius: 999px;
    padding: 8px 12px;
    flex-shrink: 0;
}
html[data-theme="dark"] #foods .food-modal-rating{
    background: rgba(2,6,23,.40);
    border-color: rgba(255,107,0,.18);
}

#foods .food-modal-stars{
    display:inline-flex;
    align-items:center;
    gap: 3px;
}
#foods .food-modal-stars .food-star{ width: 16px; height: 16px; }

#foods .food-modal-ratingText{
    font-size: .95rem;
    font-weight: 900;
    color: var(--txt-body);
    line-height: 1;
}

#foods .food-modal-desc{
    margin: 8px 0 14px;
    color: var(--muted);
    line-height: 1.65;
    font-size: .95rem;
}

/* ✅ ASAL-USUL / SEJARAH SINGKAT */
#foods .food-modal-origin{
    margin-top: 10px;
    padding: 12px 14px;
    border-radius: 16px;
    border: 1px solid color-mix(in oklab, var(--line) 85%, transparent);
    background: linear-gradient(135deg, rgba(255,107,0,.08), rgba(148,163,184,.08));
}
html[data-theme="dark"] #foods .food-modal-origin{
    background: rgba(255,107,0,.08);
}

#foods .food-modal-originTitle{
    margin: 0 0 6px;
    font-size: .95rem;
    font-weight: 900;
    color: var(--txt-body);
}

#foods .food-modal-originText{
    margin: 0;
    font-size: .9rem;
    line-height: 1.65;
    color: var(--muted);
}

#foods .food-modal-pills{
    display:flex;
    flex-wrap:wrap;
    gap: 10px;
    margin-bottom: 14px;
}

#foods .food-modal-pill{
    display:inline-flex;
    align-items:center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid var(--line);
    background: rgba(148,163,184,.10);
    color: var(--txt-body);
    font-weight: 750;
    font-size: .85rem;
}

#foods .food-modal-pill-price{
    background: rgba(255,107,0,.10);
    border-color: rgba(255,107,0,.22);
}

#foods .food-modal-actions{
    display:flex;
    gap: 12px;
    align-items:center;
    flex-wrap: wrap;
}

#foods .food-modal-source{
    display:inline-flex;
    gap: 8px;
    align-items:center;
    padding: 10px 12px;
    border-radius: 14px;
    background: rgba(255,255,255,.60);
    border: 1px solid rgba(255,107,0,.18);
    color: #ff6b00;
    text-decoration:none;
    font-weight: 850;
}
html[data-theme="dark"] #foods .food-modal-source{
    background: rgba(2,6,23,.40);
    border-color: rgba(255,107,0,.18);
    color: #ff8c42;
}

/* BUTTON TUTUP (ANIMASI) */
#foods .food-modal-closeBtn{
    position: relative;
    border: 0;
    cursor: pointer;
    font-weight: 900;
    font-size: .9rem;
    color: #fff;
    padding: 10px 14px;
    border-radius: 14px;
    background: linear-gradient(135deg, #ff6b00, #ff8c42);
    box-shadow: 0 14px 28px rgba(0,0,0,.14);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
}

#foods .food-modal-closeBtn:hover{
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 20px 44px rgba(0,0,0,.22), 0 0 26px rgba(255,107,0,.20);
    filter: saturate(1.06);
}

#foods .food-modal-closeBtn:active{
    transform: translateY(0) scale(.98);
}

#foods .food-modal-closeGlow{
    position:absolute;
    inset:-2px;
    background: radial-gradient(220px 90px at 20% 0%, rgba(255,255,255,.28), transparent 60%);
    opacity: 0;
    transition: opacity .2s ease;
    pointer-events:none;
}

#foods .food-modal-closeBtn:hover .food-modal-closeGlow{
    opacity: 1;
}

#foods .food-modal-footnote{
    margin-top: 14px;
    font-size: .8rem;
    color: var(--muted);
}

/* =========================================================
   RESPONSIVE
========================================================= */
@media (max-width: 1024px){
    #foods .foods-grid{ grid-template-columns: repeat(2, minmax(0,1fr)); }
}

@media (max-width: 720px){
    #foods .foods-grid{ grid-template-columns: 1fr; }
    #foods .food-modal-grid{ grid-template-columns: 1fr; }
    #foods .food-modal-media{
        border-right: 0;
        border-bottom: 1px solid var(--line);
        height: 280px;
    }
    #foods .food-modal-imgWrap{ padding: 14px; }
}

@media (max-width: 480px){
    #foods .food-card-top{ align-items:flex-start; }
    #foods .food-thumb-ring{ width: 110px; height: 110px; }
}
</style>

{{-- =========================================================
   SCRIPT — open/close modal + fill content + render stars (MAX 5)
   FIX UTAMA: X close harus berfungsi + tombol "Tutup" juga menutup
========================================================= --}}
<script>
(function () {
    const modal = document.getElementById('foodModal');
    if (!modal) return;

    const dialog = modal.querySelector('.food-modal-dialog');

    const imgEl    = document.getElementById('foodModalImg');
    const titleEl  = document.getElementById('foodModalTitle');
    const descEl   = document.getElementById('foodModalDesc');

    // ✅ asal-usul
    const originWrap = document.getElementById('foodModalOriginWrap');
    const originEl   = document.getElementById('foodModalOrigin');

    const starsEl  = document.getElementById('foodModalStars');
    const rateTxt  = document.getElementById('foodModalRatingText');

    const priceEl  = document.getElementById('foodModalPrice');
    const regionEl = document.getElementById('foodModalRegion');
    const whereEl  = document.getElementById('foodModalWhere');

    const sourceEl = document.getElementById('foodModalSource');

    // tombol "Tutup" di modal
    const closeBtn2 = modal.querySelector('[data-food-close="btn"]');

    // ✅ NAVBAR kamu: <header id="top" class="site-header">
    const headerEl = document.getElementById('top');

    // simpan state navbar sebelum di-hide
    let headerPrev = null;

    let lastFocus = null;

    function clampInt(n, min, max){
        n = Number(n);
        if (!Number.isFinite(n)) n = 0;
        n = Math.floor(n);
        if (n < min) n = min;
        if (n > max) n = max;
        return n;
    }

    function starSVG(type){
        const cls = type === "full" ? "food-star food-star-full" : "food-star food-star-empty";
        return `
            <svg class="${cls}" viewBox="0 0 20 20" aria-hidden="true">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
        `;
    }

    function halfStarSVG(sizePx){
        const size = sizePx || 16;
        return `
            <span class="food-star-half" aria-hidden="true" style="width:${size}px;height:${size}px;position:relative;display:inline-block;">
                ${starSVG("empty")}
                <span class="food-star-halfFill" aria-hidden="true" style="position:absolute;inset:0;width:50%;overflow:hidden;">
                    ${starSVG("full")}
                </span>
            </span>
        `;
    }

    function renderStars(container, full, half, empty){
        if (!container) return;

        full  = clampInt(full, 0, 5);
        half  = clampInt(half, 0, 1);
        empty = clampInt(empty, 0, 5);

        let total = full + half + empty;
        if (total !== 5) {
            const keep = Math.min(5, full + half);
            full = Math.min(full, keep);
            if (full === 5) half = 0;
            empty = 5 - (full + half);
        }

        let html = '';
        for (let i=0; i<full; i++) html += starSVG("full");
        if (half === 1) html += halfStarSVG(16);
        for (let i=0; i<empty; i++) html += starSVG("empty");

        container.innerHTML = html;
    }

    // ✅ hide navbar (PAKSA) supaya tidak menimpa modal
    function hideNavbar(){
        if (!headerEl) return;

        // simpan style inline yg lama supaya bisa dibalikin
        if (!headerPrev) {
            headerPrev = {
                display: headerEl.style.display || '',
                visibility: headerEl.style.visibility || '',
                opacity: headerEl.style.opacity || '',
                pointerEvents: headerEl.style.pointerEvents || '',
                transform: headerEl.style.transform || ''
            };
        }

        headerEl.style.display = 'none';
        headerEl.style.visibility = 'hidden';
        headerEl.style.opacity = '0';
        headerEl.style.pointerEvents = 'none';
        headerEl.style.transform = 'translateY(-12px)';
    }

    // ✅ show navbar balik normal
    function showNavbar(){
        if (!headerEl) return;
        if (!headerPrev) return;

        headerEl.style.display = headerPrev.display;
        headerEl.style.visibility = headerPrev.visibility;
        headerEl.style.opacity = headerPrev.opacity;
        headerEl.style.pointerEvents = headerPrev.pointerEvents;
        headerEl.style.transform = headerPrev.transform;

        headerPrev = null;
    }

    function openModalFromCard(card){
        if (!card) return;

        lastFocus = document.activeElement;

        const name = card.getAttribute('data-name') || '—';
        const desc = card.getAttribute('data-desc') || '';
        const img  = card.getAttribute('data-img')  || '';
        const price = card.getAttribute('data-price') || '';
        const region = card.getAttribute('data-region') || '';
        const where  = card.getAttribute('data-where') || '';
        const ratingText = card.getAttribute('data-rating') || '';

        // ✅ ambil wiki data (asal-usul + url)
        const wikiUrlAttr = card.getAttribute('data-wikiurl') || '';
        const wikiSummary = card.getAttribute('data-wikisummary') || '';

        // ✅ sumber: prioritas wikiUrl, fallback data-source
        const source = wikiUrlAttr || (card.getAttribute('data-source') || '');

        const fullStars  = card.getAttribute('data-fullstars') || '0';
        const halfStars  = card.getAttribute('data-halfstars') || '0';
        const emptyStars = card.getAttribute('data-emptystars') || '5';

        titleEl.textContent = name;
        descEl.textContent = desc;

        imgEl.src = img;
        imgEl.alt = name;

        renderStars(starsEl, fullStars, halfStars, emptyStars);
        rateTxt.textContent = ratingText ? (ratingText + ' / 5') : '—';

        // ✅ asal-usul
        if (originWrap && originEl) {
            if (wikiSummary && wikiSummary.trim() !== '') {
                originEl.textContent = wikiSummary.trim();
                originWrap.classList.remove('food-hidden');
            } else {
                originEl.textContent = '';
                originWrap.classList.add('food-hidden');
            }
        }

        if (price) {
            priceEl.textContent = 'Harga: ' + price;
            priceEl.classList.remove('food-hidden');
        } else {
            priceEl.textContent = '';
            priceEl.classList.add('food-hidden');
        }

        if (region) {
            regionEl.textContent = 'Wilayah: ' + region;
            regionEl.classList.remove('food-hidden');
        } else {
            regionEl.textContent = '';
            regionEl.classList.add('food-hidden');
        }

        if (where) {
            whereEl.textContent = 'Tempat: ' + where;
            whereEl.classList.remove('food-hidden');
        } else {
            whereEl.textContent = '';
            whereEl.classList.add('food-hidden');
        }

        if (source) {
            sourceEl.href = source;
            sourceEl.classList.remove('food-hidden');
        } else {
            sourceEl.href = '#';
            sourceEl.classList.add('food-hidden');
        }

        // ✅ 1) hide navbar dulu
        hideNavbar();

        // ✅ 2) buka modal
        modal.classList.remove('food-hidden');
        modal.setAttribute('aria-hidden', 'false');

        // ✅ 3) lock scroll (lebih aman: body + html)
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';

        const xBtn = modal.querySelector('.food-modal-close');
        if (xBtn) xBtn.focus();
    }

    function closeModal(){
        modal.classList.add('food-hidden');
        modal.setAttribute('aria-hidden', 'true');

        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';

        // ✅ balikin navbar
        showNavbar();

        if (lastFocus && typeof lastFocus.focus === 'function') {
            lastFocus.focus();
        }
    }

    // open handlers
    document.querySelectorAll('[data-food-card]').forEach(card => {
        card.addEventListener('click', (e) => {
            const a = e.target.closest('a');
            if (a && a.classList.contains('food-src')) return;
            openModalFromCard(card);
        });

        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openModalFromCard(card);
            }
        });

        const btn = card.querySelector('[data-open-modal]');
        if (btn) {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                openModalFromCard(card);
            });
        }
    });

    // CLOSE via backdrop + X (keduanya data-close-modal)
    modal.querySelectorAll('[data-close-modal]').forEach(el => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
            closeModal();
        }, true);
    });

    // CLOSE via tombol "Tutup" (animasi)
    if (closeBtn2) {
        closeBtn2.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
            closeModal();
        }, true);
    }

    // close on ESC
    document.addEventListener('keydown', (e) => {
        if (modal.classList.contains('food-hidden')) return;
        if (e.key === 'Escape') closeModal();
    });

    // click outside dialog closes
    modal.addEventListener('click', (e) => {
        if (modal.classList.contains('food-hidden')) return;
        if (dialog && dialog.contains(e.target)) return;
        closeModal();
    });
})();
</script>
