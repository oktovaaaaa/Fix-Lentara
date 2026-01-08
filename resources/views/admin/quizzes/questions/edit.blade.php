{{-- resources/views/admin/quizzes/questions/edit.blade.php (UI ONLY - NEW FULL) --}}
@extends('layouts.admin')

@section('title', 'Admin - Edit Pertanyaan')
@section('page-title', 'Edit Pertanyaan Quiz')

@section('content')
@php
    // NOTE: logic program jangan diubah — hanya UI / form update.
    // $quiz, $question dari controller.
    $scopeLabel = 'Global';
    if ($quiz->island_id && $quiz->tribe) $scopeLabel = 'Suku';
    elseif ($quiz->island_id) $scopeLabel = 'Pulau';

    $opts = $question->options->sortBy('order')->values();

    // map opsi berdasar order (umumnya 0..3)
    $byOrder = [];
    foreach ($opts as $o) {
        $byOrder[(int)$o->order] = $o;
    }

    // tentukan correct index berdasar urutan slot (0..3)
    $correctIndex = 0;
    foreach ([0,1,2,3] as $i) {
        if (!empty($byOrder[$i]) && $byOrder[$i]->is_correct) { $correctIndex = $i; break; }
    }
@endphp

<style>
  /* =========================================================
     ADMIN QUIZ QUESTION EDIT (MANUAL CSS, DARK/LIGHT SAFE)
  ========================================================= */

  .qqe-wrap{
    max-width: 980px;
    margin: 0 auto;
    padding: 8px 0 18px;
    color: var(--txt-body);
  }

  .qqe-card{
    border-radius: 18px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(2,6,23,.35);
    background: color-mix(in oklab, var(--card) 55%, transparent);
    backdrop-filter: blur(14px) saturate(140%);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    box-shadow: 0 18px 45px rgba(0,0,0,.10);
    overflow: hidden;
  }
  html:not([data-theme="dark"]) .qqe-card{
    background: rgba(255,255,255,.72);
    border: 1px solid rgba(15,23,42,.12);
    box-shadow: 0 12px 32px rgba(15,23,42,.08);
  }

  .qqe-head{
    padding: 16px 16px 12px;
    border-bottom: 1px solid rgba(148,163,184,.14);
  }
  html:not([data-theme="dark"]) .qqe-head{
    border-bottom: 1px solid rgba(15,23,42,.10);
  }

  .qqe-title{
    margin:0;
    font-size: 20px;
    font-weight: 1000;
    letter-spacing: -0.01em;
  }

  .qqe-sub{
    margin: 8px 0 0;
    font-size: 12px;
    font-weight: 850;
    line-height: 1.6;
    color: var(--muted);
  }

  .qqe-badges{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    margin-top: 10px;
  }

  .qqe-pill{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.03);
    font-size: 12px;
    font-weight: 1000;
    color: color-mix(in oklab, var(--txt-body) 90%, transparent);
  }
  html:not([data-theme="dark"]) .qqe-pill{
    background: rgba(15,23,42,.03);
    border: 1px solid rgba(15,23,42,.12);
    color: #0f172a;
  }

  .qqe-body{ padding: 14px 16px 16px; }

  .qqe-alert{
    border-radius: 16px;
    padding: 10px 12px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.03);
    color: color-mix(in oklab, var(--txt-body) 92%, transparent);
    font-weight: 900;
    margin: 12px 0 12px;
  }
  .qqe-alert.ok{
    border-color: rgba(34,197,94,.35);
    background: rgba(34,197,94,.10);
    color: rgba(167,243,208,.95);
  }
  .qqe-alert.err{
    border-color: rgba(239,68,68,.35);
    background: rgba(239,68,68,.10);
    color: rgba(254,202,202,.95);
  }

  .qqe-grid{
    display:grid;
    grid-template-columns: 1fr;
    gap: 12px;
  }
  .qqe-grid.two{
    grid-template-columns: 1fr;
  }
  @media (min-width: 820px){
    .qqe-grid.two{ grid-template-columns: 1fr 1fr; }
  }

  .qqe-field{ display:grid; gap: 6px; }

  .qqe-label{
    font-size: 12px;
    font-weight: 1000;
    color: color-mix(in oklab, var(--txt-body) 82%, transparent);
  }

  .qqe-input, .qqe-select, .qqe-textarea, .qqe-file{
    width:100%;
    border-radius: 12px;
    border: 1px solid rgba(148,163,184,.22);
    background: rgba(2,6,23,.25);
    background: color-mix(in oklab, var(--card) 35%, transparent);
    color: var(--txt-body);
    padding: 10px 12px;
    outline:none;
    transition: border-color .15s ease, box-shadow .15s ease;
    font-weight: 850;
  }
  .qqe-textarea{ resize: vertical; }

  html:not([data-theme="dark"]) .qqe-input,
  html:not([data-theme="dark"]) .qqe-select,
  html:not([data-theme="dark"]) .qqe-textarea,
  html:not([data-theme="dark"]) .qqe-file{
    background: rgba(255,255,255,.78);
    border: 1px solid rgba(15,23,42,.14);
  }

  .qqe-input:focus, .qqe-select:focus, .qqe-textarea:focus{
    border-color: rgba(249,115,22,.65);
    box-shadow: 0 0 0 3px rgba(249,115,22,.16);
  }

  .qqe-help{
    font-size: 11px;
    line-height: 1.55;
    color: var(--muted);
    margin-top: 2px;
  }

  .qqe-hr{
    border: none;
    height: 1px;
    background: rgba(148,163,184,.14);
    margin: 10px 0;
  }

  .qqe-opt{
    border-radius: 16px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.03);
    padding: 12px;
  }
  html:not([data-theme="dark"]) .qqe-opt{
    background: rgba(15,23,42,.03);
    border: 1px solid rgba(15,23,42,.10);
  }

  .qqe-optTop{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 10px;
  }

  .qqe-optTitle{
    font-weight: 1000;
    font-size: 13px;
    color: color-mix(in oklab, var(--txt-body) 92%, transparent);
  }

  .qqe-correct{
    display:inline-flex;
    align-items:center;
    gap: 8px;
    padding: 8px 10px;
    border-radius: 999px;
    border: 1px solid rgba(249,115,22,.28);
    background: rgba(249,115,22,.10);
    font-weight: 1000;
    font-size: 12px;
    user-select:none;
  }
  .qqe-correct input{
    width: 16px; height: 16px;
    accent-color: #f97316;
  }

  .qqe-preview{
    margin-top: 10px;
    border-radius: 14px;
    border: 1px solid rgba(148,163,184,.18);
    display:block;
    width: 100%;
    max-width: 520px;
  }

  .qqe-check{
    display:flex;
    align-items:center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 14px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.03);
    font-weight: 950;
    color: color-mix(in oklab, var(--txt-body) 90%, transparent);
    user-select:none;
    margin-top: 8px;
  }
  html:not([data-theme="dark"]) .qqe-check{
    background: rgba(15,23,42,.03);
    border: 1px solid rgba(15,23,42,.10);
  }
  .qqe-check input{
    width: 18px;
    height: 18px;
    accent-color: #f97316;
  }

  .qqe-actions{
    display:flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 6px;
  }
  .qqe-btn{
    border:none;
    cursor:pointer;
    border-radius: 14px;
    padding: 10px 14px;
    font-weight: 1000;
    letter-spacing: .2px;
    transition: transform .12s ease, filter .12s ease, box-shadow .15s ease;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    line-height:1;
    user-select:none;
    white-space:nowrap;
  }
  .qqe-btn:active{ transform: translateY(1px) scale(0.99); }

  .qqe-btn.primary{
    background: #0f172a;
    color: #fff;
  }
  html[data-theme="dark"] .qqe-btn.primary{
    background: linear-gradient(90deg, rgba(249,115,22,.95), rgba(251,146,60,.95));
    color: #111827;
    box-shadow: 0 16px 40px rgba(249,115,22,.18);
  }
  .qqe-btn.primary:hover{ filter: brightness(1.03); }

  .qqe-btn.ghost{
    background: rgba(255,255,255,.06);
    color: color-mix(in oklab, var(--txt-body) 90%, transparent);
    border: 1px solid rgba(148,163,184,.22);
  }
  html:not([data-theme="dark"]) .qqe-btn.ghost{
    background: rgba(15,23,42,.04);
    border: 1px solid rgba(15,23,42,.12);
    color: #0f172a;
  }
  .qqe-btn.ghost:hover{
    border-color: rgba(249,115,22,.35);
    box-shadow: 0 0 0 3px rgba(249,115,22,.12);
  }

  .qqe-hidden{ display:none !important; }

  @media (max-width: 720px){
    .qqe-head, .qqe-body{ padding: 12px 12px; }
  }
