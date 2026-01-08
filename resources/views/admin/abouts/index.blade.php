{{-- resources/views/admin/abouts/index.blade.php (REPLACE FULL) --}}
@extends('layouts.admin')

@section('title', 'About Suku')

@section('content')
@php
    $ready = !empty($selectedIslandId) && !empty($selectedTribeKey);
@endphp

<div class="a-wrap" data-page="admin-abouts-index">

    {{-- PAGE HEAD --}}
    <div class="a-head">
        <div class="a-head-left">
            <div class="a-head-title">About Suku (Pulau + Suku)</div>
            <div class="a-head-desc">
                Header About disimpan <strong>sekali</strong> per Pulau + Suku, tanpa gambar.
                Item About bisa <strong>banyak</strong>, gambar item <strong>upload manual</strong>, points opsional.
            </div>
        </div>

        <div class="a-head-right">
            @if(session('success'))
                <div class="a-alert a-alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>

    {{-- ===== CARD: PICKER (FILTER) ===== --}}
    <div class="a-card" data-card data-card-key="admin_abouts_picker">
        <div class="a-card-inner">
            <div class="a-card-head">
                <div>
                    <div class="a-card-title">Filter Pulau + Suku</div>
                    <div class="a-card-desc">
                        Pilih pulau lalu pilih suku. Halaman akan load data sesuai pilihan.
                    </div>
                </div>
                <div class="a-card-actions">
                    <span class="a-badge"><i class="bx bx-filter-alt"></i> Filter</span>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.abouts.index') }}" class="a-form">
                <div class="a-grid">

                    <div class="a-col-4">
                        <label class="a-label" for="islandSelect">Pilih Pulau</label>
                        <select name="island_id" id="islandSelect" class="a-select">
                            <option value="">Pilih pulau...</option>
                            @foreach($islands as $island)
                                <option value="{{ $island->id }}"
                                        data-slug="{{ $island->slug }}"
                                        @selected((string)request('island_id') === (string)$island->id)>
                                    {{ $island->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('island_id') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-4">
                        <label class="a-label" for="tribeSelect">Pilih Suku</label>
                        <select name="tribe_key" id="tribeSelect" class="a-select" disabled>
                            <option value="">Pilih suku...</option>
                        </select>
                        <div class="a-help">
                            Jika daftar kosong, berarti suku untuk pulau itu belum ada di config <strong>tribes.php</strong>.
                        </div>
                        @error('tribe_key') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-4">
                        <div class="a-actions">
                            <button type="submit" class="a-btn a-btn-primary">
                                <i class="bx bx-download"></i> Load
                            </button>

                            <a href="{{ route('admin.abouts.index') }}" class="a-btn">
                                <i class="bx bx-refresh"></i> Reset
                            </a>
                        </div>

                        @unless($ready)
                            <div class="a-note" style="margin-top:10px;">
                                Pilih Pulau + Suku dulu lalu klik <strong>Load</strong>.
                            </div>
                        @endunless
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- ===== CARD: HEADER ABOUT (PAGE) ===== --}}
    <div class="a-card" data-card data-card-key="admin_abouts_header">
        <div class="a-card-inner">
            <div class="a-card-head">
                <div>
                    <div class="a-card-title">Header About (User)</div>
                    <div class="a-card-desc">
                        Berisi: <strong>Label kecil</strong>, <strong>Judul besar</strong>, <strong>Deskripsi besar</strong>.
                        Tanpa gambar. Link opsional untuk tombol <strong>"Selengkapnya"</strong>.
                    </div>
                </div>
                <div class="a-card-actions">
                    <span class="a-badge"><i class="bx bx-pen"></i> Header</span>
                </div>
            </div>

            <form action="{{ route('admin.abouts.page.save') }}" method="POST" class="a-form">
                @csrf

                <input type="hidden" name="island_id" id="pageIslandId" value="{{ $selectedIslandId ?? '' }}">
                <input type="hidden" name="tribe_key" id="pageTribeKey" value="{{ $selectedTribeKey ?? '' }}">

                <div class="a-grid">
                    <div class="a-col-6">
                        <label class="a-label" for="labelSmall">Label Kecil (opsional)</label>
                        <input type="text" name="label_small" id="labelSmall"
                               value="{{ old('label_small', $aboutPage->label_small ?? '') }}"
                               placeholder="Contoh: MENGENAL ACEH"
                               class="a-input">
                        @error('label_small') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-6">
                        <label class="a-label" for="heroTitle">Judul Besar (opsional)</label>
                        <input type="text" name="hero_title" id="heroTitle"
                               value="{{ old('hero_title', $aboutPage->hero_title ?? '') }}"
                               placeholder="Contoh: Apa itu Aceh?"
                               class="a-input">
                        @error('hero_title') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-12">
                        <label class="a-label" for="heroDescription">Deskripsi Besar (opsional)</label>
                        <textarea name="hero_description" id="heroDescription" rows="4"
                                  placeholder="Paragraf besar untuk header about..."
                                  class="a-textarea">{{ old('hero_description', $aboutPage->hero_description ?? '') }}</textarea>
                        @error('hero_description') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-12">
                        <label class="a-label" for="pageMoreLink">Link Selengkapnya (opsional)</label>
                        <input type="url" name="more_link" id="pageMoreLink"
                               value="{{ old('more_link', $aboutPage->more_link ?? '') }}"
                               placeholder="https://contoh.com/artikel-lengkap"
                               class="a-input">
                        <div class="a-help">
                            Jika kosong, tombol <strong>"Selengkapnya"</strong> tidak tampil di user.
                        </div>
                        @error('more_link') <div class="a-err">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="a-actions">
                    <button type="submit"
                            class="a-btn a-btn-primary"
                            @disabled(!$ready)>
                        <i class="bx bx-save"></i> Simpan Header
                    </button>

                    @unless($ready)
                        <div class="a-note">
                            Pilih Pulau + Suku dulu lalu klik <strong>Load</strong>.
                        </div>
                    @endunless
                </div>
            </form>
        </div>
    </div>

    {{-- ===== CARD: TAMBAH ITEM ABOUT ===== --}}
    <div class="a-card" data-card data-card-key="admin_abouts_add_item">
        <div class="a-card-inner">
            <div class="a-card-head">
                <div>
                    <div class="a-card-title">Tambah Item About</div>
                    <div class="a-card-desc">
                        Title opsional, points opsional (isi per baris), gambar opsional (upload), dan link selengkapnya opsional.
                    </div>
                </div>
                <div class="a-card-actions">
                    <span class="a-badge"><i class="bx bx-plus-circle"></i> Tambah</span>
                </div>
            </div>

            <form action="{{ route('admin.abouts.item.store') }}" method="POST" enctype="multipart/form-data" class="a-form">
                @csrf

                <input type="hidden" name="island_id" id="itemIslandId" value="{{ $selectedIslandId ?? '' }}">
                <input type="hidden" name="tribe_key" id="itemTribeKey" value="{{ $selectedTribeKey ?? '' }}">

                <div class="a-grid">
                    <div class="a-col-8">
                        <label class="a-label" for="newTitle">Title (opsional)</label>
                        <input id="newTitle" type="text" name="title" value="{{ old('title') }}"
                               placeholder="Contoh: Tradisi, Bahasa, atau Kehidupan Sosial"
                               class="a-input">
                        @error('title') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-4">
                        <label class="a-label" for="newOrder">Urutan (opsional)</label>
                        <input id="newOrder" type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                               class="a-input">
                        @error('sort_order') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-12">
                        <label class="a-label" for="newDesc">Deskripsi (wajib)</label>
                        <textarea id="newDesc" name="description" rows="4"
                                  class="a-textarea"
                                  placeholder="Isi deskripsi item...">{{ old('description') }}</textarea>
                        @error('description') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-12">
                        <label class="a-label" for="newPoints">Points (opsional) — 1 baris = 1 poin</label>
                        <textarea id="newPoints" name="points" rows="4"
                                  class="a-textarea"
                                  placeholder="Contoh:
