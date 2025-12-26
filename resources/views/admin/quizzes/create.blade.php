@extends('layouts.admin')

@section('title', 'Admin - Buat Quiz')
@section('page-title', 'Buat Quiz')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow border p-5">
        <h2 class="text-xl font-bold mb-1">Buat Quiz Baru</h2>
        <p class="text-sm text-slate-500 mb-4">Isi judul, cakupan (global/pulau/suku), dan status aktif.</p>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700">
                <div class="font-bold mb-1">Gagal:</div>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.quizzes.store') }}" class="space-y-4" id="quizForm">
            @csrf

            <div>
                <label class="text-sm font-bold">Judul</label>
                <input class="w-full border rounded-lg px-3 py-2"
                       name="title" value="{{ old('title', 'Kuis Budaya Indonesia') }}" />
            </div>

            <div>
                <label class="text-sm font-bold">Cakupan Quiz</label>
                <select class="w-full border rounded-lg px-3 py-2" name="scope" id="scopeSelect">
                    <option value="global" @selected(old('scope','global')==='global')>Global (semua)</option>
                    <option value="island" @selected(old('scope')==='island')>Khusus Pulau</option>
                    <option value="tribe"  @selected(old('scope')==='tribe')>Khusus Suku</option>
                </select>
                <p class="text-xs text-slate-500 mt-1">
                    Global = tampil di semua. Pulau = tampil untuk pulau tertentu. Suku = tampil untuk suku tertentu di pulau tertentu.
                </p>
            </div>

            <div id="islandWrap">
                <label class="text-sm font-bold">Pulau</label>
                <select name="island_id" id="islandSelect" class="w-full border rounded-lg px-3 py-2">
                    <option value="">— Pilih Pulau —</option>
                    @foreach($islands as $island)
                        <option value="{{ $island->id }}"
                                data-slug="{{ $island->slug }}"
                                @selected(old('island_id') == $island->id)>
                            {{ $island->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-500 mt-1">
                    Jika kosong, cek `is_active` di islands (Seeder harus set true).
                </p>
            </div>

            <div id="tribeWrap">
                <label class="text-sm font-bold">Suku</label>
                <select name="tribe" id="tribeSelect" class="w-full border rounded-lg px-3 py-2">
                    <option value="">— Pilih Suku —</option>
                </select>
                <p class="text-xs text-slate-500 mt-1">
                    Daftar suku mengikuti pulau yang dipilih (dari config/tribes.php).
                </p>
            </div>

            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                <span class="font-semibold">Aktifkan quiz</span>
            </label>

            <button class="px-4 py-2 rounded-lg bg-slate-900 text-white font-bold hover:opacity-90">
                Simpan
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tribesConfig = @json($tribesConfig ?? []);
    const scopeSelect  = document.getElementById('scopeSelect');
    const islandSelect = document.getElementById('islandSelect');
    const tribeSelect  = document.getElementById('tribeSelect');

    const islandWrap = document.getElementById('islandWrap');
    const tribeWrap  = document.getElementById('tribeWrap');

    const oldTribe = @json(old('tribe'));

    function fillTribes(selectedTribe) {
        const opt  = islandSelect.options[islandSelect.selectedIndex];
        const slug = opt ? opt.dataset.slug : null;

        const tribes = (slug && tribesConfig[slug]) ? tribesConfig[slug] : [];

        tribeSelect.innerHTML = '<option value="">— Pilih Suku —</option>';

        tribes.forEach(function (t) {
            const o = document.createElement('option');
            o.value = t;
            o.textContent = t;
            if (selectedTribe && selectedTribe === t) o.selected = true;
            tribeSelect.appendChild(o);
        });

        tribeSelect.disabled = tribes.length === 0;
    }

    function syncScopeUI() {
        const scope = scopeSelect.value;

        // global: island & tribe disembunyikan
        islandWrap.classList.toggle('hidden', scope === 'global');
        tribeWrap.classList.toggle('hidden', scope !== 'tribe');

        // jika scope tribe, isi tribes
        if (scope === 'tribe') {
            fillTribes(oldTribe || '');
        }
    }

    scopeSelect.addEventListener('change', syncScopeUI);
    islandSelect.addEventListener('change', function () {
        // kalau scope tribe, reset tribe tiap ganti pulau
        if (scopeSelect.value === 'tribe') fillTribes('');
    });

    // init
    syncScopeUI();
    if (scopeSelect.value === 'tribe') fillTribes(oldTribe || '');
});
</script>
@endsection
