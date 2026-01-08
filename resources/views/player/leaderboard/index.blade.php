{{-- resources/views/player/leaderboard/index.blade.php (REPLACE FULL) --}}
@php
use Illuminate\Support\Facades\Route;

$player = $player ?? (object)[
  'id' => 0,
  'display_name' => 'Player',
  'nickname' => null,
  'avatar_key' => 1,
  'xp_total' => 0,
  'coins' => 0,
  'hearts' => 0,
  'hearts_max' => 5,
];

$rows = $rows ?? collect();
$myRank = $myRank ?? 1;

/**
 * ✅ Tier rules (sama seperti GameController)
 */
$tiers = [
  ['min' => 300, 'label' => 'Legenda'],
  ['min' => 100, 'label' => 'Pakar'],
  ['min' => 50,  'label' => 'Penjelajah'],
  ['min' => 25,  'label' => 'Pelatih'],
  ['min' => 10,  'label' => 'Pemula'],
  ['min' => 0,   'label' => '—'],
];

$resolveTier = function(int $xp) use ($tiers){
  foreach ($tiers as $t) {
    if ($xp >= (int)$t['min']) return (string)$t['label'];
  }
  return '—';
};

$safeRoute = function($name, $params = []) {
  if (Route::has($name)) return route($name, $params);
  return '#';
};

$nickname = (string)($player->nickname ?? $player->display_name ?? 'Player');

// ✅ Samakan nama variabel dengan halaman index (supaya sidebar/partial ikut kebaca)
$tierLabel = $tierLabel ?? $resolveTier((int)($player->xp_total ?? 0));

// (opsional) kalau masih ada referensi lama, biar aman:
$myTierLabel = $tierLabel;


$myAvatarKey = (int)($player->avatar_key ?? 1);
if($myAvatarKey < 1) $myAvatarKey = 1;
if($myAvatarKey > 5) $myAvatarKey = 5;
$myAvatarUrl = asset('images/avatars/avatar-'.$myAvatarKey.'.png');

// MENU (manual)
$menu = [
  ['label'=>'Belajar','route'=>'game.learn','active'=>false,'icon'=>'book'],
  ['label'=>'Panduan','route'=>'game.guide','active'=>false,'icon'=>'help'],
  ['label'=>'Papan Peringkat','route'=>'game.leaderboard','active'=>true,'icon'=>'trophy'],
  ['label'=>'Profil','route'=>'player.profile','active'=>false,'icon'=>'gear'],
];

