{{-- resources/views/admin/about-islands-stats.blade.php (REPLACE FULL) --}}
@extends('layouts.admin')

@section('title', 'Admin - About Pulau + Statistik')

@section('content')
<div class="a-wrap" data-page="admin-about-islands-stats">

    {{-- PAGE HEAD --}}
    <div class="a-head">
        <div class="a-head-left">
            <div class="a-head-title">About Pulau + Statistik</div>
            <div class="a-head-desc">
                Kelola <strong>Header About Pulau</strong>, <strong>Item About Pulau</strong>, dan <strong>Statistik</strong> per pulau.
                Semua mengikuti tema dan UI kit yang sama agar admin rapi dan konsisten.
            </div>
        </div>

        <div class="a-head-right">
            @if(session('status'))
                <div class="a-alert a-alert-success">
                    {{ session('status') }}
                </div>
            @endif
        </div>
    </div>

    {{-- ===== CARD: PICKER PULAU ===== --}}
    <div class="a-card" data-card data-card-key="admin_about_stats_picker">
        <div class="a-card-inner">
            <div class="a-card-head">
                <div>
                    <div class="a-card-title">Pilih Pulau</div>
                    <div class="a-card-desc">
                        Pilih pulau untuk mengelola About + Statistik. Halaman akan auto-load setelah memilih.
                    </div>
                </div>
                <div class="a-card-actions">
                    <span class="a-badge"><i class="bx bx-map"></i> Island</span>
                </div>
            </div>

            <div class="a-grid">
                <div class="a-col-6">
                    <form method="GET" action="{{ route('admin.about_stats.index') }}">
                        <label class="a-label" for="islandPicker">Pulau</label>
                        <select id="islandPicker" name="island" class="a-select" onchange="this.form.submit()">
                            @foreach($islands as $island)
                                <option value="{{ $island->slug }}" {{ $island->id === $activeIsland->id ? 'selected' : '' }}>
                                    {{ $island->name }}
                                </option>
                            @endforeach
                        </select>

                        <div class="a-help">
                            Pulau aktif: <strong>{{ $activeIsland->name }}</strong>
                        </div>
                    </form>
                </div>

                <div class="a-col-6">
                    <div class="a-badge" style="width:max-content;">
                        <i class="bx bx-check-circle"></i>
                        Context: {{ $activeIsland->name }}
                    </div>

                    <div class="a-note" style="margin-top:10px;">
                        Semua perubahan di halaman ini akan tersimpan untuk pulau yang sedang aktif.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================
       CARD: ABOUT HEADER (SEKALI)
       ========================= --}}
    <div class="a-card" data-card data-card-key="admin_about_stats_header">
        <div class="a-card-inner">
            <div class="a-card-head">
                <div>
                    <div class="a-card-title">Header About Pulau (Sekali)</div>
                    <div class="a-card-desc">
                        Opsional semua. Jika kosong, frontend menggunakan fallback.
                        Link (opsional) akan menampilkan tombol <strong>“Selengkapnya”</strong>.
                    </div>
                </div>
                <div class="a-card-actions">
                    <span class="a-badge"><i class="bx bx-detail"></i> Header</span>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.about_stats.about_page', $activeIsland) }}" class="a-form">
                @csrf

                <div class="a-grid">
                    <div class="a-col-6">
                        <label class="a-label">Label kecil (opsional)</label>
                        <input type="text" name="label_small"
                               value="{{ old('label_small', $aboutPage->label_small ?? '') }}"
                               class="a-input"
                               placeholder="MENGENAL {{ strtoupper($activeIsland->name) }}">
                    </div>

                    <div class="a-col-6">
                        <label class="a-label">Judul besar (opsional)</label>
                        <input type="text" name="hero_title"
                               value="{{ old('hero_title', $aboutPage->hero_title ?? '') }}"
                               class="a-input"
                               placeholder="Apa itu {{ $activeIsland->name }}?">
                    </div>

                    <div class="a-col-12">
                        <label class="a-label">Deskripsi header (opsional)</label>
                        <textarea name="hero_description" rows="3"
                                  class="a-textarea"
                                  placeholder="Deskripsi singkat...">{{ old('hero_description', $aboutPage->hero_description ?? '') }}</textarea>
                    </div>

                    <div class="a-col-12">
                        <label class="a-label">Link header (opsional)</label>
                        <input type="text" name="more_link"
                               value="{{ old('more_link', $aboutPage->more_link ?? '') }}"
                               class="a-input"
                               placeholder="https://...">
                        <div class="a-help">
                            Jika diisi, frontend tampil tombol “Selengkapnya”.
                        </div>
                    </div>
                </div>

                <div class="a-actions">
                    <button type="submit" class="a-btn a-btn-primary">
                        <i class="bx bx-save"></i> Simpan Header
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================
       CARD: ABOUT ITEMS (BISA BANYAK)
       ========================= --}}
    <div class="a-card" data-card data-card-key="admin_about_stats_items">
        <div class="a-card-inner">
            <div class="a-card-head">
                <div>
                    <div class="a-card-title">Konten About Pulau</div>
                    <div class="a-card-desc">
                        Title opsional, Image opsional, Points opsional, Link opsional. <strong>Description wajib</strong>.
                    </div>
                </div>
                <div class="a-card-actions">
                    <span class="a-badge"><i class="bx bx-layer"></i> Items</span>
                </div>
            </div>

            {{-- FORM TAMBAH ITEM --}}
            <div class="a-card" style="box-shadow:none; border-style:dashed;" data-card data-card-key="admin_about_stats_add_item">
                <div class="a-card-inner">
                    <div class="a-card-head">
                        <div>
                            <div class="a-card-title">Tambah Item About</div>
                            <div class="a-card-desc">Tambah konten baru untuk pulau ini.</div>
                        </div>
                        <div class="a-card-actions">
                            <span class="a-badge"><i class="bx bx-plus-circle"></i> Tambah</span>
                        </div>
                    </div>
