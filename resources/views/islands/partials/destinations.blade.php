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

        // 4.5 => 4 full + 1 half
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
    // 1) eksternal dulu
    if (!empty($d->image_url)) return $d->image_url;

    // 2) kalau upload (storage)
    if (!empty($d->image_path)) return asset('storage/' . ltrim($d->image_path, '/'));

    return null;
};


    // 360 helpers (AMAN)
    $getPanoEmbed = function ($d) {
        // Embed iframe WAJIB: https://www.google.com/maps/embed?pb=...
        return $d->pano_embed_url ?? ($d->pano_embed_display_url ?? null);
    };
    $getPanoMaps = function ($d) {
        return $d->pano_maps_url ?? ($d->pano_maps_display_url ?? null);
    };
    $getPanoLabel = function ($d) {
        return $d->pano_label ?? null;
    };

    // themes rotation
    $themes = ['sunset','orange','mint','violet','sky','rose'];
@endphp

<style>
/* =========================================================
   DESTINATIONS — NEON RING + UI (DISATUKAN DI FILE INI)
   NOTE: Semua selector diawali #destinations
========================================================= */
@property --dest-neon-angle {
  syntax: "<angle>";
  inherits: false;
  initial-value: 0deg;
}

#destinations .dest-hidden{ display:none !important; }

/* THEMES */
#destinations .dest-theme-sunset{
  --destGrad: linear-gradient(135deg, rgba(255, 107, 0, .18), rgba(255, 170, 107, .10));
  --accent: #ff6b00;
  --accent2:#ffb38a;
}
#destinations .dest-theme-orange{
  --destGrad: linear-gradient(135deg, rgba(249, 115, 22, .20), rgba(253, 186, 116, .10));
  --accent: #ff6b00;
  --accent2:#ffb38a;
}
#destinations .dest-theme-mint{
  --destGrad: linear-gradient(135deg, rgba(16, 185, 129, .18), rgba(167, 243, 208, .10));
  --accent: #ff6b00;
  --accent2:#ffb38a;
}
#destinations .dest-theme-violet{
  --destGrad: linear-gradient(135deg, rgba(99, 102, 241, .18), rgba(196, 181, 253, .10));
  --accent: #ff6b00;
  --accent2:#ffb38a;
}
#destinations .dest-theme-sky{
  --destGrad: linear-gradient(135deg, rgba(14, 165, 233, .18), rgba(186, 230, 253, .10));
  --accent: #ff6b00;
  --accent2:#ffb38a;
}
#destinations .dest-theme-rose{
  --destGrad: linear-gradient(135deg, rgba(244, 63, 94, .18), rgba(253, 164, 175, .10));
  --accent: #ff6b00;
  --accent2:#ffb38a;
}

/* =========================================================
   CARD BASE (Featured + Mini)
   FIX UTAMA: neon ring HARUS KELIHATAN DI KIRI & KANAN
========================================================= */
#destinations .dest-featured-card,
#destinations .dest-mini-card{
  position: relative;
  overflow: hidden;
  border-radius: 26px;
  cursor: pointer;

  border: 1px solid color-mix(in oklab, var(--line, rgba(148,163,184,.20)) 78%, transparent);
  background:
    var(--destGrad),
    linear-gradient(180deg, var(--card, rgba(15,23,42,.55)), color-mix(in oklab, var(--card, rgba(15,23,42,.55)) 88%, transparent));

  box-shadow: 0 14px 44px rgba(0,0,0,.14);
  transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease, filter .25s ease;

  /* penting biar pseudo-element neon gak ke-clip aneh */
  isolation: isolate;
}

/* neon ring muter */
#destinations .dest-featured-card::before,
#destinations .dest-mini-card::before{
  content:"";
  position:absolute;

  /* FIX UTAMA: jangan keluar card, biar gak ke-clip overflow:hidden */
  inset: 0;

  border-radius: inherit;

  /* ring ketebalan (boleh 8px / 10px) */
  padding: 5px;

  pointer-events:none;
  z-index: 0;

  background: conic-gradient(
    from var(--dest-neon-angle),
    rgba(249,115,22,0) 0deg,
    rgba(249,115,22,.20) 22deg,
    #f97316 55deg,
    #22d3ee 110deg,
    #34d399 165deg,
    rgba(34,211,238,.20) 220deg,
    #f97316 300deg,
    rgba(249,115,22,0) 360deg
  );

  -webkit-mask:
    linear-gradient(#000 0 0) content-box,
    linear-gradient(#000 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;

  /* blur jangan kebesaran biar tidak “habis” */
  filter: blur(5px);
  opacity: .95;
  animation: dest-neon-spin 7.5s linear infinite;
}

@keyframes dest-neon-spin { to { --dest-neon-angle: 360deg; } }

/* FIX: semua konten card dipaksa di atas ring */
#destinations .dest-featured-card > *,
#destinations .dest-mini-card > *{
  position: relative;
  z-index: 1;
}

#destinations .dest-featured-card:hover{
  transform: translateY(-7px);
  box-shadow: 0 22px 70px rgba(0,0,0,.22), 0 0 36px rgba(255,107,0,.20);
  border-color: rgba(255,107,0,.55);
  filter: saturate(1.06);
}
#destinations .dest-mini-card:hover{
  transform: translateY(-4px);
  box-shadow: 0 20px 55px rgba(0,0,0,.20), 0 0 30px rgba(255,107,0,.14);
  border-color: rgba(255,107,0,.35);
  filter: saturate(1.05);
}
#destinations .dest-featured-card:focus,
#destinations .dest-mini-card:focus{
  outline: none;
  box-shadow: 0 0 0 4px rgba(255,107,0,.18), 0 22px 70px rgba(0,0,0,.22);
}

/* =========================================================
   FEATURED MEDIA
========================================================= */
#destinations .dest-featured-media{ position: relative; height: 385px; }
@media (max-width: 640px){ #destinations .dest-featured-media{ height: 320px; } }

#destinations .dest-featured-img{
  width: 100%; height: 100%;
  object-fit: cover; display: block;
  transition: transform .6s ease, filter .6s ease;
  filter: saturate(1.02);
}
#destinations .dest-featured-card:hover .dest-featured-img{
  transform: scale(1.05);
  filter: saturate(1.08);
}
#destinations .dest-featured-noimg{
  width: 100%; height: 100%;
  display:flex; align-items:center; justify-content:center;
  color: var(--muted, rgba(148,163,184,.9));
  background: rgba(0,0,0,.18);
}

