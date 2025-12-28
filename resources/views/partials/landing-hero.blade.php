{{-- resources/views/partials/landing-hero.blade.php --}}

@php
    $hasSelected = isset($selectedIsland) && $selectedIsland;
@endphp

{{-- CSS khusus landing (hero + cards) --}}
<style>
    .landing-root * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    .landing-root {
        font-family: "Inter", sans-serif;
    }

    .card {
        position: absolute;
        background-size: cover;
        background-position: center;
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        color: #ffffffdd;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .card {
            background-position: right center;
        }
    }

    .indicator {
        position: absolute;
        bottom: 80px;
        left: 0;
        width: 100%;
        height: 4px;
        background: #ecad29;
        transform: translateX(-100%);
    }

    .cover {
        position: absolute;
        top: 0;
        left: 0;
        width: 200%;
        height: 100%;
        background: #1a1a1a;
        z-index: 99;
    }

    .content-title-1,
    .content-title-2 {
        font-family: "Oswald", sans-serif;
    }

    .hero-title-wrapper {
        position: absolute;
        inset-inline: 0;
        z-index: 40;
        pointer-events: none;
        display: flex;
    }

    @media (max-width: 768px) {
        .hero-title-wrapper {
            position: absolute;
            top: 28%;
            transform: translateY(-50%);
            justify-content: center;
            padding-inline: 1rem;
            text-align: center;
        }
    }

    @media (min-width: 769px) {
        .hero-title-wrapper {
            position: absolute;
            top: 50%;
            transform: translateY(-45%);
            justify-content: flex-start;
            padding-inline: 3rem;
            text-align: left;
        }
    }
</style>

<section
    id="home"
    class="landing-root landing-hero relative h-screen w-full overflow-hidden bg-[#1a1a1a] text-white"
>
    <div class="cover"></div>

    {{-- JUDUL BESAR --}}
    @if($hasSelected)
        {{-- Halaman pulau --}}
        <div class="hero-title-wrapper">
            <div class="max-w-3xl space-y-2">
                @if($selectedIsland->place_label)
                    <p class="hero-title-line text-[9px] sm:text-[10px] md:text-xs tracking-[0.3em] uppercase text-white/80">
                        {{ $selectedIsland->place_label }}
                    </p>
                @endif

                <h1
                    class="hero-title-line content-title-1
                           text-3xl sm:text-4xl md:text-6xl lg:text-7xl
                           font-bold leading-tight sm:leading-none drop-shadow-[0_0_20px_rgba(0,0,0,0.7)]">
                    {{ strtoupper($selectedIsland->title ?? $selectedIsland->name) }}
                </h1>

                @if($selectedIsland->subtitle)
                    <p
                        class="hero-title-line content-title-2
                               text-lg sm:text-2xl md:text-3xl lg:text-4xl
                               font-semibold text-white drop-shadow-[0_0_20px_rgba(0,0,0,0.7)]">
                        {{ strtoupper($selectedIsland->subtitle) }}
                    </p>
                @endif
            </div>
        </div>
    @else
        {{-- Home: Budaya Indonesia --}}
        <div class="hero-title-wrapper">
            <div class="max-w-3xl space-y-2 text-center md:text-left">
                <p class="hero-title-line text-[9px] sm:text-[10px] md:text-xs tracking-[0.3em] uppercase text-white/80">
                    Nusantara • Pulau • Cerita • Tradisi
                </p>
                <h1
                    class="hero-title-line content-title-1
                           text-3xl sm:text-4xl md:text-6xl lg:text-7xl
                           font-bold leading-tight sm:leading-none drop-shadow-[0_0_20px_rgba(0,0,0,0.7)]">
                    BUDAYA INDONESIA
                </h1>
                <p
                    class="hero-title-line content-title-2
                           text-base sm:text-xl md:text-2xl lg:text-3xl
                           font-semibold text-white/90 drop-shadow-[0_0_20px_rgba(0,0,0,0.7)]">
                    Jelajahi pulau, cerita daerah, dan tradisi Nusantara.
                </p>
            </div>
        </div>
    @endif

    {{-- AREA ANIMASI KARTU --}}
    <div id="demo" class="absolute inset-0 overflow-hidden"></div>

    {{-- PAGINATION & NOMOR SLIDE --}}
    <div
        id="pagination"
        class="absolute bottom-16 sm:bottom-12 left-1/2 -translate-x-1/2 flex gap-2 text-white text-xs sm:text-sm"
    >
        <div class="progress-sub-foreground"></div>
    </div>

    <div
        id="slide-numbers"
        class="absolute bottom-8 sm:bottom-6 left-1/2 -translate-x-1/2 flex gap-2 text-[14px] sm:text-[18px]"
    ></div>

    <div class="indicator"></div>
