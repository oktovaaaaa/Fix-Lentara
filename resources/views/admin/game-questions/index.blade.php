{{-- resources/views/admin/game-questions/index.blade.php (REPLACE FULL) --}}
@extends('layouts.admin')

@section('page-title', 'Soal untuk: '.$level->title)
@section('content')

@php
  // keep safe defaults (UI only)
  $questions = $questions ?? collect();
@endphp

<style>
  /* =========================================================
     ADMIN GAME QUESTIONS — SELARAS ADMIN (ORANGE NEON)
     - UI ONLY (logic & routes tidak diubah)
     - Fix kontras, spacing, responsive, button consistent
  ========================================================= */

  /* ---- layout ---- */
  .gq-wrap{
    max-width: 1200px;
    margin: 0 auto;
    padding: 6px 0 18px;
  }

  .gq-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
    margin: 6px 0 14px;
  }
  .gq-head h2{
    margin:0;
    font-size: 20px;
    font-weight: 1000;
    letter-spacing: -0.02em;
    color: var(--txt-body);
  }
  .gq-head p{
    margin:6px 0 0;
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
    line-height: 1.55;
  }

  .gq-chip{
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
  .gq-chip .k{ color: var(--muted); font-weight: 1000; }
  .gq-chip .v{ color: var(--txt-body); font-weight: 1000; }

  /* ---- alerts ---- */
  .gq-alert{
    margin:10px 0 12px;
    padding:10px 12px;
    border-radius:14px;
    border:1px solid rgba(255,255,255,.10);
    background: rgba(2,6,23,.35);
    background: color-mix(in oklab, var(--card) 55%, transparent);
    color: var(--txt-body);
    font-weight: 900;
  }
  .gq-alert--success{
    border-color: rgba(34,197,94,.35);
    background: rgba(34,197,94,.10);
    color: rgba(167,243,208,.95);
  }
  .gq-alert--error{
    border-color: rgba(239,68,68,.35);
    background: rgba(239,68,68,.10);
    color: rgba(254,202,202,.95);
  }

  /* ---- grid ---- */
  .gq-grid{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    align-items:start;
  }
  @media (max-width: 980px){
    .gq-grid{ grid-template-columns: 1fr; }
  }

  /* ---- card ---- */
  .gq-card{
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
  html:not([data-theme="dark"]) .gq-card{
    background: rgba(255,255,255,.65);
    border: 1px solid rgba(15,23,42,.12);
    color: rgba(15,23,42,.92);
    box-shadow: 0 12px 32px rgba(15,23,42,.08);
  }

  .gq-title{
    font-weight: 1000;
    margin: 0 0 8px;
    letter-spacing: -0.01em;
    font-size: 16px;
    color: var(--txt-body);
  }
  .gq-sub{
    margin: 0 0 12px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
    line-height: 1.55;
  }

  /* ---- form controls ---- */
  .gq-row{ margin-bottom: 10px; }
  .gq-label{
    display:block;
    font-size: 12px;
    font-weight: 1000;
    margin: 0 0 6px 2px;
    color: color-mix(in oklab, var(--txt-body) 82%, transparent);
  }

  .gq-input,
  .gq-select,
  .gq-textarea{
    width:100%;
    padding:10px 12px;
    border-radius: 12px;
    outline:none;
    border: 1px solid rgba(148,163,184,.22);
    background: rgba(2,6,23,.22);
    background: color-mix(in oklab, var(--card) 35%, transparent);
    color: var(--txt-body);
    transition: border-color .2s ease, box-shadow .2s ease, transform .15s ease, background .2s ease;
  }
  html:not([data-theme="dark"]) .gq-input,
  html:not([data-theme="dark"]) .gq-select,
  html:not([data-theme="dark"]) .gq-textarea{
    background: rgba(255,255,255,.70);
    border: 1px solid rgba(15,23,42,.14);
  }

  .gq-textarea{ resize: vertical; min-height: 86px; }
  .gq-input::placeholder,
  .gq-textarea::placeholder{ color: color-mix(in oklab, var(--txt-body) 45%, transparent); }

  .gq-input:focus,
  .gq-select:focus,
  .gq-textarea:focus{
    border-color: rgba(249,115,22,.55);
    box-shadow: 0 0 0 4px rgba(249,115,22,.14);
  }

  .gq-select option{
    background: #0b1220;
    color: rgba(255,255,255,.92);
  }
  html:not([data-theme="dark"]) .gq-select option{
    background: #fff;
    color: #0f172a;
  }

  .gq-help{
    margin-top: 6px;
    font-size: 11px;
    font-weight: 800;
    color: var(--muted);
    line-height: 1.45;
  }

  .gq-hr{
    opacity:.22;
    border-color: rgba(148,163,184,.22);
    margin: 10px 0 12px;
  }

  /* ---- two column mini grid inside form ---- */
  .gq-miniGrid{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
  }
  @media (max-width: 520px){
    .gq-miniGrid{ grid-template-columns: 1fr; }
  }

  /* ---- checkbox ---- */
  .gq-check{
    display:flex;
    gap: 10px;
    align-items:center;
    padding: 10px 12px;
    border-radius: 14px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.02);
    font-weight: 1000;
    color: var(--txt-body);
    user-select:none;
  }
  .gq-check input{ transform: translateY(1px); }

  /* ---- buttons ---- */
  .gq-actionsBar{
    display:flex;
    gap: 10px;
    flex-wrap:wrap;
    align-items:center;
    margin-top: 10px;
  }

  .gq-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 999px;
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
  .gq-btn:active{ transform: translateY(1px) scale(.99); }

  .gq-btn-primary{
    border-color: rgba(249,115,22,.30);
    background: linear-gradient(90deg, rgba(249,115,22,.95), rgba(251,146,60,.95));
    color: #0b1020;
    box-shadow: 0 16px 38px rgba(249,115,22,.18);
  }
  .gq-btn-primary:hover{
    filter: brightness(1.03);
    box-shadow: 0 20px 48px rgba(249,115,22,.24);
  }

  .gq-btn-ghost:hover{
    border-color: rgba(249,115,22,.38);
    box-shadow: 0 0 0 4px rgba(249,115,22,.12);
    background: rgba(249,115,22,.08);
  }

  .gq-btn-danger{
    border-color: rgba(239,68,68,.35);
    background: rgba(239,68,68,.12);
    color: rgba(254,202,202,.95);
  }
  html:not([data-theme="dark"]) .gq-btn-danger{ color: rgb(127,29,29); }
  .gq-btn-danger:hover{
    border-color: rgba(239,68,68,.55);
    box-shadow: 0 0 0 4px rgba(239,68,68,.12);
  }

  /* ---- list ---- */
  .gq-list{
    display:grid;
    gap: 10px;
    margin-top: 10px;
  }

  .gq-item{
    padding: 12px;
    border: 1px solid rgba(148,163,184,.18);
    border-radius: 16px;
    background: rgba(255,255,255,.02);
  }
  html:not([data-theme="dark"]) .gq-item{
    background: rgba(15,23,42,.03);
  }

  .gq-item-meta{
    font-size: 11px;
    font-weight: 900;
    color: var(--muted);
    display:flex;
    gap: 8px;
    flex-wrap:wrap;
    align-items:center;
  }
  .gq-pill{
    display:inline-flex;
    align-items:center;
    gap: 6px;
    padding: 5px 9px;
    border-radius: 999px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.02);
    font-weight: 1000;
    font-size: 11px;
    color: var(--txt-body);
  }
  .gq-pill--ok{
    border-color: rgba(34,197,94,.30);
    background: rgba(34,197,94,.10);
  }
  .gq-pill--off{
    border-color: rgba(148,163,184,.20);
    background: rgba(148,163,184,.10);
  }

  .gq-item-question{
    font-weight: 1000;
    margin-top: 8px;
    color: var(--txt-body);
    line-height: 1.35;
  }

  .gq-item-actions{
    margin-top: 10px;
    display:flex;
    gap: 8px;
    flex-wrap:wrap;
    align-items:center;
  }

  .gq-back{
    margin-top: 12px;
    display:inline-flex;
    align-items:center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 999px;
    border: 1px solid rgba(148,163,184,.20);
    background: rgba(255,255,255,.03);
    color: var(--txt-body);
    text-decoration:none;
    font-weight: 1000;
  }
  .gq-back:hover{
    border-color: rgba(249,115,22,.38);
    box-shadow: 0 0 0 4px rgba(249,115,22,.12);
    background: rgba(249,115,22,.08);
  }

  /* file input polish */
