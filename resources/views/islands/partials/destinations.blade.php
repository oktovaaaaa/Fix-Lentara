{{-- resources/views/islands/partials/destinations.blade.php --}}

@php
    // ===============================
    // SAFETY DEFAULTS (JANGAN HAPUS)
    // ===============================
    $tribeKey = $tribeKey ?? '';
    $tribeDestinations = $tribeDestinations ?? collect();

    if (!($tribeDestinations instanceof \Illuminate\Support\Collection)) {
        $tribeDestinations = collect($tribeDestinations);
    }

    $clampRating = function ($rating) {
        if ($rating === null || $rating === '') return 0.0;
        $n = (float) $rating;
        if ($n < 0) $n = 0;
        if ($n > 5) $n = 5;
        return $n;
    };

    $ratingParts = function ($rating) use ($clampRating) {
        $n = $clampRating($rating);
        $full = (int) floor($n);
        $dec  = $n - $full;

        // sesuai request:
        // 4.5 => 4 full + 1 half
        // 4.9 => 4 full + 1 half (kalau mau 5 full, bilang)
        $half = $dec >= 0.5 ? 1 : 0;

        $empty = 5 - $full - $half;
        if ($empty < 0) $empty = 0;

        return [$full, $half, $empty, $n];
    };

    $fmtRating = function ($rating) use ($clampRating) {
        $n = $clampRating($rating);
        return rtrim(rtrim(number_format($n, 1, '.', ''), '0'), '.');
    };

    // urutkan rating tertinggi dulu
    $sorted = $tribeDestinations->sortByDesc(function ($d) use ($clampRating) {
        return $clampRating($d->rating ?? 0);
    })->values();

    $featured = $sorted->first();
    $others   = $sorted->slice(1)->values();

    $getImg = function ($d) {
        return $d->image_display_url ?? null;
    };
@endphp

