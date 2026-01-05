{{-- resources/views/islands/partials/destinations.blade.php --}}

@php
    // ===============================
    // SAFETY DEFAULTS (JANGAN HAPUS)
    // ===============================
    $tribeKey = $tribeKey ?? '';
    $tribeDestinations = $tribeDestinations ?? collect();

    $clampRating = function ($rating) {
        if ($rating === null || $rating === '') return 0.0;
        $n = (float) $rating;
        if ($n < 0) $n = 0;
        if ($n > 5) $n = 5;
        return $n;
    };

    $ratingParts = function ($rating) use ($clampRating) {
        $n = $clampRating($rating);
        $full = (int) floor($n);
        $dec  = $n - $full;

        // sesuai request kamu:
        // 4.5 => 4 full + 1 half
        // 4.9 => 4 full + 1 half (kalau nanti mau 5 full, bilang ya)
        $half = $dec >= 0.5 ? 1 : 0;

        $empty = 5 - $full - $half;
        if ($empty < 0) $empty = 0;

        return [$full, $half, $empty, $n];
    };
@endphp

<section id="destinations" class="py-12">
    <h2 class="neon-title">
        Destinasi Budaya Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}
    </h2>
    <div class="title-decoration"></div>
    <p class="neon-subtitle">
        Rekomendasi tempat dan pengalaman budaya yang berkaitan dengan Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}.
    </p>

    @if($tribeDestinations->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($tribeDestinations as $d)
                @php
                    [$full, $half, $empty, $n] = $ratingParts($d->rating);
                    $img = $d->image_display_url ?? null;
                @endphp

                <div class="border border-[var(--line)] rounded-2xl bg-[var(--card)] shadow-sm overflow-hidden">
                    <div class="relative">
                        @if($img)
                            <img src="{{ $img }}" alt="{{ $d->name }}" class="w-full h-44 object-cover block">
                        @else
                            <div class="w-full h-44 flex items-center justify-center text-sm text-[var(--muted)] bg-black/20">
                                Tidak ada gambar
                            </div>
                        @endif

                        {{-- Rating badge (pojok kiri atas) --}}
                        <div class="absolute top-3 left-3 bg-black/55 backdrop-blur px-3 py-1.5 rounded-full flex items-center gap-2">
                            <div class="flex items-center gap-0.5">
                                {{-- Full stars --}}
                                @for($i=0; $i < $full; $i++)
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="#ff6b00" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                @endfor

                                {{-- Half star --}}
                                @if($half === 1)
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <defs>
                                            <linearGradient id="halfGradDest{{ $d->id }}">
                                                <stop offset="50%" stop-color="#ff6b00" />
                                                <stop offset="50%" stop-color="rgba(255,255,255,0.25)" />
                                            </linearGradient>
                                        </defs>
                                        <path fill="url(#halfGradDest{{ $d->id }})" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                @endif

                                {{-- Empty stars --}}
                                @for($i=0; $i < $empty; $i++)
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="rgba(255,255,255,0.25)" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                @endfor
                            </div>

                            <div class="text-xs font-semibold text-white">
                                {{ rtrim(rtrim(number_format($n, 1, '.', ''), '0'), '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <h3 class="text-sm sm:text-base font-semibold mb-1">{{ $d->name }}</h3>

                        @if($d->location)
                            <div class="text-xs text-[var(--muted)] mb-2">
                                📍 {{ $d->location }}
                            </div>
                        @endif

                        @if($d->description)
                            <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed">
                                {{ $d->description }}
                            </p>
                        @else
                            <p class="text-xs text-[var(--muted)]">Deskripsi belum tersedia.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-[var(--muted)]">
            Konten destinasi untuk {{ $tribeKey }} belum diinput dari admin.
        </p>
    @endif
</section>
