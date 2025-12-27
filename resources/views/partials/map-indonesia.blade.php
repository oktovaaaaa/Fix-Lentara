```blade
{{-- resources/views/partials/map-indonesia.blade.php --}}
<section
    id="map-indonesia-section"
    class="mi-section py-12 px-4 sm:px-6 flex justify-center bg-[var(--bg-body)] text-[var(--txt-body)]">

    {{-- Leaflet CSS --}}
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-o9N1j7kGStjzD76vJbE6QpWwZiUrdV+0s8H0wXkM3C4="
          crossorigin=""/>

    <style>
        /* ========= WRAPPER + GRID ========= */
        #map-indonesia-section.mi-section {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .mi-shell {
            width: 100%;
            max-width: 1200px;
        }

        .mi-container {
            width: 100%;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); /* 2 kolom SAMA BESAR */
            gap: 2rem;
            align-items: stretch; /* tinggi baris sama */
        }

        @media (max-width: 900px) {
            .mi-container {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        /* ========= MAP COLUMN ========= */
        .mi-map-wrapper {
            position: relative;
            border-radius: 26px;
            padding: 8px;
            background: transparent;
        }

        .mi-map-neon {
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            z-index: 0;
            background: conic-gradient(
                from var(--mi-border-angle),
                rgba(249, 115, 22, 0),
                rgba(249, 115, 22, 0.1) 40deg,
                #f97316 90deg,
                #fdba74 140deg,
                rgba(249, 115, 22, 0.18) 200deg,
                rgba(249, 115, 22, 0) 260deg,
                rgba(249, 115, 22, 0.2) 310deg,
                #f97316 340deg,
                rgba(249, 115, 22, 0) 360deg
            );
            -webkit-mask:
                linear-gradient(#000 0 0) content-box,
                linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            padding: 6px;
            filter: blur(4px);
            opacity: 0.9;
            animation: mi-neon-spin 8s linear infinite;
        }

        .mi-map-inner {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            background: #000;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);

            height: 100%;
            min-height: 380px; /* FIX: pastikan tinggi map valid */
        }

        #mi-map-indonesia {
            width: 100%;
            height: 100%;
            min-height: 380px; /* FIX: leafet butuh height/min-height */
        }

        @media (max-width: 900px) {
            .mi-map-inner {
                min-height: 340px;
            }
            #mi-map-indonesia {
                min-height: 340px;
            }
        }

        /* ========= TITLE AREA (ATAS KEDUA KOLOM) ========= */
        .mi-title {
            font-size: clamp(2rem, 3vw, 2.6rem);
            font-weight: 800;
            margin-bottom: 0.5rem;
            display: inline-block;

            background-image: linear-gradient(90deg, #f97316, #fdba74, #f97316);
            background-size: 200% auto;
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;

            animation: mi-title-glow 4s ease-in-out infinite;
        }

        .mi-subtitle {
            font-size: 0.95rem;
            color: var(--muted, #9ca3af);
            margin-bottom: 1.75rem;
            max-width: 760px;
        }

        @keyframes mi-title-glow {
            0%, 100% {
                background-position: 0% 50%;
                text-shadow: 0 0 18px rgba(249, 115, 22, 0.5);
                transform: translateY(0);
            }
            50% {
                background-position: 100% 50%;
                text-shadow: 0 0 28px rgba(253, 186, 116, 0.7);
                transform: translateY(-2px);
            }
        }

        /* ========= RIGHT INFO COLUMN ========= */
        .mi-info-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }

        /* ==== NEON BORDER CARD ==== */
        @property --mi-border-angle {
            syntax: "<angle>";
            inherits: false;
            initial-value: 0deg;
        }

        .mi-card {
            position: relative;
            max-width: 680px;
            border-radius: 20px;
            height: 100%;
            display: flex;
        }

        .mi-card-glow {
            position: absolute;
            inset: -6px;
            border-radius: inherit;
            padding: 10px;
            z-index: 0;
            pointer-events: none;
            background: conic-gradient(
                from var(--mi-border-angle),
                rgba(249, 115, 22, 0),
                rgba(249, 115, 22, 0.1) 30deg,
                #f97316 80deg,
                #fdba74 120deg,
                rgba(249, 115, 22, 0.1) 180deg,
                rgba(249, 115, 22, 0) 240deg,
                rgba(249, 115, 22, 0.15) 300deg,
                #f97316 330deg,
                rgba(249, 115, 22, 0) 360deg
            );
            -webkit-mask:
                linear-gradient(#000 0 0) content-box,
                linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            filter: blur(4px);
            opacity: 0.95;
            animation: mi-neon-spin 8s linear infinite;
        }

        @keyframes mi-neon-spin {
            to {
                --mi-border-angle: 360deg;
            }
        }

        .mi-card-inner {
            position: relative;
            border-radius: 18px;
            background: var(--card, radial-gradient(circle at top left, #111827, #020617));
            padding: 1.75rem 1.9rem;
            box-shadow:
                0 16px 36px rgba(0, 0, 0, 0.45),
                0 0 0 1px rgba(255, 255, 255, 0.06);
            z-index: 1;
            color: var(--txt-body, #f9fafb);
            display: flex;
            flex-direction: column;
        }

        .mi-card-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 0.25rem 0.85rem;
            margin-bottom: 0.6rem;
            border-radius: 999px;
            background: linear-gradient(to right, #fef3c7, #f97316);
            color: #7c2d12;
        }

        .mi-card-heading {
            font-size: 1.15rem;
            margin-bottom: 0.4rem;
            font-weight: 800;
        }

        .mi-card-text {
            font-size: 0.95rem;
            line-height: 1.65;
            color: var(--muted, #9ca3af);
        }

        .mi-card-list {
            margin-top: 0.9rem;
            font-size: 0.92rem;
            color: var(--txt-body, #e5e7eb);
        }

        .mi-card-list li {
            margin-bottom: 0.45rem;
            display: flex;
            align-items: flex-start;
            gap: 0.55rem;
            line-height: 1.35;
        }

        .mi-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #f97316;
            box-shadow: 0 0 10px rgba(249, 115, 22, 0.8);
            margin-top: 0.35rem;
            flex: 0 0 auto;
        }

        .mi-sources {
            margin-top: 0.9rem;
            font-size: 0.78rem;
            color: var(--muted, #9ca3af);
            line-height: 1.55;
        }

        .mi-sources a {
            text-decoration: underline;
        }

        /* ========= CUSTOM POPUP ========= */
        .mi-leaflet-popup .leaflet-popup-content-wrapper {
            border-radius: 22px;
            background: #111827;
            color: #f9fafb;
            box-shadow:
                0 20px 40px rgba(0, 0, 0, 0.65),
                0 0 0 2px rgba(248, 113, 113, 0.35);
        }

        html:not(.dark):not(.theme-dark) .mi-leaflet-popup .leaflet-popup-content-wrapper {
            background: #ffffff;
            color: #111827;
            box-shadow:
                0 20px 40px rgba(15, 23, 42, 0.35),
                0 0 0 1px rgba(15, 23, 42, 0.08);
        }

        .mi-leaflet-popup .leaflet-popup-tip {
            background: #b91c1c;
        }

        .mi-popup-card {
            min-width: 260px;
            max-width: 380px;
        }

        .mi-popup-title {
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 0.4rem;
        }

        .mi-popup-underline {
            width: 60px;
            height: 3px;
            border-radius: 999px;
            background: linear-gradient(to right, #fecaca, #ef4444);
            margin-bottom: 0.8rem;
        }

        .mi-popup-row {
            margin-bottom: 0.45rem;
            font-size: 0.92rem;
        }

        .mi-popup-row strong {
            display: inline-block;
            width: 98px;
            color: #fef2f2;
        }

        html:not(.dark):not(.theme-dark) .mi-popup-row strong {
            color: #111827;
        }

        .mi-popup-row span {
            color: #e5e7eb;
        }

        html:not(.dark):not(.theme-dark) .mi-popup-row span {
            color: #374151;
        }

        .mi-popup-small {
            font-size: 0.8rem;
            color: #9ca3af;
            margin-top: 0.55rem;
        }

        .leaflet-container a {
            color: #f97316;
        }
    </style>

    <div class="mi-shell">
        {{-- TITLE DI ATAS KEDUA KOLOM --}}
        <div class="text-center mb-8 sm:mb-10">
            <h2 class="mi-title mx-auto tracking-tight">
                Peta Interaktif Nusantara
            </h2>
            <p class="mi-subtitle mx-auto">
                Jelajahi wilayah Indonesia lewat peta. Klik area berwarna untuk melihat ringkasan budaya dan karakter
                tiap region, lalu lanjutkan ke halaman pulau untuk detail suku, tradisi, destinasi, dan kuliner.
            </p>
        </div>

        <div class="mi-container">
            {{-- LEFT: MAP --}}
            <div class="mi-map-wrapper">
                <div class="mi-map-neon"></div>
                <div class="mi-map-inner">
                    <div id="mi-map-indonesia"></div>
                </div>
            </div>

            {{-- RIGHT: NEON CARD INFO (SHOWCASE, tanpa bahas teknis) --}}
            <div class="mi-info-wrapper">
                <div class="mi-card">
                    <div class="mi-card-glow"></div>
                    <div class="mi-card-inner">
                        <div class="mi-card-badge">Nusantara</div>
                        <h3 class="mi-card-heading">Fakta yang membuat Indonesia unik</h3>
                        <p class="mi-card-text">
                            Indonesia adalah negara kepulauan dengan skala dan keragaman yang luar biasa—wilayah,
                            bahasa, serta suku bangsa. Peta ini memberi gambaran besar sebelum kamu masuk ke detail
                            tiap pulau di menu <strong>Pulau</strong>.
                        </p>

                        <ul class="mi-card-list">
                            <li><span class="mi-dot"></span> <span><strong>17.380 pulau</strong> bernama & berkoordinat (pembaruan 2024).</span></li>
                            <li><span class="mi-dot"></span> <span><strong>718 bahasa daerah</strong> tercatat dalam Peta Bahasa.</span></li>
                            <li><span class="mi-dot"></span> <span><strong>1.340 suku bangsa</strong> tersebar di seluruh Indonesia.</span></li>
                        </ul>

                        <div class="mt-5">
                            <div class="mi-card-badge">Cara Eksplorasi</div>
                            <h3 class="mi-card-heading">Klik wilayah, lanjut ke pulau</h3>
                            <p class="mi-card-text">
                                Klik area berwarna untuk ringkasan region. Setelah itu, buka halaman pulau untuk
                                melihat suku-suku utama, tradisi, serta rekomendasi destinasi dan kuliner.
                            </p>

                            <div class="mi-sources">
                                Sumber:
                                <a target="_blank" rel="noopener" href="https://sipulau.big.go.id/news/11">BIG</a> •
                                <a target="_blank" rel="noopener" href="https://petabahasa.kemdikbud.go.id/">Peta Bahasa Kemendikbud</a> •
                                <a target="_blank" rel="noopener" href="https://indonesiabaik.id/infografis/sebaran-jumlah-suku-di-indonesia">IndonesiaBaik</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-o9N1j7kGStjzD76vJbE6QpWwZiUrdV+0s8H0wXkM3C4="
            crossorigin=""></script>

    <script>
        (function () {
            const geojsonUrl = "{{ asset('data/map-indonesia.geojson') }}";

            // ====== INISIALISASI MAP ======
            const map = L.map('mi-map-indonesia', {
                zoomControl: true,
                scrollWheelZoom: true
            }).setView([-2.5, 118], 5);

            // CUSTOM ATTRIBUTION: Lentara Nusantara | Leaflet
            map.attributionControl.setPrefix(
                '<a href="/" target="_blank" rel="noopener">Lentara Nusantara</a> | ' +
                '© <a href="https://leafletjs.com" target="_blank" rel="noopener">Leaflet</a>'
            );

            // ====== BASEMAP (LIGHT & DARK) + FALLBACK OSM ======
            const maptilerKey = @json(config('services.maptiler.key'));

            const osmTiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            });

            const lightTiles = maptilerKey
                ? L.tileLayer(`https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=${maptilerKey}`, {
                    maxZoom: 20,
                    tileSize: 256,
                    attribution:
                        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> ' +
                        '&copy; <a href="https://www.maptiler.com/copyright/">MapTiler</a>'
                })
                : osmTiles;

            const darkTiles = maptilerKey
                ? L.tileLayer(`https://api.maptiler.com/maps/streets-v2-dark/{z}/{x}/{y}.png?key=${maptilerKey}`, {
                    maxZoom: 20,
                    tileSize: 256,
                    attribution:
                        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> ' +
                        '&copy; <a href="https://www.maptiler.com/copyright/">MapTiler</a>'
                })
                : osmTiles;

            let baseLayer = null;

            function setBaseMapTheme(isDark) {
                if (baseLayer) {
                    map.removeLayer(baseLayer);
                }
                baseLayer = isDark ? darkTiles : lightTiles;
                baseLayer.addTo(map);
            }

            // ====== DETEKSI TEMA AWAL ======
            function detectIsDark() {
                const html = document.documentElement;
                const body = document.body;

                if (
                    html.classList.contains('dark') ||
                    html.classList.contains('theme-dark') ||
                    body.classList.contains('dark') ||
                    body.classList.contains('theme-dark')
                ) {
                    return true;
                }

                try {
                    const stored = localStorage.getItem('theme');
                    if (stored === 'dark') return true;
                } catch (e) {}

                return false;
            }

            function syncThemeFromDOM() {
                setBaseMapTheme(detectIsDark());
                setTimeout(() => map.invalidateSize(true), 160); // FIX blank map
            }

            syncThemeFromDOM();

            function hookThemeButton(id) {
                const btn = document.getElementById(id);
                if (!btn) return;

                btn.addEventListener('click', function () {
                    setTimeout(syncThemeFromDOM, 30);
                });
            }

            hookThemeButton('themeToggle');
            hookThemeButton('drawerTheme');

            // ====== LAYER GEOJSON ======
            let geojsonLayer = null;

            function styleWilayah(feature) {
                const p = feature.properties || {};
                const warna = p.warna || '#f97316';

                return {
                    color: '#111827',
                    weight: 1.2,
                    fillColor: warna,
                    fillOpacity: 0.65
                };
            }

            function safeText(v) {
                if (v === null || v === undefined) return '-';
                if (Array.isArray(v)) return v.filter(Boolean).join(', ') || '-';
                return String(v).trim() || '-';
            }

            function onEachWilayah(feature, layer) {
                layer.on('mouseover', function () {
                    layer.setStyle({
                        weight: 2.5,
                        fillOpacity: 0.85
                    });
                });

                layer.on('mouseout', function () {
                    if (geojsonLayer) {
                        geojsonLayer.resetStyle(layer);
                    }
                });

                layer.on('click', function (e) {
                    const p = feature.properties || {};

                    const nama      = safeText(p.nama || p.name || 'Wilayah Indonesia');
                    const ringkas   = safeText(p.ringkas || p.deskripsi || p.makna || '-');
                    const suku      = safeText(p.suku_utama || p.suku || '-');
                    const destinasi = safeText(p.destinasi_ikonik || p.destinasi || '-');
                    const kuliner   = safeText(p.kuliner_khas || p.kuliner || '-');
                    const wilayah   = safeText(p.digunakan_di || p.wilayah || p.provinsi || '-');

                    const content =
                        `<div class="mi-popup-card">
                            <div class="mi-popup-title">${nama}</div>
                            <div class="mi-popup-underline"></div>

                            <div class="mi-popup-row">
                                <strong>Ringkas:</strong>
                                <span>${ringkas}</span>
                            </div>

                            <div class="mi-popup-row">
                                <strong>Suku:</strong>
                                <span>${suku}</span>
                            </div>

                            <div class="mi-popup-row">
                                <strong>Destinasi:</strong>
                                <span>${destinasi}</span>
                            </div>

                            <div class="mi-popup-row">
                                <strong>Kuliner:</strong>
                                <span>${kuliner}</span>
                            </div>

                            <div class="mi-popup-row">
                                <strong>Wilayah:</strong>
                                <span>${wilayah}</span>
                            </div>

                            <div class="mi-popup-small">
                                Klik wilayah lain pada peta untuk melihat ringkasan berbeda.
                            </div>
                        </div>`;

                    L.popup({
                        closeButton: true,
                        className: 'mi-leaflet-popup',
                        maxWidth: 380
                    })
                        .setLatLng(e.latlng)
                        .setContent(content)
                        .openOn(map);
                });
            }

            fetch(geojsonUrl)
                .then(response => response.json())
                .then(data => {
                    geojsonLayer = L.geoJSON(data, {
                        style: styleWilayah,
                        onEachFeature: onEachWilayah
                    }).addTo(map);

                    try {
                        map.fitBounds(geojsonLayer.getBounds(), { padding: [20, 20] });
                        map.setZoom(map.getZoom() - 1);
                    } catch (err) {
                        console.warn('Tidak bisa fitBounds, pakai view default.', err);
                    }

                    setTimeout(() => map.invalidateSize(true), 160); // FIX blank map after layer
                })
                .catch(err => {
                    console.error('Gagal memuat GeoJSON:', err);
                    setTimeout(() => map.invalidateSize(true), 160);
                });
        })();
    </script>
</section>