/* overlay bottom */
#destinations .dest-featured-overlay{
  position: absolute;
  inset-inline: 0;
  bottom: 0;
  padding: 18px 18px 16px;
  background: linear-gradient(to top, rgba(0,0,0,.82), rgba(0,0,0,.34), transparent);

  /* FIX: overlay wajib di atas gambar */
  z-index: 2;
}
#destinations .dest-featured-title{
  font-size: 1.35rem;
  font-weight: 950;
  color: #fff;
  line-height: 1.15;
  margin-bottom: 10px;
  overflow-wrap: anywhere;
  letter-spacing: .01em;
  text-shadow: 0 10px 28px rgba(0,0,0,.45);
}
#destinations .dest-featured-mini{ display:flex; flex-wrap:wrap; gap: 8px; margin-bottom: 8px; }
#destinations .dest-pill{
  display:inline-flex; align-items:center; gap: 6px;
  font-size: .78rem; font-weight: 850;
  color: rgba(255,255,255,.88);
  background: rgba(255,255,255,.12);
  border: 1px solid rgba(255,255,255,.14);
  padding: 6px 10px;
  border-radius: 999px;
  max-width: 100%;
  backdrop-filter: blur(10px);
}
#destinations .dest-pill-hot{
  background: rgba(255,107,0,.22);
  border-color: rgba(255,170,107,.25);
}
#destinations .pill-ico{ width: 14px; height: 14px; opacity: .92; }
#destinations .dest-featured-hint{ font-size: .8rem; font-weight: 900; color: rgba(255,255,255,.74); }

/* Featured body */
#destinations .dest-featured-body{
  padding: 16px 18px 18px;
  background: linear-gradient(180deg, color-mix(in oklab, var(--card, rgba(15,23,42,.55)) 86%, transparent), var(--card, rgba(15,23,42,.55)));
}
#destinations .dest-featured-desc{
  margin: 0;
  color: var(--muted, rgba(148,163,184,.9));
  font-size: .95rem;
  line-height: 1.65;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  overflow-wrap: anywhere;
}

/* Icon-only CTA */
#destinations .dest-featured-actions{ margin-top: 12px; display:flex; justify-content:flex-end; }
#destinations .dest-featured-openIcon,
#destinations .dest-mini-openBtn{
  width: 44px; height: 44px;
  border-radius: 14px;
  display:inline-flex; align-items:center; justify-content:center;
  color: #fff;
  background: linear-gradient(135deg, #ff6b00, #ff8c42);
  box-shadow: 0 14px 28px rgba(0,0,0,.14);
  transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
}
#destinations .dest-featured-card:hover .dest-featured-openIcon,
#destinations .dest-mini-card:hover .dest-mini-openBtn{
  transform: translateY(-1px);
  box-shadow: 0 20px 44px rgba(0,0,0,.18), 0 0 26px rgba(255,107,0,.18);
  filter: saturate(1.05);
}
#destinations .dest-open-ico{ width: 20px; height: 20px; opacity: .95; }

/* Rating badge (featured) */
#destinations .dest-rating-badge{
  position:absolute; top: 14px; left: 14px;
  display:inline-flex; align-items:center; gap: 10px;
  padding: 9px 11px;
  border-radius: 999px;
  background: rgba(255,255,255,.92);
  border: 1px solid rgba(15,23,42,.08);
  box-shadow: 0 16px 34px rgba(0,0,0,.20);
  z-index: 3;
  backdrop-filter: blur(10px);
}
html[data-theme="dark"] #destinations .dest-rating-badge{
  background: rgba(2,6,23,.72);
  border-color: rgba(148,163,184,.18);
}
#destinations .dest-rating-stars{ display:inline-flex; align-items:center; gap: 3px; }
#destinations .dest-star{ width: 14px; height: 14px; display:block; }
#destinations .dest-star-full{
  fill: #fbbf24;
  filter:
    drop-shadow(0 10px 18px rgba(251,191,36,.20))
    drop-shadow(0 0 10px rgba(255,107,0,.08));
}
#destinations .dest-star-empty{ fill: rgba(148,163,184,.45); }
html[data-theme="dark"] #destinations .dest-star-empty{ fill: rgba(148,163,184,.35); }
#destinations .dest-star-half{ position: relative; width: 14px; height: 14px; display:inline-block; }
#destinations .dest-star-half .dest-star{ position:absolute; inset:0; }
#destinations .dest-star-halfFill{ position:absolute; inset:0; width: 50%; overflow:hidden; }
#destinations .dest-rating-num{
  font-size: .92rem; line-height: 1; font-weight: 950;
  color: var(--txt-body, rgba(226,232,240,.95)); letter-spacing: .01em;
}

/* =========================================================
   RIGHT WRAP + HEADER
========================================================= */
#destinations .dest-right-wrap{
  border-radius: 22px;
  border: 1px solid color-mix(in oklab, var(--line, rgba(148,163,184,.20)) 85%, transparent);
  background: linear-gradient(135deg, rgba(255,107,0,.08), rgba(148,163,184,.06));
  padding: 12px;
  box-shadow: 0 18px 50px rgba(0,0,0,.18);
}
html[data-theme="dark"] #destinations .dest-right-wrap{ background: rgba(2,6,23,.20); }

#destinations .dest-right-headerInside{
  display:flex; align-items:center; justify-content: space-between;
  margin-bottom: 10px;
  padding: 6px 4px 2px;
}
#destinations .dest-right-title{
  display:inline-flex; align-items:center; gap: 10px;
  font-size: 1rem; font-weight: 950;
  color: var(--txt-body, rgba(226,232,240,.95));
  letter-spacing: .01em;
}
#destinations .dest-right-titleDot{
  width: 10px; height: 10px; border-radius: 999px;
  background: linear-gradient(135deg, #ff6b00, #ff8c42);
  box-shadow: 0 0 18px rgba(255,107,0,.35);
}
#destinations .dest-right-titlePill{
  font-size: .72rem;
  font-weight: 950;
  padding: 6px 10px;
  border-radius: 999px;
  background: rgba(255,107,0,.12);
  border: 1px solid rgba(255,107,0,.22);
  color: color-mix(in oklab, var(--txt-body, rgba(226,232,240,.95)) 75%, var(--muted, rgba(148,163,184,.9)));
}
html[data-theme="dark"] #destinations .dest-right-titlePill{
  background: rgba(255,107,0,.14);
  color: rgba(255,255,255,.84);
  border-color: rgba(255,107,0,.22);
}