<section id="destinations" class="py-12">

    {{-- IMPORTANT: neon-title/title-decoration/neon-subtitle pakai CSS GLOBAL dari islands.blade --}}
    <h2 class="neon-title">
        Destinasi Budaya Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}
    </h2>
    <div class="title-decoration"></div>
    <p class="neon-subtitle">
        Rekomendasi tempat dan pengalaman budaya yang berkaitan dengan Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}.
    </p>

    @if($sorted->count())

        {{-- ================= LAYOUT: KIRI FEATURED + KANAN GRID 2-VERTICAL (SCROLL HORIZONTAL) ================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- ================= KIRI: FEATURED (RATING TERTINGGI) ================= --}}
            <div class="lg:col-span-7">
                @php
                    $d = $featured;
                    [$full, $half, $empty, $n] = $ratingParts($d->rating ?? 0);
                    $img = $getImg($d);
                @endphp

                <article
                    class="dest-featured-card"
                    role="button"
                    tabindex="0"
                    data-destination-modal-trigger
                    data-id="{{ $d->id }}"
                    data-name="{{ e($d->name ?? '') }}"
                    data-location="{{ e($d->location ?? '') }}"
                    data-description="{{ e($d->description ?? '') }}"
                    data-rating="{{ e($n) }}"
                    data-ratingtext="{{ e($fmtRating($d->rating ?? 0)) }}"
                    data-image="{{ e($img ?? '') }}"
                >
                    <div class="dest-featured-media">
                        @if($img)
                            <img
                                src="{{ $img }}"
                                alt="{{ $d->name }}"
                                class="dest-featured-img"
                                loading="lazy"
                            >
                        @else
                            <div class="dest-featured-noimg">
                                Tidak ada gambar
                            </div>
                        @endif

                        {{-- Rating badge (style mirip foods) --}}
                        <div class="dest-rating-badge" aria-label="Rating {{ $fmtRating($d->rating ?? 0) }} dari 5">
                            <div class="dest-rating-stars" aria-hidden="true">
                                {{-- full --}}
                                @for($i=0; $i < $full; $i++)
                                    <svg class="dest-star dest-star-full" viewBox="0 0 20 20" aria-hidden="true">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor

                                {{-- half --}}
                                @if($half === 1)
                                    <span class="dest-star-half" aria-hidden="true">
                                        <svg class="dest-star dest-star-empty" viewBox="0 0 20 20" aria-hidden="true">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="dest-star-halfFill" aria-hidden="true">
                                            <svg class="dest-star dest-star-full" viewBox="0 0 20 20" aria-hidden="true">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </span>
                                    </span>
                                @endif

                                {{-- empty --}}
                                @for($i=0; $i < $empty; $i++)
                                    <svg class="dest-star dest-star-empty" viewBox="0 0 20 20" aria-hidden="true">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>

                            <span class="dest-rating-num">{{ $fmtRating($d->rating ?? 0) }}</span>
                        </div>

                        {{-- overlay title --}}
                        <div class="dest-featured-overlay">
                            <div class="dest-featured-title">{{ $d->name }}</div>
                            @if($d->location)
                                <div class="dest-featured-loc">
                                    <svg class="dest-ico" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $d->location }}
                                </div>
                            @endif
                            <div class="dest-featured-hint">Klik untuk detail</div>
                        </div>
                    </div>

                    <div class="dest-featured-body">
                        @if($d->description)
                            <p class="dest-featured-desc">
                                {{ $d->description }}
                            </p>
                        @else
                            <p class="dest-featured-desc">Deskripsi belum tersedia.</p>
                        @endif
                    </div>
                </article>
            </div>

            {{-- ================= KANAN: 2 VERTIKAL (2 ROWS) + SCROLL HORIZONTAL (2 ITEM PER "COLUMN") ================= --}}
            <div class="lg:col-span-5">
                <div class="dest-right-head">
                    <div class="dest-right-title">Destinasi Lainnya</div>
                    <div class="dest-right-hint">Scroll ke kanan →</div>
                </div>

                @if($others->count())
                    <div class="dest-right-wrap">
                        <div class="dest-right-scroller" aria-label="Destinasi lainnya (scroll horizontal)">
                            @foreach($others as $d)
                                @php
                                    [$full, $half, $empty, $n] = $ratingParts($d->rating ?? 0);
                                    $img = $getImg($d);
                                @endphp

                                <article
                                    class="dest-mini-card"
                                    role="button"
                                    tabindex="0"
                                    data-destination-modal-trigger
                                    data-id="{{ $d->id }}"
                                    data-name="{{ e($d->name ?? '') }}"
                                    data-location="{{ e($d->location ?? '') }}"
                                    data-description="{{ e($d->description ?? '') }}"
                                    data-rating="{{ e($n) }}"
                                    data-ratingtext="{{ e($fmtRating($d->rating ?? 0)) }}"
                                    data-image="{{ e($img ?? '') }}"
                                >
                                    <div class="dest-mini-top">
                                        <div class="dest-mini-thumb">
                                            @if($img)
                                                <img src="{{ $img }}" alt="{{ $d->name }}" class="dest-mini-img" loading="lazy">
                                            @else
                                                <div class="dest-mini-noimg">No image</div>
                                            @endif
                                        </div>

                                        <div class="dest-mini-meta">
                                            <div class="dest-mini-name">{{ $d->name }}</div>

                                            <div class="dest-mini-pills">
                                                @if($d->location)
                                                    <span class="dest-pill">
                                                        <svg class="dest-pill-ico" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        {{ $d->location }}
                                                    </span>
                                                @endif

                                                <span class="dest-pill dest-pill-rating" aria-label="Rating {{ $fmtRating($d->rating ?? 0) }} dari 5">
                                                    <svg class="dest-pill-ico" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                    {{ $fmtRating($d->rating ?? 0) }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- badge stars kecil (pojok) --}}
                                        <div class="dest-mini-badge">
                                            <div class="dest-mini-stars" aria-hidden="true">
                                                @for($i=0; $i < $full; $i++)
                                                    <svg class="dest-star dest-star-full" viewBox="0 0 20 20" aria-hidden="true">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor

                                                @if($half === 1)
                                                    <span class="dest-star-half" aria-hidden="true">
                                                        <svg class="dest-star dest-star-empty" viewBox="0 0 20 20" aria-hidden="true">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                        <span class="dest-star-halfFill" aria-hidden="true">
                                                            <svg class="dest-star dest-star-full" viewBox="0 0 20 20" aria-hidden="true">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                        </span>
                                                    </span>
                                                @endif

                                                @for($i=0; $i < $empty; $i++)
                                                    <svg class="dest-star dest-star-empty" viewBox="0 0 20 20" aria-hidden="true">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="dest-mini-rate">{{ $fmtRating($d->rating ?? 0) }}</span>
                                        </div>
                                    </div>

                                    <p class="dest-mini-desc">
                                        {{ $d->description ? $d->description : 'Deskripsi belum tersedia.' }}
                                    </p>

                                    <div class="dest-mini-action">
                                        <span class="dest-mini-cta">
                                            Lihat detail
                                            <svg class="dest-mini-cta-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </span>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        {{-- scrollbar cantik (orange) --}}
                        <style>
                            #destinations .dest-right-scroller::-webkit-scrollbar { height: 10px; }
                            #destinations .dest-right-scroller::-webkit-scrollbar-track { background: rgba(255,255,255,0.06); border-radius: 999px; }
                            #destinations .dest-right-scroller::-webkit-scrollbar-thumb { background: rgba(255,107,0,0.60); border-radius: 999px; }
                            #destinations .dest-right-scroller::-webkit-scrollbar-thumb:hover { background: rgba(255,107,0,0.78); }
                        </style>
                    </div>
                @else
                    <div class="dest-empty">
                        Belum ada destinasi lainnya untuk {{ $tribeKey }}.
                    </div>
                @endif
            </div>
        </div>

        {{-- ================= MODAL POPUP DETAIL (DINAMIS) ================= --}}
        <div id="destinationModal" class="dest-modal dest-hidden" aria-hidden="true">
            <div class="dest-modal-backdrop" data-dest-close></div>

            <div class="dest-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="destModalTitle">
                <button type="button" class="dest-modal-close" data-dest-close aria-label="Tutup">✕</button>

                <div class="dest-modal-grid">
                    <div class="dest-modal-media">
                        <div class="dest-modal-imgWrap">
                            <img id="destModalImg" src="" alt="" class="dest-modal-img" style="display:none;">
                            <div id="destModalNoImg" class="dest-modal-noimg" style="display:none;">Tidak ada gambar</div>
                        </div>

                        <div class="dest-modal-badge">
                            <div id="destModalStars" class="dest-modal-stars" aria-hidden="true"></div>
                            <span id="destModalRatingText" class="dest-modal-ratingText"></span>
                        </div>
                    </div>

                    <div class="dest-modal-body">
                        <div class="dest-modal-head">
                            <h3 id="destModalTitle" class="dest-modal-title">—</h3>

                            <div id="destModalLoc" class="dest-modal-loc dest-hidden">
                                <svg class="dest-ico" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span id="destModalLocText"></span>
                            </div>
                        </div>

                        <p id="destModalDesc" class="dest-modal-desc"></p>

                        <div class="dest-modal-actions">
                            <button type="button" class="dest-modal-closeBtn" data-dest-close-btn>
                                Tutup
                                <span class="dest-modal-closeGlow" aria-hidden="true"></span>
                            </button>
                        </div>

                        <div class="dest-modal-footnote">
                            Tip: klik di luar modal atau tekan ESC untuk menutup.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <p class="text-sm text-[var(--muted)]">
            Konten destinasi untuk {{ $tribeKey }} belum diinput dari admin.
        </p>
    @endif
