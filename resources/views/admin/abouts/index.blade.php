{{-- resources/views/admin/abouts/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'About Suku')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6 space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">About Suku (Pulau + Suku)</h1>
            <p class="text-sm text-slate-400 mt-1">
                Header About disimpan <strong>sekali</strong> per Pulau + Suku, tanpa gambar.
                Item About bisa <strong>banyak</strong>, gambar item <strong>upload manual</strong>, points opsional.
            </p>
        </div>

        @if(session('success'))
            <div class="px-4 py-2 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-200 text-sm">
                {{ session('success') }}
            </div>
        @endif
    </div>

    {{-- PICKER PULAU + SUKU --}}
    <form method="GET" action="{{ route('admin.abouts.index') }}"
          class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div>
                <label class="block text-xs font-medium mb-1">Pulau</label>
                <select name="island_id" id="islandSelect"
                        class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                    <option value="">Pilih pulau...</option>
                    @foreach($islands as $island)
                        <option value="{{ $island->id }}"
                                data-slug="{{ $island->slug }}"
                                @selected((string)request('island_id') === (string)$island->id)>
                            {{ $island->name }}
                        </option>
                    @endforeach
                </select>
                @error('island_id') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Suku</label>
                <select name="tribe_key" id="tribeSelect"
                        class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                    <option value="">Pilih suku...</option>
                </select>
                <p class="text-[11px] text-slate-500 mt-1">
                    Daftar suku mengikuti config <strong>tribes.php</strong>.
                </p>
                @error('tribe_key') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                        class="px-5 py-2.5 rounded-full bg-orange-500 hover:bg-orange-600 text-sm text-white shadow-lg shadow-orange-500/40">
                    Load
                </button>

                <a href="{{ route('admin.abouts.index') }}"
                   class="px-5 py-2.5 rounded-full bg-slate-800 hover:bg-slate-700 text-sm text-slate-200">
                    Reset
                </a>
            </div>

        </div>
    </form>

    @php
        $ready = !empty($selectedIslandId) && !empty($selectedTribeKey);
    @endphp

    {{-- HEADER ABOUT (TANPA GAMBAR) --}}
    <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4 space-y-4">
        <div>
            <div class="text-sm font-semibold">Header About (User)</div>
            <div class="text-xs text-slate-500">
                Header hanya: <strong>Label kecil</strong>, <strong>Judul besar</strong>, <strong>Deskripsi besar</strong>.
                Tanpa gambar. Link opsional untuk tombol <strong>"Selengkapnya"</strong>.
            </div>
        </div>

        <form action="{{ route('admin.abouts.page.save') }}" method="POST" class="space-y-4">
            @csrf

            <input type="hidden" name="island_id" id="pageIslandId" value="{{ $selectedIslandId ?? '' }}">
            <input type="hidden" name="tribe_key" id="pageTribeKey" value="{{ $selectedTribeKey ?? '' }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1">Label Kecil (opsional)</label>
                    <input type="text" name="label_small" id="labelSmall"
                           value="{{ old('label_small', $aboutPage->label_small ?? '') }}"
                           placeholder="Contoh: MENGENAL ACEH"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                    @error('label_small') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1">Judul Besar (opsional)</label>
                    <input type="text" name="hero_title" id="heroTitle"
                           value="{{ old('hero_title', $aboutPage->hero_title ?? '') }}"
                           placeholder="Contoh: Apa itu Aceh?"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                    @error('hero_title') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Deskripsi Besar (opsional)</label>
                <textarea name="hero_description" id="heroDescription" rows="4"
                          placeholder="Paragraf besar untuk header about..."
                          class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">{{ old('hero_description', $aboutPage->hero_description ?? '') }}</textarea>
                @error('hero_description') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Link Selengkapnya (opsional)</label>
                <input type="url" name="more_link" id="pageMoreLink"
                       value="{{ old('more_link', $aboutPage->more_link ?? '') }}"
                       placeholder="https://contoh.com/artikel-lengkap"
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                <p class="text-[11px] text-slate-500 mt-1">
                    Jika kosong, tombol <strong>"Selengkapnya"</strong> tidak tampil di user.
                </p>
                @error('more_link') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-1">
                <button type="submit"
                        class="px-5 py-2.5 rounded-full bg-orange-500 hover:bg-orange-600 text-sm text-white shadow-lg shadow-orange-500/40 disabled:opacity-50"
                        @disabled(!$ready)>
                    Simpan Header
                </button>

                @unless($ready)
                    <span class="text-xs text-slate-500">
                        Pilih Pulau + Suku dulu lalu klik <strong>Load</strong>.
                    </span>
                @endunless
            </div>
        </form>
    </div>

    {{-- TAMBAH ITEM ABOUT --}}
    <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4 space-y-4">
        <div>
            <div class="text-sm font-semibold">Tambah Item About</div>
            <div class="text-xs text-slate-500">
                Title opsional, points opsional (isi per baris), gambar opsional (UPLOAD).
                Link opsional untuk tombol "Selengkapnya".
            </div>
        </div>

        <form action="{{ route('admin.abouts.item.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <input type="hidden" name="island_id" id="itemIslandId" value="{{ $selectedIslandId ?? '' }}">
            <input type="hidden" name="tribe_key" id="itemTribeKey" value="{{ $selectedTribeKey ?? '' }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium mb-1">Title (opsional)</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           placeholder="Contoh: Aceh Kampung Banjir"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                    @error('title') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1">Urutan (opsional)</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                           class="w-full max-w-[180px] rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                    @error('sort_order') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Deskripsi (wajib)</label>
                <textarea name="description" rows="4"
                          class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm"
                          placeholder="Isi deskripsi item...">{{ old('description') }}</textarea>
                @error('description') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Points (opsional) — 1 baris = 1 poin</label>
                <textarea name="points" rows="4"
                          class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm"
                          placeholder="Contoh:
Enam varian sesuai sub-suku
Berakar dari aksara India Kuno
Kini tersedia dalam bentuk digital">{{ old('points') }}</textarea>
                <p class="text-[11px] text-slate-500 mt-1">
                    Jika kosong, points tidak tampil.
                </p>
                @error('points') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1">Gambar (opsional) — Upload</label>
                    <input type="file" name="image" accept="image/png,image/jpeg,image/webp"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                    <p class="text-[11px] text-slate-500 mt-1">
                        Upload JPG/PNG/WEBP (max 4MB). Kalau tidak upload, layout user otomatis menyesuaikan.
                    </p>
                    @error('image') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1">Link Selengkapnya (opsional)</label>
                    <input type="url" name="more_link" value="{{ old('more_link') }}"
                           placeholder="https://contoh.com/..."
                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                    @error('more_link') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 pt-1">
                <button type="submit"
                        class="px-5 py-2.5 rounded-full bg-orange-500 hover:bg-orange-600 text-sm text-white shadow-lg shadow-orange-500/40 disabled:opacity-50"
                        @disabled(!$ready)>
                    Tambah Item
                </button>

                @unless($ready)
                    <span class="text-xs text-slate-500">
                        Pilih Pulau + Suku dulu lalu klik <strong>Load</strong>.
                    </span>
                @endunless
            </div>
        </form>
    </div>

    {{-- LIST ITEM (EDIT / DELETE) --}}
    <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4 space-y-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="text-sm font-semibold">Daftar Item About</div>
                <div class="text-xs text-slate-500">Edit item. Gambar bisa diganti (upload) atau dihapus.</div>
            </div>
            <div class="text-xs text-slate-400">
                Total: <span class="text-slate-200 font-semibold">{{ $aboutItems->count() }}</span>
            </div>
        </div>

        @if(!$ready)
            <div class="text-sm text-slate-400">
                Pilih Pulau + Suku lalu klik <strong>Load</strong>.
            </div>
        @elseif($aboutItems->isEmpty())
            <div class="text-sm text-slate-400">
                Belum ada item About untuk Pulau+Suku ini.
            </div>
        @else
            <div class="space-y-4">
                @foreach($aboutItems as $it)
                    <div class="rounded-2xl border border-slate-800 bg-slate-950/30 p-4 space-y-3">
                        <div class="text-xs text-slate-500">
                            ID: <span class="text-slate-200">{{ $it->id }}</span>
                            • order: <span class="text-slate-200">{{ $it->sort_order }}</span>
                        </div>

                        <form action="{{ route('admin.abouts.item.update', $it) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            @method('PATCH')

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] text-slate-400 mb-1">Title (opsional)</label>
                                    <input type="text" name="title" value="{{ old('title', $it->title) }}"
                                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                                </div>

                                <div>
                                    <label class="block text-[11px] text-slate-400 mb-1">Urutan</label>
                                    <input type="number" name="sort_order" value="{{ old('sort_order', $it->sort_order) }}"
                                           class="w-full max-w-[180px] rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1">Deskripsi (wajib)</label>
                                <textarea name="description" rows="3"
                                          class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">{{ old('description', $it->description) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1">Points (opsional) — 1 baris = 1 poin</label>
                                <textarea name="points" rows="4"
                                          class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">{{ old('points', $it->points) }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] text-slate-400 mb-1">Ganti Gambar (opsional) — Upload</label>
                                    <input type="file" name="image" accept="image/png,image/jpeg,image/webp"
                                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">

                                    @if($it->image)
                                        <div class="mt-2 flex items-center gap-3">
                                            <img src="{{ $it->image }}" alt="img" class="w-24 h-16 object-cover rounded-lg border border-slate-700">
                                            <label class="text-xs text-slate-300 inline-flex items-center gap-2">
                                                <input type="checkbox" name="remove_image" value="1">
                                                Hapus gambar ini
                                            </label>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-[11px] text-slate-400 mb-1">Link Selengkapnya (opsional)</label>
                                    <input type="url" name="more_link" value="{{ old('more_link', $it->more_link) }}"
                                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                                </div>
                            </div>

                            <div class="flex items-center gap-3 pt-2">
                                <button type="submit"
                                        class="px-4 py-2 rounded-full bg-orange-500 hover:bg-orange-600 text-sm text-white">
                                    Update
                                </button>
                            </div>
                        </form>

                        <form action="{{ route('admin.abouts.item.destroy', $it) }}" method="POST"
                              onsubmit="return confirm('Hapus item ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-4 py-2 rounded-full bg-red-500/20 hover:bg-red-500/30 text-sm text-red-200 border border-red-500/30">
                                Hapus Item
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