/* =========================================================
   RIGHT SCROLLER (3 rows) + SCROLLBAR ORANGE (FIX)
========================================================= */
#destinations .dest-right-scroller{
  display: grid;
  grid-auto-flow: column;
  grid-template-rows: repeat(3, minmax(0, 1fr));
  grid-auto-columns: minmax(340px, 1fr);
  gap: 12px;

  overflow-x: auto;
  overflow-y: hidden;

  padding-bottom: 10px;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;

  /* Firefox scrollbar */
  scrollbar-color: #ff6b00 rgba(255,107,0,.12);
  scrollbar-width: auto;
}

@media (max-width: 1024px){ #destinations .dest-right-scroller{ grid-auto-columns: minmax(320px, 1fr); } }
@media (max-width: 520px){
  #destinations .dest-right-scroller{
    grid-auto-columns: minmax(280px, 1fr);
    grid-template-rows: repeat(3, minmax(0, 1fr));
  }
}

/* Webkit scrollbar (Chrome/Edge/Safari) */
#destinations .dest-right-scroller::-webkit-scrollbar{
  height: 12px; /* terlihat jelas */
}
#destinations .dest-right-scroller::-webkit-scrollbar-track{
  background: rgba(255,107,0,.12);
  border-radius: 999px;
  border: 1px solid rgba(255,107,0,.18);
}
#destinations .dest-right-scroller::-webkit-scrollbar-thumb{
  background: linear-gradient(90deg, #ff6b00, #ff8c42);
  border-radius: 999px;
  border: 2px solid rgba(0,0,0,.18);
}
#destinations .dest-right-scroller::-webkit-scrollbar-thumb:hover{
  background: linear-gradient(90deg, #ff8c42, #ff6b00);
}

/* =========================================================
   MINI CARD LAYOUT
========================================================= */
#destinations .dest-mini-card{
  border-radius: 18px;
  padding: 0;
  min-height: 146px;
  box-shadow: 0 14px 40px rgba(0,0,0,.16);
}

#destinations .dest-mini-row{ display:flex; align-items: stretch; gap: 0; height: 100%; }

#destinations .dest-mini-thumb{
  width: 118px;
  min-width: 118px;
  aspect-ratio: 1 / 1;
  align-self: center;
  margin: 12px 0 12px 12px;
  border-radius: 16px;
  position: relative;
  overflow:hidden;
  background: rgba(0,0,0,.10);
  border: 1px solid rgba(255,107,0,.16);
}
#destinations .dest-mini-img{
  width:100%; height:100%;
  object-fit: cover;
  display:block;
  transform: scale(1.02);
  transition: transform .6s ease, filter .6s ease;
}
#destinations .dest-mini-card:hover .dest-mini-img{
  transform: scale(1.06);
  filter: saturate(1.08);
}
#destinations .dest-mini-noimg{
  width:100%; height:100%;
  display:flex; align-items:center; justify-content:center;
  font-size:.8rem; color: var(--muted, rgba(148,163,184,.9));
  padding: 10px; text-align:center;
}
#destinations .dest-mini-thumbGlow{
  position:absolute; inset:0;
  background: linear-gradient(135deg, rgba(0,0,0,.08), rgba(0,0,0,.42));
  pointer-events:none;
}

#destinations .dest-mini-content{
  flex: 1;
  padding: 12px;
  display:flex;
  flex-direction:column;
  gap: 8px;
  min-width:0;
  position: relative;
}
#destinations .dest-mini-topRow{ display:flex; justify-content:flex-end; }

#destinations .dest-mini-badge{
  display:inline-flex; align-items:center; gap: 8px;
  padding: 7px 9px;
  border-radius: 999px;
  background: rgba(255,255,255,.92);
  border: 1px solid rgba(15,23,42,.08);
  box-shadow: 0 14px 28px rgba(0,0,0,.16);
  backdrop-filter: blur(10px);
  flex-shrink: 0;
}
html[data-theme="dark"] #destinations .dest-mini-badge{
  background: rgba(2,6,23,.72);
  border-color: rgba(148,163,184,.18);
}
#destinations .dest-mini-stars{ display:inline-flex; align-items:center; gap: 3px; }
#destinations .dest-mini-stars .dest-star{ width: 12px; height: 12px; }
#destinations .dest-mini-rate{
  font-size: .78rem;
  font-weight: 950;
  color: var(--txt-body, rgba(226,232,240,.95));
  line-height: 1;
}

#destinations .dest-mini-name{
  font-size: 1.02rem;
  font-weight: 950;
  color: var(--txt-body, rgba(226,232,240,.95));
  line-height: 1.18;
  letter-spacing: .01em;
  min-width:0;
  display:-webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow:hidden;
  text-overflow: ellipsis;
  padding-right: 4px;
}

#destinations .dest-mini-loc{
  display:flex; align-items:center; gap: 8px;
  margin-top: -2px;
}
#destinations .dest-mini-locPin{
  width: 22px; height: 22px;
  border-radius: 999px;
  display:flex; align-items:center; justify-content:center;
  background: rgba(255,107,0,.12);
  border: 1px solid rgba(255,107,0,.22);
  box-shadow: 0 10px 18px rgba(0,0,0,.10);
  flex-shrink:0;
}
#destinations .dest-mini-locIco{ width: 14px; height: 14px; color: #ff6b00; }
html[data-theme="dark"] #destinations .dest-mini-locIco{ color: #ff8c42; }

#destinations .dest-mini-locText{
  font-size: .84rem;
  font-weight: 850;
  color: color-mix(in oklab, var(--txt-body, rgba(226,232,240,.95)) 74%, var(--muted, rgba(148,163,184,.9)));
  white-space: nowrap;
  overflow:hidden;
  text-overflow: ellipsis;
  min-width:0;
}

#destinations .dest-mini-bottomRow{ margin-top: auto; display:flex; justify-content:flex-end; }

/* =========================================================
   MODAL (POPUP DEFAULT) — DIPAKAI JIKA TIDAK ADA 360
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
  background: var(--card, rgba(15,23,42,.78));
  border: 1px solid var(--line, rgba(148,163,184,.22));
  box-shadow: 0 30px 90px rgba(0,0,0,.35);
  overflow:hidden;
  z-index: 2;
  display:flex;
  flex-direction:column;
  pointer-events: auto;
}

/* ===== NEON RING UNTUK MODAL DIALOG (DETAIL) ===== */
#destinations .dest-modal-dialog{
  isolation: isolate; /* penting: layer neon rapi */
}

