{{-- resources/views/player/learn/index.blade.php (REPLACE FULL) --}}

@extends('layouts.game')

@section('title', 'Belajar')

@php
use Illuminate\Support\Facades\Route;

$lockedColor = '#6b7280'; // abu-abu locked

$islandColors = $islandColors ?? [
    'sumatera' => '#f97316',
    'jawa' => '#3b82f6',
    'kalimantan' => '#22c55e',
    'sulawesi' => '#facc15',
    'sunda-kecil' => '#86efac',
    'sunda_kecil' => '#86efac',
    'sundakecil' => '#86efac',
    'papua-maluku' => '#a16207',
    'papua_maluku' => '#a16207',
    'papua&maluku' => '#a16207',
    'papua' => '#a16207',
    'maluku' => '#a16207',
];

$player = $player ?? (object)[
    'display_name' => 'Player',
    'nickname' => null,
    'avatar_key' => 1,
    'xp_total' => 0,
    'coins' => 0,
    'hearts' => 0,
    'hearts_max' => 5,
];

$tierLabel = $tierLabel ?? 'Pemula';

$islands = $islands ?? collect();
$levels = $levels ?? [];
$unlockedIslandIds = $unlockedIslandIds ?? [];
$islandProgress = $islandProgress ?? [];
$levelProgress = $levelProgress ?? [];

$getIslandColor = function($slug) use ($islandColors) {
    $slug = (string) $slug;
    return $islandColors[$slug] ?? '#f97316';
};

$safeRoute = function($name, $params = []) {
    if (\Illuminate\Support\Facades\Route::has($name)) return route($name, $params);
    return '#';
};

$nickname = (string) ($player->nickname ?? $player->display_name ?? 'Player');
$avatarKey = (int) ($player->avatar_key ?? 1);
if ($avatarKey < 1 || $avatarKey > 5) $avatarKey = 1;
$avatarUrl = asset('images/avatars/avatar-'.$avatarKey.'.png');

// MENU (tetap manual di sini)
$menu = [
    ['label'=>'Belajar','route'=>'game.learn','active'=>true,'icon'=>'book'],
    ['label'=>'Panduan','route'=>'game.guide','active'=>false,'icon'=>'help'],
    ['label'=>'Papan Peringkat','route'=>'game.leaderboard','active'=>false,'icon'=>'trophy'],
    ['label'=>'Profil','route'=>'player.profile','active'=>false,'icon'=>'gear'],
];

// Helper: ambil subtitle/name
$islandTitle = function($isl){
    return $isl->subtitle ?? $isl->name ?? 'Pulau';
};

// Build node per island: unlocked by island unlock + previous level completed rule (controller)
$buildNodes = function($isl) use ($levels, $levelProgress, $unlockedIslandIds) {
    $islLevels = $levels[$isl->id] ?? collect();
    $islUnlocked = in_array($isl->id, $unlockedIslandIds, true);

    $prevDone = true;
    $nodes = [];
    foreach ($islLevels as $lv) {
        $prog = $levelProgress[$lv->id] ?? null;
        $done = (bool)($prog->is_completed ?? false);

        $unlocked = $islUnlocked && $prevDone;
        $prevDone = $done;

        $nodes[] = [
            'lv' => $lv,
            'done' => $done,
            'unlocked' => $unlocked,
        ];
    }
    return [$nodes, $islUnlocked];
};

// Find previous island title for locked island popup
$prevIslandTitleById = [];
$prev = null;
foreach ($islands as $isl) {
    $prevIslandTitleById[$isl->id] = $prev ? ($prev->subtitle ?? $prev->name ?? 'pulau sebelumnya') : null;
    $prev = $isl;
}

// routes for modals/actions
$refillUrl = Route::has('game.hearts.refill') ? route('game.hearts.refill') : null;
@endphp


@push('styles')
    <style>
        /* ====== THEME GLOBAL (LIGHT & DARK) ====== */
        :root{
          --bg-body: #fdfaf5;
          --txt-body: #0f172a;
          --card: #ffffff;
          --line: #e9e1d6;
          --muted: #616161;
          --brand: #b7410e;
          --brand-2: #f4c842;
          --shadow: 0 20px 50px rgba(0,0,0,.12);

          --danger:#ef4444;
          --ok:#22c55e;

          --nav-w: 280px;
          --nav-w-collapsed: 72px;
          --right-w: 320px;

          --r-xl: 22px;
          --r-lg: 18px;
          --r-md: 14px;
        }

        html[data-theme="dark"]{
          --bg-body: #020617;
          --txt-body: #e5e7eb;
          --card: #020617;
          --line: #1f2937;
          --muted: #9ca3af;
          --brand: #f97316;
          --brand-2: #fde68a;
          --shadow: 0 25px 60px rgba(0,0,0,.65);
        }

        *{ box-sizing:border-box; }
        html,body{ height:100%; }
        body{
            margin:0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
            background: var(--bg-body);
            color: var(--txt-body);
            overflow-x:hidden;
        }
    </style>

    {{-- ✅ SIDEBAR CSS (INCLUDE) --}}
    @include('player.partials.learn-sidebar', ['mode' => 'css'])
