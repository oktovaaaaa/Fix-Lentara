// public/js/navbar.js
(function () {
  const html = document.documentElement;

  const themeToggle = document.getElementById('themeToggle');
  const drawerTheme = document.getElementById('drawerTheme');

  const navLinksBox = document.getElementById('navLinks');
  const indicator = document.querySelector('.active-indicator');

  const mobileMenuToggle = document.getElementById('mobileMenuToggle');
  const drawer = document.getElementById('drawer');
  const overlay = document.getElementById('drawerOverlay');
  const closeDrawerBtn = document.getElementById('closeDrawer');

  const navPill = document.querySelector('.nav-pill');

  // Semua tombol nav (termasuk dropdown toggle karena dia .nav-btn)
  const navBtns = Array.from(document.querySelectorAll('.nav-btn'));
  // Dropdown toggle khusus Pulau (desktop)
  const islandsDropdown = document.querySelector('.nav-dropdown[data-dropdown="islands"]');
  const islandsToggle = islandsDropdown ? islandsDropdown.querySelector('.nav-dropdown-toggle') : null;

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

  function clearActive() {
    navBtns.forEach(b => b.classList.remove('is-active'));
  }

  function getDefaultBtn() {
    // prioritas: button yang punya data-default="1"
    const def = navBtns.find(b => b.dataset.default === '1');
    if (def) return def;

    // fallback: yang punya .is-active
    const active = navBtns.find(b => b.classList.contains('is-active'));
    if (active) return active;

    // fallback terakhir: tombol pertama
    return navBtns[0] || null;
  }

  // ===== ACTIVE INDICATOR (DESKTOP) =====
  function moveIndicator(targetBtn) {
    if (!indicator || !targetBtn || isMobile() || !navLinksBox) return;

    const boxRect = navLinksBox.getBoundingClientRect();
    const btnRect = targetBtn.getBoundingClientRect();

    // ukuran & posisi (lebih stabil daripada offsetLeft untuk case dropdown/flex)
    const w = btnRect.width;
    const x = btnRect.left - boxRect.left;

    // napas, biar mirip style lama
    const pad = 6;

    indicator.style.transform = `translateX(${x - pad}px)`;
    indicator.style.width = `${w + (pad * 2)}px`;
    indicator.style.opacity = 1;
  }

  function hideIndicator() {
    if (indicator) indicator.style.opacity = 0;
  }

  function setActive(btn, { move = true } = {}) {
    if (!btn) return;
    clearActive();
    btn.classList.add('is-active');
    if (!isMobile() && move) moveIndicator(btn);
  }

  // ===== INIT INDICATOR =====
  const initial = document.querySelector('.nav-btn.is-active') || getDefaultBtn();
  if (!isMobile()) moveIndicator(initial);
  else hideIndicator();

  // ===== CLICK HANDLER (SCROLL / REDIRECT) =====
  navBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      // Dropdown toggle: hanya open/close dropdown (handled below),
      // tapi tetap boleh jadi active indicator kalau section #islands aktif via observer.
      if (btn.classList.contains('nav-dropdown-toggle')) {
        // jangan trigger scroll / active dari click, supaya sesuai behavior lama
        e.preventDefault();
        e.stopPropagation();
        return;
      }

      const url = btn.dataset.url;
      if (url) {
        window.location.href = url;
        return;
      }

      const targetSelector = btn.dataset.target;
      const target = targetSelector ? document.querySelector(targetSelector) : null;

      setActive(btn);

      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ===== SECTION OBSERVER (LIKE NAVBAR LAMA) =====
  // Map sectionId -> button
  function findBtnByTarget(idHash) {
    // cari tombol biasa yang match data-target
    const normal = navBtns.find(b => !b.classList.contains('nav-dropdown-toggle') && b.dataset.target === idHash);
    if (normal) return normal;

    // fallback khusus: section islands -> dropdown toggle Pulau
    if (idHash === '#islands' && islandsToggle) return islandsToggle;

    return null;
  }

  const sections = Array.from(document.querySelectorAll('section'))
    .filter(sec => sec && sec.id);

  if (sections.length) {
    const io = new IntersectionObserver((entries) => {
      // pilih entry yang paling "kuat" terlihat
      const visible = entries
        .filter(en => en.isIntersecting && en.target && en.target.id)
        .sort((a, b) => (b.intersectionRatio || 0) - (a.intersectionRatio || 0));

      if (!visible.length) return;

      const top = visible[0];
      const idHash = `#${top.target.id}`;
      const btn = findBtnByTarget(idHash);
      if (!btn) return;

      // setActive tapi jangan memaksa open dropdown
      setActive(btn);
    }, { rootMargin: "-40% 0px -55% 0px", threshold: [0.01, 0.05, 0.1, 0.2, 0.35] });

    sections.forEach(sec => io.observe(sec));
  }

  // ===== DROPDOWN "PULAU" (DESKTOP) =====
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

    // initial state label dari blade (mode island)
    const currentIsland = drop.dataset.currentIsland;
    if (currentIsland && labelSpan) {
      labelSpan.textContent = currentIsland;
      drop.classList.add('nav-dropdown--selected');
      if (navLinksBox) navLinksBox.classList.add('nav-links--transparent');
    }

    // Toggle dropdown open/close
    toggle.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();

      const willOpen = !drop.classList.contains('open');
      closeAllDropdowns();

      if (willOpen) {
        drop.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
      }
    });

    // Klik item dropdown -> ganti label -> redirect
    menu.querySelectorAll('.dropdown-item').forEach(item => {
      item.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();

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

    html.classList.add('drawer-open');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    if (!drawer || !overlay) return;
    drawer.classList.remove('open');
    overlay.classList.remove('show');
    drawer.setAttribute('aria-hidden', 'true');

    html.classList.remove('drawer-open');
    document.body.style.overflow = '';
  }

  // Toggle drawer dengan klik circle logo di mobile
  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();

      // di desktop: klik logo circle diarahkan ke home (biar aman)
      if (!isMobile()) {
        const homeUrl = mobileMenuToggle.dataset.homeUrl;
        if (homeUrl) window.location.href = homeUrl;
        return;
      }

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

  // ===== RESIZE =====
  window.addEventListener('resize', () => {
    if (isMobile()) {
      hideIndicator();
    } else {
      closeDrawer();
      const active = document.querySelector('.nav-btn.is-active') || getDefaultBtn();
      moveIndicator(active);
    }
  });

  // ===== SCROLL EFFECT (desktop pill) =====
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
