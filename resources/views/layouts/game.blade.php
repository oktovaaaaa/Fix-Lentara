{{-- resources/views/layouts/game.blade.php --}}

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Belajar') — Lentara Nusantara</title>

    {{-- ✅ ICON BROWSER SAMA DENGAN APP.BLADE --}}
    <link rel="icon" type="image/png" href="{{ asset('images/icon/icon_lentara.PNG') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/icon/icon_lentara.PNG') }}">

    {{-- ✅ PAKAI THEME YANG SAMA (data-theme) --}}
    <script>
        (function () {
            const saved = localStorage.getItem('piforrr-theme') || 'light';
            document.documentElement.setAttribute('data-theme', saved);
        })();
    </script>

    {{-- ✅ INTERNAL CSS ONLY: NEON SCROLLBAR + GARUDA CURSOR --}}
    <style>
      /* ===== Neon Brand Scrollbar (Global) ===== */
      :root{
        --brand: #f97316;
        --sb-track: rgba(255,255,255,.06);
        --sb-thumb-a: color-mix(in oklab, var(--brand) 85%, #fff 15%);
        --sb-thumb-b: color-mix(in oklab, var(--brand) 75%, #ff3d00 25%);
      }

      /* Firefox */
      html{
        scrollbar-width: thin;
        scrollbar-color: var(--brand) rgba(255,255,255,.08);
      }

      /* WebKit (Chrome/Edge/Safari) */
      ::-webkit-scrollbar{
        width: 10px;
        height: 10px;
      }

      ::-webkit-scrollbar-track{
        background: var(--sb-track);
        border-radius: 999px;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.06);
      }

      ::-webkit-scrollbar-thumb{
        border-radius: 999px;
        background: linear-gradient(180deg, var(--sb-thumb-a), var(--sb-thumb-b));
        border: 2px solid rgba(0,0,0,.15);
        box-shadow:
          0 0 10px color-mix(in oklab, var(--brand) 55%, transparent),
          0 0 18px color-mix(in oklab, var(--brand) 35%, transparent);
        animation: neonScroll 1.8s ease-in-out infinite alternate;
      }

      ::-webkit-scrollbar-thumb:hover{
        box-shadow:
          0 0 14px color-mix(in oklab, var(--brand) 70%, transparent),
          0 0 26px color-mix(in oklab, var(--brand) 45%, transparent);
        filter: saturate(1.15) brightness(1.05);
      }

      ::-webkit-scrollbar-corner{
        background: transparent;
      }

      @keyframes neonScroll{
        from{
          filter: brightness(1) saturate(1);
          box-shadow:
            0 0 8px  color-mix(in oklab, var(--brand) 45%, transparent),
            0 0 16px color-mix(in oklab, var(--brand) 28%, transparent);
        }
        to{
          filter: brightness(1.15) saturate(1.25);
          box-shadow:
            0 0 14px color-mix(in oklab, var(--brand) 65%, transparent),
            0 0 28px color-mix(in oklab, var(--brand) 40%, transparent);
        }
      }

      /* ==========================================
         GARUDA CURSOR + PARTICLES (DESKTOP ONLY)
      ========================================== */
      @media (pointer: fine) {
        body { cursor: none; }

        .garuda-cursor{
          position: fixed;
          top: 0; left: 0;
          width: 44px;
          height: 44px;
          pointer-events: none;
          z-index: 999999;
          transform: translate(-50%, -50%);
          will-change: transform, left, top;

          background: #ff7a00;

          -webkit-mask-image: url("{{ asset('images/cursor/garuda-head.PNG') }}");
          mask-image: url("{{ asset('images/cursor/garuda-head.PNG') }}");
          -webkit-mask-repeat: no-repeat;
          mask-repeat: no-repeat;
          -webkit-mask-position: center;
          mask-position: center;
          -webkit-mask-size: contain;
          mask-size: contain;

          filter:
            drop-shadow(0 0 10px rgba(255,122,0,.35))
            drop-shadow(0 0 18px rgba(255,122,0,.18));
        }

        .garuda-cursor.is-down{
          transform: translate(-50%, -50%) scale(0.92);
          filter:
            drop-shadow(0 0 14px rgba(255,122,0,.45))
            drop-shadow(0 0 28px rgba(255,122,0,.22));
        }

        .garuda-particles{
          position: fixed;
          inset: 0;
          pointer-events: none;
          z-index: 999998;
        }

        .garuda-particle{
          position: fixed;
          width: 6px;
          height: 6px;
          border-radius: 999px;
          pointer-events: none;

          background: rgba(255, 122, 0, .95);
          box-shadow:
            0 0 10px rgba(255, 122, 0, .45),
            0 0 18px rgba(255, 122, 0, .22);

          transform: translate(-50%, -50%);
          animation: garudaParticleFade 520ms ease-out forwards;
          will-change: transform, opacity;
        }

        @keyframes garudaParticleFade{
          0%   { opacity: 1; transform: translate(-50%, -50%) scale(1); }
          100% { opacity: 0; transform: translate(-50%, -50%) scale(0.1); }
        }
      }

      @media (pointer: coarse) {
        .garuda-cursor,
        .garuda-particles{ display: none !important; }
      }

      /* ==========================================
         ANTI INSPECT (UX BLOCKER)
         - hanya menghambat user awam (bukan 100% secure)
      ========================================== */
      .antiinspect-overlay{
        position: fixed;
        inset: 0;
        z-index: 2147483647;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(0,0,0,.92);
        color: #fff;
        text-align: center;
        font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", "Liberation Sans", sans-serif;
      }
      .antiinspect-card{
        width: min(720px, 92vw);
        border-radius: 18px;
        padding: 22px 18px;
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.12);
        box-shadow: 0 20px 60px rgba(0,0,0,.5);
      }
      .antiinspect-title{
        margin: 0 0 10px;
        font-size: 18px;
        letter-spacing: .2px;
        color: #f97316;
        text-shadow: 0 0 16px rgba(249,115,22,.25);
      }
      .antiinspect-desc{
        margin: 0;
        line-height: 1.6;
        font-size: 14px;
        color: rgba(255,255,255,.85);
      }
    </style>

    {{-- style khusus halaman --}}
    @stack('styles')
