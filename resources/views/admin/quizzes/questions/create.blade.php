{{-- resources/views/admin/quizzes/questions/create.blade.php (UI ONLY - REPLACE FULL) --}}
@extends('layouts.admin')

@section('title', 'Admin - Tambah Pertanyaan')
@section('page-title', 'Tambah Pertanyaan Quiz')

@section('content')
@php
    // NOTE: logic program jangan diubah — hanya UI/kontras/layout.
@endphp

<style>
  /* =========================================================
     ADMIN QUIZ QUESTION CREATE (MANUAL CSS, DARK/LIGHT SAFE)
     - prompt image + prompt text (opsional)
     - opsi order auto (readonly)
     - Tidak mengubah name field / route / JS logic
  ========================================================= */

  .qqc-wrap{
    max-width: 980px;
    margin: 0 auto;
    padding: 8px 0 18px;
    color: var(--txt-body);
  }

  .qqc-card{
    border-radius: 18px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(2,6,23,.35);
    background: color-mix(in oklab, var(--card) 55%, transparent);
    backdrop-filter: blur(14px) saturate(140%);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    box-shadow: 0 18px 45px rgba(0,0,0,.10);
    overflow: hidden;
  }
  html:not([data-theme="dark"]) .qqc-card{
    background: rgba(255,255,255,.72);
    border: 1px solid rgba(15,23,42,.12);
    box-shadow: 0 12px 32px rgba(15,23,42,.08);
  }

  .qqc-head{
    padding: 16px 16px 12px;
    border-bottom: 1px solid rgba(148,163,184,.14);
  }
  html:not([data-theme="dark"]) .qqc-head{
    border-bottom: 1px solid rgba(15,23,42,.10);
  }

  .qqc-title{
    margin:0;
    font-size: 20px;
    font-weight: 1000;
    letter-spacing: -0.01em;
  }

  .qqc-body{ padding: 14px 16px 16px; }

  .qqc-sub{
    margin: 8px 0 0;
    font-size: 12px;
    font-weight: 850;
    line-height: 1.6;
    color: var(--muted);
  }

  .qqc-badgeRow{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    margin-top: 10px;
  }
  .qqc-pill{
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
  html:not([data-theme="dark"]) .qqc-pill{
    background: rgba(15,23,42,.03);
    border: 1px solid rgba(15,23,42,.12);
    color: #0f172a;
  }

  .qqc-alert{
    border-radius: 16px;
    padding: 10px 12px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.03);
    color: color-mix(in oklab, var(--txt-body) 92%, transparent);
    font-weight: 900;
    margin: 12px 0 12px;
  }
  .qqc-alert.err{
    border-color: rgba(239,68,68,.35);
    background: rgba(239,68,68,.10);
    color: rgba(254,202,202,.95);
  }

  .qqc-grid{
    display:grid;
    grid-template-columns: 1fr;
    gap: 12px;
  }
  .qqc-grid.two{
    grid-template-columns: 1fr;
  }
  @media (min-width: 820px){
    .qqc-grid.two{ grid-template-columns: 1fr 1fr; }
  }

  .qqc-field{ display:grid; gap: 6px; }

  .qqc-label{
    font-size: 12px;
    font-weight: 1000;
    color: color-mix(in oklab, var(--txt-body) 82%, transparent);
  }

  .qqc-input, .qqc-select, .qqc-textarea, .qqc-file{
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
  .qqc-textarea{ resize: vertical; }
  html:not([data-theme="dark"]) .qqc-input,
  html:not([data-theme="dark"]) .qqc-select,
  html:not([data-theme="dark"]) .qqc-textarea,
  html:not([data-theme="dark"]) .qqc-file{
    background: rgba(255,255,255,.78);
    border: 1px solid rgba(15,23,42,.14);
  }

  .qqc-input:focus, .qqc-select:focus, .qqc-textarea:focus{
    border-color: rgba(249,115,22,.65);
    box-shadow: 0 0 0 3px rgba(249,115,22,.16);
  }

  .qqc-select option{
    background: #0b1220;
    color: rgba(255,255,255,.92);
  }
  html:not([data-theme="dark"]) .qqc-select option{
    background: #fff;
    color: #0f172a;
  }

  .qqc-help{
    font-size: 11px;
    line-height: 1.55;
    color: var(--muted);
    margin-top: 2px;
  }

  .qqc-hr{
    border: none;
    height: 1px;
    background: rgba(148,163,184,.14);
    margin: 10px 0;
  }

  .qqc-optsHead{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap: 12px;
    margin-top: 2px;
  }
  .qqc-optsHead h3{
    margin:0;
    font-size: 14px;
    font-weight: 1000;
    letter-spacing: .2px;
  }
  .qqc-optsHead .hint{
    font-size: 11px;
    font-weight: 900;
    color: var(--muted);
  }

  /* option block */
  .qqc-opt{
    border-radius: 16px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.03);
    padding: 12px;
  }
  html:not([data-theme="dark"]) .qqc-opt{
    background: rgba(15,23,42,.03);
    border: 1px solid rgba(15,23,42,.10);
  }

  .qqc-optTop{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 10px;
  }
  .qqc-optTitle{
    font-weight: 1000;
    font-size: 13px;
    color: color-mix(in oklab, var(--txt-body) 92%, transparent);
  }

  .qqc-correct{
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
  .qqc-correct input{
    width: 16px; height: 16px;
    accent-color: #f97316;
  }

  .qqc-optGrid{
    display:grid;
    grid-template-columns: 1fr;
    gap: 10px;
  }
  @media (min-width: 820px){
    .qqc-optGrid{ grid-template-columns: 1fr 1fr 1fr; }
  }

  .qqc-mutedSmall{
    font-size: 11px;
    font-weight: 850;
    color: var(--muted);
    display:flex;
    align-items:flex-end;
  }

  .qqc-actions{
    display:flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 6px;
  }
  .qqc-btn{
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
  .qqc-btn:active{ transform: translateY(1px) scale(0.99); }

  .qqc-btn.primary{
    background: #0f172a;
    color: #fff;
  }
  html[data-theme="dark"] .qqc-btn.primary{
    background: linear-gradient(90deg, rgba(249,115,22,.95), rgba(251,146,60,.95));
    color: #111827;
    box-shadow: 0 16px 40px rgba(249,115,22,.18);
  }
  .qqc-btn.primary:hover{ filter: brightness(1.03); }

  .qqc-btn.ghost{
    background: rgba(255,255,255,.06);
    color: color-mix(in oklab, var(--txt-body) 90%, transparent);
    border: 1px solid rgba(148,163,184,.22);
  }
  html:not([data-theme="dark"]) .qqc-btn.ghost{
    background: rgba(15,23,42,.04);
    border: 1px solid rgba(15,23,42,.12);
    color: #0f172a;
  }
  .qqc-btn.ghost:hover{
    border-color: rgba(249,115,22,.35);
    box-shadow: 0 0 0 3px rgba(249,115,22,.12);
  }

  .qqc-hidden{ display:none !important; }

  @media (max-width: 720px){
    .qqc-head, .qqc-body{ padding: 12px 12px; }
  }
</style>

<div class="qqc-wrap">
  <div class="qqc-card">
    <div class="qqc-head">
      <h2 class="qqc-title">Tambah Pertanyaan</h2>

      <div class="qqc-sub">
        <div style="font-weight:1000; font-size:13px; color: color-mix(in oklab, var(--txt-body) 92%, transparent);">
          {{ $quiz->title }}
        </div>

        @php
          $scopeLabel = 'Global';
          if ($quiz->island_id && $quiz->tribe) $scopeLabel = 'Suku';
          elseif ($quiz->island_id) $scopeLabel = 'Pulau';
        @endphp

        <div class="qqc-badgeRow">
          <span class="qqc-pill">Cakupan: <b style="font-weight:1000;">{{ $scopeLabel }}</b></span>

          @if($quiz->island)
            <span class="qqc-pill">Pulau: <b style="font-weight:1000;">{{ $quiz->island->subtitle ?: $quiz->island->name }}</b></span>
          @endif

          @if($quiz->tribe)
            <span class="qqc-pill">Suku: <b style="font-weight:1000;">{{ $quiz->tribe }}</b></span>
          @endif

          <span class="qqc-pill">Pilih <b style="font-weight:1000;">1 jawaban benar</b></span>
        </div>

        <div class="qqc-help" style="margin-top:10px;">
          Soal bisa <b>Text</b> atau <b>Image</b>. Jika pilih <b>Image</b>, kamu tetap bisa isi <b>teks pertanyaan</b> (opsional).
        </div>
      </div>
    </div>

    <div class="qqc-body">

      @if($errors->any())
        <div class="qqc-alert err">
          <div style="font-weight:1000;margin-bottom:6px;">Gagal:</div>
          <ul style="margin:0; padding-left:18px; font-weight:850;">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      <form method="POST"
            action="{{ route('admin.quiz-questions.store', $quiz) }}"
            enctype="multipart/form-data"
            id="questionForm"
            class="qqc-grid">
        @csrf

        <div class="qqc-grid two">
          <div class="qqc-field">
            <label class="qqc-label">Order (angka)</label>
            <input class="qqc-input" name="order" value="{{ old('order', 0) }}" />
            <div class="qqc-help">Urutan pertanyaan di quiz ini (0 = paling atas).</div>
          </div>

          <div class="qqc-field">
            <label class="qqc-label">Tipe Soal</label>
            <select class="qqc-select" name="prompt_type" id="promptType">
              <option value="text" {{ old('prompt_type','text')==='text'?'selected':'' }}>Text</option>
              <option value="image" {{ old('prompt_type')==='image'?'selected':'' }}>Image</option>
            </select>
          </div>
        </div>

        {{-- ✅ Prompt text selalu ada (image pun boleh punya teks) --}}
        <div class="qqc-field" id="promptTextWrap">
          <label class="qqc-label">Teks Pertanyaan (wajib jika Text, opsional jika Image)</label>
          <textarea class="qqc-textarea" name="prompt_text" rows="3">{{ old('prompt_text') }}</textarea>
        </div>

        {{-- prompt image hanya saat image --}}
        <div class="qqc-field qqc-hidden" id="promptImageWrap">
          <label class="qqc-label">Gambar Pertanyaan</label>
          <input class="qqc-file" type="file" name="prompt_image" accept="image/*">
          <div class="qqc-help">JPG/PNG/WEBP max 2MB</div>
        </div>

        <div class="qqc-field">
          <label class="qqc-label">Penjelasan (opsional)</label>
          <textarea class="qqc-textarea" name="explanation" rows="2">{{ old('explanation') }}</textarea>
        </div>

        <hr class="qqc-hr">

        <div class="qqc-optsHead">
          <h3>Opsi Jawaban (ABCD)</h3>
          <div class="hint">Minimal 2, maksimal 4 slot</div>
        </div>

        @for($i=0; $i<4; $i++)
          @php
            $letter = chr(65 + $i);
          @endphp
          <div class="qqc-opt option-block" data-index="{{ $i }}">
            <div class="qqc-optTop">
              <div class="qqc-optTitle">Opsi {{ $letter }}</div>

              <label class="qqc-correct">
                <input type="radio"
                       name="correct_index"
                       value="{{ $i }}"
                       {{ (string)old('correct_index','0')===(string)$i ? 'checked' : '' }}>
                Jawaban Benar
              </label>
            </div>

            <div class="qqc-optGrid">
              <div class="qqc-field">
                <label class="qqc-label">Tipe</label>
                <select class="qqc-select optType" name="options[{{ $i }}][content_type]">
                  <option value="text" {{ old("options.$i.content_type",'text')==='text'?'selected':'' }}>Text</option>
                  <option value="image" {{ old("options.$i.content_type")==='image'?'selected':'' }}>Image</option>
                </select>
              </div>

              <div class="qqc-field">
                <label class="qqc-label">Order (auto)</label>
                <input class="qqc-input"
                       name="options[{{ $i }}][order]"
                       value="{{ old("options.$i.order",$i) }}"
                       readonly>
                <div class="qqc-help">Urutan disimpan otomatis berurutan.</div>
              </div>

              <div class="qqc-mutedSmall">
                (ABCD default)
              </div>
            </div>

            <div class="qqc-field optTextWrap" style="margin-top:10px;">
              <label class="qqc-label">Isi (Text)</label>
              <input class="qqc-input" name="options[{{ $i }}][content_text]" value="{{ old("options.$i.content_text") }}">
              <div class="qqc-help">Kosongkan jika tidak dipakai.</div>
            </div>

            <div class="qqc-field optImageWrap qqc-hidden" style="margin-top:10px;">
              <label class="qqc-label">Isi (Image)</label>
              <input class="qqc-file" type="file" name="options[{{ $i }}][content_image]" accept="image/*">
              <div class="qqc-help">Jika memilih image, upload file. (tanpa teks)</div>
            </div>
          </div>
        @endfor

        <div class="qqc-actions">
          <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="qqc-btn ghost">
            Kembali
          </a>

          <button class="qqc-btn primary" type="submit">
            Simpan Pertanyaan
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
        // promptText selalu tampil (image pun boleh punya teks)
        promptImageWrap.classList.toggle('qqc-hidden', v !== 'image');
    }
    promptType.addEventListener('change', syncPrompt);
    syncPrompt();

    document.querySelectorAll('.option-block').forEach(block => {
        const select = block.querySelector('.optType');
        const t = block.querySelector('.optTextWrap');
        const i = block.querySelector('.optImageWrap');

        function syncOpt(){
            const v = select.value;
            t.classList.toggle('qqc-hidden', v !== 'text');
            i.classList.toggle('qqc-hidden', v !== 'image');
        }
        select.addEventListener('change', syncOpt);
        syncOpt();
    });
})();
</script>
@endpush
@endsection
