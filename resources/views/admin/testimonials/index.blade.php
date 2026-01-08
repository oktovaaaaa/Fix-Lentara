{{-- resources/views/admin/testimonials/index.blade.php (UI ONLY - REPLACE FULL) --}}
@extends('layouts.admin')

@section('page-title','Testimoni')

@section('content')
@php
    // UI only: tidak mengubah logic filter/pagination.
    // Mengandalkan query string yang sama: rating, reported
    $activeRating = request('rating');
    $onlyReported = request()->has('reported');
@endphp

<style>
  /* =========================================================
     ADMIN - TESTIMONI LIST (MANUAL CSS, DARK/LIGHT SAFE)
     - UI rapi + kontras aman
     - Filter bar sticky feel, table readable, mobile scroll
     - Tidak ubah route / variable / logic
  ========================================================= */

  .at-wrap{
    max-width: 1200px;
    margin: 0 auto;
    padding: 6px 0 18px;
    color: var(--txt-body);
  }

  .at-card{
    border-radius: 18px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(2,6,23,.35);
    background: color-mix(in oklab, var(--card) 55%, transparent);
    backdrop-filter: blur(14px) saturate(140%);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    box-shadow: 0 18px 45px rgba(0,0,0,.10);
    overflow: hidden;
  }
  html:not([data-theme="dark"]) .at-card{
    background: rgba(255,255,255,.70);
    border: 1px solid rgba(15,23,42,.12);
    box-shadow: 0 12px 32px rgba(15,23,42,.08);
  }

  .at-head{
    padding: 14px 16px;
    border-bottom: 1px solid rgba(148,163,184,.14);
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap: 12px;
    flex-wrap: wrap;
  }
  html:not([data-theme="dark"]) .at-head{
    border-bottom: 1px solid rgba(15,23,42,.10);
  }

  .at-title{
    margin:0;
    font-size: 16px;
    font-weight: 1000;
    letter-spacing: -0.01em;
  }
  .at-sub{
    margin: 6px 0 0;
    font-size: 12px;
    font-weight: 800;
    line-height: 1.55;
    color: var(--muted);
  }

  .at-chip{
    display:inline-flex;
    align-items:center;
    gap:10px;
    padding:8px 12px;
    border-radius:999px;
    border:1px solid rgba(249,115,22,.22);
    background: rgba(249,115,22,.08);
    color: var(--txt-body);
    font-weight: 1000;
    font-size: 12px;
    user-select:none;
    white-space:nowrap;
  }

  /* filter */
  .at-filter{
    padding: 14px 16px;
    display:flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: flex-end;
    border-bottom: 1px solid rgba(148,163,184,.12);
  }
  html:not([data-theme="dark"]) .at-filter{
    border-bottom: 1px solid rgba(15,23,42,.08);
  }

  .at-field{ display:grid; gap: 6px; }
  .at-label{
    font-size: 12px;
    font-weight: 900;
    color: color-mix(in oklab, var(--txt-body) 82%, transparent);
  }

  .at-select{
    border-radius: 12px;
    border: 1px solid rgba(148,163,184,.22);
    background: rgba(2,6,23,.25);
    background: color-mix(in oklab, var(--card) 35%, transparent);
    color: var(--txt-body);
    padding: 10px 12px;
    outline: none;
    transition: border-color .15s ease, box-shadow .15s ease;
    min-width: 180px;
    font-weight: 800;
  }
  html:not([data-theme="dark"]) .at-select{
    background: rgba(255,255,255,.75);
    border: 1px solid rgba(15,23,42,.14);
  }
  .at-select:focus{
    border-color: rgba(249,115,22,.65);
    box-shadow: 0 0 0 3px rgba(249,115,22,.16);
  }
  .at-select option{
    background: #0b1220;
    color: rgba(255,255,255,.92);
  }

  .at-check{
    display:flex;
    align-items:center;
    gap:10px;
    padding: 10px 12px;
    border-radius: 12px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.03);
    font-weight: 900;
    color: color-mix(in oklab, var(--txt-body) 88%, transparent);
    user-select:none;
  }
  html:not([data-theme="dark"]) .at-check{
    background: rgba(15,23,42,.03);
    border: 1px solid rgba(15,23,42,.10);
  }
  .at-check input{
    width: 18px;
    height: 18px;
    accent-color: #f97316;
  }

  .at-actions{
    display:flex;
    gap: 10px;
    align-items:center;
    flex-wrap: wrap;
  }

  .at-btn{
    border: none;
    cursor:pointer;
    border-radius: 12px;
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
  }
  .at-btn:active{ transform: translateY(1px) scale(0.99); }

  .at-btn.primary{
    background: #f97316;
    color: #111827;
    box-shadow: 0 16px 40px rgba(249,115,22,.18);
  }
  .at-btn.primary:hover{ filter: brightness(1.03); }

  .at-btn.ghost{
    background: rgba(255,255,255,.06);
    color: color-mix(in oklab, var(--txt-body) 92%, transparent);
    border: 1px solid rgba(148,163,184,.22);
  }
  .at-btn.ghost:hover{
    border-color: rgba(249,115,22,.35);
    box-shadow: 0 0 0 3px rgba(249,115,22,.12);
  }

  /* table */
  .at-table-wrap{
    overflow-x:auto;
    -webkit-overflow-scrolling: touch;
  }

  table.at-table{
    width: 100%;
    min-width: 920px;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13px;
    color: var(--txt-body);
  }

  .at-table thead th{
    text-align:left;
    padding: 12px 14px;
    font-size: 12px;
    font-weight: 1000;
    color: color-mix(in oklab, var(--txt-body) 86%, transparent);
    background: rgba(255,255,255,.02);
    border-bottom: 1px solid rgba(148,163,184,.14);
    position: sticky;
    top: 0;
    z-index: 1;
  }
  html:not([data-theme="dark"]) .at-table thead th{
    background: rgba(15,23,42,.03);
    border-bottom: 1px solid rgba(15,23,42,.10);
  }

  .at-table tbody td{
    padding: 12px 14px;
    border-bottom: 1px solid rgba(148,163,184,.12);
    vertical-align: top;
  }
  html:not([data-theme="dark"]) .at-table tbody td{
    border-bottom: 1px solid rgba(15,23,42,.08);
  }

  .at-table tbody tr:hover td{
    background: rgba(249,115,22,.06);
  }
  html:not([data-theme="dark"]) .at-table tbody tr:hover td{
    background: rgba(249,115,22,.07);
  }

  .at-name{
    font-weight: 1000;
    line-height: 1.25;
    word-break: break-word;
  }

  .at-msg{
    font-size: 12px;
    line-height: 1.55;
    color: var(--muted);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-break: break-word;
  }

  .at-rating{
    display:inline-flex;
    align-items:center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid rgba(249,115,22,.22);
    background: rgba(249,115,22,.08);
    font-weight: 1000;
    font-size: 12px;
    white-space:nowrap;
  }

  .at-report{
    display:inline-flex;
    align-items:center;
    gap: 8px;
    padding: 6px 10px;
    border-radius: 999px;
    font-weight: 1000;
    font-size: 12px;
    white-space:nowrap;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.03);
    color: color-mix(in oklab, var(--txt-body) 86%, transparent);
  }
  .at-report.hot{
    border-color: rgba(239,68,68,.30);
    background: rgba(239,68,68,.10);
    color: rgba(239,68,68,.95);
  }
  html[data-theme="dark"] .at-report.hot{
    color: rgba(254,202,202,.95);
  }

  .at-danger{
    border: 1px solid rgba(239,68,68,.35);
    background: rgba(239,68,68,.12);
    color: rgba(239,68,68,.95);
    border-radius: 12px;
    padding: 9px 12px;
    font-weight: 1000;
    cursor:pointer;
    transition: transform .12s ease, filter .12s ease, box-shadow .15s ease;
  }
  html[data-theme="dark"] .at-danger{
    color: rgba(254,202,202,.95);
  }
  .at-danger:hover{
    filter: brightness(1.03);
    box-shadow: 0 0 0 3px rgba(239,68,68,.12);
  }
  .at-danger:active{ transform: translateY(1px) scale(0.99); }

  .at-empty{
    padding: 18px 14px !important;
    text-align:center;
    color: var(--muted);
    font-weight: 900;
  }

  .at-foot{ padding: 14px 16px; }
  .at-foot nav{ display:flex; justify-content:flex-end; }

  @media (max-width: 720px){
    table.at-table{ min-width: 820px; }
    .at-head, .at-filter, .at-foot{ padding: 12px 12px; }
    .at-select{ min-width: 160px; }
  }
