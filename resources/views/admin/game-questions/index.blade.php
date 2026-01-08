@extends('layouts.admin')
@section('page-title', 'Soal untuk: '.$level->title)
@section('content')

<style>
  /* =========================
     GAME QUESTIONS (FIX KONTRAS TEKS) - VERSI SESUAI VIEW KAMU
     Masalah: card bg gelap tapi teks/label/link ikut warna default (hitam)
     Fix: paksa teks dalam card jadi terang + styling input/select/textarea
  ========================= */

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
    color:rgba(255,255,255,.92); /* ✅ KUNCI: semua teks di card jadi terang */
  }

  .gq-title{
    font-weight:900;
    margin:0 0 10px 0;
    color:#fff;
    letter-spacing:.2px;
  }

  /* ✅ Paksa semua teks umum di dalam card jadi terang */
  .gq-card label,
  .gq-card div,
  .gq-card p,
  .gq-card small,
  .gq-card span{
    color:rgba(255,255,255,.88);
  }
  .gq-card b,
  .gq-card strong{
    color:#fff;
  }

  /* ✅ Input/select/textarea biar cocok dark */
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
  .gq-textarea{ resize:vertical; }

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

  .gq-hr{
    opacity:.18;
    border-color:rgba(255,255,255,.18);
    margin:10px 0;
  }

  .gq-note{
    opacity:.85;
    margin-bottom:8px;
    color:rgba(255,255,255,.80);
  }

  .gq-check{
    display:flex;
    gap:8px;
    align-items:center;
    margin-bottom:10px;
    font-weight:800;
    color:rgba(255,255,255,.88);
  }

  .gq-btn-primary{
    padding:10px 14px;
    border-radius:12px;
    background:#f97316;
    color:#111;
    font-weight:900;
    border:0;
    cursor:pointer;
  }
  .gq-btn-primary:hover{ filter:brightness(1.05); }

  .gq-btn-ghost{
    padding:8px 10px;
    border:1px solid rgba(255,255,255,.12);
    border-radius:10px;
    background:rgba(255,255,255,.04);
    color:rgba(255,255,255,.90);
    font-weight:900;
    cursor:pointer;
  }
  .gq-btn-ghost:hover{
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

  .gq-item-meta{
    opacity:.85;
    color:rgba(255,255,255,.80);
  }

  .gq-item-question{
    font-weight:900;
    margin-top:6px;
    color:#fff;
  }

  .gq-actions{
    margin-top:8px;
    display:flex;
    gap:8px;
    flex-wrap:wrap;
  }

  .gq-link{
    color:rgba(255,255,255,.92);
    text-decoration:none;
    font-weight:900;
    padding:10px 12px;
    border-radius:12px;
    border:1px solid rgba(255,255,255,.12);
    background:rgba(255,255,255,.04);
    display:inline-flex;
    align-items:center;
    line-height:1;
  }
  .gq-link:hover{
    border-color:rgba(249,115,22,.40);
    box-shadow:0 0 0 3px rgba(249,115,22,.12);
  }

  @media (max-width: 980px){
    .gq-grid{ grid-template-columns:1fr; }
  }
</style>

@if(session('success'))
  <div class="gq-alert gq-alert--success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="gq-alert gq-alert--error">{{ session('error') }}</div>
@endif

<div class="gq-grid">

  <div class="gq-card">
    <h3 class="gq-title">Tambah Soal</h3>

    <form method="POST" action="{{ route('admin.game-questions.store', $level->id) }}">
      @csrf

      <div style="margin-bottom:10px;">
        <label style="display:block;font-weight:800;margin:0 0 6px 2px;">Tipe</label>
        <select name="type" class="gq-select">
          <option value="mcq">Pilihan Ganda (A/B/C/D)</option>
          <option value="fill">Isian Singkat</option>
        </select>
      </div>

      <div style="margin-bottom:10px;">
        <label style="display:block;font-weight:800;margin:0 0 6px 2px;">Pertanyaan (Wajib)</label>
        <textarea name="question_text" rows="3" class="gq-textarea"></textarea>
      </div>

      <div style="margin-bottom:10px;">
        <label style="display:block;font-weight:800;margin:0 0 6px 2px;">Path Gambar (Opsional)</label>
        <input name="image_path"
               placeholder="contoh: images/soal/sumatera-1.png"
               class="gq-input" />
      </div>

      <hr class="gq-hr" />
      <div class="gq-note">Untuk MCQ isi opsi A-D dan jawaban benar. Untuk Isian isi jawaban benar.</div>

      <div style="margin-bottom:8px;">
        <input name="option_a" placeholder="Opsi A" class="gq-input" />
      </div>
      <div style="margin-bottom:8px;">
        <input name="option_b" placeholder="Opsi B" class="gq-input" />
      </div>
      <div style="margin-bottom:8px;">
        <input name="option_c" placeholder="Opsi C" class="gq-input" />
      </div>
      <div style="margin-bottom:8px;">
        <input name="option_d" placeholder="Opsi D" class="gq-input" />
      </div>

      <div style="margin-bottom:10px;">
        <label style="display:block;font-weight:800;margin:0 0 6px 2px;">Jawaban benar MCQ</label>
        <select name="correct_option" class="gq-select">
          <option value="">-</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>
      </div>

      <div style="margin-bottom:10px;">
        <label style="display:block;font-weight:800;margin:0 0 6px 2px;">Jawaban benar Isian</label>
        <input name="correct_text"
               placeholder="contoh: mas / toba / dll"
               class="gq-input" />
      </div>

      <div style="margin-bottom:10px;">
        <label style="display:block;font-weight:800;margin:0 0 6px 2px;">Urutan soal</label>
        <input name="order" type="number" min="1" value="1" class="gq-input" />
      </div>

      <label class="gq-check">
        <input type="checkbox" name="is_active" value="1" checked />
        Aktif
      </label>

      <button class="gq-btn-primary" type="submit">Tambah Soal</button>
    </form>
  </div>

  <div class="gq-card">
    <h3 class="gq-title">Daftar Soal ({{ $questions->count() }})</h3>
    <div class="gq-note">Catatan: Level dianggap siap kalau <b>tepat 5 soal</b>.</div>

    <div class="gq-list">
      @foreach($questions as $q)
        <div class="gq-item">
          <div class="gq-item-meta">
            #{{ $q->order }} • {{ strtoupper($q->type) }} • Aktif: {{ $q->is_active ? 'Ya' : 'Tidak' }}
          </div>

          <div class="gq-item-question">
            {{ \Illuminate\Support\Str::limit($q->question_text, 80) }}
          </div>

          <div class="gq-actions">

            {{-- ✅ TOMBOL EDIT (BARU) --}}
            <a class="gq-btn-ghost"
               href="{{ route('admin.game-questions.edit', [$level->id, $q->id]) }}">
              Edit
            </a>

            <form method="POST" action="{{ route('admin.game-questions.destroy', [$level->id, $q->id]) }}">
              @csrf
              @method('DELETE')
              <button class="gq-btn-ghost" type="submit">Hapus</button>
            </form>
          </div>
        </div>
      @endforeach
    </div>

    <div style="margin-top:12px;">
      <a href="{{ route('admin.game-levels.index') }}" class="gq-link">← Kembali ke Level</a>
    </div>
  </div>

</div>
@endsection
