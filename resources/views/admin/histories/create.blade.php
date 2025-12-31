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

        {{-- ================= HEADER UTAMA UNTUK USER (TRIBE PAGES) ================= --}}
        <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4 space-y-4">
            <div>
                <div class="text-sm font-semibold">Header Sejarah (Tampil di User)</div>
                <div class="text-xs text-slate-500">
                    Admin mengatur <strong>Judul besar</strong> &amp; <strong>Deskripsi besar</strong> sebelum timeline zigzag.
                    Data ini disimpan per <strong>Pulau + Suku</strong>.
                </div>
            </div>

            {{-- Hero Title --}}
            <div>
                <label class="block text-xs font-medium mb-1">Judul Besar</label>
                <input type="text"
                       name="hero_title"
                       id="heroTitle"
                       value="{{ old('hero_title', $tribePage->hero_title ?? '') }}"
                       placeholder="Contoh: Sejarah Suku Aceh"
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                @error('hero_title')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Hero Description --}}
            <div>
                <label class="block text-xs font-medium mb-1">Deskripsi Besar</label>
                <textarea name="hero_description"
                          id="heroDescription"
                          rows="3"
                          placeholder="Contoh: Timeline sejarah yang membentuk identitas budaya dan perjalanan..."
                          class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">{{ old('hero_description', $tribePage->hero_description ?? '') }}</textarea>
                @error('hero_description')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Hero Image (optional) --}}
            <div>
                <label class="block text-xs font-medium mb-1">Hero Image (opsional)</label>
                <input type="text"
                       name="hero_image"
                       id="heroImage"
                       value="{{ old('hero_image', $tribePage->hero_image ?? '') }}"
                       placeholder="Contoh: /storage/hero/aceh.jpg atau https://..."
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm">
                <p class="text-[11px] text-slate-500 mt-1">
                    Opsional. Kalau nanti kamu pakai gambar header di user page.
                </p>
                @error('hero_image')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2 text-[11px] text-slate-500">
                <span class="inline-flex w-2 h-2 rounded-full bg-orange-500/70"></span>
                Header akan otomatis “terload” saat kamu pilih Pulau + Suku (kalau endpoint JSON sudah dibuat).
            </div>
        </div>

        {{-- ================= ITEM TIMELINE (ISLAND HISTORIES) ================= --}}
        <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4 space-y-4">
            <div>
                <div class="text-sm font-semibold">Item Timeline (Zigzag)</div>
                <div class="text-xs text-slate-500">
                    Ini adalah isi per kejadian (contoh badge <strong>1975</strong>, <strong>1976</strong>, dst).
                </div>
            </div>

            {{-- Tahun / Waktu --}}
            <div>
                <label class="block text-xs font-medium mb-1">Tahun / Waktu</label>
                <input type="text" name="year_label" value="{{ old('year_label') }}"
                       placeholder="misal: 1975, Abad ke-14, 1900–1945"
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

{{-- =========================================================
   SCRIPT:
   1) Update daftar suku saat pulau berubah (dari tribesConfig)
   2) Auto-load header (hero_title/desc/image) dari tribe_pages
      via endpoint JSON (optional; form tetap jalan kalau endpoint belum ada)
========================================================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // mapping pulau -> daftar suku dari config/tribes.php
    const tribesConfig = @json($tribesConfig ?? []);

    const islandSelect   = document.getElementById('islandSelect');
    const tribeSelect    = document.getElementById('tribeSelect');

    const heroTitle      = document.getElementById('heroTitle');
    const heroDescription= document.getElementById('heroDescription');
    const heroImage      = document.getElementById('heroImage');

    const oldTribe = @json(old('tribe'));
    const oldHeroTitle = @json(old('hero_title'));
    const oldHeroDesc  = @json(old('hero_description'));
    const oldHeroImage = @json(old('hero_image'));

    function getSelectedIslandId() {
        return islandSelect.value || '';
    }

    function getSelectedIslandSlug() {
        const opt  = islandSelect.options[islandSelect.selectedIndex];
        return opt ? (opt.dataset.slug || '') : '';
    }

    function getSelectedTribe() {
        return tribeSelect.value || '';
    }

    function fillTribes(selectedTribe) {
        const slug = getSelectedIslandSlug();
        const tribes = slug && tribesConfig[slug] ? tribesConfig[slug] : [];

        tribeSelect.innerHTML = '<option value="">Pilih suku...</option>';

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

    // === Optional: load header from DB (tribe_pages)
    async function loadHeroFromDB() {
        const islandId = getSelectedIslandId();
        const tribe    = getSelectedTribe();

        // kalau user lagi punya old input (validasi gagal), jangan override
        if (oldHeroTitle || oldHeroDesc || oldHeroImage) return;

        if (!islandId || !tribe) return;

        // endpoint JSON (kita buat di file berikutnya)
        // contoh rute: admin/tribe-pages/lookup?island_id=1&tribe_key=Aceh
        const url = `{{ url('/admin/tribe-pages/lookup') }}?island_id=${encodeURIComponent(islandId)}&tribe_key=${encodeURIComponent(tribe)}`;

        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });

            // kalau route belum ada -> res bisa 404, kita abaikan agar form tidak crash
            if (!res.ok) return;

            const data = await res.json();

            // data bisa null/empty jika belum ada tribe_pages
            if (!data) return;

            heroTitle.value = data.hero_title ?? '';
            heroDescription.value = data.hero_description ?? '';
            heroImage.value = data.hero_image ?? '';
        } catch (e) {
            // silent: jangan ganggu admin
        }
    }

    // init: isi tribes berdasarkan pulau + old tribe
    fillTribes(oldTribe || '');

    // init: kalau sudah ada pulau+suku, coba load hero
    // (kalau user sedang edit old input, tidak akan override)
    loadHeroFromDB();

    islandSelect.addEventListener('change', function () {
        // ganti pulau -> reset suku & hero (jangan hapus isi manual user)
        fillTribes('');

        // hanya auto-load kalau user belum mengetik manual
        if (!heroTitle.value && !heroDescription.value && !heroImage.value) {
            loadHeroFromDB();
        }
    });

    tribeSelect.addEventListener('change', function () {
        // saat ganti suku, load hero baru
        loadHeroFromDB();
    });
});
</script>
@endsection
