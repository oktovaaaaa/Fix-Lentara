@extends('layouts.admin')

@section('page-title', 'Soal untuk: ' . ($level->title ?? 'Level'))
@section('content')

<style>
  /* =========================
     GAME QUESTIONS - FULL VIEW (FIX KONTRAS TEKS)
     - Dark card (#0b1220)
     - Teks/label/link jadi terang
     - Input/select/textarea tetap kebaca
  ========================= */

  .gq-page-title{
    margin: 0 0 14px 0;
    font-weight: 900;
    font-size: 28px;
    letter-spacing: .2px;
    color: #0b1220;
  }

  .gq-alert{
    margin:10px 0;
    padding:10px 12px;
    border-radius:12px;
    border:1px solid rgba(255,255,255,.10);
    background:rgba(11,18,32,.65);
  }
  .gq-alert--success{
    color:#86efac;
    border-color:rgba(34,197,94,.35);
    background:rgba(34,197,94,.10);
  }
  .gq-alert--error{
    color:#fca5a5;
    border-color:rgba(239,68,68,.35);
    background:rgba(239,68,68,.10);
  }

  .gq-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
    align-items:start;
  }

  .gq-card{
    background:#0b1220;
    border:1px solid rgba(255,255,255,.08);
    border-radius:14px;
    padding:14px;
    color:rgba(255,255,255,.92); /* ✅ KUNCI: teks di card jadi terang */
  }

  .gq-title{
    margin:0 0 10px 0;
    font-weight:900;
    color:#fff;
    letter-spacing:.2px;
  }

  /* Paksa elemen teks dalam card jadi terang (biar tidak hitam) */
  .gq-card label,
  .gq-card p,
  .gq-card small,
  .gq-card span,
  .gq-card div{
    color:rgba(255,255,255,.88);
  }

  .gq-card b,
  .gq-card strong{
    color:#fff;
  }

  .gq-note{
    margin: 8px 0 14px 0;
    opacity:.88;
    color:rgba(255,255,255,.80);
  }

  .gq-field{ margin-bottom:10px; }

  .gq-label{
    display:block;
    font-size:13px;
    font-weight:800;
    margin:0 0 6px 2px;
    color:rgba(255,255,255,.90);
  }

  .gq-input,
  .gq-select,
  .gq-textarea{
    width:100%;
    padding:10px 12px;
    border-radius:10px;
    outline:none;
    border:1px solid rgba(255,255,255,.12);
    background:rgba(255,255,255,.08);
    color:rgba(255,255,255,.92);
  }

  .gq-textarea{ min-height: 96px; resize: vertical; }

  .gq-input::placeholder,
  .gq-textarea::placeholder{
    color:rgba(255,255,255,.45);
  }

  .gq-input:focus,
  .gq-select:focus,
  .gq-textarea:focus{
    border-color:rgba(249,115,22,.55);
    box-shadow:0 0 0 3px rgba(249,115,22,.18);
  }

  .gq-select option{
    background:#0b1220;
    color:rgba(255,255,255,.92);
  }

  .gq-check{
    display:flex;
    gap:8px;
    align-items:center;
    margin: 6px 0 10px 0;
    font-weight:800;
    color:rgba(255,255,255,.88);
  }

  .gq-actions{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:10px;
  }

  .gq-btn{
    padding:10px 14px;
    border-radius:12px;
    border:0;
    cursor:pointer;
    font-weight:900;
    line-height:1;
  }
  .gq-btn--primary{
    background:#f97316;
    color:#111;
  }
  .gq-btn--primary:hover{ filter:brightness(1.05); }

  .gq-link{
    padding:10px 12px;
    border-radius:12px;
    border:1px solid rgba(255,255,255,.12);
    background:rgba(255,255,255,.04);
    color:rgba(255,255,255,.92);
    text-decoration:none;
    font-weight:900;
    display:inline-flex;
    align-items:center;
    line-height:1;
  }
  .gq-link:hover{
    border-color:rgba(249,115,22,.40);
    box-shadow:0 0 0 3px rgba(249,115,22,.12);
  }

  .gq-list{
    display:grid;
    gap:10px;
  }

  .gq-item{
    padding:12px;
    border:1px solid rgba(255,255,255,.10);
    border-radius:12px;
    background:rgba(255,255,255,.03);
    color:rgba(255,255,255,.92);
  }

  .gq-item-top{
    display:flex;
    gap:10px;
    justify-content:space-between;
    align-items:flex-start;
    flex-wrap:wrap;
  }

  .gq-chip{
    display:inline-flex;
    padding:6px 10px;
    border-radius:999px;
    border:1px solid rgba(255,255,255,.12);
    background:rgba(255,255,255,.04);
    color:rgba(255,255,255,.88);
    font-size:12px;
    font-weight:900;
  }

  .gq-muted{
    opacity:.85;
    color:rgba(255,255,255,.80);
    margin-top:4px;
  }

  .gq-mini-actions{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    margin-top:10px;
  }

  .gq-mini-link,
  .gq-mini-btn{
    padding:8px 10px;
    border-radius:10px;
    border:1px solid rgba(255,255,255,.12);
    background:rgba(255,255,255,.04);
    color:rgba(255,255,255,.90);
    text-decoration:none;
    font-weight:900;
    cursor:pointer;
    line-height:1;
    display:inline-flex;
    align-items:center;
  }

  .gq-mini-link:hover,
  .gq-mini-btn:hover{
    border-color:rgba(249,115,22,.40);
    box-shadow:0 0 0 3px rgba(249,115,22,.12);
  }

  @media (max-width: 980px){
    .gq-grid{ grid-template-columns:1fr; }
  }
