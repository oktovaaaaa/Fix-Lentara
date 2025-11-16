{{-- resources/views/admin/histories/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'History Pulau & Suku')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">History Pulau &amp; Suku</h1>
        <a href="{{ route('admin.histories.create') }}"
           class="px-4 py-2 rounded-full bg-orange-500 hover:bg-orange-600 text-white text-sm shadow-lg shadow-orange-500/40">
            + Tambah History
        </a>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/40 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- FILTER --}}
    <form method="GET" class="mb-5 flex flex-wrap gap-3 items-end">
        <div>
            <label class="text-xs font-medium block mb-1">Pulau</label>
            <select name="island_id"
                    class="rounded-lg border border-slate-700 bg-slate-900/60 text-sm px-3 py-2">
                <option value="">Semua Pulau</option>
                @foreach($islands as $island)
                    <option value="{{ $island->id }}" @selected($islandId == $island->id)>
                        {{ $island->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-xs font-medium block mb-1">Suku (khusus Sumatera)</label>
            <select name="tribe"
                    class="rounded-lg border border-slate-700 bg-slate-900/60 text-sm px-3 py-2">
                <option value="">Semua</option>
                @foreach($sumateraTribes as $t)
                    <option value="{{ $t }}" @selected($tribe === $t)>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit"
                class="px-4 py-2 rounded-lg bg-slate-800 text-sm border border-slate-700">
            Filter
        </button>
    </form>

    {{-- TABLE --}}
    <div class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-950/40">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-900/60">
                <tr class="text-left">
                    <th class="px-4 py-3">Pulau</th>
                    <th class="px-4 py-3">Suku</th>
                    <th class="px-4 py-3">Tahun / Waktu</th>
                    <th class="px-4 py-3">Judul</th>
                    <th class="px-4 py-3">Link?</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $history)
                    <tr class="border-t border-slate-800/70">
                        <td class="px-4 py-3">{{ $history->island->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $history->tribe }}</td>
                        <td class="px-4 py-3">{{ $history->year_label }}</td>
                        <td class="px-4 py-3">
                            {{ \Illuminate\Support\Str::limit($history->title, 50) }}
                        </td>
                        <td class="px-4 py-3">
                            @if($history->more_link)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-xs">
                                    Ada
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-500/10 text-slate-400 text-xs">
                                    Tidak ada
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('admin.histories.edit', $history) }}"
                               class="text-xs px-3 py-1 rounded-full border border-slate-700 hover:bg-slate-800">
                                Edit
                            </a>

                            <form action="{{ route('admin.histories.destroy', $history) }}"
                                  method="POST"
                                  class="inline-block"
                                  onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-xs px-3 py-1 rounded-full border border-red-500/60 text-red-400 hover:bg-red-500/10">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-5 text-center text-slate-500">
                            Belum ada data history.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $histories->withQueryString()->links() }}
    </div>
</div>
@endsection
