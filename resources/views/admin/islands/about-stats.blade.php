{{-- resources/views/admin/about-islands-stats.blade.php --}}
@extends('layouts.admin')

@section('title', 'Admin - About Pulau + Statistik')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4 text-slate-100">
    <h1 class="text-2xl font-bold mb-4">About Pulau + Statistik</h1>

    @if(session('status'))
        <div class="mb-4 rounded bg-emerald-600/80 px-3 py-2 text-sm">
            {{ session('status') }}
        </div>
    @endif

    {{-- PICKER PULAU --}}
    <div class="mb-6">
        <label class="block text-xs text-slate-400 mb-1">Pilih Pulau</label>
        <form method="GET" action="{{ route('admin.about_stats.index') }}">
            <select
                name="island"
                onchange="this.form.submit()"
                class="bg-slate-900 border border-slate-700 rounded px-3 py-2 text-sm"
            >
                @foreach($islands as $island)
                    <option value="{{ $island->slug }}" {{ $island->id === $activeIsland->id ? 'selected' : '' }}>
                        {{ $island->name }}
                    </option>
                @endforeach
            </select>
        </form>
        <p class="text-xs text-slate-500 mt-1">
            Pulau aktif: <span class="font-semibold">{{ $activeIsland->name }}</span>
        </p>
    </div>

    <div class="space-y-6">
        {{-- =========================
           ABOUT HEADER (SEKALI)
           ========================= --}}
        <div class="border border-slate-700 rounded-xl bg-slate-900/80 overflow-hidden">
            <div class="px-4 py-3 bg-slate-800/70">
                <h2 class="text-sm sm:text-base font-semibold">Header About Pulau (Sekali)</h2>
                <p class="text-[11px] text-slate-400">
                    Opsional semua. Jika kosong, frontend pakai fallback.
                </p>
            </div>

            <div class="px-4 py-4">
                <form method="POST" action="{{ route('admin.about_stats.about_page', $activeIsland) }}" class="space-y-3">
                    @csrf

                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-400">Label kecil (opsional)</label>
                            <input type="text" name="label_small"
                                   value="{{ old('label_small', $aboutPage->label_small ?? '') }}"
                                   class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                                   placeholder="MENGENAL SUMATERA">
                        </div>
                        <div>
                            <label class="text-xs text-slate-400">Judul besar (opsional)</label>
                            <input type="text" name="hero_title"
                                   value="{{ old('hero_title', $aboutPage->hero_title ?? '') }}"
                                   class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                                   placeholder="Apa itu Sumatera?">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-slate-400">Deskripsi header (opsional)</label>
                        <textarea name="hero_description" rows="3"
                                  class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                                  placeholder="Deskripsi singkat...">{{ old('hero_description', $aboutPage->hero_description ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="text-xs text-slate-400">Link header (opsional)</label>
                        <input type="text" name="more_link"
                               value="{{ old('more_link', $aboutPage->more_link ?? '') }}"
                               class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                               placeholder="https://...">
                        <p class="text-[11px] text-slate-500 mt-1">
                            Jika diisi, frontend tampil tombol “Selengkapnya”.
                        </p>
                    </div>

                    <button type="submit" class="text-xs px-3 py-2 rounded-lg bg-amber-500 text-black hover:bg-amber-400">
                        Simpan Header
                    </button>
                </form>
            </div>
        </div>

        {{-- =========================
           ABOUT ITEMS (BISA BANYAK)
           ========================= --}}
        <div class="border border-slate-700 rounded-xl bg-slate-900/80 overflow-hidden">
            <div class="px-4 py-3 bg-slate-800/70">
                <h2 class="text-sm sm:text-base font-semibold">Konten About Pulau</h2>
                <p class="text-[11px] text-slate-400">
                    Title opsional, Image opsional, Points opsional, Link opsional. Description wajib.
                </p>
            </div>

            <div class="px-4 py-4 space-y-4">
                {{-- FORM TAMBAH ITEM --}}
                <form method="POST" action="{{ route('admin.about_stats.items.store', $activeIsland) }}" class="space-y-3">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-400">Title (opsional)</label>
                            <input type="text" name="title"
                                   value="{{ old('title') }}"
                                   class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                                   placeholder="Judul item...">
                        </div>
                        <div>
                            <label class="text-xs text-slate-400">Image URL / path (opsional)</label>
                            <input type="text" name="image"
                                   value="{{ old('image') }}"
                                   class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                                   placeholder="/storage/... atau https://...">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-slate-400">Description (wajib)</label>
                        <textarea name="description" rows="3" required
                                  class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                                  placeholder="Deskripsi...">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="text-xs text-slate-400">Points (opsional, 1 baris = 1 point)</label>
                        <textarea name="points" rows="3"
                                  class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                                  placeholder="Point 1&#10;Point 2&#10;Point 3">{{ old('points') }}</textarea>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-400">Link (opsional)</label>
                            <input type="text" name="more_link"
                                   value="{{ old('more_link') }}"
                                   class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                                   placeholder="https://...">
                        </div>
                        <div>
                            <label class="text-xs text-slate-400">Sort order (opsional)</label>
                            <input type="number" name="sort_order"
                                   value="{{ old('sort_order', 0) }}"
                                   class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                                   placeholder="0">
                        </div>
                    </div>

                    <button type="submit" class="text-xs px-3 py-2 rounded-lg bg-emerald-500 text-black hover:bg-emerald-400">
                        + Tambah Item
                    </button>
                </form>

                <hr class="border-slate-700">

                {{-- LIST ITEM --}}
                <div class="space-y-3">
                    @forelse($aboutItems as $it)
                        <div class="rounded-lg border border-slate-700 bg-slate-950/40 p-3">
                            <form method="POST" action="{{ route('admin.about_stats.items.update', [$activeIsland, $it]) }}" class="space-y-2">
                                @csrf
                                @method('PUT')

                                <div class="grid sm:grid-cols-2 gap-2">
                                    <input type="text" name="title" value="{{ old('title', $it->title) }}"
                                           class="bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                                           placeholder="Title (opsional)">
                                    <input type="text" name="image" value="{{ old('image', $it->image) }}"
                                           class="bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                                           placeholder="Image (opsional)">
                                </div>

                                <textarea name="description" rows="3" required
                                          class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                                          placeholder="Description">{{ old('description', $it->description) }}</textarea>

                                <textarea name="points" rows="2"
                                          class="w-full bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                                          placeholder="Points (opsional)">{{ old('points', $it->points) }}</textarea>

                                <div class="grid sm:grid-cols-2 gap-2">
                                    <input type="text" name="more_link" value="{{ old('more_link', $it->more_link) }}"
                                           class="bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                                           placeholder="Link (opsional)">
                                    <input type="number" name="sort_order" value="{{ old('sort_order', $it->sort_order) }}"
                                           class="bg-slate-950 border border-slate-700 rounded px-2 py-2 text-sm"
                                           placeholder="Sort">
                                </div>

                                <div class="flex gap-2">
                                    <button type="submit" class="text-xs px-3 py-2 rounded bg-amber-500 text-black hover:bg-amber-400">
                                        Simpan
                                    </button>
                            </form>

                            <form method="POST" action="{{ route('admin.about_stats.items.destroy', [$activeIsland, $it]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs px-3 py-2 rounded bg-red-500/80 hover:bg-red-500">
                                    Hapus
                                </button>
                            </form>
                                </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada item About.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- =========================
           STATISTIK (PAKAI DATA SAMA)
           ========================= --}}
        <div class="border border-slate-700 rounded-xl bg-slate-900/80 overflow-hidden">
            <div class="px-4 py-3 bg-slate-800/70">
                <h2 class="text-sm sm:text-base font-semibold">Statistik Pulau</h2>
                <p class="text-[11px] text-slate-400">
                    Data & form sama seperti halaman Statistik kamu.
                </p>
            </div>

            <div class="px-4 py-4 space-y-6">
                {{-- POPULATION --}}
                <div class="rounded-lg border border-slate-700 bg-slate-950/40 p-3">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs text-slate-400">Total penduduk (perkiraan)</p>
                            <p class="text-2xl font-semibold">
                                {{ $activeIsland->population ? number_format($activeIsland->population, 0, ',', '.') : '—' }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('admin.stats.population.update', $activeIsland) }}" class="flex items-end gap-2">
                            @csrf
                            <div class="flex flex-col">
                                <label class="text-[11px] text-slate-400 mb-1">Ubah jumlah penduduk</label>
                                <input type="number" name="population"
                                       value="{{ old('population', $activeIsland->population) }}"
                                       class="bg-slate-950 border border-slate-700 rounded px-2 py-1 text-sm w-44 text-right"
                                       placeholder="0">
                            </div>
                            <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-amber-500 text-black hover:bg-amber-400">
                                Simpan
                            </button>
                        </form>
                    </div>
                </div>

                {{-- DEMOGRAPHICS --}}
                <div class="grid md:grid-cols-2 gap-4">
                    {{-- AGAMA --}}
                    <div class="rounded-lg border border-slate-700 bg-slate-950/40 p-3">
                        <h3 class="text-sm font-semibold mb-2">Agama</h3>

                        <div class="space-y-2 mb-3 max-h-40 overflow-auto pr-1">
                            @forelse($religions as $row)
                                <div class="flex items-center justify-between text-xs bg-slate-800/80 rounded px-2 py-1.5">
                                    <span>{{ $row->label }}</span>
                                    <div class="flex items-center gap-2">
                                        <span>{{ $row->percentage }}%</span>
                                        <form method="POST" action="{{ route('admin.stats.demographics.destroy', [$activeIsland, $row]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500">Belum ada data agama.</p>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('admin.stats.demographics.store', $activeIsland) }}" class="space-y-2 text-xs">
                            @csrf
                            <input type="hidden" name="type" value="religion">
                            <div class="flex gap-2">
                                <input type="text" name="label" class="bg-slate-950 border border-slate-700 rounded px-2 py-1 flex-1" placeholder="Islam" required>
                                <input type="number" step="0.01" name="percentage" class="bg-slate-950 border border-slate-700 rounded px-2 py-1 w-24 text-right" placeholder="%" required>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <input type="number" name="order" class="bg-slate-950 border border-slate-700 rounded px-2 py-1 w-20 text-right" placeholder="Urut">
                                <button type="submit" class="px-3 py-1.5 rounded bg-emerald-500 text-black hover:bg-emerald-400">
                                    + Tambah
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- SUKU --}}
                    <div class="rounded-lg border border-slate-700 bg-slate-950/40 p-3">
                        <h3 class="text-sm font-semibold mb-2">Suku</h3>

                        <div class="space-y-2 mb-3 max-h-40 overflow-auto pr-1">
                            @forelse($ethnicities as $row)
                                <div class="flex items-center justify-between text-xs bg-slate-800/80 rounded px-2 py-1.5">
                                    <span>{{ $row->label }}</span>
                                    <div class="flex items-center gap-2">
                                        <span>{{ $row->percentage }}%</span>
                                        <form method="POST" action="{{ route('admin.stats.demographics.destroy', [$activeIsland, $row]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500">Belum ada data suku.</p>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('admin.stats.demographics.store', $activeIsland) }}" class="space-y-2 text-xs">
                            @csrf
                            <input type="hidden" name="type" value="ethnicity">
                            <div class="flex gap-2">
                                <input type="text" name="label" class="bg-slate-950 border border-slate-700 rounded px-2 py-1 flex-1" placeholder="Batak" required>
                                <input type="number" step="0.01" name="percentage" class="bg-slate-950 border border-slate-700 rounded px-2 py-1 w-24 text-right" placeholder="%" required>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <input type="number" name="order" class="bg-slate-950 border border-slate-700 rounded px-2 py-1 w-20 text-right" placeholder="Urut">
                                <button type="submit" class="px-3 py-1.5 rounded bg-emerald-500 text-black hover:bg-emerald-400">
                                    + Tambah
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- BAHASA --}}
                    <div class="rounded-lg border border-slate-700 bg-slate-950/40 p-3 md:col-span-2">
                        <h3 class="text-sm font-semibold mb-2">Bahasa</h3>

                        <div class="space-y-2 mb-3 max-h-40 overflow-auto pr-1">
                            @forelse($languages as $row)
                                <div class="flex items-center justify-between text-xs bg-slate-800/80 rounded px-2 py-1.5">
                                    <span>{{ $row->label }}</span>
                                    <div class="flex items-center gap-2">
                                        <span>{{ $row->percentage }}%</span>
                                        <form method="POST" action="{{ route('admin.stats.demographics.destroy', [$activeIsland, $row]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500">Belum ada data bahasa.</p>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('admin.stats.demographics.store', $activeIsland) }}" class="space-y-2 text-xs">
                            @csrf
                            <input type="hidden" name="type" value="language">
                            <div class="flex gap-2">
                                <input type="text" name="label" class="bg-slate-950 border border-slate-700 rounded px-2 py-1 flex-1" placeholder="Bahasa Minang" required>
                                <input type="number" step="0.01" name="percentage" class="bg-slate-950 border border-slate-700 rounded px-2 py-1 w-24 text-right" placeholder="%" required>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <input type="number" name="order" class="bg-slate-950 border border-slate-700 rounded px-2 py-1 w-20 text-right" placeholder="Urut">
                                <button type="submit" class="px-3 py-1.5 rounded bg-emerald-500 text-black hover:bg-emerald-400">
                                    + Tambah
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <p class="text-[11px] text-slate-500">
                    Frontend stats sudah neon + chart dinamis. Admin cukup isi data di sini.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
