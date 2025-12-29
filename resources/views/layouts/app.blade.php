{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- penting buat fetch POST --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Lentara Islands</title>

    {{-- SET THEME PALING AWAL (default: light) --}}
    <script>
        (function () {
            const saved = localStorage.getItem('piforrr-theme') || 'light';
            document.documentElement.setAttribute('data-theme', saved);
        })();
    </script>

    {{-- ✅ NAV LOADER HEAD RESUME (ANTI 0% DI HALAMAN BARU) --}}
    <script>
      (function () {
        try {
          var KEY_START = "lentara:navigation:start";
          var KEY_PCT   = "lentara:navigation:pct";
          var s = sessionStorage.getItem(KEY_START);

          if (s) {
            // show loader sejak first paint
            document.documentElement.classList.add("nav-loading");

            // set resume progress sejak first paint (agar tidak pernah terlihat 0%)
            var p = sessionStorage.getItem(KEY_PCT);
            if (!p) p = "99";
            document.documentElement.style.setProperty("--loader-resume", p);
          }
        } catch (e) {}
      })();
    </script>

    {{-- Tailwind via CDN (tanpa Vite) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- CSS Navbar (sekaligus theme) --}}
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

<style>
/* ==========================================
   THEME AWARE LOADER
   - Anti kedip via html.nav-loading
   - Anti ulang 0% via --loader-resume
========================================== */

/* saat navigasi: paksa loader ON sebelum JS body jalan */
html.nav-loading .page-loader{
  opacity: 1;
  pointer-events: auto;
  transition: none; /* cegah flicker */
}

.page-loader{
  position: fixed;
  inset: 0;
  z-index: 99999;

  background: var(--bg-body);
  display: flex;
  align-items: center;
  justify-content: center;

  opacity: 0;
  pointer-events: none;
  transition: opacity .18s ease;
}

.page-loader.is-on{
  opacity: 1;
  pointer-events: auto;
}

/* LOGO/IMAGE */
.page-loader__img{
  width: 100vw;
  height: 100vh;
  object-fit: contain; /* aman, tidak terpotong */
  object-position: center;
  user-select: none;
  pointer-events: none;
}

/* BOTTOM PROGRESS */
.page-loader__bottom{
  position: absolute;
  left: 0;
  right: 0;
  bottom: 24px;
  padding: 0 24px;
}

.page-loader__bar{
  width: 100%;
  height: 10px;
  border-radius: 999px;
  background: color-mix(in srgb, var(--txt-body) 15%, transparent);
  overflow: hidden;
}

.page-loader__barFill{
  height: 100%;
  /* ✅ INI KUNCI: first paint pakai resume, bukan 0% */
  width: calc(var(--loader-resume, 0) * 1%);
  border-radius: 999px;
  background: linear-gradient(
    90deg,
    var(--brand),
    var(--brand-2),
    var(--brand)
  );
  transition: width .1s linear;
}

/* kalau sedang nav-loading, jangan animasi dari 0 (biar gak terlihat "mulai ulang") */
html.nav-loading .page-loader__barFill{
  transition: none;
}

.page-loader__meta{
  margin-top: 8px;
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  font-weight: 700;
  color: var(--muted);
}
</style>

</head>
<body class="antialiased">
    <div class="min-h-screen flex flex-col">

        {{-- NAVBAR UTAMA --}}
        @include('partials.navbar')

        @include('partials.page-loader')

        {{-- KONTEN HALAMAN --}}
        <main class="flex-1">
            @yield('hero')
            @yield('content')
        </main>

<footer class="w-full border-t border-slate-800/40 bg-[var(--bg-body)]">
    <div class="w-full flex flex-col items-center gap-3 py-4">

        {{-- Teks di ATAS --}}
        <div class="text-xs text-slate-500">
            © {{ date('Y') }} Piforrr G6 Lentara Nusantara.
        </div>

        {{-- Gambar FULL kiri-kanan --}}
        <img
            src="{{ asset('images/icon/footer.PNG') }}"
            alt="Lentara Footer"
            class="w-full h-24 object-cover block opacity-90"
            loading="lazy"
        />
    </div>
</footer>

    </div>

    {{-- === CHATBOT FLOATING NUSANTARA AI (di luar container utama) === --}}
    @include('components.nusantara-chatbot')

    {{-- scripts tambahan dari child view --}}
    @stack('scripts')

    {{-- JS Navbar (theme toggle, drawer, indikator) --}}
    <script src="{{ asset('js/navbar.js') }}"></script>

    {{-- SCRIPT CHATBOT NUSANTARA AI --}}
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn   = document.getElementById('nusantara-toggle');
        const panel       = document.getElementById('nusantara-panel');
        const closeBtn    = document.getElementById('nusantara-close');
        const form        = document.getElementById('nusantara-form');
        const input       = document.getElementById('nusantara-input');
        const messagesBox = document.getElementById('nusantara-messages');

        if (!toggleBtn || !panel) return;

        let messages = []; // riwayat chat
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // === FUNGSI BUKA/TUTUP DENGAN ANIMASI + HIDE/SHOW BUTTON ===
        function openPanel() {
            // sembunyikan tombol floating
            toggleBtn.classList.add('hidden');

            // tampilkan panel dengan animasi
            panel.classList.remove('pointer-events-none');
            panel.classList.add('opacity-100', 'translate-y-0', 'scale-100');
            panel.classList.remove('opacity-0', 'translate-y-2', 'scale-95');
        }

        function closePanel() {
            // animasi keluar dulu
            panel.classList.add('opacity-0', 'translate-y-2', 'scale-95');
            panel.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
            panel.classList.add('pointer-events-none');

            // setelah animasi, munculkan tombol floating lagi
            setTimeout(() => {
                toggleBtn.classList.remove('hidden');
            }, 200);
        }

        function togglePanel() {
            const isClosed = panel.classList.contains('pointer-events-none');
            if (isClosed) {
                openPanel();
            } else {
                closePanel();
            }
        }

        function addMessage(role, content) {
            const wrapper = document.createElement('div');
            wrapper.className = 'flex mb-1 ' + (role === 'user' ? 'justify-end' : 'justify-start');

            const bubble = document.createElement('div');
            bubble.className =
                'max-w-[80%] px-3 py-2 rounded-2xl text-xs leading-relaxed ' +
                (role === 'user'
                    ? 'bg-amber-600 text-white rounded-br-sm'
                    : 'bg-slate-800 text-slate-50 rounded-bl-sm');

            bubble.textContent = content;
            wrapper.appendChild(bubble);
            messagesBox.appendChild(wrapper);
            messagesBox.scrollTop = messagesBox.scrollHeight;
        }

        // event tombol buka
        toggleBtn.addEventListener('click', () => {
            openPanel();
            setTimeout(() => input?.focus(), 220);
        });

        // event tombol close (X)
        closeBtn.addEventListener('click', () => {
            closePanel();
        });

        // submit form
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = input.value.trim();
            if (!text) return;

            // tampilkan pesan user
            addMessage('user', text);
            messages.push({ role: 'user', content: text });
            input.value = '';

            // tampilkan "sedang mengetik..."
            const loadingId = 'nusantara-loading-' + Date.now();
            const loadingWrapper = document.createElement('div');
            loadingWrapper.id = loadingId;
            loadingWrapper.className = 'flex mb-1 justify-start';
            loadingWrapper.innerHTML = `
                <div class="max-w-[80%] px-3 py-2 rounded-2xl text-xs bg-slate-800 text-slate-300 rounded-bl-sm">
                    Nusantara AI sedang memikirkan jawabannya...
                </div>
            `;
            messagesBox.appendChild(loadingWrapper);
            messagesBox.scrollTop = messagesBox.scrollHeight;

            try {
                const res = await fetch('{{ route('nusantara.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ messages }),
                });

                const data = await res.json();
                loadingWrapper.remove();

                if (data.reply) {
                    messages.push({ role: 'assistant', content: data.reply });
                    addMessage('assistant', data.reply);
                } else {
                    addMessage('assistant', 'Maaf, Nusantara AI lagi bingung menjawab. Coba lagi ya.');
                }
            } catch (error) {
                console.error(error);
                loadingWrapper.remove();
                addMessage('assistant', 'Terjadi kesalahan jaringan. Coba lagi sebentar lagi ya.');
            }
        });
    });
    </script>

