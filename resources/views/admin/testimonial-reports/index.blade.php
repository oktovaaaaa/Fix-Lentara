{{-- resources/views/admin/testimonial-reports/index.blade.php (UI ONLY - REPLACE FULL) --}}
@extends('layouts.admin')

@section('page-title', 'Laporan Testimoni')

@section('content')
@php
  // UI only: tidak mengubah logic, hanya tampilan + kontras dark/light
@endphp

<style>
  /* =========================================================
     ADMIN - LAPORAN TESTIMONI (MANUAL CSS, DARK/LIGHT SAFE)
     - Tidak ubah route / variable / pagination
     - Fokus: kontras teks, table rapi, mobile friendly
  ========================================================= */

  .tr-wrap{
    max-width: 1200px;
    margin: 0 auto;
    padding: 8px 0 18px;
    color: var(--txt-body);
  }

  .tr-card{
    border-radius: 18px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(2,6,23,.35);
    background: color-mix(in oklab, var(--card) 55%, transparent);
    backdrop-filter: blur(14px) saturate(140%);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    box-shadow: 0 18px 45px rgba(0,0,0,.10);
    overflow: hidden;
  }
  html:not([data-theme="dark"]) .tr-card{
    background: rgba(255,255,255,.70);
    border: 1px solid rgba(15,23,42,.12);
    box-shadow: 0 12px 32px rgba(15,23,42,.08);
  }

  .tr-head{
    padding: 14px 16px;
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap: 12px;
    flex-wrap: wrap;
    border-bottom: 1px solid rgba(148,163,184,.14);
  }
  html:not([data-theme="dark"]) .tr-head{
    border-bottom: 1px solid rgba(15,23,42,.10);
  }

  .tr-title{
    margin: 0;
    font-size: 16px;
    font-weight: 1000;
    letter-spacing: -0.01em;
    color: var(--txt-body);
  }
  .tr-sub{
    margin: 6px 0 0;
    font-size: 12px;
    font-weight: 800;
    line-height: 1.55;
    color: var(--muted);
  }

  .tr-chip{
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

  .tr-table-wrap{
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  table.tr-table{
    width: 100%;
    min-width: 820px;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13px;
    color: var(--txt-body);
  }

  .tr-table thead th{
    text-align: left;
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
  html:not([data-theme="dark"]) .tr-table thead th{
    background: rgba(15,23,42,.03);
    border-bottom: 1px solid rgba(15,23,42,.10);
  }

  .tr-table tbody td{
    padding: 12px 14px;
    border-bottom: 1px solid rgba(148,163,184,.12);
    vertical-align: top;
  }
  html:not([data-theme="dark"]) .tr-table tbody td{
    border-bottom: 1px solid rgba(15,23,42,.08);
  }

  .tr-table tbody tr:hover td{
    background: rgba(249,115,22,.06);
  }
  html:not([data-theme="dark"]) .tr-table tbody tr:hover td{
    background: rgba(249,115,22,.07);
  }

  .tr-date{
    font-weight: 900;
    color: color-mix(in oklab, var(--txt-body) 90%, transparent);
    white-space: nowrap;
  }

  .tr-name{
    font-weight: 1000;
    color: var(--txt-body);
    line-height: 1.25;
    margin-bottom: 6px;
    word-break: break-word;
  }

  .tr-msg{
    font-size: 12px;
    line-height: 1.55;
    color: var(--muted);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-break: break-word;
  }

  .tr-reason{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding: 6px 10px;
    border-radius: 999px;
    font-weight: 1000;
    font-size: 12px;
    border: 1px solid rgba(239,68,68,.30);
    background: rgba(239,68,68,.10);
    color: rgba(239,68,68,.95);
    white-space: nowrap;
  }
  html[data-theme="dark"] .tr-reason{
    color: rgba(254,202,202,.95);
  }

  .tr-note{
    color: color-mix(in oklab, var(--txt-body) 82%, transparent);
    line-height: 1.55;
    word-break: break-word;
  }
  .tr-note.muted{
    color: var(--muted);
  }

  .tr-empty{
    padding: 18px 14px !important;
    text-align:center;
    color: var(--muted);
    font-weight: 900;
  }

  .tr-foot{
    padding: 14px 16px;
  }

  /* pagination from Laravel: keep default markup but make it fit card */
  .tr-foot nav{
    display:flex;
    justify-content:flex-end;
  }

  /* Mobile: allow stacked view by reducing min width a bit */
  @media (max-width: 720px){
    table.tr-table{ min-width: 680px; }
    .tr-head{ padding: 12px 12px; }
    .tr-foot{ padding: 12px 12px; }
  }
</style>

<div class="tr-wrap">
  <div class="tr-card">
    <div class="tr-head">
      <div>
        <h2 class="tr-title">Laporan Masuk</h2>
        <p class="tr-sub">Daftar laporan testimoni dari user. Scroll horizontal kalau layar kecil.</p>
      </div>

      <div class="tr-chip">
        Total: <span style="font-weight:1100;">{{ $reports->total() }}</span>
      </div>
    </div>

    <div class="tr-table-wrap">
      <table class="tr-table">
        <thead>
          <tr>
            <th style="width:170px;">Tanggal</th>
            <th>Testimoni</th>
            <th style="width:180px;">Alasan</th>
            <th style="width:320px;">Catatan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($reports as $r)
            <tr>
              <td class="tr-date">{{ $r->created_at->format('d/m/Y H:i') }}</td>

              <td>
                <div class="tr-name">{{ optional($r->testimonial)->name }}</div>
                <div class="tr-msg">
                  {{ optional($r->testimonial)->message }}
                </div>
              </td>

              <td>
                <span class="tr-reason">{{ $r->reason }}</span>
              </td>

              <td class="tr-note {{ trim((string)$r->note) === '' ? 'muted' : '' }}">
                {{ trim((string)$r->note) === '' ? '—' : $r->note }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="tr-empty">Belum ada laporan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="tr-foot">
      {{ $reports->links() }}
    </div>
  </div>
</div>
@endsection