</style>

<h1 class="gq-page-title">
  Soal untuk: {{ $level->title ?? 'Level' }}
</h1>

@if(session('success'))
  <div class="gq-alert gq-alert--success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="gq-alert gq-alert--error">{{ session('error') }}</div>
@endif

<div class="gq-grid">

  {{-- ================= LEFT: TAMBAH SOAL ================= --}}
  <div class="gq-card">
    <h3 class="gq-title">Tambah Soal</h3>

    <form method="POST" action="{{ route('admin.game-questions.store', $level->id) }}">
      @csrf

      <div class="gq-field">
        <label class="gq-label">Tipe</label>
        <select name="type" class="gq-select">
          <option value="mcq">Pilihan Ganda (A/B/C/D)</option>
          <option value="fill">Isian (tetap pakai opsi A/B/C/D)</option>
        </select>
      </div>

      <div class="gq-field">
        <label class="gq-label">Pertanyaan (Wajib)</label>
        <textarea name="question" class="gq-textarea" placeholder="Tulis pertanyaan..."></textarea>
      </div>

      <div class="gq-field">
        <label class="gq-label">Path Gambar (Opsional)</label>
        <input name="image_path" class="gq-input" placeholder="contoh: images/soal/sumatera-1.png" />
      </div>

      <p class="gq-note">
        Untuk MCQ isi opsi A–D dan jawaban benar. Untuk isian isi jawaban benar (format teks).
      </p>

      <div class="gq-field">
        <label class="gq-label">Opsi A</label>
        <input name="option_a" class="gq-input" placeholder="Opsi A" />
      </div>

      <div class="gq-field">
        <label class="gq-label">Opsi B</label>
        <input name="option_b" class="gq-input" placeholder="Opsi B" />
      </div>

      <div class="gq-field">
        <label class="gq-label">Opsi C</label>
        <input name="option_c" class="gq-input" placeholder="Opsi C" />
      </div>

      <div class="gq-field">
        <label class="gq-label">Opsi D</label>
        <input name="option_d" class="gq-input" placeholder="Opsi D" />
      </div>

      <div class="gq-field">
        <label class="gq-label">Jawaban benar (MCQ)</label>
        <select name="correct_choice" class="gq-select">
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>
      </div>

      <div class="gq-field">
        <label class="gq-label">Jawaban benar (Isian)</label>
        <input name="correct_text" class="gq-input" placeholder="contoh: rendang / pempek / dll" />
      </div>

      <button class="gq-btn gq-btn--primary" type="submit">Simpan</button>
    </form>
  </div>

  {{-- ================= RIGHT: DAFTAR SOAL ================= --}}
  <div class="gq-card">
    <h3 class="gq-title">Daftar Soal ({{ $questions->count() ?? 0 }})</h3>

    <div class="gq-note">
      Catatan: Level dianggap siap kalau <b>tepat 5 soal</b>.
    </div>

    <a class="gq-link" href="{{ route('admin.game-levels.index') }}">← Kembali ke Level</a>

    <div style="height:12px;"></div>

    <div class="gq-list">
      @forelse(($questions ?? collect()) as $q)
        <div class="gq-item">
          <div class="gq-item-top">
            <div>
              <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                <span class="gq-chip">#{{ $loop->iteration }}</span>
                <span class="gq-chip">{{ strtoupper($q->type ?? 'MCQ') }}</span>
              </div>

              <div style="margin-top:8px;">
                <strong>{{ $q->question }}</strong>
              </div>

              @if(!empty($q->image_path))
                <div class="gq-muted">Gambar: {{ $q->image_path }}</div>
              @endif

              <div class="gq-muted" style="margin-top:8px;">
                <div> A: {{ $q->option_a ?? '-' }}</div>
                <div> B: {{ $q->option_b ?? '-' }}</div>
                <div> C: {{ $q->option_c ?? '-' }}</div>
                <div> D: {{ $q->option_d ?? '-' }}</div>
              </div>

              <div class="gq-muted" style="margin-top:8px;">
                Jawaban:
                @if(($q->type ?? 'mcq') === 'fill')
                  <b>{{ $q->correct_text ?? '-' }}</b>
                @else
                  <b>{{ $q->correct_choice ?? '-' }}</b>
                @endif
              </div>
            </div>
          </div>

          <div class="gq-mini-actions">
            <a class="gq-mini-link" href="{{ route('admin.game-questions.edit', [$level->id, $q->id]) }}">Edit</a>

            <form method="POST" action="{{ route('admin.game-questions.destroy', [$level->id, $q->id]) }}">
              @csrf
              @method('DELETE')
              <button class="gq-mini-btn" type="submit">Hapus</button>
            </form>
          </div>
        </div>
      @empty
        <div class="gq-item">
          Belum ada soal untuk level ini.
        </div>
      @endforelse
    </div>
  </div>

</div>
@endsection