</section>

{{-- =========================================================
   STYLES (SCOPED) — mirip tone ai-foods (orange glow)
   Semua selector diawali #destinations
========================================================= --}}
<style>
/* ===== base hidden helper ===== */
#destinations .dest-hidden{ display:none !important; }

/* =========================================================
   FEATURED CARD (KIRI)
========================================================= */
#destinations .dest-featured-card{
    border-radius: 24px;
    border: 1px solid color-mix(in oklab, var(--line) 78%, transparent);
    background: linear-gradient(135deg, rgba(255,107,0,.12), rgba(255,170,107,.08));
    box-shadow: 0 18px 52px rgba(0,0,0,.22);
    overflow: hidden;
    cursor: pointer;
    transition: transform .25s ease, box-shadow .25s ease, filter .25s ease;
    position: relative;
}

#destinations .dest-featured-card:hover{
    transform: translateY(-6px);
    box-shadow: 0 26px 70px rgba(0,0,0,.28), 0 0 36px rgba(255,107,0,.16);
    filter: saturate(1.06);
}

#destinations .dest-featured-media{
    position: relative;
    height: 380px;
}

@media (max-width: 640px){
    #destinations .dest-featured-media{ height: 320px; }
}

#destinations .dest-featured-img{
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .6s ease;
}
#destinations .dest-featured-card:hover .dest-featured-img{
    transform: scale(1.04);
}

