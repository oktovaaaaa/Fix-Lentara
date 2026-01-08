// public/js/admin-sidebar.js
let sidebar  = document.querySelector(".sidebar");
let closeBtn = document.querySelector("#btn");

/* =========================
   Sidebar open/close (KEEP)
========================= */
if (closeBtn) {
  closeBtn.addEventListener("click", () => {
    sidebar.classList.toggle("open");
    menuBtnChange();
  });
}

function menuBtnChange() {
  if (!closeBtn) return;

  if (sidebar.classList.contains("open")) {
    closeBtn.classList.replace("bx-menu", "bx-menu-alt-right");
  } else {
    closeBtn.classList.replace("bx-menu-alt-right", "bx-menu");
  }
}

// set initial correct icon state for menu button
menuBtnChange();

/* =========================
   Theme toggle (NEW)
========================= */
const htmlEl = document.documentElement;
const themeToggleBtn = document.querySelector("#themeToggle");
const themeIcon = document.querySelector("#themeIcon");
const themeText = document.querySelector("#themeText");
const themeTooltip = document.querySelector("#themeTooltip");

function getSavedTheme() {
  return localStorage.getItem("admin_theme"); // "light" | "dark" | null
}

function setTheme(theme) {
  if (theme === "dark") {
    htmlEl.setAttribute("data-theme", "dark");
  } else {
    htmlEl.removeAttribute("data-theme");
  }

  localStorage.setItem("admin_theme", theme);

  // update UI text/icon/tooltip
  if (themeIcon) {
    themeIcon.classList.remove("bx-sun", "bx-moon");
    themeIcon.classList.add(theme === "dark" ? "bx-sun" : "bx-moon");
  }

  if (themeText) {
    themeText.textContent = theme === "dark" ? "Tema (Dark)" : "Tema (Light)";
  }

  if (themeTooltip) {
    themeTooltip.textContent = theme === "dark" ? "Tema: Dark" : "Tema: Light";
  }

  if (themeToggleBtn) {
    themeToggleBtn.setAttribute(
      "aria-label",
      theme === "dark" ? "Ganti ke Light mode" : "Ganti ke Dark mode"
    );
  }
}

function toggleTheme() {
  const isDark = htmlEl.getAttribute("data-theme") === "dark";
  setTheme(isDark ? "light" : "dark");
}

// init theme on load
(function initTheme() {
  const saved = getSavedTheme();
  if (saved === "dark" || saved === "light") {
    setTheme(saved);
  } else {
    // default: light (no attribute)
    setTheme("light");
  }
})();

if (themeToggleBtn) {
  themeToggleBtn.addEventListener("click", () => {
    toggleTheme();
  });
}



// =====================================================================================

/* =========================================================
   ADMIN ABOUTS INDEX (APPEND) - tribes picker + header lookup
   Uses window.__ABOUTS_INDEX__ from the blade
========================================================= */
function initAdminAboutsIndex() {
  const root = document.querySelector('[data-page="admin-abouts-index"]');
  if (!root) return;

  const payload = window.__ABOUTS_INDEX__ || {};
  const tribesConfig = payload.tribesConfig || {};
  const selectedTribeFromQuery = payload.selectedTribeFromQuery || "";
  const lookupUrlBase = payload.lookupUrlBase || "";

  const islandSelect = document.getElementById("islandSelect");
  const tribeSelect = document.getElementById("tribeSelect");

  const pageIslandId = document.getElementById("pageIslandId");
  const pageTribeKey = document.getElementById("pageTribeKey");

  const itemIslandId = document.getElementById("itemIslandId");
  const itemTribeKey = document.getElementById("itemTribeKey");

  const labelSmall = document.getElementById("labelSmall");
  const heroTitle = document.getElementById("heroTitle");
  const heroDesc = document.getElementById("heroDescription");
  const pageMoreLink = document.getElementById("pageMoreLink");

  if (!islandSelect || !tribeSelect) return;

  function getSelectedIslandSlug() {
    const opt = islandSelect.options[islandSelect.selectedIndex];
    return opt ? (opt.dataset.slug || "") : "";
  }

  function fillTribes(selectedTribe) {
    const slug = getSelectedIslandSlug();
    const tribes = slug && tribesConfig[slug] ? tribesConfig[slug] : [];

    tribeSelect.innerHTML = '<option value="">Pilih suku...</option>';

    tribes.forEach((t) => {
      const o = document.createElement("option");
      o.value = t;
      o.textContent = t;
      if (selectedTribe && selectedTribe === t) o.selected = true;
      tribeSelect.appendChild(o);
    });

    tribeSelect.disabled = tribes.length === 0;
  }

  async function loadHeader() {
    const islandId = islandSelect.value || "";
    const tribeKey = tribeSelect.value || "";

    if (pageIslandId) pageIslandId.value = islandId;
    if (pageTribeKey) pageTribeKey.value = tribeKey;
    if (itemIslandId) itemIslandId.value = islandId;
    if (itemTribeKey) itemTribeKey.value = tribeKey;

    if (!islandId || !tribeKey) return;
    if (!lookupUrlBase) return;

    const url =
      lookupUrlBase +
      `?island_id=${encodeURIComponent(islandId)}&tribe_key=${encodeURIComponent(tribeKey)}`;

    try {
      const res = await fetch(url, { headers: { Accept: "application/json" } });
      if (!res.ok) return;

      const data = await res.json();

      if (!data) {
        if (labelSmall) labelSmall.value = "";
        if (heroTitle) heroTitle.value = "";
        if (heroDesc) heroDesc.value = "";
        if (pageMoreLink) pageMoreLink.value = "";
        return;
      }

      if (labelSmall) labelSmall.value = data.label_small ?? "";
      if (heroTitle) heroTitle.value = data.hero_title ?? "";
      if (heroDesc) heroDesc.value = data.hero_description ?? "";
      if (pageMoreLink) pageMoreLink.value = data.more_link ?? "";
    } catch (e) {
      // silent
    }
  }

  // init dropdown tribes
  fillTribes(selectedTribeFromQuery || "");

  // init hidden values
  if (pageIslandId) pageIslandId.value = islandSelect.value || "";
  if (itemIslandId) itemIslandId.value = islandSelect.value || "";
  if (pageTribeKey) pageTribeKey.value = tribeSelect.value || "";
  if (itemTribeKey) itemTribeKey.value = tribeSelect.value || "";

  // init header load if selection exists
  if ((islandSelect.value || "") && (tribeSelect.value || "")) {
    loadHeader();
  }

  islandSelect.addEventListener("change", () => {
    fillTribes("");
    if (pageIslandId) pageIslandId.value = islandSelect.value || "";
    if (itemIslandId) itemIslandId.value = islandSelect.value || "";
    if (pageTribeKey) pageTribeKey.value = "";
    if (itemTribeKey) itemTribeKey.value = "";
  });

  tribeSelect.addEventListener("change", () => {
    loadHeader();
  });
}

document.addEventListener("DOMContentLoaded", () => {
  initAdminAboutsIndex();
});

