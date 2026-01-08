{{-- resources/views/admin/quizzes/index.blade.php (UI ONLY - REPLACE FULL) --}}
@extends('layouts.admin')
@section('title', 'Admin - Quiz')
@section('page-title', 'Quiz')

@section('content')
@php
$islands = $islands ?? collect();

// tribesMap: island_id => [tribes...]
$tribesMap = [];
foreach ($islands as $isl) {
    $tribesMap[(string)$isl->id] = config("tribes.{$isl->slug}") ?? [];
}

$filterScope = request('scope', '');
$filterIsland = request('island_id', '');
$filterTribe = request('tribe', '');
@endphp

<style>
  /* =========================================================
     ADMIN QUIZ INDEX (MANUAL CSS, DARK/LIGHT SAFE)
     - UI rapi + kontras aman untuk layout admin kamu
     - Tidak ubah logic, route, name field, query string
  ========================================================= */

  .aq-wrap{
    max-width: 1200px;
    margin: 0 auto;
    padding: 6px 0 18px;
    color: var(--txt-body);
  }

  .aq-card{
    border-radius: 18px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(2,6,23,.35);
    background: color-mix(in oklab, var(--card) 55%, transparent);
    backdrop-filter: blur(14px) saturate(140%);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    box-shadow: 0 18px 45px rgba(0,0,0,.10);
    overflow: hidden;
  }
  html:not([data-theme="dark"]) .aq-card{
    background: rgba(255,255,255,.70);
    border: 1px solid rgba(15,23,42,.12);
    box-shadow: 0 12px 32px rgba(15,23,42,.08);
  }

  .aq-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap: 14px;
    flex-wrap: wrap;
    padding: 14px 16px;
    border-bottom: 1px solid rgba(148,163,184,.14);
  }
  html:not([data-theme="dark"]) .aq-head{
    border-bottom: 1px solid rgba(15,23,42,.10);
  }

  .aq-title{
    margin:0;
    font-size: 18px;
    font-weight: 1000;
    letter-spacing: -0.01em;
  }
  .aq-sub{
    margin: 6px 0 0;
    font-size: 12px;
    font-weight: 800;
    line-height: 1.55;
    color: var(--muted);
  }

  .aq-btn{
    border: none;
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
  .aq-btn:active{ transform: translateY(1px) scale(0.99); }

  .aq-btn.primary{
    background: #f97316;
    color: #111827;
    box-shadow: 0 16px 40px rgba(249,115,22,.18);
  }
  .aq-btn.primary:hover{ filter: brightness(1.03); }

  .aq-btn.ghost{
    background: rgba(255,255,255,.06);
    color: color-mix(in oklab, var(--txt-body) 92%, transparent);
    border: 1px solid rgba(148,163,184,.22);
  }
  .aq-btn.ghost:hover{
    border-color: rgba(249,115,22,.35);
    box-shadow: 0 0 0 3px rgba(249,115,22,.12);
  }

  .aq-alert{
    border-radius: 16px;
    padding: 10px 12px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.03);
    color: color-mix(in oklab, var(--txt-body) 92%, transparent);
    font-weight: 900;
    margin-bottom: 12px;
  }
  .aq-alert.ok{
    border-color: rgba(34,197,94,.35);
    background: rgba(34,197,94,.10);
    color: rgba(167,243,208,.95);
  }

  /* filter */
  .aq-filter{
    padding: 14px 16px;
    border-bottom: 1px solid rgba(148,163,184,.12);
  }
  html:not([data-theme="dark"]) .aq-filter{
    border-bottom: 1px solid rgba(15,23,42,.08);
  }

  .aq-grid{
    display:grid;
    grid-template-columns: 1fr;
    gap: 12px;
  }
  @media (min-width: 720px){
    .aq-grid{ grid-template-columns: repeat(4, minmax(0, 1fr)); align-items:end; }
  }

  .aq-field{ display:grid; gap: 6px; }
  .aq-label{
    font-size: 12px;
    font-weight: 1000;
    color: color-mix(in oklab, var(--txt-body) 82%, transparent);
  }

  .aq-select{
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
  html:not([data-theme="dark"]) .aq-select{
    background: rgba(255,255,255,.75);
    border: 1px solid rgba(15,23,42,.14);
  }
  .aq-select:focus{
    border-color: rgba(249,115,22,.65);
    box-shadow: 0 0 0 3px rgba(249,115,22,.16);
  }
  .aq-select option{
    background:#0b1220;
    color:rgba(255,255,255,.92);
  }

  .aq-actions{
    display:flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content:flex-end;
  }
  @media (max-width: 719px){
    .aq-actions{ justify-content:flex-start; }
  }

  /* table */
  .aq-table-wrap{
    overflow-x:auto;
    -webkit-overflow-scrolling: touch;
  }

  table.aq-table{
    width:100%;
    min-width: 980px;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13px;
    color: var(--txt-body);
  }

  .aq-table thead th{
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
  html:not([data-theme="dark"]) .aq-table thead th{
    background: rgba(15,23,42,.03);
    border-bottom: 1px solid rgba(15,23,42,.10);
  }

  .aq-table tbody td{
    padding: 12px 14px;
    border-bottom: 1px solid rgba(148,163,184,.12);
    vertical-align: top;
  }
  html:not([data-theme="dark"]) .aq-table tbody td{
    border-bottom: 1px solid rgba(15,23,42,.08);
  }

  .aq-table tbody tr:hover td{
    background: rgba(249,115,22,.06);
  }
  html:not([data-theme="dark"]) .aq-table tbody tr:hover td{
    background: rgba(249,115,22,.07);
  }

  .aq-tt{
    font-weight: 1000;
    line-height: 1.25;
    word-break: break-word;
  }

  .aq-meta{
    margin-top: 6px;
    font-size: 12px;
    font-weight: 800;
    color: var(--muted);
    line-height: 1.45;
  }

  .aq-pill{
    display:inline-flex;
    align-items:center;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 1000;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(255,255,255,.03);
    color: color-mix(in oklab, var(--txt-body) 88%, transparent);
    white-space: nowrap;
  }
  .aq-pill.global{ background: rgba(148,163,184,.10); }
  .aq-pill.island{ border-color: rgba(59,130,246,.28); background: rgba(59,130,246,.12); color: rgba(191,219,254,.95); }
  html:not([data-theme="dark"]) .aq-pill.island{ color: rgb(29,78,216); background: rgba(59,130,246,.10); }
  .aq-pill.tribe{ border-color: rgba(168,85,247,.28); background: rgba(168,85,247,.12); color: rgba(233,213,255,.95); }
  html:not([data-theme="dark"]) .aq-pill.tribe{ color: rgb(107,33,168); background: rgba(168,85,247,.10); }

  .aq-pill.ok{ border-color: rgba(34,197,94,.35); background: rgba(34,197,94,.10); color: rgba(167,243,208,.95); }
  .aq-pill.off{ border-color: rgba(148,163,184,.18); background: rgba(148,163,184,.08); color: color-mix(in oklab, var(--txt-body) 70%, transparent); }

  .aq-row-actions{
    display:flex;
    justify-content:flex-end;
    gap: 10px;
    flex-wrap: wrap;
    white-space: nowrap;
  }

  .aq-btn-mini{
    border-radius: 12px;
    padding: 9px 12px;
    font-weight: 1000;
    border: 1px solid rgba(148,163,184,.22);
    background: rgba(255,255,255,.04);
    color: color-mix(in oklab, var(--txt-body) 92%, transparent);
    text-decoration:none;
    cursor:pointer;
    transition: transform .12s ease, box-shadow .15s ease, filter .12s ease;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    line-height:1;
  }
  .aq-btn-mini:hover{
    border-color: rgba(249,115,22,.35);
    box-shadow: 0 0 0 3px rgba(249,115,22,.12);
  }
  .aq-btn-mini:active{ transform: translateY(1px) scale(0.99); }

  .aq-btn-mini.warn{
    border-color: rgba(245,158,11,.30);
    background: rgba(245,158,11,.12);
    color: rgba(253,230,138,.95);
  }
  html:not([data-theme="dark"]) .aq-btn-mini.warn{ color: rgb(146,64,14); background: rgba(245,158,11,.12); }

  .aq-btn-mini.danger{
    border-color: rgba(239,68,68,.35);
    background: rgba(239,68,68,.12);
    color: rgba(254,202,202,.95);
  }
  html:not([data-theme="dark"]) .aq-btn-mini.danger{ color: rgb(127,29,29); background: rgba(239,68,68,.12); }

  .aq-empty{
    padding: 16px 14px !important;
    font-weight: 900;
    color: var(--muted);
  }

  .aq-foot{ padding: 14px 16px; }

  @media (max-width: 720px){
    .aq-head, .aq-filter, .aq-foot{ padding: 12px 12px; }
    table.aq-table{ min-width: 920px; }
  }
</style>

<div class="aq-wrap">

    @if(session('success'))
        <div class="aq-alert ok">✅ {{ session('success') }}</div>
    @endif

    <div class="aq-card">

        <div class="aq-head">
            <div>
                <h2 class="aq-title">Daftar Quiz</h2>
                <p class="aq-sub">Kelola quiz dan pertanyaannya (global / pulau / suku).</p>
            </div>
            <a href="{{ route('admin.quizzes.create') }}" class="aq-btn primary">
                + Buat Quiz
            </a>
        </div>

        {{-- FILTER --}}
        <div class="aq-filter">
            <form method="GET" class="aq-grid">
                <div class="aq-field">
                    <label class="aq-label">Cakupan</label>
                    <select name="scope" class="aq-select" id="scopeFilter">
                        <option value="" {{ $filterScope===''?'selected':'' }}>Semua</option>
                        <option value="global" {{ $filterScope==='global'?'selected':'' }}>Global</option>
                        <option value="island" {{ $filterScope==='island'?'selected':'' }}>Pulau</option>
                        <option value="tribe" {{ $filterScope==='tribe'?'selected':'' }}>Suku</option>
                    </select>
                </div>

                <div class="aq-field">
                    <label class="aq-label">Pulau</label>
                    <select name="island_id" class="aq-select" id="islandFilter">
                        <option value="">Semua pulau</option>
                        @foreach($islands as $isl)
                            <option value="{{ $isl->id }}" {{ (string)$filterIsland===(string)$isl->id?'selected':'' }}>
                                {{ $isl->subtitle ?: $isl->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="aq-field">
                    <label class="aq-label">Suku</label>
                    <select name="tribe" class="aq-select" id="tribeFilter">
                        <option value="">Semua suku</option>
                    </select>
                </div>

                <div class="aq-actions">
                    <button class="aq-btn primary" type="submit">Terapkan</button>
                    <a href="{{ route('admin.quizzes.index') }}" class="aq-btn ghost">Reset</a>
                </div>
            </form>
        </div>

        <div class="aq-table-wrap">
            <table class="aq-table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th style="width:160px;">Cakupan</th>
                        <th style="width:160px;">Status</th>
                        <th style="width:140px;">Dibuat</th>
                        <th style="width:220px; text-align:right;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($quizzes as $q)
                        <tr>
                            <td>
                                <div class="aq-tt">{{ $q->title }}</div>

                                @if($q->island_id || $q->tribe)
                                    <div class="aq-meta">
                                        @if($q->island)
                                            Pulau: <span style="font-weight:1000;">{{ $q->island->subtitle ?: $q->island->name }}</span>
                                        @endif
                                        @if($q->tribe)
                                            <span style="opacity:.6;">•</span>
                                            Suku: <span style="font-weight:1000;">{{ $q->tribe }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <td>
                                @php $label = $q->scope_label; @endphp
                                <span class="aq-pill
                                    {{ $label==='Global' ? 'global' : '' }}
                                    {{ $label==='Pulau' ? 'island' : '' }}
                                    {{ $label==='Suku' ? 'tribe' : '' }}">
                                    {{ $label }}
                                </span>
                            </td>

                            <td>
                                <span class="aq-pill {{ $q->is_active ? 'ok' : 'off' }}">
                                    {{ $q->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td style="color: var(--muted); font-weight: 900;">
                                {{ optional($q->created_at)->format('d/m/Y') }}
                            </td>

                            <td style="text-align:right;">
                                <div class="aq-row-actions">
                                    <a class="aq-btn-mini warn" href="{{ route('admin.quizzes.edit', $q) }}">Edit</a>

                                    <form class="inline"
                                          method="POST"
                                          action="{{ route('admin.quizzes.destroy', $q) }}"
                                          onsubmit="return confirm('Hapus quiz ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="aq-btn-mini danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="aq-empty">Belum ada quiz.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="aq-foot">
            {{ $quizzes->appends(request()->query())->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
(function(){
    const tribesMap = @json($tribesMap);
    const islandEl = document.getElementById('islandFilter');
    const tribeEl = document.getElementById('tribeFilter');
    const oldTribe = @json($filterTribe);

    function fillTribes(){
        const islandId = String(islandEl.value || '');
        const tribes = tribesMap[islandId] || [];

        tribeEl.innerHTML = '<option value="">Semua suku</option>';

        tribes.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t;
            opt.textContent = t;
            if (oldTribe && oldTribe === t) opt.selected = true;
            tribeEl.appendChild(opt);
        });

        tribeEl.disabled = tribes.length === 0;
    }

    islandEl.addEventListener('change', function(){
        fillTribes();
    });

    fillTribes();
})();
</script>
@endpush

@endsection
