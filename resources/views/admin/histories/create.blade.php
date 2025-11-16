{{-- resources/views/admin/histories/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Tambah History')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-4">Tambah History Pulau &amp; Suku</h1>

    <form action="{{ route('admin.histories.store') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Pulau --}}
        <div>
            <label class="block text-xs font-medium mb-1">Pulau</label>
            <select name="island_id" id="islandSelect"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                <option value="">Pilih pulau...</option>
                @foreach($islands as $island)
                    <option value="{{ $island->id }}"
                            data-slug="{{ $island->slug }}"
                            @selected(old('island_id', $selectedIslandId ?? null) == $island->id)>
                        {{ $island->name }}
                    </option>
                @endforeach
            </select>
            @error('island_id')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Suku (mengikuti pulau yang dipilih) --}}
        <div>
            <label class="block text-xs font-medium mb-1">Suku</label>
            <select name="tribe" id="tribeSelect"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                <option value="">Pilih suku...</option>
                @foreach($tribes as $t)
                    <option value="{{ $t }}" @selected(old('tribe') === $t)>{{ $t }}</option>
                @endforeach
            </select>
            <p class="text-[11px] text-slate-500 mt-1">
                Daftar suku akan menyesuaikan <strong>pulau</strong> yang dipilih
                (Jawa, Sumatera, Kalimantan, Sulawesi, Sunda Kecil, Papua &amp; Maluku).
            </p>
            @error('tribe')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tahun / Waktu --}}
        <div>
            <label class="block text-xs font-medium mb-1">Tahun / Waktu</label>
            <input type="text" name="year_label" value="{{ old('year_label') }}"
                   placeholder="misal: Abad ke-14, 1900–1945"
                   class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
            @error('year_label')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Judul --}}
        <div>
            <label class="block text-xs font-medium mb-1">Judul</label>
            <input type="text" name="title" value="{{ old('title') }}"
                   class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
            @error('title')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Isi --}}
        <div>
            <label class="block text-xs font-medium mb-1">Isi History</label>
            <textarea name="content" rows="6"
                      class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">{{ old('content') }}</textarea>
            @error('content')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Link opsional --}}
        <div>
            <label class="block text-xs font-medium mb-1">
                Link Selengkapnya (opsional)
            </label>
            <input type="url" name="more_link" value="{{ old('more_link') }}"
                   placeholder="https://contoh.com/artikel-lengkap"
                   class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
            <p class="text-[11px] text-slate-500 mt-1">
                Jika dikosongkan, tombol <strong>"Lihat selengkapnya"</strong> tidak akan muncul di tampilan user.
            </p>
            @error('more_link')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Urutan timeline --}}
        <div>
            <label class="block text-xs font-medium mb-1">Urutan (opsional)</label>
            <input type="number" name="order" value="{{ old('order', 0) }}"
                   class="w-full max-w-[120px] rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
            @error('order')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2.5 rounded-full bg-orange-500 hover:bg-orange-600 text-sm text-white shadow-lg shadow-orange-500/40">
                Simpan
            </button>
            <a href="{{ route('admin.histories.index') }}"
               class="text-sm text-slate-400 hover:text-slate-200">
                Batal
            </a>
        </div>
    </form>
</div>

{{-- SCRIPT: update daftar suku setiap kali pulau diganti --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // mapping pulau -> daftar suku dari config/tribes.php
        const tribesConfig = @json($tribesConfig ?? []);

        const islandSelect = document.getElementById('islandSelect');
        const tribeSelect  = document.getElementById('tribeSelect');
        const oldTribe     = @json(old('tribe'));

        function fillTribes(selectedTribe) {
            const opt  = islandSelect.options[islandSelect.selectedIndex];
            const slug = opt ? opt.dataset.slug : null;
            const tribes = slug && tribesConfig[slug] ? tribesConfig[slug] : [];

            // kosongkan option
            tribeSelect.innerHTML = '<option value=\"\">Pilih suku...</option>';

            tribes.forEach(function (t) {
                const o = document.createElement('option');
                o.value = t;
                o.textContent = t;

                if (selectedTribe && selectedTribe === t) {
                    o.selected = true;
                }

                tribeSelect.appendChild(o);
            });

            tribeSelect.disabled = tribes.length === 0;
        }

        // pertama kali load (pakai old('tribe') kalau ada)
        fillTribes(oldTribe || '');

        // ketika pulau diganti, reset pilihan suku
        islandSelect.addEventListener('change', function () {
            fillTribes('');
        });
    });
</script>
@endsection