#destinations .dest-featured-noimg{
    width: 100%;
    height: 100%;
    display:flex;
    align-items:center;
    justify-content:center;
    color: var(--muted);
    background: rgba(0,0,0,.18);
}

/* overlay bottom */
#destinations .dest-featured-overlay{
    position: absolute;
    inset-inline: 0;
    bottom: 0;
    padding: 18px 18px 16px;
    background: linear-gradient(to top, rgba(0,0,0,.76), rgba(0,0,0,.32), transparent);
}

#destinations .dest-featured-title{
    font-size: 1.25rem;
    font-weight: 900;
    color: #fff;
    line-height: 1.15;
    margin-bottom: 6px;
    overflow-wrap: anywhere;
}

#destinations .dest-featured-loc{
    display:inline-flex;
    align-items:center;
    gap: 8px;
    font-size: .92rem;
    color: rgba(255,255,255,.85);
    margin-bottom: 6px;
}

#destinations .dest-featured-hint{
    font-size: .78rem;
    font-weight: 800;
    color: rgba(255,255,255,.72);
}

#destinations .dest-ico{ width: 16px; height: 16px; opacity: .9; }

#destinations .dest-featured-body{
    padding: 16px 18px 18px;
    background: linear-gradient(180deg, color-mix(in oklab, var(--card) 86%, transparent), var(--card));
}

#destinations .dest-featured-desc{
    margin: 0;
    color: var(--muted);
    font-size: .95rem;
    line-height: 1.65;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    overflow-wrap: anywhere;
}

/* =========================================================
   RATING BADGE (mimik food-rating-badge)
========================================================= */
#destinations .dest-rating-badge{
    position:absolute;
    top: 14px;
    left: 14px;
    display:inline-flex;
    align-items:center;
    gap: 10px;
    padding: 8px 10px;
    border-radius: 999px;
    background: rgba(255,255,255,.92);
    border: 1px solid rgba(15,23,42,.08);
    box-shadow: 0 12px 26px rgba(0,0,0,.18);
    z-index: 2;
}

html[data-theme="dark"] #destinations .dest-rating-badge{
    background: rgba(2,6,23,.72);
    border-color: rgba(148,163,184,.18);
}

#destinations .dest-rating-stars{
    display:inline-flex;
    align-items:center;
    gap: 3px;
}

#destinations .dest-star{
    width: 14px;
    height: 14px;
    display:block;
}

#destinations .dest-star-full{
    fill: #fbbf24;
    filter: drop-shadow(0 6px 12px rgba(251,191,36,.18));
}

#destinations .dest-star-empty{
    fill: rgba(148,163,184,.45);
}
html[data-theme="dark"] #destinations .dest-star-empty{
    fill: rgba(148,163,184,.35);
}

#destinations .dest-star-half{
    position: relative;
    width: 14px;
    height: 14px;
    display:inline-block;
}
#destinations .dest-star-half .dest-star{
    position:absolute;
    inset:0;
}
#destinations .dest-star-halfFill{
    position:absolute;
    inset:0;
    width: 50%;
    overflow:hidden;
}

#destinations .dest-rating-num{
    font-size: .92rem;
    line-height: 1;
    font-weight: 900;
    color: var(--txt-body);
}

/* =========================================================
   RIGHT HEADER
========================================================= */
#destinations .dest-right-head{
    display:flex;
    align-items:center;
    justify-content: space-between;
    margin-bottom: 10px;
}

#destinations .dest-right-title{
    font-size: .98rem;
    font-weight: 900;
    color: var(--txt-body);
}

#destinations .dest-right-hint{
    font-size: .78rem;
    font-weight: 800;
    color: var(--muted);
}

/* =========================================================
   RIGHT SCROLLER (2 ROWS GRID, HORIZONTAL SCROLL)
   - Ini kunci: grid-auto-flow: column
   - grid-template-rows: repeat(2, ...)
   - setiap "kolom" = 2 card (atas + bawah)
========================================================= */
#destinations .dest-right-wrap{
    border-radius: 22px;
    border: 1px solid color-mix(in oklab, var(--line) 85%, transparent);
    background: linear-gradient(135deg, rgba(255,107,0,.08), rgba(148,163,184,.06));
    padding: 12px;
    box-shadow: 0 18px 50px rgba(0,0,0,.18);
}

