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

<div class="max-w-6xl mx-auto px-4 py-6 space-y-6" id="heritage-admin">

    <style>
        /* =========================================================
           ADMIN WARISAN UI (LIGHT/DARK SAFE)
           - Picker pulau/suku: auto populate
           - CRUD 3 kategori
           - Modal detail sebelum edit
           - ✅ NEW: location + detail_url input + tampil di modal
           - UI lebih enak: section header, action hint, spacing rapi
        ========================================================= */

        #heritage-admin { color: var(--txt-body); }

        /* ---------- cards ---------- */
        .ha-card{
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(2, 6, 23, 0.35);
            background: color-mix(in oklab, var(--card) 55%, transparent);
            backdrop-filter: blur(14px) saturate(140%);
            -webkit-backdrop-filter: blur(14px) saturate(140%);
            box-shadow: 0 16px 40px rgba(0,0,0,0.18);
        }
        html:not([data-theme="dark"]) .ha-card{
            border: 1px solid rgba(15, 23, 42, 0.12);
            background: rgba(255, 255, 255, 0.65);
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
        }

        .ha-title{
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .ha-sub{
            color: color-mix(in oklab, var(--txt-body) 55%, transparent);
            font-size: .92rem;
            line-height: 1.6;
        }

        /* ---------- inputs ---------- */
        .ha-label{
            display:block;
            font-size:12px;
            font-weight:700;
            margin-bottom:6px;
            color: color-mix(in oklab, var(--txt-body) 78%, transparent);
        }

        .ha-input, .ha-select, .ha-textarea, .ha-file{
            width:100%;
            border-radius:12px;
            border:1px solid rgba(148, 163, 184, 0.22);
            background: rgba(2, 6, 23, 0.25);
            background: color-mix(in oklab, var(--card) 35%, transparent);
            color: var(--txt-body);
            padding:10px 12px;
            outline:none;
            transition:border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }
        html:not([data-theme="dark"]) .ha-input,
        html:not([data-theme="dark"]) .ha-select,
        html:not([data-theme="dark"]) .ha-textarea,
        html:not([data-theme="dark"]) .ha-file{
            background: rgba(255,255,255,0.7);
            border:1px solid rgba(15, 23, 42, 0.14);
        }

        .ha-input:focus, .ha-select:focus, .ha-textarea:focus{
            border-color: rgba(var(--brand-rgb, 249, 115, 22), 0.55);
            box-shadow: 0 0 0 4px rgba(var(--brand-rgb, 249, 115, 22), 0.14);
        }
        .ha-select:disabled{ opacity:.6; cursor:not-allowed; }

        .ha-help{
            font-size: 11px;
            margin-top: 8px;
            color: color-mix(in oklab, var(--txt-body) 55%, transparent);
            line-height: 1.55;
        }

        /* ---------- buttons ---------- */
        .ha-btn{
            border-radius:12px;
            padding:10px 14px;
            font-weight:800;
            transition: transform .15s ease, box-shadow .2s ease, filter .2s ease, background .2s ease;
            user-select:none;
        }
        .ha-btn:active{ transform: translateY(1px) scale(0.99); }

        .ha-btn-primary{
            background: linear-gradient(90deg, rgba(var(--brand-rgb, 249, 115, 22), .95), rgba(var(--brand-2-rgb, 251, 146, 60), .95));
            color:white;
            box-shadow: 0 14px 30px rgba(var(--brand-rgb, 249, 115, 22), 0.18);
        }
        .ha-btn-primary:hover{
            filter: brightness(1.02);
            box-shadow: 0 18px 40px rgba(var(--brand-rgb, 249, 115, 22), 0.24);
        }

        .ha-btn-soft{
            background: rgba(148, 163, 184, 0.12);
            border: 1px solid rgba(148, 163, 184, 0.18);
            color: var(--txt-body);
        }
        .ha-btn-soft:hover{ background: rgba(148, 163, 184, 0.18); }

        .ha-btn-danger{
            background: rgba(239, 68, 68, 0.16);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #fecaca;
        }
        html:not([data-theme="dark"]) .ha-btn-danger{ color: rgb(127, 29, 29); }
        .ha-btn-danger:hover{ background: rgba(239, 68, 68, 0.22); }

        .ha-chip{
            display:inline-flex;
            align-items:center;
            gap:.5rem;
            padding:6px 10px;
            border-radius:999px;
            font-size:12px;
            font-weight:800;
            border:1px solid rgba(var(--brand-rgb, 249, 115, 22), 0.22);
            background: rgba(var(--brand-rgb, 249, 115, 22), 0.10);
            color: var(--txt-body);
        }

        .ha-grid-3{
            display:grid;
            grid-template-columns: 1fr;
            gap: 14px;
        }
        @media (min-width: 1024px){
            .ha-grid-3{ grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        /* ---------- category header small separator ---------- */
        .ha-cat-head{
            padding-bottom: 10px;
            border-bottom: 1px dashed rgba(148, 163, 184, 0.20);
        }
        html:not([data-theme="dark"]) .ha-cat-head{
            border-bottom: 1px dashed rgba(15, 23, 42, 0.14);
        }

        /* ---------- item card ---------- */
        .hi-card{
            border-radius: 16px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(2, 6, 23, 0.22);
            background: color-mix(in oklab, var(--card) 42%, transparent);
            padding: 12px;
        }
        html:not([data-theme="dark"]) .hi-card{
            background: rgba(255,255,255,0.75);
            border: 1px solid rgba(15, 23, 42, 0.10);
        }

        .hi-title{
            font-weight: 900;
            font-size: 14px;
            line-height: 1.25;
            word-break: break-word;
        }
        .hi-meta{
            font-size: 11px;
            color: color-mix(in oklab, var(--txt-body) 55%, transparent);
            line-height: 1.5;
        }

        .hi-badges{
            display:flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px;
        }
        .hi-badge{
            font-size: 11px;
            font-weight: 900;
            padding: 5px 8px;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(148, 163, 184, 0.10);
        }
        html:not([data-theme="dark"]) .hi-badge{
            border: 1px solid rgba(15, 23, 42, 0.10);
            background: rgba(15, 23, 42, 0.04);
        }

        /* ---------- modal detail ---------- */
        .hd-overlay{
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 18px;
        }
        html:not([data-theme="dark"]) .hd-overlay{
            background: rgba(255,255,255,0.72);
        }
        .hd-overlay.active{ display:flex; }

        .hd-modal{
            width: min(980px, 100%);
            max-height: 90vh;
            overflow: hidden;
            border-radius: 22px;
            border: 1px solid rgba(var(--brand-rgb, 249, 115, 22), 0.22);
            background: rgba(2, 6, 23, 0.35);
            background: color-mix(in oklab, var(--card) 55%, transparent);
            box-shadow: 0 30px 80px rgba(0,0,0,0.25);
            display: grid;
            grid-template-columns: 1fr;
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
            font-size: 52px;
            color: rgba(var(--brand-rgb, 249, 115, 22), 0.9);
            background:
                radial-gradient(60% 60% at 30% 20%, rgba(var(--brand-rgb, 249, 115, 22), .25), transparent 60%),
                radial-gradient(60% 60% at 70% 40%, rgba(var(--brand-2-rgb, 251, 146, 60), .20), transparent 60%),
                linear-gradient(135deg, rgba(2,6,23,.40), rgba(2,6,23,.20));
        }

        .hd-body{ padding: 18px 18px 16px; overflow:auto; }

        .hd-top{
            display:flex;
            align-items:flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .hd-h2{
            font-size: 18px;
            font-weight: 900;
            line-height: 1.2;
            word-break: break-word;
            margin: 0;
        }

        .hd-close{
            width: 44px;
            height: 44px;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.22);
            background: rgba(148, 163, 184, 0.12);
            display:inline-flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
            transition: transform .15s ease, background .2s ease;
            flex: 0 0 auto;
        }
        .hd-close:hover{ background: rgba(148, 163, 184, 0.18); }
        .hd-close:active{ transform: scale(0.98); }

        .hd-p{
            margin: 0;
            font-size: 13px;
            line-height: 1.7;
            color: color-mix(in oklab, var(--txt-body) 85%, transparent);
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
            font-weight: 800;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(var(--brand-rgb, 249, 115, 22), 0.20);
            background: rgba(var(--brand-rgb, 249, 115, 22), 0.09);
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
            border-radius: 12px;
            padding: 10px 12px;
            font-weight: 900;
            font-size: 12px;
            border: 1px solid rgba(var(--brand-rgb, 249, 115, 22), 0.28);
            background: rgba(255,255,255,0.92);
            color: rgba(var(--brand-rgb, 249, 115, 22), 1);
            text-decoration: none;
            box-shadow: 0 14px 34px rgba(0,0,0,.14);
            transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
        }
        html[data-theme="dark"] .hd-link{
            background: rgba(2,6,23,.62);
            color: #ff8c42;
            box-shadow: 0 18px 40px rgba(0,0,0,.35);
            border-color: rgba(var(--brand-rgb, 249, 115, 22), 0.22);
        }
        .hd-link:hover{
            transform: translateY(-1px);
            box-shadow: 0 18px 44px rgba(0,0,0,.18), 0 0 26px rgba(249,115,22,.12);
            filter: saturate(1.04);
        }
        .hd-link svg{ width: 16px; height: 16px; opacity: .95; }

        /* ---------- accordion edit ---------- */
        .ha-details summary{
            list-style:none;
            cursor:pointer;
            user-select:none;
        }
        .ha-details summary::-webkit-details-marker{ display:none; }
        .ha-details summary .chev{ transition: transform .2s ease; }
        .ha-details[open] summary .chev{ transform: rotate(180deg); }
    </style>

    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="ha-title">Warisan (Per Pulau & Suku)</h1>
            <p class="ha-sub mt-1">
                Pilih pulau lalu pilih suku. Dalam 1 halaman ini kamu bisa CRUD 3 kategori warisan + header (title & deskripsi).
            </p>
        </div>

        @if($selectedIsland && $selectedTribeKey)
            <div class="ha-chip">
                <span>Pulau:</span>
                <span class="font-black">{{ $selectedIsland->name }}</span>
                <span class="opacity-70">—</span>
                <span>Suku:</span>
                <span class="font-black">{{ $selectedTribeKey }}</span>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="ha-card px-4 py-3" style="border-color: rgba(16, 185, 129, 0.35); background: rgba(16, 185, 129, 0.08);">
            <div class="text-sm" style="color: rgba(167, 243, 208, 0.95);">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="ha-card px-4 py-3" style="border-color: rgba(239, 68, 68, 0.35); background: rgba(239, 68, 68, 0.08);">
            <div class="text-sm" style="color: rgba(254, 202, 202, 0.95);">
                <div class="font-black mb-1">Ada error:</div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- FILTER PULAU + SUKU --}}
    <form method="GET"
          action="{{ route('admin.heritages.index') }}"
          class="ha-card p-4"
          id="haFilterForm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="ha-label">Pilih Pulau</label>
                <select name="island_id" id="haIslandSelect" class="ha-select">
                    <option value="">-- pilih --</option>
                    @foreach($islands as $island)
                        <option value="{{ $island->id }}" @selected($selectedIsland && $selectedIsland->id === $island->id)>
                            {{ $island->name }}
                        </option>
                    @endforeach
                </select>
                <div class="ha-help">Saat pulau diganti, suku akan otomatis terisi & halaman akan auto submit.</div>
            </div>

            <div>
                <label class="ha-label">Pilih Suku</label>
                <select name="tribe" id="haTribeSelect" class="ha-select" @disabled(!$selectedIsland)>
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

            <div class="flex items-end gap-2">
                <button class="ha-btn ha-btn-primary w-full md:w-auto" type="submit">
                    Tampilkan
                </button>

                <a href="{{ route('admin.heritages.index') }}"
                   class="ha-btn ha-btn-soft w-full md:w-auto text-center">
                    Reset
                </a>
            </div>
        </div>
    </form>

    @if($selectedIsland && $selectedTribeKey)

        {{-- HEADER SUKU --}}
        <div class="ha-card p-5 space-y-4">
            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div>
                    <h2 class="text-lg font-black">Header Suku</h2>
                    <p class="ha-sub text-xs mt-1">
                        Ini untuk title besar + deskripsi besar per suku (yang tampil di hero section Warisan).
                    </p>
                </div>
                <div class="text-xs" style="color: color-mix(in oklab, var(--txt-body) 55%, transparent);">
                    Disimpan berdasarkan:
                    <span class="font-black">{{ $selectedIsland->name }}</span>
                    —
                    <span class="font-black">{{ $selectedTribeKey }}</span>
                </div>
            </div>

            <form method="POST"
                  action="{{ route('admin.heritages.page.save') }}"
                  enctype="multipart/form-data"
                  class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf

                <input type="hidden" name="island_id" value="{{ $selectedIsland->id }}">
                <input type="hidden" name="tribe_key" value="{{ $selectedTribeKey }}">

                <div>
                    <label class="ha-label">Title Besar</label>
                    <input type="text"
                           name="hero_title"
                           value="{{ old('hero_title', $tribePage->hero_title ?? '') }}"
                           class="ha-input"
                           placeholder="Contoh: Warisan Suku Aceh">
                </div>

                <div>
                    <label class="ha-label">Gambar Header (opsional)</label>
                    <input type="file" name="hero_image" class="ha-file">
                    @if(!empty($tribePage?->hero_image))
                        <div class="text-xs mt-2" style="color: color-mix(in oklab, var(--txt-body) 60%, transparent);">
                            Saat ini:
                            <a class="underline font-bold" href="{{ asset('storage/'.$tribePage->hero_image) }}" target="_blank">lihat</a>
                        </div>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <label class="ha-label">Deskripsi Besar</label>
                    <textarea name="hero_description"
                              rows="3"
                              class="ha-textarea"
                              placeholder="Deskripsi singkat yang tampil di bagian hero / section warisan...">{{ old('hero_description', $tribePage->hero_description ?? '') }}</textarea>
                </div>

                <div class="md:col-span-2 flex items-center gap-2">
                    <button class="ha-btn ha-btn-primary" type="submit">
                        Simpan Header
                    </button>
                    <div class="text-xs" style="color: color-mix(in oklab, var(--txt-body) 55%, transparent);">
                        Tip: isi yang rapi dan sopan karena ini tampil ke publik.
                    </div>
                </div>
            </form>
        </div>

        {{-- 3 CRUD DALAM 1 HALAMAN --}}
        <div class="ha-grid-3">
            @foreach($categoryLabels as $catKey => $catLabel)
                <div class="ha-card p-4 space-y-4">

                    <div class="ha-cat-head flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-base font-black">{{ $catLabel }}</h3>
                            <p class="ha-sub text-xs mt-1">
                                Tambah / lihat detail / edit / hapus item untuk kategori ini.
                            </p>
                        </div>
                        <span class="ha-chip" style="font-size: 11px;">{{ $catKey }}</span>
                    </div>

                    {{-- FORM TAMBAH ITEM --}}
                    <form method="POST"
                          action="{{ route('admin.heritages.item.store') }}"
                          enctype="multipart/form-data"
                          class="space-y-3">
                        @csrf
                        <input type="hidden" name="island_id" value="{{ $selectedIsland->id }}">
                        <input type="hidden" name="tribe_key" value="{{ $selectedTribeKey }}">
                        <input type="hidden" name="category" value="{{ $catKey }}">

                        <div>
                            <label class="ha-label">Judul</label>
                            <input type="text" name="title" class="ha-input" placeholder="Contoh: Ulos / Rumah Bolon / Sasando ...">
                        </div>

                        <div>
                            <label class="ha-label">Deskripsi (opsional)</label>
                            <textarea name="description" rows="2" class="ha-textarea" placeholder="Deskripsi singkat..."></textarea>
                        </div>

                        {{-- ✅ NEW: lokasi + url --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="ha-label">Lokasi (opsional)</label>
                                <input type="text" name="location" class="ha-input" placeholder="Contoh: Banda Aceh / Toraja">
                                <div class="ha-help">Muncul di card publik & modal jika diisi.</div>
                            </div>
                            <div>
                                <label class="ha-label">URL Detail (opsional)</label>
                                <input type="url" name="detail_url" class="ha-input" placeholder="https://... (wiki/artikel)">
                                <div class="ha-help">Tombol “Lihat Selengkapnya” akan muncul jika URL valid.</div>
                            </div>
                        </div>

                        <div>
                            <label class="ha-label">Gambar (opsional)</label>
                            <input type="file" name="image" class="ha-file">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="ha-label">Urutan (opsional)</label>
                                <input type="number" name="sort_order" min="0" value="0" class="ha-input">
                            </div>
                            <div class="flex items-end">
                                <button class="ha-btn ha-btn-primary w-full" type="submit">
                                    + Tambah Item
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- LIST ITEM --}}
                    <div class="space-y-3">
                        @php
                            $items = $itemsByCategory[$catKey] ?? collect();
                        @endphp

                        @if($items->count() === 0)
                            <div class="hi-card">
                                <div class="text-xs" style="color: color-mix(in oklab, var(--txt-body) 60%, transparent);">
                                    Belum ada item di kategori ini.
                                </div>
                            </div>
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

                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="hi-title">{{ $item->title }}</div>

                                            <div class="hi-meta mt-1">
                                                sort: <span class="font-black">{{ $item->sort_order }}</span>

                                                @if($item->image_path)
                                                    <span class="opacity-60">•</span>
                                                    <span class="font-bold">ada gambar</span>
                                                @endif

                                                @if($locVal !== '')
                                                    <span class="opacity-60">•</span>
                                                    <span class="font-bold">ada lokasi</span>
                                                @endif

                                                @if($urlVal !== '')
                                                    <span class="opacity-60">•</span>
                                                    <span class="font-bold">ada link</span>
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

                                        <div class="flex items-center gap-2">
                                            <button type="button"
                                                    class="ha-btn ha-btn-soft text-xs"
                                                    data-ha-detail>
                                                Detail
                                            </button>

                                            <form method="POST"
                                                  action="{{ route('admin.heritages.item.destroy', $item) }}"
                                                  onsubmit="return confirm('Hapus item ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="ha-btn ha-btn-danger text-xs" type="submit">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Edit accordion --}}
                                    <details class="ha-details mt-3">
                                        <summary class="flex items-center justify-between gap-2 text-xs font-black"
                                                 style="color: color-mix(in oklab, var(--txt-body) 78%, transparent);">
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
                                              class="mt-3 space-y-2">
                                            @csrf
                                            @method('PATCH')

                                            <div>
                                                <label class="ha-label">Judul</label>
                                                <input type="text" name="title" value="{{ $item->title }}" class="ha-input">
                                            </div>

                                            <div>
                                                <label class="ha-label">Deskripsi</label>
                                                <textarea name="description" rows="2" class="ha-textarea">{{ $item->description }}</textarea>
                                            </div>

                                            {{-- ✅ NEW: lokasi + url --}}
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="ha-label">Lokasi (opsional)</label>
                                                    <input type="text" name="location" value="{{ $item->location }}" class="ha-input" placeholder="Contoh: Toraja / Bali">
                                                </div>
                                                <div>
                                                    <label class="ha-label">URL Detail (opsional)</label>
                                                    <input type="url" name="detail_url" value="{{ $item->detail_url }}" class="ha-input" placeholder="https://...">
                                                </div>
                                            </div>

                                            <div>
                                                <label class="ha-label">Ganti gambar (opsional)</label>
                                                <input type="file" name="image" class="ha-file">
                                                @if($item->image_path)
                                                    <div class="text-xs mt-2" style="color: color-mix(in oklab, var(--txt-body) 60%, transparent);">
                                                        Saat ini:
                                                        <a class="underline font-bold" href="{{ asset('storage/'.$item->image_path) }}" target="_blank">lihat</a>
                                                    </div>
                                                @endif
                                            </div>

                                            <div>
                                                <label class="ha-label">Urutan</label>
                                                <input type="number" name="sort_order" min="0" value="{{ $item->sort_order }}" class="ha-input">
                                            </div>

                                            <button class="ha-btn ha-btn-primary w-full text-xs" type="submit">
                                                Simpan Perubahan
                                            </button>
                                        </form>
                                    </details>

                                </div>
                            @endforeach
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

    @else
        <div class="ha-card p-6">
            <div class="text-sm" style="color: color-mix(in oklab, var(--txt-body) 80%, transparent);">
                Pilih pulau & suku dulu untuk mulai mengelola warisan.
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

                <div class="mt-4 text-xs" style="color: color-mix(in oklab, var(--txt-body) 55%, transparent);">
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

                // optional: kalau user ganti suku -> auto submit juga
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