{{-- =============================
   SCRIPT: tribes picker + auto-load header
============================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tribesConfig = @json($tribesConfig ?? []);
    const islandSelect = document.getElementById('islandSelect');
    const tribeSelect  = document.getElementById('tribeSelect');

    const pageIslandId = document.getElementById('pageIslandId');
    const pageTribeKey = document.getElementById('pageTribeKey');

    const itemIslandId = document.getElementById('itemIslandId');
    const itemTribeKey = document.getElementById('itemTribeKey');

    const labelSmall    = document.getElementById('labelSmall');
    const heroTitle     = document.getElementById('heroTitle');
    const heroDesc      = document.getElementById('heroDescription');
    const pageMoreLink  = document.getElementById('pageMoreLink');

    const selectedTribeFromQuery = @json(request('tribe_key'));

    function getSelectedIslandSlug() {
        const opt = islandSelect.options[islandSelect.selectedIndex];
        return opt ? (opt.dataset.slug || '') : '';
    }

    function fillTribes(selectedTribe) {
        const slug = getSelectedIslandSlug();
        const tribes = slug && tribesConfig[slug] ? tribesConfig[slug] : [];

        tribeSelect.innerHTML = '<option value="">Pilih suku...</option>';

        tribes.forEach(function (t) {
            const o = document.createElement('option');
            o.value = t;
            o.textContent = t;
            if (selectedTribe && selectedTribe === t) o.selected = true;
            tribeSelect.appendChild(o);
        });

        tribeSelect.disabled = tribes.length === 0;
    }

    async function loadHeader() {
        const islandId = islandSelect.value || '';
        const tribeKey = tribeSelect.value || '';

        pageIslandId.value = islandId;
        pageTribeKey.value = tribeKey;

        itemIslandId.value = islandId;
        itemTribeKey.value = tribeKey;

        if (!islandId || !tribeKey) return;

        const url = `{{ url('/admin/about-pages/lookup') }}?island_id=${encodeURIComponent(islandId)}&tribe_key=${encodeURIComponent(tribeKey)}`;

        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;

            const data = await res.json();
            if (!data) {
                // kosongkan agar admin tahu ini belum ada
                labelSmall.value = '';
                heroTitle.value  = '';
                heroDesc.value   = '';
                pageMoreLink.value = '';
                return;
            }

            labelSmall.value = data.label_small ?? '';
            heroTitle.value  = data.hero_title ?? '';
            heroDesc.value   = data.hero_description ?? '';
            pageMoreLink.value = data.more_link ?? '';
        } catch (e) {
            // silent
        }
    }

    // init fill tribe dropdown
    fillTribes(selectedTribeFromQuery || '');

    // kalau sudah ada pilihan, set hidden input
    pageIslandId.value = islandSelect.value || '';
    itemIslandId.value = islandSelect.value || '';

    pageTribeKey.value = tribeSelect.value || '';
    itemTribeKey.value = tribeSelect.value || '';

    // init load header kalau query lengkap
    if ((islandSelect.value || '') && (tribeSelect.value || '')) {
        loadHeader();
    }

    islandSelect.addEventListener('change', function () {
        fillTribes('');
        // reset hidden
        pageIslandId.value = islandSelect.value || '';
        itemIslandId.value = islandSelect.value || '';

        pageTribeKey.value = '';
        itemTribeKey.value = '';
    });

    tribeSelect.addEventListener('change', function () {
        loadHeader();
    });
});
</script>
@endsection