<form method="POST" action="{{ route('admin.about_stats.items.store', $activeIsland) }}" class="a-form" enctype="multipart/form-data">

                        @csrf

                        <div class="a-grid">
                            <div class="a-col-6">
                                <label class="a-label">Title (opsional)</label>
                                <input type="text" name="title"
                                       value="{{ old('title') }}"
                                       class="a-input"
                                       placeholder="Judul item...">
                            </div>

<div class="a-col-6">
    <label class="a-label">Image via Link (opsional)</label>
    <input
        type="text"
        name="image_url"
        value="{{ old('image_url') }}"
        class="a-input js-img-url"
        placeholder="/storage/... atau https://..."
        autocomplete="off"
    >
    <div class="a-help">Isi salah satu: Link atau Upload.</div>
</div>

<div class="a-col-6">
    <label class="a-label">Upload Image (opsional)</label>
    <input
        type="file"
        name="image_file"
        class="a-input js-img-file"
        accept="image/*"
    >
    <div class="a-help">Jika upload dipilih, input Link akan nonaktif.</div>
</div>


                            <div class="a-col-12">
                                <label class="a-label">Description (wajib)</label>
                                <textarea name="description" rows="3" required
                                          class="a-textarea"
                                          placeholder="Deskripsi...">{{ old('description') }}</textarea>
                            </div>

                            <div class="a-col-12">
                                <label class="a-label">Points (opsional, 1 baris = 1 point)</label>
                                <textarea name="points" rows="3"
                                          class="a-textarea"
                                          placeholder="Point 1&#10;Point 2&#10;Point 3">{{ old('points') }}</textarea>
                                <div class="a-help">Jika kosong, points tidak tampil di frontend.</div>
                            </div>

                            <div class="a-col-6">
                                <label class="a-label">Link (opsional)</label>
                                <input type="text" name="more_link"
                                       value="{{ old('more_link') }}"
                                       class="a-input"
                                       placeholder="https://...">
                            </div>

                            <div class="a-col-6">
                                <label class="a-label">Sort order (opsional)</label>
                                <input type="number" name="sort_order"
                                       value="{{ old('sort_order', 0) }}"
                                       class="a-input"
                                       placeholder="0">
                            </div>
                        </div>

                        <div class="a-actions">
                            <button type="submit" class="a-btn a-btn-primary">
                                <i class="bx bx-plus"></i> Tambah Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- LIST ITEM --}}
            <div style="margin-top:12px;">
                @forelse($aboutItems as $it)
                    <div class="a-card" style="margin-bottom:12px;" data-card data-card-key="admin_about_stats_item_{{ $it->id }}">
                        <div class="a-card-inner">
                            <div class="a-card-head">
                                <div>
                                    <div class="a-card-title">Item #{{ $it->id }}</div>
                                    <div class="a-card-desc">Edit konten item ini. Simpan untuk update, hapus untuk delete.</div>
                                </div>
                                <div class="a-card-actions">
                                    <span class="a-badge"><i class="bx bx-sort"></i> Order: {{ $it->sort_order }}</span>
                                </div>
                            </div>

                            <div class="a-grid">

                                {{-- FORM UPDATE --}}
                                <div class="a-col-12">
