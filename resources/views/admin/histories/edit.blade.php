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
                <option value="">Pilih pulau...</option>
                @foreach($islands as $island)
                    <option value="{{ $island->id }}"
                            data-slug="{{ $island->slug }}"
                            @selected(old('island_id', $history->island_id) == $island->id)>
                        {{ $island->name }}
                    </option>
                @endforeach
            </select>
            @error('island_id')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Suku --}}
        <div>
            <label class="block text-xs font-medium mb-1">Suku (Sumatera)</label>
            <select name="tribe" id="tribeSelect"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                <option value="">Pilih suku...</option>
                @foreach($sumateraTribes as $t)
                    <option value="{{ $t }}" @selected(old('tribe', $history->tribe) === $t)>{{ $t }}</option>
                @endforeach
            </select>
            @error('tribe')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tahun / Waktu --}}
        <div>
            <label class="block text-xs font-medium mb-1">Tahun / Waktu</label>
            <input type="text" name="year_label"
                   value="{{ old('year_label', $history->year_label) }}"
                   class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
            @error('year_label')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Judul --}}
        <div>
            <label class="block text-xs font-medium mb-1">Judul</label>
            <input type="text" name="title"
                   value="{{ old('title', $history->title) }}"
                   class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
            @error('title')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Isi --}}
        <div>
            <label class="block text-xs font-medium mb-1">Isi History</label>
            <textarea name="content" rows="6"
                      class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">{{ old('content', $history->content) }}</textarea>
            @error('content')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Link --}}
        <div>
            <label class="block text-xs font-medium mb-1">Link Selengkapnya (opsional)</label>
            <input type="url" name="more_link"
                   value="{{ old('more_link', $history->more_link) }}"
                   class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
            @error('more_link')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Urutan --}}
        <div>
            <label class="block text-xs font-medium mb-1">Urutan</label>
            <input type="number" name="order"
                   value="{{ old('order', $history->order) }}"
                   class="w-full max-w-[120px] rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
            @error('order')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2.5 rounded-full bg-orange-500 hover:bg-orange-600 text-sm text-white shadow-lg shadow-orange-500/40">
                Update
            </button>
            <a href="{{ route('admin.histories.index') }}"
               class="text-sm text-slate-400 hover:text-slate-200">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
    (function () {
        const islandSelect = document.getElementById('islandSelect');
        const tribeSelect  = document.getElementById('tribeSelect');

        function updateTribeSelect() {
            const selected = islandSelect.options[islandSelect.selectedIndex];
            const slug = selected ? selected.getAttribute('data-slug') : null;

            if (slug === 'sumatera') {
                tribeSelect.disabled = false;
            } else {
                tribeSelect.disabled = true;
            }
        }

        islandSelect.addEventListener('change', updateTribeSelect);
        updateTribeSelect();
    })();
</script>
@endsection
