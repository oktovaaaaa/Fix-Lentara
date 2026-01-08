{{-- resources/views/admin/quizzes/edit.blade.php (UI ONLY - REPLACE FULL) --}}
@extends('layouts.admin')

@section('title', 'Admin - Edit Quiz')
@section('page-title', 'Edit Quiz')

@section('content')
@php
    $scopeLabel = 'Global';
    if ($quiz->scope === 'tribe') $scopeLabel = 'Suku';
    elseif ($quiz->scope === 'island') $scopeLabel = 'Pulau';

    // Urutkan pertanyaan biar rapih
    $questions = $quiz->questions->sortBy('order')->values();
@endphp

<style>
  /* =========================================================
     ADMIN QUIZ EDIT (MANUAL CSS, DARK/LIGHT SAFE)
     - UI rapi + kontras aman
     - Tambah tombol EDIT per pertanyaan (route: admin.quiz-questions.edit)
     - Tidak ubah logic data, hanya UI dan link route
  ========================================================= */

  .qe-wrap{
    max-width: 1200px;
    margin: 0 auto;
    padding: 6px 0 18px;
    color: var(--txt-body);
  }

  .qe-grid{
    display:grid;
    grid-template-columns: 1fr;
    gap: 16px;
    align-items: start;
  }
  @media (min-width: 1024px){
    .qe-grid{ grid-template-columns: 1fr 1.35fr; }
  }

  .qe-card{
    border-radius: 18px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(2,6,23,.35);
    background: color-mix(in oklab, var(--card) 55%, transparent);
    backdrop-filter: blur(14px) saturate(140%);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    box-shadow: 0 18px 45px rgba(0,0,0,.10);
    overflow: hidden;
  }
  html:not([data-theme="dark"]) .qe-card{
    background: rgba(255,255,255,.70);
    border: 1px solid rgba(15,23,42,.12);
    box-shadow: 0 12px 32px rgba(15,23,42,.08);
  }

  .qe-head{
    padding: 14px 16px;
    border-bottom: 1px solid rgba(148,163,184,.14);
  }
  html:not([data-theme="dark"]) .qe-head{
    border-bottom: 1px solid rgba(15,23,42,.10);
  }

  .qe-title{
    margin:0;
    font-size: 18px;
    font-weight: 1000;
    letter-spacing: -0.01em;
  }
  .qe-sub{
    margin: 6px 0 0;
    font-size: 12px;
    font-weight: 850;
    line-height: 1.55;
    color: var(--muted);
  }

  .qe-body{ padding: 14px 16px 16px; }

  .qe-alert{
    border-radius: 16px;
    padding: 10px 12px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.03);
    color: color-mix(in oklab, var(--txt-body) 92%, transparent);
    font-weight: 900;
    margin-bottom: 12px;
  }
  .qe-alert.ok{
    border-color: rgba(34,197,94,.35);
    background: rgba(34,197,94,.10);
    color: rgba(167,243,208,.95);
  }
  .qe-alert.err{
    border-color: rgba(239,68,68,.35);
    background: rgba(239,68,68,.10);
    color: rgba(254,202,202,.95);
  }

  .qe-scope{
    border-radius: 16px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.03);
    padding: 12px;
  }
  html:not([data-theme="dark"]) .qe-scope{
    background: rgba(15,23,42,.03);
    border: 1px solid rgba(15,23,42,.10);
  }

  .qe-scope .k{
    font-size: 11px;
    font-weight: 900;
    color: var(--muted);
  }
  .qe-scope .v{
    font-size: 15px;
    font-weight: 1000;
    margin-top: 2px;
    color: color-mix(in oklab, var(--txt-body) 92%, transparent);
  }
  .qe-scope .m{
    font-size: 12px;
    font-weight: 850;
    margin-top: 8px;
    color: var(--muted);
    line-height: 1.4;
  }
  .qe-scope .m b{ color: color-mix(in oklab, var(--txt-body) 92%, transparent); }

  .qe-form{ display:grid; gap: 12px; margin-top: 12px; }

  .qe-field{ display:grid; gap: 6px; }
  .qe-label{
    font-size: 12px;
    font-weight: 1000;
    color: color-mix(in oklab, var(--txt-body) 82%, transparent);
  }

  .qe-input{
    width:100%;
    border-radius: 12px;
    border: 1px solid rgba(148,163,184,.22);
    background: rgba(2,6,23,.25);
    background: color-mix(in oklab, var(--card) 35%, transparent);
    color: var(--txt-body);
    padding: 10px 12px;
    outline: none;
    transition: border-color .15s ease, box-shadow .15s ease;
    font-weight: 850;
  }
  html:not([data-theme="dark"]) .qe-input{
    background: rgba(255,255,255,.75);
    border: 1px solid rgba(15,23,42,.14);
  }
  .qe-input:focus{
    border-color: rgba(249,115,22,.65);
    box-shadow: 0 0 0 3px rgba(249,115,22,.16);
  }

  .qe-check{
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
  }
  html:not([data-theme="dark"]) .qe-check{
    background: rgba(15,23,42,.03);
    border: 1px solid rgba(15,23,42,.10);
  }
  .qe-check input{
    width: 18px;
    height: 18px;
    accent-color: #f97316;
  }

  .qe-btn{
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
  .qe-btn:active{ transform: translateY(1px) scale(0.99); }

  .qe-btn.primary{
    background: #0f172a;
    color: #fff;
  }
  html[data-theme="dark"] .qe-btn.primary{
    background: linear-gradient(90deg, rgba(249,115,22,.95), rgba(251,146,60,.95));
    color: #111827;
    box-shadow: 0 16px 40px rgba(249,115,22,.18);
  }
  .qe-btn.primary:hover{ filter: brightness(1.03); }

  .qe-btn.amber{
    background: rgba(245,158,11,.18);
    border: 1px solid rgba(245,158,11,.28);
    color: rgba(253,230,138,.95);
  }
  html:not([data-theme="dark"]) .qe-btn.amber{
    background: rgba(245,158,11,.18);
    color: rgb(146,64,14);
  }
  .qe-btn.amber:hover{
    box-shadow: 0 0 0 3px rgba(245,158,11,.12);
  }

  .qe-btn.ghost{
    background: rgba(255,255,255,.06);
    color: color-mix(in oklab, var(--txt-body) 90%, transparent);
    border: 1px solid rgba(148,163,184,.22);
  }
  html:not([data-theme="dark"]) .qe-btn.ghost{
    background: rgba(15,23,42,.04);
    border: 1px solid rgba(15,23,42,.12);
    color: #0f172a;
  }
  .qe-btn.ghost:hover{
    border-color: rgba(249,115,22,.35);
    box-shadow: 0 0 0 3px rgba(249,115,22,.12);
  }

  .qe-btn.danger{
    background: rgba(239,68,68,.12);
    border: 1px solid rgba(239,68,68,.35);
    color: rgba(254,202,202,.95);
  }
  html:not([data-theme="dark"]) .qe-btn.danger{
    color: rgb(127,29,29);
  }
  .qe-btn.danger:hover{ box-shadow: 0 0 0 3px rgba(239,68,68,.10); }

  .qe-btn.full{ width: 100%; }

  .qe-split{
    display:flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 12px;
  }

  /* Questions list */
  .qq-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 10px;
    flex-wrap: wrap;
    padding: 14px 16px;
    border-bottom: 1px solid rgba(148,163,184,.14);
  }
  html:not([data-theme="dark"]) .qq-head{
    border-bottom: 1px solid rgba(15,23,42,.10);
  }

  .qq-count{
    font-size: 12px;
    font-weight: 950;
    color: var(--muted);
  }

  .qq-body{ padding: 14px 16px 16px; }

  .qq-list{ display:grid; gap: 12px; }

  .qq-item{
    border-radius: 16px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.03);
    padding: 12px;
  }
  html:not([data-theme="dark"]) .qq-item{
    background: rgba(15,23,42,.03);
    border: 1px solid rgba(15,23,42,.10);
  }

  .qq-top{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap: 12px;
    flex-wrap: wrap;
  }

  .qq-order{
    font-size: 11px;
    font-weight: 950;
    color: var(--muted);
    margin-bottom: 6px;
  }

  .qq-prompt{
    font-weight: 1000;
    color: color-mix(in oklab, var(--txt-body) 92%, transparent);
    line-height: 1.35;
    word-break: break-word;
  }

  .qq-promptHint{
    font-size: 12px;
    font-weight: 900;
    margin: 8px 0 0;
    color: var(--muted);
    line-height: 1.4;
  }

  .qq-img{
    width: 100%;
    max-width: 420px;
    border-radius: 14px;
    border: 1px solid rgba(148,163,184,.18);
    display:block;
    margin-top: 10px;
  }

  .qq-opts{
    margin-top: 12px;
    display:grid;
    grid-template-columns: 1fr;
    gap: 10px;
  }
  @media (min-width: 720px){
    .qq-opts{ grid-template-columns: 1fr 1fr; }
  }

  .qq-opt{
    border-radius: 14px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(2,6,23,.18);
    background: color-mix(in oklab, var(--card) 35%, transparent);
    padding: 10px 10px;
  }
  html:not([data-theme="dark"]) .qq-opt{
    background: rgba(255,255,255,.80);
    border: 1px solid rgba(15,23,42,.10);
  }

  .qq-opt .tag{
    font-size: 11px;
    font-weight: 1000;
    margin-bottom: 6px;
    color: var(--muted);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 10px;
  }

  .qq-opt.ok{
    border-color: rgba(34,197,94,.35);
    background: rgba(34,197,94,.08);
  }
  html:not([data-theme="dark"]) .qq-opt.ok{
    background: rgba(34,197,94,.08);
  }

  .qq-opt img{
    width: 100%;
    border-radius: 12px;
    border: 1px solid rgba(148,163,184,.18);
    display:block;
  }

  .qq-exp{
    margin-top: 12px;
    font-size: 12px;
    font-weight: 850;
    color: var(--muted);
    line-height: 1.5;
  }
  .qq-exp b{ color: color-mix(in oklab, var(--txt-body) 92%, transparent); }

  .qq-actions{
    display:flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items:center;
    justify-content:flex-end;
    margin-top: 8px;
  }

  .qq-empty{
    color: var(--muted);
    font-weight: 950;
    padding: 6px 0;
  }

  @media (max-width: 720px){
    .qe-head, .qe-body, .qq-head, .qq-body{ padding: 12px 12px; }
  }