/* ring neon */
#destinations .dest-modal-dialog::before{
  content:"";
  position:absolute;
  inset: 0;                 /* jangan -7px, nanti ke-clip */
  border-radius: inherit;
  padding: 5px;            /* tebal ring */
  pointer-events:none;
  z-index: 0;

  background: conic-gradient(
    from var(--dest-neon-angle),
    rgba(249,115,22,0) 0deg,
    rgba(249,115,22,.20) 22deg,
    #f97316 55deg,
    #22d3ee 110deg,
    #34d399 165deg,
    rgba(34,211,238,.20) 220deg,
    #f97316 300deg,
    rgba(249,115,22,0) 360deg
  );

  -webkit-mask:
    linear-gradient(#000 0 0) content-box,
    linear-gradient(#000 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;

  filter: blur(5px);
  opacity: .95;
  animation: dest-neon-spin 7.5s linear infinite;
}

/* pastikan isi modal ada di atas ring */
#destinations .dest-modal-dialog > *{
  position: relative;
  z-index: 1;
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
  border-right: 1px solid var(--line, rgba(148,163,184,.22));
  min-height: 0;
}
html[data-theme="dark"] #destinations .dest-modal-media{ background: rgba(2,6,23,.25); }

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
  color: var(--muted, rgba(148,163,184,.9));
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
#destinations .dest-modal-stars{ display:inline-flex; align-items:center; gap: 3px; }
#destinations .dest-modal-stars .dest-star{ width: 16px; height: 16px; }
#destinations .dest-modal-ratingText{
  font-size: .95rem;
  font-weight: 900;
  color: var(--txt-body, rgba(226,232,240,.95));
  line-height: 1;
}
#destinations .dest-modal-body{
  padding: 20px 20px 18px;
  overflow:auto;
  min-height: 0;
}
#destinations .dest-modal-title{
  font-size: 1.4rem;
  font-weight: 900;
  margin: 0;
  color: var(--txt-body, rgba(226,232,240,.95));
  line-height:1.2;
  overflow-wrap: anywhere;
}
#destinations .dest-modal-loc{
  display:inline-flex;
  align-items:center;
  gap: 8px;
  color: var(--muted, rgba(148,163,184,.9));
  font-weight: 750;
}
#destinations .dest-ico{ width: 16px; height: 16px; opacity: .95; }

#destinations .dest-modal-desc{
  margin: 8px 0 14px;
  color: var(--muted, rgba(148,163,184,.9));
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
#destinations .dest-modal-closeBtn:active{ transform: translateY(0) scale(.98); }
#destinations .dest-modal-closeGlow{
  position:absolute;
  inset:-2px;
  background: radial-gradient(220px 90px at 20% 0%, rgba(255,255,255,.28), transparent 60%);
  opacity: 0;
  transition: opacity .2s ease;
  pointer-events:none;
}
#destinations .dest-modal-closeBtn:hover .dest-modal-closeGlow{ opacity: 1; }

#destinations .dest-modal-footnote{
  margin-top: 14px;
  font-size: .8rem;
  color: var(--muted, rgba(148,163,184,.9));
}

/* responsive modal + thumb */
@media (max-width: 720px){
  #destinations .dest-modal-grid{ grid-template-columns: 1fr; }
  #destinations .dest-modal-media{
    border-right: 0;
    border-bottom: 1px solid var(--line, rgba(148,163,184,.22));
    height: 280px;
  }
  #destinations .dest-modal-imgWrap{ padding: 14px; }
  #destinations .dest-mini-thumb{
    width: 110px;
    min-width: 110px;
    margin: 12px 0 12px 12px;
  }
}

/* =========================================================
   FIX: NEON KHUSUS FEATURED (ANTI-HILANG / ANTI-MASK BUG)
========================================================= */
#destinations .dest-featured-neon{
  position: relative;
  border: 1px solid rgba(255,107,0,.35); /* fallback kalau neon mati */
}

/* NEON RING FEATURED PALING AMAN (tanpa mask, tanpa blur) */
#destinations .dest-featured-neon{
  border: 5px solid transparent;          /* ruang untuk ring */
  border-radius: 26px;                     /* pastikan sama */
  background-origin: border-box;

  /* 3 layer:
     1) conic neon = border-box (ring)
     2) var(--destGrad) = padding-box (theme)
     3) body bg = padding-box (dasar card)
  */
  background:
    conic-gradient(
      from var(--dest-neon-angle),
      rgba(249,115,22,0) 0deg,
      rgba(249,115,22,.25) 22deg,
      #f97316 55deg,
      #22d3ee 110deg,
      #34d399 165deg,
      rgba(34,211,238,.25) 220deg,
      #f97316 300deg,
      rgba(249,115,22,0) 360deg
    ) border-box,
    var(--destGrad) padding-box,
    linear-gradient(
      180deg,
      var(--card, rgba(15,23,42,.55)),
      color-mix(in oklab, var(--card, rgba(15,23,42,.55)) 88%, transparent)
    ) padding-box;

  background-clip: border-box, padding-box, padding-box;

  /* animasi ring */
  animation: dest-neon-spin 7.5s linear infinite;
}

/* =========================================================
   FULLSCREEN DETAIL (DIPAKAI JIKA ADA 360)
   - BUKAN "POPUP KECIL": ini 1 layar penuh
   - 360 dibuat besar + tombol zoom + fullscreen
========================================================= */
#destinations .dest-full{
  position: fixed;
  inset: 0;
  z-index: 999999; /* di atas semuanya */
  display: flex;
  flex-direction: column;
  background: rgba(0,0,0,.60);
  backdrop-filter: blur(10px);
}

#destinations .dest-full-shell{
  position: relative;
  margin: 12px;
  flex: 1;
  min-height: 0;
  border-radius: 22px;
  background: var(--card, rgba(15,23,42,.82));
  border: 1px solid var(--line, rgba(148,163,184,.22));
  box-shadow: 0 30px 90px rgba(0,0,0,.45);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  isolation: isolate;
}

/* neon ring */
#destinations .dest-full-shell::before{
  content:"";
  position:absolute;
  inset: 0;
  border-radius: inherit;
  padding: 5px;
  pointer-events:none;
  z-index: 0;

  background: conic-gradient(
    from var(--dest-neon-angle),
    rgba(249,115,22,0) 0deg,
    rgba(249,115,22,.20) 22deg,
    #f97316 55deg,
    #22d3ee 110deg,
    #34d399 165deg,
    rgba(34,211,238,.20) 220deg,
    #f97316 300deg,
    rgba(249,115,22,0) 360deg
  );

  -webkit-mask:
    linear-gradient(#000 0 0) content-box,
    linear-gradient(#000 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;

  filter: blur(6px);
  opacity: .95;
  animation: dest-neon-spin 7.5s linear infinite;
}

/* pastikan isi di atas ring */
#destinations .dest-full-shell > *{ position: relative; z-index: 1; }

#destinations .dest-full-topbar{
  display:flex;
  align-items:center;
  justify-content: space-between;
  gap: 12px;
  padding: 14px 14px 10px;
  border-bottom: 1px solid rgba(148,163,184,.18);
  background: linear-gradient(135deg, rgba(255,107,0,.08), rgba(148,163,184,.06));
}
html[data-theme="dark"] #destinations .dest-full-topbar{ background: rgba(2,6,23,.18); }