Asal-usul dan persebaran
Ciri khas pakaian
Kesenian tradisional">{{ old('points') }}</textarea>
                        <div class="a-help">Jika kosong, points tidak tampil.</div>
                        @error('points') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-6">
                        <label class="a-label" for="newImg">Gambar (opsional) — Upload</label>
                        <input id="newImg" type="file" name="image" accept="image/png,image/jpeg,image/webp" class="a-file">
                        <div class="a-help">Upload JPG/PNG/WEBP (max 4MB).</div>
                        @error('image') <div class="a-err">{{ $message }}</div> @enderror
                    </div>

                    <div class="a-col-6">
                        <label class="a-label" for="newMore">Link Selengkapnya (opsional)</label>
                        <input id="newMore" type="url" name="more_link" value="{{ old('more_link') }}"
                               placeholder="https://contoh.com/..."
                               class="a-input">
                        @error('more_link') <div class="a-err">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="a-actions">
                    <button type="submit"
                            class="a-btn a-btn-primary"
                            @disabled(!$ready)>
                        <i class="bx bx-plus"></i> Tambah Item
                    </button>

                    @unless($ready)
                        <div class="a-note">
                            Pilih Pulau + Suku dulu lalu klik <strong>Load</strong>.
                        </div>
                    @endunless
                </div>
            </form>
        </div>
    </div>

    {{-- ===== CARD: LIST ITEMS ===== --}}
    <div class="a-card" data-card data-card-key="admin_abouts_list">
        <div class="a-card-inner">
            <div class="a-card-head">
                <div>
                    <div class="a-card-title">Daftar Item About</div>
                    <div class="a-card-desc">Edit item. Gambar bisa diganti (upload) atau dihapus.</div>
                </div>
                <div class="a-card-actions">
                    <span class="a-badge"><i class="bx bx-list-ul"></i> Total: {{ $aboutItems->count() }}</span>
                </div>
            </div>

            @if(!$ready)
                <div class="a-empty">
                    Pilih Pulau + Suku lalu klik <strong>Load</strong>.
                </div>
            @elseif($aboutItems->isEmpty())
                <div class="a-empty">
                    Belum ada item About untuk Pulau+Suku ini.
                </div>
            @else
                {{-- LIST STACK --}}
                <div class="a-stack">
                    @foreach($aboutItems as $it)
                        <div class="a-card" style="box-shadow:none;" data-searchable>
                            <div class="a-card-inner">

                                <div class="a-card-head">
                                    <div>
                                        <div class="a-card-title">
                                            Item #{{ $it->id }} — Order {{ $it->sort_order }}
                                        </div>
                                        <div class="a-card-desc">
                                            Gunakan form di bawah untuk mengubah konten item ini.
                                        </div>
                                    </div>
                                    <div class="a-card-actions">
                                        <span class="a-badge"><i class="bx bx-edit"></i> Edit</span>
                                    </div>
                                </div>

                                <form action="{{ route('admin.abouts.item.update', $it) }}" method="POST" enctype="multipart/form-data" class="a-form a-form-tight">
                                    @csrf
                                    @method('PATCH')

                                    <div class="a-grid">
                                        <div class="a-col-8">
                                            <label class="a-label">Title (opsional)</label>
                                            <input type="text" name="title" value="{{ old('title', $it->title) }}"
                                                   class="a-input">
                                        </div>

                                        <div class="a-col-4">
                                            <label class="a-label">Urutan</label>
                                            <input type="number" name="sort_order" value="{{ old('sort_order', $it->sort_order) }}"
                                                   class="a-input">
                                        </div>

                                        <div class="a-col-12">
                                            <label class="a-label">Deskripsi (wajib)</label>
                                            <textarea name="description" rows="3" class="a-textarea">{{ old('description', $it->description) }}</textarea>
                                        </div>

                                        <div class="a-col-12">
                                            <label class="a-label">Points (opsional) — 1 baris = 1 poin</label>
                                            <textarea name="points" rows="4" class="a-textarea">{{ old('points', $it->points) }}</textarea>
                                        </div>

                                        <div class="a-col-6">
                                            <label class="a-label">Ganti Gambar (opsional) — Upload</label>
                                            <input type="file" name="image" accept="image/png,image/jpeg,image/webp" class="a-file">

                                            @if($it->image)
                                                <div style="margin-top:10px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                                                    <img src="{{ $it->image }}" alt="img" style="width:110px;height:80px;object-fit:cover;border-radius:14px;border:1px solid var(--line);">
                                                    <label class="a-badge" style="background:rgba(255,255,255,.02); border-color: var(--line);">
                                                        <input type="checkbox" name="remove_image" value="1" style="transform:translateY(1px);">
                                                        <span>Hapus gambar ini</span>
                                                    </label>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="a-col-6">
                                            <label class="a-label">Link Selengkapnya (opsional)</label>
                                            <input type="url" name="more_link" value="{{ old('more_link', $it->more_link) }}"
                                                   class="a-input">
                                        </div>
                                    </div>

                                    <div class="a-actions">
                                        <button type="submit" class="a-btn a-btn-primary">
                                            <i class="bx bx-save"></i> Update
                                        </button>
                                    </div>
                                </form>

                                <form action="{{ route('admin.abouts.item.destroy', $it) }}" method="POST"
                                      onsubmit="return confirm('Hapus item ini?');" style="margin-top:10px;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="a-btn a-btn-danger">
                                        <i class="bx bx-trash"></i> Hapus Item
                                    </button>
                                </form>

                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

</div>

{{-- Data untuk JS global (admin-sidebar.js) --}}
<script>
window.__ABOUTS_INDEX__ = {
  tribesConfig: @json($tribesConfig ?? []),
  selectedTribeFromQuery: @json(request('tribe_key')),
  lookupUrlBase: @json(url('/admin/about-pages/lookup'))
};
</script>
@endsection
