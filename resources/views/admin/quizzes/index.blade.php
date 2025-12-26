@extends('layouts.admin')
@section('title', 'Admin - Quiz')
@section('page-title', 'Quiz')

@section('content')
@php
$islands = $islands ?? collect();

// tribesMap: island_id => [tribes...]
$tribesMap = [];
foreach ($islands as $isl) {
    $tribesMap[(string)$isl->id] = config("tribes.{$isl->slug}") ?? [];
}

$filterScope = request('scope', '');
$filterIsland = request('island_id', '');
$filterTribe = request('tribe', '');
@endphp


<div class="max-w-6xl mx-auto">
    @if(session('success'))
        <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800"> ✅ {{ session('success') }} </div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-bold">Daftar Quiz</h2>
            <p class="text-sm text-slate-500">Kelola quiz dan pertanyaannya (global / pulau / suku).</p>
        </div>
        <a href="{{ route('admin.quizzes.create') }}"
           class="px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:opacity-90">
            + Buat Quiz
        </a>
    </div>

    {{-- FILTER --}}
    <div class="bg-white rounded-xl shadow border p-4 mb-4">
        <form method="GET" class="grid gap-3 sm:grid-cols-4 items-end">
            <div>
                <label class="text-xs font-bold text-slate-600">Cakupan</label>
                <select name="scope" class="w-full border rounded-lg px-3 py-2" id="scopeFilter">
                    <option value="" {{ $filterScope===''?'selected':'' }}>Semua</option>
                    <option value="global" {{ $filterScope==='global'?'selected':'' }}>Global</option>
                    <option value="island" {{ $filterScope==='island'?'selected':'' }}>Pulau</option>
                    <option value="tribe" {{ $filterScope==='tribe'?'selected':'' }}>Suku</option>
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-600">Pulau</label>
                <select name="island_id" class="w-full border rounded-lg px-3 py-2" id="islandFilter">
                    <option value="">Semua pulau</option>
                    @foreach($islands as $isl)
                        <option value="{{ $isl->id }}" {{ (string)$filterIsland===(string)$isl->id?'selected':'' }}>
                            {{ $isl->subtitle ?: $isl->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-600">Suku</label>
                <select name="tribe" class="w-full border rounded-lg px-3 py-2" id="tribeFilter">
                    <option value="">Semua suku</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button class="px-4 py-2 rounded-lg bg-slate-900 text-white font-bold">Terapkan</button>
                <a href="{{ route('admin.quizzes.index') }}" class="px-4 py-2 rounded-lg border font-bold">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left p-3">Judul</th>
                    <th class="text-left p-3">Cakupan</th>
                    <th class="text-left p-3">Status</th>
                    <th class="text-left p-3">Dibuat</th>
                    <th class="text-right p-3">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($quizzes as $q)
                    <tr class="border-t">
                        <td class="p-3 font-semibold">
                            {{ $q->title }}
                            @if($q->island_id || $q->tribe)
                                <div class="text-xs text-slate-500 mt-1">
                                    @if($q->island)
                                        Pulau: <span class="font-bold">{{ $q->island->subtitle ?: $q->island->name }}</span>
                                    @endif
                                    @if($q->tribe)
                                        • Suku: <span class="font-bold">{{ $q->tribe }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        <td class="p-3">
                            @php $label = $q->scope_label; @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-bold
                                {{ $label==='Global' ? 'bg-slate-100 text-slate-700' : '' }}
                                {{ $label==='Pulau' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $label==='Suku' ? 'bg-purple-100 text-purple-700' : '' }}">
                                {{ $label }}
                            </span>
                        </td>

                        <td class="p-3">
                            <span class="px-2 py-1 rounded-full text-xs font-bold
                                {{ $q->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $q->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>

                        <td class="p-3 text-slate-500">{{ optional($q->created_at)->format('d/m/Y') }}</td>

                        <td class="p-3 text-right whitespace-nowrap">
                            <a class="px-3 py-1 rounded-lg bg-amber-100 text-amber-800 font-bold"
                               href="{{ route('admin.quizzes.edit', $q) }}">Edit</a>

                            <form class="inline" method="POST" action="{{ route('admin.quizzes.destroy', $q) }}"
                                  onsubmit="return confirm('Hapus quiz ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="px-3 py-1 rounded-lg bg-red-100 text-red-700 font-bold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-4 text-slate-500">Belum ada quiz.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $quizzes->appends(request()->query())->links() }}
    </div>
</div>

@push('scripts')
<script>
(function(){
    const tribesMap = @json($tribesMap);
    const islandEl = document.getElementById('islandFilter');
    const tribeEl = document.getElementById('tribeFilter');
    const oldTribe = @json($filterTribe);

    function fillTribes(){
        const islandId = String(islandEl.value || '');
        const tribes = tribesMap[islandId] || [];

        tribeEl.innerHTML = '<option value="">Semua suku</option>';

        tribes.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t;
            opt.textContent = t;
            if (oldTribe && oldTribe === t) opt.selected = true;
            tribeEl.appendChild(opt);
        });

        tribeEl.disabled = tribes.length === 0;
    }

    islandEl.addEventListener('change', function(){
        // reset oldTribe selection visual
        fillTribes();
    });

    fillTribes();
})();
</script>
@endpush

@endsection
