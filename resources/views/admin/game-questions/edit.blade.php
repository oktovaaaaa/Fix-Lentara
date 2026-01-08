{{-- resources/views/admin/game-questions/edit.blade.php (REPLACE FULL) --}}
@extends('layouts.admin')
@section('page-title', 'Edit Soal - '.$level->title)
@section('content')

@php
  // UI only: no logic changes
@endphp

<style>
  /* =========================================================
     ADMIN GAME QUESTIONS EDIT — SELARAS ADMIN (ORANGE NEON)
     UI ONLY (route/field name/logic tetap)
  ========================================================= */

  .gqe-wrap{
    max-width: 980px;
    margin: 0 auto;
    padding: 6px 0 18px;
  }

  .gqe-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
    margin: 6px 0 14px;
  }
  .gqe-head h2{
    margin:0;
    font-size: 20px;
    font-weight: 1000;
    letter-spacing: -0.02em;
    color: var(--txt-body);
  }
  .gqe-head p{
    margin:6px 0 0;
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
    line-height: 1.55;
  }

  .gqe-chip{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 12px;
    border-radius:999px;
    border:1px solid var(--line);
    background: rgba(255,255,255,.02);
    color: var(--txt-body);
    font-weight: 1000;
    font-size: 12px;
    box-shadow: 0 14px 35px rgba(0,0,0,.08);
  }
  .gqe-chip .k{ color: var(--muted); font-weight: 1000; }
  .gqe-chip .v{ color: var(--txt-body); font-weight: 1000; }

  /* ---- alerts ---- */
  .gqe-alert{
    margin:10px 0 12px;
    padding:10px 12px;
    border-radius:14px;
    border:1px solid rgba(255,255,255,.10);
    background: rgba(2,6,23,.35);
    background: color-mix(in oklab, var(--card) 55%, transparent);
    color: var(--txt-body);
    font-weight: 900;
  }
  .gqe-alert--success{
    border-color: rgba(34,197,94,.35);
    background: rgba(34,197,94,.10);
    color: rgba(167,243,208,.95);
  }
  .gqe-alert--error{
    border-color: rgba(239,68,68,.35);
    background: rgba(239,68,68,.10);
    color: rgba(254,202,202,.95);
  }

  /* ---- card ---- */
  .gqe-card{
    border-radius: 18px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(2,6,23,.35);
    background: color-mix(in oklab, var(--card) 55%, transparent);
    backdrop-filter: blur(14px) saturate(140%);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    box-shadow: 0 18px 45px rgba(0,0,0,.10);
    padding: 14px;
    color: rgba(255,255,255,.92);
  }
  html:not([data-theme="dark"]) .gqe-card{
    background: rgba(255,255,255,.65);
    border: 1px solid rgba(15,23,42,.12);
    color: rgba(15,23,42,.92);
    box-shadow: 0 12px 32px rgba(15,23,42,.08);
  }

  .gqe-title{
    font-weight: 1000;
    margin: 0 0 6px 0;
    letter-spacing: -0.01em;
    font-size: 16px;
    color: var(--txt-body);
  }
  .gqe-note{
    margin: 0 0 12px 0;
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
    line-height: 1.55;
  }

  /* ---- rows ---- */
  .gqe-row{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }
  @media (max-width: 900px){
    .gqe-row{ grid-template-columns: 1fr; }
  }

  /* ---- fields ---- */
  .gqe-field{ margin-bottom: 12px; }
  .gqe-label{
    display:block;
    font-size: 12px;
    font-weight: 1000;
    margin:0 0 6px 2px;
    color: color-mix(in oklab, var(--txt-body) 82%, transparent);
  }

  .gqe-input,.gqe-select,.gqe-textarea{
    width:100%;
    padding:10px 12px;
    border-radius:12px;
    outline:none;
    border: 1px solid rgba(148,163,184,.22);
    background: rgba(2,6,23,.22);
    background: color-mix(in oklab, var(--card) 35%, transparent);
    color: var(--txt-body);
    transition: border-color .2s ease, box-shadow .2s ease, transform .15s ease, background .2s ease;
  }
  html:not([data-theme="dark"]) .gqe-input,
  html:not([data-theme="dark"]) .gqe-select,
  html:not([data-theme="dark"]) .gqe-textarea{
    background: rgba(255,255,255,.70);
    border: 1px solid rgba(15,23,42,.14);
  }

  .gqe-textarea{ resize:vertical; }
  .gqe-input::placeholder,.gqe-textarea::placeholder{
    color: color-mix(in oklab, var(--txt-body) 45%, transparent);
  }

  .gqe-input:focus,.gqe-select:focus,.gqe-textarea:focus{
    border-color: rgba(249,115,22,.55);
    box-shadow: 0 0 0 4px rgba(249,115,22,.14);
  }

  .gqe-select option{
    background:#0b1220;
    color: rgba(255,255,255,.92);
  }
  html:not([data-theme="dark"]) .gqe-select option{
    background:#fff;
    color:#0f172a;
  }

  .gqe-divider{
    margin: 10px 0 12px;
    opacity: .22;
    border-color: rgba(148,163,184,.22);
  }

  .gqe-help{
    margin-top: 6px;
    font-size: 11px;
    font-weight: 800;
    color: var(--muted);
    line-height: 1.45;
  }

  /* ---- checkbox ---- */
  .gqe-check{
    display:flex;
    gap:10px;
    align-items:center;
    padding: 10px 12px;
    border-radius: 14px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.02);
    font-weight: 1000;
    color: var(--txt-body);
    user-select:none;
    margin-top: 6px;
  }
  .gqe-check input{ transform: translateY(1px); }

  /* ---- actions ---- */
  .gqe-actions{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top: 12px;
    align-items:center;
  }

  .gqe-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    padding:10px 14px;
    border-radius:999px;
    font-weight: 1000;
    font-size: 12px;
    text-decoration:none;
    border: 1px solid rgba(148,163,184,.20);
    background: rgba(255,255,255,.03);
    color: var(--txt-body);
    cursor:pointer;
    transition: transform .15s ease, box-shadow .2s ease, filter .2s ease, background .2s ease, border-color .2s ease;
    user-select:none;
    line-height: 1;
  }
  .gqe-btn:active{ transform: translateY(1px) scale(.99); }

  .gqe-btn-primary{
    border-color: rgba(249,115,22,.30);
    background: linear-gradient(90deg, rgba(249,115,22,.95), rgba(251,146,60,.95));
    color:#0b1020;
    box-shadow: 0 16px 38px rgba(249,115,22,.18);
  }
  .gqe-btn-primary:hover{
    filter: brightness(1.03);
    box-shadow: 0 20px 48px rgba(249,115,22,.24);
  }

  .gqe-btn-ghost:hover{
    border-color: rgba(249,115,22,.38);
    box-shadow: 0 0 0 4px rgba(249,115,22,.12);
    background: rgba(249,115,22,.08);
  }

  /* preview image */
