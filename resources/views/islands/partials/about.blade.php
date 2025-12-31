{{-- resources/views/islands/partials/about.blade.php --}}

@php
    // SAFETY DEFAULTS
    $tribeKey   = $tribeKey ?? '';
    $aboutPage  = $aboutPage ?? null;
    $aboutItems = $aboutItems ?? collect();

    $labelSmall = $aboutPage->label_small ?? ('MENGENAL ' . strtoupper($tribeKey ?: 'SUKU'));
    $heroTitle  = $aboutPage->hero_title ?? ('Apa itu ' . ($tribeKey ?: 'Suku') . ' ?');
    $heroDesc   = $aboutPage->hero_description ?? null;
    $headerLink = $aboutPage->more_link ?? null;
@endphp

<section id="about-tribe" class="about-tribe-section py-12">
    <style>
        /* =========================================================
           ABOUT SUKU - Mengikuti desain gambar dengan tema CSS variables
        ========================================================= */

        #about-tribe.about-tribe-section{
            padding: 5rem 1.5rem;
            background: transparent;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        #about-tribe .about-wrap{
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ===== HEADER ===== */
        #about-tribe .about-head{
            margin-bottom: 4rem;
            text-align: left;
            position: relative;
        }

        #about-tribe .about-label{
            display: inline-block;
            font-size: 0.75rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--brand);
            font-weight: 700;
            margin-bottom: 1rem;
            padding: 0.25rem 0.75rem;
            background: color-mix(in srgb, var(--brand) 8%, transparent);
            border-radius: 2rem;
        }

        #about-tribe .about-title{
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            line-height: 1.1;
            font-weight: 800;
            color: var(--txt-body);
            margin: 0 0 1.5rem 0;
            letter-spacing: -0.02em;
        }

        #about-tribe .about-desc{
            margin: 0 0 2rem 0;
            max-width: 48rem;
            color: color-mix(in srgb, var(--txt-body) 70%, transparent);
            font-size: 1.125rem;
            line-height: 1.7;
            font-weight: 400;
        }

        #about-tribe .about-head-actions{
            margin-top: 2rem;
        }

        #about-tribe .btn-more{
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 3rem;
            border: 1px solid color-mix(in srgb, var(--brand) 40%, transparent);
            background: color-mix(in srgb, var(--brand) 10%, transparent);
            color: var(--brand);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #about-tribe .btn-more:hover{
            background: color-mix(in srgb, var(--brand) 20%, transparent);
            border-color: color-mix(in srgb, var(--brand) 60%, transparent);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px color-mix(in srgb, var(--brand) 15%, transparent);
        }

        /* ===== ITEM LIST ===== */
        #about-tribe .items{
            display: grid;
            grid-template-columns: 1fr;
            gap: 3rem;
        }

        /* Layout dengan gambar (lengkap) */
        #about-tribe .layout-with-image{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: start;
        }

        /* Layout tanpa gambar (hanya teks dan poin) */
        #about-tribe .layout-text-points{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: start;
        }

        /* Bagian konten kiri (teks) */
        #about-tribe .text-content{
            flex: 1;
        }

        #about-tribe .item-title{
            margin: 0 0 1.5rem 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--txt-body);
            letter-spacing: -0.01em;
            line-height: 1.3;
        }

        #about-tribe .item-desc{
            margin: 0 0 1.5rem 0;
            font-size: 1rem;
            line-height: 1.75;
            color: color-mix(in srgb, var(--txt-body) 70%, transparent);
            white-space: pre-line;
        }

        /* Poin-poin checklist */
        #about-tribe .points{
            margin-top: 2rem;
            display: grid;
            gap: 1rem;
        }

        #about-tribe .point{
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            font-size: 0.9375rem;
            line-height: 1.6;
            color: color-mix(in srgb, var(--txt-body) 70%, transparent);
            padding: 0.5rem 0;
        }

        #about-tribe .check{
            width: 1.25rem;
            height: 1.25rem;
            min-width: 1.25rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--brand), color-mix(in srgb, var(--brand) 80%, black));
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.125rem;
            flex-shrink: 0;
        }

        /* Bagian gambar */
        #about-tribe .image-content{
            flex: 1;
            position: relative;
        }

        #about-tribe .imgbox{
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: var(--shadow);
            height: 100%;
            min-height: 20rem;
            position: relative;
        }

        #about-tribe .imgbox img{
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }

        #about-tribe .imgbox:hover img{
            transform: scale(1.05);
        }

        /* Bagian poin untuk layout tanpa gambar */
        #about-tribe .points-only{
            padding: 2rem;
            background: color-mix(in srgb, var(--brand) 5%, transparent);
            border-radius: 1rem;
            border: 1px solid color-mix(in srgb, var(--brand) 15%, transparent);
        }

        #about-tribe .points-only .points{
            margin-top: 0;
        }

        /* Link item */
        #about-tribe .item-link{
            margin-top: 1.5rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            color: var(--brand);
            font-size: 0.875rem;
            transition: all 0.2s ease;
            padding: 0.5rem 0;
        }

        #about-tribe .item-link:hover{
            gap: 0.75rem;
            color: color-mix(in srgb, var(--brand) 80%, white);
        }

        /* Responsive */
        @media (max-width: 1024px){
            #about-tribe .about-title{
                font-size: 2.5rem;
            }

            #about-tribe .about-desc{
                font-size: 1.0625rem;
            }
        }

        @media (max-width: 768px){
            #about-tribe.about-tribe-section{
                padding: 3rem 1rem;
            }

            #about-tribe .about-head{
                margin-bottom: 3rem;
            }

            #about-tribe .about-title{
                font-size: 2rem;
            }

            #about-tribe .about-desc{
                font-size: 1rem;
            }

            #about-tribe .items{
                gap: 2rem;
            }

            #about-tribe .layout-with-image,
            #about-tribe .layout-text-points{
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            #about-tribe .points-only{
                order: 2;
            }

            #about-tribe .item-title{
                font-size: 1.25rem;
            }
        }

        @media (max-width: 480px){
            #about-tribe .about-title{
                font-size: 1.75rem;
            }

            #about-tribe .about-label{
                font-size: 0.6875rem;
            }

            #about-tribe .points-only{
                padding: 1.5rem;
            }
        }

        /* Animasi subtle */
        #about-tribe .layout-with-image,
        #about-tribe .layout-text-points,
        #about-tribe .layout-text-only {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="about-wrap">
        {{-- HEADER --}}
        <div class="about-head">
            <div class="about-label">{{ $labelSmall }}</div>
            <h2 class="about-title">{{ $heroTitle }}</h2>

            @if($heroDesc)
                <p class="about-desc">{{ $heroDesc }}</p>
            @endif

            @if($headerLink)
                <div class="about-head-actions">
                    <a class="btn-more" href="{{ $headerLink }}" target="_blank" rel="noopener">
                        Selengkapnya <span aria-hidden="true">→</span>
                    </a>
                </div>
            @endif
        </div>

        {{-- ITEMS (TANPA CARD, langsung konten) --}}
        <div class="items">
            @forelse($aboutItems as $it)
                @php
                    $title = $it->title ?: null;
                    $desc  = $it->description ?? '';
                    $img   = $it->image ?: null;
                    $link  = $it->more_link ?: null;

                    // points array from model helper
                    $pointsArr = method_exists($it, 'pointsArray') ? $it->pointsArray() : [];
                    $hasPoints = !empty($pointsArr);
                    $hasImage  = !empty($img);

                    // Tentukan layout berdasarkan ketersediaan data
                    if($hasImage && $hasPoints) {
                        $layout = 'with-image'; // Layout lengkap: kiri teks+poin, kanan gambar
                    } elseif(!$hasImage && $hasPoints) {
                        $layout = 'text-points'; // Layout tanpa gambar: kiri teks, kanan poin
                    } else {
                        $layout = 'text-only'; // Layout hanya teks
                    }
                @endphp

                @if($layout === 'with-image')
                    {{-- LAYOUT LENGKAP: dengan gambar dan poin --}}
                    <div class="layout-with-image">
                        <div class="text-content">
                            @if($title)
                                <h3 class="item-title">{{ $title }}</h3>
                            @endif

                            @if($desc)
                                <p class="item-desc">{{ $desc }}</p>
                            @endif

                            @if($hasPoints)
                                <div class="points">
                                    @foreach($pointsArr as $p)
                                        <div class="point">
                                            <span class="check">✓</span>
                                            <span>{{ $p }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($link)
                                <a class="item-link" href="{{ $link }}" target="_blank" rel="noopener">
                                    Selengkapnya <span aria-hidden="true">→</span>
                                </a>
                            @endif
                        </div>

                        <div class="image-content">
                            <div class="imgbox">
                                <img src="{{ $img }}" alt="{{ $title ?? 'Gambar' }}" loading="lazy">
                            </div>
                        </div>
                    </div>

                @elseif($layout === 'text-points')
                    {{-- LAYOUT TANPA GAMBAR: kiri teks, kanan poin --}}
                    <div class="layout-text-points">
                        <div class="text-content">
                            @if($title)
                                <h3 class="item-title">{{ $title }}</h3>
                            @endif

                            @if($desc)
                                <p class="item-desc">{{ $desc }}</p>
                            @endif

                            @if($link)
                                <a class="item-link" href="{{ $link }}" target="_blank" rel="noopener">
                                    Selengkapnya <span aria-hidden="true">→</span>
                                </a>
                            @endif
                        </div>

                        <div class="points-only">
                            <div class="points">
                                @foreach($pointsArr as $p)
                                    <div class="point">
                                        <span class="check">✓</span>
                                        <span>{{ $p }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                @else
                    {{-- LAYOUT HANYA TEKS --}}
                    <div class="layout-text-only">
                        @if($title)
                            <h3 class="item-title">{{ $title }}</h3>
                        @endif

                        @if($desc)
                            <p class="item-desc">{{ $desc }}</p>
                        @endif

                        @if($link)
                            <a class="item-link" href="{{ $link }}" target="_blank" rel="noopener">
                                Selengkapnya <span aria-hidden="true">→</span>
                            </a>
                        @endif
                    </div>
                @endif

            @empty
                <div class="layout-text-only">
                    <p class="item-desc" style="text-align: center; padding: 3rem 1rem; color: color-mix(in srgb, var(--txt-body) 50%, transparent);">
                        Konten About untuk suku <strong>{{ $tribeKey ?: '—' }}</strong> belum diinput.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</section>
