{{-- resources/views/islands/partials/tribe-styles.blade.php --}}
<style>
    .tribe-tab {
        background: rgba(148, 163, 184, 0.12); /* abu soft, masih kelihatan di dark / light */
        color: var(--txt-body, #020617);
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.55rem 1.5rem;
        border: 1px solid transparent;
        transition: all 0.18s ease-out;
    }

    .tribe-tab:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.25);
    }

    .tribe-tab.is-active {
        background-image: linear-gradient(90deg, #fb923c, #f97316, #fb7185);
        color: #f9fafb;
        border-color: rgba(248, 250, 252, 0.45);
        box-shadow:
                0 0 0 1px rgba(248, 250, 252, 0.25),
                0 20px 40px rgba(0, 0, 0, 0.55);
    }

    [data-tribe-panel].hidden {
        display: none;
    }

    .history-empty {
        margin-top: 1rem;
        font-size: 0.8rem;
        color: var(--muted, #9ca3af);
    }

    .history-section {
        padding: 4rem 1.5rem 2rem 1.5rem;
        background: transparent;
        display: flex;
        justify-content: center;
    }

    .history-container {
        width: 100%;
        max-width: 1100px;
        text-align: center;
        font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        color: var(--txt-body);
    }

    .history-title {
        font-size: clamp(1.75rem, 3vw, 2.25rem);
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .history-subtitle {
        font-size: 0.95rem;
        max-width: 640px;
        margin: 0 auto 3rem auto;
        color: var(--muted);
    }

    .timeline {
        position: relative;
        padding: 2rem 0;
        margin: 0 auto;
    }

    .timeline::before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 50%;
        width: 4px;
        transform: translateX(-50%);
        border-radius: 999px;
        background: linear-gradient(to bottom, #fef3c7, #f97316);
    }

    .timeline-item {
        position: relative;
        width: 100%;
        margin-bottom: 2.5rem;
        display: flex;
    }

    .timeline-item::before {
        content: "";
        position: absolute;
        top: 26px;
        left: 50%;
        transform: translateX(-50%);
        width: 14px;
        height: 14px;
        border-radius: 999px;
        background: var(--bg-body);
        border: 3px solid #f97316;
        box-shadow: 0 0 10px rgba(249, 115, 22, 0.6);
        z-index: 2;
    }

    .timeline-card {
        position: relative;
        max-width: 520px;
        border-radius: 20px;
    }

    @property --border-angle {
        syntax: "<angle>";
        inherits: false;
        initial-value: 0deg;
    }

    .timeline-card-glow {
        position: absolute;
        inset: -5px;
        border-radius: inherit;
        padding: 10px;
        z-index: 0;
        pointer-events: none;

        background: conic-gradient(
            from var(--border-angle),
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

        animation: neon-border-spin 8s linear infinite;
    }

    @keyframes neon-border-spin {
        to {
            --border-angle: 360deg;
        }
    }

    .timeline-card-inner {
        position: relative;
        border-radius: 18px;
        background: var(--card);
        padding: 1.6rem 1.8rem;
        box-shadow:
                0 14px 32px rgba(0, 0, 0, 0.18),
                0 0 0 1px rgba(255, 255, 255, 0.12);
        z-index: 1;
        text-align: left;
    }

    .timeline-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.2rem 0.75rem;
        margin-bottom: 0.5rem;
        border-radius: 999px;
        background: linear-gradient(to right, #fef3c7, #f97316);
        color: #7c2d12;
    }

    .timeline-heading {
        font-size: 1.1rem;
        margin-bottom: 0.35rem;
        color: var(--txt-body);
    }

    .timeline-text {
        font-size: 0.95rem;
        line-height: 1.6;
        color: var(--muted);
    }

    .timeline-link {
        margin-top: 0.3rem;
        display: inline-block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--brand, #f97316);
        text-decoration: none;
    }

    .timeline-link:hover {
        text-decoration: underline;
    }

    @media (max-width: 767px) {
        .timeline::before {
            left: 14px;
            transform: none;
        }

        .timeline-item {
            padding-left: 2.8rem;
        }

        .timeline-item::before {
            left: 14px;
            transform: none;
        }

        .history-container {
            text-align: left;
        }
    }

    @media (min-width: 768px) {
        .timeline-item:nth-child(odd) {
            justify-content: flex-start;
            padding-right: 50%;
        }

        .timeline-item:nth-child(even) {
            justify-content: flex-end;
            padding-left: 50%;
        }

        .timeline-item:nth-child(odd) .timeline-card {
            margin-right: 2.2rem;
        }

        .timeline-item:nth-child(even) .timeline-card {
            margin-left: 2.2rem;
        }
    }
</style>
