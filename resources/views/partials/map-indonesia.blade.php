{{-- resources/views/partials/map-indonesia.blade.php --}}
<section
    id="map-indonesia-section"
    class="mi-section py-12 px-4 sm:px-6 flex justify-center bg-[var(--bg-body)] text-[var(--txt-body)]">

    {{-- Leaflet CSS --}}
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          crossorigin="anonymous"/>

{{-- ===========================
FULL CSS (REPLACE SEMUA <style> KAMU DENGAN INI)
=========================== --}}
<style>
    /* ========= WRAPPER + LAYOUT (MAP ATAS, DETAIL BAWAH) ========= */
    #map-indonesia-section.mi-section {
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .mi-shell {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* UBAH JADI VERTICAL: MAP DI ATAS, CARD DI BAWAH */
    .mi-container {
        width: 100%;
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    /* ========= JUDUL ========= */
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
        background: linear-gradient(90deg,#ff6b00 0%,#ff8c42 25%,#ffaa6b 50%,#ff8c42 75%,#ff6b00 100%);
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

    .mi-title-decoration::before { left: 0; }
    .mi-title-decoration::after { right: 0; }

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
            text-shadow: 0 0 20px rgba(255, 107, 0, 0.3), 0 0 40px rgba(255, 140, 66, 0.2);
        }
        50% {
            background-position: 100% 50%;
            text-shadow: 0 0 30px rgba(255, 107, 0, 0.5), 0 0 60px rgba(255, 140, 66, 0.3);
        }
    }

    /* ========= MAP CONTAINER ========= */
    .mi-map-wrapper {
        position: relative;
        border-radius: 26px;
        padding: 8px;
        background: transparent;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
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
        height: 600px;
        width: 100%;
        z-index: 1;
    }

    html[data-theme="dark"] .mi-map-inner {
        background: linear-gradient(145deg, #0a0a0a, #1a1a1a);
    }

    html[data-theme="light"] .mi-map-inner {
        background: linear-gradient(145deg, #f8fafc, #e2e8f0);
    }

    #mi-map-indonesia {
        width: 100%;
        height: 100%;
    }

    @media (max-width: 900px) {
        .mi-map-inner {
            height: 450px;
        }
    }

    @media (max-width: 640px) {
        .mi-map-inner {
            height: 400px;
        }
    }

    @keyframes mi-glow-spin {
        to {
            --mi-border-angle: 360deg;
        }
    }

    /* ========= CARD CONTAINER (BAWAH) ========= */
    .mi-card-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
    }

    @media (max-width: 900px) {
        .mi-card-container {
            grid-template-columns: 1fr;
        }
    }

    /* ========= KARTU INFO UTAMA ========= */
    .mi-card {
        position: relative;
        border-radius: 24px;
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
        padding: 2rem;
        z-index: 1;
        color: var(--txt-body);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

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
    }

    html[data-theme="dark"] .mi-card-heading {
        color: #f9fafb;
    }

    html[data-theme="light"] .mi-card-heading {
        color: #0f172a;
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

    /* ========= KARTU FITUR ========= */
    .mi-features-title {
        font-size: 1.4rem;
        margin-bottom: 1.5rem;
        font-weight: 700;
        line-height: 1.3;
        background: linear-gradient(90deg, #ff6b00, #ff8c42);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .mi-features-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 640px) {
        .mi-features-grid {
            grid-template-columns: 1fr;
        }
    }

    .mi-feature-item {
        background: rgba(255, 107, 0, 0.05);
        border: 1px solid rgba(255, 107, 0, 0.1);
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        transition: all 0.3s ease;
    }

    .mi-feature-item:hover {
        background: rgba(255, 107, 0, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 107, 0, 0.15);
    }

    .mi-feature-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #ff6b00, #ff8c42);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.8rem;
        color: white;
        font-size: 1.2rem;
    }

    .mi-feature-label {
        font-weight: 600;
        color: var(--txt-body);
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }

    .mi-feature-desc {
        font-size: 0.8rem;
        color: var(--muted);
        line-height: 1.4;
    }

    .mi-watermark {
        margin-top: auto;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid color-mix(in oklab, var(--line) 50%, transparent);
    }

    .mi-watermark img {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        object-fit: contain;
    }

    .mi-watermark-text {
        font-size: 0.85rem;
        color: var(--muted);
    }

    /* ========= WATERMARK DI MAP (TANPA BACKGROUND, ICON + TEXT LEBIH BESAR) ========= */
    .mi-map-watermark{
        position: absolute;
        bottom: 15px;
        left: 15px;
        z-index: 1000;

        display: inline-flex;
        align-items: center;
        gap: 10px;

        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        backdrop-filter: none !important;

        padding: 0 !important;
        border-radius: 0 !important;
    }

    .mi-map-watermark img{
        width: 34px;
        height: 34px;
        border-radius: 10px;
        object-fit: contain;

        filter: none !important;
        drop-shadow: 0 6px 14px rgba(0,0,0,.35);
    }

    .mi-map-watermark span{
        font-size: 1rem;
        font-weight: 800;

        color: #ffffff;
        text-shadow: 0 2px 12px rgba(0,0,0,.6);
    }

    html[data-theme="light"] .mi-map-watermark span{
        color: #0f172a;
        text-shadow: 0 2px 10px rgba(255,255,255,.75);
    }

    /* ========= POPUP STYLING ========= */
    .leaflet-popup-content-wrapper {
        border-radius: 16px !important;
        padding: 0 !important;
        overflow: hidden !important;
        border: none !important;
        max-width: 95vw !important;
    }

    html[data-theme="dark"] .leaflet-popup-content-wrapper {
        background: linear-gradient(145deg, #111827, #1e293b) !important;
        color: #f9fafb !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.65), 0 0 0 2px rgba(255, 107, 0, 0.35) !important;
    }

    html[data-theme="light"] .leaflet-popup-content-wrapper {
        background: linear-gradient(145deg, #ffffff, #f8fafc) !important;
        color: #0f172a !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18), 0 0 0 2px rgba(255, 107, 0, 0.22) !important;
    }

    .leaflet-popup-tip {
        background: #ff6b00 !important;
        box-shadow: 0 0 10px rgba(255, 107, 0, 0.45) !important;
    }

    .leaflet-popup-close-button {
        width: 32px !important;
        height: 32px !important;
        font-size: 20px !important;
        line-height: 32px !important;
        color: #ff8c42 !important;
        transition: all 0.2s ease !important;
        background: rgba(0, 0, 0, 0.1) !important;
        border-radius: 50% !important;
        margin: 8px !important;
        z-index: 1001 !important;
    }

    .leaflet-popup-close-button:hover {
        color: #ff6b00 !important;
        background: rgba(255, 107, 0, 0.2) !important;
        border-radius: 50% !important;
    }

    .mi-popup-card {
        min-width: 280px;
        max-width: 400px;
        padding: 1.2rem;
    }

    @media (max-width: 640px) {
        .mi-popup-card {
            min-width: 240px !important;
            max-width: 300px !important;
            padding: 1rem !important;
        }

        .leaflet-popup {
            max-width: 320px !important;
        }

        .leaflet-popup-content-wrapper {
            max-width: 85vw !important;
            margin: 0 auto !important;
        }
    }

    .mi-popup-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .mi-popup-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #ff6b00, #ff8c42);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(255, 107, 0, 0.4);
        position: relative;
        overflow: hidden;
    }

    .mi-popup-icon::before {
        content: '';
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        background: radial-gradient(circle, rgba(255,255,255,0.3) 1%, transparent 10%);
        animation: mi-icon-glow 2s infinite alternate;
    }

    @keyframes mi-icon-glow {
        0% { opacity: 0.3; transform: scale(1); }
        100% { opacity: 0.6; transform: scale(1.1); }
    }

    .mi-popup-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0;
        background: linear-gradient(90deg, #ff6b00, #ff8c42);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 2px 10px rgba(255, 107, 0, 0.3);
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
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .mi-popup-label {
        font-weight: 600;
        color: #ff8c42;
        font-size: 0.85rem;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .mi-popup-value {
        color: var(--txt-body);
        line-height: 1.5;
        font-size: 0.9rem;
        padding-left: 0.5rem;
        border-left: 2px solid rgba(255, 107, 0, 0.3);
    }

    .mi-popup-note {
        margin-top: 1rem;
        padding: 0.75rem;
        background: rgba(255, 107, 0, 0.1);
        border-radius: 8px;
        font-size: 0.8rem;
        color: var(--muted);
        border-left: 3px solid #ff6b00;
        animation: mi-note-pulse 3s infinite;
    }

    @keyframes mi-note-pulse {
        0%, 100% { border-left-color: #ff6b00; }
        50% { border-left-color: #ff8c42; }
    }

    /* ========= TOOLTIP HOVER (NAMA PULAU) - STYLING KAYA POPUP ========= */
    .leaflet-tooltip.mi-tooltip-card{
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        pointer-events: none !important;
        opacity: 1 !important;
    }

    .leaflet-tooltip.mi-tooltip-card::before{
        display: none !important;
    }

    .mi-hover-card{
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 14px;
        white-space: nowrap;
        animation: mi-hover-glow 2s infinite alternate;
        position: relative;
        overflow: hidden;
    }

    .mi-hover-card::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: conic-gradient(from var(--mi-border-angle),
            #ff6b00, #ff8c42, #ffaa6b, #ff8c42, #ff6b00);
        border-radius: 16px;
        z-index: -1;
        animation: mi-glow-spin 3s linear infinite;
        filter: blur(4px);
        opacity: 0.7;
    }

    .mi-hover-card::after {
        content: '';
        position: absolute;
        inset: 0;
        background: inherit;
        border-radius: 14px;
        z-index: -1;
    }

    html[data-theme="dark"] .mi-hover-card{
        background: linear-gradient(145deg, #111827, #1e293b);
        color: #f9fafb;
    }

    html[data-theme="light"] .mi-hover-card{
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        color: #0f172a;
    }

    .mi-hover-icon{
        width: 28px;
        height: 28px;
        border-radius: 10px;
        background: linear-gradient(135deg, #ff6b00, #ff8c42);
        box-shadow: 0 6px 14px rgba(255, 107, 0, 0.35);
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
        position: relative;
        overflow: hidden;
    }

    .mi-hover-icon::before {
        content: '📍';
        position: absolute;
    }

    @keyframes mi-hover-glow {
        0% { box-shadow: 0 8px 25px rgba(255, 107, 0, 0.3), 0 0 0 1px rgba(255, 107, 0, 0.2); }
        100% { box-shadow: 0 12px 35px rgba(255, 107, 0, 0.5), 0 0 0 2px rgba(255, 107, 0, 0.4); }
    }

    .mi-hover-title{
        font-weight: 800;
        font-size: 0.95rem;
        line-height: 1.1;
        background: linear-gradient(90deg, #ff6b00, #ff8c42);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 2px 8px rgba(255, 107, 0, 0.2);
    }

    /* ========= LEAFLET CONTROLS ========= */
    .leaflet-control-zoom {
        border: 1px solid rgba(255, 107, 0, 0.2) !important;
        background: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(10px) !important;
        border-radius: 10px !important;
        overflow: hidden !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2) !important;
    }

    .leaflet-control-zoom a {
        background: rgba(255, 255, 255, 0.05) !important;
        color: var(--txt-body) !important;
        border-bottom: 1px solid rgba(255, 107, 0, 0.1) !important;
        transition: all 0.3s ease !important;
    }

    .leaflet-control-zoom a:hover {
        background: rgba(255, 107, 0, 0.15) !important;
        color: #ff6b00 !important;
    }

    .leaflet-control-attribution {
        background: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(10px) !important;
        color: var(--muted) !important;
        border: 1px solid rgba(255, 107, 0, 0.1) !important;
        border-radius: 8px !important;
        padding: 4px 8px !important;
        font-size: 0.75rem !important;
    }

    .leaflet-control-attribution a {
        color: #ff8c42 !important;
    }

    /* ========= MOBILE OPTIMIZATION ========= */
    @media (max-width: 640px) {
        .leaflet-control-zoom {
            transform: scale(0.9);
            transform-origin: top right;
        }

        .leaflet-control-layers {
            transform: scale(0.85);
            transform-origin: top left;
        }

        .mi-hover-card {
            padding: 8px 12px;
            font-size: 0.85rem;
        }

        .mi-hover-icon {
            width: 24px;
            height: 24px;
            font-size: 0.8rem;
        }

        .leaflet-popup-close-button {
            width: 28px !important;
            height: 28px !important;
            font-size: 18px !important;
            line-height: 28px !important;
        }
    }

    /* ========= PULAU HOVER EFFECT ========= */
    .mi-island-hover {
        animation: mi-island-pulse 1.5s infinite alternate;
        filter: brightness(1.2) saturate(1.3);
    }

    @keyframes mi-island-pulse {
        0% { filter: brightness(1.1) saturate(1.2); }
        100% { filter: brightness(1.3) saturate(1.4); }
    }
</style>


    <div class="mi-shell">
        <div class="mi-title-section">
            <h2 class="mi-title mx-auto tracking-tight">Peta Interaktif Nusantara</h2>
            <div class="mi-title-decoration"></div>
            <p class="mi-subtitle">
                Jelajahi keindahan dan keragaman Indonesia melalui peta interaktif.
                Klik setiap wilayah untuk menemukan kekayaan budaya, destinasi wisata,
                dan kuliner khas Nusantara.
            </p>
        </div>

        <div class="mi-container">
            {{-- MAP (ATAS) --}}
            <div class="mi-map-wrapper">
                <div class="mi-map-glow"></div>
                <div class="mi-map-inner">
                    <div id="mi-map-indonesia"></div>
                </div>
            </div>

            {{-- CARDS (BAWAH) --}}
            <div class="mi-card-container">


                                {{-- KARTU FITUR --}}
                <div class="mi-card">
                    <div class="mi-card-glow"></div>
                    <div class="mi-card-inner">
                        <h3 class="mi-features-title">Fitur Peta Interaktif</h3>

                        <div class="mi-features-grid">
                            <div class="mi-feature-item">
                                <div class="mi-feature-icon">🔍</div>
                                <div class="mi-feature-label">Zoom In/Out</div>
                                <div class="mi-feature-desc">Gunakan roda mouse atau tombol +/- untuk memperbesar/memperkecil</div>
                            </div>

                            <div class="mi-feature-item">
                                <div class="mi-feature-icon">🗺️</div>
                                <div class="mi-feature-label">Ganti Lapisan</div>
                                <div class="mi-feature-desc">Pilih tampilan peta satelit, gelap, atau terang sesuai preferensi</div>
                            </div>

                            <div class="mi-feature-item">
                                <div class="mi-feature-icon">📍</div>
                                <div class="mi-feature-label">Klik Pulau</div>
                                <div class="mi-feature-desc">Dapatkan informasi detail tentang pulau yang diklik dari GeoJSON</div>
                            </div>

                            <div class="mi-feature-item">
                                <div class="mi-feature-icon">🔄</div>
                                <div class="mi-feature-label">Toggle Layer</div>
                                <div class="mi-feature-desc">Aktif/nonaktifkan layer pulau untuk tampilan bersih</div>
                            </div>

                            <div class="mi-feature-item">
                                <div class="mi-feature-icon">📱</div>
                                <div class="mi-feature-label">Responsif</div>
                                <div class="mi-feature-desc">Tampilan optimal di desktop, tablet, dan smartphone</div>
                            </div>

                            <div class="mi-feature-item">
                                <div class="mi-feature-icon">🌓</div>
                                <div class="mi-feature-label">Mode Gelap/Terang</div>
                                <div class="mi-feature-desc">Otomatis menyesuaikan tema website Anda</div>
                            </div>
                        </div>

                        <div class="mi-watermark">
                            <img src="{{ asset('images/icon/icon_lentara.png') }}" alt="Lentara Nusantara Icon">
                            <div class="mi-watermark-text">
                                <strong>Lentara Nusantara</strong> • Peta Interaktif Indonesia
                            </div>
                        </div>

                        <div class="mi-sources">
                            Teknologi: <a href="https://leafletjs.com" target="_blank" rel="noopener">Leaflet</a> •
                            <a href="https://openstreetmap.org" target="_blank" rel="noopener">OpenStreetMap</a>
                        </div>
                    </div>
                </div>

                {{-- KARTU INFO UTAMA --}}
                <div class="mi-card">
                    <div class="mi-card-glow"></div>
                    <div class="mi-card-inner">
                        <div class="mi-card-badge">Keunikan Indonesia</div>
                        <h3 class="mi-card-heading">Negeri Seribu Pulau dengan Segudang Keajaiban</h3>
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
                            <h4 class="mi-card-heading" style="font-size: 1.2rem; margin-bottom: 1rem;">Cara Menjelajahi Nusantara</h4>
                            <p class="mi-card-text" style="margin-bottom: 1.5rem;">
                                1. Klik wilayah berwarna di peta untuk melihat ringkasan<br>
                                2. Jelajahi halaman pulau untuk detail lengkap<br>
                                3. Temukan destinasi dan kuliner yang wajib dicoba
                            </p>

                            <button class="mi-cta-button" onclick="document.getElementById('map-indonesia-section')?.scrollIntoView({behavior:'smooth'})">
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
            crossorigin="anonymous"></script>

<!-- ===========================
FULL JS (REPLACE SEMUA <script> MAP KAMU DENGAN INI)
=========================== -->
<script>
(function () {
    const geojsonUrl = "{{ asset('data/map-indonesia.geojson') }}";
    const maptilerKey = @json(config('services.maptiler.key'));

    if (typeof L === 'undefined') {
        console.error('[MAP] Leaflet tidak ter-load. Cek apakah leaflet.js berhasil dimuat.');
        return;
    }

    // ===== THEME DETECT =====
    function isDarkMode() {
        const html = document.documentElement;
        if (html.getAttribute('data-theme') === 'dark') return true;
        if (html.classList.contains('dark')) return true;
        return !!(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
    }

    // ===== DETECT MOBILE =====
    function isMobileDevice() {
        return window.innerWidth <= 768;
    }

    // ===== MAP INIT =====
    const map = L.map('mi-map-indonesia', {
        zoomControl: false,
        scrollWheelZoom: true,
        zoomSnap: 0.5,
        zoomDelta: 0.5,
        fadeAnimation: true,
        zoomAnimation: true,
        preferCanvas: false,
        renderer: L.svg({ padding: 0.5 }),
        touchZoom: true,
        dragging: true,
        tap: !L.Browser.mobile, // Prevent double-tap zoom on mobile
        tapTolerance: 15
    });

    const INITIAL_VIEW = { center: [-2.5, 118], zoom: 4.5 };
    map.setView(INITIAL_VIEW.center, INITIAL_VIEW.zoom);

    // ===== Controls =====
    L.control.zoom({
        position: 'topright',
        zoomInTitle: 'Perbesar',
        zoomOutTitle: 'Perkecil'
    }).addTo(map);

    // Add watermark to map (tanpa background, icon+text besar via CSS)
    const watermark = L.control({position: 'bottomleft'});
    watermark.onAdd = function() {
        const div = L.DomUtil.create('div', 'mi-map-watermark');
        div.innerHTML = `
            <img src="{{ asset('images/icon/icon_lentara.png') }}" alt="Lentara">
            <span>Lentara Nusantara</span>
        `;
        return div;
    };
    watermark.addTo(map);

    map.attributionControl.setPrefix(
        '<a href="/" target="_blank" rel="noopener">Lentara Nusantara</a> | ' +
        '© <a href="https://leafletjs.com" target="_blank" rel="noopener">Leaflet</a>'
    );

    // ===== BASEMAPS =====
    const osmTiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        detectRetina: true,
        attribution: '© OpenStreetMap'
    });

    const maptilerSatellite = maptilerKey
        ? L.tileLayer(`https://api.maptiler.com/maps/satellite/{z}/{x}/{y}.jpg?key=${maptilerKey}`, {
            maxZoom: 20,
            tileSize: 512,
            zoomOffset: -1,
            detectRetina: true,
            attribution:
                '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> ' +
                '© <a href="https://www.maptiler.com/copyright/">MapTiler</a>',
          })
        : null;

    const maptilerLight = maptilerKey
        ? L.tileLayer(`https://api.maptiler.com/maps/voyager/{z}/{x}/{y}.png?key=${maptilerKey}`, {
            maxZoom: 20,
            tileSize: 512,
            zoomOffset: -1,
            detectRetina: true,
            attribution:
                '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> ' +
                '© <a href="https://www.maptiler.com/copyright/">MapTiler</a>',
          })
        : null;

    const maptilerDark = maptilerKey
        ? L.tileLayer(`https://api.maptiler.com/maps/dataviz-dark/{z}/{x}/{y}.png?key=${maptilerKey}`, {
            maxZoom: 20,
            tileSize: 512,
            zoomOffset: -1,
            detectRetina: true,
            attribution:
                '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> ' +
                '© <a href="https://www.maptiler.com/copyright/">MapTiler</a>',
          })
        : null;

    // Default basemap: Satellite kalau ada key, kalau tidak → OSM
    const defaultBase = (maptilerSatellite || osmTiles);
    defaultBase.addTo(map);

    // ===== GEOJSON LAYER =====
    let geojsonLayer = null;

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
        const dark = isDarkMode();

        return {
            color: dark ? '#ffffff' : '#1f2937',
            weight: dark ? 1.2 : 1.5,
            fillColor: warna,
            fillOpacity: dark ? 0.86 : 0.78,
            dashArray: '3',
            lineJoin: 'round',
            lineCap: 'round'
        };
    }

    function safeText(v) {
        if (v === null || v === undefined) return '-';
        if (Array.isArray(v)) return v.filter(Boolean).join(', ') || '-';
        return String(v).trim() || '-';
    }

    // Popup dengan data lengkap dari GeoJSON
    function openRegionPopup(latlng, props) {
        // Data dari GeoJSON - hanya data yang ada di struktur Anda
        const nama = safeText(props.name || props.nama || 'Wilayah Indonesia');
        const aksara = safeText(props.aksara || 'Tidak tersedia');
        const kataKhas = safeText(props.kata_khas || 'Tidak tersedia');
        const makna = safeText(props.makna || 'Tidak tersedia');
        const digunakanDi = safeText(props.digunakan_di || 'Tidak tersedia');
        const warna = props.warna || '#FF6B00';

        const content = `
            <div class="mi-popup-card">
                <div class="mi-popup-header">
                    <div class="mi-popup-icon" style="background: linear-gradient(135deg, ${warna}, ${warna}99);">📍</div>
                    <div class="mi-popup-title">${nama}</div>
                </div>

                <div class="mi-popup-divider"></div>

                <div class="mi-popup-content">
                    <div class="mi-popup-item">
                        <div class="mi-popup-label">Aksara</div>
                        <div class="mi-popup-value">${aksara}</div>
                    </div>

                    <div class="mi-popup-item">
                        <div class="mi-popup-label">Kata Khas</div>
                        <div class="mi-popup-value">${kataKhas}</div>
                    </div>

                    <div class="mi-popup-item">
                        <div class="mi-popup-label">Makna</div>
                        <div class="mi-popup-value">${makna}</div>
                    </div>

                    <div class="mi-popup-item">
                        <div class="mi-popup-label">Digunakan di</div>
                        <div class="mi-popup-value">${digunakanDi}</div>
                    </div>
                </div>

                <div class="mi-popup-note">
                    Klik wilayah lain untuk menjelajahi kekayaan Nusantara lainnya
                </div>
            </div>`;

        // Mobile optimization
        const popupOptions = {
            closeButton: true,
            maxWidth: isMobileDevice() ? 300 : 420,
            minWidth: isMobileDevice() ? 240 : 320,
            autoClose: true,
            closeOnClick: true,
            keepInView: true,
            className: 'mi-custom-popup',
            autoPanPadding: isMobileDevice() ? [20, 20] : [50, 50],
            autoPanPaddingTopLeft: isMobileDevice() ? [10, 10] : null,
            autoPanPaddingBottomRight: isMobileDevice() ? [10, 10] : null
        };

        const popup = L.popup(popupOptions)
            .setLatLng(latlng)
            .setContent(content);

        // Close existing popup before opening new one
        map.closePopup();

        // Add slight delay for better mobile UX
        setTimeout(() => {
            popup.openOn(map);

            // Auto-pan for mobile to ensure popup is visible
            if (isMobileDevice()) {
                setTimeout(() => {
                    map.panTo(latlng, {
                        animate: true,
                        duration: 0.5,
                        easeLinearity: 0.5
                    });
                }, 50);
            }
        }, 10);
    }

    function onEachWilayah(feature, layer) {
        layer.on({
            mouseover: function (e) {
                const l = e.target;
                l.setStyle({
                    weight: 3,
                    fillOpacity: 0.98,
                    dashArray: '',
                    color: '#ffffff'
                });
                l.bringToFront();

                // Add pulse animation class
                l._path.classList.add('mi-island-hover');

                // Tooltip hover (nama pulau) -> dibuat seperti popup (warna, icon, rounded)
                const props = feature.properties || {};
                const nama = props.name || props.nama || 'Pulau';

                const tooltipHtml = `
                  <div class="mi-hover-card">
                      <div class="mi-hover-icon">📍</div>
                      <div class="mi-hover-title">${nama}</div>
                  </div>
                `;

                l.bindTooltip(tooltipHtml, {
                    direction: 'top',
                    offset: [0, -12],
                    opacity: 1,
                    sticky: true,
                    className: 'mi-tooltip-card'
                }).openTooltip();
            },

            mouseout: function (e) {
                if (geojsonLayer) geojsonLayer.resetStyle(e.target);
                // Remove pulse animation
                if (e.target._path) {
                    e.target._path.classList.remove('mi-island-hover');
                }
                e.target.closeTooltip();
            },

            click: function (e) {
                // Prevent multiple popups on mobile
                if (isMobileDevice()) {
                    const now = Date.now();
                    if (layer._lastClick && (now - layer._lastClick < 500)) {
                        return; // Ignore double clicks on mobile
                    }
                    layer._lastClick = now;
                }

                map.closePopup();
                openRegionPopup(e.latlng, feature.properties || {});
            }
        });
    }

    // ===== LAYERS CONTROL =====
    const baseChoices = {};
    if (maptilerSatellite) baseChoices['Satellite (MapTiler)'] = maptilerSatellite;
    if (maptilerLight) baseChoices['Light (MapTiler)'] = maptilerLight;
    if (maptilerDark) baseChoices['Dark (MapTiler)'] = maptilerDark;
    baseChoices['OpenStreetMap'] = osmTiles;

    const overlays = {};

    const layersControl = L.control.layers(baseChoices, overlays, {
        position: 'topleft',
        collapsed: true
    }).addTo(map);

    // ===== RESIZE HANDLING =====
    function safeInvalidate() {
        setTimeout(() => map.invalidateSize(true), 80);
    }

    window.addEventListener('resize', safeInvalidate);
    window.addEventListener('orientationchange', () => setTimeout(safeInvalidate, 240));

    // ===== THEME SYNC =====
    function syncTheme() {
        if (geojsonLayer) geojsonLayer.setStyle(styleWilayah);
    }

    // Theme observer
    (function observeThemeChanges() {
        const observer = new MutationObserver(function (mutations) {
            for (const m of mutations) {
                if (m.attributeName === 'data-theme' || m.attributeName === 'class') {
                    setTimeout(() => {
                        syncTheme();
                        safeInvalidate();
                    }, 50);
                    break;
                }
            }
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme', 'class']
        });

        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                syncTheme();
                safeInvalidate();
            });
        }
    })();

    // ===== LOAD GEOJSON =====
    fetch(geojsonUrl)
        .then(async (response) => {
            if (!response.ok) throw new Error(`HTTP ${response.status} saat fetch GeoJSON`);
            const text = await response.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('[MAP] GeoJSON bukan JSON valid.');
                // Coba parse dengan error handling
                const cleaned = text.replace(/'/g, '"').replace(/,\s*]/g, ']').replace(/,\s*}/g, '}');
                return JSON.parse(cleaned);
            }
        })
        .then(data => {
            geojsonLayer = L.geoJSON(data, {
                style: styleWilayah,
                onEachFeature: onEachWilayah,
                smoothFactor: 1.2
            }).addTo(map);

            // Add to layer control
            overlays['Wilayah Indonesia'] = geojsonLayer;
            layersControl.addOverlay(geojsonLayer, 'Wilayah Indonesia');

            // Fit bounds ke Indonesia
            try {
                const bounds = geojsonLayer.getBounds();
                if (bounds && bounds.isValid && bounds.isValid()) {
                    map.fitBounds(bounds, {
                        padding: isMobileDevice() ? [20, 20] : [30, 30],
                        maxZoom: 6,
                        animate: true,
                        duration: 1
                    });
                }
            } catch (err) {
                console.warn('Tidak bisa fitBounds:', err);
                map.setView(INITIAL_VIEW.center, INITIAL_VIEW.zoom);
            }

            safeInvalidate();
            console.log('[MAP] GeoJSON loaded successfully with', data.features?.length || 0, 'features');
        })
        .catch(err => {
            console.error('Gagal memuat GeoJSON:', err);

            // Fallback dengan sample data dari contoh di gambar
            const sampleData = {
                "type": "FeatureCollection",
                "features": [{
                    "type": "Feature",
                    "properties": {
                        "name": "Pulau Papua",
                        "aksara": "Papua",
                        "kata_khas": "Waa Waa / Selamat",
                        "makna": "Ungkapan sapaan masyarakat Papua",
                        "digunakan_di": "Papua Barat, Papua, Papua Tengah, Papua Selatan, Papua Pegunungan",
                        "warna": "#FF4444"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [130.0, -1.0],
                            [141.0, -1.0],
                            [141.0, -10.0],
                            [130.0, -10.0],
                            [130.0, -1.0]
                        ]]
                    }
                }, {
                    "type": "Feature",
                    "properties": {
                        "name": "Pulau Jawa",
                        "aksara": "Jawa",
                        "kata_khas": "Monggo",
                        "makna": "Silakan, undangan dengan keramahan",
                        "digunakan_di": "Jawa Tengah, Yogyakarta, Jawa Timur, Jawa Barat",
                        "warna": "#36B37E"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [105.0, -5.5],
                            [114.5, -5.5],
                            [114.5, -8.8],
                            [105.0, -8.8],
                            [105.0, -5.5]
                        ]]
                    }
                }, {
                    "type": "Feature",
                    "properties": {
                        "name": "Pulau Kalimantan",
                        "aksara": "eekovk",
                        "kata_khas": "Mongöa / Sampe",
                        "makna": "Salam dan sapsan tradisional (beragam per da)",
                        "digunakan_di": "Sulawesi Utara, Tengah, Barat, Tengpara, dan*",
                        "warna": "#6554C0"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [108.0, 7.0],
                            [119.0, 7.0],
                            [119.0, -4.0],
                            [108.0, -4.0],
                            [108.0, 7.0]
                        ]]
                    }
                }]
            };

            geojsonLayer = L.geoJSON(sampleData, {
                style: styleWilayah,
                onEachFeature: onEachWilayah,
                smoothFactor: 1.2
            }).addTo(map);

            // Add to layer control
            overlays['Wilayah Indonesia'] = geojsonLayer;
            layersControl.addOverlay(geojsonLayer, 'Wilayah Indonesia');

            // Fit bounds
            map.fitBounds([[7, 108], [-4, 119]], {
                padding: isMobileDevice() ? [20, 20] : [30, 30],
                maxZoom: 6,
                animate: true
            });

            safeInvalidate();
            console.log('[MAP] Using sample data');
        });

    // ===== MOBILE TOUCH OPTIMIZATION =====
    if (isMobileDevice()) {
        // Disable some animations on mobile for better performance
        map.options.fadeAnimation = false;
        map.options.zoomAnimation = false;

        // Add touch gesture hints
        setTimeout(() => {
            const hint = L.popup({
                closeButton: true,
                autoClose: 5000,
                closeOnClick: true,
                className: 'mi-mobile-hint'
            })
            .setLatLng(INITIAL_VIEW.center)
            .setContent(`
                <div style="padding: 15px; text-align: center;">
                    <div style="font-size: 24px; margin-bottom: 10px;">📍</div>
                    <div style="font-weight: bold; margin-bottom: 5px;">Tips Mobile:</div>
                    <div style="font-size: 14px;">
                        • Sentuh pulau untuk info<br>
                        • Geser dengan 2 jari untuk zoom<br>
                        • Geser dengan 1 jari untuk pan
                    </div>
                </div>
            `);

            // Only show hint on first visit
            if (!localStorage.getItem('mapHintShown')) {
                setTimeout(() => {
                    hint.openOn(map);
                    localStorage.setItem('mapHintShown', 'true');
                }, 1000);
            }
        }, 500);
    }

})();
</script>


</section>