html[data-theme="dark"] #destinations .dest-right-wrap{
    background: rgba(2,6,23,.20);
}

#destinations .dest-right-scroller{
    display: grid;
    grid-auto-flow: column;
    grid-template-rows: repeat(2, minmax(0, 1fr));
    grid-auto-columns: minmax(320px, 1fr);
    gap: 12px;
    overflow-x: auto;
    overflow-y: hidden;
    padding-bottom: 10px;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 1024px){
    #destinations .dest-right-scroller{
        grid-auto-columns: minmax(300px, 1fr);
    }
}
@media (max-width: 520px){
    #destinations .dest-right-scroller{
        grid-auto-columns: minmax(260px, 1fr);
        grid-template-rows: repeat(2, minmax(0, 1fr));
    }
}

/* =========================================================
   MINI CARD (KANAN)
========================================================= */
#destinations .dest-mini-card{
    scroll-snap-align: start;
    border-radius: 18px;
    border: 1px solid color-mix(in oklab, var(--line) 82%, transparent);
    background:
        linear-gradient(135deg, rgba(255,107,0,.10), rgba(255,170,107,.06)),
        linear-gradient(180deg, var(--card), color-mix(in oklab, var(--card) 88%, transparent));
    padding: 12px;
    box-shadow: 0 12px 34px rgba(0,0,0,.14);
    cursor: pointer;
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease, filter .22s ease;
    position: relative;
    overflow: hidden;
}

#destinations .dest-mini-card::before{
    content:"";
    position:absolute;
    inset:-2px;
    background: radial-gradient(380px 120px at 15% 0%, rgba(255,107,0,.18), transparent 60%);
    pointer-events:none;
    opacity:.9;
}

#destinations .dest-mini-card > *{ position: relative; z-index: 1; }

#destinations .dest-mini-card:hover{
    transform: translateY(-4px);
    box-shadow: 0 18px 42px rgba(0,0,0,.18), 0 0 26px rgba(255,107,0,.14);
    border-color: rgba(255,107,0,.35);
    filter: saturate(1.05);
}

#destinations .dest-mini-top{
    display:flex;
    align-items:flex-start;
    gap: 12px;
    position: relative;
    padding-right: 92px; /* kasih ruang untuk badge stars */
}

#destinations .dest-mini-thumb{
    width: 74px;
    height: 74px;
    border-radius: 16px;
    overflow:hidden;
    background: rgba(0,0,0,.12);
    border: 1px solid rgba(255,107,0,.18);
    flex-shrink: 0;
}

#destinations .dest-mini-img{
    width:100%;
    height:100%;
    object-fit: cover;
    display:block;
}

#destinations .dest-mini-noimg{
    width:100%;
    height:100%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:.75rem;
    color: var(--muted);
}

#destinations .dest-mini-meta{
    flex: 1;
    min-width:0;
}

#destinations .dest-mini-name{
    font-size: .98rem;
    font-weight: 900;
    color: var(--txt-body);
    line-height: 1.15;
    margin-bottom: 6px;
    white-space: nowrap;
    overflow:hidden;
    text-overflow: ellipsis;
}

#destinations .dest-mini-pills{
    display:flex;
    flex-wrap:wrap;
    gap: 8px;
}

#destinations .dest-pill{
    display:inline-flex;
    align-items:center;
    gap: 6px;
    font-size: .72rem;
    font-weight: 800;
    color: color-mix(in oklab, var(--txt-body) 75%, var(--muted));
    background: rgba(255,255,255,.55);
    border: 1px solid rgba(15,23,42,.06);
    padding: 5px 10px;
    border-radius: 999px;
    max-width: 100%;
}

html[data-theme="dark"] #destinations .dest-pill{
    background: rgba(2,6,23,.40);
    border-color: rgba(148,163,184,.18);
    color: color-mix(in oklab, var(--txt-body) 86%, var(--muted));
}

#destinations .dest-pill-ico{
    width: 14px;
    height: 14px;
    opacity: .85;
}