</style>

<div class="qqe-wrap">
  <div class="qqe-card">

    <div class="qqe-head">
      <h2 class="qqe-title">Edit Pertanyaan</h2>

      <div class="qqe-sub">
        <div style="font-weight:1000; font-size:13px; color: color-mix(in oklab, var(--txt-body) 92%, transparent);">
          {{ $quiz->title }}
        </div>

        <div class="qqe-badges">
          <span class="qqe-pill">Cakupan: <b style="font-weight:1000;">{{ $scopeLabel }}</b></span>

          @if($quiz->island)
            <span class="qqe-pill">Pulau: <b style="font-weight:1000;">{{ $quiz->island->subtitle ?: $quiz->island->name }}</b></span>
          @endif

          @if($quiz->tribe)
            <span class="qqe-pill">Suku: <b style="font-weight:1000;">{{ $quiz->tribe }}</b></span>
          @endif

          <span class="qqe-pill">Pilih <b style="font-weight:1000;">1 jawaban benar</b></span>
        </div>

        <div class="qqe-help" style="margin-top:10px;">
          Soal <b>gambar</b> boleh punya <b>teks</b> (opsional). Opsi gambar tetap hanya gambar (sesuai permintaanmu).
        </div>
      </div>
    </div>

    <div class="qqe-body">

      @if(session('success'))
        <div class="qqe-alert ok">✅ {{ session('success') }}</div>
      @endif

      @if($errors->any())
        <div class="qqe-alert err">
          <div style="font-weight:1000;margin-bottom:6px;">Gagal:</div>
          <ul style="margin:0; padding-left:18px; font-weight:850;">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      <form method="POST"
            action="{{ route('admin.quiz-questions.update', [$quiz, $question]) }}"
            enctype="multipart/form-data"
            id="questionEditForm"
            class="qqe-grid">
        @csrf
        @method('PUT')

        <div class="qqe-grid two">
          <div class="qqe-field">
            <label class="qqe-label">Order (angka)</label>
            <input class="qqe-input" name="order" value="{{ old('order', $question->order) }}" />
          </div>

          <div class="qqe-field">
            <label class="qqe-label">Tipe Soal</label>
            <select class="qqe-select" name="prompt_type" id="promptType">
              <option value="text"  {{ old('prompt_type', $question->prompt_type)==='text'?'selected':'' }}>Text</option>
              <option value="image" {{ old('prompt_type', $question->prompt_type)==='image'?'selected':'' }}>Image</option>
            </select>
          </div>
        </div>

        <div class="qqe-field" id="promptTextWrap">
          <label class="qqe-label">Teks Pertanyaan (opsional jika Image)</label>
          <textarea class="qqe-textarea" name="prompt_text" rows="3">{{ old('prompt_text', $question->prompt_text) }}</textarea>
        </div>

        <div class="qqe-field" id="promptImageWrap">
          <label class="qqe-label">Gambar Pertanyaan</label>
          <input class="qqe-file" type="file" name="prompt_image" accept="image/*">
          <div class="qqe-help">JPG/PNG/WEBP max 2MB. Upload baru akan mengganti gambar lama.</div>

          @if(!empty($question->prompt_image))
            <img class="qqe-preview" src="{{ asset('storage/'.$question->prompt_image) }}" alt="prompt">

            <label class="qqe-check">
              <input type="checkbox" name="remove_prompt_image" value="1" {{ old('remove_prompt_image') ? 'checked' : '' }}>
              <span>Hapus gambar prompt</span>
            </label>
          @endif
        </div>

        <div class="qqe-field">
          <label class="qqe-label">Penjelasan (opsional)</label>
          <textarea class="qqe-textarea" name="explanation" rows="2">{{ old('explanation', $question->explanation) }}</textarea>
        </div>

        <hr class="qqe-hr">

        <div style="display:flex; align-items:flex-end; justify-content:space-between; gap:12px; flex-wrap:wrap;">
          <div style="font-weight:1000; font-size:14px;">Opsi Jawaban (ABCD)</div>
          <div class="qqe-help" style="margin:0;">Minimal 2 opsi terisi</div>
        </div>

        @for($i=0; $i<4; $i++)
          @php
            $opt = $byOrder[$i] ?? null;
            $letter = chr(65 + $i); // A B C D
            $oldType = old("options.$i.content_type", $opt->content_type ?? 'text');
          @endphp

          <div class="qqe-opt option-block" data-index="{{ $i }}">
            <div class="qqe-optTop">
              <div class="qqe-optTitle">Opsi {{ $letter }}</div>

              <label class="qqe-correct">
                <input type="radio"
                       name="correct_index"
                       value="{{ $i }}"
                       {{ (string)old('correct_index', (string)$correctIndex) === (string)$i ? 'checked' : '' }}>
                Jawaban Benar
              </label>
            </div>

            {{-- simpan id jika ada, supaya controller bisa update bukan bikin baru --}}
            <input type="hidden" name="options[{{ $i }}][id]" value="{{ old("options.$i.id", $opt->id ?? '') }}">

            <div class="qqe-grid two" style="margin-top:6px;">
              <div class="qqe-field">
                <label class="qqe-label">Tipe</label>
                <select class="qqe-select optType" name="options[{{ $i }}][content_type]">
                  <option value="text"  {{ $oldType==='text'?'selected':'' }}>Text</option>
                  <option value="image" {{ $oldType==='image'?'selected':'' }}>Image</option>
                </select>
              </div>

              {{-- order tetap ada untuk kompatibilitas, tapi sistem akan auto urutkan lagi --}}
              <div class="qqe-field">
                <label class="qqe-label">Order (auto)</label>
                <input class="qqe-input" name="options[{{ $i }}][order]" value="{{ old("options.$i.order", $opt->order ?? $i) }}" readonly>
                <div class="qqe-help">Sistem akan menyimpan urutan berurutan otomatis.</div>
              </div>
            </div>

            <div class="qqe-field optTextWrap" style="margin-top:10px;">
              <label class="qqe-label">Isi (Text)</label>
              <input class="qqe-input"
                     name="options[{{ $i }}][content_text]"
                     value="{{ old("options.$i.content_text", $opt->content_type==='text' ? ($opt->content_text ?? '') : '') }}">
            </div>

            <div class="qqe-field optImageWrap" style="margin-top:10px;">
              <label class="qqe-label">Isi (Image)</label>
              <input class="qqe-file" type="file" name="options[{{ $i }}][content_image]" accept="image/*">
              <div class="qqe-help">Jika tidak upload, akan memakai gambar lama (kalau ada).</div>

              @if($opt && $opt->content_type==='image' && !empty($opt->content_image))
                <img class="qqe-preview" src="{{ asset('storage/'.$opt->content_image) }}" alt="option">

                <label class="qqe-check">
                  <input type="checkbox" name="options[{{ $i }}][remove_image]" value="1" {{ old("options.$i.remove_image") ? 'checked' : '' }}>
                  <span>Hapus gambar opsi {{ $letter }}</span>
                </label>
              @endif
            </div>
          </div>
        @endfor

        <div class="qqe-actions">
          <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="qqe-btn ghost">
            Kembali
          </a>

          <button class="qqe-btn primary" type="submit">
            Update Pertanyaan
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function(){
    const promptType = document.getElementById('promptType');
    const promptImageWrap = document.getElementById('promptImageWrap');

    function syncPrompt(){
        const v = promptType.value;
        // prompt text selalu tampil (karena image juga boleh punya teks)
        // prompt image hanya tampil saat image
        promptImageWrap.classList.toggle('qqe-hidden', v !== 'image');
    }
    promptType.addEventListener('change', syncPrompt);
    syncPrompt();

    document.querySelectorAll('.option-block').forEach(block => {
        const select = block.querySelector('.optType');
        const t = block.querySelector('.optTextWrap');
        const i = block.querySelector('.optImageWrap');

        function syncOpt(){
            const v = select.value;
            t.classList.toggle('qqe-hidden', v !== 'text');
            i.classList.toggle('qqe-hidden', v !== 'image');
        }
        select.addEventListener('change', syncOpt);
        syncOpt();
    });
})();
</script>
@endpush
@endsection
