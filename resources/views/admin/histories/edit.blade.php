{{-- resources/views/admin/histories/edit.blade.php (REPLACE FULL) --}}
@extends('layouts.admin')

@section('title', 'Edit History')

@section('content')
<div class="a-wrap" data-page="admin-histories-edit">

    {{-- PAGE HEAD --}}
    <div class="a-head">
        <div class="a-head-left">
            <div class="a-head-title">Edit History Pulau &amp; Suku</div>
            <div class="a-head-desc">
                Edit header sejarah (untuk user) dan item timeline (untuk zigzag timeline).
                Semua fungsi tetap sama, hanya tampilan diselaraskan dengan UI kit admin.
            </div>
        </div>

        <div class="a-head-right" style="display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap;">
            <a href="{{ route('admin.histories.index') }}" class="a-btn">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>
    </div>

    {{-- FORM --}}
    <form action="{{ route('admin.histories.update', $history) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- CARD: TARGET (PULAU + SUKU) --}}
        <div class="a-card" data-card data-card-key="admin_histories_edit_target">
            <div class="a-card-inner">
                <div class="a-card-head">
                    <div>
                        <div class="a-card-title">Target</div>
                        <div class="a-card-desc">Pilih Pulau &amp; Suku yang terkait dengan history ini.</div>
                    </div>
                    <div class="a-card-actions">
                        <span class="a-badge"><i class="bx bx-map"></i> Pulau + Suku</span>
                    </div>
                </div>

                <div class="a-grid">
                    {{-- Pulau --}}
                    <div class="a-col-6">
                        <label class="a-label" for="islandSelect">Pulau</label>
                        <select name="island_id" id="islandSelect" class="a-select">
                            @foreach($islands as $island)
                                <option value="{{ $island->id }}"
                                        data-slug="{{ $island->slug }}"
                                        @selected(old('island_id', $history->island_id) == $island->id)>
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
                            @foreach($tribes as $t)
                                <option value="{{ $t }}" @selected(old('tribe', $history->tribe) === $t)>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                        @error('tribe') <div class="a-err">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="a-note" style="margin-top:10px;">
                    Perubahan Pulau/Suku akan mempengaruhi pengambilan otomatis Header Sejarah dari <strong>TribePage</strong>.
                </div>
            </div>
        </div>

        {{-- CARD: HEADER UTAMA (TRIBE PAGES) --}}
        <div class="a-card" data-card data-card-key="admin_histories_edit_header" style="margin-top:12px;">
            <div class="a-card-inner">
                <div class="a-card-head">
                    <div>
                        <div class="a-card-title">Header Sejarah (User)</div>
                        <div class="a-card-desc">
                            Judul &amp; deskripsi besar sebelum timeline zigzag. Bisa auto-load dari endpoint lookup.
                        </div>
                    </div>
                    <div class="a-card-actions">
                        <span class="a-badge"><i class="bx bx-crown"></i> Header</span>
                        <button type="button" class="a-btn a-btn-sm" id="btnReloadHero">
                            <i class="bx bx-sync"></i> Reload
                        </button>
                    </div>
                </div>

                <div class="a-grid">
                    <div class="a-col-6">
                        <label class="a-label" for="heroTitle">Judul Besar</label>
                        <input type="text"
                               name="hero_title"
                               id="heroTitle"
                               value="{{ old('hero_title', $tribePage->hero_title ?? '') }}"
                               class="a-input"
                               placeholder="Contoh: Sejarah Aceh">
                        @error('hero_title') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-6">
                        <label class="a-label" for="heroImage">Hero Image (opsional)</label>
                        <input type="text"
                               name="hero_image"
                               id="heroImage"
                               value="{{ old('hero_image', $tribePage->hero_image ?? '') }}"
                               class="a-input"
                               placeholder="/storage/... atau https://...">
                        @error('hero_image') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-12">
                        <label class="a-label" for="heroDescription">Deskripsi Besar</label>
                        <textarea name="hero_description"
                                  id="heroDescription"
                                  rows="3"
                                  class="a-textarea"
                                  placeholder="Deskripsi besar untuk header sejarah...">{{ old('hero_description', $tribePage->hero_description ?? '') }}</textarea>
                        @error('hero_description') <div class="a-err">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="a-help" style="margin-top:10px;">
                    Tips: kalau kamu ganti Pulau/Suku, klik <strong>Reload</strong> untuk menarik data terbaru dari TribePage.
                </div>
            </div>
        </div>

        {{-- CARD: TIMELINE ITEM --}}
        <div class="a-card" data-card data-card-key="admin_histories_edit_timeline" style="margin-top:12px;">
            <div class="a-card-inner">
                <div class="a-card-head">
                    <div>
                        <div class="a-card-title">Item Timeline</div>
                        <div class="a-card-desc">Data yang akan tampil sebagai 1 blok timeline (zigzag) di halaman user.</div>
                    </div>
                    <div class="a-card-actions">
                        <span class="a-badge"><i class="bx bx-time-five"></i> Timeline</span>
                    </div>
                </div>

                <div class="a-grid">
                    <div class="a-col-6">
                        <label class="a-label">Tahun / Waktu</label>
                        <input type="text" name="year_label"
                               value="{{ old('year_label', $history->year_label) }}"
                               class="a-input"
                               placeholder="Contoh: 1904 / Abad ke-16 / Masa Kolonial">
                        @error('year_label') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-6">
                        <label class="a-label">Urutan</label>
                        <input type="number" name="order"
                               value="{{ old('order', $history->order) }}"
                               class="a-input a-input-sm"
                               placeholder="0">
                        @error('order') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-12">
                        <label class="a-label">Judul</label>
                        <input type="text" name="title"
                               value="{{ old('title', $history->title) }}"
                               class="a-input"
                               placeholder="Judul timeline...">
                        @error('title') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-12">
                        <label class="a-label">Isi</label>
                        <textarea name="content" rows="6"
                                  class="a-textarea"
                                  placeholder="Isi konten timeline...">{{ old('content', $history->content) }}</textarea>
                        @error('content') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-12">
                        <label class="a-label">Link (opsional)</label>
                        <input type="url" name="more_link"
                               value="{{ old('more_link', $history->more_link) }}"
                               class="a-input"
                               placeholder="https://...">
                        @error('more_link') <div class="a-err">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="a-actions" style="margin-top:12px;">
            <button type="submit" class="a-btn a-btn-primary">
                <i class="bx bx-save"></i> Update
            </button>

            <a href="{{ route('admin.histories.index') }}" class="a-btn">
                <i class="bx bx-x"></i> Batal
            </a>
        </div>
    </form>
</div>

{{-- AUTO LOAD HEADER VIA ENDPOINT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const island = document.getElementById('islandSelect');
    const tribe  = document.getElementById('tribeSelect');

    const title  = document.getElementById('heroTitle');
    const desc   = document.getElementById('heroDescription');
    const img    = document.getElementById('heroImage');

    const btnReload = document.getElementById('btnReloadHero');

    async function loadHero() {
        if (!island || !tribe) return;
        if (!island.value || !tribe.value) return;

        const url = `{{ route('admin.tribe-pages.lookup') }}?island_id=${encodeURIComponent(island.value)}&tribe_key=${encodeURIComponent(tribe.value)}`;

        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;

            const data = await res.json();
            if (!data) return;

            if (title) title.value = data.hero_title ?? '';
            if (desc)  desc.value  = data.hero_description ?? '';
            if (img)   img.value   = data.hero_image ?? '';
        } catch (e) {
            // silent
        }
    }

    if (island) island.addEventListener('change', loadHero);
    if (tribe)  tribe.addEventListener('change', loadHero);

    if (btnReload) btnReload.addEventListener('click', loadHero);
});
</script>
@endsection
