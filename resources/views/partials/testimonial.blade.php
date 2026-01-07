{{-- ================= TESTIMONI ================= --}}
<section id="testimoni" class="py-12">
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

        // ===============================
        // MARQUEE DATA (TIDAK UBAH LOGIKA INTI)
        // Split selang-seling: index genap => atas, index ganjil => bawah
        // ===============================
        $items = $testimonials instanceof \Illuminate\Support\Collection
            ? $testimonials->values()
            : collect($testimonials)->values();

        $topRow = $items->filter(fn($v, $i) => $i % 2 === 0)->values();
        $botRow = $items->filter(fn($v, $i) => $i % 2 === 1)->values();

        // Threshold: kalau < 3 => TIDAK jalan (static)
        $shouldAnimate = $items->count() >= 3;

        // helper initial letter
        $initial = function($name) {
            $name = trim((string)$name);
            if ($name === '') return '?';
            $first = mb_substr($name, 0, 1, 'UTF-8');
            return mb_strtoupper($first, 'UTF-8');
        };

        // helper role (kalau field tidak ada, fallback)
        $roleText = function($t) {
            $role = $t->role ?? $t->occupation ?? $t->title ?? null;
            return $role ? (string)$role : 'Pengunjung';
        };
    @endphp

    <style>
        /* =========================================================
           TESTIMONI THEME SAFE (LIGHT/DARK) + FIX OVERFLOW + NO CIRCLE STAR
        ========================================================= */

        .t-wrap { color: var(--txt-body); }

        .t-card {
            background: linear-gradient(145deg, var(--card), color-mix(in oklab, var(--card-bg-dark) 90%, transparent));
            border: 1px solid rgba(255, 107, 0, 0.2);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            border-radius: 20px;
            padding: 1.5rem;
        }

        html[data-theme="dark"] .t-card {
            background: linear-gradient(145deg, #111827, #020617);
        }
        html[data-theme="light"] .t-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
        }

        .t-soft {
            background: linear-gradient(145deg, var(--card), color-mix(in oklab, var(--card-bg-dark) 90%, transparent));
            border: 1px solid rgba(255, 107, 0, 0.1);
            border-radius: 16px;
            padding: 1.25rem;
        }

        html[data-theme="dark"] .t-soft { background: linear-gradient(145deg, #111827, #020617); }
        html[data-theme="light"] .t-soft { background: linear-gradient(145deg, #ffffff, #f8fafc); }

        .t-muted { color: var(--muted); }

        .t-input, .t-textarea {
            width: 100%;
            border-radius: 12px;
            border: 1px solid rgba(255, 107, 0, 0.2);
            background: color-mix(in oklab, var(--bg-body) 95%, transparent);
            color: var(--txt-body);
            padding: 12px 14px;
            outline: none;
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        }

        html[data-theme="dark"] .t-input,
        html[data-theme="dark"] .t-textarea { background: rgba(255, 255, 255, 0.05); }

        html[data-theme="light"] .t-input,
        html[data-theme="light"] .t-textarea { background: rgba(0, 0, 0, 0.02); }

        .t-input::placeholder, .t-textarea::placeholder { color: rgba(156, 163, 175, 0.7); }

        .t-input:focus, .t-textarea:focus {
            border-color: #ff6b00;
            box-shadow: 0 0 0 4px rgba(255, 107, 0, 0.2);
            transform: translateY(-1px);
        }

        /* ===============================
           RATING STARS (CLICK) - NO CIRCLE
        =============================== */
        .star-row {
            display: inline-flex;
            gap: 10px;
            align-items: center;
        }

        .star-btn {
            width: auto;
            height: auto;
            border: 0;
            background: transparent;
            padding: 0;
            cursor: pointer;
            user-select: none;
            line-height: 1;
        }

        .star-btn:focus { outline: none; }
        .star-btn:focus-visible {
            outline: 2px solid rgba(255,107,0,.55);
            outline-offset: 4px;
            border-radius: 10px;
        }

        .star {
            font-size: 28px;
            line-height: 1;
            color: rgba(156, 163, 175, 0.55);
            transition: transform .12s ease, color .18s ease, filter .18s ease;
            display: inline-block;
        }

        .star-btn:hover .star {
            transform: translateY(-1px) scale(1.08);
            filter: drop-shadow(0 10px 20px rgba(255,107,0,.25));
        }

        .star.is-on { color: #f59e0b; transform: scale(1.06); }

        /* Progress bars */
        .t-bar {
            height: 12px;
            border-radius: 999px;
            background: rgba(255, 107, 0, 0.1);
            overflow: hidden;
        }
        .t-bar > span {
            display: block;
            height: 100%;
            width: 0%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f59e0b, #ff6b00);
            transition: width .5s ease;
        }

        .t-file {
            width: 100%;
            border-radius: 12px;
            border: 2px dashed rgba(255, 107, 0, 0.3);
            background: rgba(255, 107, 0, 0.05);
            color: var(--txt-body);
            padding: 12px 14px;
            transition: all .18s ease;
        }
        .t-file:hover {
            border-color: #ff6b00;
            background: rgba(255, 107, 0, 0.1);
        }

        .t-btn {
            border-radius: 14px;
            padding: 14px 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #ff6b00, #ff8c42);
            color: white;
            border: 0;
            box-shadow: 0 18px 30px rgba(255, 107, 0, 0.25);
            transition: all .18s ease;
            font-size: 1rem;
        }
        .t-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 35px rgba(255, 107, 0, 0.35);
            filter: brightness(1.1);
        }
        .t-btn:active { transform: translateY(0px); }

        .t-chip {
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid rgba(255, 107, 0, 0.3);
            background: rgba(255, 107, 0, 0.1);
            color: #ff8c42;
            font-weight: 600;
        }

        /* =========================================================
           FIX OVERFLOW TEKS PANJANG
        ========================================================= */
        .t-name,
        .t-comment,
        .t-anywhere {
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .t-comment { white-space: pre-wrap; }

        /* =========================================================
           MODAL REPORT DARK MODE
        ========================================================= */
        html[data-theme="dark"] #reportModal .t-card{
            background: linear-gradient(145deg, #0b1220, #020617) !important;
            border: 1px solid rgba(255,107,0,.22) !important;
            box-shadow: 0 26px 70px rgba(0,0,0,.55) !important;
        }
        html[data-theme="dark"] #reportModal h3,
        html[data-theme="dark"] #reportModal label{
            color: rgba(255,255,255,.92) !important;
        }
        html[data-theme="dark"] #reportModal .t-muted{
            color: rgba(255,255,255,.62) !important;
        }
        #reportModal select.t-input{
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-clip: padding-box;
        }
        html[data-theme="dark"] #reportModal select.t-input{
            background-color: rgba(255,255,255,.06) !important;
            color: rgba(255,255,255,.92) !important;
            border-color: rgba(255,107,0,.28) !important;
        }
        html[data-theme="dark"] #reportModal select.t-input option{
            background-color: #0b1220 !important;
            color: rgba(255,255,255,.92) !important;
        }
        html[data-theme="dark"] #reportModal select.t-input option:checked,
        html[data-theme="dark"] #reportModal select.t-input option:hover{
            background-color: #111b2f !important;
            color: rgba(255,255,255,.95) !important;
        }
        html[data-theme="dark"] #reportModal select.t-input:focus{
            border-color: #ff6b00 !important;
            box-shadow: 0 0 0 4px rgba(255,107,0,.22) !important;
        }
        html[data-theme="dark"] #reportModal textarea.t-textarea{
            background-color: rgba(255,255,255,.06) !important;
            color: rgba(255,255,255,.92) !important;
            border-color: rgba(255,107,0,.22) !important;
        }
        html[data-theme="dark"] #reportModal textarea.t-textarea::placeholder{
            color: rgba(255,255,255,.45) !important;
        }
        html[data-theme="dark"] #reportModal button.t-input{
            background-color: rgba(255,255,255,.06) !important;
            color: rgba(255,255,255,.88) !important;
            border-color: rgba(255,107,0,.18) !important;
        }
        html[data-theme="dark"] #reportModal button.t-input:hover{
            background-color: rgba(255,255,255,.09) !important;
            border-color: rgba(255,107,0,.28) !important;
        }

        /* =========================================================
           MARQUEE
        ========================================================= */
        .t-marquee-wrap {
            margin-top: 10px;
            padding-top: 8px;
        }

        .t-marquee {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            background: transparent;
        }

        .t-marquee::before,
        .t-marquee::after{
            content:"";
            position:absolute;
            top:0;
            bottom:0;
            width: 90px;
            pointer-events:none;
            z-index: 5;
        }
        .t-marquee::before{
            left:0;
            background: linear-gradient(to right,
                color-mix(in oklab, var(--bg-body) 92%, transparent),
                transparent
            );
        }
        .t-marquee::after{
            right:0;
            background: linear-gradient(to left,
                color-mix(in oklab, var(--bg-body) 92%, transparent),
                transparent
            );
        }

        .t-lane { display:block; padding: 14px 0; }

        .t-track {
            display:flex;
            width:max-content;
            gap: 16px;
            padding: 0 12px;
            will-change: transform;
            transform: translateZ(0);
        }

        .t-track.is-left  { animation: tMarqueeLeft  var(--t-dur, 34s) linear infinite; }
        .t-track.is-right { animation: tMarqueeRight var(--t-dur, 34s) linear infinite; }

        @keyframes tMarqueeLeft {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }
        @keyframes tMarqueeRight {
            from { transform: translateX(-50%); }
            to   { transform: translateX(0); }
        }

        .t-mini-card{
            width: 420px;
            max-width: 78vw;
            border-radius: 18px;
            padding: 16px 16px 14px;
            border: 1px solid rgba(15, 23, 42, 0.06);
            box-shadow: 0 10px 26px rgba(0,0,0,.06);
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(10px);
            text-align: left;
        }
        html[data-theme="dark"] .t-mini-card{
            border: 1px solid rgba(255,255,255,.10);
            box-shadow: 0 16px 36px rgba(0,0,0,.45);
            background: rgba(17, 24, 39, .68);
        }

        .t-mini-head{
            display:flex;
            align-items:flex-start;
            gap: 12px;
            justify-content: flex-start;
            text-align: left;
        }

        .t-avatar{
          width: 56px;
          height: 56px;
          border-radius: 999px;
          display:flex;
          align-items:center;
          justify-content:center;
          font-weight: 900;
          background: var(--brand);
          color: #ffffff;
          flex: 0 0 auto;
          box-shadow: 0 10px 22px rgba(249,115,22,.28);
          overflow:hidden;
        }

        #testimoni{ --brand: #f97316; }
        html[data-theme="dark"] #testimoni{ --brand: #f97316; }

        .t-avatar img{
            width: 100%;
            height: 100%;
            border-radius: 999px;
            object-fit: cover;
            display:block;
        }

        .t-mini-meta{ min-width:0; flex:1; text-align:left; }
        .t-mini-name{
            font-weight: 900;
            font-size: 16px;
            line-height: 1.15;
            color: rgba(15,23,42,.92);
            text-align:left;
        }
        html[data-theme="dark"] .t-mini-name{ color: rgba(255,255,255,.92); }

        .t-mini-role{
            margin-top: 2px;
            font-size: 12px;
            color: rgba(100,116,139,.9);
            font-weight: 600;
            text-align:left;
        }
        html[data-theme="dark"] .t-mini-role{ color: rgba(148,163,184,.9); }

        .t-mini-stars{
            margin-top: 6px;
            display:flex;
            gap: 2px;
            font-size: 14px;
            line-height: 1;
            justify-content: flex-start;
        }

        /* ===============================
   FIX FINAL: QUOTE FULL WIDTH & RATA KIRI
=============================== */
#testimoni .t-mini-quote{
  /* PENTING: jangan shrink ke konten */
  display: block !important;

  /* paksa isi lebar card */
  width: 100% !important;
  max-width: 100% !important;

  /* hilangkan efek "kotak di tengah" */
  margin: 10px 0 0 0 !important;

  /* teks */
  text-align: left !important;
  font-style: italic;
  line-height: 1.55;

  /* wrapping */
  white-space: pre-wrap;
  word-break: break-word;
  overflow-wrap: anywhere;

  /* clamp tetap jalan */
  display: -webkit-box !important;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;

  overflow: hidden;
}

        html[data-theme="dark"] .t-mini-quote{ color: rgba(226,232,240,.82); }

        .t-mini-foot{
            margin-top: 10px;
            display:flex;
            justify-content: space-between;
            align-items:center;
            gap: 10px;
            text-align: left;
        }
        .t-mini-date{
            font-size: 11px;
            color: rgba(100,116,139,.85);
            font-weight: 700;
        }
        html[data-theme="dark"] .t-mini-date{ color: rgba(148,163,184,.75); }

        .t-mini-report{
            display:inline-flex;
            align-items:center;
            gap: 8px;
            font-size: 11px;
            font-weight: 900;
            color: rgba(239,68,68,.95);
            text-decoration: none;
            user-select:none;
        }
        .t-mini-report:hover{ text-decoration: underline; }
        .t-mini-report svg{
            width: 16px;
            height: 16px;
            flex: 0 0 auto;
        }

        .t-track.is-static { animation: none !important; transform: translateX(0) !important; }

        @media (prefers-reduced-motion: reduce) {
            .t-track.is-left, .t-track.is-right { animation: none !important; }
        }

        @media (max-width: 640px){
            .t-marquee::before, .t-marquee::after{ width: 36px; }
            .t-lane{ padding: 10px 0; }
            .t-track{ gap: 12px; padding: 0 10px; }
            .t-mini-card{
                width: 88vw;
                max-width: 88vw;
                padding: 14px 14px 12px;
            }
            .t-avatar{ width: 52px; height: 52px; }
        }

        /* ===============================
           FORM GRID
        =============================== */
        .t-form-grid{
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
        }
        @media (min-width: 768px){
            .t-form-grid{
                grid-template-columns: 1fr 1fr;
                gap: 14px;
            }
            .t-form-span-2{ grid-column: span 2 / span 2; }
        }

        /* ===============================
           ALERTS
        =============================== */
        .t-alert {
            border-radius: 16px;
            padding: 14px 16px;
            border: 1px solid transparent;
            font-size: 14px;
            font-weight: 900;
        }
        .t-alert-success{
            background: rgba(34, 197, 94, 0.14);
            border-color: rgba(34, 197, 94, 0.30);
            color: rgba(34, 197, 94, 0.95);
        }
        .t-alert-error{
            background: rgba(239, 68, 68, 0.12);
            border-color: rgba(239, 68, 68, 0.28);
            color: rgba(239, 68, 68, 0.95);
        }
        .t-alert-client{ display:none; }

        /* =========================================================
           NEON WRAPPER
        ========================================================= */
        @property --t-neon-angle {
            syntax: "<angle>";
            inherits: false;
            initial-value: 0deg;
        }

        .t-neon-shell{
            position: relative;
            border-radius: 22px;
        }

        .t-neon-glow{
            position: absolute;
            inset: -5px;
            border-radius: inherit;
            padding: 10px;
            z-index: 0;
            pointer-events: none;

            background: conic-gradient(from var(--t-neon-angle),
                    rgba(255, 107, 0, 0),
                    rgba(255, 140, 66, 0.18) 30deg,
                    rgba(255, 107, 0, 0.95) 80deg,
                    rgba(255, 170, 107, 0.9) 120deg,
                    rgba(255, 140, 66, 0.18) 180deg,
                    rgba(255, 107, 0, 0) 240deg,
                    rgba(255, 140, 66, 0.20) 300deg,
                    rgba(255, 107, 0, 0.95) 330deg,
                    rgba(255, 107, 0, 0) 360deg);

            -webkit-mask:
                linear-gradient(#000 0 0) content-box,
                linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;

            filter: blur(4px);
            opacity: .90;
            animation: t-neon-spin 8s linear infinite;
        }

        @keyframes t-neon-spin{
            to { --t-neon-angle: 360deg; }
        }

        .t-neon-inner{
            position: relative;
            z-index: 1;
            border-radius: 20px;
        }

        @media (prefers-reduced-motion: reduce){
            .t-neon-glow{ animation: none !important; }
        }

        /* =========================================================
           ✅ FINAL OVERRIDE (INI YANG BENER)
           Paksa lane & track START KIRI (ANTI CSS EXTERNAL / AUTO-CENTER)
        ========================================================= */

        /* lane jadi flex supaya kita bisa kontrol posisi track */
        #testimoni .t-lane{
            display: flex !important;
            justify-content: flex-start !important;
            align-items: stretch !important;
        }

        /* track jangan pernah auto-center */
        #testimoni .t-track{
            justify-content: flex-start !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        /* kalau ada global rule yang bikin max-width + auto margin, matikan */
        #testimoni .t-track,
        #testimoni .t-mini-card{
            margin: 0 !important;
        }

        /* pastikan teks card tetap left */
        #testimoni .t-mini-card,
        #testimoni .t-mini-card *{
            text-align: left !important;
        }
