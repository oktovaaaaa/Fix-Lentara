@extends('layouts.admin')

@section('title', 'Admin - Edit Destinasi')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 text-slate-100">
    <h1 class="text-2xl font-bold mb-4">Edit Destinasi</h1>

    @if($errors->any())
        <div class="mb-4 rounded bg-red-600/70 px-3 py-2 text-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.destinations.update', $destination) }}" method="POST" enctype="multipart/form-data"
          class="rounded-xl bg-slate-900/60 border border-slate-700 p-5 space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-slate-400 mb-1">Pulau</label>
                <select name="island_id" class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
                    @foreach($islands as $isl)
                        <option value="{{ $isl->id }}" {{ $destination->island_id === $isl->id ? 'selected' : '' }}>
                            {{ $isl->name }} ({{ $isl->slug }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">Suku</label>
                <select name="tribe_key" class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
                    @foreach($tribes as $t)
                        <option value="{{ $t }}" {{ $destination->tribe_key === $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs text-slate-400 mb-1">Nama Destinasi</label>
            <input type="text" name="name" value="{{ old('name', $destination->name) }}"
                   class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-xs text-slate-400 mb-1">Lokasi</label>
            <input type="text" name="location" value="{{ old('location', $destination->location) }}"
                   class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-xs text-slate-400 mb-1">Deskripsi</label>
            <textarea name="description" rows="5"
                      class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">{{ old('description', $destination->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-slate-400 mb-1">Gambar (URL) - opsional</label>
                <input type="text" name="image_url" value="{{ old('image_url', $destination->image_url) }}"
                       class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">Gambar (Upload) - opsional</label>
                <input type="file" name="image_file"
                       class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">

                @if($destination->image_path)
                    <div class="mt-2 text-xs text-slate-300">
                        Upload aktif: <span class="text-orange-400">{{ $destination->image_path }}</span>
                        <label class="ml-3 inline-flex items-center gap-2">
                            <input type="checkbox" name="remove_upload" value="1" class="w-4 h-4">
                            Hapus upload ini
                        </label>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs text-slate-400 mb-1">Rating (0 - 5)</label>
                <input type="number" name="rating" step="0.1" min="0" max="5"
                       value="{{ old('rating', $destination->rating) }}"
                       class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">Sort Order</label>
                <input type="number" name="sort_order" min="0"
                       value="{{ old('sort_order', $destination->sort_order) }}"
                       class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm">
            </div>

            <div class="flex items-center gap-2 pt-6">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $destination->is_active) ? 'checked' : '' }}
                       class="w-4 h-4">
                <label class="text-sm text-slate-200">Aktif</label>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-orange-600 hover:bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                Update
            </button>

            <a href="{{ route('admin.destinations.index') }}"
               class="bg-slate-800 hover:bg-slate-700 text-slate-100 px-4 py-2 rounded-lg text-sm font-semibold">
                Kembali
            </a>
        </div>
    </form>
</div>
@endsection
