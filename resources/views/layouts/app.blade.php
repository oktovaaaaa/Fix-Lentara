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




    </div>
<footer class="w-full border-t border-slate-800/40 bg-[var(--bg-body)] relative overflow-hidden">

    {{-- GARIS NEON TIPIS --}}
    <div class="absolute top-0 left-0 w-full h-[2px]
                bg-gradient-to-r from-orange-500/0 via-orange-500/60 to-orange-500/0
                animate-pulse"></div>

    <div class="w-full flex flex-col items-center gap-4 py-6 relative z-10">

        {{-- TEXT ATAS --}}
        <div class="text-center text-xs leading-relaxed text-[var(--txt-body)]">
            © {{ date('Y') }}
            <span class="font-medium text-[var(--txt-body)]">
                Piforrr G6 – Lentara Nusantara
            </span><br>
            <span class="text-[var(--txt-body)]">
                Platform digital berbasis AI untuk mengenal budaya dan suku bangsa Indonesia.
            </span>
        </div>

        {{-- DEVELOPER SIGNATURE --}}
        <div class="flex items-center gap-3 text-xs text-[var(--txt-body)]">

            {{-- LOGO = LOGIN --}}
            <a
                href="{{ route('login') }}"
                aria-label="Admin Login"
                title="Admin Login"
                class="group inline-flex items-center"
            >
                <img
                    src="{{ asset('images/icon/oktovaaaaa.PNG') }}"
                    alt="Developer Logo"
                    class="w-6 h-6 rounded-full object-cover
                           ring-1 ring-orange-500/40
                           transition duration-200
                           group-hover:ring-orange-400
                           group-hover:scale-105"
                    loading="lazy"
                />
            </a>

            <span class="text-[var(--txt-body)]">
                Dibangun dengan <span class="text-orange-500">❤</span> oleh
                <span class="font-semibold text-[var(--txt-body)]">
                    Oktovaaaaa
                </span>
            </span>

        </div>

        {{-- ICON LINKS --}}
        <div class="flex items-center gap-4 text-xs">

            {{-- PORTFOLIO (JANGAN DIUBAH ICONNYA) --}}
            <a
                href="https://www.oktovaaaaa.cloud/"
                target="_blank"
                aria-label="Portfolio"
                title="Portfolio"
                class="group inline-flex items-center justify-center text-[var(--muted)]"
            >
                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="currentColor"
                     class="w-5 h-5 transition duration-200 group-hover:text-orange-400"
                     aria-hidden="true">
                    <path d="M12 12c2.76 0 5-2.24 5-5S14.76 2 12 2
                             7 4.24 7 7s2.24 5 5 5zm0 2
                             c-3.33 0-10 1.67-10 5v1h20v-1
                             c0-3.33-6.67-5-10-5z"/>
                </svg>
            </a>

            <span class="text-[var(--muted)]">•</span>

            {{-- GITHUB (JANGAN DIUBAH ICONNYA) --}}
            <a
                href="https://github.com/oktovaaaaa"
                target="_blank"
                aria-label="GitHub"
                title="GitHub"
                class="group inline-flex items-center justify-center text-[var(--muted)]"
            >
                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="currentColor"
                     class="w-5 h-5 transition duration-200 group-hover:text-orange-400"
                     aria-hidden="true">
                    <path d="M12 .5C5.73.5.75 5.7.75 12.2
                             c0 5.2 3.44 9.6 8.2 11.16
                             .6.12.82-.27.82-.6v-2.2
                             c-3.34.75-4.04-1.67-4.04-1.67
                             -.55-1.42-1.35-1.8-1.35-1.8
                             -1.1-.78.08-.77.08-.77
                             1.22.09 1.86 1.29 1.86 1.29
                             1.08 1.9 2.84 1.35 3.53 1.03
                             .11-.82.42-1.35.76-1.66
                             -2.66-.31-5.46-1.38-5.46-6.13
                             0-1.35.46-2.45 1.23-3.31
                             -.12-.31-.54-1.57.12-3.27
                             0 0 1-.33 3.3 1.27
                             .96-.28 1.99-.42 3.01-.42
                             1.02 0 2.05.14 3.01.42
                             2.3-1.6 3.3-1.27 3.3-1.27
                             .66 1.7.24 2.96.12 3.27
                             .76.86 1.23 1.96 1.23 3.31
                             0 4.77-2.8 5.81-5.47 6.12
                             .43.39.81 1.14.81 2.31v3.42
                             c0 .33.22.72.83.6
                             4.75-1.56 8.18-5.96 8.18-11.16
                             C23.25 5.7 18.27.5 12 .5Z"/>
                </svg>
            </a>

            <span class="text-[var(--muted)]">•</span>

            {{-- INSTAGRAM (FIX: PASTI MUNCUL) --}}
            <a
                href="https://www.instagram.com/oktovaaaaa/"
                target="_blank"
                aria-label="Instagram"
                title="Instagram"
                class="group inline-flex items-center justify-center text-[var(--muted)]"
            >
                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 16 16"
                     fill="currentColor"
                     class="w-5 h-5 transition duration-200 group-hover:text-orange-400"
                     aria-hidden="true">
                    <path d="M8 0C5.827 0 5.555.01 4.703.048c-.85.039-1.432.174-1.942.372A3.9 3.9 0 0 0 1.35 1.35
                             c-.42.42-.72.92-.93 1.411-.198.51-.333 1.092-.372 1.942C.01 5.555 0 5.827 0 8s.01 2.445.048 3.297
                             c.039.85.174 1.432.372 1.942.21.49.51.99.93 1.41.42.42.92.72 1.411.93.51.198 1.092.333 1.942.372
                             C5.555 15.99 5.827 16 8 16s2.445-.01 3.297-.048c.85-.039 1.432-.174 1.942-.372.49-.21.99-.51 1.41-.93
                             .42-.42.72-.92.93-1.411.198-.51.333-1.092.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.297
                             c-.039-.85-.174-1.432-.372-1.942a3.9 3.9 0 0 0-.93-1.41 3.9 3.9 0 0 0-1.41-.93c-.51-.198-1.092-.333-1.942-.372
                             C10.445.01 10.173 0 8 0zm0 3.9A4.1 4.1 0 1 1 3.9 8 4.1 4.1 0 0 1 8 3.9zm0 6.7A2.6 2.6 0 1 0 5.4 8
                             2.6 2.6 0 0 0 8 10.6zm4.25-6.95a.95.95 0 1 1-1.9 0 .95.95 0 0 1 1.9 0z"/>
                </svg>
            </a>

        </div>

    </div>

    {{-- FOOTER IMAGE --}}
    <img
        src="{{ asset('images/icon/footer.JPEG') }}"
        alt="Lentara Footer"
        class="w-full h-24 object-cover block opacity-90"
        loading="lazy"
    />

</footer>



        {{-- === MUSIC FLOATING BUTTON === --}}
@include('components.music-toggle')

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