#destinations .dest-pill-rating{
    background: rgba(255,107,0,.12);
    border-color: rgba(255,107,0,.22);
}
html[data-theme="dark"] #destinations .dest-pill-rating{
    background: rgba(255,107,0,.14);
    border-color: rgba(148,163,184,.18);
}

/* badge kecil kanan atas mini card */
#destinations .dest-mini-badge{
    position:absolute;
    top: 10px;
    right: 10px;
    display:inline-flex;
    align-items:center;
    gap: 8px;
    padding: 8px 10px;
    border-radius: 999px;
    background: rgba(255,255,255,.92);
    border: 1px solid rgba(15,23,42,.08);
    box-shadow: 0 12px 26px rgba(0,0,0,.14);
}

html[data-theme="dark"] #destinations .dest-mini-badge{
    background: rgba(2,6,23,.72);
    border-color: rgba(148,163,184,.18);
}

#destinations .dest-mini-stars{
    display:inline-flex;
    align-items:center;
    gap: 3px;
}
#destinations .dest-mini-stars .dest-star{
    width: 12px;
    height: 12px;
}
#destinations .dest-mini-rate{
    font-size: .78rem;
    font-weight: 900;
    color: var(--txt-body);
}

/* desc */
#destinations .dest-mini-desc{
    margin: 10px 0 10px;
    color: var(--muted);
    font-size: .88rem;
    line-height: 1.55;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    overflow-wrap: anywhere;
}

/* CTA feel */
#destinations .dest-mini-action{
    display:flex;
    justify-content:flex-start;
}

#destinations .dest-mini-cta{
    display:inline-flex;
    align-items:center;
    gap: 10px;
    font-weight: 900;
    font-size: .85rem;
    color: #fff;
    background: linear-gradient(135deg, #ff6b00, #ff8c42);
    padding: 10px 12px;
    border-radius: 14px;
    box-shadow: 0 14px 28px rgba(0,0,0,.10);
    transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
}

#destinations .dest-mini-card:hover .dest-mini-cta{
    transform: translateY(-1px);
    box-shadow: 0 18px 40px rgba(0,0,0,.14), 0 0 24px rgba(255,107,0,.14);
    filter: saturate(1.05);
}

#destinations .dest-mini-cta-ico{
    width: 18px;
    height: 18px;
    opacity: .95;
}

/* empty */
#destinations .dest-empty{
    padding: 16px 12px;
    border-radius: 18px;
    border: 1px solid var(--line);
    background: rgba(255,107,0,.08);
    color: var(--muted);
}

/* =========================================================
   MODAL (theme mirip foods)
========================================================= */
#destinations .dest-modal{
    position: fixed;
    inset: 0;
    z-index: 99999;
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 18px;
}

#destinations .dest-modal-backdrop{
    position:absolute;
    inset:0;
    background: rgba(0,0,0,.55);
    backdrop-filter: blur(8px);
    z-index: 1;
}

#destinations .dest-modal-dialog{
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

#destinations .dest-modal-close{
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
html[data-theme="dark"] #destinations .dest-modal-close{
    background: rgba(2,6,23,.70);
    border-color: rgba(255,107,0,.35);
    color: #ff8c42;
}
#destinations .dest-modal-close:hover{
    background: rgba(255,107,0,.95);
    color: #fff;
    transform: rotate(90deg);
    border-color: rgba(255,107,0,.95);
    box-shadow: 0 14px 30px rgba(0,0,0,.28), 0 0 26px rgba(255,107,0,.22);
}

#destinations .dest-modal-grid{
    display:grid;
    grid-template-columns: 1.05fr 1fr;
    gap: 0;
    height: 100%;
    min-height: 0;
}

#destinations .dest-modal-media{
    position: relative;
    background: linear-gradient(135deg, rgba(255,255,255,.55), rgba(255,255,255,.25));
    border-right: 1px solid var(--line);
    min-height: 0;
}
html[data-theme="dark"] #destinations .dest-modal-media{
    background: rgba(2,6,23,.25);
}

#destinations .dest-modal-imgWrap{
    padding: 18px;
    height: 100%;
    display:flex;
    align-items:center;
    justify-content:center;
}
#destinations .dest-modal-img{
    width: 100%;
    height: 100%;
    max-height: 100%;
    object-fit: cover;
    border-radius: 18px;
    border: 1px solid rgba(15,23,42,.06);
}
#destinations .dest-modal-noimg{
    width: 100%;
    height: 100%;
    border-radius: 18px;
    display:flex;
    align-items:center;
    justify-content:center;
    color: var(--muted);
    background: rgba(0,0,0,.12);
    border: 1px solid rgba(255,107,0,.18);
}