</style>

<div class="qe-wrap">

    @if(session('success'))
        <div class="qe-alert ok">✅ {{ session('success') }}</div>
    @endif

    <div class="qe-grid">

        {{-- LEFT: SETTINGS --}}
        <div class="qe-card">
            <div class="qe-head">
                <h2 class="qe-title">Pengaturan Quiz</h2>
                <p class="qe-sub">Scope dikunci agar tidak membingungkan. Ubah judul & status saja.</p>
            </div>

            <div class="qe-body">
                <div class="qe-scope">
                    <div class="k">Cakupan</div>
                    <div class="v">{{ $scopeLabel }}</div>

                    @if($quiz->island)
                        <div class="m">
                            Pulau: <b>{{ $quiz->island->subtitle ?: $quiz->island->name }}</b>
                        </div>
                    @endif
                    @if($quiz->tribe)
                        <div class="m">
                            Suku: <b>{{ $quiz->tribe }}</b>
                        </div>
                    @endif
                </div>

                @if($errors->any())
                    <div class="qe-alert err" style="margin-top:12px;">
                        <div style="font-weight:1000;margin-bottom:6px;">Gagal:</div>
                        <ul style="margin:0; padding-left:18px; font-weight:850;">
                            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}" class="qe-form">
                    @csrf
                    @method('PUT')

                    <div class="qe-field">
                        <label class="qe-label">Judul</label>
                        <input class="qe-input" name="title" value="{{ old('title', $quiz->title) }}" />
                    </div>

                    <label class="qe-check">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $quiz->is_active) ? 'checked' : '' }}>
                        <span>Aktif</span>
                    </label>

                    <button class="qe-btn primary full" type="submit">
                        Update
                    </button>
                </form>

                <div style="margin-top: 14px;">
                    <a href="{{ route('admin.quiz-questions.create', $quiz) }}" class="qe-btn amber full">
                        + Tambah Pertanyaan
                    </a>
                </div>

                <div class="qe-split">
                    <a href="{{ route('admin.quizzes.index') }}" class="qe-btn ghost" style="flex:1;">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        {{-- RIGHT: QUESTIONS --}}
        <div class="qe-card">
            <div class="qq-head">
                <div>
                    <h3 class="qe-title" style="font-size:16px; margin:0;">Pertanyaan</h3>
                    <div class="qe-sub" style="margin-top:4px;">Kelola isi pertanyaan & opsi jawaban untuk quiz ini.</div>
                </div>
                <span class="qq-count">{{ $questions->count() }} item</span>
            </div>

            <div class="qq-body">
                <div class="qq-list">
                    @forelse($questions as $q)
                        @php
                            $opts = $q->options->sortBy('order')->values();
                        @endphp
                        <div class="qq-item">
                            <div class="qq-top">
                                <div style="flex:1; min-width: 0;">
                                    <div class="qq-order">Order: {{ $q->order }}</div>

                                    <div class="qq-prompt">
                                        @if($q->prompt_type === 'text')
                                            {{ $q->prompt_text }}
                                        @else
                                            {{-- image prompt --}}
                                            @if(!empty($q->prompt_text))
                                                <div class="qq-promptHint">
                                                    {{ $q->prompt_text }}
                                                </div>
                                            @else
                                                <div class="qq-promptHint">[Soal Gambar]</div>
                                            @endif

                                            @if(!empty($q->prompt_image))
                                                <img class="qq-img" src="{{ asset('storage/'.$q->prompt_image) }}" alt="prompt">
                                            @endif
                                        @endif
                                    </div>

                                    <div class="qq-opts">
                                        @foreach($opts as $idx => $opt)
                                            @php
                                              $letter = chr(65 + $idx); // A,B,C,D...
                                            @endphp
                                            <div class="qq-opt {{ $opt->is_correct ? 'ok' : '' }}">
                                                <div class="tag">
                                                    <span>
                                                      {{ $letter }} · {{ $opt->is_correct ? '✅ Benar' : '• Opsi' }}
                                                    </span>
                                                    <span style="opacity:.75;">order: {{ $opt->order }}</span>
                                                </div>

                                                @if($opt->content_type === 'text')
                                                    <div style="font-weight:900; line-height:1.4; color: color-mix(in oklab, var(--txt-body) 92%, transparent);">
                                                        {{ $opt->content_text }}
                                                    </div>
                                                @else
                                                    @if(!empty($opt->content_image))
                                                      <img src="{{ asset('storage/'.$opt->content_image) }}" alt="option">
                                                    @else
                                                      <div style="font-weight:900; color: var(--muted);">[gambar kosong]</div>
                                                    @endif
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    @if(!empty($q->explanation))
                                        <div class="qq-exp">
                                            <b>Penjelasan:</b> {{ $q->explanation }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Actions --}}
                                <div style="display:flex; flex-direction:column; gap:10px; align-items:flex-end;">
                                    <a class="qe-btn ghost"
                                       href="{{ route('admin.quiz-questions.edit', [$quiz, $q]) }}">
                                        Edit
                                    </a>

                                    <form method="POST"
                                          action="{{ route('admin.quiz-questions.destroy', [$quiz, $q]) }}"
                                          onsubmit="return confirm('Hapus pertanyaan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="qe-btn danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="qq-empty">Belum ada pertanyaan. Klik “Tambah Pertanyaan”.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
