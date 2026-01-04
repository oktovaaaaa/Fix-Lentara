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

    <div class="about-wrap">
        {{-- HEADER --}}
        <div class="about-head">
            <div class="about-label">{{ $labelSmall }}</div>

            {{-- TITLE SYSTEM: harus sama seperti islands/home --}}
            <div class="about-title-wrap">
                <h2 class="neon-title">{{ $heroTitle }}</h2>
                <div class="title-decoration"></div>

                @if($heroDesc)
                    <p class="neon-subtitle">{{ $heroDesc }}</p>
                @else
                    <p class="neon-subtitle">
                        Penjelasan singkat tentang suku ini, termasuk poin-poin penting yang kamu input di admin agar tampil konsisten di seluruh halaman.
                    </p>
                @endif
            </div>

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