.gq-input[type="file"]{
  padding: 9px 12px;
}
.gq-input[type="file"]::file-selector-button{
  border: 1px solid rgba(148,163,184,.20);
  background: rgba(255,255,255,.04);
  color: var(--txt-body);
  padding: 8px 12px;
  border-radius: 999px;
  font-weight: 1000;
  cursor: pointer;
  margin-right: 10px;
}
.gq-input[type="file"]::file-selector-button:hover{
  border-color: rgba(249,115,22,.38);
  box-shadow: 0 0 0 4px rgba(249,115,22,.10);
}

.gq-item-img{
  margin-top: 10px;
  width: 100%;
  max-height: 220px;
  object-fit: cover;
  border-radius: 14px;
  border: 1px solid rgba(148,163,184,.18);
  display:block;
}

</style>

<div class="gq-wrap">

  <div class="gq-head">
    <div>
      <h2>Soal untuk: {{ $level->title }}</h2>
      <p>
        Tambah soal MCQ / isian singkat untuk level ini. Catatan: Level dianggap siap kalau <b>tepat 5 soal</b>.
      </p>
    </div>
    <div class="gq-chip" title="Ringkasan">
      <span class="k">Total</span>
      <span class="v">{{ $questions->count() }}</span>
    </div>
  </div>

  @if(session('success'))
    <div class="gq-alert gq-alert--success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="gq-alert gq-alert--error">{{ session('error') }}</div>
  @endif

  <div class="gq-grid">

    {{-- ================= ADD FORM ================= --}}
    <div class="gq-card">
      <div class="gq-title">Tambah Soal</div>
      <div class="gq-sub">
        Untuk <b>MCQ</b> isi opsi A–D dan pilih jawaban benar.
        Untuk <b>Isian</b> isi jawaban benar di “Jawaban benar Isian”.
      </div>

