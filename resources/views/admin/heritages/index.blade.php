{{-- resources/views/admin/heritages/index.blade.php (REPLACE FULL) --}}
@extends('layouts.admin')

@section('title', 'Warisan')

@section('content')
@php
    // ====== Build tribe map langsung dari config, supaya island->tribes bisa di-populate via JS tanpa route tambahan ======
    $tribeMap = [];
    foreach(($islands ?? collect()) as $isl){
        $tribeMap[$isl->id] = array_values(config('tribes.' . ($isl->slug ?? ''), []));
    }

    $selectedIslandIdForJs = $selectedIsland?->id;
    $selectedTribeForJs = $selectedTribeKey ? trim((string) $selectedTribeKey) : '';
@endphp

<div class="a-wrap" id="heritage-admin" data-page="admin-heritage-index">

    <style>
        /* =========================================================
           ADMIN WARISAN (SELARAS UI KIT a-*)
           - Gunakan a-card, a-grid, a-btn agar konsisten admin
           - CSS ini hanya untuk komponen khusus warisan: badge, item card, modal detail
           - LOGIC TIDAK DIUBAH
        ========================================================= */

        #heritage-admin{ color: var(--txt-body); }

        /* ---- header chip (pulau/suku) ---- */
        .ha-chip{
            display:inline-flex;
            align-items:center;
            gap:10px;
            padding:8px 12px;
            border-radius:999px;
            border:1px solid var(--line);
            background: rgba(255,255,255,.02);
            font-weight: 900;
            font-size: 12px;
            box-shadow: 0 14px 35px rgba(0,0,0,.08);
        }

        .ha-chip .k{ color: var(--muted); font-weight: 900; }
        .ha-chip .v{ color: var(--txt-body); font-weight: 1000; }

        /* ---- helper text ---- */
        .ha-help{
            margin-top: 6px;
            font-size: 11px;
            font-weight: 800;
            color: var(--muted);
            line-height: 1.45;
        }

        /* ---- sections grid ---- */
        .ha-grid-3{
            display:grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 12px;
        }
        .ha-col{
            grid-column: span 12 / span 12;
        }
        @media (min-width: 1024px){
            .ha-col{ grid-column: span 4 / span 4; }
        }

        /* ---- category small header ---- */
        .ha-catHead{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap: 10px;
            padding-bottom: 10px;
            margin-bottom: 10px;
            border-bottom: 1px dashed rgba(148,163,184,.28);
        }
        html[data-theme="dark"] .ha-catHead{
            border-bottom: 1px dashed rgba(255,255,255,.10);
        }

        .ha-catTitle{
            font-size: 14px;
            font-weight: 1000;
            color: var(--txt-body);
        }
        .ha-catDesc{
            margin-top: 4px;
            font-size: 12px;
            font-weight: 800;
            color: var(--muted);
            line-height: 1.5;
        }

        /* ---- item cards ---- */
        .hi-card{
            border-radius: 18px;
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: 0 18px 45px rgba(0,0,0,.10);
            padding: 12px;
        }
        html[data-theme="dark"] .hi-card{
            background: rgba(2,6,23,.35);
        }

        .hi-top{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap: 12px;
        }

        .hi-title{
            font-weight: 1000;
            font-size: 13px;
            line-height: 1.25;
            word-break: break-word;
            color: var(--txt-body);
        }

        .hi-meta{
            margin-top: 6px;
            font-size: 11px;
            font-weight: 800;
            color: var(--muted);
            line-height: 1.45;
        }

        .hi-badges{
            display:flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px;
        }
        .hi-badge{
            display:inline-flex;
            align-items:center;
            gap: 6px;
            font-size: 11px;
            font-weight: 900;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255,255,255,.02);
            color: var(--txt-body);
        }

        /* ---- details accordion ---- */
        .ha-details summary{
            list-style:none;
            cursor:pointer;
            user-select:none;
            padding: 10px 10px;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: rgba(255,255,255,.02);
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap: 10px;
            font-weight: 1000;
            font-size: 12px;
        }
        .ha-details summary::-webkit-details-marker{ display:none; }
        .ha-details summary .chev{ transition: transform .2s ease; }
        .ha-details[open] summary .chev{ transform: rotate(180deg); }

        /* ---- modal detail ---- */
        .hd-overlay{
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.62);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 18px;
        }
        html:not([data-theme="dark"]) .hd-overlay{ background: rgba(255,255,255,0.70); }
        .hd-overlay.active{ display:flex; }

        .hd-modal{
            width: min(980px, 100%);
            max-height: 90vh;
            overflow: hidden;
            border-radius: 22px;
            border: 1px solid rgba(249,115,22,.25);
            background: var(--card);
            box-shadow: 0 30px 80px rgba(0,0,0,0.25);
            display: grid;
            grid-template-columns: 1fr;
        }
        html[data-theme="dark"] .hd-modal{
            background: rgba(2,6,23,.55);
        }
        @media (min-width: 900px){
            .hd-modal{ grid-template-columns: 1.1fr 1fr; }
        }

        .hd-img{
            min-height: 260px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .hd-img::after{
            content:"";
            position:absolute;
            inset:0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.06), rgba(0,0,0,0.18));
        }
        .hd-img.fallback{
            display:flex;
            align-items:center;
            justify-content:center;
            font-size: 56px;
            color: rgba(249,115,22,.95);
            background:
                radial-gradient(60% 60% at 30% 20%, rgba(249,115,22,.22), transparent 60%),
                radial-gradient(60% 60% at 70% 40%, rgba(251,146,60,.18), transparent 60%),
                linear-gradient(135deg, rgba(2,6,23,.25), rgba(2,6,23,.10));
        }

        .hd-body{ padding: 18px; overflow:auto; }

        .hd-top{
            display:flex;
            align-items:flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .hd-h2{
            font-size: 18px;
            font-weight: 1000;
            line-height: 1.2;
            margin: 0;
            color: var(--txt-body);
            word-break: break-word;
        }

        .hd-close{
            width: 44px;
            height: 44px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255,255,255,.02);
            display:inline-flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
            transition: transform .15s ease, background .2s ease;
            flex: 0 0 auto;
        }
        .hd-close:hover{ background: rgba(148,163,184,.12); }
        .hd-close:active{ transform: scale(0.98); }

        .hd-p{
            margin: 0;
            font-size: 13px;
            line-height: 1.7;
            color: color-mix(in oklab, var(--txt-body) 88%, transparent);
            white-space: pre-wrap;
            word-break: break-word;
        }

        .hd-meta{
            display:flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 12px;
        }

        .hd-pill{
            font-size: 12px;
            font-weight: 900;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(249,115,22,.22);
            background: rgba(249,115,22,.08);
            color: var(--txt-body);
        }

        .hd-links{
            display:flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 14px;
        }

        .hd-link{
            display:inline-flex;
            align-items:center;
            gap: 10px;
            border-radius: 999px;
            padding: 10px 14px;
            font-weight: 1000;
            font-size: 12px;
            border: 1px solid rgba(249,115,22,.28);
            background: rgba(249,115,22,.10);
            color: var(--txt-body);
            text-decoration: none;
            box-shadow: 0 14px 35px rgba(0,0,0,.10);
            transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
        }
        .hd-link:hover{
            transform: translateY(-1px);
            box-shadow: 0 18px 44px rgba(0,0,0,.16), 0 0 26px rgba(249,115,22,.12);
            filter: saturate(1.04);
        }
        .hd-link svg{ width: 16px; height: 16px; opacity: .95; }
    </style>

    {{-- PAGE HEAD --}}
    <div class="a-head">
        <div class="a-head-left">
            <div class="a-head-title">Warisan (Per Pulau &amp; Suku)</div>
            <div class="a-head-desc">
                Pilih pulau lalu pilih suku. Dalam 1 halaman ini kamu bisa CRUD 3 kategori warisan + header (title &amp; deskripsi).
            </div>
        </div>

        <div class="a-head-right" style="display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap;">
            @if($selectedIsland && $selectedTribeKey)
                <div class="ha-chip" title="Filter aktif">
                    <span class="k">Pulau</span><span class="v">{{ $selectedIsland->name }}</span>
                    <span style="opacity:.55;">•</span>
                    <span class="k">Suku</span><span class="v">{{ $selectedTribeKey }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
        <div class="a-alert a-alert-success" style="margin-top:10px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="a-alert" style="margin-top:10px; border-color: rgba(239,68,68,.35); background: rgba(239,68,68,.10);">
            <div style="font-weight:1000; margin-bottom:6px; color: var(--txt-body);">Ada error:</div>
            <ul style="margin-left: 18px; display:grid; gap:4px;">
                @foreach($errors->all() as $err)
                    <li style="color:#ef4444; font-weight:900;">{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FILTER PULAU + SUKU --}}
    <div class="a-card" data-card data-card-key="admin_heritage_picker" style="margin-top:12px;">
        <div class="a-card-inner">
            <div class="a-card-head">
                <div>
                    <div class="a-card-title">Pilih Pulau + Suku</div>
                    <div class="a-card-desc">Suku akan otomatis terisi dari config <strong>tribes.php</strong>, lalu auto submit.</div>
                </div>
                <div class="a-card-actions">
                    <span class="a-badge"><i class="bx bx-filter-alt"></i> Filter</span>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.heritages.index') }}" id="haFilterForm" class="a-form">
                <div class="a-grid">
                    <div class="a-col-4">
                        <label class="a-label">Pilih Pulau</label>
                        <select name="island_id" id="haIslandSelect" class="a-select">
                            <option value="">-- pilih --</option>
                            @foreach($islands as $island)
                                <option value="{{ $island->id }}" @selected($selectedIsland && $selectedIsland->id === $island->id)>
                                    {{ $island->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="ha-help">Saat pulau diganti, suku otomatis terisi & halaman auto submit.</div>
                    </div>

                    <div class="a-col-4">
                        <label class="a-label">Pilih Suku</label>
                        <select name="tribe" id="haTribeSelect" class="a-select" @disabled(!$selectedIsland)>
                            @if(!$selectedIsland)
                                <option value="">Pilih pulau dulu</option>
                            @else
                                @foreach($tribes as $t)
                                    @php $val = is_string($t) ? trim($t) : (string)$t; @endphp
                                    <option value="{{ $val }}" @selected(trim((string)$selectedTribeKey) === $val)>{{ $val }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="ha-help">Saat suku diganti, halaman juga auto submit.</div>
                    </div>

                    <div class="a-col-4" style="display:flex; align-items:flex-end;">
                        <div class="a-actions" style="margin-top:0;">
                            <button class="a-btn a-btn-primary" type="submit">
                                <i class="bx bx-show"></i> Tampilkan
                            </button>
                            <a href="{{ route('admin.heritages.index') }}" class="a-btn">
                                <i class="bx bx-refresh"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($selectedIsland && $selectedTribeKey)

        {{-- HEADER SUKU --}}
        <div class="a-card" data-card data-card-key="admin_heritage_header" style="margin-top:12px;">
            <div class="a-card-inner">
                <div class="a-card-head">
                    <div>
                        <div class="a-card-title">Header Suku</div>
                        <div class="a-card-desc">
                            Ini untuk title besar + deskripsi besar per suku (tampil di hero Warisan).
                            Disimpan berdasarkan <strong>{{ $selectedIsland->name }}</strong> — <strong>{{ $selectedTribeKey }}</strong>.
                        </div>
                    </div>
                    <div class="a-card-actions">
                        <span class="a-badge"><i class="bx bx-crown"></i> Header</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.heritages.page.save') }}" enctype="multipart/form-data" class="a-form">
                    @csrf

                    <input type="hidden" name="island_id" value="{{ $selectedIsland->id }}">
                    <input type="hidden" name="tribe_key" value="{{ $selectedTribeKey }}">

                    <div class="a-grid">
                        <div class="a-col-6">
                            <label class="a-label">Title Besar</label>
                            <input type="text"
                                   name="hero_title"
                                   value="{{ old('hero_title', $tribePage->hero_title ?? '') }}"
                                   class="a-input"
                                   placeholder="Contoh: Warisan Suku Aceh">
                        </div>

                        <div class="a-col-6">
                            <label class="a-label">Gambar Header (opsional)</label>
                            <input type="file" name="hero_image" class="a-file" accept="image/png,image/jpeg,image/webp">
                            @if(!empty($tribePage?->hero_image))
                                <div class="ha-help">
                                    Saat ini:
                                    <a class="a-link" href="{{ asset('storage/'.$tribePage->hero_image) }}" target="_blank">lihat</a>
                                </div>
                            @endif
                        </div>

                        <div class="a-col-12">
                            <label class="a-label">Deskripsi Besar</label>
                            <textarea name="hero_description" rows="3" class="a-textarea"
                                      placeholder="Deskripsi singkat yang tampil di bagian hero / section warisan...">{{ old('hero_description', $tribePage->hero_description ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="a-actions">
                        <button class="a-btn a-btn-primary" type="submit">
                            <i class="bx bx-save"></i> Simpan Header
                        </button>
                        <div class="ha-help" style="margin-top:0;">
                            Tip: isi yang rapi dan sopan karena ini tampil ke publik.
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 3 CRUD DALAM 1 HALAMAN --}}
        <div class="ha-grid-3" style="margin-top:12px;">
            @foreach($categoryLabels as $catKey => $catLabel)
                <div class="ha-col">
                    <div class="a-card" data-card data-card-key="admin_heritage_cat_{{ $catKey }}">
                        <div class="a-card-inner">

                            <div class="ha-catHead">
                                <div>
                                    <div class="ha-catTitle">{{ $catLabel }}</div>
                                    <div class="ha-catDesc">Tambah / detail / edit / hapus item untuk kategori ini.</div>
                                </div>
                                <span class="a-badge"><i class="bx bx-tag-alt"></i> {{ $catKey }}</span>
                            </div>

                            {{-- FORM TAMBAH ITEM --}}
                            <form method="POST" action="{{ route('admin.heritages.item.store') }}" enctype="multipart/form-data" class="a-form">
                                @csrf
                                <input type="hidden" name="island_id" value="{{ $selectedIsland->id }}">
                                <input type="hidden" name="tribe_key" value="{{ $selectedTribeKey }}">
                                <input type="hidden" name="category" value="{{ $catKey }}">

                                <div class="a-grid">
                                    <div class="a-col-12">
                                        <label class="a-label">Judul</label>
                                        <input type="text" name="title" class="a-input" placeholder="Contoh: Ulos / Rumah Bolon / Sasando ...">
                                    </div>

                                    <div class="a-col-12">
                                        <label class="a-label">Deskripsi (opsional)</label>
                                        <textarea name="description" rows="2" class="a-textarea" placeholder="Deskripsi singkat..."></textarea>
                                    </div>

                                    {{-- ✅ NEW: lokasi + url --}}
                                    <div class="a-col-6">
                                        <label class="a-label">Lokasi (opsional)</label>
                                        <input type="text" name="location" class="a-input" placeholder="Contoh: Banda Aceh / Toraja">
                                        <div class="ha-help">Muncul di card publik & modal jika diisi.</div>
                                    </div>

                                    <div class="a-col-6">
                                        <label class="a-label">URL Detail (opsional)</label>
                                        <input type="url" name="detail_url" class="a-input" placeholder="https://... (wiki/artikel)">
                                        <div class="ha-help">Tombol “Lihat Selengkapnya” muncul jika URL valid.</div>
                                    </div>

                                    <div class="a-col-12">
                                        <label class="a-label">Gambar (opsional)</label>
                                        <input type="file" name="image" class="a-file" accept="image/png,image/jpeg,image/webp">
                                    </div>

                                    <div class="a-col-6">
                                        <label class="a-label">Urutan (opsional)</label>
                                        <input type="number" name="sort_order" min="0" value="0" class="a-input a-input-sm">
                                    </div>

                                    <div class="a-col-6" style="display:flex; align-items:flex-end;">
                                        <button class="a-btn a-btn-primary" type="submit" style="width:100%; justify-content:center;">
                                            <i class="bx bx-plus"></i> Tambah Item
                                        </button>
                                    </div>
                                </div>
                            </form>

                            {{-- LIST ITEM --}}
                            <div class="a-stack" style="margin-top:12px;">
                                @php
                                    $items = $itemsByCategory[$catKey] ?? collect();
                                @endphp

                                @if($items->count() === 0)
                                    <div class="a-empty">Belum ada item di kategori ini.</div>
                                @else
                                    @foreach($items as $item)
                                        @php
                                            $imgUrl = $item->image_path ? asset('storage/'.$item->image_path) : '';
                                            $locVal = $item->location ? trim((string)$item->location) : '';
                                            $urlVal = $item->detail_url ? trim((string)$item->detail_url) : '';
                                        @endphp

                                        <div class="hi-card"
                                             data-ha-item
                                             data-id="{{ $item->id }}"
                                             data-title="{{ e($item->title) }}"
                                             data-desc="{{ e($item->description ?? '') }}"
                                             data-img="{{ e($imgUrl) }}"
                                             data-cat="{{ e($catLabel) }}"
                                             data-sort="{{ (int)$item->sort_order }}"
                                             data-loc="{{ e($locVal) }}"
                                             data-url="{{ e($urlVal) }}">

                                            <div class="hi-top">
                                                <div style="min-width:0;">
                                                    <div class="hi-title">{{ $item->title }}</div>

                                                    <div class="hi-meta">
                                                        sort: <span style="font-weight:1000; color: var(--txt-body);">{{ $item->sort_order }}</span>
                                                        @if($item->image_path)
                                                            <span style="opacity:.55;">•</span> ada gambar
                                                        @endif
                                                        @if($locVal !== '')
                                                            <span style="opacity:.55;">•</span> ada lokasi
                                                        @endif
                                                        @if($urlVal !== '')
                                                            <span style="opacity:.55;">•</span> ada link
                                                        @endif
                                                    </div>

                                                    <div class="hi-badges">
                                                        @if($locVal !== '')
                                                            <span class="hi-badge" title="{{ $locVal }}">📍 {{ \Illuminate\Support\Str::limit($locVal, 18) }}</span>
                                                        @endif
                                                        @if($urlVal !== '')
                                                            <span class="hi-badge" title="{{ $urlVal }}">🔗 link</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="a-rowActions">
                                                    <button type="button" class="a-btn a-btn-sm" data-ha-detail>
                                                        <i class="bx bx-search-alt-2"></i> Detail
                                                    </button>

                                                    <form method="POST"
                                                          action="{{ route('admin.heritages.item.destroy', $item) }}"
                                                          onsubmit="return confirm('Hapus item ini?')"
                                                          class="a-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="a-btn a-btn-danger a-btn-sm" type="submit">
                                                            <i class="bx bx-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            {{-- Edit accordion --}}
                                            <details class="ha-details" style="margin-top:10px;">
                                                <summary>
                                                    <span>Edit item</span>
                                                    <svg class="chev" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                         stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="6 9 12 15 18 9"></polyline>
                                                    </svg>
                                                </summary>

                                                <form method="POST"
                                                      action="{{ route('admin.heritages.item.update', $item) }}"
                                                      enctype="multipart/form-data"
                                                      class="a-form a-form-tight"
                                                      style="margin-top:10px;">
                                                    @csrf
                                                    @method('PATCH')

                                                    <div class="a-grid">
                                                        <div class="a-col-12">
                                                            <label class="a-label a-label-sm">Judul</label>
                                                            <input type="text" name="title" value="{{ $item->title }}" class="a-input">
                                                        </div>

                                                        <div class="a-col-12">
                                                            <label class="a-label a-label-sm">Deskripsi</label>
                                                            <textarea name="description" rows="2" class="a-textarea">{{ $item->description }}</textarea>
                                                        </div>

                                                        {{-- ✅ NEW: lokasi + url --}}
                                                        <div class="a-col-6">
                                                            <label class="a-label a-label-sm">Lokasi (opsional)</label>
                                                            <input type="text" name="location" value="{{ $item->location }}" class="a-input" placeholder="Contoh: Toraja / Bali">
                                                        </div>

                                                        <div class="a-col-6">
                                                            <label class="a-label a-label-sm">URL Detail (opsional)</label>
                                                            <input type="url" name="detail_url" value="{{ $item->detail_url }}" class="a-input" placeholder="https://...">
                                                        </div>

                                                        <div class="a-col-12">
                                                            <label class="a-label a-label-sm">Ganti gambar (opsional)</label>
                                                            <input type="file" name="image" class="a-file" accept="image/png,image/jpeg,image/webp">
                                                            @if($item->image_path)
                                                                <div class="ha-help">
                                                                    Saat ini:
                                                                    <a class="a-link" href="{{ asset('storage/'.$item->image_path) }}" target="_blank">lihat</a>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="a-col-6">
                                                            <label class="a-label a-label-sm">Urutan</label>
                                                            <input type="number" name="sort_order" min="0" value="{{ $item->sort_order }}" class="a-input a-input-sm">
                                                        </div>

                                                        <div class="a-col-6" style="display:flex; align-items:flex-end;">
                                                            <button class="a-btn a-btn-primary" type="submit" style="width:100%; justify-content:center;">
                                                                <i class="bx bx-save"></i> Simpan Perubahan
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </details>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        <div class="a-card" style="margin-top:12px;">
            <div class="a-card-inner">
                <div class="a-empty">Pilih pulau &amp; suku dulu untuk mulai mengelola warisan.</div>
            </div>
        </div>
    @endif

    {{-- MODAL DETAIL (GLOBAL) --}}
    <div class="hd-overlay" id="hdOverlay" aria-hidden="true">
        <div class="hd-modal" role="dialog" aria-modal="true" aria-label="Detail Warisan">
            <div class="hd-img fallback" id="hdImg">🏛️</div>

            <div class="hd-body">
                <div class="hd-top">
                    <h2 class="hd-h2" id="hdTitle">Detail</h2>
                    <button type="button" class="hd-close" id="hdClose" aria-label="Tutup">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <p class="hd-p" id="hdDesc">-</p>

                <div class="hd-meta">
                    <span class="hd-pill" id="hdCat">Kategori</span>
                    <span class="hd-pill" id="hdSort">sort: 0</span>
                    <span class="hd-pill" id="hdId">#0</span>
                    <span class="hd-pill" id="hdLoc" style="display:none;">📍 lokasi</span>
                </div>

                <div class="hd-links" id="hdLinks" style="display:none;">
                    <a class="hd-link" id="hdUrlBtn" href="#" target="_blank" rel="noopener noreferrer">
                        Lihat Selengkapnya
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H18m0 0v4.5M18 6l-9 9"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 7.5H7.8A2.3 2.3 0 005.5 9.8v6.4A2.3 2.3 0 007.8 18.5h6.4a2.3 2.3 0 002.3-2.3V13.5"/>
                        </svg>
                    </a>
                </div>

                <div class="ha-help" style="margin-top:14px;">
                    Ini hanya tampilan detail. Untuk mengubah, gunakan panel <b>Edit item</b> di bawah card.
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            // ====== Tribe mapping injected from blade ======
            const TRIBE_MAP = @json($tribeMap);
            const selectedIslandId = @json($selectedIslandIdForJs);
            const selectedTribe = @json($selectedTribeForJs);

            const form = document.getElementById('haFilterForm');
            const islandSelect = document.getElementById('haIslandSelect');
            const tribeSelect  = document.getElementById('haTribeSelect');

            function clearTribeSelect(message = 'Pilih pulau dulu') {
                tribeSelect.innerHTML = '';
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = message;
                tribeSelect.appendChild(opt);
                tribeSelect.disabled = true;
            }

            function fillTribes(islandId, preferValue = '') {
                const tribes = (TRIBE_MAP[String(islandId)] || TRIBE_MAP[islandId] || []).map(t => (typeof t === 'string' ? t.trim() : String(t)));
                tribeSelect.innerHTML = '';

                if (!tribes.length) {
                    clearTribeSelect('Tidak ada suku untuk pulau ini');
                    return { selected: '' };
                }

                tribes.forEach((t) => {
                    const opt = document.createElement('option');
                    opt.value = t;
                    opt.textContent = t;
                    tribeSelect.appendChild(opt);
                });

                tribeSelect.disabled = false;

                // pilih preferValue kalau ada & valid, kalau tidak pilih suku pertama
                const cleanPref = (preferValue || '').trim();
                const finalValue = tribes.includes(cleanPref) ? cleanPref : tribes[0];
                tribeSelect.value = finalValue;

                return { selected: finalValue };
            }

            // Init on load (jaga-jaga state)
            if (islandSelect && tribeSelect) {
                if (!islandSelect.value) {
                    clearTribeSelect();
                } else {
                    fillTribes(islandSelect.value, selectedTribe);
                }

                // Auto populate & auto submit saat pulau berubah
                islandSelect.addEventListener('change', () => {
                    const islandId = islandSelect.value;
                    if (!islandId) {
                        clearTribeSelect();
                        return;
                    }
                    fillTribes(islandId, '');
                    if (form) form.submit();
                });

                // kalau user ganti suku -> auto submit juga
                tribeSelect.addEventListener('change', () => {
                    if (form) form.submit();
                });
            }

            // ====== DETAIL MODAL ======
            const overlay = document.getElementById('hdOverlay');
            const closeBtn = document.getElementById('hdClose');
            const imgBox = document.getElementById('hdImg');
            const titleEl = document.getElementById('hdTitle');
            const descEl = document.getElementById('hdDesc');
            const catEl = document.getElementById('hdCat');
            const sortEl = document.getElementById('hdSort');
            const idEl = document.getElementById('hdId');
            const locEl = document.getElementById('hdLoc');

            const linksWrap = document.getElementById('hdLinks');
            const urlBtn = document.getElementById('hdUrlBtn');

            let lastFocus = null;

            function safeHttpUrl(raw) {
                const s = (raw || '').trim();
                if (!s) return '';
                try {
                    const u = new URL(s, window.location.origin);
                    if (u.protocol === 'http:' || u.protocol === 'https:') return u.href;
                    return '';
                } catch {
                    return '';
                }
            }

            function openDetail(data) {
                if (!overlay) return;

                titleEl.textContent = data.title || 'Detail';
                descEl.textContent = (data.desc && data.desc.trim() !== '') ? data.desc : 'Deskripsi belum diisi.';
                catEl.textContent = data.cat || 'Kategori';
                sortEl.textContent = 'sort: ' + (data.sort ?? 0);
                idEl.textContent = '#' + (data.id ?? 0);

                // lokasi (opsional)
                const loc = (data.loc || '').trim();
                if (loc) {
                    locEl.style.display = '';
                    locEl.textContent = '📍 ' + loc;
                } else {
                    locEl.style.display = 'none';
                    locEl.textContent = '📍 lokasi';
                }

                // link (opsional)
                const safeUrl = safeHttpUrl(data.url || '');
                if (safeUrl) {
                    linksWrap.style.display = '';
                    urlBtn.href = safeUrl;
                } else {
                    linksWrap.style.display = 'none';
                    urlBtn.href = '#';
                }

                // image
                const url = (data.img || '').trim();
                if (url) {
                    imgBox.classList.remove('fallback');
                    imgBox.style.backgroundImage = `url('${url.replace(/'/g, "\\'")}')`;
                    imgBox.textContent = '';

                    const probe = new Image();
                    probe.onerror = () => {
                        imgBox.classList.add('fallback');
                        imgBox.style.backgroundImage = '';
                        imgBox.textContent = '🏛️';
                    };
                    probe.src = url;
                } else {
                    imgBox.classList.add('fallback');
                    imgBox.style.backgroundImage = '';
                    imgBox.textContent = '🏛️';
                }

                overlay.classList.add('active');
                overlay.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';

                setTimeout(() => closeBtn?.focus(), 50);
            }

            function closeDetail() {
                if (!overlay) return;
                overlay.classList.remove('active');
                overlay.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                if (lastFocus) setTimeout(() => lastFocus.focus(), 50);
            }

            document.querySelectorAll('[data-ha-item]').forEach(card => {
                const btn = card.querySelector('[data-ha-detail]');
                if (!btn) return;

                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    lastFocus = btn;

                    openDetail({
                        id: card.getAttribute('data-id'),
                        title: card.getAttribute('data-title'),
                        desc: card.getAttribute('data-desc'),
                        img: card.getAttribute('data-img'),
                        cat: card.getAttribute('data-cat'),
                        sort: card.getAttribute('data-sort'),
                        loc: card.getAttribute('data-loc'),
                        url: card.getAttribute('data-url'),
                    });
                });
            });

            closeBtn?.addEventListener('click', closeDetail);

            overlay?.addEventListener('click', (e) => {
                if (e.target === overlay) closeDetail();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && overlay?.classList.contains('active')) {
                    closeDetail();
                }
            });
        })();
    </script>

</div>
@endsection