{{-- js untuk trransisi--}}
<script>
(function () {
  const loader = document.getElementById("pageLoader");
  const bar    = document.getElementById("pageLoaderBar");
  const pctEl  = document.getElementById("pageLoaderPct");
  const txtEl  = document.getElementById("pageLoaderText");
  if (!loader || !bar || !pctEl || !txtEl) return;

  const KEY_START = "lentara:navigation:start";
  const KEY_PCT   = "lentara:navigation:pct";

  let raf = null;
  let startedAt = 0;
  let finished = false;
  let running = false;

  let lastSavedPct = -1;
  let lastSaveAt = 0;

  function clampPct(p) { return Math.max(0, Math.min(100, p)); }

  function syncResumeVar(p) {
    document.documentElement.style.setProperty("--loader-resume", String(Math.round(p)));
  }

  function show() {
    loader.classList.add("is-on");
    loader.setAttribute("aria-hidden", "false");
  }

  function hide() {
    loader.classList.remove("is-on");
    loader.setAttribute("aria-hidden", "true");
    document.documentElement.classList.remove("nav-loading");
    document.documentElement.style.removeProperty("--loader-resume");
  }

  // ✅ Hard reset saat back/forward supaya gak nyangkut
  function hardReset() {
    finished = true;
    running = false;
    if (raf) cancelAnimationFrame(raf);
    raf = null;

    try { sessionStorage.removeItem(KEY_START); } catch(e) {}
    try { sessionStorage.removeItem(KEY_PCT); } catch(e) {}

    hide();
  }

  function setProgress(p) {
    const v = clampPct(p);

    bar.style.width = v + "%";
    pctEl.textContent = Math.round(v) + "%";

    // sync css-var (buat first paint halaman tujuan)
    syncResumeVar(v);

    // simpan (throttle)
    const now = Date.now();
    const rounded = Math.round(v);
    if (rounded !== lastSavedPct && (now - lastSaveAt) > 120) {
      lastSavedPct = rounded;
      lastSaveAt = now;
      try { sessionStorage.setItem(KEY_PCT, String(rounded)); } catch(e) {}
    }
  }

  function networkSpeed() {
    const c = navigator.connection || {};
    return c.effectiveType || "3g";
  }

  function curve(t) {
    const speed = networkSpeed();
    const k = speed === "4g" ? 500 :
              speed === "3g" ? 900 :
              speed === "2g" ? 1500 : 2200;

    let p = 100 - 95 * Math.exp(-t / k);
    if (!finished) p = Math.min(p, 99.2);
    return Math.max(2, p);
  }

  function loop() {
    if (!running) return;
    const t = Date.now() - startedAt;
    setProgress(curve(t));
    raf = requestAnimationFrame(loop);
  }

  function start() {
    if (running) return;

    finished = false;
    running = true;
    startedAt = Date.now();

    txtEl.textContent = "Memuat halaman…";

    try { sessionStorage.setItem(KEY_START, String(startedAt)); } catch(e) {}
    document.documentElement.classList.add("nav-loading");

    setProgress(2);
    show();
    loop();
  }

  function finish() {
    if (finished) return;

    let hasNav = null;
    try { hasNav = sessionStorage.getItem(KEY_START); } catch(e) { hasNav = null; }
    if (!hasNav && !running) return;

    finished = true;
    running = false;
    if (raf) cancelAnimationFrame(raf);

    syncResumeVar(100);
    try { sessionStorage.setItem(KEY_PCT, "100"); } catch(e) {}

    const minShow = 120;
    const elapsed = Date.now() - (startedAt || Date.now());
    const delay = Math.max(0, minShow - elapsed);

    setTimeout(() => {
      setProgress(100);
      txtEl.textContent = "Selesai";

      setTimeout(() => {
        try { sessionStorage.removeItem(KEY_START); } catch(e) {}
        try { sessionStorage.removeItem(KEY_PCT); } catch(e) {}
        hide();
      }, 220);
    }, delay);
  }

// =====================================================
// INTERCEPT CLICK (support <a>, button[data-url], DIV[data-url], dll)
// =====================================================
document.addEventListener("click", function (e) {
  const el =
    e.target.closest("a[href]") ||
    e.target.closest("[data-url]");

  if (!el) return;

  const href = el.getAttribute("href") || el.dataset?.url;
  if (!href) return;

  if (href.startsWith("#")) return;
  if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
  if (el.getAttribute("target") === "_blank") return;

  const url = new URL(href, location.href);
  if (url.origin !== location.origin) return;
  if (url.href === location.href) return;

  e.preventDefault();
  start();
  setTimeout(() => { location.href = url.href; }, 90);
}, true);


  // =====================================================
  // RESUME DI HALAMAN TUJUAN
  // =====================================================
  let navStart = null;
  try { navStart = sessionStorage.getItem(KEY_START); } catch(e) { navStart = null; }

  if (navStart) {
    startedAt = Number(navStart) || Date.now();
    running = true;
    finished = false;

    txtEl.textContent = "Memuat halaman…";
    show();

    let resumePct = 99;
    try { resumePct = Number(sessionStorage.getItem(KEY_PCT) || "99"); } catch(e) {}
    if (!Number.isFinite(resumePct) || resumePct <= 0) resumePct = 99;
    resumePct = Math.min(resumePct, 99.2);

    bar.style.width = resumePct + "%";
    pctEl.textContent = Math.round(resumePct) + "%";
    syncResumeVar(resumePct);

    loop();
  }

  // =====================================================
  // FINISH NORMAL
  // =====================================================
  window.addEventListener("DOMContentLoaded", finish, { once: true });
  window.addEventListener("load", finish, { once: true });

  // =====================================================
  // ✅ FIX BACK/FORWARD (BFCache)
  // =====================================================
  window.addEventListener("pageshow", function (e) {
    // Kalau halaman di-restore dari bfcache / back-forward, paksa reset
    const navEntry = performance.getEntriesByType?.("navigation")?.[0];
    const isBackForward = (navEntry && navEntry.type === "back_forward");
    if (e.persisted || isBackForward) {
      hardReset();
    }
  });

  // Jaga-jaga: kalau page dibekukan (BFCache), stop animasinya
  window.addEventListener("pagehide", function () {
    if (raf) cancelAnimationFrame(raf);
    raf = null;
    running = false;
  });

})();
</script>

</body>
</html>
