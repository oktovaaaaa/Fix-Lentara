{{-- resources/views/admin/histories/create.blade.php (REPLACE FULL) --}}
@extends('layouts.admin')

@section('title', 'Tambah History')

@section('content')
@php
    // UI only: tidak mengubah logika program
@endphp

<div class="a-wrap" data-page="admin-histories-create">

    {{-- PAGE HEAD --}}
    <div class="a-head">
        <div class="a-head-left">
            <div class="a-head-title">Tambah History Pulau &amp; Suku</div>
            <div class="a-head-desc">
                Buat 1 item timeline baru. Header Sejarah (user) ikut tersimpan per <strong>Pulau + Suku</strong>.
                (Tampilan diselaraskan dengan UI kit admin; logic tetap.)
            </div>
        </div>

        <div class="a-head-right" style="display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap;">
            <a href="{{ route('admin.histories.index') }}" class="a-btn">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('admin.histories.store') }}" method="POST">
        @csrf

        {{-- CARD: TARGET (PULAU + SUKU) --}}
        <div class="a-card" data-card data-card-key="admin_histories_create_target">
            <div class="a-card-inner">
                <div class="a-card-head">
                    <div>
                        <div class="a-card-title">Target</div>
                        <div class="a-card-desc">
                            Pilih Pulau &amp; Suku. Daftar suku mengikuti config <strong>tribes.php</strong>.
                        </div>
                    </div>
                    <div class="a-card-actions">
                        <span class="a-badge"><i class="bx bx-filter-alt"></i> Filter</span>
                    </div>
                </div>

                <div class="a-grid">
                    {{-- Pulau --}}
                    <div class="a-col-6">
                        <label class="a-label" for="islandSelect">Pulau</label>
                        <select name="island_id" id="islandSelect" class="a-select">
                            <option value="">Pilih pulau...</option>
                            @foreach($islands as $island)
                                <option value="{{ $island->id }}"
                                        data-slug="{{ $island->slug }}"
                                        @selected(old('island_id', $selectedIslandId ?? null) == $island->id)>
                                    {{ $island->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('island_id') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    {{-- Suku --}}
                    <div class="a-col-6">
                        <label class="a-label" for="tribeSelect">Suku</label>
                        <select name="tribe" id="tribeSelect" class="a-select">
                            <option value="">Pilih suku...</option>
                            @foreach($tribes as $t)
                                <option value="{{ $t }}" @selected(old('tribe') === $t)>{{ $t }}</option>
                            @endforeach
                        </select>

                        <div class="a-help">
                            Daftar suku akan menyesuaikan <strong>pulau</strong> yang dipilih
                            (Jawa, Sumatera, Kalimantan, Sulawesi, Sunda Kecil, Papua &amp; Maluku).
                        </div>

                        @error('tribe') <div class="a-err">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD: HEADER UTAMA UNTUK USER (TRIBE PAGES) --}}
        <div class="a-card" data-card data-card-key="admin_histories_create_header" style="margin-top:12px;">
            <div class="a-card-inner">
                <div class="a-card-head">
                    <div>
                        <div class="a-card-title">Header Sejarah (Tampil di User)</div>
                        <div class="a-card-desc">
                            Admin mengatur <strong>Judul besar</strong> &amp; <strong>Deskripsi besar</strong> sebelum timeline zigzag.
                            Data ini disimpan per <strong>Pulau + Suku</strong>.
                        </div>
                    </div>
                    <div class="a-card-actions">
                        <span class="a-badge"><i class="bx bx-crown"></i> Header</span>
                        <button type="button" class="a-btn a-btn-sm" id="btnReloadHero">
                            <i class="bx bx-sync"></i> Reload
                        </button>
                    </div>
                </div>

                {{-- Hero Title --}}
                <div class="a-grid">
                    <div class="a-col-6">
                        <label class="a-label" for="heroTitle">Judul Besar</label>
                        <input type="text"
                               name="hero_title"
                               id="heroTitle"
                               value="{{ old('hero_title', $tribePage->hero_title ?? '') }}"
                               placeholder="Contoh: Sejarah Suku Aceh"
                               class="a-input">
                        @error('hero_title') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-6">
                        <label class="a-label" for="heroImage">Hero Image (opsional)</label>
                        <input type="text"
                               name="hero_image"
                               id="heroImage"
                               value="{{ old('hero_image', $tribePage->hero_image ?? '') }}"
                               placeholder="Contoh: /storage/hero/aceh.jpg atau https://..."
                               class="a-input">
                        <div class="a-help">Opsional. Kalau nanti kamu pakai gambar header di user page.</div>
                        @error('hero_image') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    {{-- Hero Description --}}
                    <div class="a-col-12">
                        <label class="a-label" for="heroDescription">Deskripsi Besar</label>
                        <textarea name="hero_description"
                                  id="heroDescription"
                                  rows="3"
                                  placeholder="Contoh: Timeline sejarah yang membentuk identitas budaya dan perjalanan..."
                                  class="a-textarea">{{ old('hero_description', $tribePage->hero_description ?? '') }}</textarea>
                        @error('hero_description') <div class="a-err">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="a-help" style="display:flex; gap:10px; align-items:center; margin-top:10px;">
                    <span style="display:inline-flex;width:10px;height:10px;border-radius:999px;background:rgba(249,115,22,.65);box-shadow:0 0 0 3px rgba(249,115,22,.15);"></span>
                    <span>Header bisa otomatis “terload” saat kamu pilih Pulau + Suku (kalau endpoint JSON ada).</span>
                </div>
            </div>
        </div>

        {{-- CARD: ITEM TIMELINE (ISLAND HISTORIES) --}}
        <div class="a-card" data-card data-card-key="admin_histories_create_timeline" style="margin-top:12px;">
            <div class="a-card-inner">
                <div class="a-card-head">
                    <div>
                        <div class="a-card-title">Item Timeline (Zigzag)</div>
                        <div class="a-card-desc">
                            Ini adalah isi per kejadian (contoh badge <strong>1975</strong>, <strong>1976</strong>, dst).
                        </div>
                    </div>
                    <div class="a-card-actions">
                        <span class="a-badge"><i class="bx bx-time-five"></i> Timeline</span>
                    </div>
                </div>

                <div class="a-grid">
                    {{-- Tahun / Waktu --}}
                    <div class="a-col-6">
                        <label class="a-label">Tahun / Waktu</label>
                        <input type="text" name="year_label" value="{{ old('year_label') }}"
                               placeholder="misal: 1975, Abad ke-14, 1900–1945"
                               class="a-input">
                        @error('year_label') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    {{-- Urutan --}}
                    <div class="a-col-6">
                        <label class="a-label">Urutan (opsional)</label>
                        <input type="number" name="order" value="{{ old('order', 0) }}"
                               class="a-input a-input-sm">
                        @error('order') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    {{-- Judul --}}
                    <div class="a-col-12">
                        <label class="a-label">Judul</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               class="a-input"
                               placeholder="Judul timeline...">
                        @error('title') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    {{-- Isi --}}
                    <div class="a-col-12">
                        <label class="a-label">Isi History</label>
                        <textarea name="content" rows="6"
                                  class="a-textarea"
                                  placeholder="Isi konten history...">{{ old('content') }}</textarea>
                        @error('content') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    {{-- Link opsional --}}
                    <div class="a-col-12">
                        <label class="a-label">Link Selengkapnya (opsional)</label>
                        <input type="url" name="more_link" value="{{ old('more_link') }}"
                               placeholder="https://contoh.com/artikel-lengkap"
                               class="a-input">
                        <div class="a-help">
                            Jika dikosongkan, tombol <strong>"Lihat selengkapnya"</strong> tidak akan muncul di tampilan user.
                        </div>
                        @error('more_link') <div class="a-err">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="a-actions" style="margin-top:12px;">
            <button type="submit" class="a-btn a-btn-primary">
                <i class="bx bx-save"></i> Simpan
            </button>

            <a href="{{ route('admin.histories.index') }}" class="a-btn">
                <i class="bx bx-x"></i> Batal
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

    const btnReload      = document.getElementById('btnReloadHero');

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
    async function loadHeroFromDB(force = false) {
        const islandId = getSelectedIslandId();
        const tribe    = getSelectedTribe();

        // kalau user lagi punya old input (validasi gagal), jangan override
        if (!force && (oldHeroTitle || oldHeroDesc || oldHeroImage)) return;

        if (!islandId || !tribe) return;

        // endpoint JSON (optional)
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
            // silent
        }
    }

    // init: isi tribes berdasarkan pulau + old tribe
    fillTribes(oldTribe || '');

    // init: kalau sudah ada pulau+suku, coba load hero
    loadHeroFromDB(false);

    islandSelect.addEventListener('change', function () {
        fillTribes('');
        // kalau user belum mengetik manual, coba load
        if (!heroTitle.value && !heroDescription.value && !heroImage.value) {
            loadHeroFromDB(false);
        }
    });

    tribeSelect.addEventListener('change', function () {
        loadHeroFromDB(false);
    });

    if (btnReload) {
        btnReload.addEventListener('click', function () {
            loadHeroFromDB(true);
        });
    }
});
</script>
@endsection
