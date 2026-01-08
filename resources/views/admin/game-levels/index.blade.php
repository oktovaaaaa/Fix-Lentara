{{-- resources/views/admin/game-levels/index.blade.php (REPLACE FULL) --}}
@extends('layouts.admin')

@section('page-title', 'Game Levels (Per Pulau)')
@section('content')

@php
  // UI only — logic tetap
@endphp

<style>
  /* =========================================================
     ADMIN GAME LEVELS — SELARAS ADMIN (ORANGE NEON)
     UI ONLY: route/field name/logic TIDAK diubah
  ========================================================= */

  .gl-wrap{
    max-width: 1200px;
    margin: 0 auto;
    padding: 6px 0 18px;
  }

  .gl-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
    margin: 6px 0 14px;
  }
  .gl-head h1{
    margin:0;
    font-size: 20px;
    font-weight: 1000;
    letter-spacing: -0.02em;
    color: var(--txt-body);
  }
  .gl-head p{
    margin:6px 0 0;
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
    line-height: 1.55;
  }

  .gl-chip{
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
    user-select:none;
  }
  .gl-chip .k{ color: var(--muted); }
  .gl-chip .v{ color: var(--txt-body); }

  /* ---- alerts ---- */
  .gl-alert{
    margin:10px 0 12px;
    padding:10px 12px;
    border-radius:14px;
    border:1px solid rgba(255,255,255,.10);
    background: rgba(2,6,23,.35);
    background: color-mix(in oklab, var(--card) 55%, transparent);
    color: var(--txt-body);
    font-weight: 900;
  }
  .gl-alert--success{
    border-color: rgba(34,197,94,.35);
    background: rgba(34,197,94,.10);
    color: rgba(167,243,208,.95);
  }
  .gl-alert--error{
    border-color: rgba(239,68,68,.35);
    background: rgba(239,68,68,.10);
    color: rgba(254,202,202,.95);
  }

  /* ---- layout ---- */
  .gl-grid{
    display:grid;
    grid-template-columns: 1fr 1.15fr;
    gap: 16px;
    align-items:start;
  }
  @media (max-width: 1024px){
    .gl-grid{ grid-template-columns: 1fr; }
  }

  /* ---- cards ---- */
  .gl-card{
    border-radius: 18px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(2,6,23,.35);
    background: color-mix(in oklab, var(--card) 55%, transparent);
    backdrop-filter: blur(14px) saturate(140%);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    box-shadow: 0 18px 45px rgba(0,0,0,.10);
    padding: 14px;
    color: var(--txt-body);
  }
  html:not([data-theme="dark"]) .gl-card{
    background: rgba(255,255,255,.65);
    border: 1px solid rgba(15,23,42,.12);
    box-shadow: 0 12px 32px rgba(15,23,42,.08);
  }

  .gl-title{
    font-weight: 1000;
    margin: 0 0 10px 0;
    letter-spacing: -0.01em;
    font-size: 16px;
    color: var(--txt-body);
  }
  .gl-sub{
    margin: -4px 0 12px 0;
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
    line-height: 1.55;
  }

  /* ---- fields ---- */
  .gl-field{ margin-bottom: 12px; }

  .gl-label{
    display:block;
    font-size:12px;
    font-weight:1000;
    margin:0 0 6px 2px;
    color: color-mix(in oklab, var(--txt-body) 82%, transparent);
  }

  .gl-input,
  .gl-select{
    width:100%;
    padding:10px 12px;
    border-radius:12px;
    outline:none;
    border: 1px solid rgba(148,163,184,.22);
    background: rgba(2,6,23,.22);
    background: color-mix(in oklab, var(--card) 35%, transparent);
    color: var(--txt-body);
    transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
  }
  html:not([data-theme="dark"]) .gl-input,
  html:not([data-theme="dark"]) .gl-select{
    background: rgba(255,255,255,.70);
    border: 1px solid rgba(15,23,42,.14);
  }

  .gl-input::placeholder{ color: color-mix(in oklab, var(--txt-body) 45%, transparent); }

  .gl-input:focus,
  .gl-select:focus{
    border-color: rgba(249,115,22,.55);
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

  /* ---- check ---- */
  .gl-check{
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
    margin: 2px 0 12px;
  }
  .gl-check input{ transform: translateY(1px); }

  /* ---- buttons ---- */
  .gl-btn{
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
    line-height:1;
    white-space: nowrap;
  }
  .gl-btn:active{ transform: translateY(1px) scale(.99); }

  .gl-btn-primary{
    border-color: rgba(249,115,22,.30);
    background: linear-gradient(90deg, rgba(249,115,22,.95), rgba(251,146,60,.95));
    color:#0b1020;
    box-shadow: 0 16px 38px rgba(249,115,22,.18);
  }
  .gl-btn-primary:hover{
    filter: brightness(1.03);
    box-shadow: 0 20px 48px rgba(249,115,22,.24);
  }

  .gl-btn-ghost:hover{
    border-color: rgba(249,115,22,.38);
    box-shadow: 0 0 0 4px rgba(249,115,22,.12);
    background: rgba(249,115,22,.08);
  }

  .gl-btn-danger{
    border-color: rgba(239,68,68,.32);
    background: rgba(239,68,68,.10);
    color: rgba(254,202,202,.95);
  }
  html:not([data-theme="dark"]) .gl-btn-danger{
    color: rgb(127, 29, 29);
    background: rgba(239,68,68,.08);
  }
  .gl-btn-danger:hover{
    background: rgba(239,68,68,.14);
    box-shadow: 0 0 0 4px rgba(239,68,68,.10);
  }

  /* ---- list items ---- */
  .gl-list{
    display:grid;
    gap:10px;
  }
  .gl-item{
    padding: 12px;
    border-radius: 16px;
    border: 1px solid rgba(148,163,184,.16);
    background: rgba(2,6,23,.22);
    background: color-mix(in oklab, var(--card) 42%, transparent);
    color: var(--txt-body);
  }
  html:not([data-theme="dark"]) .gl-item{
    background: rgba(255,255,255,.75);
    border: 1px solid rgba(15,23,42,.10);
  }

  .gl-item-top{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap: 12px;
    flex-wrap:wrap;
  }

  .gl-item-title{
    font-weight: 1000;
    color: var(--txt-body);
    line-height: 1.3;
  }
  .gl-item-meta{
    margin-top: 4px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
    line-height: 1.5;
  }
  .gl-pill{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:6px 10px;
    border-radius:999px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(148,163,184,.10);
    font-size: 11px;
    font-weight: 1000;
    color: var(--txt-body);
    user-select:none;
  }
  html:not([data-theme="dark"]) .gl-pill{
    border: 1px solid rgba(15,23,42,.10);
    background: rgba(15,23,42,.04);
  }

  .gl-actions{
    margin-top: 10px;
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    align-items:center;
  }

  .gl-actions form{ display:inline-block; margin:0; }
</style>

<div class="gl-wrap">

  <div class="gl-head">
    <div>
      <h1>Game Levels (Per Pulau)</h1>
      <p>Tambah level baru & kelola level yang sudah ada. (UI diselaraskan, logika tetap)</p>
    </div>
    <div class="gl-chip" title="Ringkasan">
      <span class="k">Total</span>
      <span class="v">{{ $levels->count() }}</span>
      <span class="k">level</span>
    </div>
  </div>

  @if(session('success'))
    <div class="gl-alert gl-alert--success">{{ session('success') }}</div>
  @endif

  @if(session('error'))
    <div class="gl-alert gl-alert--error">{{ session('error') }}</div>
  @endif

  <div class="gl-grid">

    <div class="gl-card">
      <h3 class="gl-title">Tambah Level</h3>
      <div class="gl-sub">Isi pulau, judul, urutan, lalu simpan.</div>

      <form method="POST" action="{{ route('admin.game-levels.store') }}">
        @csrf

        <div class="gl-field">
          <label class="gl-label">Pulau</label>
          <select name="island_id" class="gl-select">
            @foreach($islands as $island)
              <option value="{{ $island->id }}">{{ $island->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="gl-field">
          <label class="gl-label">Judul Level</label>
          <input
            name="title"
            class="gl-input"
            placeholder="Contoh: Level 1"
          />
        </div>

        <div class="gl-field">
          <label class="gl-label">Urutan</label>
          <input
            name="order"
            type="number"
            min="1"
            value="1"
            class="gl-input"
          />
        </div>

        <label class="gl-check">
          <input type="checkbox" name="is_active" value="1" checked />
          Aktif
        </label>

        <button class="gl-btn gl-btn-primary" type="submit">Simpan</button>
      </form>
    </div>

    <div class="gl-card">
      <h3 class="gl-title">Daftar Level</h3>
      <div class="gl-sub">Kelola soal, edit, atau hapus level.</div>

      <div class="gl-list">
        @foreach($levels as $lv)
          <div class="gl-item">
            <div class="gl-item-top">
              <div class="min-w-0">
                <div class="gl-item-title">
                  {{ $lv->island->name }} — {{ $lv->title }}
                </div>
                <div class="gl-item-meta">
                  Order: <b>{{ $lv->order }}</b>
                  <span style="opacity:.6;">•</span>
                  Aktif: <b>{{ $lv->is_active ? 'Ya' : 'Tidak' }}</b>
                </div>
              </div>

              <span class="gl-pill">
                #{{ $lv->id }}
              </span>
            </div>

            <div class="gl-actions">
              <a
                href="{{ route('admin.game-questions.index', $lv->id) }}"
                class="gl-btn gl-btn-ghost"
              >Kelola Soal</a>

              <a
                href="{{ route('admin.game-levels.edit', $lv->id) }}"
                class="gl-btn gl-btn-ghost"
              >Edit</a>

              <form method="POST" action="{{ route('admin.game-levels.destroy', $lv->id) }}">
                @csrf
                @method('DELETE')
                <button class="gl-btn gl-btn-danger" type="submit">Hapus</button>
              </form>
            </div>
          </div>
        @endforeach
      </div>
    </div>

  </div>
</div>
@endsection
