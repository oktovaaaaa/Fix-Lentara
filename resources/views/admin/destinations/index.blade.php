@extends('layouts.admin')

@section('title', 'Admin - Destinasi')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4 text-slate-100">

    <h1 class="text-2xl font-bold mb-4">Destinasi (CRUD per Pulau & Suku)</h1>

    @if(session('status'))
        <div class="mb-4 rounded bg-emerald-600/80 px-3 py-2 text-sm">
            {{ session('status') }}
        </div>
    @endif

    {{-- FILTER: Pulau + Suku --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <form method="GET" action="{{ route('admin.destinations.index') }}" class="rounded-xl bg-slate-900/60 border border-slate-700 p-4">
            <div class="mb-3">
                <label class="block text-xs text-slate-400 mb-1">Pilih Pulau</label>
                <select name="island" class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm"
                        onchange="this.form.submit()">
                    @foreach($islands as $isl)
                        <option value="{{ $isl->slug }}" {{ optional($selectedIsland)->slug === $isl->slug ? 'selected' : '' }}>
                            {{ $isl->name }} ({{ $isl->slug }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">Pilih Suku</label>
                <select name="tribe" class="w-full bg-slate-950 border border-slate-700 rounded px-3 py-2 text-sm"
                        onchange="this.form.submit()">
                    @forelse($tribes as $t)
                        <option value="{{ $t }}" {{ $selectedTribe === $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                    @empty
                        <option value="">(Belum ada suku untuk pulau ini)</option>
                    @endforelse
                </select>
            </div>
        </form>

        <div class="rounded-xl bg-slate-900/60 border border-slate-700 p-4 flex flex-col justify-between">
            <div class="text-sm text-slate-300">
                <div><span class="text-slate-400">Pulau:</span> <b>{{ optional($selectedIsland)->name ?? '-' }}</b></div>
                <div><span class="text-slate-400">Suku:</span> <b>{{ $selectedTribe ?: '-' }}</b></div>
            </div>

            <div class="mt-4">
                @if($selectedIsland && $selectedTribe)
                    <a href="{{ route('admin.destinations.create', ['island' => $selectedIsland->slug, 'tribe' => $selectedTribe]) }}"
                       class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                        + Tambah Destinasi
                    </a>
                @else
                    <div class="text-xs text-slate-400">
                        Pilih pulau & suku dulu untuk menambah destinasi.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="overflow-x-auto rounded-xl border border-slate-700 bg-slate-900/60">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-950/70 text-slate-300">
                <tr>
                    <th class="text-left px-4 py-3">#</th>
                    <th class="text-left px-4 py-3">Nama</th>
                    <th class="text-left px-4 py-3">Lokasi</th>
                    <th class="text-left px-4 py-3">Rating</th>
                    <th class="text-left px-4 py-3">Gambar</th>
                    <th class="text-left px-4 py-3">Aktif</th>
                    <th class="text-right px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-slate-200">
                @forelse($rows as $i => $row)
                    <tr class="border-t border-slate-800">
                        <td class="px-4 py-3">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $row->name }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ $row->location ?? '—' }}</td>
                        <td class="px-4 py-3">{{ number_format((float)$row->rating, 1) }}</td>
                        <td class="px-4 py-3">
                            @if($row->image_display_url)
                                <a href="{{ $row->image_display_url }}" target="_blank" class="text-orange-400 underline">Lihat</a>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($row->is_active)
                                <span class="px-2 py-1 rounded bg-emerald-600/70 text-white text-xs">Aktif</span>
                            @else
                                <span class="px-2 py-1 rounded bg-slate-700 text-slate-200 text-xs">Off</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.destinations.edit', $row) }}"
                               class="inline-flex items-center gap-2 px-3 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-xs font-semibold">
                                Edit
                            </a>

                            <form action="{{ route('admin.destinations.destroy', $row) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Hapus destinasi ini?')"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded bg-red-600/80 hover:bg-red-600 text-xs font-semibold text-white">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr class="border-t border-slate-800">
                        <td colspan="7" class="px-4 py-6 text-slate-400">
                            Belum ada destinasi untuk pulau & suku yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