<form method="POST" action="{{ route('admin.about_stats.items.update', [$activeIsland, $it]) }}" class="a-form" enctype="multipart/form-data">

                                    @csrf
                                        @method('PUT')

                                        <div class="a-grid">
                                            <div class="a-col-6">
                                                <label class="a-label">Title (opsional)</label>
                                                <input type="text" name="title"
                                                       value="{{ old('title', $it->title) }}"
                                                       class="a-input"
                                                       placeholder="Title (opsional)">
                                            </div>

<div class="a-col-6">
    <label class="a-label">Image via Link (opsional)</label>
    <input
        type="text"
        name="image_url"
        value="{{ old('image_url', $it->image) }}"
        class="a-input js-img-url"
        placeholder="/storage/... atau https://..."
        autocomplete="off"
    >
    <div class="a-help">Kalau kamu upload file baru, link akan dinonaktifkan.</div>
</div>

<div class="a-col-6">
    <label class="a-label">Upload Image (opsional)</label>
    <input
        type="file"
        name="image_file"
        class="a-input js-img-file"
        accept="image/*"
    >
    <div class="a-help">Upload ini akan mengganti image sebelumnya.</div>
</div>


                                            <div class="a-col-12">
                                                <label class="a-label">Description (wajib)</label>
                                                <textarea name="description" rows="3" required
                                                          class="a-textarea"
                                                          placeholder="Description">{{ old('description', $it->description) }}</textarea>
                                            </div>

                                            <div class="a-col-12">
                                                <label class="a-label">Points (opsional, 1 baris = 1 point)</label>
                                                <textarea name="points" rows="2"
                                                          class="a-textarea"
                                                          placeholder="Points (opsional)">{{ old('points', $it->points) }}</textarea>
                                            </div>

                                            <div class="a-col-6">
                                                <label class="a-label">Link (opsional)</label>
                                                <input type="text" name="more_link"
                                                       value="{{ old('more_link', $it->more_link) }}"
                                                       class="a-input"
                                                       placeholder="Link (opsional)">
                                            </div>

                                            <div class="a-col-6">
                                                <label class="a-label">Sort order</label>
                                                <input type="number" name="sort_order"
                                                       value="{{ old('sort_order', $it->sort_order) }}"
                                                       class="a-input"
                                                       placeholder="Sort">
                                            </div>
                                        </div>

                                        <div class="a-actions">
                                            <button type="submit" class="a-btn a-btn-primary">
                                                <i class="bx bx-save"></i> Simpan
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                {{-- FORM DELETE --}}
                                <div class="a-col-12">
                                    <form method="POST" action="{{ route('admin.about_stats.items.destroy', [$activeIsland, $it]) }}"
                                          onsubmit="return confirm('Hapus item ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="a-btn a-btn-danger">
                                            <i class="bx bx-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="a-empty">Belum ada item About.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- =========================
       CARD: STATISTIK (PAKAI DATA SAMA)
       ========================= --}}
    <div class="a-card" data-card data-card-key="admin_about_stats_stats">
        <div class="a-card-inner">
            <div class="a-card-head">
                <div>
                    <div class="a-card-title">Statistik Pulau</div>
                    <div class="a-card-desc">
                        Data & form sama seperti halaman Statistik kamu. Admin cukup isi data di sini.
                    </div>
                </div>
                <div class="a-card-actions">
                    <span class="a-badge"><i class="bx bx-bar-chart-alt-2"></i> Stats</span>
                </div>
            </div>

            {{-- POPULATION --}}
            <div class="a-card" style="box-shadow:none;" data-card data-card-key="admin_stats_population">
                <div class="a-card-inner">
                    <div class="a-card-head">
                        <div>
                            <div class="a-card-title">Total Penduduk (perkiraan)</div>
                            <div class="a-card-desc">Nilai ini tampil di frontend statistik.</div>
                        </div>
                        <div class="a-card-actions">
                            <span class="a-badge">
                                <i class="bx bx-user"></i>
                                {{ $activeIsland->population ? number_format($activeIsland->population, 0, ',', '.') : '—' }}
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.stats.population.update', $activeIsland) }}" class="a-form">
                        @csrf

                        <div class="a-grid">
                            <div class="a-col-6">
                                <label class="a-label">Ubah jumlah penduduk</label>
                                <input type="number" name="population"
                                       value="{{ old('population', $activeIsland->population) }}"
                                       class="a-input"
                                       placeholder="0">
                                <div class="a-help">Gunakan angka tanpa titik/koma. Format akan otomatis saat tampil.</div>
                            </div>

                            <div class="a-col-6">
                                <div class="a-actions" style="margin-top:26px;">
                                    <button type="submit" class="a-btn a-btn-primary">
                                        <i class="bx bx-save"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            {{-- DEMOGRAPHICS GRID --}}
            <div class="a-grid" style="margin-top:12px;">

                {{-- AGAMA --}}
                <div class="a-col-6">
                    <div class="a-card" style="box-shadow:none;" data-card data-card-key="admin_stats_religion">
                        <div class="a-card-inner">
                            <div class="a-card-head">
                                <div>
                                    <div class="a-card-title">Agama</div>
                                    <div class="a-card-desc">Tambah data persentase. Bisa hapus item.</div>
                                </div>
                                <div class="a-card-actions">
                                    <span class="a-badge"><i class="bx bx-book-heart"></i> Religion</span>
                                </div>
                            </div>

                            <div style="display:grid; gap:8px; max-height:220px; overflow:auto; padding-right:4px;">
                                @forelse($religions as $row)
                                    <div class="a-card" style="box-shadow:none;">
                                        <div class="a-card-inner" style="padding:10px; display:flex; align-items:center; justify-content:space-between; gap:10px;">
                                            <div style="font-weight:900;">{{ $row->label }}</div>
                                            <div style="display:flex; align-items:center; gap:10px;">
                                                <span class="a-badge"><i class="bx bx-pie-chart-alt-2"></i> {{ $row->percentage }}%</span>
                                                <form method="POST" action="{{ route('admin.stats.demographics.destroy', [$activeIsland, $row]) }}"
                                                      onsubmit="return confirm('Hapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="a-btn a-btn-danger a-btn-sm">
                                                        <i class="bx bx-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="a-empty">Belum ada data agama.</div>
                                @endforelse
                            </div>

                            <form method="POST" action="{{ route('admin.stats.demographics.store', $activeIsland) }}" class="a-form" style="margin-top:10px;">
                                @csrf
                                <input type="hidden" name="type" value="religion">

                                <div class="a-grid">
                                    <div class="a-col-6">
                                        <label class="a-label">Label</label>
                                        <input type="text" name="label" class="a-input" placeholder="Islam" required>
                                    </div>
                                    <div class="a-col-6">
                                        <label class="a-label">Persentase (%)</label>
                                        <input type="number" step="0.01" name="percentage" class="a-input" placeholder="0" required>
                                    </div>
                                    <div class="a-col-6">
                                        <label class="a-label">Urutan (opsional)</label>
                                        <input type="number" name="order" class="a-input" placeholder="0">
                                    </div>
                                    <div class="a-col-6">
                                        <div class="a-actions" style="margin-top:26px;">
                                            <button type="submit" class="a-btn a-btn-primary">
                                                <i class="bx bx-plus"></i> Tambah
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                {{-- SUKU --}}
                <div class="a-col-6">
                    <div class="a-card" style="box-shadow:none;" data-card data-card-key="admin_stats_ethnicity">
                        <div class="a-card-inner">
                            <div class="a-card-head">
                                <div>
                                    <div class="a-card-title">Suku</div>
                                    <div class="a-card-desc">Tambah data persentase. Bisa hapus item.</div>
                                </div>
                                <div class="a-card-actions">
                                    <span class="a-badge"><i class="bx bx-group"></i> Ethnicity</span>
                                </div>
                            </div>

                            <div style="display:grid; gap:8px; max-height:220px; overflow:auto; padding-right:4px;">
                                @forelse($ethnicities as $row)
                                    <div class="a-card" style="box-shadow:none;">
                                        <div class="a-card-inner" style="padding:10px; display:flex; align-items:center; justify-content:space-between; gap:10px;">
                                            <div style="font-weight:900;">{{ $row->label }}</div>
                                            <div style="display:flex; align-items:center; gap:10px;">
                                                <span class="a-badge"><i class="bx bx-pie-chart-alt-2"></i> {{ $row->percentage }}%</span>
                                                <form method="POST" action="{{ route('admin.stats.demographics.destroy', [$activeIsland, $row]) }}"
                                                      onsubmit="return confirm('Hapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="a-btn a-btn-danger a-btn-sm">
                                                        <i class="bx bx-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="a-empty">Belum ada data suku.</div>
                                @endforelse
                            </div>

                            <form method="POST" action="{{ route('admin.stats.demographics.store', $activeIsland) }}" class="a-form" style="margin-top:10px;">
                                @csrf
                                <input type="hidden" name="type" value="ethnicity">

                                <div class="a-grid">
                                    <div class="a-col-6">
                                        <label class="a-label">Label</label>
                                        <input type="text" name="label" class="a-input" placeholder="Batak" required>
                                    </div>
                                    <div class="a-col-6">
                                        <label class="a-label">Persentase (%)</label>
                                        <input type="number" step="0.01" name="percentage" class="a-input" placeholder="0" required>
                                    </div>
                                    <div class="a-col-6">
                                        <label class="a-label">Urutan (opsional)</label>
                                        <input type="number" name="order" class="a-input" placeholder="0">
                                    </div>
                                    <div class="a-col-6">
                                        <div class="a-actions" style="margin-top:26px;">
                                            <button type="submit" class="a-btn a-btn-primary">
                                                <i class="bx bx-plus"></i> Tambah
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                {{-- BAHASA (FULL WIDTH) --}}
                <div class="a-col-12">
                    <div class="a-card" style="box-shadow:none;" data-card data-card-key="admin_stats_language">
                        <div class="a-card-inner">
                            <div class="a-card-head">
                                <div>
                                    <div class="a-card-title">Bahasa</div>
                                    <div class="a-card-desc">Tambah data persentase. Bisa hapus item.</div>
                                </div>
                                <div class="a-card-actions">
                                    <span class="a-badge"><i class="bx bx-message-square-dots"></i> Language</span>
                                </div>
                            </div>

                            <div style="display:grid; gap:8px; max-height:220px; overflow:auto; padding-right:4px;">
                                @forelse($languages as $row)
                                    <div class="a-card" style="box-shadow:none;">
                                        <div class="a-card-inner" style="padding:10px; display:flex; align-items:center; justify-content:space-between; gap:10px;">
                                            <div style="font-weight:900;">{{ $row->label }}</div>
                                            <div style="display:flex; align-items:center; gap:10px;">
                                                <span class="a-badge"><i class="bx bx-pie-chart-alt-2"></i> {{ $row->percentage }}%</span>
                                                <form method="POST" action="{{ route('admin.stats.demographics.destroy', [$activeIsland, $row]) }}"
                                                      onsubmit="return confirm('Hapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="a-btn a-btn-danger a-btn-sm">
                                                        <i class="bx bx-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="a-empty">Belum ada data bahasa.</div>
                                @endforelse
                            </div>

                            <form method="POST" action="{{ route('admin.stats.demographics.store', $activeIsland) }}" class="a-form" style="margin-top:10px;">
                                @csrf
                                <input type="hidden" name="type" value="language">

                                <div class="a-grid">
                                    <div class="a-col-6">
                                        <label class="a-label">Label</label>
                                        <input type="text" name="label" class="a-input" placeholder="Bahasa Minang" required>
                                    </div>
                                    <div class="a-col-6">
                                        <label class="a-label">Persentase (%)</label>
                                        <input type="number" step="0.01" name="percentage" class="a-input" placeholder="0" required>
                                    </div>
                                    <div class="a-col-6">
                                        <label class="a-label">Urutan (opsional)</label>
                                        <input type="number" name="order" class="a-input" placeholder="0">
                                    </div>
                                    <div class="a-col-6">
                                        <div class="a-actions" style="margin-top:26px;">
                                            <button type="submit" class="a-btn a-btn-primary">
                                                <i class="bx bx-plus"></i> Tambah
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="a-help" style="margin-top:10px;">
                                Frontend stats sudah neon + chart dinamis. Admin cukup isi data di sini.
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /a-grid --}}
        </div>
    </div>

