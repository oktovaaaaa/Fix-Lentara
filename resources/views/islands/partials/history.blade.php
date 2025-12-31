{{-- resources/views/islands/partials/history.blade.php --}}

@php
    // ===============================
    // SAFETY DEFAULTS (JANGAN HAPUS)
    // ===============================
    $tribeKey               = $tribeKey ?? '';
    $currentTribeHistories  = $currentTribeHistories ?? collect();
    $historyFeatures        = $historyFeatures ?? collect();
@endphp

<section id="history" class="history-section py-12">
    <style>
        /* =========================================================
           HISTORY TIMELINE (DYNAMIC) - MATCH HOME.BLADE UI
           - Neon conic border muter (glow)
           - Desktop: center line + zig-zag cards
           - Mobile: left line
           - FIX: text never overflow card (wrap long strings)
        ========================================================= */

        /* ====== WRAPPER (PAKAI BG PARENT) ====== */
        #history.history-section {
            padding: 4rem 1.5rem;
            background: transparent;
            display: flex;
            justify-content: center;
            overflow: hidden; /* aman dari glow yang melebar */
        }

        #history .history-container {
            width: 100%;
            max-width: 1100px;
            text-align: center;
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--txt-body);
        }

        /* Title styling sama persis dengan contoh */
        #history .neon-title {
            font-size: clamp(1.75rem, 3vw, 2.25rem);
            font-weight: 700;
            margin-bottom: .5rem;
            background: linear-gradient(90deg, #ff6b00, #ff8c42);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            display: inline-block;
        }

        /* Title decoration sama persis dengan contoh */
        #history .mi-title-decoration {
            height: 4px;
            width: 80px;
            margin: 0 auto 1.5rem auto;
            background: linear-gradient(90deg, #ff6b00, #ff8c42);
            border-radius: 2px;
            box-shadow: 0 0 10px rgba(255, 107, 0, 0.5);
        }

        #history .history-subtitle {
            font-size: 1rem;
            max-width: 640px;
            margin: 0 auto 3rem auto;
            color: var(--muted);
            line-height: 1.6;
        }

        /* ====== TIMELINE ====== */
        #history .timeline {
            position: relative;
            padding: 2rem 0;
            margin: 0 auto;
        }

        /* garis tengah (desktop) / nanti di mobile digeser kiri */
        #history .timeline::before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 4px;
            transform: translateX(-50%);
            border-radius: 999px;
            background: linear-gradient(to bottom, #ff6b00, #ff8c42);
            box-shadow: 0 0 10px rgba(255, 107, 0, 0.5);
        }

        #history .timeline-item {
            position: relative;
            width: 100%;
            margin-bottom: 2.5rem;
            display: flex;
        }

        /* titik di tengah garis */
        #history .timeline-item::before {
            content: "";
            position: absolute;
            top: 26px;
            left: 50%;
            transform: translateX(-50%);
            width: 18px;
            height: 18px;
            border-radius: 999px;
            background: var(--bg-body);
            border: 4px solid #ff6b00;
            box-shadow: 0 0 15px rgba(255, 107, 0, 0.8);
            z-index: 2;
        }

        #history .timeline-card {
            position: relative;
            width: 100%;
            max-width: 520px;
            border-radius: 20px;
        }

        /* ===== NEON BORDER SMOOTH MUTER DI SEPANJANG GARIS CARD ===== */
        @property --border-angle {
            syntax: "<angle>";
            inherits: false;
            initial-value: 0deg;
        }

        #history .timeline-card-glow {
            position: absolute;
            inset: -5px;
            border-radius: inherit;
            padding: 10px;
            z-index: 0;
            pointer-events: none;
            background: conic-gradient(from var(--border-angle),
                    rgba(255, 107, 0, 0),
                    rgba(255, 140, 66, 0.2) 30deg,
                    #ff6b00 80deg,
                    #ffaa6b 120deg,
                    rgba(255, 140, 66, 0.2) 180deg,
                    rgba(255, 107, 0, 0) 240deg,
                    rgba(255, 140, 66, 0.25) 300deg,
                    #ff6b00 330deg,
                    rgba(255, 107, 0, 0) 360deg);
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
            to { --border-angle: 360deg; }
        }

        /* isi card */
        #history .timeline-card-inner {
            position: relative;
            border-radius: 18px;

            /* fallback aman kalau --card-bg-dark belum ada */
            background: linear-gradient(
                145deg,
                var(--card),
                color-mix(in oklab, var(--card) 82%, #020617 18%)
            );

            padding: 1.8rem 2rem;

            box-shadow:
                0 14px 32px rgba(0, 0, 0, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);

            z-index: 1;
            text-align: left;
            border: 1px solid rgba(255, 107, 0, 0.1);

            /* FIX: pastikan konten tidak keluar karena glitch */
            overflow: hidden;
        }

        /* Dark/Light mode adjustment (samakan home) */
        html[data-theme="dark"] #history .timeline-card-inner {
            background: linear-gradient(145deg, #111827, #020617);
        }
        html[data-theme="light"] #history .timeline-card-inner {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
        }

        #history .timeline-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            font-weight: 700;
            padding: .4rem 1rem;
            margin-bottom: .8rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #ff6b00, #ff8c42);
            color: white;
            box-shadow: 0 4px 12px rgba(255, 107, 0, 0.3);
            max-width: 100%;
        }

        #history .timeline-heading {
            font-size: 1.2rem;
            margin-bottom: .5rem;
            color: var(--txt-body);
            font-weight: 700;

            /* FIX overflow heading */
            overflow-wrap: anywhere;
            word-break: break-word;
            hyphens: auto;
            max-width: 100%;
        }

        #history .timeline-text {
            font-size: .95rem;
            line-height: 1.7;
            color: var(--muted);

            /* FIX UTAMA: long string TANPA SPASI (kayak screenshot) */
            overflow-wrap: anywhere;   /* paling kuat */
            word-break: break-word;    /* fallback */
            hyphens: auto;
            max-width: 100%;

            /* kalau ada <strong>, <em> dll tetap wrap */
        }
        #history .timeline-text * {
            overflow-wrap: anywhere;
            word-break: break-word;
            hyphens: auto;
        }

        #history .timeline-link {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            margin-top: .9rem;
            font-weight: 800;
            color: var(--brand);
            text-decoration: none;

            /* FIX supaya link juga wrap */
            overflow-wrap: anywhere;
            word-break: break-word;
            max-width: 100%;
        }
        #history .timeline-link:hover {
            text-decoration: underline;
        }

        /* ===== OPTIONAL: Title kecil di atas timeline (kalau kamu mau) ===== */
        #history .history-mini-title {
            font-size: 1rem;
            font-weight: 700;
            margin: 0 0 .25rem 0;
            color: var(--txt-body);
            text-align: left;
        }
        #history .history-mini-subtitle {
            font-size: .95rem;
            color: var(--muted);
            margin: 0 0 1.25rem 0;
            text-align: left;
        }

        /* ===== RESPONSIVE (SAMA PERSIS HOME) ===== */
        @media (max-width: 767px) {
            #history .timeline::before {
                left: 14px;
                transform: none;
            }

            #history .timeline-item {
                padding-left: 2.8rem;
            }

            #history .timeline-item::before {
                left: 14px;
                transform: none;
            }

            #history .history-container {
                text-align: left;
            }

            /* mobile padding card biar lega */
            #history .timeline-card-inner {
                padding: 1.25rem 1.25rem;
            }

            #history .timeline-heading {
                font-size: 1.05rem;
            }

            #history .timeline-text {
                font-size: .92rem;
            }
        }

        @media (min-width: 768px) {
            #history .timeline-item:nth-child(odd) {
                justify-content: flex-start;
                padding-right: 50%;
            }

            #history .timeline-item:nth-child(even) {
                justify-content: flex-end;
                padding-left: 50%;
            }

            #history .timeline-item:nth-child(odd) .timeline-card {
                margin-right: 2.2rem;
            }

            #history .timeline-item:nth-child(even) .timeline-card {
                margin-left: 2.2rem;
            }
        }

        /* Empty state */
        #history .history-empty {
            font-size: 1rem;
            color: var(--muted);
            padding: .5rem 0;
            text-align: left;
        }

        /* Optional features cards (pulau umum) */
        #history .feature-card {
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 1rem 1.1rem;
            background: var(--card);
            box-shadow: 0 10px 24px rgba(0,0,0,.08);
            text-align: left;

            overflow: hidden;
        }
        #history .feature-card h4 {
            font-size: 1rem;
            font-weight: 800;
            margin: 0 0 .35rem 0;
            color: var(--txt-body);

            overflow-wrap: anywhere;
            word-break: break-word;
            hyphens: auto;
        }
        #history .feature-card p {
            font-size: .92rem;
            color: var(--muted);
            line-height: 1.65;

            overflow-wrap: anywhere;
            word-break: break-word;
            hyphens: auto;
        }
    </style>

    <div class="history-container">
        {{-- Title: sama persis dengan contoh yang diberikan --}}
        <h2 class="neon-title">
            Sejarah Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}
        </h2>
        <div class="mi-title-decoration"></div>

        <p class="history-subtitle">
            Timeline sejarah yang membentuk identitas budaya dan perjalanan Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}.
        </p>

        @if($currentTribeHistories->count())
            <div class="timeline">
                @foreach($currentTribeHistories as $item)
                    <div class="timeline-item">
                        <div class="timeline-card">
                            <div class="timeline-card-glow"></div>

                            <div class="timeline-card-inner">
                                <div class="timeline-badge">
                                    {{ !empty($item->year_label) ? $item->year_label : 'Jejak Sejarah' }}
                                </div>

                                <h3 class="timeline-heading">
                                    {{ $item->title }}
                                </h3>

                                <p class="timeline-text">
                                    {{ $item->content }}
                                </p>

                                @if(!empty($item->more_link))
                                    <a href="{{ $item->more_link }}"
                                       target="_blank"
                                       rel="noopener"
                                       class="timeline-link">
                                        Lihat selengkapnya →
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="history-empty">
                Belum ada data sejarah {{ $tribeKey !== '' ? $tribeKey : 'suku' }} yang diinput dari admin.
            </p>
        @endif

        {{-- OPTIONAL: Sejarah pulau-level dari features (kalau kamu pakai) --}}
        @if($historyFeatures->count())
            <div class="space-y-3 mt-8">
                <h3 class="history-mini-title">Sejarah Pulau (Umum)</h3>
                <p class="history-mini-subtitle">
                    Ringkasan sejarah umum untuk pulau ini (opsional).
                </p>

                @foreach($historyFeatures as $f)
                    <div class="feature-card">
                        <h4>{{ $f->title }}</h4>
                        <p>{{ $f->content }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
