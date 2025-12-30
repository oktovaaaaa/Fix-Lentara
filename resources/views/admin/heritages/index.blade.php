@extends('layouts.admin')

@section('title', 'Warisan')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6 space-y-6">

    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-semibold">Warisan (Per Pulau & Suku)</h1>
            <p class="text-sm text-slate-400 mt-1">
                Pilih pulau lalu pilih suku. Dalam 1 halaman ini kamu bisa CRUD 3 kategori warisan + header (title & deskripsi).
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-emerald-200 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-200 text-sm">
            <div class="font-semibold mb-1">Ada error:</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FILTER PULAU + SUKU --}}
    <form method="GET" action="{{ route('admin.heritages.index') }}"
          class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium mb-1 text-slate-300">Pilih Pulau</label>
                <select name="island_id" class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2">
                    <option value="">-- pilih --</option>
                    @foreach($islands as $island)
                        <option value="{{ $island->id }}" @selected($selectedIsland && $selectedIsland->id === $island->id)>
                            {{ $island->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium mb-1 text-slate-300">Pilih Suku</label>
                <select name="tribe" class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2" @disabled(!$selectedIsland)>
                    @if(!$selectedIsland)
                        <option value="">Pilih pulau dulu</option>
                    @else
                        @foreach($tribes as $t)
                            <option value="{{ $t }}" @selected($selectedTribeKey === $t)>{{ $t }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button class="px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-500 text-white font-semibold">
                    Tampilkan
                </button>

                <a href="{{ route('admin.heritages.index') }}"
                   class="px-4 py-2 rounded-lg border border-slate-700 text-slate-200 hover:bg-slate-900">
                    Reset
                </a>
            </div>
        </div>
    </form>

    @if($selectedIsland && $selectedTribeKey)

        {{-- HEADER SUKU: TITLE BESAR + DESKRIPSI BESAR --}}
        <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-5 space-y-4">
            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div>
                    <h2 class="text-lg font-semibold">Header Suku</h2>
                    <p class="text-xs text-slate-400">
                        Ini untuk title besar + deskripsi besar per suku (seperti di contoh gambar hero).
                    </p>
                </div>
                <div class="text-xs text-slate-400">
                    Pulau: <span class="text-slate-200 font-semibold">{{ $selectedIsland->name }}</span> —
                    Suku: <span class="text-slate-200 font-semibold">{{ $selectedTribeKey }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.heritages.page.save') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input type="hidden" name="island_id" value="{{ $selectedIsland->id }}">
                <input type="hidden" name="tribe_key" value="{{ $selectedTribeKey }}">

                <div>
                    <label class="block text-xs font-medium mb-1 text-slate-300">Title Besar</label>
                    <input type="text" name="hero_title"
                           value="{{ old('hero_title', $tribePage->hero_title ?? '') }}"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2"
                           placeholder="Contoh: Warisan Suku Batak">
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1 text-slate-300">Gambar Header (opsional)</label>
                    <input type="file" name="hero_image"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2">
                    @if(!empty($tribePage?->hero_image))
                        <p class="text-xs text-slate-400 mt-2">
                            Saat ini:
                            <a class="underline" href="{{ asset('storage/'.$tribePage->hero_image) }}" target="_blank">lihat</a>
                        </p>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium mb-1 text-slate-300">Deskripsi Besar</label>
                    <textarea name="hero_description" rows="3"
                              class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2"
                              placeholder="Deskripsi singkat yang tampil di bagian hero / section warisan...">{{ old('hero_description', $tribePage->hero_description ?? '') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <button class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold">
                        Simpan Header
                    </button>
                </div>
            </form>
        </div>

        {{-- 3 CRUD DALAM 1 HALAMAN --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @foreach($categoryLabels as $catKey => $catLabel)
                <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4 space-y-4">
                    <div>
                        <h3 class="text-base font-semibold">{{ $catLabel }}</h3>
                        <p class="text-xs text-slate-400">Tambah / edit / hapus item untuk kategori ini.</p>
                    </div>

                    {{-- FORM TAMBAH ITEM --}}
                    <form method="POST" action="{{ route('admin.heritages.item.store') }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <input type="hidden" name="island_id" value="{{ $selectedIsland->id }}">
                        <input type="hidden" name="tribe_key" value="{{ $selectedTribeKey }}">
                        <input type="hidden" name="category" value="{{ $catKey }}">

                        <div>
                            <label class="block text-xs font-medium mb-1 text-slate-300">Judul</label>
                            <input type="text" name="title"
                                   class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2"
                                   placeholder="Contoh: Ulos / Rumah Bolon / Sasando ...">
                        </div>

                        <div>
                            <label class="block text-xs font-medium mb-1 text-slate-300">Deskripsi (opsional)</label>
                            <textarea name="description" rows="2"
                                      class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2"
                                      placeholder="Deskripsi singkat..."></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-medium mb-1 text-slate-300">Gambar (opsional)</label>
                            <input type="file" name="image"
                                   class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2">
                        </div>

                        <div>
                            <label class="block text-xs font-medium mb-1 text-slate-300">Urutan (opsional)</label>
                            <input type="number" name="sort_order" min="0" value="0"
                                   class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2">
                        </div>

                        <button class="w-full px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-500 text-white font-semibold">
                            + Tambah Item
                        </button>
                    </form>

                    {{-- LIST ITEM --}}
                    <div class="space-y-3">
                        @php
                            $items = $itemsByCategory[$catKey] ?? collect();
                        @endphp

                        @if($items->count() === 0)
                            <div class="text-xs text-slate-400 border border-slate-800 rounded-lg p-3">
                                Belum ada item di kategori ini.
                            </div>
                        @else
                            @foreach($items as $item)
                                <div class="border border-slate-800 rounded-xl p-3 space-y-2">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-100 text-sm break-words">
                                                {{ $item->title }}
                                            </div>
                                            <div class="text-[11px] text-slate-400">
                                                sort: {{ $item->sort_order }}
                                            </div>
                                        </div>

                                        <form method="POST" action="{{ route('admin.heritages.item.destroy', $item) }}"
                                              onsubmit="return confirm('Hapus item ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs px-2 py-1 rounded-lg bg-red-600/80 hover:bg-red-600 text-white">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>

                                    @if($item->image_path)
                                        <a href="{{ asset('storage/'.$item->image_path) }}" target="_blank"
                                           class="text-xs underline text-slate-300">
                                            lihat gambar
                                        </a>
                                    @endif

                                    @if($item->description)
                                        <p class="text-xs text-slate-300 leading-relaxed">
                                            {{ $item->description }}
                                        </p>
                                    @endif

                                    {{-- FORM EDIT --}}
                                    <details class="pt-1">
                                        <summary class="cursor-pointer text-xs text-slate-300 hover:text-white">
                                            Edit item
                                        </summary>

                                        <form method="POST" action="{{ route('admin.heritages.item.update', $item) }}"
                                              enctype="multipart/form-data" class="mt-3 space-y-2">
                                            @csrf
                                            @method('PATCH')

                                            <div>
                                                <label class="block text-xs font-medium mb-1 text-slate-300">Judul</label>
                                                <input type="text" name="title" value="{{ $item->title }}"
                                                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2">
                                            </div>

                                            <div>
                                                <label class="block text-xs font-medium mb-1 text-slate-300">Deskripsi</label>
                                                <textarea name="description" rows="2"
                                                          class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2">{{ $item->description }}</textarea>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-medium mb-1 text-slate-300">Ganti gambar (opsional)</label>
                                                <input type="file" name="image"
                                                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2">
                                            </div>

                                            <div>
                                                <label class="block text-xs font-medium mb-1 text-slate-300">Urutan</label>
                                                <input type="number" name="sort_order" min="0" value="{{ $item->sort_order }}"
                                                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2">
                                            </div>

                                            <button class="w-full px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold">
                                                Simpan Perubahan
                                            </button>
                                        </form>
                                    </details>
                                </div>
                            @endforeach
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

    @else
        <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-6 text-slate-300">
            Pilih pulau & suku dulu untuk mulai mengelola warisan.
        </div>
    @endif

</div>
@endsection