#destinations .dest-full-titleWrap{ min-width:0; }
#destinations .dest-full-title{
  margin: 0;
  font-size: 1.25rem;
  font-weight: 950;
  color: var(--txt-body, rgba(226,232,240,.95));
  line-height: 1.15;
  overflow-wrap:anywhere;
}
#destinations .dest-full-sub{
  margin-top: 6px;
  display:flex;
  flex-wrap: wrap;
  gap: 8px;
  align-items:center;
}

#destinations .dest-full-pill{
  display:inline-flex;
  align-items:center;
  gap: 8px;
  padding: 7px 10px;
  border-radius: 999px;
  background: rgba(255,255,255,.92);
  border: 1px solid rgba(15,23,42,.08);
  box-shadow: 0 14px 28px rgba(0,0,0,.12);
  backdrop-filter: blur(10px);
  max-width: 100%;
}
html[data-theme="dark"] #destinations .dest-full-pill{
  background: rgba(2,6,23,.72);
  border-color: rgba(148,163,184,.18);
}

#destinations .dest-full-pillText{
  font-size: .86rem;
  font-weight: 900;
  color: var(--txt-body, rgba(226,232,240,.95));
  white-space: nowrap;
  overflow:hidden;
  text-overflow: ellipsis;
  max-width: 62vw;
}
@media (max-width: 720px){
  #destinations .dest-full-pillText{ max-width: 74vw; }
}

#destinations .dest-full-actions{
  display:flex;
  align-items:center;
  gap: 10px;
  flex-shrink: 0;
}

/* icon buttons */
#destinations .dest-ctrlBtn{
  width: 44px;
  height: 44px;
  border-radius: 14px;
  border: 1px solid rgba(255,107,0,.30);
  background: rgba(255,255,255,.86);
  color: #ff6b00;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  cursor: pointer;
  transition: transform .18s ease, box-shadow .18s ease, background .18s ease, color .18s ease;
  user-select: none;
}
html[data-theme="dark"] #destinations .dest-ctrlBtn{
  background: rgba(2,6,23,.70);
  border-color: rgba(255,107,0,.25);
  color: #ff8c42;
}
#destinations .dest-ctrlBtn:hover{
  transform: translateY(-1px);
  background: rgba(255,107,0,.92);
  color: #fff;
  box-shadow: 0 18px 40px rgba(0,0,0,.25), 0 0 26px rgba(255,107,0,.20);
  border-color: rgba(255,107,0,.92);
}
#destinations .dest-ctrlIco{ width: 20px; height: 20px; opacity: .95; }

#destinations .dest-full-main{
  flex: 1;
  min-height: 0;
  display:grid;
  grid-template-columns: 1.55fr 1fr; /* 360 lebih besar */
  gap: 0;
}

#destinations .dest-full-360{
  position: relative;
  min-height: 0;
  border-right: 1px solid rgba(148,163,184,.18);
  background: rgba(0,0,0,.16);
}

#destinations .dest-iframeStage{
  position:absolute;
  inset: 14px;
  border-radius: 18px;
  border: 1px solid rgba(255,107,0,.18);
  background: rgba(0,0,0,.22);
  overflow: hidden;
}

/* wrapper untuk zoom manual (transform scale) */
#destinations .dest-iframeZoom{
  position:absolute;
  inset: 0;
  transform-origin: 50% 50%;
  will-change: transform;
}

#destinations .dest-iframe{
  width: 100%;
  height: 100%;
  border: 0;
  display:block;
  background: rgba(0,0,0,.10);
}

/* overlay label */
#destinations .dest-360Label{
  position:absolute;
  left: 22px;
  top: 22px;
  z-index: 3;
  display:inline-flex;
  align-items:center;
  gap: 8px;
  padding: 8px 10px;
  border-radius: 999px;
  background: rgba(255,255,255,.92);
  border: 1px solid rgba(15,23,42,.08);
  box-shadow: 0 14px 28px rgba(0,0,0,.16);
  backdrop-filter: blur(10px);
  max-width: calc(100% - 44px);
}
html[data-theme="dark"] #destinations .dest-360Label{
  background: rgba(2,6,23,.72);
  border-color: rgba(148,163,184,.18);
}
#destinations .dest-360LabelText{
  font-size: .86rem;
  font-weight: 950;
  color: var(--txt-body, rgba(226,232,240,.95));
  white-space: nowrap;
  overflow:hidden;
  text-overflow: ellipsis;
}

#destinations .dest-full-info{
  min-height: 0;
  padding: 16px 16px 14px;
  overflow: auto;
}

#destinations .dest-full-aboutTitle{
  font-size: 1.05rem;
  font-weight: 950;
  color: var(--txt-body, rgba(226,232,240,.95));
  margin: 0 0 10px;
}

#destinations .dest-full-aboutText{
  margin: 0 0 14px;
  color: var(--muted, rgba(148,163,184,.9));
  line-height: 1.7;
  font-size: .95rem;
  overflow-wrap: anywhere;
}

#destinations .dest-full-links{
  display:flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 10px;
}

/* CTA link button */
#destinations .dest-linkBtn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap: 8px;
  padding: 10px 12px;
  border-radius: 14px;
  font-weight: 950;
  font-size: .9rem;
  color: #fff;
  background: linear-gradient(135deg, #ff6b00, #ff8c42);
  box-shadow: 0 14px 28px rgba(0,0,0,.14);
  text-decoration: none;
  transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
}
#destinations .dest-linkBtn:hover{
  transform: translateY(-2px);
  box-shadow: 0 20px 44px rgba(0,0,0,.22), 0 0 26px rgba(255,107,0,.20);
  filter: saturate(1.06);
}
#destinations .dest-linkIco{ width: 18px; height: 18px; opacity: .95; }

#destinations .dest-full-footTip{
  margin-top: 14px;
  font-size: .82rem;
  color: var(--muted, rgba(148,163,184,.9));
}

/* mobile: 360 tetap besar, info di bawah */
@media (max-width: 860px){
  #destinations .dest-full-main{
    grid-template-columns: 1fr;
    grid-template-rows: minmax(320px, 54vh) 1fr;
  }
  #destinations .dest-full-360{
    border-right: 0;
    border-bottom: 1px solid rgba(148,163,184,.18);
  }
  #destinations .dest-iframeStage{ inset: 12px; }
}
</style>

