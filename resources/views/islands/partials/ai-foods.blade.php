{{-- resources/views/islands/partials/ai-foods.blade.php --}}

@php
    $tribeKey = $tribeKey ?? '';
    $aiFoodRecommendation = $aiFoodRecommendation ?? null;

    $payload = $aiFoodRecommendation?->payload ?? null;
    $items = is_array($payload) ? ($payload['items'] ?? []) : [];
@endphp

<section id="foods" class="py-12">
    <h2 class="neon-title">
        Kuliner Khas Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}
    </h2>
    <div class="title-decoration"></div>
    <p class="neon-subtitle">
        Rekomendasi kuliner khas yang dikurasi otomatis per minggu untuk Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}.
    </p>

    @if(!empty($items))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
            @foreach($items as $it)
                @php
                    $name = $it['name'] ?? '';
                    $desc = $it['description'] ?? '';
                    $img  = $it['image_url'] ?? '';
                    $price = $it['price_range'] ?? null;
                    $rating = $it['rating_estimate'] ?? null;
                    $region = $it['region_hint'] ?? null;
                    $where = $it['where_to_find'] ?? [];
                @endphp

                <div class="border border-[var(--line)] rounded-2xl overflow-hidden bg-[var(--card)] shadow-sm">
                    <div class="w-full h-44 bg-black/10">
                        <img src="{{ $img }}" alt="{{ $name }}" class="w-full h-full object-cover" loading="lazy">
                    </div>

                    <div class="p-4">
                        <h3 class="text-base sm:text-lg font-semibold mb-1">
                            {{ $name }}
                        </h3>

                        <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mb-3">
                            {{ $desc }}
                        </p>

                        <div class="flex flex-wrap gap-2 text-xs">
                            @if($price)
                                <span class="px-2 py-1 rounded-full border border-[var(--line)]">
                                    💰 {{ $price }}
                                </span>
                            @endif

                            @if($rating)
                                <span class="px-2 py-1 rounded-full border border-[var(--line)]">
                                    ⭐ {{ $rating }}
                                </span>
                            @endif

                            @if($region)
                                <span class="px-2 py-1 rounded-full border border-[var(--line)]">
                                    📍 {{ $region }}
                                </span>
                            @endif

                            @if(is_array($where) && count($where))
                                <span class="px-2 py-1 rounded-full border border-[var(--line)]">
                                    🏪 {{ implode(', ', array_slice($where, 0, 2)) }}
                                </span>
                            @endif
                        </div>

                        {{-- detail sederhana (sources) --}}
                        @php $sources = $it['sources'] ?? []; @endphp
                        @if(is_array($sources) && count($sources))
                            <div class="mt-3">
                                <a href="{{ $sources[0] }}" target="_blank" rel="noopener"
                                   class="text-xs font-semibold text-[var(--brand)] hover:underline">
                                    Lihat sumber
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <p class="text-xs text-[var(--muted)] mt-4">
            Update mingguan: {{ $payload['generated_at'] ?? '—' }} (Senin 00:00)
        </p>
    @else
        <p class="text-sm text-[var(--muted)]">
            Rekomendasi kuliner untuk {{ $tribeKey }} minggu ini belum tersedia.
            Admin: jalankan scheduler atau dispatch job generate.
        </p>
    @endif
</section>