</section>

@push('scripts')
    {{-- GSAP untuk hero cards --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const data        = @json($carouselData);
            const initialSlug = @json(optional($selectedIsland)->slug);

            if (!Array.isArray(data) || data.length === 0) {
                console.warn('Tidak ada data island di database.');
                return;
            }

            const _ = (id) => document.getElementById(id);
            const coverEl = document.querySelector('.cover');

            // ✅ DIPERBAIKI: card punya data-url agar loader interceptor bisa menangkap
            const cards = data.map((i, index) => `
              <div
                class="card"
                id="card${index}"
                data-slug="${i.slug}"
                data-url="/islands/${i.slug}"
                role="link"
                tabindex="0"
                style="background-image:url(${i.image})"
              >
                <div class="card-content bg-gradient-to-t from-black/60 to-transparent p-4 sm:p-5">
                  <div class="content-place text-[11px] sm:text-[13px] tracking-[1px] opacity-80 uppercase">
                    ${i.place ?? ''}
                  </div>
                  <div class="content-title-1 text-[22px] sm:text-[26px] leading-none">
                    ${i.title ?? ''}
                  </div>
                  <div class="content-title-2 text-[18px] sm:text[22px] leading-none">
                    ${i.title2 ?? ''}
                  </div>
                </div>
              </div>
            `).join('');

            const demoEl = _('demo');
            if (!demoEl) return;
            demoEl.innerHTML = cards;

            const slideNumbers = data
                .map((_, index) => `<div class="item" id="slide-item-${index}">${index + 1}</div>`)
                .join('');
            const slideNumbersEl = _('slide-numbers');
            if (slideNumbersEl) slideNumbersEl.innerHTML = slideNumbers;

            // ✅ DIPERBAIKI: jangan pakai window.location.href di sini
            // karena loader interceptor akan handle via data-url
            // tapi tetap support keyboard Enter / Space
            function attachCardClicks() {
                const cardElements = document.querySelectorAll('.card');

                cardElements.forEach((card) => {
                    card.addEventListener('keydown', (ev) => {
                        if (ev.key === 'Enter' || ev.key === ' ') {
                            ev.preventDefault();
                            card.click();
                        }
                    });
                });
            }

            const range = (n) => Array(n).fill(0).map((_, j) => j);
            const set   = gsap.set;
            const ease  = "sine.inOut";

            function getCard(index)       { return `#card${index}`; }
            function getSliderItem(index) { return `#slide-item-${index}`; }

            let order = range(data.length);

            if (initialSlug) {
                const idx = data.findIndex(item => item.slug === initialSlug);
                if (idx > 0) {
                    order = [
                        ...order.slice(idx),
                        ...order.slice(0, idx),
                    ];
                }
            }

            let offsetTop   = 200;
            let offsetLeft  = 700;
            let cardWidth   = 200;
            let cardHeight  = 300;
            let gap         = 40;
            let numberSize  = 50;

            function animate(target, duration, properties) {
                return new Promise((resolve) => {
                    gsap.to(target, {
                        ...properties,
                        duration,
                        onComplete: resolve
                    });
                });
            }

            function setupLayout() {
                const { innerHeight: height, innerWidth: width } = window;

                if (width >= 768) {
                    cardWidth  = 220;
                    cardHeight = 320;
                    offsetTop  = height - 430;
                    offsetLeft = width - 830;
                    gap        = 40;
                } else {
                    cardWidth  = Math.min(width * 0.75, 260);
                    cardHeight = Math.min(height * 0.45, 280);
                    offsetTop  = height - (cardHeight + 110);
                    offsetLeft = width - (cardWidth * 1.9);
                    gap        = 24;
                }
            }

            function init() {
                setupLayout();

                const [active, ...rest] = order;
                const { innerHeight: height, innerWidth: width } = window;

                if (coverEl) {
                    gsap.set(coverEl, { x: 0 });
                }

                gsap.set(getCard(active), {
                    x: 0,
                    y: 0,
                    width: width,
                    height: height,
                    borderRadius: 0
                });

                rest.forEach((i, index) => {
                    gsap.set(getCard(i), {
                        x: offsetLeft + 400 + index * (cardWidth + gap),
                        y: offsetTop,
                        width: cardWidth,
                        height: cardHeight,
                        zIndex: 30,
                        borderRadius: 10
                    });
                });

                if (coverEl) {
                    gsap.to(coverEl, {
                        x: window.innerWidth + 400,
                        duration: 1.2,
                        ease,
                        onComplete: () => {
                            if (order.length > 1) loop();
                        }
                    });
                } else {
                    if (order.length > 1) loop();
                }
            }

            function step() {
                return new Promise((resolve) => {
                    order.push(order.shift());
                    const [active, ...rest] = order;
                    const prv = rest[rest.length - 1];

                    gsap.set(getCard(prv),    { zIndex: 10 });
                    gsap.set(getCard(active), { zIndex: 20 });
                    gsap.to(getCard(prv),     { scale: 1.5, ease });

                    gsap.to(getCard(active), {
                        x: 0,
                        y: 0,
                        ease,
                        width: window.innerWidth,
                        height: window.innerHeight,
                        borderRadius: 0,
                        onComplete: () => {
                            const xNew = offsetLeft + (rest.length - 1) * (cardWidth + gap);
                            gsap.set(getCard(prv), {
                                x: xNew,
                                y: offsetTop,
                                width: cardWidth,
                                height: cardHeight,
                                zIndex: 30,
                                scale: 1,
                                borderRadius: 10
                            });
                            if (slideNumbersEl) {
                                gsap.set(getSliderItem(prv), { x: rest.length * numberSize });
                            }
                            resolve();
                        }
                    });

                    rest.forEach((i, index) => {
                        if (i !== prv) {
                            const xNew = offsetLeft + index * (cardWidth + gap);
                            gsap.to(getCard(i), {
                                x: xNew,
                                y: offsetTop,
                                width: cardWidth,
                                height: cardHeight,
                                ease,
                                delay: 0.1 * (index + 1)
                            });
                        }
                    });
                });
            }

            async function loop() {
                if (order.length <= 1) return;

                await animate(".indicator", 2,   { x: 0 });
                await animate(".indicator", 0.8, { x: window.innerWidth, delay: 0.3 });
                set(".indicator", { x: -window.innerWidth });
                await step();
                loop();
            }

            function loadImage(src) {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.onload  = () => resolve(img);
                    img.onerror = reject;
                    img.src     = src;
                });
            }

            async function loadImages() {
                const promises = data.map(({ image }) => loadImage(image));
                return Promise.all(promises);
            }

            function animateHeroTitle() {
                const heroLines = document.querySelectorAll('.hero-title-line');
                if (!heroLines.length) return;

                gsap.from(heroLines, {
                    y: 40,
                    opacity: 0,
                    duration: 1.1,
                    ease: "power3.out",
                    stagger: 0.15
                });
            }

            async function start() {
                try {
                    await loadImages();
                    attachCardClicks();
                    init();
                    animateHeroTitle();
                } catch (e) {
                    console.error("Image failed to load", e);
                }
            }

            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    init();
                }, 150);
            });

            start();
        });
    </script>
@endpush
