@php
    $heroTitle = $tribePage->hero_title ?? ("Warisan " . $tribeKey);
    $heroDesc  = $tribePage->hero_description ?? "Eksplorasi warisan budaya berdasarkan suku di pulau ini.";
@endphp

<section class="py-10 sm:py-14">
    <div class="text-center max-w-3xl mx-auto">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--txt-body)]">
            {{ $heroTitle }}
        </h2>

        <div class="mx-auto mt-4 h-1 w-24 rounded-full bg-gradient-to-r from-[var(--brand)] to-[var(--brand-2)]"></div>

        <p class="mt-5 text-sm sm:text-base text-[var(--muted)] leading-relaxed">
            {{ $heroDesc }}
        </p>
    </div>

    @if(!empty($tribePage?->hero_image))
        <div class="mt-8 max-w-5xl mx-auto">
            <img
                src="{{ asset('storage/'.$tribePage->hero_image) }}"
                alt="{{ $heroTitle }}"
                class="w-full h-56 sm:h-72 md:h-80 object-cover rounded-3xl border border-[var(--line)] shadow-sm"
                loading="lazy"
            />
        </div>
    @endif
</section>