<form method="POST" action="{{ route('admin.game-questions.store', $level->id) }}" enctype="multipart/form-data">
        @csrf

        <div class="gq-row">
          <label class="gq-label">Tipe</label>
          <select name="type" class="gq-select">
            <option value="mcq">Pilihan Ganda (A/B/C/D)</option>
            <option value="fill">Isian Singkat</option>
          </select>
        </div>

        <div class="gq-row">
          <label class="gq-label">Pertanyaan (Wajib)</label>
          <textarea name="question_text" rows="3" class="gq-textarea" placeholder="Tulis pertanyaan..."></textarea>
        </div>

<div class="gq-row">
  <label class="gq-label">Upload Gambar (Opsional)</label>
  <input
    type="file"
    name="image"
    class="gq-input"
    accept="image/*"
  />
  <div class="gq-help">Pilih file dari komputer (jpg/png/webp). Kosongkan jika tidak pakai gambar.</div>
</div>


        <hr class="gq-hr" />

        <div class="gq-row">
          <div class="gq-miniGrid">
            <div>
              <label class="gq-label">Opsi A</label>
              <input name="option_a" placeholder="Opsi A" class="gq-input" />
            </div>
            <div>
              <label class="gq-label">Opsi B</label>
              <input name="option_b" placeholder="Opsi B" class="gq-input" />
            </div>
            <div>
              <label class="gq-label">Opsi C</label>
              <input name="option_c" placeholder="Opsi C" class="gq-input" />
            </div>
            <div>
              <label class="gq-label">Opsi D</label>
              <input name="option_d" placeholder="Opsi D" class="gq-input" />
            </div>
          </div>
        </div>

        <div class="gq-row">
          <label class="gq-label">Jawaban benar MCQ</label>
          <select name="correct_option" class="gq-select">
            <option value="">-</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
          </select>
          <div class="gq-help">Kosongkan jika tipe “Isian”.</div>
        </div>

        <div class="gq-row">
          <label class="gq-label">Jawaban benar Isian</label>
          <input name="correct_text"
                 placeholder="contoh: mas / toba / dll"
                 class="gq-input" />
          <div class="gq-help">Kosongkan jika tipe “MCQ”.</div>
        </div>

        <div class="gq-row">
          <label class="gq-label">Urutan soal</label>
          <input name="order" type="number" min="1" value="1" class="gq-input" />
        </div>

        <div class="gq-row">
          <label class="gq-check">
            <input type="checkbox" name="is_active" value="1" checked />
            Aktif
          </label>
        </div>

        <div class="gq-actionsBar">
          <button class="gq-btn gq-btn-primary" type="submit">
            Tambah Soal
          </button>
          <a href="{{ route('admin.game-levels.index') }}" class="gq-btn gq-btn-ghost">
            Kembali ke Level
          </a>
        </div>
      </form>
    </div>

    {{-- ================= LIST ================= --}}
    <div class="gq-card">
      <div class="gq-title">Daftar Soal ({{ $questions->count() }})</div>
      <div class="gq-sub">
        Tip: Pastikan jumlah soal <b>tepat 5</b> agar level dianggap siap.
      </div>

      <div class="gq-list">
        @foreach($questions as $q)
          <div class="gq-item">
            <div class="gq-item-meta">
              <span class="gq-pill">#{{ $q->order }}</span>
              <span class="gq-pill">{{ strtoupper($q->type) }}</span>
              @if($q->is_active)
                <span class="gq-pill gq-pill--ok">Aktif</span>
              @else
                <span class="gq-pill gq-pill--off">Nonaktif</span>
              @endif
            </div>

            <div class="gq-item-question">
              {{ \Illuminate\Support\Str::limit($q->question_text, 120) }}
            </div>

            @if(!empty($q->image_path))
  <img
    class="gq-item-img"
    src="{{ asset($q->image_path) }}"
    alt="Gambar soal"
    loading="lazy"
  />
@endif


            <div class="gq-item-actions">
              {{-- ✅ TOMBOL EDIT (SUDAH ADA, TETAP) --}}
              <a class="gq-btn gq-btn-ghost"
                 href="{{ route('admin.game-questions.edit', [$level->id, $q->id]) }}">
                Edit
              </a>

              <form method="POST" action="{{ route('admin.game-questions.destroy', [$level->id, $q->id]) }}"
                    onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                @csrf
                @method('DELETE')
                <button class="gq-btn gq-btn-danger" type="submit">Hapus</button>
              </form>
            </div>
          </div>
        @endforeach
      </div>

      <div style="margin-top:12px;">
        <a href="{{ route('admin.game-levels.index') }}" class="gq-back">← Kembali ke Level</a>
      </div>
    </div>

  </div>
</div>
@endsection