.gqe-img{
  width: 100%;
  max-height: 260px;
  object-fit: cover;
  border-radius: 14px;
  border: 1px solid rgba(148,163,184,.18);
  display:block;
  margin: 0 0 10px;
}

/* file input polish */
.gqe-input[type="file"]{
  padding: 9px 12px;
}
.gqe-input[type="file"]::file-selector-button{
  border: 1px solid rgba(148,163,184,.20);
  background: rgba(255,255,255,.04);
  color: var(--txt-body);
  padding: 8px 12px;
  border-radius: 999px;
  font-weight: 1000;
  cursor: pointer;
  margin-right: 10px;
}
.gqe-input[type="file"]::file-selector-button:hover{
  border-color: rgba(249,115,22,.38);
  box-shadow: 0 0 0 4px rgba(249,115,22,.10);
}

</style>

<div class="gqe-wrap">

  <div class="gqe-head">
    <div>
      <h2>Edit Soal — {{ $level->title }}</h2>
      <p>
        Ubah data soal. Tipe boleh diubah, tapi pastikan field wajib terisi sesuai tipe.
      </p>
    </div>
    <div class="gqe-chip" title="Info">
      <span class="k">ID</span>
      <span class="v">{{ $question->id }}</span>
    </div>
  </div>

  @if(session('success'))
    <div class="gqe-alert gqe-alert--success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="gqe-alert gqe-alert--error">{{ session('error') }}</div>
  @endif
  @if($errors->any())
    <div class="gqe-alert gqe-alert--error">
      <b>Validasi gagal:</b>
      <ul style="margin:8px 0 0 18px;">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="gqe-card">
    <div class="gqe-title">Form Edit Soal</div>
    <div class="gqe-note">
      Level: <b>{{ $level->title }}</b> • Pastikan urutan & jawaban benar sesuai.
    </div>