</div>
<script>
(function(){
    function syncPair(urlInput, fileInput){
        if(!urlInput || !fileInput) return;

        const hasUrl  = () => (urlInput.value || '').trim().length > 0;
        const hasFile = () => fileInput.files && fileInput.files.length > 0;

        const apply = () => {
            if (hasUrl()) {
                fileInput.disabled = true;
                fileInput.style.opacity = '0.6';
                fileInput.style.pointerEvents = 'none';
            } else {
                fileInput.disabled = false;
                fileInput.style.opacity = '';
                fileInput.style.pointerEvents = '';
            }

            if (hasFile()) {
                urlInput.disabled = true;
                urlInput.style.opacity = '0.6';
                urlInput.style.pointerEvents = 'none';
            } else {
                urlInput.disabled = false;
                urlInput.style.opacity = '';
                urlInput.style.pointerEvents = '';
            }
        };

        urlInput.addEventListener('input', apply);
        fileInput.addEventListener('change', apply);
        apply();
    }

    // berlaku untuk semua form item (tambah + edit banyak)
    document.querySelectorAll('form').forEach(form => {
        const urlInput  = form.querySelector('.js-img-url');
        const fileInput = form.querySelector('.js-img-file');
        if(urlInput && fileInput) syncPair(urlInput, fileInput);
    });
})();
</script>

@endsection
