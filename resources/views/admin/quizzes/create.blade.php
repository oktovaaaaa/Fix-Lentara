{{-- resources/views/admin/quizzes/create.blade.php (UI ONLY - REPLACE FULL) --}}
@extends('layouts.admin')

@section('title', 'Admin - Buat Quiz')
@section('page-title', 'Buat Quiz')

@section('content')
@php
    // NOTE: logic program jangan diubah — hanya UI.
    // $islands, $tribesConfig sudah dari controller (sesuai code kamu).
@endphp

<style>
  /* =========================================================
     ADMIN QUIZ CREATE (MANUAL CSS, DARK/LIGHT SAFE)
     - Kontras aman untuk tema gelap
     - Layout rapi + helper text jelas
     - Tidak mengubah name field / route / JS logic
  ========================================================= */

  .qc-wrap{
    max-width: 860px;
    margin: 0 auto;
    padding: 8px 0 18px;
    color: var(--txt-body);
  }

  .qc-card{
    border-radius: 18px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(2,6,23,.35);
    background: color-mix(in oklab, var(--card) 55%, transparent);
    backdrop-filter: blur(14px) saturate(140%);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    box-shadow: 0 18px 45px rgba(0,0,0,.10);
    overflow: hidden;
  }
  html:not([data-theme="dark"]) .qc-card{
    background: rgba(255,255,255,.72);
    border: 1px solid rgba(15,23,42,.12);
    box-shadow: 0 12px 32px rgba(15,23,42,.08);
  }

  .qc-head{
    padding: 16px 16px 12px;
    border-bottom: 1px solid rgba(148,163,184,.14);
  }
  html:not([data-theme="dark"]) .qc-head{
    border-bottom: 1px solid rgba(15,23,42,.10);
  }

  .qc-title{
    margin:0;
    font-size: 20px;
    font-weight: 1000;
    letter-spacing: -0.01em;
  }
  .qc-sub{
    margin: 6px 0 0;
    font-size: 12px;
    font-weight: 850;
    line-height: 1.6;
    color: var(--muted);
  }

  .qc-body{ padding: 14px 16px 16px; }

  .qc-alert{
    border-radius: 16px;
    padding: 10px 12px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.03);
    color: color-mix(in oklab, var(--txt-body) 92%, transparent);
    font-weight: 900;
    margin-bottom: 12px;
  }
  .qc-alert.err{
    border-color: rgba(239,68,68,.35);
    background: rgba(239,68,68,.10);
    color: rgba(254,202,202,.95);
  }

  .qc-grid{
    display:grid;
    grid-template-columns: 1fr;
    gap: 12px;
  }
  @media (min-width: 820px){
    .qc-grid.two{ grid-template-columns: 1fr 1fr; }
  }

  .qc-field{ display:grid; gap: 6px; }

  .qc-label{
    font-size: 12px;
    font-weight: 1000;
    color: color-mix(in oklab, var(--txt-body) 82%, transparent);
  }

  .qc-input, .qc-select{
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
  html:not([data-theme="dark"]) .qc-input,
  html:not([data-theme="dark"]) .qc-select{
    background: rgba(255,255,255,.78);
    border: 1px solid rgba(15,23,42,.14);
  }

  .qc-input::placeholder{ color: color-mix(in oklab, var(--txt-body) 40%, transparent); }

  .qc-input:focus, .qc-select:focus{
    border-color: rgba(249,115,22,.65);
    box-shadow: 0 0 0 3px rgba(249,115,22,.16);
  }

  .qc-select option{
    background: #0b1220;
    color: rgba(255,255,255,.92);
  }
  html:not([data-theme="dark"]) .qc-select option{
    background: #fff;
    color: #0f172a;
  }

  .qc-help{
    font-size: 11px;
    line-height: 1.55;
    color: var(--muted);
    margin-top: 2px;
  }

  .qc-scopeHint{
    border-radius: 16px;
    border: 1px dashed rgba(148,163,184,.22);
    background: rgba(255,255,255,.03);
    padding: 10px 12px;
    font-size: 12px;
    font-weight: 850;
    line-height: 1.6;
    color: var(--muted);
  }
  html:not([data-theme="dark"]) .qc-scopeHint{
    background: rgba(15,23,42,.03);
    border: 1px dashed rgba(15,23,42,.16);
  }
  .qc-scopeHint b{ color: color-mix(in oklab, var(--txt-body) 90%, transparent); }

  .qc-check{
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
  html:not([data-theme="dark"]) .qc-check{
    background: rgba(15,23,42,.03);
    border: 1px solid rgba(15,23,42,.10);
  }
  .qc-check input{
    width: 18px;
    height: 18px;
    accent-color: #f97316;
  }

  .qc-actions{
    display:flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 4px;
  }

  .qc-btn{
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
  .qc-btn:active{ transform: translateY(1px) scale(0.99); }

  .qc-btn.primary{
    background: #0f172a;
    color: #fff;
  }
  html[data-theme="dark"] .qc-btn.primary{
    background: linear-gradient(90deg, rgba(249,115,22,.95), rgba(251,146,60,.95));
    color: #111827;
    box-shadow: 0 16px 40px rgba(249,115,22,.18);
  }
  .qc-btn.primary:hover{ filter: brightness(1.03); }

  .qc-btn.ghost{
    background: rgba(255,255,255,.06);
    color: color-mix(in oklab, var(--txt-body) 90%, transparent);
    border: 1px solid rgba(148,163,184,.22);
  }
  html:not([data-theme="dark"]) .qc-btn.ghost{
    background: rgba(15,23,42,.04);
    border: 1px solid rgba(15,23,42,.12);
    color: #0f172a;
  }
  .qc-btn.ghost:hover{
    border-color: rgba(249,115,22,.35);
    box-shadow: 0 0 0 3px rgba(249,115,22,.12);
  }

  .qc-hidden{ display:none !important; }

  @media (max-width: 720px){
    .qc-head, .qc-body{ padding: 12px 12px; }
  }
</style>

<div class="qc-wrap">
  <div class="qc-card">
    <div class="qc-head">
      <h2 class="qc-title">Buat Quiz Baru</h2>
      <p class="qc-sub">Isi judul, cakupan (global/pulau/suku), dan status aktif.</p>
    </div>

    <div class="qc-body">

      @if($errors->any())
        <div class="qc-alert err">
          <div style="font-weight:1000;margin-bottom:6px;">Gagal:</div>
          <ul style="margin:0; padding-left:18px; font-weight:850;">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('admin.quizzes.store') }}" class="qc-grid" id="quizForm">
        @csrf

        <div class="qc-field">
          <label class="qc-label">Judul</label>
          <input class="qc-input"
                 name="title"
                 value="{{ old('title', 'Kuis Budaya Indonesia') }}"
                 placeholder="Contoh: Kuis Budaya Indonesia" />
        </div>

        <div class="qc-field">
          <label class="qc-label">Cakupan Quiz</label>
          <select class="qc-select" name="scope" id="scopeSelect">
            <option value="global" @selected(old('scope','global')==='global')>Global (semua)</option>
            <option value="island" @selected(old('scope')==='island')>Khusus Pulau</option>
            <option value="tribe"  @selected(old('scope')==='tribe')>Khusus Suku</option>
          </select>

          <div class="qc-scopeHint">
            <b>Global</b> = tampil di semua.<br>
            <b>Pulau</b> = tampil untuk pulau tertentu.<br>
            <b>Suku</b> = tampil untuk suku tertentu di pulau tertentu.
          </div>
        </div>

        <div class="qc-grid two" style="margin-top:2px;">
          <div class="qc-field" id="islandWrap">
            <label class="qc-label">Pulau</label>
            <select name="island_id" id="islandSelect" class="qc-select">
              <option value="">— Pilih Pulau —</option>
              @foreach($islands as $island)
                <option value="{{ $island->id }}"
                        data-slug="{{ $island->slug }}"
                        @selected(old('island_id') == $island->id)>
                  {{ $island->name }}
                </option>
              @endforeach
            </select>
            <div class="qc-help">
              Jika kosong, cek <b>is_active</b> di islands (Seeder harus set true).
            </div>
          </div>

          <div class="qc-field" id="tribeWrap">
            <label class="qc-label">Suku</label>
            <select name="tribe" id="tribeSelect" class="qc-select">
              <option value="">— Pilih Suku —</option>
            </select>
            <div class="qc-help">
              Daftar suku mengikuti pulau yang dipilih (dari <b>config/tribes.php</b>).
            </div>
          </div>
        </div>

        <label class="qc-check">
          <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
          <span>Aktifkan quiz</span>
        </label>

        <div class="qc-actions">
          <button class="qc-btn primary" type="submit">Simpan</button>
          <a class="qc-btn ghost" href="{{ route('admin.quizzes.index') }}">Kembali</a>
        </div>
      </form>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tribesConfig = @json($tribesConfig ?? []);
    const scopeSelect  = document.getElementById('scopeSelect');
    const islandSelect = document.getElementById('islandSelect');
    const tribeSelect  = document.getElementById('tribeSelect');

    const islandWrap = document.getElementById('islandWrap');
    const tribeWrap  = document.getElementById('tribeWrap');

    const oldTribe = @json(old('tribe'));

    function fillTribes(selectedTribe) {
        const opt  = islandSelect.options[islandSelect.selectedIndex];
        const slug = opt ? opt.dataset.slug : null;

        const tribes = (slug && tribesConfig[slug]) ? tribesConfig[slug] : [];

        tribeSelect.innerHTML = '<option value="">— Pilih Suku —</option>';

        tribes.forEach(function (t) {
            const o = document.createElement('option');
            o.value = t;
            o.textContent = t;
            if (selectedTribe && selectedTribe === t) o.selected = true;
            tribeSelect.appendChild(o);
        });

        tribeSelect.disabled = tribes.length === 0;
    }

    function syncScopeUI() {
        const scope = scopeSelect.value;

        // global: island & tribe disembunyikan
        islandWrap.classList.toggle('qc-hidden', scope === 'global');
        tribeWrap.classList.toggle('qc-hidden', scope !== 'tribe');

        // jika scope tribe, isi tribes
        if (scope === 'tribe') {
            fillTribes(oldTribe || '');
        }
    }

    scopeSelect.addEventListener('change', syncScopeUI);
    islandSelect.addEventListener('change', function () {
        // kalau scope tribe, reset tribe tiap ganti pulau
        if (scopeSelect.value === 'tribe') fillTribes('');
    });

    // init
    syncScopeUI();
    if (scopeSelect.value === 'tribe') fillTribes(oldTribe || '');
});
</script>
@endsection