<form method="POST" action="{{ route('admin.game-questions.update', [$level->id, $question->id]) }}" enctype="multipart/form-data">

    @csrf
      @method('PUT')

      <div class="gqe-row">
        <div class="gqe-field">
          <label class="gqe-label">Tipe</label>
          <select name="type" class="gqe-select">
            <option value="mcq" {{ old('type', $question->type) === 'mcq' ? 'selected' : '' }}>Pilihan Ganda (A/B/C/D)</option>
            <option value="fill" {{ old('type', $question->type) === 'fill' ? 'selected' : '' }}>Isian Singkat</option>
          </select>
        </div>

        <div class="gqe-field">
          <label class="gqe-label">Urutan soal</label>
          <input type="number" min="1" name="order" class="gqe-input" value="{{ old('order', $question->order) }}">
        </div>
      </div>

      <div class="gqe-field">
        <label class="gqe-label">Pertanyaan (Wajib)</label>
        <textarea name="question_text" rows="3" class="gqe-textarea" placeholder="Tulis pertanyaan...">{{ old('question_text', $question->question_text) }}</textarea>
      </div>

      <div class="gqe-field">
  <label class="gqe-label">Gambar (Opsional)</label>

  {{-- preview gambar lama --}}
  @if(!empty($question->image_path))
    <img
      class="gqe-img"
      src="{{ asset($question->image_path) }}"
      alt="Gambar soal"
      loading="lazy"
    />
  @endif

  {{-- upload baru --}}
  <input
    type="file"
    name="image"
    class="gqe-input"
    accept="image/*"
  />
  <div class="gqe-help">
    Upload gambar baru untuk mengganti gambar lama. Kosongkan jika tidak ingin mengganti.
  </div>

  {{-- opsi hapus gambar --}}
  <label class="gqe-check" style="margin-top:10px;">
    <input type="checkbox" name="remove_image" value="1" {{ old('remove_image') ? 'checked' : '' }}>
    Hapus gambar (jadikan tanpa gambar)
  </label>
</div>


      <hr class="gqe-divider" />

      <div class="gqe-note" style="margin:0 0 10px 0;">
        <b>MCQ</b>: isi opsi A–D + jawaban benar (A/B/C/D).<br>
        <b>Isian</b>: isi jawaban benar (correct_text).
      </div>

      <div class="gqe-row">
        <div class="gqe-field">
          <label class="gqe-label">Opsi A</label>
          <input name="option_a" class="gqe-input" value="{{ old('option_a', $question->option_a) }}">
        </div>
        <div class="gqe-field">
          <label class="gqe-label">Opsi B</label>
          <input name="option_b" class="gqe-input" value="{{ old('option_b', $question->option_b) }}">
        </div>
      </div>

      <div class="gqe-row">
        <div class="gqe-field">
          <label class="gqe-label">Opsi C</label>
          <input name="option_c" class="gqe-input" value="{{ old('option_c', $question->option_c) }}">
        </div>
        <div class="gqe-field">
          <label class="gqe-label">Opsi D</label>
          <input name="option_d" class="gqe-input" value="{{ old('option_d', $question->option_d) }}">
        </div>
      </div>

      <div class="gqe-row">
        <div class="gqe-field">
          <label class="gqe-label">Jawaban benar MCQ</label>
          <select name="correct_option" class="gqe-select">
            <option value="">-</option>
            @foreach(['A','B','C','D'] as $opt)
              <option value="{{ $opt }}" {{ old('correct_option', $question->correct_option) === $opt ? 'selected' : '' }}>
                {{ $opt }}
              </option>
            @endforeach
          </select>
          <div class="gqe-help">Kosongkan jika tipe “Isian”.</div>
        </div>

        <div class="gqe-field">
          <label class="gqe-label">Jawaban benar Isian</label>
          <input name="correct_text" class="gqe-input"
                 value="{{ old('correct_text', $question->correct_text) }}"
                 placeholder="contoh: mas / toba / dll">
          <div class="gqe-help">Kosongkan jika tipe “MCQ”.</div>
        </div>
      </div>

      <label class="gqe-check">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $question->is_active) ? 'checked' : '' }}>
        Aktif
      </label>

      <div class="gqe-actions">
        <button class="gqe-btn gqe-btn-primary" type="submit">Simpan Perubahan</button>
        <a class="gqe-btn gqe-btn-ghost" href="{{ route('admin.game-levels.edit', $level->id) }}">← Kembali ke Level</a>
      </div>
    </form>
  </div>
</div>

@endsection
