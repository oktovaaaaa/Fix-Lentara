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
    ========================================================= */

    .gl-wrap{
        max-width: 1200px;
        margin: 0 auto;
    }

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
        background: #0b1020;
        border: 1px solid rgba(148,163,184,.18);
        border-radius: 18px;
        padding: 16px;
        box-shadow: 0 18px 60px rgba(0,0,0,.25);
        color: rgba(226,232,240,.92);
    }

    .gl-card h2{
        margin: 0 0 10px;
        font-size: 18px;
        font-weight: 900;
        letter-spacing: .2px;
    }

    .gl-sub{
        margin: 0 0 12px;
        color: rgba(148,163,184,.9);
        font-weight: 600;
        font-size: 13px;
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
        font-size: 13px;
        font-weight: 800;
        color: rgba(226,232,240,.92);
    }

    .gl-input, .gl-select, .gl-textarea{
        width: 100%;
        border-radius: 14px;
        border: 1px solid rgba(148,163,184,.22);
        background: rgba(2,6,23,.35);
        color: rgba(226,232,240,.95);
        padding: 10px 12px;
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        font-weight: 650;
    }

    .gl-textarea{
        min-height: 96px;
        resize: vertical;
        line-height: 1.35;
    }

    .gl-input:focus, .gl-select:focus, .gl-textarea:focus{
        border-color: rgba(249,115,22,.65);
        box-shadow: 0 0 0 3px rgba(249,115,22,.18);
    }

    .gl-actions{
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        margin-top: 10px;
    }

    .gl-btn{
        border: none;
        cursor: pointer;
        border-radius: 14px;
        padding: 10px 14px;
        font-weight: 900;
        letter-spacing: .2px;
        transition: transform .12s ease, filter .12s ease;
    }

    .gl-btn:hover{ transform: translateY(-1px); filter: saturate(1.05); }

    .gl-btn.primary{
        background: #f97316;
        color: #111827;
        box-shadow: 0 16px 40px rgba(249,115,22,.18);
    }

    .gl-btn.ghost{
        background: rgba(255,255,255,.06);
        color: rgba(226,232,240,.92);
        border: 1px solid rgba(148,163,184,.22);
    }

    .gl-btn.danger{
        background: rgba(239,68,68,.12);
        color: rgba(254,202,202,.95);
        border: 1px solid rgba(239,68,68,.35);
    }

    /* ✅ NEW: tombol edit (styling mirip ghost tapi aksen orange) */
    .gl-btn.edit{
        background: rgba(249,115,22,.10);
        color: rgba(255,237,213,.95);
        border: 1px solid rgba(249,115,22,.35);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 12px;
    }
    .gl-btn.edit:hover{
        box-shadow: 0 0 0 3px rgba(249,115,22,.14);
    }

    .gl-check{
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 14px;
        background: rgba(255,255,255,.04);
        border: 1px solid rgba(148,163,184,.18);
        font-weight: 850;
        color: rgba(226,232,240,.92);
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
        color: rgba(148,163,184,.92);
        font-weight: 700;
    }

    /* Questions list */
    .q-list{
        display: grid;
        gap: 12px;
        margin-top: 10px;
    }

    .q-item{
        border-radius: 16px;
        border: 1px solid rgba(148,163,184,.18);
        background: rgba(255,255,255,.03);
        padding: 12px;
    }

    .q-top{
        display: flex;
        gap: 10px;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .q-title{
        font-weight: 950;
        margin: 0;
        font-size: 14px;
        color: rgba(226,232,240,.95);
    }

    .q-meta{
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 6px;
        color: rgba(148,163,184,.92);
        font-weight: 750;
        font-size: 12px;
    }

    .q-badge{
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid rgba(148,163,184,.18);
        background: rgba(2,6,23,.35);
        font-weight: 900;
        font-size: 12px;
    }

    .q-badge.ok{
        border-color: rgba(34,197,94,.35);
        background: rgba(34,197,94,.10);
        color: rgba(167,243,208,.95);
    }

    .q-badge.off{
        border-color: rgba(239,68,68,.35);
        background: rgba(239,68,68,.10);
        color: rgba(254,202,202,.95);
    }

    .q-body{
        margin-top: 10px;
        font-size: 13px;
        line-height: 1.45;
        color: rgba(226,232,240,.92);
        white-space: pre-wrap;
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
        border: 1px solid rgba(148,163,184,.18);
        background: rgba(2,6,23,.25);
        font-size: 13px;
        font-weight: 750;
        color: rgba(226,232,240,.92);
    }

    .q-opt b{
        color: rgba(226,232,240,.95);
    }

    /* ✅ NEW: action buttons in question top-right */
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

    .q-hide{
        display: none !important;
    }

    .gl-alert{
        border-radius: 16px;
        padding: 10px 12px;
        border: 1px solid rgba(148,163,184,.18);
        background: rgba(255,255,255,.03);
        color: rgba(226,232,240,.92);
        font-weight: 800;
        margin-bottom: 12px;
    }

    .gl-alert.ok{
        border-color: rgba(34,197,94,.35);
        background: rgba(34,197,94,.10);
        color: rgba(167,243,208,.95);
    }

    .gl-alert.err{
        border-color: rgba(239,68,68,.35);
        background: rgba(239,68,68,.10);
        color: rgba(254,202,202,.95);
    }

    .gl-errors{
        margin: 0;
        padding-left: 18px;
        font-weight: 750;
    }
</style>

<div class="gl-wrap">

    {{-- FLASH --}}
    @if(session('success'))
        <div class="gl-alert ok">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="gl-alert err">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="gl-alert err">
            <div style="font-weight:950;margin-bottom:6px;">Ada error validasi:</div>
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

                    <a href="{{ route('admin.game-levels.index') }}" class="gl-btn ghost" style="text-decoration:none;display:inline-flex;align-items:center;">
                        Kembali
                    </a>

                    <form method="POST" action="{{ route('admin.game-levels.destroy', $gameLevel->id) }}"
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
            <h2>Kelola Soal Level</h2>
            <p class="gl-sub">
                Tambah / hapus soal untuk level: <b>{{ $gameLevel->title }}</b>.
                (Minimal 5 soal aktif untuk dimainkan.)
            </p>

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

                            {{-- ✅ ACTIONS: EDIT + DELETE --}}
                            <div class="q-actions">

                                {{-- ✅ EDIT BUTTON --}}
                                <a
                                    href="{{ route('admin.game-questions.edit', [$gameLevel->id, $q->id]) }}"
                                    class="gl-btn edit"
                                    style="padding:10px 12px;"
                                >
                                    Edit
                                </a>

                                {{-- DELETE --}}
                                <form method="POST"
                                      action="{{ route('admin.game-questions.destroy', [$gameLevel->id, $q->id]) }}"
                                      onsubmit="return confirm('Yakin hapus soal ini?');"
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="gl-btn danger" style="padding:10px 12px;">Hapus</button>
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
                    <div class="q-item" style="text-align:center;color:rgba(148,163,184,.92);font-weight:850;">
                        Belum ada soal di level ini. Tambahkan dulu di bawah.
                    </div>
                @endforelse
            </div>

            <hr class="gl-hr">

            {{-- ADD QUESTION --}}
            <h2 style="margin-top:0;">Tambah Soal</h2>
            <p class="gl-sub">Isi data soal. Pilih tipe soal supaya field yang relevan muncul.</p>

            <form method="POST" action="{{ route('admin.game-questions.store', $gameLevel->id) }}">
                @csrf

                <div class="q-form-grid">
                    <div class="gl-field">
                        <label class="gl-label">Tipe Soal</label>
                        <select name="type" class="gl-select" id="qType" required>
                            <option value="mcq" @selected(old('type') === 'mcq')>Pilihan Ganda (A/B/C/D)</option>
                            <option value="fill" @selected(old('type') === 'fill')>Isian Singkat</option>
                        </select>
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
                            placeholder="Contoh: MAS (atau IKANMAS sesuai kebutuhan)"
                        />
                        <div class="gl-note">
                            Panjang jawaban inilah yang nanti dipakai sebagai maxlength di input pemain.
                            (Jawaban diperiksa case-insensitive, spasi diabaikan.)
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
