@extends('layouts.admin')

@section('page-title', 'Game Levels (Per Pulau)')
@section('content')

<style>
  /* =========================
     ADMIN GAME LEVELS - FIX KONTRAS TEKS
     Masalah: background gelap tapi warna teks default (gelap) -> tidak kelihatan
  ========================= */

  .gl-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
  }

  .gl-card{
    background:#0b1220;
    border:1px solid rgba(255,255,255,.08);
    border-radius:14px;
    padding:14px;
    color:rgba(255,255,255,.92); /* ✅ FIX: semua teks di card jadi terang */
  }

  .gl-title{
    font-weight:900;
    margin:0 0 10px 0;
    letter-spacing:.2px;
    color:#fff;
  }

  .gl-alert{
    margin:10px 0;
    padding:10px 12px;
    border-radius:12px;
    border:1px solid rgba(255,255,255,.10);
    background:rgba(11,18,32,.65);
  }
  .gl-alert--success{
    color:#86efac;
    border-color:rgba(34,197,94,.35);
    background:rgba(34,197,94,.10);
  }
  .gl-alert--error{
    color:#fca5a5;
    border-color:rgba(239,68,68,.35);
    background:rgba(239,68,68,.10);
  }

  .gl-field{
    margin-bottom:10px;
  }

  .gl-label{
    display:block;
    font-size:13px;
    font-weight:700;
    margin:0 0 6px 2px;
    color:rgba(255,255,255,.90); /* ✅ FIX label */
  }

  .gl-input,
  .gl-select{
    width:100%;
    padding:10px 12px;
    border-radius:10px;
    outline:none;
    border:1px solid rgba(255,255,255,.12);
    background:rgba(255,255,255,.08); /* ✅ lebih cocok di dark bg */
    color:rgba(255,255,255,.92);      /* ✅ FIX teks input */
  }

  .gl-input::placeholder{
    color:rgba(255,255,255,.45);
  }

  .gl-input:focus,
  .gl-select:focus{
    border-color:rgba(249,115,22,.55);
    box-shadow:0 0 0 3px rgba(249,115,22,.18);
  }

  .gl-check{
    display:flex;
    gap:8px;
    align-items:center;
    margin-bottom:10px;
    color:rgba(255,255,255,.88); /* ✅ FIX teks checkbox */
    font-weight:700;
  }

  .gl-btn-primary{
    padding:10px 14px;
    border-radius:12px;
    background:#f97316;
    color:#111;
    font-weight:900;
    border:0;
    cursor:pointer;
  }
  .gl-btn-primary:hover{
    filter:brightness(1.05);
  }

  .gl-list{
    display:grid;
    gap:10px;
  }

  .gl-item{
    padding:12px;
    border:1px solid rgba(255,255,255,.10);
    border-radius:12px;
    background:rgba(255,255,255,.03);
    color:rgba(255,255,255,.92); /* ✅ FIX teks item */
  }

  .gl-item b{
    color:#fff;
  }

  .gl-muted{
    opacity:.86;
    color:rgba(255,255,255,.82);
    margin-top:4px;
  }

  .gl-actions{
    margin-top:10px;
    display:flex;
    gap:8px;
    flex-wrap:wrap;
  }

  .gl-link,
  .gl-btn-danger{
    padding:8px 10px;
    border:1px solid rgba(255,255,255,.12);
    border-radius:10px;
    background:rgba(255,255,255,.04);
    color:rgba(255,255,255,.90); /* ✅ FIX warna teks tombol/anchor */
    text-decoration:none;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    line-height:1;
  }

  .gl-link:hover,
  .gl-btn-danger:hover{
    border-color:rgba(249,115,22,.40);
    box-shadow:0 0 0 3px rgba(249,115,22,.12);
  }

  .gl-btn-danger{
    background:rgba(255,255,255,.04);
  }

  /* ✅ Biar option dropdown kebaca di browser tertentu */
  .gl-select option{
    background:#0b1220;
    color:rgba(255,255,255,.92);
  }

  /* Responsive */
  @media (max-width: 980px){
    .gl-grid{ grid-template-columns:1fr; }
  }
</style>

@if(session('success'))
  <div class="gl-alert gl-alert--success">{{ session('success') }}</div>
@endif

@if(session('error'))
  <div class="gl-alert gl-alert--error">{{ session('error') }}</div>
@endif

<div class="gl-grid">

  <div class="gl-card">
    <h3 class="gl-title">Tambah Level</h3>

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

      <button class="gl-btn-primary" type="submit">Simpan</button>
    </form>
  </div>

  <div class="gl-card">
    <h3 class="gl-title">Daftar Level</h3>

    <div class="gl-list">
      @foreach($levels as $lv)
        <div class="gl-item">
          <div>
            <b>{{ $lv->island->name }}</b> — {{ $lv->title }} (Order: {{ $lv->order }})
          </div>

          <div class="gl-muted">
            Aktif: {{ $lv->is_active ? 'Ya' : 'Tidak' }}
          </div>

          <div class="gl-actions">
            <a
              href="{{ route('admin.game-questions.index', $lv->id) }}"
              class="gl-link"
            >Kelola Soal</a>

            <a
              href="{{ route('admin.game-levels.edit', $lv->id) }}"
              class="gl-link"
            >Edit</a>

            <form method="POST" action="{{ route('admin.game-levels.destroy', $lv->id) }}">
              @csrf
              @method('DELETE')
              <button class="gl-btn-danger" type="submit">Hapus</button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  </div>

</div>
@endsection
