{{-- resources/views/admin/histories/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit History')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-4">Edit History Pulau &amp; Suku</h1>

    <form action="{{ route('admin.histories.update', $history) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Pulau --}}
        <div>
            <label class="block text-xs font-medium mb-1">Pulau</label>
            <select name="island_id" id="islandSelect"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                @foreach($islands as $island)
                    <option value="{{ $island->id }}"
                            data-slug="{{ $island->slug }}"
                            @selected(old('island_id', $history->island_id) == $island->id)>
                        {{ $island->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Suku --}}
        <div>
            <label class="block text-xs font-medium mb-1">Suku</label>
            <select name="tribe" id="tribeSelect"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                @foreach($tribes as $t)
                    <option value="{{ $t }}" @selected(old('tribe', $history->tribe) === $t)>
                        {{ $t }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ================= HEADER UTAMA (TRIBE PAGES) ================= --}}
        <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4 space-y-4">
            <div>
                <div class="text-sm font-semibold">Header Sejarah (User)</div>
                <div class="text-xs text-slate-500">
                    Judul & deskripsi besar sebelum timeline zigzag.
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Judul Besar</label>
                <input type="text"
                       name="hero_title"
                       id="heroTitle"
                       value="{{ old('hero_title', $tribePage->hero_title ?? '') }}"
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Deskripsi Besar</label>
                <textarea name="hero_description"
                          id="heroDescription"
                          rows="3"
                          class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">{{ old('hero_description', $tribePage->hero_description ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Hero Image (opsional)</label>
                <input type="text"
                       name="hero_image"
                       id="heroImage"
                       value="{{ old('hero_image', $tribePage->hero_image ?? '') }}"
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
            </div>
        </div>

        {{-- ================= TIMELINE ITEM ================= --}}
        <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4 space-y-4">
            <div class="text-sm font-semibold">Item Timeline</div>

            <div>
                <label class="block text-xs font-medium mb-1">Tahun / Waktu</label>
                <input type="text" name="year_label"
                       value="{{ old('year_label', $history->year_label) }}"
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Judul</label>
                <input type="text" name="title"
                       value="{{ old('title', $history->title) }}"
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Isi</label>
                <textarea name="content" rows="6"
                          class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">{{ old('content', $history->content) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Link (opsional)</label>
                <input type="url" name="more_link"
                       value="{{ old('more_link', $history->more_link) }}"
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Urutan</label>
                <input type="number" name="order"
                       value="{{ old('order', $history->order) }}"
                       class="w-full max-w-[120px] rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2.5 rounded-full bg-orange-500 hover:bg-orange-600 text-sm text-white">
                Update
            </button>
            <a href="{{ route('admin.histories.index') }}"
               class="text-sm text-slate-400 hover:text-slate-200">
                Batal
            </a>
        </div>
    </form>
</div>

{{-- AUTO LOAD HEADER VIA ENDPOINT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const island = document.getElementById('islandSelect');
    const tribe  = document.getElementById('tribeSelect');

    const title  = document.getElementById('heroTitle');
    const desc   = document.getElementById('heroDescription');
    const img    = document.getElementById('heroImage');

    async function loadHero() {
        if (!island.value || !tribe.value) return;

        const url = `{{ route('admin.tribe-pages.lookup') }}?island_id=${island.value}&tribe_key=${tribe.value}`;

        const res = await fetch(url);
        if (!res.ok) return;

        const data = await res.json();
        if (!data) return;

        title.value = data.hero_title ?? '';
        desc.value  = data.hero_description ?? '';
        img.value   = data.hero_image ?? '';
    }

    island.addEventListener('change', loadHero);
    tribe.addEventListener('change', loadHero);
});
</script>
@endsection
