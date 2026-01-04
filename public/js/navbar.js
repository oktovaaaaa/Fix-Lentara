// public/js/navbar.js
(function () {
  const html = document.documentElement;

  const themeToggle = document.getElementById('themeToggle');
  const drawerTheme = document.getElementById('drawerTheme');

  const links = [...document.querySelectorAll('.nav-btn')];
  const indicator = document.querySelector('.active-indicator');
  const navLinksBox = document.getElementById('navLinks');

  const mobileMenuToggle = document.getElementById('mobileMenuToggle');
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

  // ===== Helpers =====
  function isMobile() {
    return window.matchMedia('(max-width: 860px)').matches;
  }

  // ===== NAV ACTIVE INDICATOR (desktop only) =====
  function moveIndicator(targetBtn) {
    if (!indicator || !targetBtn || isMobile() || !navLinksBox) return;
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
  if (!isMobile()) moveIndicator(initial);
  else hideIndicator();

  // klik btn → scroll ke section / redirect Home (mode island)
  links.forEach(btn => {
    btn.addEventListener('click', () => {
      const url = btn.dataset.url;
      if (url) {
        window.location.href = url;
        return;
      }

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
      if (navLinksBox) navLinksBox.classList.add('nav-links--transparent');
    }

    toggle.addEventListener('click', (e) => {
      e.stopPropagation();
      const willOpen = !drop.classList.contains('open');
      closeAllDropdowns();
      if (willOpen) {
        drop.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
      }
    });

    menu.querySelectorAll('.dropdown-item').forEach(item => {
      item.addEventListener('click', (e) => {
        e.preventDefault();

        const islandName = item.dataset.island || item.textContent.trim();
        const url = item.dataset.url;

        if (labelSpan && islandName) labelSpan.textContent = islandName;

        drop.classList.add('nav-dropdown--selected');
        if (navLinksBox) navLinksBox.classList.add('nav-links--transparent');

        drop.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');

        if (url) window.location.href = url;
      });
    });
  });

  document.addEventListener('click', () => {
    closeAllDropdowns();
  });

  // ===== MOBILE DRAWER =====
  function openDrawer() {
    if (!drawer || !overlay) return;
    drawer.classList.add('open');
    overlay.classList.add('show');
    drawer.setAttribute('aria-hidden', 'false');

    // ✅ hide icon circle lewat class di html (CSS handle)
    html.classList.add('drawer-open');

    // Prevent body scroll
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    if (!drawer || !overlay) return;
    drawer.classList.remove('open');
    overlay.classList.remove('show');
    drawer.setAttribute('aria-hidden', 'true');

    // ✅ show icon circle lagi
    html.classList.remove('drawer-open');

    // Restore body scroll
    document.body.style.overflow = '';
  }

  // Toggle drawer dengan klik circle logo di mobile
  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener('click', (e) => {
      if (!isMobile()) return; // desktop: normal link
      e.preventDefault();
      e.stopPropagation();

      const willOpen = !(drawer && drawer.classList.contains('open'));
      willOpen ? openDrawer() : closeDrawer();
    });
  }

  if (overlay) overlay.addEventListener('click', closeDrawer);
  if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeDrawer);

  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeDrawer();
  });

  // klik link di drawer → scroll (kalau #anchor) atau redirect (kalau data-url)
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

  // ✅ Resize: rapikan indicator + kalau pindah ke desktop, pastikan drawer tertutup
  window.addEventListener('resize', () => {
    if (isMobile()) {
      hideIndicator();
    } else {
      // jika berubah ke desktop, tutup drawer biar tidak nyangkut
      closeDrawer();
      const active = document.querySelector('.nav-btn.is-active') || links[0];
      moveIndicator(active);
    }
  });

  // efek shrink/bounce saat scroll (desktop saja karena mobile nav-pill disembunyikan)
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