<section id="destinations" class="py-12">

    {{-- TITLE GLOBAL (tetap, tidak mengubah logic) --}}
    <h2 class="neon-title">
        Destinasi Budaya Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}
    </h2>
    <div class="title-decoration"></div>
    <p class="neon-subtitle">
        Rekomendasi tempat dan pengalaman budaya yang berkaitan dengan Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}.
    </p>

    @if($sorted->count())

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- ================= KIRI: FEATURED ================= --}}
            <div class="lg:col-span-7">
                @php
                    $d = $featured;
                    [$full, $half, $empty, $n] = $ratingParts($d->rating ?? 0);
                    $img = $getImg($d);
                    $theme = $themes[0];

                    $panoEmbed = $getPanoEmbed($d);
                    $panoMaps  = $getPanoMaps($d);
                    $panoLabel = $getPanoLabel($d);
                @endphp

                <article
                    class="dest-featured-card dest-featured-neon dest-theme-{{ $theme }}"
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
                    data-panoembed="{{ e($panoEmbed ?? '') }}"
                    data-panomaps="{{ e($panoMaps ?? '') }}"
                    data-panolabel="{{ e($panoLabel ?? '') }}"
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

                        {{-- Rating badge --}}
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

                        {{-- overlay --}}
                        <div class="dest-featured-overlay">
                            <div class="dest-featured-title">{{ $d->name }}</div>

                            <div class="dest-featured-mini">
                                @if($d->location)
                                    <span class="dest-pill">
                                        <svg class="pill-ico" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $d->location }}
                                    </span>
                                @endif

                                <span class="dest-pill dest-pill-hot">
                                    <svg class="pill-ico" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M11.3 1.046c.042.49-.03 1.156-.41 1.962-.505 1.07-1.47 2.09-1.87 3.4-.34 1.11-.16 2.22.5 3.04.69.86 1.81 1.28 3.2.9 2.19-.6 3.54-2.92 3.1-5.52-.27-1.59-1.11-3.1-2.52-4.5z"/>
                                        <path d="M6.1 6.6c.07.83-.09 1.96-.7 3.18-.83 1.66-2.23 3.1-2.36 5.33-.13 2.18 1.37 4.17 3.65 4.74 2.24.56 4.66-.36 5.91-2.42 1.15-1.9 1.09-4.52-.72-6.4-.91-.95-1.97-1.45-3.2-1.35-1.16.09-2.17.7-2.58 1.72-.31.79-.23 1.61.14 2.28.37.67 1 1.18 1.78 1.35-1.22.22-2.34-.35-2.9-1.25-.71-1.16-.34-2.68.16-3.7.55-1.12 1.53-2.21 1.82-3.48z"/>
                                    </svg>
                                    Highlight
                                </span>
                            </div>

                            <div class="dest-featured-hint">
                                Klik untuk detail
                                @if(!empty($panoEmbed))
                                    • 360°
                                @endif
                            </div>
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

                        <div class="dest-featured-actions">
                            <span class="dest-featured-openIcon" aria-hidden="true" title="Buka detail">
                                <svg class="dest-open-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </article>
            </div>

            {{-- ================= KANAN: SCROLLER 3 ROWS ================= --}}
            <div class="lg:col-span-5">

                @if($others->count())
                    <div class="dest-right-wrap">
                        <div class="dest-right-headerInside">
                            <div class="dest-right-title">
                                <span class="dest-right-titleDot" aria-hidden="true"></span>
                                Destinasi Lainnya
                                <span class="dest-right-titlePill">pilihan</span>
                            </div>
                        </div>

                        <div class="dest-right-scroller" aria-label="Destinasi lainnya (scroll horizontal)">
                            @foreach($others as $idx => $d)
                                @php
                                    [$full, $half, $empty, $n] = $ratingParts($d->rating ?? 0);
                                    $img = $getImg($d);
                                    $theme = $themes[($idx + 1) % count($themes)];

                                    $panoEmbed = $getPanoEmbed($d);
                                    $panoMaps  = $getPanoMaps($d);
                                    $panoLabel = $getPanoLabel($d);
                                @endphp

                                <article
                                    class="dest-mini-card dest-theme-{{ $theme }}"
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
                                    data-panoembed="{{ e($panoEmbed ?? '') }}"
                                    data-panomaps="{{ e($panoMaps ?? '') }}"
                                    data-panolabel="{{ e($panoLabel ?? '') }}"
                                >
                                    <div class="dest-mini-row">

                                        <div class="dest-mini-thumb">
                                            @if($img)
                                                <img src="{{ $img }}" alt="{{ $d->name }}" class="dest-mini-img" loading="lazy">
                                            @else
                                                <div class="dest-mini-noimg">No image</div>
                                            @endif
                                            <span class="dest-mini-thumbGlow" aria-hidden="true"></span>
                                        </div>

                                        <div class="dest-mini-content">

                                            <div class="dest-mini-topRow">
                                                <div class="dest-mini-badge" aria-label="Rating {{ $fmtRating($d->rating ?? 0) }} dari 5">
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

                                            <div class="dest-mini-name" title="{{ $d->name }}">{{ $d->name }}</div>

                                            @if($d->location)
                                                <div class="dest-mini-loc">
                                                    <span class="dest-mini-locPin" aria-hidden="true">
                                                        <svg class="dest-mini-locIco" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </span>
                                                    <span class="dest-mini-locText">{{ $d->location }}</span>
                                                </div>
                                            @endif

                                            <div class="dest-mini-bottomRow">
                                                <span class="dest-mini-openBtn" aria-hidden="true" title="Buka detail">
                                                    <svg class="dest-open-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                    </svg>
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="dest-empty" style="text-align: center">
                        Belum ada destinasi lainnya di daerah suku {{ $tribeKey }}.
                    </div>
                @endif

            </div>
        </div>

        {{-- =========================================================
           FULLSCREEN DETAIL (JIKA ADA 360)
           - Jika ada data-panoembed -> buka panel 1 layar penuh
           - Jika tidak ada -> tetap pakai modal popup seperti sebelumnya
        ========================================================== --}}
        <div id="destinationFull" class="dest-full dest-hidden" aria-hidden="true">
            <div class="dest-full-shell" id="destFullShell" role="dialog" aria-modal="true" aria-labelledby="destFullTitle">
                <div class="dest-full-topbar">
                    <div class="dest-full-titleWrap">
                        <h3 id="destFullTitle" class="dest-full-title">—</h3>

                        <div class="dest-full-sub">
                            <span class="dest-full-pill" aria-label="Rating">
                                <span id="destFullStars" class="dest-modal-stars" aria-hidden="true" style="display:inline-flex;gap:3px;"></span>
                                <span id="destFullRatingText" class="dest-full-pillText">0 / 5</span>
                            </span>

                            <span id="destFullLocPill" class="dest-full-pill dest-hidden" aria-label="Lokasi">
                                <svg class="dest-ico" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span id="destFullLocText" class="dest-full-pillText"></span>
                            </span>
                        </div>
                    </div>

                    <div class="dest-full-actions">
                        {{-- Zoom Out --}}
                        <button type="button" class="dest-ctrlBtn" id="destZoomOut" aria-label="Zoom out">
                            <svg class="dest-ctrlIco" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/>
                            </svg>
                        </button>

                        {{-- Zoom In --}}
                        <button type="button" class="dest-ctrlBtn" id="destZoomIn" aria-label="Zoom in">
                            <svg class="dest-ctrlIco" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                            </svg>
                        </button>

                        {{-- Fullscreen --}}
                        <button type="button" class="dest-ctrlBtn" id="destGoFullscreen" aria-label="Layar penuh">
                            <svg class="dest-ctrlIco" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3M3 16v3a2 2 0 002 2h3m11-2h-3m8-3v3a2 2 0 01-2 2h-3"/>
                            </svg>
                        </button>

                        {{-- Close --}}
                        <button type="button" class="dest-ctrlBtn" id="destFullClose" aria-label="Tutup detail">
                            <svg class="dest-ctrlIco" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="dest-full-main">
                    <div class="dest-full-360">
                        <div id="dest360Label" class="dest-360Label dest-hidden">
                            <svg class="dest-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-9-9c2.2 0 4.3.8 5.9 2.2M21 3v6h-6"/>
                            </svg>
                            <span id="dest360LabelText" class="dest-360LabelText">360°</span>
                        </div>

                        <div class="dest-iframeStage" id="destIframeStage">
                            <div class="dest-iframeZoom" id="destIframeZoom">
                                <iframe
                                    id="destPanoIframe"
                                    class="dest-iframe"
                                    src=""
                                    loading="lazy"
                                    allowfullscreen
                                    referrerpolicy="no-referrer-when-downgrade"
                                ></iframe>
                            </div>
                        </div>
                    </div>

                    <div class="dest-full-info">
                        <h4 class="dest-full-aboutTitle">Tentang Destinasi</h4>
                        <p id="destFullDesc" class="dest-full-aboutText">—</p>

                        <div class="dest-full-links">
                            <a id="destMapsLink" href="#" class="dest-linkBtn dest-hidden" target="_blank" rel="noopener">
                                <svg class="dest-linkIco" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Buka di Google Maps
                            </a>
                        </div>

                        <div class="dest-full-footTip">
                            Tip: gunakan tombol <b>+</b> / <b>−</b> untuk memperbesar tampilan, tombol layar penuh untuk mode lebar,
                            dan tekan <b>ESC</b> untuk keluar.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= MODAL POPUP DETAIL (DEFAULT) ================= --}}
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
    {{-- ✅ tombol maps (muncul kalau ada data-panomaps) --}}
    <a id="destModalMapsLink"
       href="#"
       class="dest-linkBtn dest-hidden"
       target="_blank"
       rel="noopener">
        <svg class="dest-linkIco" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
        Buka di Google Maps
    </a>

    <button type="button" class="dest-modal-closeBtn" data-dest-close-btn>
        Tutup
        <span class="dest-modal-closeGlow" aria-hidden="true"></span>
    </button>
