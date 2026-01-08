{{-- resources/views/admin/game-levels/edit.blade.php (REPLACE FULL) --}}
@extends('layouts.admin')

@section('title', 'Edit Game Level')

@section('page-title')
    Edit Game Level
@endsection

@section('content')
@php
    // Ambil daftar soal untuk level ini (biar halaman edit bisa kelola soal tanpa butuh controller tambahan)
    $questions = $gameLevel->questions()->orderBy('order')->get();
@endphp

<style>
    /* =========================================================
       ADMIN GAME LEVEL EDIT (MANUAL CSS, INDONESIA)
       - UI ONLY (logika/route/field tetap)
       - Selaras dengan tema admin orange-neon & light/dark safe
    ========================================================= */

    .gl-wrap{
        max-width: 1200px;
        margin: 0 auto;
        padding: 6px 0 18px;
        color: var(--txt-body);
    }

    .gl-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
        flex-wrap:wrap;
        margin: 4px 0 14px;
    }
    .gl-head h1{
        margin:0;
        font-size: 20px;
        font-weight: 1000;
        letter-spacing: -0.02em;
        color: var(--txt-body);
    }
    .gl-head .sub{
        margin:6px 0 0;
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
        line-height: 1.55;
    }

    .gl-chip{
        display:inline-flex;
        align-items:center;
        gap:10px;
        padding:8px 12px;
        border-radius:999px;
        border:1px solid var(--line);
        background: rgba(255,255,255,.02);
        color: var(--txt-body);
        font-weight: 1000;
        font-size: 12px;
        box-shadow: 0 14px 35px rgba(0,0,0,.08);
        user-select:none;
        white-space:nowrap;
    }
    .gl-chip .k{ color: var(--muted); }
    .gl-chip .v{ color: var(--txt-body); }

    .gl-grid{
        display: grid;
        grid-template-columns: 1fr 1.15fr;
        gap: 18px;
        align-items: start;
    }

    @media (max-width: 1024px){
        .gl-grid{ grid-template-columns: 1fr; }
    }

    .gl-card{
        border-radius: 18px;
        border: 1px solid rgba(148,163,184,.18);
        background: rgba(2,6,23,.35);
        background: color-mix(in oklab, var(--card) 55%, transparent);
        backdrop-filter: blur(14px) saturate(140%);
        -webkit-backdrop-filter: blur(14px) saturate(140%);
        box-shadow: 0 18px 45px rgba(0,0,0,.10);
        padding: 16px;
        color: var(--txt-body);
    }
    html:not([data-theme="dark"]) .gl-card{
        background: rgba(255,255,255,.65);
        border: 1px solid rgba(15,23,42,.12);
        box-shadow: 0 12px 32px rgba(15,23,42,.08);
    }

    .gl-card h2{
        margin: 0 0 8px;
        font-size: 16px;
        font-weight: 1000;
        letter-spacing: -0.01em;
        color: var(--txt-body);
    }

    .gl-sub{
        margin: 0 0 12px;
        color: var(--muted);
        font-weight: 800;
        font-size: 12px;
        line-height: 1.55;
    }

    .gl-row{
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 12px;
    }

    @media (max-width: 520px){
        .gl-row{ grid-template-columns: 1fr; }
    }

    .gl-field{
        display: grid;
        gap: 6px;
    }

    .gl-label{
        font-size: 12px;
        font-weight: 1000;
        color: color-mix(in oklab, var(--txt-body) 82%, transparent);
        margin-left: 2px;
    }

    .gl-input, .gl-select, .gl-textarea{
        width: 100%;
        border-radius: 14px;
        border: 1px solid rgba(148,163,184,.22);
        background: rgba(2,6,23,.22);
        background: color-mix(in oklab, var(--card) 35%, transparent);
        color: var(--txt-body);
        padding: 10px 12px;
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease, background .15s ease;
        font-weight: 750;
    }
    html:not([data-theme="dark"]) .gl-input,
    html:not([data-theme="dark"]) .gl-select,
    html:not([data-theme="dark"]) .gl-textarea{
        background: rgba(255,255,255,.70);
        border: 1px solid rgba(15,23,42,.14);
    }

    .gl-textarea{
        min-height: 96px;
        resize: vertical;
        line-height: 1.45;
    }

    .gl-input::placeholder, .gl-textarea::placeholder{
        color: color-mix(in oklab, var(--txt-body) 45%, transparent);
    }

    .gl-input:focus, .gl-select:focus, .gl-textarea:focus{
        border-color: rgba(249,115,22,.60);
        box-shadow: 0 0 0 4px rgba(249,115,22,.14);
    }

    .gl-select option{
        background:#0b1220;
        color: rgba(255,255,255,.92);
    }
    html:not([data-theme="dark"]) .gl-select option{
        background:#fff;
        color:#0f172a;
    }

    .gl-actions{
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        margin-top: 10px;
    }

    .gl-btn{
        border: 1px solid rgba(148,163,184,.20);
        background: rgba(255,255,255,.03);
        cursor: pointer;
        border-radius: 999px;
        padding: 10px 14px;
        font-weight: 1000;
        letter-spacing: .2px;
        transition: transform .12s ease, filter .12s ease, box-shadow .2s ease, border-color .2s ease, background .2s ease;
        color: var(--txt-body);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        white-space: nowrap;
        user-select: none;
    }
    .gl-btn:active{ transform: translateY(1px) scale(.99); }

    .gl-btn.primary{
        border-color: rgba(249,115,22,.30);
        background: linear-gradient(90deg, rgba(249,115,22,.95), rgba(251,146,60,.95));
        color: #0b1020;
        box-shadow: 0 16px 38px rgba(249,115,22,.18);
    }
    .gl-btn.primary:hover{
        filter: brightness(1.03);
        box-shadow: 0 20px 48px rgba(249,115,22,.24);
    }

    .gl-btn.ghost:hover{
        border-color: rgba(249,115,22,.38);
        box-shadow: 0 0 0 4px rgba(249,115,22,.12);
        background: rgba(249,115,22,.08);
    }

    .gl-btn.danger{
        border-color: rgba(239,68,68,.32);
        background: rgba(239,68,68,.10);
        color: rgba(254,202,202,.95);
    }
    html:not([data-theme="dark"]) .gl-btn.danger{
        color: rgb(127,29,29);
        background: rgba(239,68,68,.08);
    }
    .gl-btn.danger:hover{
        background: rgba(239,68,68,.14);
        box-shadow: 0 0 0 4px rgba(239,68,68,.10);
    }

    /* tombol edit soal (aksen orange) */
    .gl-btn.edit{
        border-color: rgba(249,115,22,.35);
        background: rgba(249,115,22,.10);
        color: color-mix(in oklab, #ffedd5 95%, transparent);
    }
    .gl-btn.edit:hover{
        box-shadow: 0 0 0 4px rgba(249,115,22,.12);
        background: rgba(249,115,22,.14);
    }

    .gl-check{
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 14px;
        background: rgba(255,255,255,.02);
        border: 1px solid rgba(148,163,184,.18);
        font-weight: 1000;
        color: var(--txt-body);
        user-select: none;
    }

    .gl-check input{
        width: 18px;
        height: 18px;
        accent-color: #f97316;
    }

    .gl-hr{
        height: 1px;
        background: rgba(148,163,184,.14);
        margin: 14px 0;
        border: none;
    }

    .gl-note{
        font-size: 12px;
        color: var(--muted);
        font-weight: 800;
        line-height: 1.55;
    }

    /* Questions list */
    .q-list{
        display: grid;
        gap: 12px;
        margin-top: 10px;
    }

    .q-item{
        border-radius: 16px;
        border: 1px solid rgba(148,163,184,.16);
        background: rgba(2,6,23,.18);
        background: color-mix(in oklab, var(--card) 38%, transparent);
        padding: 12px;
    }
    html:not([data-theme="dark"]) .q-item{
        background: rgba(255,255,255,.75);
        border: 1px solid rgba(15,23,42,.10);
    }

    .q-top{
        display: flex;
        gap: 10px;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .q-title{
        font-weight: 1000;
        margin: 0;
        font-size: 14px;
        color: var(--txt-body);
        line-height: 1.25;
    }

    .q-meta{
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 6px;
        color: var(--muted);
        font-weight: 900;
        font-size: 12px;
        line-height: 1.4;
    }

    .q-badge{
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid rgba(148,163,184,.18);
        background: rgba(255,255,255,.02);
        font-weight: 1000;
        font-size: 12px;
        color: var(--txt-body);
    }
    html:not([data-theme="dark"]) .q-badge{
        border: 1px solid rgba(15,23,42,.10);
        background: rgba(15,23,42,.04);
    }

    .q-badge.ok{
        border-color: rgba(34,197,94,.35);
        background: rgba(34,197,94,.10);
        color: rgba(16,185,129,.95);
    }
    html[data-theme="dark"] .q-badge.ok{
        color: rgba(167,243,208,.95);
    }

    .q-badge.off{
        border-color: rgba(239,68,68,.35);
        background: rgba(239,68,68,.10);
        color: rgba(239,68,68,.95);
    }
    html[data-theme="dark"] .q-badge.off{
        color: rgba(254,202,202,.95);
    }

    .q-body{
        margin-top: 10px;
        font-size: 13px;
        line-height: 1.55;
        color: color-mix(in oklab, var(--txt-body) 92%, transparent);
        white-space: pre-wrap;
        word-break: break-word;
    }

    .q-img{
        margin-top: 10px;
        border-radius: 14px;
        border: 1px solid rgba(148,163,184,.18);
        max-width: 100%;
        display: block;
    }

    .q-opts{
        margin-top: 10px;
        display: grid;
        gap: 8px;
    }

    .q-opt{
        padding: 10px 12px;
        border-radius: 14px;
        border: 1px solid rgba(148,163,184,.16);
        background: rgba(2,6,23,.18);
        background: color-mix(in oklab, var(--card) 30%, transparent);
        font-size: 13px;
        font-weight: 900;
        color: color-mix(in oklab, var(--txt-body) 92%, transparent);
        word-break: break-word;
    }
    html:not([data-theme="dark"]) .q-opt{
        background: rgba(255,255,255,.70);
        border: 1px solid rgba(15,23,42,.10);
    }

    .q-opt b{ color: var(--txt-body); }

    /* action buttons on question top-right */
    .q-actions{
        display:flex;
        gap:10px;
        flex-wrap:wrap;
        align-items:center;
        justify-content:flex-end;
        flex:0 0 auto;
    }

    /* Create Question form */
    .q-form-grid{
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    @media (max-width: 520px){
        .q-form-grid{ grid-template-columns: 1fr; }
    }

    .q-hide{ display: none !important; }

    .gl-alert{
        border-radius: 16px;
        padding: 10px 12px;
        border: 1px solid rgba(148,163,184,.18);
        background: rgba(255,255,255,.02);
        color: var(--txt-body);
        font-weight: 900;
        margin-bottom: 12px;
    }

    .gl-alert.ok{
        border-color: rgba(34,197,94,.35);
        background: rgba(34,197,94,.10);
        color: rgba(16,185,129,.95);
    }
    html[data-theme="dark"] .gl-alert.ok{
        color: rgba(167,243,208,.95);
    }

    .gl-alert.err{
        border-color: rgba(239,68,68,.35);
        background: rgba(239,68,68,.10);
        color: rgba(239,68,68,.95);
    }
    html[data-theme="dark"] .gl-alert.err{
        color: rgba(254,202,202,.95);
    }

    .gl-errors{
        margin: 0;
        padding-left: 18px;
        font-weight: 800;
        color: color-mix(in oklab, var(--txt-body) 92%, transparent);
    }

    .gl-minirow{
        display:flex;
        gap:10px;
        flex-wrap:wrap;
        align-items:center;
        justify-content:space-between;
        margin: 0 0 10px;
    }

    .gl-minirow .count{
        font-size: 12px;
        font-weight: 1000;
        color: var(--muted);
    }

    .gl-input[type="file"]{
    padding: 9px 12px;
}
.gl-input[type="file"]::file-selector-button{
    border: 1px solid rgba(148,163,184,.20);
    background: rgba(255,255,255,.04);
    color: var(--txt-body);
    padding: 8px 12px;
    border-radius: 999px;
    font-weight: 1000;
    cursor: pointer;
    margin-right: 10px;
}
.gl-input[type="file"]::file-selector-button:hover{
    border-color: rgba(249,115,22,.38);
    box-shadow: 0 0 0 4px rgba(249,115,22,.10);
}

</style>

<div class="gl-wrap">

    <div class="gl-head">
        <div>
            <h1>Edit Game Level</h1>
            <div class="sub">Ubah data level & kelola soal. (UI diperbaiki, logika tetap)</div>
        </div>
        <div class="gl-chip" title="Ringkasan level">
            <span class="k">Level</span>
            <span class="v">#{{ $gameLevel->id }}</span>
            <span class="k">• Soal</span>
            <span class="v">{{ $questions->count() }}</span>
        </div>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
        <div class="gl-alert ok">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="gl-alert err">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="gl-alert err">
            <div style="font-weight:1000;margin-bottom:6px;">Ada error validasi:</div>
            <ul class="gl-errors">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="gl-grid">

        {{-- =========================================================
           LEFT: EDIT LEVEL
        ========================================================= --}}
        <section class="gl-card">
            <h2>Ubah Level</h2>
            <p class="gl-sub">Edit data level ini. Setelah disimpan, kamu bisa kelola soal di panel kanan.</p>

            <form method="POST" action="{{ route('admin.game-levels.update', $gameLevel->id) }}">
                @csrf
                @method('PUT')

                <div class="gl-row">
                    <div class="gl-field">
                        <label class="gl-label">Pulau</label>
                        <select name="island_id" class="gl-select" required>
                            @foreach($islands as $isl)
                                <option value="{{ $isl->id }}"
                                    @selected((int)old('island_id', $gameLevel->island_id) === (int)$isl->id)>
                                    {{ $isl->subtitle ?? $isl->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="gl-field">
                        <label class="gl-label">Urutan Level (Order)</label>
                        <input
                            type="number"
                            name="order"
                            class="gl-input"
                            min="1"
                            required
                            value="{{ old('order', $gameLevel->order) }}"
                        />
                    </div>
                </div>

                <div class="gl-field" style="margin-bottom: 12px;">
                    <label class="gl-label">Judul Level</label>
                    <input
                        type="text"
                        name="title"
                        class="gl-input"
                        maxlength="120"
                        required
                        value="{{ old('title', $gameLevel->title) }}"
                        placeholder="Contoh: Level 1 — Pengenalan"
                    />
                </div>

                <label class="gl-check">
                    <input type="checkbox" name="is_active" value="1" @checked((bool)old('is_active', $gameLevel->is_active)) />
                    <span>Aktifkan level ini</span>
                </label>

                <hr class="gl-hr">

                <div class="gl-actions">
                    <button type="submit" class="gl-btn primary">Simpan Perubahan</button>

                    <a href="{{ route('admin.game-levels.index') }}" class="gl-btn ghost">
                        Kembali
                    </a>

                    <form method="POST"
                          action="{{ route('admin.game-levels.destroy', $gameLevel->id) }}"
                          onsubmit="return confirm('Yakin hapus level ini? Semua soal di level ini juga akan ikut terhapus.');"
                          style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="gl-btn danger">Hapus Level</button>
                    </form>
                </div>

                <p class="gl-note" style="margin-top:12px;">
                    Catatan: Level untuk pemain harus punya <b>5 soal aktif</b> supaya bisa dimainkan.
                </p>
            </form>
        </section>

        {{-- =========================================================
           RIGHT: QUESTIONS (LIST + ADD)
        ========================================================= --}}
        <section class="gl-card">
            <div class="gl-minirow">
                <div>
                    <h2 style="margin:0;">Kelola Soal Level</h2>
                    <p class="gl-sub" style="margin:6px 0 0;">
                        Tambah / hapus soal untuk level: <b>{{ $gameLevel->title }}</b>.
                    </p>
                </div>
                <div class="count">
                    Total soal: <b style="color:var(--txt-body)">{{ $questions->count() }}</b> • Minimal siap main: <b style="color:var(--txt-body)">5 soal aktif</b>
                </div>
            </div>

            {{-- LIST QUESTIONS --}}
            <div class="q-list">
                @forelse($questions as $q)
                    <div class="q-item">
                        <div class="q-top">
                            <div style="min-width:0;">
                                <p class="q-title">
                                    Soal #{{ (int)$q->order }} — {{ $q->type === 'mcq' ? 'Pilihan Ganda' : 'Isian Singkat' }}
                                </p>

                                <div class="q-meta">
                                    <span class="q-badge {{ $q->is_active ? 'ok' : 'off' }}">
                                        {{ $q->is_active ? 'AKTIF' : 'NONAKTIF' }}
                                    </span>

                                    <span class="q-badge">
                                        ID: {{ $q->id }}
                                    </span>

                                    @if($q->type === 'mcq' && $q->correct_option)
                                        <span class="q-badge">
                                            Jawaban: {{ $q->correct_option }}
                                        </span>
                                    @endif

                                    @if($q->type === 'fill' && $q->correct_text)
                                        <span class="q-badge">
                                            Jawaban: {{ $q->correct_text }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- ACTIONS: EDIT + DELETE --}}
                            <div class="q-actions">
                                <a
                                    href="{{ route('admin.game-questions.edit', [$gameLevel->id, $q->id]) }}"
                                    class="gl-btn edit"
                                >
                                    Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.game-questions.destroy', [$gameLevel->id, $q->id]) }}"
                                      onsubmit="return confirm('Yakin hapus soal ini?');"
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="gl-btn danger">Hapus</button>
                                </form>
                            </div>
                        </div>

                        <div class="q-body">{{ $q->question_text }}</div>

                        @if($q->image_path)
                            <img class="q-img" src="{{ asset($q->image_path) }}" alt="Gambar Soal" />
                        @endif

                        @if($q->type === 'mcq')
                            <div class="q-opts">
                                <div class="q-opt"><b>A.</b> {{ $q->option_a }}</div>
                                <div class="q-opt"><b>B.</b> {{ $q->option_b }}</div>
                                <div class="q-opt"><b>C.</b> {{ $q->option_c }}</div>
                                <div class="q-opt"><b>D.</b> {{ $q->option_d }}</div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="q-item" style="text-align:center;">
                        <div style="font-weight:1000;color:var(--muted);">Belum ada soal di level ini.</div>
                        <div class="gl-note" style="margin-top:6px;">Tambahkan soal baru di form “Tambah Soal” di bawah.</div>
                    </div>
                @endforelse
            </div>

            <hr class="gl-hr">

            {{-- ADD QUESTION --}}
            <h2 style="margin-top:0;">Tambah Soal</h2>
            <p class="gl-sub">Isi data soal. Pilih tipe soal supaya field yang relevan muncul.</p>

<form method="POST" action="{{ route('admin.game-questions.store', $gameLevel->id) }}" enctype="multipart/form-data">

            @csrf

                <div class="q-form-grid">
                   <div class="gl-field">
    <label class="gl-label">Upload Gambar (opsional)</label>
    <input
        type="file"
        name="image"
        class="gl-input"
        accept="image/*"
    />
    <div class="gl-note">Pilih file gambar dari komputer (jpg/png/webp).</div>
</div>


                    <div class="gl-field">
                        <label class="gl-label">Urutan Soal (Order)</label>
                        <input
                            type="number"
                            name="order"
                            class="gl-input"
                            min="1"
                            required
                            value="{{ old('order', max(1, (int)($questions->max('order') ?? 0) + 1)) }}"
                        />
                    </div>
                </div>

                <div class="gl-field" style="margin-top:12px;">
                    <label class="gl-label">Teks Soal</label>
                    <textarea
                        name="question_text"
                        class="gl-textarea"
                        required
                        placeholder="Tulis soal di sini...">{{ old('question_text') }}</textarea>
                </div>

                <div class="gl-row" style="margin-top:12px;">
                    <div class="gl-field">
                        <label class="gl-label">Path Gambar (opsional)</label>
                        <input
                            type="text"
                            name="image_path"
                            class="gl-input"
                            value="{{ old('image_path') }}"
                            placeholder="Contoh: storage/game/soal1.png"
                        />
                        <div class="gl-note">Jika pakai upload terpisah, isi path hasil upload.</div>
                    </div>

                    <div class="gl-field" style="align-content:end;">
                        <label class="gl-check" style="width:100%;justify-content:flex-start;">
                            <input type="checkbox" name="is_active" value="1" @checked((bool)old('is_active', true)) />
                            <span>Aktifkan soal ini</span>
                        </label>
                    </div>
                </div>

                {{-- MCQ FIELDS --}}
                <div id="mcqFields" style="margin-top:12px;">
                    <div class="q-form-grid">
                        <div class="gl-field">
                            <label class="gl-label">Opsi A</label>
                            <input type="text" name="option_a" class="gl-input" value="{{ old('option_a') }}" placeholder="Jawaban A" />
                        </div>
                        <div class="gl-field">
                            <label class="gl-label">Opsi B</label>
                            <input type="text" name="option_b" class="gl-input" value="{{ old('option_b') }}" placeholder="Jawaban B" />
                        </div>
                        <div class="gl-field">
                            <label class="gl-label">Opsi C</label>
                            <input type="text" name="option_c" class="gl-input" value="{{ old('option_c') }}" placeholder="Jawaban C" />
                        </div>
                        <div class="gl-field">
                            <label class="gl-label">Opsi D</label>
                            <input type="text" name="option_d" class="gl-input" value="{{ old('option_d') }}" placeholder="Jawaban D" />
                        </div>
                    </div>

                    <div class="gl-field" style="margin-top:12px;">
                        <label class="gl-label">Jawaban Benar (A/B/C/D)</label>
                        <select name="correct_option" class="gl-select">
                            <option value="">— Pilih —</option>
                            <option value="A" @selected(old('correct_option') === 'A')>A</option>
                            <option value="B" @selected(old('correct_option') === 'B')>B</option>
                            <option value="C" @selected(old('correct_option') === 'C')>C</option>
                            <option value="D" @selected(old('correct_option') === 'D')>D</option>
                        </select>
                    </div>
                </div>

                {{-- FILL FIELDS --}}
                <div id="fillFields" class="q-hide" style="margin-top:12px;">
                    <div class="gl-field">
                        <label class="gl-label">Jawaban Benar (Isian Singkat)</label>
                        <input
                            type="text"
                            name="correct_text"
                            class="gl-input"
                            value="{{ old('correct_text') }}"
                            placeholder="Contoh: mas / toba / dll"
                        />
                        <div class="gl-note">
                            Panjang jawaban dipakai sebagai maxlength di input pemain (sesuai logic kamu).
                        </div>
                    </div>
                </div>

                <div class="gl-actions" style="margin-top:12px;">
                    <button type="submit" class="gl-btn primary">Tambah Soal</button>
                    <button type="button" class="gl-btn ghost" onclick="window.scrollTo({top:0,behavior:'smooth'})">
                        Ke Atas
                    </button>
                </div>
            </form>
        </section>

    </div>
</div>

<script>
    (function(){
        const qType = document.getElementById('qType');
        const mcq = document.getElementById('mcqFields');
        const fill = document.getElementById('fillFields');

        function syncFields(){
            const t = (qType && qType.value) ? qType.value : 'mcq';
            if (t === 'fill') {
                mcq.classList.add('q-hide');
                fill.classList.remove('q-hide');
            } else {
                fill.classList.add('q-hide');
                mcq.classList.remove('q-hide');
            }
        }

        if (qType) {
            qType.addEventListener('change', syncFields);
            syncFields();
        }
    })();
</script>
@endsection