</head>
<body class="antialiased">

    {{-- ✅ GARUDA CURSOR LAYERS --}}
    <div id="garudaCursor" class="garuda-cursor" aria-hidden="true"></div>
    <div id="garudaParticles" class="garuda-particles" aria-hidden="true"></div>

    {{-- ✅ ANTI-INSPECT OVERLAY (muncul saat DevTools terdeteksi) --}}
    <div id="antiInspectOverlay" class="antiinspect-overlay" aria-hidden="true">
      <div class="antiinspect-card" role="alert" aria-live="assertive">
        <h3 class="antiinspect-title">DevTools terdeteksi ⚠️</h3>
        <p class="antiinspect-desc">
          Halaman ini dilindungi untuk pengalaman belajar yang lebih baik.
          Silakan tutup DevTools untuk melanjutkan.
        </p>
      </div>
    </div>

    {{-- KONTEN GAME --}}
    @yield('content')

    {{-- scripts khusus halaman --}}
    @stack('scripts')

    {{-- OPTIONAL: kalau kamu mau game ikut update saat theme toggle di tab lain --}}
    <script>
      window.addEventListener('storage', function(e){
        if(e.key === 'piforrr-theme'){
          document.documentElement.setAttribute('data-theme', e.newValue || 'light');
        }
      });
    </script>

    {{-- ✅ GARUDA CURSOR JS (INTERNAL) --}}
    <script>
      (function () {
        const finePointer = window.matchMedia && window.matchMedia("(pointer: fine)").matches;
        if (!finePointer) return;

        const cursor = document.getElementById("garudaCursor");
        const holder = document.getElementById("garudaParticles");
        if (!cursor || !holder) return;

        let x = window.innerWidth / 2;
        let y = window.innerHeight / 2;

        let lastParticleAt = 0;

        function moveCursor(nx, ny){
          cursor.style.left = nx + "px";
          cursor.style.top  = ny + "px";
        }

        function spawnParticle(px, py){
          const now = performance.now();
          if (now - lastParticleAt < 18) return;
          lastParticleAt = now;

          const p = document.createElement("div");
          p.className = "garuda-particle";

          const size = 3 + Math.random() * 5;
          p.style.width  = size + "px";
          p.style.height = size + "px";

          const ox = (Math.random() - 0.5) * 10;
          const oy = (Math.random() - 0.5) * 10;

          p.style.left = (px + ox) + "px";
          p.style.top  = (py + oy) + "px";

          holder.appendChild(p);

          p.addEventListener("animationend", () => p.remove());
        }

        moveCursor(x, y);

        window.addEventListener("mousemove", (e) => {
          x = e.clientX; y = e.clientY;
          moveCursor(x, y);
          spawnParticle(x, y);
        }, { passive: true });

        document.addEventListener("mousedown", () => cursor.classList.add("is-down"), true);
        document.addEventListener("mouseup",   () => cursor.classList.remove("is-down"), true);

        document.addEventListener("mouseleave", () => cursor.style.opacity = "0", true);
        document.addEventListener("mouseenter", () => cursor.style.opacity = "", true);
      })();
    </script>

    {{-- ✅ ANTI INSPECT JS (INTERNAL) --}}
    <script>
      (function () {
        const overlay = document.getElementById('antiInspectOverlay');

        // ====== 1) Block right click ======
        document.addEventListener('contextmenu', function (e) {
          e.preventDefault();
        }, { capture: true });

        // ====== 2) Block common DevTools shortcuts ======
        document.addEventListener('keydown', function (e) {
          const key = (e.key || '').toLowerCase();

          // F12
          if (e.key === 'F12') {
            e.preventDefault();
            e.stopPropagation();
            return false;
          }

          // Ctrl/Meta + Shift + (I/J/C)
          if ((e.ctrlKey || e.metaKey) && e.shiftKey && (key === 'i' || key === 'j' || key === 'c')) {
            e.preventDefault();
            e.stopPropagation();
            return false;
          }

          // Ctrl/Meta + U (view-source)
          if ((e.ctrlKey || e.metaKey) && key === 'u') {
            e.preventDefault();
            e.stopPropagation();
            return false;
          }

          // Ctrl/Meta + S (save)
          if ((e.ctrlKey || e.metaKey) && key === 's') {
            e.preventDefault();
            e.stopPropagation();
            return false;
          }
        }, { capture: true });

        // ====== 3) Detect DevTools opened (heuristic) ======
        // Note: ini hanya menghambat user awam, bukan 100% secure.
        function showOverlay() {
          if (!overlay) return;
          overlay.style.display = 'flex';
          overlay.setAttribute('aria-hidden', 'false');

          // opsional: sembunyikan isi halaman
          document.documentElement.style.overflow = 'hidden';
        }

        function hideOverlay() {
          if (!overlay) return;
          overlay.style.display = 'none';
          overlay.setAttribute('aria-hidden', 'true');

          document.documentElement.style.overflow = '';
        }

        const TH = 160; // threshold perbedaan outer-inner
        let lastState = false;

        setInterval(() => {
          const widthDiff = window.outerWidth - window.innerWidth;
          const heightDiff = window.outerHeight - window.innerHeight;

          const opened = (widthDiff > TH) || (heightDiff > TH);

          // update only when changed (biar ringan)
          if (opened !== lastState) {
            lastState = opened;
            opened ? showOverlay() : hideOverlay();
          }
        }, 450);
      })();
    </script>

</body>
</html>
