{{-- resources/views/admin/destinations/create.blade.php (REPLACE FULL) --}}
@extends('layouts.admin')

@section('title', 'Admin - Tambah Destinasi')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 text-slate-100">
    <h1 class="text-2xl font-bold mb-4">Tambah Destinasi</h1>

    @if($errors->any())
        <div class="mb-4 rounded bg-red-600/70 px-3 py-2 text-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.destinations.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="rounded-xl bg-slate-900/60 border border-slate-700 p-5 space-y-5">
        @csrf

        {{-- Pulau & Suku --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-slate-400 mb-1">Pulau</label>
                <select name="island_id" class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
                    @foreach($islands as $isl)
                        <option value="{{ $isl->id }}" {{ optional($selectedIsland)->id === $isl->id ? 'selected' : '' }}>
                            {{ $isl->name }} ({{ $isl->slug }})
                        </option>
                    @endforeach
                </select>
                <div class="text-xs text-slate-400 mt-1">
                    Suku diambil dari <strong>config/tribes.php</strong> berdasarkan pulau.
                </div>
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">Suku</label>
                <select name="tribe_key" class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
                    @foreach($tribes as $t)
                        <option value="{{ $t }}" {{ $selectedTribe === $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Nama --}}
        <div>
            <label class="block text-xs text-slate-400 mb-1">Nama Destinasi</label>
            <input type="text" name="name"
                   value="{{ old('name') }}"
                   placeholder="Contoh: Masjid Raya Baiturrahman"
                   class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
        </div>

        {{-- Lokasi --}}
        <div>
            <label class="block text-xs text-slate-400 mb-1">Lokasi</label>
            <input type="text" name="location"
                   value="{{ old('location') }}"
                   placeholder="Contoh: Banda Aceh, Aceh"
                   class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block text-xs text-slate-400 mb-1">Deskripsi</label>
            <textarea name="description" rows="5"
                      placeholder="Deskripsi singkat destinasi..."
                      class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">{{ old('description') }}</textarea>
        </div>

        {{-- Gambar --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-slate-400 mb-1">Gambar (URL) – opsional</label>
                <input type="text" name="image_url"
                       value="{{ old('image_url') }}"
                       placeholder="https://..."
                       class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
                <div class="text-xs text-slate-400 mt-1">
                    Jika upload diisi, upload akan diprioritaskan.
                </div>
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">Gambar (Upload) – opsional</label>
                <input type="file" name="image_file"
                       class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
                <div class="text-xs text-slate-400 mt-1">
                    Maks. 4MB (jpg / png / webp).
                </div>
            </div>
        </div>

        {{-- =======================
             360° GOOGLE MAPS
        ======================== --}}
        <div class="border-t border-slate-700 pt-4 space-y-4">
            <div class="flex items-center gap-2 text-orange-400 font-semibold text-sm">
                <i class="bx bx-panorama text-lg"></i>
                Konten 360° (Google Maps)
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">
                    Embed Google Maps (iframe)
                </label>
                <textarea name="pano_embed_url" rows="3"
                          placeholder="https://www.google.com/maps/embed?pb=..."
                          class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">{{ old('pano_embed_url') }}</textarea>
                <div class="text-xs text-slate-500 mt-1">
                    Digunakan untuk tampilan 360 layar penuh di halaman publik.
                </div>
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">
                    Link Google Maps (opsional)
                </label>
                <input type="text" name="pano_maps_url"
                       value="{{ old('pano_maps_url') }}"
                       placeholder="https://maps.app.goo.gl/xxxx"
                       class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">
                    Label 360° (opsional)
                </label>
                <input type="text" name="pano_label"
                       value="{{ old('pano_label') }}"
                       placeholder="Contoh: 360° Bukit Holbung"
                       class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
            </div>

            <div class="text-xs text-slate-500">
                Jika embed kosong, halaman publik akan menggunakan tampilan popup biasa.
            </div>
        </div>

        {{-- Rating / Order / Aktif --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs text-slate-400 mb-1">Rating (0 – 5)</label>
                <input type="number" name="rating" step="0.1" min="0" max="5"
                       value="{{ old('rating', 0) }}"
                       class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">Sort Order</label>
                <input type="number" name="sort_order" min="0"
                       value="{{ old('sort_order', 0) }}"
                       class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
            </div>

            <div class="flex items-center gap-2 pt-6">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4">
                <label class="text-sm text-slate-200">Aktif</label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <button type="submit"
                    class="bg-orange-600 hover:bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                Simpan
            </button>

            <a href="{{ route('admin.destinations.index', ['island' => optional($selectedIsland)->slug, 'tribe' => $selectedTribe]) }}"
               class="bg-slate-800 hover:bg-slate-700 text-slate-100 px-4 py-2 rounded-lg text-sm font-semibold">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
