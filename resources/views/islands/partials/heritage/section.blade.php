@php
    use App\Models\HeritageItem;

    $labels = HeritageItem::CATEGORIES;

    // pastikan keys selalu ada
    $itemsByCategory = $itemsByCategory ?? [
        'pakaian' => collect(),
        'rumah_tradisi' => collect(),
        'senjata_alatmusik' => collect(),
    ];
@endphp

<section id="warisan" class="space-y-8">
    {{-- HERO TITLE + DESC --}}
    @include('islands.partials.heritage.hero', [
        'tribePage' => $tribePage,
        'tribeKey' => $tribeKey,
    ])

    {{-- 3 KATEGORI --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($labels as $key => $label)
            @php $items = $itemsByCategory[$key] ?? collect(); @endphp

            <div class="rounded-3xl border border-[var(--line)] bg-[var(--card)] shadow-sm p-5 space-y-4">
                <div>
                    <h3 class="text-lg font-semibold text-[var(--txt-body)]">
                        {{ $label }}
                    </h3>
                    <p class="text-xs text-[var(--muted)] mt-1">
                        Data dari admin (per pulau & suku).
                    </p>
                </div>

                @if($items->count() === 0)
                    <div class="text-sm text-[var(--muted)] border border-[var(--line)] rounded-2xl p-4">
                        Belum ada data untuk kategori ini.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($items as $item)
                            <div class="rounded-2xl border border-[var(--line)] p-4 bg-[color-mix(in_srgb,var(--card)_92%,transparent)]">
                                <div class="font-semibold text-[var(--txt-body)] text-sm">
                                    {{ $item->title }}
                                </div>

                                @if($item->image_path)
                                    <img
                                        src="{{ asset('storage/'.$item->image_path) }}"
                                        alt="{{ $item->title }}"
                                        class="w-full h-40 object-cover rounded-xl mt-3 border border-[var(--line)]"
                                        loading="lazy"
                                    />
                                @endif

                                @if($item->description)
                                    <p class="text-xs sm:text-sm text-[var(--muted)] leading-relaxed mt-3">
                                        {{ $item->description }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</section>