</div>


                        <div class="dest-modal-footnote">

                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <p class="text-sm text-[var(--muted)]" style="text-align: center">
            Konten destinasi untuk {{ $tribeKey }} belum diinput dari admin.
        </p>
    @endif
</section>

<script>
(function () {
    const modal = document.getElementById('destinationModal');
    const full  = document.getElementById('destinationFull');

    if (!modal || !full) return;

    // ===== popup modal refs =====
    const dialog = modal.querySelector('.dest-modal-dialog');

    const imgEl   = document.getElementById('destModalImg');
    const noImgEl = document.getElementById('destModalNoImg');

    const titleEl = document.getElementById('destModalTitle');
    const descEl  = document.getElementById('destModalDesc');

    const locWrap = document.getElementById('destModalLoc');
    const locTxt  = document.getElementById('destModalLocText');

    const starsEl = document.getElementById('destModalStars');
    const rateTxt = document.getElementById('destModalRatingText');
    const modalMapsLink = document.getElementById('destModalMapsLink');

    // ===== fullscreen 360 refs =====
    const fullShell = document.getElementById('destFullShell');

    const fullTitle = document.getElementById('destFullTitle');
    const fullDesc  = document.getElementById('destFullDesc');

    const fullLocPill = document.getElementById('destFullLocPill');
    const fullLocText = document.getElementById('destFullLocText');

    const fullStars   = document.getElementById('destFullStars');
    const fullRateTxt = document.getElementById('destFullRatingText');

    const labelWrap = document.getElementById('dest360Label');
    const labelText = document.getElementById('dest360LabelText');

    const iframe = document.getElementById('destPanoIframe');
    const zoomWrap = document.getElementById('destIframeZoom');

    const btnZoomIn  = document.getElementById('destZoomIn');
    const btnZoomOut = document.getElementById('destZoomOut');
    const btnFs      = document.getElementById('destGoFullscreen');
    const btnClose   = document.getElementById('destFullClose');

    const mapsLink = document.getElementById('destMapsLink');

    // ===== navbar hide/show =====
    const headerEl = document.getElementById('top');
    let headerPrev = null;

    // ===== focus management =====
    let lastFocus = null;

    // ===== zoom state (visual zoom, karena iframe cross-origin) =====
    let zoom = 1;
    const Z_MIN = 1;
    const Z_MAX = 2.6;
    const Z_STEP = 0.15;

    function clamp(n, min, max){ return Math.max(min, Math.min(max, n)); }

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
            <svg width="${px}" height="${px}" viewBox="0 0 20 20" aria-hidden="true" style="display:block;filter:${type==='full'?'drop-shadow(0 10px 18px rgba(251,191,36,.18))':''}">
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

    function lockScroll(){
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';
    }
    function unlockScroll(){
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
    }

    // =========================================================
    // POPUP MODAL (DEFAULT)
    // =========================================================
    function openModalFromCard(card){
        if (!card) return;

        lastFocus = document.activeElement;

        const name = card.getAttribute('data-name') || '—';
        const desc = card.getAttribute('data-description') || '';
        const img  = card.getAttribute('data-image') || '';
        const loc  = card.getAttribute('data-location') || '';
        const rating = card.getAttribute('data-rating') || '0';
        const panoMaps = (card.getAttribute('data-panomaps') || '').trim();


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

        // ✅ Maps link tetap tampil walau 360 kosong (modal popup)
if (modalMapsLink) {
    if (panoMaps) {
        modalMapsLink.href = panoMaps;
        modalMapsLink.classList.remove('dest-hidden');
    } else {
        modalMapsLink.href = '#';
        modalMapsLink.classList.add('dest-hidden');
    }
}


        hideNavbar();
        modal.classList.remove('dest-hidden');
        modal.setAttribute('aria-hidden', 'false');
        lockScroll();

        const xBtn = modal.querySelector('.dest-modal-close');
        if (xBtn) xBtn.focus();
    }

    function closeModal(){
        modal.classList.add('dest-hidden');
        modal.setAttribute('aria-hidden', 'true');

        if (modalMapsLink) {
    modalMapsLink.href = '#';
    modalMapsLink.classList.add('dest-hidden');
}

        unlockScroll();
        showNavbar();

        if (lastFocus && typeof lastFocus.focus === 'function') {
            lastFocus.focus();
        }
    }

    // =========================================================
    // FULLSCREEN DETAIL (JIKA ADA 360)
    // =========================================================
    function applyZoom(){
        zoom = clamp(zoom, Z_MIN, Z_MAX);
        if (zoomWrap) zoomWrap.style.transform = `scale(${zoom})`;
    }

    function openFullFromCard(card){
        if (!card) return;

        lastFocus = document.activeElement;

        const name = card.getAttribute('data-name') || '—';
        const desc = card.getAttribute('data-description') || '';
        const loc  = card.getAttribute('data-location') || '';
        const rating = card.getAttribute('data-rating') || '0';

        const panoEmbed = (card.getAttribute('data-panoembed') || '').trim();
        const panoMaps  = (card.getAttribute('data-panomaps') || '').trim();
        const panoLabel = (card.getAttribute('data-panolabel') || '').trim();

        // safety: kalau embed kosong, jangan buka full
        if (!panoEmbed) {
            openModalFromCard(card);
            return;
        }

        fullTitle.textContent = name;
        fullDesc.textContent  = desc ? desc : 'Deskripsi belum tersedia.';

        // location
        if (loc && loc.trim() !== '') {
            fullLocText.textContent = loc;
            fullLocPill.classList.remove('dest-hidden');
        } else {
            fullLocText.textContent = '';
            fullLocPill.classList.add('dest-hidden');
        }

        // rating
        renderStars(fullStars, rating);
        fullRateTxt.textContent = fmtRating(rating) + ' / 5';

        // label 360
        if (panoLabel) {
            labelText.textContent = panoLabel;
            labelWrap.classList.remove('dest-hidden');
        } else {
            labelText.textContent = '360°';
            labelWrap.classList.remove('dest-hidden');
        }

        // maps link (opsional)
        if (panoMaps) {
            mapsLink.href = panoMaps;
            mapsLink.classList.remove('dest-hidden');
        } else {
            mapsLink.href = '#';
            mapsLink.classList.add('dest-hidden');
        }

        // set iframe
        iframe.src = panoEmbed;

        // reset zoom
        zoom = 1;
        applyZoom();

        hideNavbar();
        full.classList.remove('dest-hidden');
        full.setAttribute('aria-hidden', 'false');
        lockScroll();

        // fokus ke tombol close agar keyboard enak
        if (btnClose) btnClose.focus();
    }

    function closeFull(){
        // keluar dari fullscreen browser jika aktif
        try{
            if (document.fullscreenElement) document.exitFullscreen();
        } catch(e){}

        full.classList.add('dest-hidden');
        full.setAttribute('aria-hidden', 'true');

        // bersihin iframe supaya stop load
        iframe.src = '';
        zoom = 1;
        applyZoom();

        unlockScroll();
        showNavbar();

        if (lastFocus && typeof lastFocus.focus === 'function') {
            lastFocus.focus();
        }
    }

    // fullscreen api untuk shell (biar panel jadi benar2 full screen)
    function toggleFullscreen(){
        try{
            if (!document.fullscreenElement) {
                if (fullShell && fullShell.requestFullscreen) fullShell.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        } catch(e){}
    }

    // =========================================================
    // ROUTING CLICK: JIKA ADA 360 -> FULL, JIKA TIDAK -> POPUP
    // =========================================================
    function openByCard(card){
        const panoEmbed = (card.getAttribute('data-panoembed') || '').trim();
        if (panoEmbed) return openFullFromCard(card);
        return openModalFromCard(card);
    }

    document.querySelectorAll('[data-destination-modal-trigger]').forEach(card => {
        card.addEventListener('click', (e) => {
            const a = e.target.closest('a');
            if (a) return;
            openByCard(card);
        });

        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openByCard(card);
            }
        });
    });

    // =========================================================
    // CLOSE HANDLERS: POPUP
    // =========================================================
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

    modal.addEventListener('click', (e) => {
        if (modal.classList.contains('dest-hidden')) return;
        if (dialog && dialog.contains(e.target)) return;
        closeModal();
    });

    // =========================================================
    // CLOSE HANDLERS: FULLSCREEN DETAIL
    // =========================================================
    if (btnClose) {
        btnClose.addEventListener('click', (e) => {
            e.preventDefault();
            closeFull();
        });
    }

    full.addEventListener('click', (e) => {
        // klik backdrop luar shell -> close
        if (full.classList.contains('dest-hidden')) return;
        if (fullShell && fullShell.contains(e.target)) return;
        closeFull();
    });

    // zoom buttons
    if (btnZoomIn) {
        btnZoomIn.addEventListener('click', (e) => {
            e.preventDefault();
            zoom = zoom + Z_STEP;
            applyZoom();
        });
    }
    if (btnZoomOut) {
        btnZoomOut.addEventListener('click', (e) => {
            e.preventDefault();
            zoom = zoom - Z_STEP;
            applyZoom();
        });
    }

    // fullscreen button
    if (btnFs) {
        btnFs.addEventListener('click', (e) => {
            e.preventDefault();
            toggleFullscreen();
        });
    }

    // ESC: jika full terbuka -> tutup full, else jika modal terbuka -> tutup modal
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;

        if (!full.classList.contains('dest-hidden')) {
            closeFull();
            return;
        }
        if (!modal.classList.contains('dest-hidden')) {
            closeModal();
            return;
        }
    });
})();
</script>