</style>

<div class="at-wrap">

  <div class="at-card">

    <div class="at-head">
      <div>
        <h2 class="at-title">Testimoni</h2>
        <p class="at-sub">Filter berdasarkan rating atau status dilaporkan. Lalu kelola data.</p>
      </div>
      <div class="at-chip">
        Total: <span style="font-weight:1100;">{{ $testimonials->total() }}</span>
      </div>
    </div>

    <form class="at-filter" method="GET" action="{{ url()->current() }}">
      <div class="at-field">
        <label class="at-label">Rating</label>
        <select name="rating" class="at-select">
          <option value="">Semua Rating</option>
          @for($i=5;$i>=1;$i--)
            <option value="{{ $i }}" @selected((string)$activeRating === (string)$i)>{{ $i }} ⭐</option>
          @endfor
        </select>
      </div>

      <div class="at-field">
        <label class="at-label">Laporan</label>
        <label class="at-check">
          <input type="checkbox" name="reported" value="1" @checked($onlyReported)>
          Dilaporkan
        </label>
      </div>

      <div class="at-actions">
        <button class="at-btn primary" type="submit">Filter</button>

        @if($activeRating || $onlyReported)
          <a class="at-btn ghost" href="{{ url()->current() }}">Reset</a>
        @endif
      </div>
    </form>

    <div class="at-table-wrap">
      <table class="at-table">
        <thead>
          <tr>
            <th style="width:220px;">Nama</th>
            <th style="width:140px;">Rating</th>
            <th>Pesan</th>
            <th style="width:140px;">Report</th>
            <th style="width:140px; text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($testimonials as $t)
            <tr>
              <td>
                <div class="at-name">{{ $t->name }}</div>
              </td>

              <td>
                <span class="at-rating">{{ $t->rating }} ⭐</span>
              </td>

              <td>
                <div class="at-msg">{{ \Illuminate\Support\Str::limit($t->message, 60) }}</div>
              </td>

              <td>
                @php $rc = (int)($t->reports_count ?? 0); @endphp
                <span class="at-report {{ $rc > 0 ? 'hot' : '' }}">
                  {{ $rc }}
                </span>
              </td>

              <td style="text-align:right;">
                <form method="POST"
                      action="{{ route('admin.testimonials.destroy',$t) }}"
                      onsubmit="return confirm('Yakin ingin menghapus testimoni ini?')"
                      style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button class="at-danger" type="submit">Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="at-empty">Belum ada testimoni.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="at-foot">
      {{ $testimonials->links() }}
    </div>

  </div>
</div>
@endsection