/* =========================================================
   FIX: neon shell & card harus sama tinggi (no empty space)
========================================================= */
#testimoni .t-neon-shell{
  display: flex !important;
  flex-direction: column !important;
  align-items: stretch !important;
}

/* inner card ikut nge-fill shell */
#testimoni .t-neon-inner{
  flex: 1 1 auto !important;
  width: 100% !important;
}

/* khusus card average (yang kanan) jangan “shrink” */
#testimoni .t-neon-inner.t-card{
  height: 100% !important;
}

/* kalau ada CSS luar yang bikin shell punya min-height, matiin */
#testimoni .t-neon-shell{
  min-height: 0 !important;
}


    </style>

    <div class="max-w-6xl mx-auto px-4">
        <div class="t-wrap">
            <h2 class="neon-title">
                Testimoni Pengunjung
            </h2>
            <div class="title-decoration"></div>
            <p class="neon-subtitle">
                Bagikan pengalamanmu, bantu kami jadi lebih baik.
            </p>

            {{-- ===== SUMMARY (TOP) ===== --}}
            <div class="grid gap-6 lg:grid-cols-3 mb-8">

                {{-- Left: distribution (NEON) --}}
                <div class="t-neon-shell lg:col-span-2">
                    <div class="t-neon-glow"></div>
                    <div class="t-neon-inner t-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="font-bold text-lg">Ringkasan Rating</div>
                            <div class="t-chip">{{ $total }} Rating</div>
                        </div>

                        @for($r = 5; $r >= 1; $r--)
                            @php $p = $pct($counts[$r]); @endphp
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-20 text-sm font-bold tracking-wide" style="color: #ff8c42;">
                                    {{ $r }} ★
                                </div>
                                <div class="flex-1 t-bar">
                                    <span style="width: {{ $p }}%"></span>
                                </div>
                                <div class="w-16 text-right text-sm font-semibold" style="color: #ff8c42;">
                                    {{ $counts[$r] }} ({{ $p }}%)
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- Right: average (NEON) --}}
                <div class="t-neon-shell">
                    <div class="t-neon-glow"></div>
                    <div class="t-neon-inner t-card flex flex-col items-center justify-center text-center">
                        <div class="text-5xl font-extrabold" style="color: #ff6b00;">
                            {{ number_format($avg, 1) }}
                        </div>

                        <div class="mt-3 flex items-center justify-center gap-1">
                            @php
                                $full = (int) floor($avg);
                                $dec  = $avg - $full;
                                $half = $dec >= 0.5 ? 1 : 0;
                                $empty = 5 - $full - $half;
                                if ($empty < 0) $empty = 0;
                            @endphp

                            @for($i=0; $i < $full; $i++)
                                <span class="text-3xl" style="color:#f59e0b;">★</span>
                            @endfor

                            @if($half === 1)
                                <span class="text-3xl relative inline-block" aria-hidden="true" style="line-height:1;">
                                    <span style="color: rgba(156, 163, 175, 0.3);">★</span>
                                    <span style="position:absolute; left:0; top:0; width:50%; overflow:hidden; color:#f59e0b;">★</span>
                                </span>
                            @endif

                            @for($i=0; $i < $empty; $i++)
                                <span class="text-3xl" style="color: rgba(156, 163, 175, 0.3);">★</span>
                            @endfor
                        </div>

                        <div class="mt-2 t-muted text-sm">
                            Dari {{ $total }} rating
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== MARQUEE ===== --}}
            <div class="t-marquee-wrap mb-10">
                <div class="t-marquee">
                    @if($items->count() === 0)
                        <div class="t-muted text-center py-10">Belum ada testimoni. Jadilah yang pertama</div>
                    @else
                        {{-- Lane TOP --}}
                        <div class="t-lane">
                            <div class="t-track {{ $shouldAnimate ? 'is-right' : 'is-static' }}" style="--t-dur: 34s;">
                                @for($rep=0; $rep<2; $rep++)
                                    @foreach($topRow as $t)
                                        <div class="t-mini-card">
                                            <div class="t-mini-head">
                                                <div class="t-avatar" aria-hidden="true">
                                                    @if(!empty($t->photo))
                                                        <img src="{{ asset('storage/'.$t->photo) }}" alt="Avatar {{ $t->name }}">
                                                    @else
                                                        {{ $initial($t->name) }}
                                                    @endif
                                                </div>

                                                <div class="t-mini-meta">
                                                    <div class="t-mini-name t-anywhere">{{ $t->name }}</div>
                                                    <div class="t-mini-role">{{ $roleText($t) }}</div>

                                                    <div class="t-mini-stars" aria-label="Rating {{ $t->rating }} dari 5">
                                                        @for($i=1; $i<=5; $i++)
                                                            <span style="color: {{ $i <= (int)$t->rating ? '#f59e0b' : 'rgba(156, 163, 175, 0.35)' }};">★</span>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="t-mini-quote">
                                                {{ $t->message }}
                                            </div>

                                            <div class="t-mini-foot">
                                                <div class="t-mini-date">{{ $t->created_at?->translatedFormat('d M Y') }}</div>

                                                <a href="javascript:void(0)"
                                                   onclick="openReportModal('{{ route('testimonials.report', $t) }}')"
                                                   class="t-mini-report">
                                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M12 3.5L22 20.5H2L12 3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                        <path d="M12 9V14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                        <path d="M12 17.5H12.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                                    </svg>
                                                    <span>Laporkan</span>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                @endfor
                            </div>
                        </div>

                        {{-- Lane BOTTOM --}}
                        <div class="t-lane">
                            <div class="t-track {{ $shouldAnimate ? 'is-left' : 'is-static' }}" style="--t-dur: 36s;">
                                @for($rep=0; $rep<2; $rep++)
                                    @foreach($botRow as $t)
                                        <div class="t-mini-card">
                                            <div class="t-mini-head">
                                                <div class="t-avatar" aria-hidden="true">
                                                    @if(!empty($t->photo))
                                                        <img src="{{ asset('storage/'.$t->photo) }}" alt="Avatar {{ $t->name }}">
                                                    @else
                                                        {{ $initial($t->name) }}
                                                    @endif
                                                </div>

                                                <div class="t-mini-meta">
                                                    <div class="t-mini-name t-anywhere">{{ $t->name }}</div>
                                                    <div class="t-mini-role">{{ $roleText($t) }}</div>

                                                    <div class="t-mini-stars" aria-label="Rating {{ $t->rating }} dari 5">
                                                        @for($i=1; $i<=5; $i++)
                                                            <span style="color: {{ $i <= (int)$t->rating ? '#f59e0b' : 'rgba(156, 163, 175, 0.35)' }};">★</span>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="t-mini-quote">
                                                {{ $t->message }}
                                            </div>

                                            <div class="t-mini-foot">
                                                <div class="t-mini-date">{{ $t->created_at?->translatedFormat('d M Y') }}</div>

                                                <a href="javascript:void(0)"
                                                   onclick="openReportModal('{{ route('testimonials.report', $t) }}')"
                                                   class="t-mini-report">
                                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M12 3.5L22 20.5H2L12 3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                        <path d="M12 9V14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                        <path d="M12 17.5H12.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                                    </svg>
                                                    <span>Laporkan</span>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                @endfor
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ===== MAIN (BOTTOM): Add a Review (NEON) ===== --}}
            <div class="grid gap-6 lg:grid-cols-1">
                <div class="t-neon-shell">
                    <div class="t-neon-glow"></div>
                    <div class="t-neon-inner t-card">
                        <div class="font-bold text-xl mb-4">Add a Review</div>

                        <div id="clientAlert" class="mb-4 t-alert t-alert-error t-alert-client" role="alert" aria-live="polite"></div>

                        @if(session('testimonial_success'))
                            <div class="mb-4 t-alert t-alert-success">
                                {{ session('testimonial_success') }}
                            </div>
                        @endif

                        @if(session('testimonial_error'))
                            <div class="mb-4 t-alert t-alert-error">
                                {{ session('testimonial_error') }}
                            </div>
                        @endif

                        @if(session('report_success'))
                            <div class="mb-4 t-alert t-alert-success">
                                {{ session('report_success') }}
                            </div>
                        @endif

                        @if(session('report_error'))
                            <div class="mb-4 t-alert t-alert-error">
                                {{ session('report_error') }}
                            </div>
                        @endif

                        @if(session('success') && !session('testimonial_success') && !session('report_success'))
                            <div class="mb-4 t-alert t-alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error') && !session('testimonial_error') && !session('report_error'))
                            <div class="mb-4 t-alert t-alert-error">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="mb-4 t-alert t-alert-error">
                                <div class="font-bold mb-2">Gagal mengirim:</div>
                                <ul class="list-disc list-inside space-y-1" style="font-weight: 700;">
                                    @foreach($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ route('testimonials.store') }}"
                              enctype="multipart/form-data"
                              class="space-y-4"
                              id="testimonialForm">
                            @csrf

                            <input type="text" name="website" value="" autocomplete="off" tabindex="-1"
                                   style="position:absolute;left:-9999px;top:-9999px;height:1px;width:1px;opacity:0;">

                            <div class="t-form-grid">
                                <div class="t-form-span-2">
                                    <label class="text-sm font-bold">Rating <span class="text-red-500">*</span></label>
                                    <input type="hidden" name="rating" id="ratingValue" value="{{ old('rating', 0) }}">

                                    <div class="mt-2 star-row" id="starRow" aria-label="Pilih rating bintang">
                                        @for($i=1; $i<=5; $i++)
                                            <button
                                                type="button"
                                                class="star-btn"
                                                data-star="{{ $i }}"
                                                aria-label="Pilih {{ $i }} bintang"
                                                title="Pilih {{ $i }} bintang"
                                            >
                                                <span class="star">★</span>
                                            </button>
                                        @endfor
                                    </div>
                                    <div class="t-muted text-xs mt-2">Klik bintang untuk memilih rating.</div>
                                </div>

                                <div>
                                    <label class="text-sm font-bold">Nama <span class="text-red-500">*</span></label>
                                    <input class="t-input" name="name" value="{{ old('name') }}" placeholder="Nama kamu">
                                </div>

                                <div>
                                    <label class="text-sm font-bold">Foto Profil <span class="t-muted text-xs">(opsional, max 5MB)</span></label>
                                    <input class="t-file" type="file" name="photo" accept="image/png,image/jpeg,image/jpg">
                                    <div class="t-muted text-xs mt-2">Format: JPG / JPEG / PNG.</div>
                                </div>

                                <div class="t-form-span-2">
                                    <label class="text-sm font-bold">Pesan <span class="text-red-500">*</span></label>
                                    <textarea class="t-textarea" name="message" rows="5" placeholder="Tulis pengalamanmu...">{{ old('message') }}</textarea>
                                </div>

                                <div class="t-form-span-2">
                                    <button class="t-btn w-full mt-2">Kirim Testimoni</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ================= MODAL REPORT ================= --}}
    <div id="reportModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4" style="backdrop-filter: blur(4px);">
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

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeReportModal()"
                            class="t-input !w-auto !px-5 !py-2 font-bold">
                        Batal
                    </button>

                    <button class="t-btn !w-auto !px-5 !py-2">
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
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

        (function () {
            const row = document.getElementById('starRow');
            const input = document.getElementById('ratingValue');
            const form = document.getElementById('testimonialForm');
            const textarea = form?.querySelector('textarea[name="message"]');
            const nameInput = form?.querySelector('input[name="name"]');
            const alertBox = document.getElementById('clientAlert');

            if (!row || !input) return;

            function paint(v) {
                const stars = row.querySelectorAll('.star-btn .star');
                stars.forEach((el, idx) => {
                    const n = idx + 1;
                    el.classList.toggle('is-on', n <= v);
                });
            }

            function showAlert(msg) {
                if (!alertBox) return;
                alertBox.textContent = msg;
                alertBox.style.display = 'block';
                alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            function hideAlert() {
                if (!alertBox) return;
                alertBox.textContent = '';
                alertBox.style.display = 'none';
            }

            const initial = parseInt(input.value || '0', 10);
            paint(initial);

            row.querySelectorAll('.star-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const v = parseInt(btn.dataset.star, 10);
                    input.value = v;
                    paint(v);
                    hideAlert();
                });

                btn.addEventListener('mouseenter', () => {
                    const v = parseInt(btn.dataset.star, 10);
                    paint(v);
                });
            });

            row.addEventListener('mouseleave', () => {
                const v = parseInt(input.value || '0', 10);
                paint(v);
            });

            textarea?.addEventListener('input', () => hideAlert());
            nameInput?.addEventListener('input', () => hideAlert());

            form?.addEventListener('submit', (e) => {
                const v = parseInt(input.value || '0', 10);
                const msg = (textarea?.value || '').trim();
                const nm  = (nameInput?.value || '').trim();

                if (!v || v < 1) {
                    e.preventDefault();
                    showAlert('Silakan pilih rating bintang terlebih dahulu.');
                    return;
                }
                if (!msg) {
                    e.preventDefault();
                    showAlert('Silakan isi deskripsi/pesan testimoni terlebih dahulu.');
                    return;
                }
                if (!nm) {
                    e.preventDefault();
                    showAlert('Silakan isi nama terlebih dahulu.');
                    return;
                }
            });
        })();

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