@endpush


@section('content')

    {{-- ✅ MOBILE BAR + MOBILE DRAWER (INCLUDE) --}}
    @include('player.partials.learn-sidebar', ['mode' => 'mobile'])

    <div class="shell">
        {{-- ✅ DESKTOP SIDEBAR (INCLUDE) --}}
        @include('player.partials.learn-sidebar', ['mode' => 'desktop'])

        {{-- Main content --}}
        <main class="main" aria-label="Map Belajar">
            <style>
                /* ===== Main ===== */
                .main{ min-width:0; }

                /* ===== Right panel desktop ===== */
                .right{
                    position: sticky;
                    top: 14px;
                    height: calc(100vh - 28px);
                    display:grid;
                    gap: 12px;
                    align-content:start;
                }

                .panel-head{
                    padding: 14px;
                    border-bottom: 1px solid var(--line);
                    display:flex;
                    align-items:center;
                    justify-content:space-between;
                }
                .panel-title{ margin:0; font-size: 16px; font-weight: 950; }

                .metrics{
                    padding: 14px;
                    display:flex;
                    gap: 10px;
                    flex-wrap:wrap;
                }

                .pill{
                    display:inline-flex;
                    align-items:center;
                    gap: 8px;
                    padding: 10px 12px;
                    border-radius: 16px;
                    border: 1px solid var(--line);
                    background: color-mix(in oklab, var(--card) 92%, transparent);
                    font-weight: 950;
                }
                .pill svg{ width: 18px; height: 18px; }
                .pill.heart{ color: var(--danger); }
                .pill.money{ color: #22c55e; }
                .pill.xp{ color: #3b82f6; }

                .right-bottom{
                    padding: 14px;
                    border-top: 1px solid var(--line);
                    display:grid;
                    gap: 10px;
                }
                .hello{
                    font-weight: 900;
                    color: var(--muted);
                    line-height: 1.3;
                }
                .hello b{ color: var(--txt-body); }

                .btn-logout{
                    width:100%;
                    border-radius: 16px;
                    padding: 12px;
                    border: 1px solid var(--line);
                    background: color-mix(in oklab, var(--card) 92%, transparent);
                    cursor:pointer;
                    font-weight: 950;
                    color: var(--txt-body);
                }

                .btn{
                    border-radius: 16px;
                    padding: 12px 14px;
                    border: 1px solid var(--line);
                    background: color-mix(in oklab, var(--card) 92%, transparent);
                    color: var(--txt-body);
                    cursor:pointer;
                    font-weight: 950;
                    text-decoration:none;
                    display:inline-flex;
                    align-items:center;
                    justify-content:center;
                }
                .btn.primary{ border-color: var(--brand); }

                /* ===== Map / Duolingo path ===== */
                .map{
                    border-radius: var(--r-xl);
                    border: 1px solid var(--line);
                    background: color-mix(in oklab, var(--card) 92%, transparent);
                    box-shadow: var(--shadow);
                    padding: 14px;
                }

                .map-head{
                    display:flex;
                    align-items:center;
                    justify-content:space-between;
                    gap: 12px;
                    padding: 12px;
                    border: 1px solid var(--line);
                    border-radius: var(--r-xl);
                    background: color-mix(in oklab, var(--card) 92%, transparent);
                    margin-bottom: 14px;
                }

                .map-head-left{
                    display:flex;
                    align-items:center;
                    gap: 12px;
                    min-width:0;
                }
                .map-title{
                    font-weight: 950;
                    font-size: 16px;
                }
                .map-help{
                    display:inline-flex;
                    align-items:center;
                    gap: 8px;
                    padding: 10px 12px;
                    border-radius: 16px;
                    border: 1px solid var(--line);
                    background: color-mix(in oklab, var(--card) 92%, transparent);
                    font-weight: 950;
                    text-decoration:none;
                    color: var(--txt-body);
                }
                .map-help svg{ width:18px;height:18px; }

                .island-section{
                    margin-top: 16px;
                    padding-top: 16px;
                    border-top: 1px dashed var(--line);
                }
                .island-section:first-of-type{
                    margin-top: 0;
                    padding-top: 0;
                    border-top: 0;
                }

                .island-row{
                    display:flex;
                    align-items:center;
                    justify-content:space-between;
                    gap: 12px;
                    margin-bottom: 10px;
                }

                .island-name{
                    display:flex;
                    align-items:center;
                    gap: 10px;
                    font-weight: 950;
                    font-size: 16px;
                }
                .dot{
                    width: 10px; height: 10px;
                    border-radius: 999px;
                    background: var(--brand);
                    border: 1px solid var(--line);
                }

                .island-status{
                    font-weight: 950;
                    font-size: 12px;
                    color: var(--muted);
                }

                .path{
                    width: min(560px, 100%);
                    margin: 0 auto;
                    position: relative;
                    padding: 6px 0 10px;
                    display:grid;
                    gap: 18px;
                    justify-items:center;
                }

                .path-svg{
                    position:absolute;
                    inset: 0;
                    width:100%;
                    height:100%;
                    pointer-events:none;
                }

                .node-wrap{
                    width: 100%;
                    display:grid;
                    justify-items:center;
                    position: relative;
                    z-index: 2;
                }

                /* Zigzag offsets (repeat pattern) */
                .node-wrap[data-pos="0"]{ transform: translateX(0); }
                .node-wrap[data-pos="1"]{ transform: translateX(-96px); }
                .node-wrap[data-pos="2"]{ transform: translateX(92px); }
                .node-wrap[data-pos="3"]{ transform: translateX(-72px); }
                .node-wrap[data-pos="4"]{ transform: translateX(72px); }

                .node{
                    width: 78px; height: 78px;
                    border-radius: 18px;
                    border: 2px solid var(--line);
                    background: color-mix(in oklab, var(--card) 92%, transparent);
                    display:grid;
                    place-items:center;
                    text-decoration:none;
                    color: var(--txt-body);
                    cursor:pointer;
                }

                .node.is-locked{ cursor:not-allowed; opacity:.7; }

                .node-core{
                    width: 60px; height: 60px;
                    border-radius: 16px;
                    border: 2px solid var(--line);
                    background: color-mix(in oklab, var(--card) 92%, transparent);
                    display:grid;
                    place-items:center;
                }
                .node svg{ width: 34px; height: 34px; }

                .node-label{
                    margin-top: 6px;
                    font-size: 13px;
                    font-weight: 950;
                    text-align:center;
                }
                .node-sub{
                    margin-top: 2px;
                    font-size: 12px;
                    font-weight: 900;
                    color: var(--muted);
                    text-align:center;
                }

                .stroke{
                    stroke: var(--line);
                    stroke-width: 8;
                    fill: none;
                    stroke-linecap: round;
                    stroke-linejoin: round;
                    stroke-dasharray: 12 10;
                }

                /* ===== Modals ===== */
                .modal-wrap{
                    position: fixed;
                    inset: 0;
                    z-index: 220;
                    display:none;
                    align-items:center;
                    justify-content:center;
                    padding: 16px;
                    background: rgba(0,0,0,.55);
                }
                .modal-wrap.is-open{ display:flex; }

                .modal{
                    width: min(520px, 92vw);
                    border-radius: 22px;
                    border: 1px solid var(--line);
                    background: var(--card);
                    box-shadow: var(--shadow);
                    overflow:hidden;
                }

                .modal-inner{ padding: 16px; }
                .modal-title{ margin:0 0 6px; font-size: 18px; font-weight: 950; }
                .modal-sub{ margin:0 0 14px; font-weight: 900; color: var(--muted); line-height: 1.35; }

                .modal-actions{
                    display:flex;
                    gap: 10px;
                    justify-content:flex-end;
                    flex-wrap:wrap;
                }

                @media (max-width: 520px){
                    .node{ width: 72px; height: 72px; }
                    .node-core{ width: 56px; height: 56px; }
                    .node-wrap[data-pos="1"],
                    .node-wrap[data-pos="2"],
                    .node-wrap[data-pos="3"],
                    .node-wrap[data-pos="4"]{ transform: translateX(0); }
                }
            </style>

            <section class="map">
                <div class="map-head">
                    <div class="map-head-left">
                        <div class="map-title">Belajar</div>
                    </div>

                    <a class="map-help" href="{{ $safeRoute('game.guide') }}">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M12 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M9.1 9a3 3 0 1 1 5.8 1c0 2-3 2-3 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <span>Panduan</span>
                    </a>
                </div>

                @if($islands->isEmpty())
                    <div style="padding:14px;font-weight:900;color:var(--muted);text-align:center;">
                        Belum ada pulau aktif.
                    </div>
                @else

                    @foreach($islands as $islIndex => $isl)
                        @php
                            [$nodes, $islUnlocked] = $buildNodes($isl);
                            $title = $islandTitle($isl);
                            $color = $getIslandColor($isl->slug);

                            $isCompleted = (bool)($islandProgress[$isl->id]->is_completed ?? false);
                            $statusText = $isCompleted ? 'Selesai' : ($islUnlocked ? 'Terbuka' : 'Terkunci');

                            $prevIslandTitle = $prevIslandTitleById[$isl->id] ?? null;

                            // Default: kalau island locked → semua level tampil locked juga
                            $effectiveLocked = !$islUnlocked;

                            $nodeCount = count($nodes);
                        @endphp

                        <div class="island-section" data-island-section="1">
                            <div class="island-row">
                                <div class="island-name">
                                    <span class="dot" style="background: {{ $islUnlocked ? $color : $lockedColor }};"></span>
                                    <span>{{ $title }}</span>
                                </div>
                                <div class="island-status">{{ $statusText }}</div>
                            </div>

                            @if($nodeCount === 0)
                                <div style="padding:10px 0; text-align:center; color: var(--muted); font-weight:900;">
                                    Belum ada level untuk pulau ini.
                                </div>
                            @else
                                <div class="path" data-path="1" data-island-slug="{{ e($isl->slug) }}">
                                    <svg class="path-svg" data-path-svg="1" aria-hidden="true"></svg>

                                    @foreach($nodes as $i => $node)
                                        @php
                                            $lv = $node['lv'];
                                            $done = (bool)$node['done'];
                                            $lvUnlocked = (bool)$node['unlocked'];

                                            $lvTitle = $lv->title ?? ('Level '.($i+1));

                                            if($effectiveLocked){
                                                $lvUnlocked = false;
                                            }

                                            $nodeColor = $lvUnlocked ? $color : $lockedColor;
                                            $sub = $done ? 'Selesai' : ($lvUnlocked ? 'Mulai' : 'Terkunci');

                                            $lockReason = '';
                                            if(!$islUnlocked){
                                                $need = $prevIslandTitle ?: 'pulau sebelumnya';
                                                $lockReason = 'Selesaikan pulau '.$need.' dulu.';
                                            }else{
                                                $lockReason = 'Selesaikan level sebelumnya dulu.';
                                            }

                                            $pos = $i % 5;
                                        @endphp

                                        <div class="node-wrap" data-pos="{{ $pos }}" data-node-wrap="1">
                                            @if($lvUnlocked)
                                                <a
                                                    class="node"
                                                    href="{{ route('game.play', $lv->id) }}"
                                                    style="border-color: {{ $nodeColor }};"
                                                    data-node="1"
                                                    data-open="1"
                                                    data-level-link="1"
                                                    title="{{ $lvTitle }}"
                                                >
                                                    <span class="node-core" style="border-color: {{ $nodeColor }};">
                                                        @if($done)
                                                            <svg viewBox="0 0 24 24" fill="none">
                                                                <path d="M12 17.3l-5.1 2.7 1-5.7L3.8 9.6l5.8-.8L12 3.6l2.4 5.2 5.8.8-4.1 4.7 1 5.7-5.1-2.7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                            </svg>
                                                        @else
                                                            <svg viewBox="0 0 24 24" fill="none">
                                                                <path d="M9 7l10 5-10 5V7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                                <path d="M4 4v16" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".25"/>
                                                            </svg>
                                                        @endif
                                                    </span>
                                                </a>
                                            @else
                                                <button
                                                    type="button"
                                                    class="node is-locked"
                                                    style="border-color: {{ $lockedColor }};"
                                                    data-node="1"
                                                    data-locked="1"
                                                    data-lock-reason="{{ e($lockReason) }}"
                                                    aria-label="{{ $lvTitle }} terkunci"
                                                >
                                                    <span class="node-core" style="border-color: {{ $lockedColor }};">
                                                        <svg viewBox="0 0 24 24" fill="none">
                                                            <path d="M7 11V8a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                            <path d="M6 11h12v9H6v-9Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                            <path d="M12 15v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                        </svg>
                                                    </span>
                                                </button>
                                            @endif

                                            <div class="node-label">{{ $lvTitle }}</div>
                                            <div class="node-sub">{{ $sub }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach

                @endif
            </section>
        </main>

        {{-- Right panel desktop --}}
        <aside class="right" aria-label="Panel Status Desktop">
            <section class="card">
                <div class="panel-head">
                    <h3 class="panel-title">Status</h3>
                </div>

                <div class="metrics">
                    <div class="pill xp" title="XP Total">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M13 2s2 3 2 6-2 4-2 4 4-1 4-6 2-4 2-4-1 7-4 10-1 8-1 8-6-3-6-8 4-10 5-10Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                        <span>{{ (int)($player->xp_total ?? 0) }}</span>
                    </div>

                    <div class="pill heart" title="Hati">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M12 21s-7-4.6-9.2-9.1C1.4 8.9 3.4 6 6.6 6c1.8 0 3.1 1 3.9 2 0.8-1 2.1-2 3.9-2 3.2 0 5.2 2.9 3.8 5.9C19 16.4 12 21 12 21Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                        <span id="heartsNowDesktop">{{ (int)($player->hearts ?? 0) }}</span>/<span>{{ (int)($player->hearts_max ?? 5) }}</span>
                    </div>

                    <div class="pill money" title="Uang">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M3 7h18v10H3V7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M7 7V5h10v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M12 10.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <span>{{ number_format((int)($player->coins ?? 0), 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="right-bottom">
                    <div class="hello">
                        Halo, <b>{{ $nickname }}</b><br>
                        Tier: <b>{{ $tierLabel }}</b>
                    </div>

                    <a class="btn" href="{{ route('player.profile') }}">Profil</a>

                    <form method="POST" action="{{ route('player.logout') }}">
                        @csrf
                        <button class="btn-logout" type="submit">Keluar</button>
                    </form>
                </div>
            </section>
        </aside>
    </div>

    {{-- Modal locked reason --}}
    <div class="modal-wrap" id="lockModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true">
            <div class="modal-inner">
                <div class="modal-title">Terkunci</div>
                <div class="modal-sub" id="lockText">Selesaikan bagian sebelumnya dulu.</div>
                <div class="modal-actions">
                    <button class="btn primary" type="button" id="btnLockOk">OK</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal hearts empty --}}
    <div class="modal-wrap" id="heartsModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true">
            <div class="modal-inner">
                <div class="modal-title">Hati kamu habis</div>
                <div class="modal-sub" id="heartsText">
                    Kamu tidak bisa mulai level karena hati kamu 0.
                    Isi ulang hati atau tutup popup ini.
                </div>
                <div class="modal-actions" style="justify-content:space-between;">
                    <button class="btn" type="button" id="btnHeartsClose">Tutup</button>
                    <button class="btn primary" type="button" id="btnHeartsRefill">Isi Ulang</button>
                </div>
                <div class="modal-sub" id="heartsErr" style="display:none;"></div>
            </div>
        </div>
    </div>

@endsection


@push('scripts')
    {{-- ✅ SIDEBAR JS (INCLUDE) --}}
    @include('player.partials.learn-sidebar', ['mode' => 'scripts'])

    <script>
    (function(){
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const REFILL_URL = @json($refillUrl);

        // =========================================
        // Locked modal (island/level)
        // =========================================
        const lockModal = document.getElementById('lockModal');
        const lockText  = document.getElementById('lockText');
        const btnLockOk = document.getElementById('btnLockOk');

        function openLock(msg){
            if(lockText) lockText.textContent = msg || 'Terkunci.';
            if(lockModal){
                lockModal.classList.add('is-open');
                lockModal.setAttribute('aria-hidden','false');
            }
        }
        function closeLock(){
            if(lockModal){
                lockModal.classList.remove('is-open');
                lockModal.setAttribute('aria-hidden','true');
            }
        }
        if(btnLockOk) btnLockOk.addEventListener('click', closeLock);

        document.querySelectorAll('[data-locked="1"]').forEach(el => {
            el.addEventListener('click', () => {
                const msg = el.getAttribute('data-lock-reason') || 'Selesaikan bagian sebelumnya dulu.';
                openLock(msg);
            });
        });

        // =========================================
        // Hearts empty modal (index)
        // =========================================
        let heartsNow = {{ (int)($player->hearts ?? 0) }};
        const heartsModal = document.getElementById('heartsModal');
        const heartsErr   = document.getElementById('heartsErr');
        const btnHeartsClose = document.getElementById('btnHeartsClose');
        const btnHeartsRefill= document.getElementById('btnHeartsRefill');

        function openHearts(){
            if(heartsErr){ heartsErr.style.display='none'; heartsErr.textContent=''; }
            if(heartsModal){
                heartsModal.classList.add('is-open');
                heartsModal.setAttribute('aria-hidden','false');
            }
        }
        function closeHearts(){
            if(heartsModal){
                heartsModal.classList.remove('is-open');
                heartsModal.setAttribute('aria-hidden','true');
            }
        }
        if(btnHeartsClose) btnHeartsClose.addEventListener('click', closeHearts);

        async function safeJson(res){
            const ct = (res.headers.get('content-type') || '').toLowerCase();
            if(ct.includes('application/json')){
                try { return await res.json(); } catch(e){ return null; }
            }
            try { await res.text(); } catch(e){}
            return null;
        }

        async function doRefill(){
            if(!REFILL_URL){
                if(heartsErr){
                    heartsErr.style.display='block';
                    heartsErr.textContent='Route isi ulang hati belum ada (game.hearts.refill).';
                }
                return;
            }

            btnHeartsRefill.disabled = true;
            btnHeartsRefill.style.opacity = '.7';

            try{
                const res = await fetch(REFILL_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                });

                const json = await safeJson(res);

                if(!res.ok || !json || !json.ok){
                    if(heartsErr){
                        heartsErr.style.display='block';
                        heartsErr.textContent = (json && json.message) ? json.message : 'Gagal isi ulang (uang tidak cukup / error).';
                    }
                    btnHeartsRefill.disabled = false;
                    btnHeartsRefill.style.opacity = '1';
                    return;
                }

                // kalau sukses, refresh halaman biar hearts & coins update
                window.location.reload();
            }catch(e){
                if(heartsErr){
                    heartsErr.style.display='block';
                    heartsErr.textContent = 'Terjadi kesalahan jaringan.';
                }
                btnHeartsRefill.disabled = false;
                btnHeartsRefill.style.opacity = '1';
            }
        }
        if(btnHeartsRefill) btnHeartsRefill.addEventListener('click', doRefill);

        // intercept klik level terbuka saat hearts 0
        document.querySelectorAll('[data-level-link="1"]').forEach(a => {
            a.addEventListener('click', (e) => {
                if(heartsNow <= 0){
                    e.preventDefault();
                    openHearts();
                }
            });
        });

        // sync UI hearts text if needed
        const hD = document.getElementById('heartsNowDesktop');
        const hM = document.getElementById('heartsNowMobile');
        if(hD) hD.textContent = String(heartsNow);
        if(hM) hM.textContent = String(heartsNow);

        // =========================================
        // Build SVG dashed connections for EACH island path
        // =========================================
        function buildPathFor(pathEl){
            const svg = pathEl.querySelector('[data-path-svg="1"]');
            if(!svg) return;

            while(svg.firstChild) svg.removeChild(svg.firstChild);

            const nodes = Array.from(pathEl.querySelectorAll('[data-node="1"]'));
            if(nodes.length <= 1) return;

            const r = pathEl.getBoundingClientRect();
            svg.setAttribute('viewBox', `0 0 ${r.width} ${r.height}`);
            svg.setAttribute('preserveAspectRatio', 'none');

            function center(el){
                const b = el.getBoundingClientRect();
                return {
                    x: (b.left - r.left) + b.width/2,
                    y: (b.top - r.top) + b.height/2
                };
            }

            for(let i=0;i<nodes.length-1;i++){
                const a = center(nodes[i]);
                const b = center(nodes[i+1]);

                const midY = (a.y + b.y) / 2;
                const d = `M ${a.x} ${a.y} C ${a.x} ${midY}, ${b.x} ${midY}, ${b.x} ${b.y}`;

                const p = document.createElementNS('http://www.w3.org/2000/svg','path');
                p.setAttribute('d', d);
                p.setAttribute('class','stroke');
                svg.appendChild(p);
            }
        }

        function rebuildAll(){
            document.querySelectorAll('[data-path="1"]').forEach(pathEl => buildPathFor(pathEl));
        }

        const ro = new ResizeObserver(() => rebuildAll());
        document.querySelectorAll('[data-path="1"]').forEach(el => ro.observe(el));

        window.addEventListener('load', rebuildAll);
        window.addEventListener('resize', rebuildAll);
    })();
    </script>
@endpush
