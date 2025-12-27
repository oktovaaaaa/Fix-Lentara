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
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 2rem;
            align-items: stretch;
        }

        @media (max-width: 900px) {
            .mi-container {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        /* ========= JUDUL KONSISTEN (NEON ORANGE TENGAH) ========= */
        .mi-title-section {
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
        }

        .mi-title {
            font-size: clamp(2.2rem, 4vw, 3rem);
            font-weight: 800;
            margin-bottom: 0.5rem;
            display: inline-block;
            position: relative;

            background: linear-gradient(
                90deg,
                #ff6b00 0%,
                #ff8c42 25%,
                #ffaa6b 50%,
                #ff8c42 75%,
                #ff6b00 100%
            );
            background-size: 200% auto;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: mi-title-glow 3s ease-in-out infinite;
        }

        .mi-title-decoration {
            width: 120px;
            height: 4px;
            margin: 0.8rem auto;
            background: linear-gradient(90deg, transparent, #ff6b00, transparent);
            border-radius: 2px;
            position: relative;
        }

        .mi-title-decoration::before,
        .mi-title-decoration::after {
            content: '';
            position: absolute;
            width: 8px;
            height: 8px;
            background: #ff6b00;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            box-shadow: 0 0 12px #ff6b00;
        }

        .mi-title-decoration::before {
            left: 0;
        }

        .mi-title-decoration::after {
            right: 0;
        }

        .mi-subtitle {
            font-size: 1.1rem;
            color: var(--muted);
            max-width: 760px;
            margin: 0 auto;
            line-height: 1.6;
        }

        @keyframes mi-title-glow {
            0%, 100% {
                background-position: 0% 50%;
                text-shadow: 0 0 20px rgba(255, 107, 0, 0.3),
                             0 0 40px rgba(255, 140, 66, 0.2);
            }
            50% {
                background-position: 100% 50%;
                text-shadow: 0 0 30px rgba(255, 107, 0, 0.5),
                             0 0 60px rgba(255, 140, 66, 0.3);
            }
        }

        /* ========= MAP DENGAN GRADIEN & EFEK ========= */
        .mi-map-wrapper {
            position: relative;
            border-radius: 26px;
            padding: 8px;
            background: transparent;
            overflow: hidden;
        }

        @property --mi-border-angle {
            syntax: "<angle>";
            inherits: false;
            initial-value: 0deg;
        }

        .mi-map-glow {
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            z-index: 0;
            background: conic-gradient(
                from var(--mi-border-angle),
                rgba(255, 107, 0, 0),
                rgba(255, 140, 66, 0.2) 40deg,
                #ff6b00 90deg,
                #ffaa6b 140deg,
                rgba(255, 140, 66, 0.3) 200deg,
                rgba(255, 107, 0, 0) 260deg,
                rgba(255, 140, 66, 0.25) 310deg,
                #ff6b00 340deg,
                rgba(255, 107, 0, 0) 360deg
            );
            -webkit-mask:
                linear-gradient(#000 0 0) content-box,
                linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            padding: 6px;
            filter: blur(8px);
            opacity: 0.8;
            animation: mi-glow-spin 12s linear infinite;
        }

        .mi-map-inner {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            background: linear-gradient(145deg, var(--card), var(--card-bg-dark));
            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            height: 100%;
            min-height: 420px;
        }

        /* Dark/Light mode adjustment for map inner */
        html[data-theme="dark"] .mi-map-inner {
            background: linear-gradient(145deg, #0a0a0a, #1a1a1a);
        }

        html[data-theme="light"] .mi-map-inner {
            background: linear-gradient(145deg, #f8fafc, #e2e8f0);
            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        #mi-map-indonesia {
            width: 100%;
            height: 100%;
            min-height: 420px;
        }

        /* EFEK PULAU DI MAP */
        .leaflet-interactive {
            transition: all 0.3s ease;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .leaflet-interactive:hover {
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.4));
            transform: translateY(-1px);
        }

        @media (max-width: 900px) {
            .mi-map-inner {
                min-height: 360px;
            }
            #mi-map-indonesia {
                min-height: 360px;
            }
        }

        @keyframes mi-glow-spin {
            to {
                --mi-border-angle: 360deg;
            }
        }

        /* ========= KARTU INFORMASI ========= */
        .mi-info-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }

        .mi-card {
            position: relative;
            max-width: 680px;
            border-radius: 24px;
            height: 100%;
            display: flex;
            overflow: hidden;
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
                rgba(255, 107, 0, 0),
                rgba(255, 140, 66, 0.15) 30deg,
                #ff6b00 80deg,
                #ffaa6b 120deg,
                rgba(255, 140, 66, 0.15) 180deg,
                rgba(255, 107, 0, 0) 240deg,
                rgba(255, 140, 66, 0.2) 300deg,
                #ff6b00 330deg,
                rgba(255, 107, 0, 0) 360deg
            );
            -webkit-mask:
                linear-gradient(#000 0 0) content-box,
                linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            filter: blur(6px);
            opacity: 0.9;
            animation: mi-glow-spin 12s linear infinite;
        }

        .mi-card-inner {
            position: relative;
            border-radius: 20px;
            background: linear-gradient(145deg, var(--card), var(--card-bg-dark));
            padding: 2rem;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.1),
                0 20px 40px rgba(0, 0, 0, 0.4);
            z-index: 1;
            color: var(--txt-body);
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        /* Dark/Light mode adjustment for card inner */
        html[data-theme="dark"] .mi-card-inner {
            background: linear-gradient(145deg, #111827, #020617);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.1),
                0 20px 40px rgba(0, 0, 0, 0.4);
        }

        html[data-theme="light"] .mi-card-inner {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.8),
                0 20px 40px rgba(0, 0, 0, 0.1);
            color: var(--txt-body);
        }

        .mi-card-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            padding: 0.4rem 1rem;
            margin-bottom: 1rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #ff6b00, #ff8c42);
            color: white;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 4px 12px rgba(255, 107, 0, 0.3);
            width: fit-content;
        }

        .mi-card-heading {
            font-size: 1.4rem;
            margin-bottom: 0.8rem;
            font-weight: 700;
            line-height: 1.3;
            background: linear-gradient(90deg, var(--txt-body), color-mix(in srgb, var(--txt-body) 70%, transparent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Dark/Light mode adjustment for card heading */
        html[data-theme="dark"] .mi-card-heading {
            background: linear-gradient(90deg, #f9fafb, #d1d5db);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        html[data-theme="light"] .mi-card-heading {
            background: linear-gradient(90deg, #0f172a, #334155);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .mi-card-text {
            font-size: 1rem;
            line-height: 1.7;
            color: var(--muted);
            margin-bottom: 1.5rem;
        }

        .mi-card-highlights {
            background: rgba(255, 107, 0, 0.05);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 107, 0, 0.1);
        }

        /* Dark/Light mode adjustment for highlights */
        html[data-theme="dark"] .mi-card-highlights {
            background: rgba(255, 107, 0, 0.05);
            border: 1px solid rgba(255, 107, 0, 0.1);
        }

        html[data-theme="light"] .mi-card-highlights {
            background: rgba(255, 107, 0, 0.03);
            border: 1px solid rgba(255, 107, 0, 0.08);
        }

        .mi-highlight-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .mi-highlight-item:hover {
            background: rgba(255, 107, 0, 0.08);
            transform: translateX(4px);
        }

        /* Dark/Light mode adjustment for highlight hover */
        html[data-theme="dark"] .mi-highlight-item:hover {
            background: rgba(255, 107, 0, 0.08);
        }

        html[data-theme="light"] .mi-highlight-item:hover {
            background: rgba(255, 107, 0, 0.05);
        }

        .mi-highlight-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #ff6b00, #ff8c42);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.9rem;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(255, 107, 0, 0.3);
        }

        .mi-highlight-text {
            flex: 1;
        }

        .mi-highlight-text strong {
            color: #ff8c42;
            font-weight: 700;
        }

        .mi-card-cta {
            margin-top: auto;
        }

        .mi-cta-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #ff6b00, #ff8c42);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(255, 107, 0, 0.3);
        }

        .mi-cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 0, 0.4);
        }

        .mi-sources {
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: var(--muted);
            line-height: 1.6;
            padding-top: 1rem;
            border-top: 1px solid color-mix(in oklab, var(--line) 50%, transparent);
        }

        .mi-sources a {
            color: #ff8c42;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .mi-sources a:hover {
            color: #ff6b00;
            text-decoration: underline;
        }

        /* ========= POPUP CUSTOM ========= */
        .mi-leaflet-popup .leaflet-popup-content-wrapper {
            border-radius: 16px;
            background: linear-gradient(145deg, var(--card), var(--card-bg-dark));
            color: var(--txt-body);
            box-shadow:
                0 20px 40px rgba(0, 0, 0, 0.6),
                0 0 0 2px rgba(255, 107, 0, 0.4);
            border: none;
            padding: 0;
            overflow: hidden;
        }

        /* Dark/Light mode adjustment for popup */
        html[data-theme="dark"] .mi-leaflet-popup .leaflet-popup-content-wrapper {
            background: linear-gradient(145deg, #111827, #1e293b);
            color: #f9fafb;
        }

        html[data-theme="light"] .mi-leaflet-popup .leaflet-popup-content-wrapper {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            color: #0f172a;
            box-shadow:
                0 20px 40px rgba(0, 0, 0, 0.2),
                0 0 0 2px rgba(255, 107, 0, 0.3);
        }

        .mi-leaflet-popup .leaflet-popup-tip {
            background: #ff6b00;
            box-shadow: 0 0 10px rgba(255, 107, 0, 0.5);
        }

        .mi-popup-card {
            min-width: 280px;
            max-width: 400px;
            padding: 1.5rem;
        }

        .mi-popup-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(90deg, #ff6b00, #ff8c42);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .mi-popup-divider {
            height: 2px;
            background: linear-gradient(90deg, #ff6b00, transparent);
            margin-bottom: 1rem;
            border-radius: 1px;
        }

        .mi-popup-content {
            display: grid;
            gap: 0.8rem;
        }

        .mi-popup-item {
            display: grid;
            grid-template-columns: 100px 1fr;
            gap: 0.5rem;
            align-items: start;
        }

        .mi-popup-label {
            font-weight: 600;
            color: #ff8c42;
            font-size: 0.9rem;
        }

        .mi-popup-value {
            color: color-mix(in oklab, var(--txt-body) 80%, transparent);
            line-height: 1.5;
            font-size: 0.9rem;
        }

        /* Dark/Light mode adjustment for popup value */
        html[data-theme="dark"] .mi-popup-value {
            color: #d1d5db;
        }

        html[data-theme="light"] .mi-popup-value {
            color: #374151;
        }

        .mi-popup-note {
            margin-top: 1rem;
            padding: 0.75rem;
            background: rgba(255, 107, 0, 0.1);
            border-radius: 8px;
            font-size: 0.8rem;
            color: var(--muted);
            border-left: 3px solid #ff6b00;
        }

        /* Dark/Light mode adjustment for popup note */
        html[data-theme="dark"] .mi-popup-note {
            background: rgba(255, 107, 0, 0.1);
            color: #9ca3af;
        }

        html[data-theme="light"] .mi-popup-note {
            background: rgba(255, 107, 0, 0.05);
            color: #6b7280;
        }

        /* ========= LEAFLET MAP CONTROLS ========= */
        .leaflet-control-zoom {
            border: 1px solid rgba(255, 107, 0, 0.2) !important;
            background: linear-gradient(145deg, var(--card), var(--card-bg-dark)) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
            border-radius: 10px !important;
            overflow: hidden;
        }

        .leaflet-control-zoom a {
            background: color-mix(in oklab, var(--card) 90%, transparent) !important;
            color: var(--txt-body) !important;
            border-bottom: 1px solid rgba(255, 107, 0, 0.1) !important;
            transition: all 0.3s ease !important;
        }

        .leaflet-control-zoom a:hover {
            background: rgba(255, 107, 0, 0.1) !important;
            color: #ff6b00 !important;
        }

        .leaflet-control-attribution {
            background: color-mix(in oklab, var(--card) 90%, transparent) !important;
            color: var(--muted) !important;
            border: 1px solid rgba(255, 107, 0, 0.1) !important;
            border-radius: 6px !important;
            padding: 4px 8px !important;
            font-size: 0.75rem !important;
        }

        .leaflet-control-attribution a {
            color: #ff8c42 !important;
        }
    </style>

    <div class="mi-shell">
        {{-- JUDUL KONSISTEN NEON ORANGE --}}
        <div class="mi-title-section">
            <h2 class="mi-title mx-auto tracking-tight">
                Peta Interaktif Nusantara
            </h2>
            <div class="mi-title-decoration"></div>
            <p class="mi-subtitle">
                Jelajahi keindahan dan keragaman Indonesia melalui peta interaktif.
                Klik setiap wilayah untuk menemukan kekayaan budaya, destinasi wisata,
                dan kuliner khas Nusantara.
            </p>
        </div>

        <div class="mi-container">
            {{-- KOLOM KIRI: PETA INTERAKTIF --}}
            <div class="mi-map-wrapper">
                <div class="mi-map-glow"></div>
                <div class="mi-map-inner">
                    <div id="mi-map-indonesia"></div>
                </div>
            </div>

            {{-- KOLOM KANAN: INFORMASI --}}
            <div class="mi-info-wrapper">
                <div class="mi-card">
                    <div class="mi-card-glow"></div>
                    <div class="mi-card-inner">
                        <div class="mi-card-badge">Keunikan Indonesia</div>
                        <h3 class="mi-card-heading">
                            Negeri Seribu Pulau dengan Segudang Keajaiban
                        </h3>
                        <p class="mi-card-text">
                            Indonesia bukan hanya sekadar negara kepulauan, melainkan
                            mozaik peradaban yang hidup. Setiap pulau menyimpan kisahnya
                            sendiri, dari ritual kuno hingga seni kontemporer.
                        </p>

                        <div class="mi-card-highlights">
                            <div class="mi-highlight-item">
                                <div class="mi-highlight-icon">🌏</div>
                                <div class="mi-highlight-text">
                                    <strong>17.380 pulau</strong> dengan nama dan koordinat resmi (Data 2024)
                                </div>
                            </div>
                            <div class="mi-highlight-item">
                                <div class="mi-highlight-icon">🗣️</div>
                                <div class="mi-highlight-text">
                                    <strong>718 bahasa daerah</strong> yang masih hidup dan aktif digunakan
                                </div>
                            </div>
                            <div class="mi-highlight-item">
                                <div class="mi-highlight-icon">👥</div>
                                <div class="mi-highlight-text">
                                    <strong>1.340 suku bangsa</strong> dengan tradisi dan adat istiadat unik
                                </div>
                            </div>
                        </div>

                        <div class="mi-card-cta">
                            <h4 class="mi-card-heading" style="font-size: 1.2rem; margin-bottom: 1rem;">
                                Cara Menjelajahi Nusantara
                            </h4>
                            <p class="mi-card-text" style="margin-bottom: 1.5rem;">
                                1. Klik wilayah berwarna di peta untuk melihat ringkasan<br>
                                2. Jelajahi halaman pulau untuk detail lengkap<br>
                                3. Temukan destinasi dan kuliner yang wajib dicoba
                            </p>

                            <button class="mi-cta-button" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
                                <span>Mulai Eksplorasi</span>
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M8 1v14M1 8h14"/>
                                </svg>
                            </button>
                        </div>

                        <div class="mi-sources">
                            Sumber data terpercaya:
                            <a target="_blank" rel="noopener" href="https://sipulau.big.go.id/news/11">Badan Informasi Geospasial</a> •
                            <a target="_blank" rel="noopener" href="https://petabahasa.kemdikbud.go.id/">Peta Bahasa Kemendikbud</a> •
                            <a target="_blank" rel="noopener" href="https://indonesiabaik.id/infografis/sebaran-jumlah-suku-di-indonesia">IndonesiaBaik</a>
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
                scrollWheelZoom: true,
                zoomSnap: 0.5,
                zoomDelta: 0.5,
                fadeAnimation: true,
                zoomAnimation: true,
                preferCanvas: true
            }).setView([-2.5, 118], 4.5);

            // Custom attribution
            map.attributionControl.setPrefix(
                '<a href="/" target="_blank" rel="noopener">Lentara Nusantara</a> | ' +
                '© <a href="https://leafletjs.com" target="_blank" rel="noopener">Leaflet</a>'
            );

            // ====== BASEMAP ======
            const maptilerKey = @json(config('services.maptiler.key'));

            // Peta yang lebih menarik dengan kontras tinggi
            const osmTiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap',
                detectRetina: true,
                className: 'leaflet-tile'
            });

            // Peta untuk light mode
            const lightTiles = maptilerKey
                ? L.tileLayer(`https://api.maptiler.com/maps/voyager/{z}/{x}/{y}.png?key=${maptilerKey}`, {
                    maxZoom: 20,
                    tileSize: 512,
                    zoomOffset: -1,
                    detectRetina: true,
                    attribution:
                        '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> ' +
                        '© <a href="https://www.maptiler.com/copyright/">MapTiler</a>',
                    className: 'leaflet-tile leaflet-tile-light'
                })
                : osmTiles;

            // Peta untuk dark mode
            const darkTiles = maptilerKey
                ? L.tileLayer(`https://api.maptiler.com/maps/dataviz-dark/{z}/{x}/{y}.png?key=${maptilerKey}`, {
                    maxZoom: 20,
                    tileSize: 512,
                    zoomOffset: -1,
                    detectRetina: true,
                    attribution:
                        '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> ' +
                        '© <a href="https://www.maptiler.com/copyright/">MapTiler</a>',
                    className: 'leaflet-tile leaflet-tile-dark'
                })
                : osmTiles;

            let baseLayer = null;

            function setBaseMapTheme(isDark) {
                if (baseLayer) {
                    map.removeLayer(baseLayer);
                }
                baseLayer = isDark ? darkTiles : lightTiles;
                baseLayer.addTo(map);

                // Update popup theme
                updatePopupTheme(isDark);
            }

            // ====== DETEKSI TEMA DARI SISTEM ANDA ======
            function detectIsDark() {
                const html = document.documentElement;

                // Cek atribut data-theme dari sistem Anda
                if (html.getAttribute('data-theme') === 'dark') {
                    return true;
                }

                // Fallback untuk class dark
                if (html.classList.contains('dark')) {
                    return true;
                }

                // Cek prefers-color-scheme
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    return true;
                }

                return false;
            }

            function syncThemeFromDOM() {
                const isDark = detectIsDark();
                setBaseMapTheme(isDark);

                // Update Leaflet controls styling
                updateLeafletControls(isDark);

                setTimeout(() => {
                    map.invalidateSize(true);
                    if (geojsonLayer) {
                        geojsonLayer.setStyle(styleWilayah);
                    }
                }, 160);
            }

            // Inisialisasi tema awal
            syncThemeFromDOM();

            // ====== OBSERVER UNTUK PERUBAHAN TEMA ======
            function observeThemeChanges() {
                // Observer untuk atribut data-theme
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.attributeName === 'data-theme') {
                            setTimeout(syncThemeFromDOM, 50);
                        }
                    });
                });

                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['data-theme', 'class']
                });

                // Juga observe perubahan di localStorage (jika ada)
                window.addEventListener('storage', function(e) {
                    if (e.key === 'theme') {
                        setTimeout(syncThemeFromDOM, 50);
                    }
                });

                // Observe prefers-color-scheme changes
                if (window.matchMedia) {
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', syncThemeFromDOM);
                }
            }

            observeThemeChanges();

            // ====== UPDATE STYLING LEAFLET CONTROLS ======
            function updateLeafletControls(isDark) {
                // Update zoom control
                const zoomControl = document.querySelector('.leaflet-control-zoom');
                if (zoomControl) {
                    zoomControl.style.background = `linear-gradient(145deg, var(--card), var(--card-bg-dark))`;
                    zoomControl.style.border = `1px solid rgba(255, 107, 0, ${isDark ? '0.3' : '0.2'})`;
                }

                // Update attribution
                const attribution = document.querySelector('.leaflet-control-attribution');
                if (attribution) {
                    attribution.style.background = `color-mix(in oklab, var(--card) 90%, transparent)`;
                    attribution.style.color = `var(--muted)`;
                    attribution.style.border = `1px solid rgba(255, 107, 0, ${isDark ? '0.15' : '0.1'})`;
                }
            }

            // ====== UPDATE POPUP THEME ======
            function updatePopupTheme(isDark) {
                const popups = document.querySelectorAll('.mi-leaflet-popup');
                popups.forEach(popup => {
                    const wrapper = popup.querySelector('.leaflet-popup-content-wrapper');
                    if (wrapper) {
                        if (isDark) {
                            wrapper.style.background = 'linear-gradient(145deg, #111827, #1e293b)';
                            wrapper.style.color = '#f9fafb';
                        } else {
                            wrapper.style.background = 'linear-gradient(145deg, #ffffff, #f8fafc)';
                            wrapper.style.color = '#0f172a';
                        }
                    }
                });
            }

            // ====== LAYER PULAU DENGAN WARNA GRADIEN ======
            let geojsonLayer = null;

            // Palette warna menarik untuk pulau
            const islandColors = [
                '#FF6B00', '#FF8C42', '#FFAA6B', '#36B37E', '#00B8D9',
                '#6554C0', '#FF5630', '#FFAB00', '#00A3BF', '#8E44AD'
            ];

            function getRandomColor() {
                return islandColors[Math.floor(Math.random() * islandColors.length)];
            }

            function styleWilayah(feature) {
                const p = feature.properties || {};
                const warna = p.warna || getRandomColor();
                const isDark = detectIsDark();

                return {
                    color: isDark ? '#ffffff' : '#1f2937',
                    weight: isDark ? 1.2 : 1.5,
                    fillColor: warna,
                    fillOpacity: isDark ? 0.8 : 0.7,
                    dashArray: '3',
                    className: 'island-polygon'
                };
            }

            function highlightFeature(e) {
                const layer = e.target;
                const isDark = detectIsDark();

                layer.setStyle({
                    weight: 3,
                    fillOpacity: 0.95,
                    dashArray: '',
                    color: '#ffffff',
                    fillColor: layer.options.fillColor
                });

                layer.bringToFront();

                // Tambah efek glow
                const popup = L.popup({
                    closeButton: false,
                    className: 'mi-glow-popup',
                    offset: [0, -10]
                })
                .setLatLng(e.latlng)
                .setContent(`<div style="text-align:center; color:#ff6b00; font-weight:bold;">${layer.feature.properties?.nama || 'Pulau'}</div>`)
                .openOn(map);

                setTimeout(() => map.closePopup(popup), 800);
            }

            function resetHighlight(e) {
                if (geojsonLayer) {
                    geojsonLayer.resetStyle(e.target);
                }
            }

            function safeText(v) {
                if (v === null || v === undefined) return '-';
                if (Array.isArray(v)) return v.filter(Boolean).join(', ') || '-';
                return String(v).trim() || '-';
            }

            function onEachWilayah(feature, layer) {
                layer.on({
                    mouseover: highlightFeature,
                    mouseout: resetHighlight,
                    click: function (e) {
                        const p = feature.properties || {};

                        const nama      = safeText(p.nama || p.name || 'Wilayah Indonesia');
                        const ringkas   = safeText(p.ringkas || p.deskripsi || p.makna || 'Deskripsi belum tersedia.');
                        const suku      = safeText(p.suku_utama || p.suku || 'Beragam suku bangsa');
                        const destinasi = safeText(p.destinasi_ikonik || p.destinasi || 'Tempat wisata unggulan');
                        const kuliner   = safeText(p.kuliner_khas || p.kuliner || 'Makanan khas daerah');
                        const wilayah   = safeText(p.digunakan_di || p.wilayah || p.provinsi || 'Seluruh Indonesia');

                        const isDark = detectIsDark();
                        const popupClass = isDark ? 'mi-leaflet-popup mi-leaflet-popup-dark' : 'mi-leaflet-popup mi-leaflet-popup-light';

                        const content =
                            `<div class="mi-popup-card">
                                <div class="mi-popup-title">${nama}</div>
                                <div class="mi-popup-divider"></div>

                                <div class="mi-popup-content">
                                    <div class="mi-popup-item">
                                        <div class="mi-popup-label">Ringkasan</div>
                                        <div class="mi-popup-value">${ringkas}</div>
                                    </div>
                                    <div class="mi-popup-item">
                                        <div class="mi-popup-label">Suku Utama</div>
                                        <div class="mi-popup-value">${suku}</div>
                                    </div>
                                    <div class="mi-popup-item">
                                        <div class="mi-popup-label">Destinasi</div>
                                        <div class="mi-popup-value">${destinasi}</div>
                                    </div>
                                    <div class="mi-popup-item">
                                        <div class="mi-popup-label">Kuliner</div>
                                        <div class="mi-popup-value">${kuliner}</div>
                                    </div>
                                    <div class="mi-popup-item">
                                        <div class="mi-popup-label">Wilayah</div>
                                        <div class="mi-popup-value">${wilayah}</div>
                                    </div>
                                </div>

                                <div class="mi-popup-note">
                                    Klik wilayah lain untuk menjelajahi kekayaan Nusantara lainnya
                                </div>
                            </div>`;

                        L.popup({
                            closeButton: true,
                            className: popupClass,
                            maxWidth: 400,
                            minWidth: 300,
                            autoClose: false,
                            closeOnClick: false
                        })
                        .setLatLng(e.latlng)
                        .setContent(content)
                        .openOn(map);
                    }
                });
            }

            // ====== LOAD GEOJSON ======
            fetch(geojsonUrl)
                .then(response => response.json())
                .then(data => {
                    geojsonLayer = L.geoJSON(data, {
                        style: styleWilayah,
                        onEachFeature: onEachWilayah
                    }).addTo(map);

                    // Animasi zoom ke seluruh Indonesia
                    try {
                        const bounds = geojsonLayer.getBounds();
                        if (bounds.isValid()) {
                            map.fitBounds(bounds, {
                                padding: [40, 40],
                                maxZoom: 5,
                                animate: true,
                                duration: 1.5
                            });
                        }
                    } catch (err) {
                        console.warn('Tidak bisa fitBounds:', err);
                    }

                    setTimeout(() => map.invalidateSize(true), 200);

                    // Tambah kontrol zoom yang lebih baik
                    L.control.zoom({
                        position: 'topright',
                        zoomInTitle: 'Perbesar',
                        zoomOutTitle: 'Perkecil'
                    }).addTo(map);

                    // Update controls styling
                    updateLeafletControls(detectIsDark());

                })
                .catch(err => {
                    console.error('Gagal memuat GeoJSON:', err);
                    // Fallback: tampilkan Indonesia dengan marker utama
                    L.marker([-6.2088, 106.8456])
                        .addTo(map)
                        .bindPopup('<b>Jakarta</b><br>Ibu kota Indonesia')
                        .openPopup();

                    setTimeout(() => map.invalidateSize(true), 200);
                    updateLeafletControls(detectIsDark());
                });

            // ====== EFEK TAMBAHAN UNTUK MAP ======
            // Tambah overlay gradient
            const gradientOverlay = L.rectangle(map.getBounds(), {
                color: 'transparent',
                fillColor: 'transparent',
                className: 'map-gradient-overlay'
            }).addTo(map);

            // Update overlay saat zoom/pan
            map.on('moveend', function() {
                gradientOverlay.setBounds(map.getBounds());
            });

            // ====== HANDLE RESIZE DAN ORIENTATION CHANGE ======
            window.addEventListener('resize', function() {
                setTimeout(() => map.invalidateSize(true), 100);
            });

            window.addEventListener('orientationchange', function() {
                setTimeout(() => map.invalidateSize(true), 300);
            });

        })();
    </script>
</section>