#destinations .dest-modal-badge{
    position:absolute;
    left: 18px;
    top: 18px;
    display:inline-flex;
    align-items:center;
    gap: 10px;
    padding: 8px 10px;
    border-radius: 999px;
    background: rgba(255,255,255,.92);
    border: 1px solid rgba(15,23,42,.08);
    box-shadow: 0 12px 26px rgba(0,0,0,.14);
}
html[data-theme="dark"] #destinations .dest-modal-badge{
    background: rgba(2,6,23,.72);
    border-color: rgba(148,163,184,.18);
}

#destinations .dest-modal-stars{
    display:inline-flex;
    align-items:center;
    gap: 3px;
}
#destinations .dest-modal-stars .dest-star{
    width: 16px;
    height: 16px;
}
#destinations .dest-modal-ratingText{
    font-size: .95rem;
    font-weight: 900;
    color: var(--txt-body);
    line-height: 1;
}

#destinations .dest-modal-body{
    padding: 20px 20px 18px;
    overflow:auto;
    min-height: 0;
}

#destinations .dest-modal-head{
    display:flex;
    flex-direction:column;
    gap: 10px;
    margin-bottom: 10px;
}

#destinations .dest-modal-title{
    font-size: 1.4rem;
    font-weight: 900;
    margin: 0;
    color: var(--txt-body);
    line-height:1.2;
    overflow-wrap: anywhere;
}

#destinations .dest-modal-loc{
    display:inline-flex;
    align-items:center;
    gap: 8px;
    color: var(--muted);
    font-weight: 750;
}

#destinations .dest-modal-desc{
    margin: 8px 0 14px;
    color: var(--muted);
    line-height: 1.65;
    font-size: .95rem;
    overflow-wrap: anywhere;
}

#destinations .dest-modal-actions{
    display:flex;
    gap: 12px;
    align-items:center;
    flex-wrap: wrap;
    margin-top: 10px;
}

/* button tutup (animasi mirip foods) */
#destinations .dest-modal-closeBtn{
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

#destinations .dest-modal-closeBtn:hover{
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 20px 44px rgba(0,0,0,.22), 0 0 26px rgba(255,107,0,.20);
    filter: saturate(1.06);
}

#destinations .dest-modal-closeBtn:active{
    transform: translateY(0) scale(.98);
}

#destinations .dest-modal-closeGlow{
    position:absolute;
    inset:-2px;
    background: radial-gradient(220px 90px at 20% 0%, rgba(255,255,255,.28), transparent 60%);
    opacity: 0;
    transition: opacity .2s ease;
    pointer-events:none;
}

#destinations .dest-modal-closeBtn:hover .dest-modal-closeGlow{
    opacity: 1;
}

#destinations .dest-modal-footnote{
    margin-top: 14px;
    font-size: .8rem;
    color: var(--muted);
}

/* responsive modal */
@media (max-width: 720px){
    #destinations .dest-modal-grid{ grid-template-columns: 1fr; }
    #destinations .dest-modal-media{
        border-right: 0;
        border-bottom: 1px solid var(--line);
        height: 280px;
    }
    #destinations .dest-modal-imgWrap{ padding: 14px; }
}
</style>

