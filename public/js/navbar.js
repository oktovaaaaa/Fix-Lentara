// public/js/navbar.js
(function () {
  const html = document.documentElement;

  const themeToggle = document.getElementById('themeToggle');
  const drawerTheme = document.getElementById('drawerTheme');
  const links = [...document.querySelectorAll('.nav-btn')];
  const indicator = document.querySelector('.active-indicator');
  const navLinksBox = document.getElementById('navLinks');
  const hamburger = document.getElementById('hamburger');
  const drawer = document.getElementById('drawer');
  const overlay = document.getElementById('drawerOverlay');
  const closeDrawerBtn = document.getElementById('closeDrawer');
  const navPill = document.querySelector('.nav-pill');

  // ===== THEME (light/dark) =====
  function applyTheme(mode) {
    html.setAttribute('data-theme', mode);
    localStorage.setItem('piforrr-theme', mode);
  }
  applyTheme(localStorage.getItem('piforrr-theme') || 'light');

  function toggleTheme() {
    const next = (html.getAttribute('data-theme') === 'light') ? 'dark' : 'light';
    applyTheme(next);
  }

  if (themeToggle) themeToggle.addEventListener('click', toggleTheme);
  if (drawerTheme) drawerTheme.addEventListener('click', toggleTheme);

  // ===== NAV ACTIVE INDICATOR (desktop only) =====
  function isMobile() {
    return window.matchMedia('(max-width: 860px)').matches;
  }

  function moveIndicator(targetBtn) {
    if (!indicator || !targetBtn || isMobile()) return;
    const b = targetBtn.getBoundingClientRect();
    const p = navLinksBox.getBoundingClientRect();
    const w = Math.max(110, b.width + 10);
    const x = b.left - p.left + (b.width - w) / 2;
    indicator.style.transform = `translateX(${x}px)`;
    indicator.style.width = `${w}px`;
    indicator.style.opacity = 1;
  }

  function hideIndicator() {
    if (indicator) indicator.style.opacity = 0;
  }

  // set awal
  const initial = document.querySelector('.nav-btn.is-active') || links[0];
  if (!isMobile()) moveIndicator(initial); else hideIndicator();

  // klik btn → scroll ke section / redirect Home (mode island)
  links.forEach(btn => {
    btn.addEventListener('click', () => {
      const url = btn.dataset.url;
      if (url) {
        // MODE ISLAND: tombol Home → redirect ke Budaya Indonesia
        window.location.href = url;
        return;
      }

      // behaviour lama untuk tombol yang scroll ke section
      const targetSelector = btn.dataset.target;
      const target = targetSelector ? document.querySelector(targetSelector) : null;

      links.forEach(l => l.classList.remove('is-active'));
      btn.classList.add('is-active');
      if (!isMobile()) moveIndicator(btn);

      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // update aktif berdasarkan scroll (IntersectionObserver)
  const io = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const id = `#${entry.target.id}`;
        const btn = links.find(b => b.dataset.target === id);
        if (btn) {
          links.forEach(l => l.classList.remove('is-active'));
          btn.classList.add('is-active');
          if (!isMobile()) moveIndicator(btn);
        }
      }
    });
  }, { rootMargin: "-40% 0px -55% 0px", threshold: 0.01 });

  document.querySelectorAll('section').forEach(sec => io.observe(sec));

  // ===== DROPDOWN "PULAU" DI NAVBAR DESKTOP =====
  const dropdowns = document.querySelectorAll('.nav-dropdown');

  function closeAllDropdowns() {
    dropdowns.forEach(drop => {
      drop.classList.remove('open');
      const toggle = drop.querySelector('.nav-dropdown-toggle');
      if (toggle) toggle.setAttribute('aria-expanded', 'false');
    });
  }

  dropdowns.forEach(drop => {
    const toggle = drop.querySelector('.nav-dropdown-toggle');
    const menu = drop.querySelector('.nav-dropdown-menu');
    const labelSpan = drop.querySelector('.dropdown-label');
    if (!toggle || !menu) return;

    // ==== INITIAL STATE dari Blade (selectedIsland) ====
    const currentIsland = drop.dataset.currentIsland;
    if (currentIsland && labelSpan) {
      labelSpan.textContent = currentIsland;
      drop.classList.add('nav-dropdown--selected');
      if (navLinksBox) {
        navLinksBox.classList.add('nav-links--transparent');
      }
    }

    // klik tombol "Pulau" → buka/tutup dropdown
    toggle.addEventListener('click', (e) => {
      // biar nggak ikut klik di luar / dokumen
      e.stopPropagation();
      const willOpen = !drop.classList.contains('open');
      closeAllDropdowns();
      if (willOpen) {
        drop.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
      }
    });

    // klik item di dropdown → ubah label, besarkan tombol, transparan, lalu redirect
    menu.querySelectorAll('.dropdown-item').forEach(item => {
      item.addEventListener('click', (e) => {
        e.preventDefault();

        const islandName = item.dataset.island || item.textContent.trim();
        const url = item.dataset.url;

        if (labelSpan && islandName) {
          labelSpan.textContent = islandName;
        }

        // tandai dropdown sudah memilih pulau → CSS akan membesarkan tombol
        drop.classList.add('nav-dropdown--selected');

        // jadikan kapsul nav-links transparan (walaupun sekarang memang sudah transparan)
        if (navLinksBox) {
          navLinksBox.classList.add('nav-links--transparent');
        }

        // tutup dropdown
        drop.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');

        // redirect ke halaman pulau
        if (url) {
          window.location.href = url;
        }
      });
    });
  });

  // klik area luar navbar → tutup semua dropdown
  document.addEventListener('click', () => {
    closeAllDropdowns();
  });

  // ===== MOBILE DRAWER =====
  function openDrawer() {
    drawer.classList.add('open');
    overlay.classList.add('show');
    hamburger.classList.add('is-open');
    hamburger.setAttribute('aria-expanded', 'true');
    drawer.setAttribute('aria-hidden', 'false');
  }

  function closeDrawer() {
    drawer.classList.remove('open');
    overlay.classList.remove('show');
    if (hamburger) {
      hamburger.classList.remove('is-open');
      hamburger.setAttribute('aria-expanded', 'false');
    }
    drawer.setAttribute('aria-hidden', 'true');
  }

  if (hamburger) {
    hamburger.addEventListener('click', () => {
      const willOpen = !drawer.classList.contains('open');
      willOpen ? openDrawer() : closeDrawer();
    });
  }

  if (overlay) overlay.addEventListener('click', closeDrawer);
  if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeDrawer);

  // close drawer dengan ESC
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeDrawer();
  });

  // klik link di drawer → bisa scroll (kalau #anchor) atau redirect (kalau data-url)
  document.querySelectorAll('.drawer-link').forEach(a => {
    a.addEventListener('click', (e) => {
      const url = a.dataset.url;

      if (url) {
        e.preventDefault();
        window.location.href = url;
        closeDrawer();
        return;
      }

      const targetSelector = a.dataset.target || a.getAttribute('href');
      const isHash = targetSelector && targetSelector.startsWith('#');
      const target = isHash ? document.querySelector(targetSelector) : null;

      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }

      closeDrawer();
    });
  });

  // indicator tetap rapi saat resize
  window.addEventListener('resize', () => {
    if (isMobile()) {
      hideIndicator();
    } else {
      const active = document.querySelector('.nav-btn.is-active') || links[0];
      moveIndicator(active);
    }
  });

  // efek shrink/bounce saat scroll
  let scrollTimer;
  window.addEventListener('scroll', () => {
    if (!navPill) return;
    navPill.classList.add('scrolling');
    navPill.classList.remove('idle-bounce');
    clearTimeout(scrollTimer);
    scrollTimer = setTimeout(() => {
      navPill.classList.remove('scrolling');
      navPill.classList.add('idle-bounce');
      setTimeout(() => navPill.classList.remove('idle-bounce'), 200);
    }, 180);
  }, { passive: true });

})();
