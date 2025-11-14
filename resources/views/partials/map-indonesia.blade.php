{{-- resources/views/partials/map-indonesia.blade.php --}}
<section id="map-indonesia-section" class="mi-section">

    {{-- Leaflet CSS --}}
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-o9N1j7kGStjzD76vJbE6QpWwZiUrdV+0s8H0wXkM3C4="
          crossorigin=""/>

    <style>
        /* ========= WRAPPER SECTION ========= */
        #map-indonesia-section.mi-section {
            padding: 3rem 1.5rem;
            display: flex;
            justify-content: center;
            /* ikut variable global supaya sinkron tema */
            background: var(--bg-body, #030712);
            color: var(--txt-body, #f9fafb);
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .mi-container {
            width: 100%;
            max-width: 1200px;
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(0, 1.4fr);
            gap: 2rem;
            align-items: stretch;
        }

        @media (max-width: 900px) {
            .mi-container {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        /* ========= MAP COLUMN ========= */
       .mi-map-wrapper {
    position: relative;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
    background: #000;

    /* >>> ini penting: tinggi wrapper fix / fleksibel */
    /* height: min(70vh, 650px);  contoh: maksimal 650px, atau 70% tinggi layar */
}

#mi-map-indonesia {
    width: 100%;
    height: 100%;     /* >>> ganti min-height jadi height full*/
}


        /* ========= RIGHT INFO COLUMN (NEON CARD) ========= */
        .mi-info-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .mi-title {
            font-size: clamp(2rem, 3vw, 2.6rem);
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--txt-body, #f9fafb);
        }

        .mi-subtitle {
            font-size: 0.95rem;
            color: var(--muted, #9ca3af);
            margin-bottom: 1.75rem;
            max-width: 480px;
        }

        /* ==== NEON BORDER ==== */
        @property --mi-border-angle {
            syntax: "<angle>";
            inherits: false;
            initial-value: 0deg;
        }

        .mi-card {
            position: relative;
            max-width: 480px;
            border-radius: 20px;
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
            /* ikut var(--card) supaya warnanya nyambung tema */
            background: var(--card, radial-gradient(circle at top left, #111827, #020617));
            padding: 1.75rem 1.9rem;
            box-shadow:
                0 16px 36px rgba(0, 0, 0, 0.45),
                0 0 0 1px rgba(255, 255, 255, 0.06);
            z-index: 1;
            color: var(--txt-body, #f9fafb);
        }

        .mi-card-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.25rem 0.85rem;
            margin-bottom: 0.6rem;
            border-radius: 999px;
            background: linear-gradient(to right, #fef3c7, #f97316);
            color: #7c2d12;
        }

        .mi-card-heading {
            font-size: 1.1rem;
            margin-bottom: 0.4rem;
        }

        .mi-card-text {
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--muted, #9ca3af);
        }

        .mi-card-list {
            margin-top: 0.9rem;
            font-size: 0.9rem;
            color: var(--txt-body, #e5e7eb);
        }

        .mi-card-list li {
            margin-bottom: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mi-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #f97316;
            box-shadow: 0 0 10px rgba(249, 115, 22, 0.8);
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

        /* kalau tema light, biar popup nggak terlalu gelap */
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
            max-width: 320px;
        }

        .mi-popup-title {
            font-size: 1.25rem;
            font-weight: 700;
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
            margin-bottom: 0.35rem;
            font-size: 0.9rem;
        }

        .mi-popup-row strong {
            display: inline-block;
            width: 90px;
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
            margin-top: 0.5rem;
        }

        .leaflet-container a {
            color: #f97316;
        }
    </style>

    <div class="mi-container">
        {{-- LEFT: MAP --}}
        <div class="mi-map-wrapper">
            <div id="mi-map-indonesia"></div>
        </div>

        {{-- RIGHT: NEON CARD INFO --}}
        <div class="mi-info-wrapper">
            <h2 class="mi-title">Peta Interaktif Indonesia</h2>
            <p class="mi-subtitle">
                Klik area berwarna pada peta untuk melihat informasi yang diambil dari
                <code>map-indonesia.geojson</code>. Tema peta akan ikut tombol tema di navbar
                (light / dark).
            </p>

            <div class="mi-card">
                <div class="mi-card-glow"></div>
                <div class="mi-card-inner">
                    <div class="mi-card-badge">Panduan</div>
                    <h3 class="mi-card-heading">Cara Menggunakan Peta</h3>
                    <p class="mi-card-text">
                        Gunakan scroll untuk zoom, drag untuk menggeser peta, lalu klik polygon
                        berwarna untuk membuka popup detail wilayah. Tema peta otomatis sinkron
                        dengan tombol ☀️ / 🌙 di navbar.
                    </p>
                    <ul class="mi-card-list">
                        <li><span class="mi-dot"></span> GeoJSON: <code>public/data/map-indonesia.geojson</code></li>
                        <li><span class="mi-dot"></span> Basemap: MapTiler Streets (light & dark)</li>
                        <li><span class="mi-dot"></span> Neon border mengikuti desain timeline-mu</li>
                    </ul>
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

            // ====== DEFINISI 2 BASEMAP (LIGHT & DARK) ======
            const lightTiles = L.tileLayer(
                "https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key={{ config('services.maptiler.key') }}",
                {
                    maxZoom: 20,
                    tileSize: 256,
                    attribution:
                        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> ' +
                        '&copy; <a href="https://www.maptiler.com/copyright/">MapTiler</a>'
                }
            );

            const darkTiles = L.tileLayer(
                "https://api.maptiler.com/maps/streets-v2-dark/{z}/{x}/{y}.png?key={{ config('services.maptiler.key') }}",
                {
                    maxZoom: 20,
                    tileSize: 256,
                    attribution:
                        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> ' +
                        '&copy; <a href=\"https://www.maptiler.com/copyright/\">MapTiler</a>'
                }
            );

            let baseLayer = null;

            function setBaseMapTheme(isDark) {
                if (baseLayer) {
                    map.removeLayer(baseLayer);
                }
                baseLayer = isDark ? darkTiles : lightTiles;
                baseLayer.addTo(map);
            }

            // ====== DETEKSI TEMA AWAL (cocok dengan sistem kamu sebisa mungkin) ======
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

                // fallback: cek localStorage.theme kalau ada
                try {
                    const stored = localStorage.getItem('theme');
                    if (stored === 'dark') return true;
                } catch (e) {
                    // abaikan error localStorage
                }

                return false;
            }

            function syncThemeFromDOM() {
                const dark = detectIsDark();
                setBaseMapTheme(dark);
            }

            // pas pertama load
            syncThemeFromDOM();

            // ====== HOOK KE TOMBOL NAVBAR & DRAWER ======
            function hookThemeButton(id) {
                const btn = document.getElementById(id);
                if (!btn) return;

                btn.addEventListener('click', function () {
                    // kasih sedikit delay supaya script utama navbar sempat update class / localStorage
                    setTimeout(syncThemeFromDOM, 30);
                });
            }

            hookThemeButton('themeToggle');  // tombol ☀️ / 🌙 di navbar
            hookThemeButton('drawerTheme');  // tombol ganti tema di drawer mobile

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

                    const content =
                        `<div class="mi-popup-card">
                            <div class="mi-popup-title">${p.nama || 'Wilayah Indonesia'}</div>
                            <div class="mi-popup-underline"></div>
                            <div class="mi-popup-row">
                                <strong>Aksara:</strong>
                                <span>${p.aksara || '-'}</span>
                            </div>
                            <div class="mi-popup-row">
                                <strong>Kata Khas:</strong>
                                <span>${p.kata_khas || '-'}</span>
                            </div>
                            <div class="mi-popup-row">
                                <strong>Makna:</strong>
                                <span>${p.makna || '-'}</span>
                            </div>
                            <div class="mi-popup-row">
                                <strong>Digunakan di:</strong>
                                <span>${p.digunakan_di || '-'}</span>
                            </div>
                            <div class="mi-popup-small">
                                Klik wilayah lain pada peta untuk melihat informasi berbeda.
                            </div>
                        </div>`;

                    L.popup({
                        closeButton: true,
                        className: 'mi-leaflet-popup',
                        maxWidth: 340
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
                    } catch (err) {
                        console.warn('Tidak bisa fitBounds, pakai view default.', err);
                    }
                })
                .catch(err => {
                    console.error('Gagal memuat GeoJSON:', err);
                });
        })();
    </script>
</section>
