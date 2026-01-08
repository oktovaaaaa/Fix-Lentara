@extends('layouts.admin')
@section('page-title', 'Edit Soal - '.$level->title)
@section('content')

<style>
  .gqe-wrap{ max-width: 980px; }
  .gqe-card{
    background:#0b1220;
    border:1px solid rgba(255,255,255,.08);
    border-radius:16px;
    padding:16px;
    color:rgba(255,255,255,.92);
  }
  .gqe-title{
    font-weight:1000;
    margin:0 0 10px 0;
    color:#fff;
    letter-spacing:.2px;
    font-size:20px;
  }
  .gqe-note{
    opacity:.85;
    margin:0 0 14px 0;
    color:rgba(255,255,255,.80);
  }
  .gqe-field{ margin-bottom:12px; }
  .gqe-label{
    display:block;
    font-weight:900;
    margin:0 0 6px 2px;
    color:rgba(255,255,255,.88);
  }
  .gqe-input,.gqe-select,.gqe-textarea{
    width:100%;
    padding:10px 12px;
    border-radius:12px;
    outline:none;
    border:1px solid rgba(255,255,255,.12);
    background:rgba(255,255,255,.08);
    color:rgba(255,255,255,.92);
  }
  .gqe-textarea{ resize:vertical; }
  .gqe-input:focus,.gqe-select:focus,.gqe-textarea:focus{
    border-color:rgba(249,115,22,.55);
    box-shadow:0 0 0 3px rgba(249,115,22,.18);
  }
  .gqe-row{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
  }
  .gqe-actions{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:10px;
    align-items:center;
  }
  .gqe-btn-primary{
    padding:10px 14px;
    border-radius:12px;
    background:#f97316;
    color:#111;
    font-weight:1000;
    border:0;
    cursor:pointer;
  }
  .gqe-btn-primary:hover{ filter:brightness(1.05); }
  .gqe-btn-ghost{
    padding:10px 12px;
    border:1px solid rgba(255,255,255,.12);
    border-radius:12px;
    background:rgba(255,255,255,.04);
    color:rgba(255,255,255,.90);
    font-weight:1000;
    cursor:pointer;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    line-height:1;
  }
  .gqe-btn-ghost:hover{
    border-color:rgba(249,115,22,.40);
    box-shadow:0 0 0 3px rgba(249,115,22,.12);
  }
  .gqe-check{
    display:flex;
    gap:10px;
    align-items:center;
    margin-top:6px;
    font-weight:900;
    color:rgba(255,255,255,.88);
  }

  .gqe-alert{
    margin:10px 0 14px;
    padding:10px 12px;
    border-radius:12px;
    border:1px solid rgba(255,255,255,.10);
    background:rgba(11,18,32,.65);
  }
  .gqe-alert--success{
    color:#86efac;
    border-color:rgba(34,197,94,.35);
    background:rgba(34,197,94,.10);
  }
  .gqe-alert--error{
    color:#fca5a5;
    border-color:rgba(239,68,68,.35);
    background:rgba(239,68,68,.10);
  }

  @media (max-width: 900px){
    .gqe-row{ grid-template-columns:1fr; }
  }
</style>

<div class="gqe-wrap">
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
    <div class="gqe-title">Edit Soal (ID: {{ $question->id }})</div>
    <div class="gqe-note">
      Level: <b>{{ $level->title }}</b> • Tipe soal bisa diubah, tapi pastikan field yang wajib terisi sesuai tipe.
    </div>

    <form method="POST" action="{{ route('admin.game-questions.update', [$level->id, $question->id]) }}">
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
        <textarea name="question_text" rows="3" class="gqe-textarea">{{ old('question_text', $question->question_text) }}</textarea>
      </div>

      <div class="gqe-field">
        <label class="gqe-label">Path Gambar (Opsional)</label>
        <input name="image_path" class="gqe-input"
               value="{{ old('image_path', $question->image_path) }}"
               placeholder="contoh: images/soal/sumatera-1.png">
      </div>

      <div class="gqe-note" style="margin-top:8px;">
        <b>MCQ</b>: isi opsi A-D + jawaban benar (A/B/C/D).<br>
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
        </div>

        <div class="gqe-field">
          <label class="gqe-label">Jawaban benar Isian</label>
          <input name="correct_text" class="gqe-input" value="{{ old('correct_text', $question->correct_text) }}"
                 placeholder="contoh: mas / toba / dll">
        </div>
      </div>

      <label class="gqe-check">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $question->is_active) ? 'checked' : '' }}>
        Aktif
      </label>

      <div class="gqe-actions">
        <button class="gqe-btn-primary" type="submit">Simpan Perubahan</button>
        <a class="gqe-btn-ghost" href="{{ route('admin.game-levels.edit', $level->id) }}">← Kembali ke Level</a>
      </div>
    </form>
  </div>
</div>

@endsection