{{-- =========================================================
   SCRIPT — open/close modal + fill content + render stars (MAX 5)
========================================================= --}}
<script>
(function () {
    const modal = document.getElementById('destinationModal');
    if (!modal) return;

    const dialog = modal.querySelector('.dest-modal-dialog');

    const imgEl   = document.getElementById('destModalImg');
    const noImgEl = document.getElementById('destModalNoImg');

    const titleEl = document.getElementById('destModalTitle');
    const descEl  = document.getElementById('destModalDesc');

    const locWrap = document.getElementById('destModalLoc');
    const locTxt  = document.getElementById('destModalLocText');

    const starsEl = document.getElementById('destModalStars');
    const rateTxt = document.getElementById('destModalRatingText');

    const headerEl = document.getElementById('top');
    let headerPrev = null;
    let lastFocus = null;

    function clampRating(n) {
        n = parseFloat(n || 0);
        if (isNaN(n)) n = 0;
        if (n < 0) n = 0;
        if (n > 5) n = 5;
        return n;
    }

    function fmtRating(n){
        const x = clampRating(n);
        return (Math.round(x * 10) / 10).toString().replace(/\.0$/, '');
    }

    function starSVG(type, size){
        const px = size || 16;
        const fill = type === 'full' ? '#fbbf24' : 'rgba(148,163,184,.45)';
        return `
            <svg width="${px}" height="${px}" viewBox="0 0 20 20" aria-hidden="true" style="display:block;">
                <path fill="${fill}" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
        `;
    }

    function halfStarSVG(size){
        const px = size || 16;
        return `
            <span aria-hidden="true" style="width:${px}px;height:${px}px;position:relative;display:inline-block;">
                ${starSVG('empty', px)}
                <span aria-hidden="true" style="position:absolute;inset:0;width:50%;overflow:hidden;">
                    ${starSVG('full', px)}
                </span>
            </span>
        `;
    }

    function renderStars(container, rating){
        const n = clampRating(rating);
        const full = Math.floor(n);
        const dec = n - full;
        const half = dec >= 0.5 ? 1 : 0;
        let empty = 5 - full - half;
        if (empty < 0) empty = 0;

        let html = '';
        for (let i=0; i<full; i++) html += starSVG('full', 16);
        if (half === 1) html += halfStarSVG(16);
        for (let i=0; i<empty; i++) html += starSVG('empty', 16);

        container.innerHTML = html;
    }

    function hideNavbar(){
        if (!headerEl) return;
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

    function showNavbar(){
        if (!headerEl || !headerPrev) return;
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
        const desc = card.getAttribute('data-description') || '';
        const img  = card.getAttribute('data-image') || '';
        const loc  = card.getAttribute('data-location') || '';
        const rating = card.getAttribute('data-rating') || '0';

        titleEl.textContent = name;
        descEl.textContent  = desc ? desc : 'Deskripsi belum tersedia.';

        if (loc && loc.trim() !== '') {
            locTxt.textContent = loc;
            locWrap.classList.remove('dest-hidden');
        } else {
            locTxt.textContent = '';
            locWrap.classList.add('dest-hidden');
        }

        if (img && img.trim() !== '') {
            imgEl.src = img;
            imgEl.alt = name;
            imgEl.style.display = '';
            noImgEl.style.display = 'none';
        } else {
            imgEl.src = '';
            imgEl.alt = '';
            imgEl.style.display = 'none';
            noImgEl.style.display = '';
        }

        renderStars(starsEl, rating);
        rateTxt.textContent = fmtRating(rating) + ' / 5';

        hideNavbar();

        modal.classList.remove('dest-hidden');
        modal.setAttribute('aria-hidden', 'false');

        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';

        const xBtn = modal.querySelector('.dest-modal-close');
        if (xBtn) xBtn.focus();
    }

    function closeModal(){
        modal.classList.add('dest-hidden');
        modal.setAttribute('aria-hidden', 'true');

        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';

        showNavbar();

        if (lastFocus && typeof lastFocus.focus === 'function') {
            lastFocus.focus();
        }
    }

    // bind triggers
    document.querySelectorAll('[data-destination-modal-trigger]').forEach(card => {
        card.addEventListener('click', (e) => {
            // kalau ada link di dalam card (kalau nanti ditambah), jangan buka modal
            const a = e.target.closest('a');
            if (a) return;
            openModalFromCard(card);
        });

        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openModalFromCard(card);
            }
        });
    });

    // close events
    modal.querySelectorAll('[data-dest-close]').forEach(el => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
            closeModal();
        }, true);
    });

    const closeBtn2 = modal.querySelector('[data-dest-close-btn]');
    if (closeBtn2) {
        closeBtn2.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
            closeModal();
        }, true);
    }

    document.addEventListener('keydown', (e) => {
        if (modal.classList.contains('dest-hidden')) return;
        if (e.key === 'Escape') closeModal();
    });

    modal.addEventListener('click', (e) => {
        if (modal.classList.contains('dest-hidden')) return;
        if (dialog && dialog.contains(e.target)) return;
        closeModal();
    });
})();
</script>