// top 3
$top3 = $rows->take(3)->values();
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Papan Peringkat — Lentara Nusantara</title>

  {{-- ✅ THEME CONNECT (localStorage: piforrr-theme) --}}
  <script>
    (function () {
      const saved = localStorage.getItem('piforrr-theme') || 'dark';
      document.documentElement.setAttribute('data-theme', saved);
    })();
  </script>

  <style>
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

    .shell{
      min-height:100vh;
      display:grid;
      grid-template-columns: var(--nav-w) 1fr var(--right-w);
      gap: 14px;
      padding: 14px;
      align-items:start;
    }
    body.nav-collapsed{ --nav-w: var(--nav-w-collapsed); }

    .card{
      background: color-mix(in oklab, var(--card) 94%, transparent);
      border: 1px solid var(--line);
      border-radius: var(--r-xl);
      box-shadow: var(--shadow);
      overflow:hidden;
    }

    .main{ min-width:0; }

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

    .board{
      border-radius: var(--r-xl);
      border: 1px solid var(--line);
      background: color-mix(in oklab, var(--card) 92%, transparent);
      box-shadow: var(--shadow);
      padding: 14px;
    }

    .board-head{
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
    .board-title{
      display:grid;
      gap: 2px;
      min-width:0;
    }
    .board-title .big{ font-weight: 950; font-size: 16px; }
    .board-title .small{ font-weight: 900; font-size: 12px; color: var(--muted); }

    .back{
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
      flex:0 0 auto;
    }
    .back svg{ width:18px;height:18px; }

    .me-card{
      border-radius: var(--r-xl);
      border: 1px solid var(--line);
      background: color-mix(in oklab, var(--card) 92%, transparent);
      box-shadow: var(--shadow);
      padding: 14px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 12px;
      margin-bottom: 14px;
    }

    .me-left{ display:flex; align-items:center; gap: 12px; min-width:0; }

    .avatar{
      width: 52px;
      height: 52px;
      border-radius: 18px;
      border: 1px solid var(--line);
      background: color-mix(in oklab, var(--card) 92%, transparent);
      overflow:hidden;
      flex: 0 0 auto;
    }
    .avatar img{ width:100%; height:100%; object-fit:cover; display:block; }

    .me-meta{ display:grid; gap: 2px; min-width:0; }
    .me-meta .name{
      font-weight: 950;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
      max-width: 420px;
    }
    .me-meta .sub{
      font-weight: 900;
      color: var(--muted);
      font-size: 12px;
    }

    .me-right{ display:flex; gap: 10px; flex-wrap:wrap; justify-content:flex-end; flex: 0 0 auto; }

    .stat{
      display:inline-flex;
      align-items:center;
      gap: 8px;
      padding: 10px 12px;
      border-radius: 16px;
      border: 1px solid var(--line);
      background: color-mix(in oklab, var(--card) 92%, transparent);
      font-weight: 950;
      white-space:nowrap;
    }
    .stat .k{ color: var(--muted); font-weight: 900; }

    .podium{ display:grid; gap: 12px; margin-bottom: 14px; }
    .podium-grid{ display:grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    @media (max-width: 720px){ .podium-grid{ grid-template-columns: 1fr; } }

    .pod{
      border-radius: var(--r-xl);
      border: 1px solid var(--line);
      background: color-mix(in oklab, var(--card) 92%, transparent);
      box-shadow: var(--shadow);
      padding: 12px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 12px;
      position:relative;
      overflow:hidden;
    }
    .pod::before{
      content:"";
      position:absolute;
      inset:-2px;
      background:
        radial-gradient(420px 140px at 20% 10%, rgba(249,115,22,.18), transparent 60%),
        radial-gradient(420px 140px at 80% 70%, rgba(59,130,246,.10), transparent 60%);
      pointer-events:none;
      opacity:.85;
    }

    .pod-left{ display:flex; align-items:center; gap: 10px; min-width:0; position:relative; z-index:1; }

    .rank-badge{
      width: 44px; height: 44px;
      border-radius: 16px;
      border: 1px solid var(--line);
      background: color-mix(in oklab, var(--card) 92%, transparent);
      display:grid;
      place-items:center;
      font-weight: 950;
      flex:0 0 auto;
    }

    .r-ava{
      width: 42px; height: 42px;
      border-radius: 16px;
      border: 1px solid var(--line);
      background: color-mix(in oklab, var(--card) 92%, transparent);
      overflow:hidden;
      flex: 0 0 auto;
    }
    .r-ava img{ width:100%; height:100%; object-fit:cover; display:block; }

    .pod-name{ display:grid; gap: 2px; min-width:0; }
    .pod-name .n{
      font-weight: 950;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
      max-width: 260px;
    }
    .pod-name .meta{
      display:flex; gap:10px; flex-wrap:wrap;
      font-weight: 900;
      color: var(--muted);
      font-size: 12px;
    }

    .pod-right{ position:relative; z-index:1; font-weight: 950; color: color-mix(in oklab, var(--brand) 75%, var(--txt-body)); white-space:nowrap; }

    .list{
      border-radius: var(--r-xl);
      border: 1px solid var(--line);
      background: color-mix(in oklab, var(--card) 92%, transparent);
      box-shadow: var(--shadow);
      overflow:hidden;
    }

    .row{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 10px;
      padding: 12px 14px;
      border-top: 1px solid var(--line);
    }
    .row:first-child{ border-top:none; }

    .left{ display:flex; align-items:center; gap: 10px; min-width:0; }

    .r-rank{
      width: 42px; height: 42px;
      border-radius: 16px;
      border: 1px solid var(--line);
      background: color-mix(in oklab, var(--card) 92%, transparent);
      display:grid;
      place-items:center;
      font-weight: 950;
      flex: 0 0 auto;
    }

    .r-meta{ min-width:0; display:grid; gap:2px; }
    .r-name{
      font-weight: 950;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
      max-width: 520px;
    }
    .r-tier{
      font-weight: 900;
      font-size: 12px;
      color: var(--muted);
    }

    .xp{ font-weight: 950; color: #93c5fd; white-space:nowrap; flex: 0 0 auto; }

    .you{ background: rgba(249,115,22,.10); }

    @media (max-width: 1024px){
      .shell{ grid-template-columns: 1fr !important; padding: 0 14px 14px; }
      .right{ display:none !important; }
    }

    /* ✅ MOBILE: jangan double — tampilkan hanya Top 1 di podium */
@media (max-width: 720px){
  /* podium: sembunyikan rank 2 & 3 */
  .pod[data-podium-rank="2"],
  .pod[data-podium-rank="3"]{
    display:none !important;
  }

  /* list: sembunyikan rank 1 karena sudah tampil di podium */
  .row[data-rank="1"]{
    display:none !important;
  }
}

  </style>

  {{-- ✅ SIDEBAR CSS (INCLUDE) --}}
  @include('player.partials.learn-sidebar', ['mode' => 'css'])
</head>

<body>
  {{-- ✅ MOBILE BAR + MOBILE DRAWER (INCLUDE) --}}
  @include('player.partials.learn-sidebar', ['mode' => 'mobile'])

  <div class="shell">
    {{-- ✅ DESKTOP SIDEBAR (INCLUDE) --}}
    @include('player.partials.learn-sidebar', ['mode' => 'desktop'])

    <main class="main" aria-label="Papan Peringkat">
      <section class="board">
        <div class="board-head">
          <div class="board-title">
            <div class="big">Papan Peringkat</div>
            <div class="small">Ranking berdasarkan total XP</div>
          </div>

          <a class="back" href="{{ $safeRoute('game.learn') }}">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Kembali</span>
          </a>
        </div>

        {{-- My card --}}
        <div class="me-card">
          <div class="me-left">
            <div class="avatar" aria-hidden="true">
              <img src="{{ $myAvatarUrl }}" alt="Avatar {{ $myAvatarKey }}" onerror="this.style.display='none'">
            </div>
            <div class="me-meta">
              <div class="name">{{ $nickname }}</div>
<div class="sub">Tier: <b style="color:var(--txt-body)">{{ $tierLabel }}</b></div>

            </div>
          </div>

          <div class="me-right">
            <div class="stat"><span class="k">Rank</span> <span>#{{ (int)$myRank }}</span></div>
            <div class="stat"><span class="k">Total XP</span> <span>{{ (int)($player->xp_total ?? 0) }}</span></div>
          </div>
        </div>

        {{-- Podium top 3 --}}
        <div class="podium">
          <div class="podium-grid">
            @for($p=0;$p<3;$p++)
              @php
                $r = $top3[$p] ?? null;
                $rankNum = $p + 1;

                $nm = $r ? (string)($r->nickname ?: $r->username ?: $r->display_name ?: 'Player') : '—';
                $xp = $r ? (int)($r->xp_total ?? 0) : 0;
                $tier = $resolveTier($xp); // ✅ tier per user

                $ak = $r ? (int)($r->avatar_key ?? 1) : 1;
                if($ak < 1) $ak = 1;
                if($ak > 5) $ak = 5;
                $ava = asset('images/avatars/avatar-'.$ak.'.png');
              @endphp

<div class="pod" data-podium-rank="{{ $rankNum }}">

<div class="pod-left">
                  <div class="rank-badge">{{ $rankNum }}</div>
                  <div class="r-ava" aria-hidden="true">
                    <img src="{{ $ava }}" alt="Avatar {{ $ak }}" onerror="this.style.display='none'">
                  </div>
                  <div class="pod-name">
                    <div class="n">{{ $nm }}</div>
                    <div class="meta">
                      <span>{{ $xp }} XP</span>
                      <span>• Tier: <b style="color:var(--txt-body)">{{ $tier }}</b></span>
                    </div>
                  </div>
                </div>
                <div class="pod-right">
                  @if($rankNum === 1) 👑 @endif
                  @if($rankNum === 2) 🥈 @endif
                  @if($rankNum === 3) 🥉 @endif
                </div>
              </div>
            @endfor
          </div>
        </div>

        {{-- List --}}
        <div class="list" aria-label="Daftar peringkat">
          @forelse($rows as $i => $r)
            @php
              $rank = $i + 1;
              $isMe = ((int)($r->id ?? 0) === (int)($player->id ?? 0));
              $nm = (string)($r->nickname ?: $r->username ?: $r->display_name ?: 'Player');

              $xp = (int)($r->xp_total ?? 0);
              $tier = $resolveTier($xp); // ✅ tier per user

              $ak = (int)($r->avatar_key ?? 1);
              if($ak < 1) $ak = 1;
              if($ak > 5) $ak = 5;
              $ava = asset('images/avatars/avatar-'.$ak.'.png');
            @endphp

<div class="row {{ $isMe ? 'you' : '' }}" data-rank="{{ $rank }}">

<div class="left">
                <div class="r-rank">{{ $rank }}</div>
                <div class="r-ava" aria-hidden="true">
                  <img src="{{ $ava }}" alt="Avatar {{ $ak }}" onerror="this.style.display='none'">
                </div>

                <div class="r-meta">
                  <div class="r-name">{{ $nm }}</div>
                  <div class="r-tier">Tier: <b style="color:var(--txt-body)">{{ $tier }}</b></div>
                </div>
              </div>

              <div class="xp">{{ $xp }} XP</div>
            </div>
          @empty
            <div class="row">
              <div style="font-weight:900;color:var(--muted);">Belum ada data.</div>
            </div>
          @endforelse
        </div>
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
            <span>{{ (int)($player->hearts ?? 0) }}</span>/<span>{{ (int)($player->hearts_max ?? 5) }}</span>
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

          <a class="btn" href="{{ $safeRoute('player.profile') }}">Profil</a>

          @if(Route::has('player.logout'))
            <form method="POST" action="{{ route('player.logout') }}">
              @csrf
              <button class="btn-logout" type="submit">Keluar</button>
            </form>
          @endif
        </div>
      </section>
    </aside>
  </div>

  {{-- ✅ SIDEBAR JS (INCLUDE) --}}
  @include('player.partials.learn-sidebar', ['mode' => 'scripts'])
</body>
</html>
