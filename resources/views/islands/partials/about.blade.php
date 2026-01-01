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
           ABOUT SUKU - Desain baru berdasarkan gambar referensi
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
            margin-bottom: 3rem;
            text-align: center;
            position: relative;
        }

        #about-tribe .about-label{
            display: inline-block;
            font-size: 0.875rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--brand);
            font-weight: 600;
            margin-bottom: 1rem;
            padding: 0.375rem 1rem;
            background: color-mix(in srgb, var(--brand) 10%, transparent);
            border-radius: 4px;
        }

        #about-tribe .about-title{
            font-size: clamp(2.25rem, 5vw, 3rem);
            line-height: 1.2;
            font-weight: 700;
            color: var(--txt-body);
            margin: 0 0 1rem 0;
            letter-spacing: -0.01em;
        }

        #about-tribe .about-desc{
            margin: 0 auto 2rem auto;
            max-width: 700px;
            color: color-mix(in srgb, var(--txt-body) 70%, transparent);
            font-size: 1.125rem;
            line-height: 1.7;
            font-weight: 400;
        }

        #about-tribe .about-head-actions{
            margin-top: 1.5rem;
        }

        #about-tribe .btn-more{
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            border: 2px solid var(--brand);
            background: var(--brand);
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9375rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #about-tribe .btn-more:hover{
            background: transparent;
            color: var(--brand);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px color-mix(in srgb, var(--brand) 20%, transparent);
        }

        /* ===== ITEM LIST ===== */
        #about-tribe .items{
            display: flex;
            flex-direction: column;
            gap: 4rem;
        }

        /* Layout utama - gambar di kiri, konten di kanan */
        #about-tribe .about-item{
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 4rem;
            align-items: start;
        }

        /* Alternating layout untuk item berikutnya */
        #about-tribe .about-item:nth-child(even) {
            grid-template-columns: 1.2fr 1fr;
        }

        #about-tribe .about-item:nth-child(even) .image-content {
            order: 2;
        }

        #about-tribe .about-item:nth-child(even) .text-content {
            order: 1;
        }

        /* Bagian gambar kiri */
        #about-tribe .image-content{
            position: relative;
            height: 100%;
        }

        #about-tribe .imgbox{
            border-radius: 12px;
            overflow: hidden;
            background: var(--card);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            height: 400px;
            width: 100%;
            position: relative;
            border: none;
        }

        #about-tribe .imgbox img{
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.8s ease;
        }

        #about-tribe .imgbox:hover img{
            transform: scale(1.05);
        }

        /* Efek overlay pada gambar */
        #about-tribe .imgbox::after{
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                45deg,
                rgba(183, 65, 14, 0.1) 0%,
                rgba(183, 65, 14, 0.05) 50%,
                transparent 100%
            );
            pointer-events: none;
        }

        /* Bagian konten kanan */
        #about-tribe .text-content{
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 1rem 0;
        }

        #about-tribe .item-title{
            margin: 0 0 1.5rem 0;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--txt-body);
            line-height: 1.3;
            position: relative;
            padding-bottom: 1rem;
        }

        #about-tribe .item-title::after{
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--brand);
            border-radius: 2px;
        }

        #about-tribe .item-desc{
            margin: 0 0 2rem 0;
            font-size: 1.0625rem;
            line-height: 1.8;
            color: color-mix(in srgb, var(--txt-body) 70%, transparent);
            white-space: pre-line;
        }

        /* Poin-poin checklist */
        #about-tribe .points{
            margin-top: 1.5rem;
            display: grid;
            gap: 0.875rem;
        }

        #about-tribe .point{
            display: flex;
            gap: 0.875rem;
            align-items: flex-start;
            font-size: 1rem;
            line-height: 1.6;
            color: color-mix(in srgb, var(--txt-body) 80%, transparent);
            padding: 0.5rem 0;
        }

        #about-tribe .check{
            width: 24px;
            height: 24px;
            min-width: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--brand), color-mix(in srgb, var(--brand) 80%, black));
            color: white;
            font-size: 0.875rem;
            font-weight: 600;
            flex-shrink: 0;
            margin-top: 0.125rem;
        }

        /* Bagian poin untuk layout tanpa gambar */
        #about-tribe .points-only{
            padding: 2.5rem;
            background: linear-gradient(135deg,
                color-mix(in srgb, var(--brand) 8%, transparent) 0%,
                color-mix(in srgb, var(--brand) 4%, transparent) 100%);
            border-radius: 12px;
            border: 1px solid color-mix(in srgb, var(--brand) 15%, transparent);
            margin-top: 1.5rem;
        }

        #about-tribe .points-only .points{
            margin-top: 0;
        }

        /* Link item */
        #about-tribe .item-link{
            margin-top: 2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            color: var(--brand);
            font-size: 0.9375rem;
            transition: all 0.3s ease;
            padding: 0.75rem 1.5rem;
            border: 2px solid color-mix(in srgb, var(--brand) 30%, transparent);
            border-radius: 6px;
            width: fit-content;
        }

        #about-tribe .item-link:hover{
            background: var(--brand);
            color: white;
            border-color: var(--brand);
            gap: 0.75rem;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px color-mix(in srgb, var(--brand) 20%, transparent);
        }

        /* Responsive */
        @media (max-width: 1024px){
            #about-tribe .about-item{
                gap: 3rem;
            }

            #about-tribe .imgbox{
                height: 350px;
            }
        }

        @media (max-width: 768px){
            #about-tribe.about-tribe-section{
                padding: 3rem 1rem;
            }

            #about-tribe .about-head{
                margin-bottom: 2.5rem;
            }

            #about-tribe .about-title{
                font-size: 2rem;
            }

            #about-tribe .about-desc{
                font-size: 1rem;
            }

            #about-tribe .items{
                gap: 3rem;
            }

            #about-tribe .about-item,
            #about-tribe .about-item:nth-child(even) {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            #about-tribe .about-item:nth-child(even) .image-content,
            #about-tribe .about-item:nth-child(even) .text-content {
                order: unset;
            }

            #about-tribe .imgbox{
                height: 300px;
            }

            #about-tribe .item-title{
                font-size: 1.5rem;
            }

            #about-tribe .points-only{
                padding: 2rem;
            }
        }

        @media (max-width: 480px){
            #about-tribe .about-title{
                font-size: 1.75rem;
            }

            #about-tribe .about-label{
                font-size: 0.75rem;
            }

            #about-tribe .imgbox{
                height: 250px;
            }

            #about-tribe .points-only{
                padding: 1.5rem;
            }
        }

        /* Animasi */
        #about-tribe .about-item {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.6s ease-out forwards;
        }

        #about-tribe .about-item:nth-child(1) { animation-delay: 0.1s; }
        #about-tribe .about-item:nth-child(2) { animation-delay: 0.2s; }
        #about-tribe .about-item:nth-child(3) { animation-delay: 0.3s; }
        #about-tribe .about-item:nth-child(4) { animation-delay: 0.4s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
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

        {{-- ITEMS --}}
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
                        $layout = 'with-image';
                    } elseif(!$hasImage && $hasPoints) {
                        $layout = 'text-points';
                    } else {
                        $layout = 'text-only';
                    }
                @endphp

                @if($layout === 'with-image')
                    {{-- LAYOUT DENGAN GAMBAR --}}
                    <div class="about-item">
                        <div class="image-content">
                            <div class="imgbox">
                                <img src="{{ $img }}" alt="{{ $title ?? 'Gambar' }}" loading="lazy">
                            </div>
                        </div>

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
                    </div>

                @elseif($layout === 'text-points')
                    {{-- LAYOUT TANPA GAMBAR TAPI ADA POIN --}}
                    <div class="about-item">
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
                    <div class="about-item">
                        <div class="text-content" style="grid-column: 1 / -1;">
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
                    </div>
                @endif

            @empty
                <div class="about-item">
                    <div class="text-content" style="grid-column: 1 / -1; text-align: center;">
                        <p class="item-desc" style="padding: 3rem 1rem; color: color-mix(in srgb, var(--txt-body) 50%, transparent);">
                            Konten About untuk suku <strong>{{ $tribeKey ?: '—' }}</strong> belum diinput.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
