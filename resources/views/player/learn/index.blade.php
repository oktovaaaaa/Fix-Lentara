{{-- resources/views/player/learn/index.blade.php (REPLACE FULL) --}}
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

    $activeIsland = $activeIsland ?? $islands->first();

    $getIslandColor = function($slug) use ($islandColors) {
        $slug = (string) $slug;
        return $islandColors[$slug] ?? '#f97316';
    };

    $safeRoute = function($name, $params = []) {
        if (\Illuminate\Support\Facades\Route::has($name)) return route($name, $params);
        return '#';
    };

    $activeIdx = 0;
    if ($activeIsland) {
        $activeIdx = $islands->search(fn($x)=>$x->id===$activeIsland->id);
        if ($activeIdx === false) $activeIdx = 0;
    }

    $activeIslandTitle = $activeIsland ? ($activeIsland->subtitle ?? $activeIsland->name) : 'Belajar';
    $activeLevels = $activeIsland ? ($levels[$activeIsland->id] ?? collect()) : collect();

    // build nodes (unlocked if previous completed)
    $prevDone = true;
    $pathNodes = [];
    foreach($activeLevels as $lv){
        $lvProg = $levelProgress[$lv->id] ?? null;
        $lvDone = (bool)($lvProg->is_completed ?? false);
        $isUnlockedIsland = $activeIsland ? in_array($activeIsland->id, $unlockedIslandIds, true) : false;
        $lvUnlocked = $isUnlockedIsland && $prevDone;

        $prevDone = $lvDone;

        $pathNodes[] = [
            'lv' => $lv,
            'done' => $lvDone,
            'unlocked' => $lvUnlocked,
        ];
    }

    $hasHeartsEmptyFlag = session('hearts_empty') ? true : false;
    $heartsEmpty = ((int)($player->hearts ?? 0) <= 0) || $hasHeartsEmptyFlag;

@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ request()->cookie('theme', 'dark') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Belajar — Lentara Nusantara</title>

    <style>
        /* =========================================================
           THEME (LIGHT DEFAULT) + DARK OVERRIDE
           Brand ORANGE MUST stay #f97316 in both modes
        ========================================================= */
        :root{
            /* light mode (default) */
            --bg-body: #fdfaf5;
            --txt-body: #0f172a;
            --card: rgba(255,255,255,.92);
            --card-2: rgba(255,255,255,.80);
            --line: #e9e1d6;
            --muted: #616161;

            --brand: #f97316;   /* ✅ always */
            --brand-2: #f97316; /* ✅ always */

            --shadow: 0 20px 50px rgba(0,0,0,.12);
            --overlay: rgba(15,23,42,.35);

            --nav-w: 280px;
            --right-w: 320px;

            --radius-xl: 22px;
            --radius-lg: 18px;
            --radius-md: 14px;
        }

        html[data-theme="dark"]{
            --bg-body: #020617;
            --txt-body: #e5e7eb;
            --card: rgba(2,6,23,.78);
            --card-2: rgba(2,6,23,.66);
            --line: #1f2937;
            --muted: #9ca3af;

            --brand: #f97316;   /* ✅ always */
            --brand-2: #f97316; /* ✅ always */

            --shadow: 0 25px 60px rgba(0,0,0,.65);
            --overlay: rgba(0,0,0,.55);
        }

        *{ box-sizing: border-box; }
        html, body{ height: 100%; }
        body{
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Apple Color Emoji","Segoe UI Emoji";
            background: var(--bg-body);
            color: var(--txt-body);
            overflow-x: hidden;
        }

        /* subtle background */
        .lentara-bg{
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            opacity: .95;
            background:
                radial-gradient(circle at 18% 12%, rgba(249,115,22,.14), transparent 42%),
                radial-gradient(circle at 78% 18%, rgba(249,115,22,.10), transparent 50%),
                radial-gradient(circle at 20% 85%, rgba(2,6,23,.10), transparent 55%),
                radial-gradient(circle at 85% 82%, rgba(2,6,23,.10), transparent 55%);
        }
        html[data-theme="dark"] .lentara-bg{
            background:
                radial-gradient(circle at 18% 12%, rgba(249,115,22,.16), transparent 42%),
                radial-gradient(circle at 78% 18%, rgba(249,115,22,.12), transparent 52%),
                radial-gradient(circle at 20% 85%, rgba(148,163,184,.06), transparent 55%),
                radial-gradient(circle at 85% 82%, rgba(148,163,184,.06), transparent 55%);
        }

        .app-shell{
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: grid;
            grid-template-columns: var(--nav-w) 1fr var(--right-w);
            gap: 18px;
            padding: 18px;
        }

        /* =========================================================
           SIDEBAR (DESKTOP TOGGLE + MOBILE DRAWER)
        ========================================================= */
        .drawer-overlay{
            position: fixed;
            inset: 0;
            background: var(--overlay);
            z-index: 60;
            opacity: 0;
            pointer-events: none;
            transition: opacity .18s ease;
        }
        .drawer-overlay.is-open{
            opacity: 1;
            pointer-events: auto;
        }

        .nav{
            border-radius: var(--radius-xl);
            background: linear-gradient(180deg, color-mix(in oklab, var(--card) 92%, transparent), color-mix(in oklab, var(--card-2) 92%, transparent));
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        /* desktop sidebar container sticky */
        .nav-desktop{
            position: sticky;
            top: 18px;
            height: calc(100vh - 36px);
        }

        /* ✅ desktop collapse behaviour (B) */
        body.nav-collapsed{
            --nav-w: 92px;
        }
        body.nav-collapsed .nav-head .brand-text,
        body.nav-collapsed .nav-item span.label,
        body.nav-collapsed .nav-foot .foot-text{
            display:none;
        }
        body.nav-collapsed .nav-item{
            justify-content:center;
            padding: 12px 10px;
        }
        body.nav-collapsed .nav-head{
            justify-content:center;
        }
        body.nav-collapsed .brand-ico{
            width: 52px; height: 52px;
        }

        .nav-head{
            padding: 16px 14px 12px;
            display: flex;
            gap: 12px;
            align-items: center;
            border-bottom: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: linear-gradient(180deg, rgba(249,115,22,.08), transparent);
        }
        .brand-ico{
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at 30% 25%, rgba(255,255,255,.35), transparent 55%),
                linear-gradient(135deg, rgba(249,115,22,.95), rgba(251,146,60,.78));
            box-shadow: 0 16px 40px rgba(0,0,0,.18), 0 0 22px rgba(249,115,22,.22);
            overflow: hidden;
            flex: 0 0 auto;
        }
        .brand-ico img{ width: 100%; height: 100%; object-fit: cover; display:block; }
        .brand-text{ display: grid; gap: 2px; line-height: 1; min-width:0; }
        .brand-title{ font-weight: 950; font-size: 18px; letter-spacing: .2px; }
        .brand-sub{ font-weight: 900; font-size: 12px; color: color-mix(in oklab, var(--muted) 92%, transparent); letter-spacing: .2px; }

        .nav-top-actions{
            margin-left: auto;
            display:flex;
            align-items:center;
            gap: 8px;
        }

        .nav-toggle{
            width: 38px;
            height: 38px;
            border-radius: 14px;
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: rgba(255,255,255,.03);
            color: var(--txt-body);
            cursor: pointer;
            display: grid;
            place-items: center;
            transition: transform .15s ease, background .15s ease;
        }
        .nav-toggle:hover{ transform: translateY(-1px); background: rgba(255,255,255,.05); }
        .nav-toggle svg{ width:18px;height:18px; }

        .nav-menu{ padding: 12px 10px; display: grid; gap: 8px; }
        .nav-item{
            display: flex; align-items: center; gap: 12px;
            padding: 12px 12px;
            border-radius: 16px;
            text-decoration: none;
            color: color-mix(in oklab, var(--txt-body) 92%, transparent);
            font-weight: 900;
            transition: transform .15s ease, background .15s ease, border-color .15s ease;
            border: 1px solid transparent;
        }
        .nav-item:hover{
            background: rgba(249,115,22,.08);
            border-color: color-mix(in oklab, var(--brand) 30%, var(--line));
            transform: translateY(-1px);
        }
        .nav-item.is-active{
            background: rgba(249,115,22,.12);
            border: 1px solid color-mix(in oklab, var(--brand) 40%, var(--line));
            box-shadow: inset 0 0 0 1px rgba(249,115,22,.14);
        }

        .nav-ico{
            width: 38px; height: 38px;
            border-radius: 14px;
            display: grid; place-items: center;
            background: rgba(255,255,255,.03);
            border: 1px solid color-mix(in oklab, var(--line) 86%, transparent);
            color: color-mix(in oklab, var(--muted) 86%, transparent);
            flex:0 0 auto;
        }
        .nav-ico svg{ width: 18px; height: 18px; }

        .nav-foot{
            margin-top: auto;
            padding: 12px 12px 14px;
            border-top: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            display:grid;
            gap:10px;
        }

        .theme-toggle{
            width:100%;
            border-radius: 16px;
            padding: 10px 12px;
            font-weight: 950;
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: rgba(255,255,255,.03);
            color: var(--txt-body);
            cursor: pointer;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
        }
        .theme-toggle .pill{
            display:inline-flex;
            align-items:center;
            gap:8px;
            font-weight:950;
        }
        .theme-dot{
            width:10px;height:10px;border-radius:999px;
            background: var(--brand);
            box-shadow: 0 0 14px rgba(249,115,22,.28);
        }

        /* Mobile drawer */
        .nav-mobile{
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: min(320px, 88vw);
            z-index: 70;
            transform: translateX(-105%);
            transition: transform .22s ease;
            border-radius: 0 22px 22px 0;
        }
        .nav-mobile.is-open{ transform: translateX(0); }

        /* MAIN */
        .main{ min-width: 0; position: relative; }

        /* mobile topbar (hamburger + metrics row) */
        .mobile-topbar{
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
        }

        .hamburger{
            width: 44px;
            height: 44px;
            border-radius: 14px;
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: rgba(255,255,255,.03);
            color: var(--txt-body);
            cursor: pointer;
            display: grid;
            place-items: center;
            box-shadow: 0 14px 40px rgba(0,0,0,.12);
        }
        .hamburger svg{ width: 20px; height: 20px; }

        .metrics-row{
            display:flex;
            align-items:center;
            gap: 10px;
        }

        .metric{
            display:inline-flex;
            align-items:center;
            gap:8px;
            font-weight:950;
            border:1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: rgba(255,255,255,.03);
            padding: 8px 10px;
            border-radius: 16px;
            color: var(--txt-body);
        }
        .metric svg{ width:18px;height:18px; opacity:.95; }
        .metric.heart{ color: #ef4444; }
        .metric.xp{ color: color-mix(in oklab, var(--brand) 35%, #3b82f6); }
        .metric.money{ color:#22c55e; }

        /* HEADER BAR (DUO STYLE) */
        .unit-bar{
            border-radius: var(--radius-xl);
            background: linear-gradient(135deg, rgba(249,115,22,.95), rgba(251,146,60,.80));
            border: 1px solid rgba(255,255,255,.22);
            box-shadow: 0 16px 55px rgba(0,0,0,.14);
            padding: 16px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 14px;
            overflow:hidden;
            position:relative;
        }
        .unit-bar::before{
            content:"";
            position:absolute;
            inset:-2px;
            background:
                radial-gradient(520px 200px at 20% 20%, rgba(255,255,255,.22), transparent 60%),
                radial-gradient(520px 200px at 85% 70%, rgba(255,255,255,.14), transparent 60%);
            pointer-events:none;
        }

        .unit-left{ display:flex; align-items:center; gap:12px; min-width:0; position:relative; z-index:1; }
        .unit-back{
            width:36px;height:36px;border-radius:14px;
            display:grid;place-items:center;
            background: rgba(0,0,0,.16);
            border: 1px solid rgba(255,255,255,.26);
            color: rgba(255,255,255,.95);
            text-decoration:none;
            flex:0 0 auto;
        }
        .unit-back svg{ width:18px;height:18px; }
        .unit-text{ display:grid; gap:4px; min-width:0; }
        .unit-small{ font-weight:900;color:rgba(255,255,255,.95);opacity:.95;font-size:14px; }
        .unit-title{
            font-weight:950;font-size:26px;color:#fff;letter-spacing:.2px;
            white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
            max-width:680px;
        }

        .unit-right{ display:flex;align-items:center;gap:10px;flex:0 0 auto; position:relative; z-index:1; }
        .guide-btn{
            display:inline-flex;align-items:center;gap:10px;
            padding:10px 14px;border-radius:16px;
            border:1px solid rgba(0,0,0,.18);
            background: rgba(0,0,0,.12);
            color: rgba(255,255,255,.95);
            text-decoration:none;
            font-weight:950;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.14);
            transition: transform .15s ease, filter .15s ease;
        }
        .guide-btn:hover{ transform: translateY(-1px); filter: saturate(1.05); }
        .guide-btn svg{ width:20px;height:20px; }

        /* BOARD */
        .board{
            border-radius: var(--radius-xl);
            background: linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.02));
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            box-shadow: var(--shadow);
            padding: 18px;
            min-height: calc(100vh - 18px - 18px - 14px - 92px);
            position: relative;
            overflow: hidden;
        }
        .board::before{
            content:"";
            position:absolute;
            inset:-2px;
            background:
                radial-gradient(520px 220px at 52% 10%, rgba(249,115,22,.14), transparent 60%),
                radial-gradient(420px 260px at 35% 92%, rgba(249,115,22,.10), transparent 60%);
            pointer-events:none;
            opacity:.9;
        }
        .board-inner{ position:relative; z-index:1; display:grid; gap:14px; }
        .section-title{
            text-align:center;font-weight:950;font-size:16px;
            color: color-mix(in oklab, var(--txt-body) 92%, transparent);
            margin:6px 0 0;
        }

        /* =========================================================
           PATH NODES (CONNECTED VIA SVG CURVES)
        ========================================================= */
        .path{
            margin: 6px auto 0;
            width: min(560px, 100%);
            display:grid;
            gap: 18px;
            justify-items:center;
            position:relative;
            padding: 8px 0 6px;
        }

        .path svg.path-svg{
            position:absolute;
            inset:0;
            width:100%;
            height:100%;
            pointer-events:none;
            z-index:0;
            overflow:visible;
        }

        .node-wrap{
            display:grid;
            justify-items:center;
            position:relative;
            z-index:2;
            width: 100%;
        }

        /* Zig-zag layout similar to duolingo (works nice for 5 levels) */
        .node-wrap:nth-child(1){ transform: translateX(0); }
        .node-wrap:nth-child(2){ transform: translateX(-96px); }
        .node-wrap:nth-child(3){ transform: translateX(92px); }
        .node-wrap:nth-child(4){ transform: translateX(-72px); }
        .node-wrap:nth-child(5){ transform: translateX(72px); }

        .node{
            width:78px;height:78px;border-radius:18px;
            display:grid;place-items:center;
            border:1px solid color-mix(in oklab, var(--line) 100%, transparent);
            background: rgba(255,255,255,.02);
            box-shadow: 0 12px 40px rgba(0,0,0,.14);
            position:relative;
            transition: transform .16s ease, box-shadow .16s ease, filter .16s ease;
            text-decoration:none;
            color: var(--txt-body);
            cursor: pointer;
        }
        .node:hover{
            transform: translateY(-2px);
            filter: saturate(1.04);
            box-shadow: 0 18px 55px rgba(0,0,0,.18);
        }
        .node.is-locked{ cursor:not-allowed; opacity:.70; filter: grayscale(.05); }
        .node.is-locked:hover{ transform:none; box-shadow: 0 12px 40px rgba(0,0,0,.14); }

        .node-core{
            width:62px;height:62px;border-radius:16px;
            display:grid;place-items:center;
            position:relative;
            overflow:hidden;
            border:2px solid rgba(255,255,255,.10);
            background: rgba(2,6,23,.08);
        }
        html[data-theme="dark"] .node-core{ background: rgba(2,6,23,.35); }

        /* done: star */
        .node.is-done .node-core{
            background: color-mix(in oklab, var(--node-color) 92%, #ffffff 8%);
            border-color: rgba(0,0,0,.14);
            box-shadow: 0 0 0 4px rgba(255,255,255,.10), 0 0 30px rgba(255,255,255,.12);
        }

        /* open: play */
        .node.is-open .node-core{
            background: rgba(255,255,255,.04);
            border-color: color-mix(in oklab, var(--node-color) 45%, rgba(255,255,255,.18));
        }
        .node.is-open::before{
            content:"";
            position:absolute;
            inset:-4px;
            border-radius:22px;
            background:
                radial-gradient(circle at 30% 20%, color-mix(in oklab, var(--node-color) 65%, transparent), transparent 55%),
                radial-gradient(circle at 70% 85%, rgba(255,255,255,.10), transparent 55%);
            filter: blur(10px);
            opacity:.9;
            pointer-events:none;
        }

        .node svg{ width:34px;height:34px; opacity:.95; }

        .node-label{ margin-top:6px; font-size:13px; font-weight:950; opacity:.92; text-align:center; }
        .node-sub{ font-size:12px; opacity:.75; font-weight:900; margin-top:2px; }

        /* connected stroke style (SVG) */
        .path-stroke{
            stroke: color-mix(in oklab, var(--line) 80%, transparent);
            stroke-width: 10;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            opacity: .9;
        }
        .path-stroke.glow{
            stroke: color-mix(in oklab, var(--brand) 55%, transparent);
            stroke-width: 4;
            opacity: .28;
            filter: blur(1.2px);
        }

        /* RIGHT PANEL */
        .right{
            position: sticky;
            top: 18px;
            height: calc(100vh - 36px);
            display:grid;
            gap:14px;
            align-content:start;
        }
        .panel{
            border-radius: var(--radius-xl);
            background: linear-gradient(180deg, color-mix(in oklab, var(--card) 92%, transparent), color-mix(in oklab, var(--card-2) 92%, transparent));
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            box-shadow: var(--shadow);
            overflow:hidden;
        }
        .panel-head{
            padding:14px 14px 10px;
            border-bottom: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            display:flex; align-items:center; justify-content:space-between; gap:10px;
        }
        .panel-title{ font-weight:950; font-size:18px; margin:0; }

        .top-metrics{
            display:flex;
            align-items:center;
            gap:14px;
            padding: 14px;
            flex-wrap: wrap;
        }

        .right-actions{
            padding: 14px;
            display:flex;
            justify-content:space-between;
            gap: 10px;
            align-items:center;
        }
        .right-actions a{
            text-decoration:none;
            font-weight:950;
            color: color-mix(in oklab, var(--brand) 75%, #3b82f6);
        }
        .btn-logout{
            background: rgba(255,255,255,.03);
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            color: var(--txt-body);
            padding: 8px 10px;
            border-radius: 14px;
            font-weight: 950;
            cursor: pointer;
        }

        .tier-row{
            padding: 0 14px 14px;
            font-size: 13px;
            font-weight: 900;
            color: color-mix(in oklab, var(--muted) 92%, transparent);
        }

        .flash{
            border-radius:16px;
            padding:10px 12px;
            border:1px solid transparent;
            margin-bottom: 12px;
            font-weight:900;
            background: rgba(255,255,255,.03);
        }
        .flash.ok{
            border-color: rgba(34,197,94,.35);
            background: rgba(34,197,94,.10);
            color: rgba(6,95,70,.95);
        }
        html[data-theme="dark"] .flash.ok{ color: rgba(167,243,208,.95); }
        .flash.err{
            border-color: rgba(239,68,68,.35);
            background: rgba(239,68,68,.10);
            color: rgba(127,29,29,.95);
        }
        html[data-theme="dark"] .flash.err{ color: rgba(254,202,202,.95); }

        /* MODAL */
        .modal-wrap{
            position: fixed;
            inset: 0;
            z-index: 90;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: var(--overlay);
        }
        .modal-wrap.is-open{ display:flex; }

        .modal{
            width: min(520px, 92vw);
            border-radius: 22px;
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: linear-gradient(180deg, color-mix(in oklab, var(--card) 95%, transparent), color-mix(in oklab, var(--card-2) 92%, transparent));
            box-shadow: 0 30px 110px rgba(0,0,0,.22);
            overflow: hidden;
            position: relative;
        }
        html[data-theme="dark"] .modal{ box-shadow: 0 30px 110px rgba(0,0,0,.55); }

        .modal::before{
            content:"";
            position:absolute;
            inset:-2px;
            background: radial-gradient(420px 220px at 30% 15%, color-mix(in oklab, var(--modal-accent) 40%, transparent), transparent 62%);
            pointer-events:none;
            opacity:.9;
        }
        .modal-inner{ position:relative; z-index:1; padding: 16px; }
        .modal-title{
            font-weight: 950;
            font-size: 18px;
            margin: 0 0 6px;
        }
        .modal-sub{
            margin: 0 0 12px;
            color: color-mix(in oklab, var(--muted) 92%, transparent);
            font-weight: 900;
            line-height: 1.4;
        }
        .modal-actions{
            display:flex;
            gap: 10px;
            justify-content: flex-end;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        .btn{
            border-radius: 16px;
            padding: 10px 14px;
            font-weight: 950;
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: rgba(255,255,255,.03);
            color: var(--txt-body);
            cursor: pointer;
        }
        .btn.primary{
            background: var(--modal-accent);
            border-color: color-mix(in oklab, var(--modal-accent) 60%, transparent);
            color: #111;
        }
        .btn.danger{
            background: rgba(239,68,68,.16);
            border-color: rgba(239,68,68,.35);
            color: rgba(127,29,29,.95);
        }
        html[data-theme="dark"] .btn.danger{ color: rgba(254,202,202,.95); }

        .small-note{
            font-size: 12px;
            font-weight: 900;
            color: color-mix(in oklab, var(--muted) 92%, transparent);
            margin-top: 10px;
        }

        /* Responsive */
        @media (max-width: 1100px){
            :root{ --right-w: 300px; --nav-w: 270px; }
            body.nav-collapsed{ --nav-w: 92px; }
            .unit-title{ max-width: 520px; }
        }

        @media (max-width: 980px){
            .app-shell{ grid-template-columns: 1fr; padding: 14px; }
            .right{ position: relative; height:auto; top:0; }

            .nav-desktop{ display:none; }
            .mobile-topbar{ display:flex; }
        }

        @media (min-width: 981px){
            .nav-mobile{ display:none !important; }
            .drawer-overlay{ display:none !important; }
        }

        @media (max-width: 520px){
            .unit-bar{ padding:14px; }
            .unit-title{ font-size:22px; }
            .guide-btn{ padding:10px 12px; }
            .node{ width:72px; height:72px; }
            .node-core{ width:58px; height:58px; }
            .node-wrap:nth-child(2),
            .node-wrap:nth-child(3),
            .node-wrap:nth-child(4),
            .node-wrap:nth-child(5){ transform: translateX(0); }
        }
    </style>
</head>

<body>
<div class="lentara-bg" aria-hidden="true"></div>

{{-- MOBILE SIDEBAR OVERLAY --}}
<div class="drawer-overlay" id="drawerOverlay" aria-hidden="true"></div>

{{-- MOBILE SIDEBAR --}}
<aside class="nav nav-mobile" id="mobileNav" aria-label="Sidebar Navigasi Mobile">
    <div class="nav-head">
        <div class="brand-ico" aria-hidden="true">
            <img src="{{ asset('images/icon/icon_lentara.png') }}" alt="Lentara" onerror="this.style.display='none'">
        </div>
        <div class="brand-text">
            <div class="brand-title">Lentara Nusantara</div>
            <div class="brand-sub">Belajar budaya dengan game</div>
        </div>
        <div class="nav-top-actions">
            <button class="nav-toggle" type="button" id="closeMobileNav" aria-label="Tutup menu">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </div>

    <nav class="nav-menu">
        @php
            $menu = [
                ['label'=>'Belajar','route'=>'game.learn','active'=>true,'icon'=>'book'],
                ['label'=>'Panduan','route'=>'game.guide','active'=>false,'icon'=>'help'],
                ['label'=>'Papan Peringkat','route'=>'game.leaderboard','active'=>false,'icon'=>'trophy'],
                ['label'=>'Profil','route'=>'player.profile','active'=>false,'icon'=>'gear'],
            ];
        @endphp

        @foreach($menu as $m)
            <a class="nav-item {{ $m['active'] ? 'is-active' : '' }}" href="{{ $safeRoute($m['route']) }}">
                <span class="nav-ico" aria-hidden="true">
                    @if($m['icon']==='book')
                        <svg viewBox="0 0 24 24" fill="none"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v17.5H6.5A2.5 2.5 0 0 0 4 23V5.5Z" stroke="currentColor" stroke-width="2"/><path d="M20 3v17.5" stroke="currentColor" stroke-width="2"/></svg>
                    @elseif($m['icon']==='help')
                        <svg viewBox="0 0 24 24" fill="none"><path d="M12 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M9.1 9a3 3 0 1 1 5.8 1c0 2-3 2-3 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z" stroke="currentColor" stroke-width="2"/></svg>
                    @elseif($m['icon']==='gear')
                        <svg viewBox="0 0 24 24" fill="none"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="2"/><path d="M19.4 15a7.94 7.94 0 0 0 .1-1 7.94 7.94 0 0 0-.1-1l2-1.5-2-3.5-2.3 1a8.3 8.3 0 0 0-1.7-1L15 2h-6l-.4 3.5a8.3 8.3 0 0 0-1.7 1l-2.3-1-2 3.5 2 1.5a7.94 7.94 0 0 0-.1 1c0 .34.03.67.1 1l-2 1.5 2 3.5 2.3-1a8.3 8.3 0 0 0 1.7 1L9 22h6l.4-3.5a8.3 8.3 0 0 0 1.7-1l2.3 1 2-3.5-2-1.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none"><path d="M8 21h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 17a5 5 0 0 0 5-5V4H7v8a5 5 0 0 0 5 5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M17 6h3a2 2 0 0 1-2 3h-1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M7 6H4a2 2 0 0 0 2 3h1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    @endif
                </span>
                <span class="label">{{ $m['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="nav-foot">
        <button class="theme-toggle" type="button" id="themeToggleMobile">
            <span class="pill"><span class="theme-dot"></span> Mode</span>
            <span id="themeLabelMobile">Dark</span>
        </button>
    </div>
</aside>

<div class="app-shell">

    {{-- LEFT SIDEBAR DESKTOP (TOGGLE) --}}
    <aside class="nav nav-desktop" aria-label="Sidebar Navigasi">
        <div class="nav-head">
            <div class="brand-ico" aria-hidden="true">
                <img src="{{ asset('images/icon/icon_lentara.png') }}" alt="Lentara" onerror="this.style.display='none'">
            </div>
            <div class="brand-text">
                <div class="brand-title">Lentara Nusantara</div>
                <div class="brand-sub">Belajar budaya dengan game</div>
            </div>

            <div class="nav-top-actions">
                <button class="nav-toggle" type="button" id="desktopToggleNav" aria-label="Buka/Tutup sidebar">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M4 7h16M4 12h16M4 17h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <nav class="nav-menu">
            @php
                $menu = [
                    ['label'=>'Belajar','route'=>'game.learn','active'=>true,'icon'=>'book'],
                    ['label'=>'Panduan','route'=>'game.guide','active'=>false,'icon'=>'help'],
                    ['label'=>'Papan Peringkat','route'=>'game.leaderboard','active'=>false,'icon'=>'trophy'],
                    ['label'=>'Profil','route'=>'player.profile','active'=>false,'icon'=>'gear'],
                ];
            @endphp

            @foreach($menu as $m)
                <a class="nav-item {{ $m['active'] ? 'is-active' : '' }}" href="{{ $safeRoute($m['route']) }}">
                    <span class="nav-ico" aria-hidden="true">
                        @if($m['icon']==='book')
                            <svg viewBox="0 0 24 24" fill="none"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v17.5H6.5A2.5 2.5 0 0 0 4 23V5.5Z" stroke="currentColor" stroke-width="2"/><path d="M20 3v17.5" stroke="currentColor" stroke-width="2"/></svg>
                        @elseif($m['icon']==='help')
                            <svg viewBox="0 0 24 24" fill="none"><path d="M12 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M9.1 9a3 3 0 1 1 5.8 1c0 2-3 2-3 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z" stroke="currentColor" stroke-width="2"/></svg>
                        @elseif($m['icon']==='gear')
                            <svg viewBox="0 0 24 24" fill="none"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="2"/><path d="M19.4 15a7.94 7.94 0 0 0 .1-1 7.94 7.94 0 0 0-.1-1l2-1.5-2-3.5-2.3 1a8.3 8.3 0 0 0-1.7-1L15 2h-6l-.4 3.5a8.3 8.3 0 0 0-1.7 1l-2.3-1-2 3.5 2 1.5a7.94 7.94 0 0 0-.1 1c0 .34.03.67.1 1l-2 1.5 2 3.5 2.3-1a8.3 8.3 0 0 0 1.7 1L9 22h6l.4-3.5a8.3 8.3 0 0 0 1.7-1l2.3 1 2-3.5-2-1.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none"><path d="M8 21h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 17a5 5 0 0 0 5-5V4H7v8a5 5 0 0 0 5 5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M17 6h3a2 2 0 0 1-2 3h-1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M7 6H4a2 2 0 0 0 2 3h1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        @endif
                    </span>
                    <span class="label">{{ $m['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="nav-foot">
            <button class="theme-toggle" type="button" id="themeToggleDesktop">
                <span class="pill"><span class="theme-dot"></span> Mode</span>
                <span id="themeLabelDesktop">Dark</span>
            </button>

            <div class="foot-text" style="font-weight:900;color:color-mix(in oklab, var(--muted) 92%, transparent);font-size:12px;">
                Kamu bisa ganti mode dari sini.<br>
                Mode akan sama dengan Home.
            </div>
        </div>
    </aside>

    {{-- MAIN --}}
    <main class="main" aria-label="Konten Belajar">

        {{-- MOBILE TOPBAR --}}
        <div class="mobile-topbar">
            <button class="hamburger" id="openSidebar" aria-label="Buka menu">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M5 7h14M5 12h14M5 17h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <div class="metrics-row">
                <div class="metric xp" title="XP Total">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M13 2s2 3 2 6-2 4-2 4 4-1 4-6 2-4 2-4-1 7-4 10-1 8-1 8-6-3-6-8 4-10 5-10Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ (int)($player->xp_total ?? 0) }}</span>
                </div>
                <div class="metric heart" title="Hati">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 21s-7-4.6-9.2-9.1C1.4 8.9 3.4 6 6.6 6c1.8 0 3.1 1 3.9 2 0.8-1 2.1-2 3.9-2 3.2 0 5.2 2.9 3.8 5.9C19 16.4 12 21 12 21Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ (int)($player->hearts ?? 0) }}/{{ (int)($player->hearts_max ?? 5) }}</span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="flash ok">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash err">{{ session('error') }}</div>
        @endif

        {{-- TOP BAR --}}
        <section class="unit-bar">
            <div class="unit-left">
                <a class="unit-back" href="{{ $safeRoute('home') }}" aria-label="Kembali">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>

                <div class="unit-text">
                    <div class="unit-small">Bagian {{ ($activeIdx ?? 0) + 1 }}, Unit 1</div>
                    <div class="unit-title">{{ $activeIslandTitle }}</div>
                </div>
            </div>

            <div class="unit-right">
                <a class="guide-btn" href="{{ $safeRoute('game.guide') }}">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M9.1 9a3 3 0 1 1 5.8 1c0 2-3 2-3 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span>Panduan</span>
                </a>
            </div>
        </section>

        {{-- BOARD / PATH --}}
        <section class="board">
            <div class="board-inner">
                <div class="section-title">{{ $activeIslandTitle }}</div>

                <div class="path" id="pathRoot" aria-label="Jalur Level">
                    {{-- SVG connection lines will be injected by JS --}}
                    <svg class="path-svg" id="pathSvg" aria-hidden="true"></svg>

                    @if(collect($pathNodes)->isEmpty())
                        <div style="text-align:center; padding: 24px; color: color-mix(in oklab, var(--muted) 92%, transparent); font-weight: 900;">
                            Belum ada level untuk bagian ini (admin harus menambahkan).
                        </div>
                    @else
                        @foreach($pathNodes as $i => $node)
                            @php
                                $lv = $node['lv'];
                                $lvDone = $node['done'];
                                $lvUnlocked = $node['unlocked'];

                                $nodeColor = $activeIsland ? $getIslandColor($activeIsland->slug) : '#f97316';
                                $nodeColorFinal = $lvDone ? $nodeColor : ($lvUnlocked ? $nodeColor : $lockedColor);

                                $lvTitle = $lv->title ?? ('Level '.($i+1));
                                $totalQ = 5;   // (game constant) keep as existing UI
                                $maxXp = 10;   // (5 correct * 2 xp) keep as existing UI
                            @endphp

                            <div class="node-wrap" data-node-wrap="1">
                                @if($lvUnlocked)
                                    <a class="node {{ $lvDone ? 'is-done' : 'is-open' }}"
                                       data-node="1"
                                       style="--node-color: {{ $nodeColorFinal }};"
                                       href="{{ route('game.play', $lv->id) }}"
                                       data-open-level="1"
                                       data-level-url="{{ route('game.play', $lv->id) }}"
                                       data-level-title="{{ e($lvTitle) }}"
                                       data-level-index="{{ $i+1 }}"
                                       data-level-totalq="{{ $totalQ }}"
                                       data-level-maxxp="{{ $maxXp }}"
                                       data-accent="{{ $nodeColor }}"
                                       title="{{ $lvTitle }} — {{ $lvDone ? 'Selesai' : 'Mulai' }}">

                                        <span class="node-core">
                                            @if($lvDone)
                                                {{-- DONE: STAR --}}
                                                <svg viewBox="0 0 24 24" fill="none">
                                                    <path d="M12 17.3l-5.1 2.7 1-5.7L3.8 9.6l5.8-.8L12 3.6l2.4 5.2 5.8.8-4.1 4.7 1 5.7-5.1-2.7Z"
                                                          stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                </svg>
                                            @else
                                                {{-- OPEN: PLAY --}}
                                                <svg viewBox="0 0 24 24" fill="none">
                                                    <path d="M9 7l10 5-10 5V7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                    <path d="M4 4v16" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".25"/>
                                                </svg>
                                            @endif
                                        </span>
                                    </a>
                                    <div class="node-label">{{ $lvTitle }}</div>
                                    <div class="node-sub">{{ $lvDone ? 'Selesai' : 'Mulai' }}</div>
                                @else
                                    <span class="node is-locked"
                                          data-node="1"
                                          style="--node-color: {{ $lockedColor }};"
                                          title="{{ $lvTitle }} — Terkunci">
                                        <span class="node-core">
                                            {{-- LOCKED: PADLOCK --}}
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path d="M7 11V8a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <path d="M6 11h12v9H6v-9Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                                <path d="M12 15v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </span>
                                    </span>
                                    <div class="node-label">{{ $lvTitle }}</div>
                                    <div class="node-sub">Terkunci</div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- BAGIAN LAINNYA --}}
                <div style="margin-top: 18px; border-top: 1px solid color-mix(in oklab, var(--line) 92%, transparent); padding-top: 14px;">
                    <div style="text-align:center;font-weight:950;opacity:.92;margin-bottom:10px;">
                        Bagian Lainnya
                    </div>

                    <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:10px;">
                        @foreach($islands as $idx => $isl)
                            @php
                                $isUnlocked = in_array($isl->id, $unlockedIslandIds, true);
                                $isCompleted = (bool)($islandProgress[$isl->id]->is_completed ?? false);

                                $c = $isCompleted ? $getIslandColor($isl->slug) : ($isUnlocked ? $getIslandColor($isl->slug) : $lockedColor);
                                $islandLink = request()->fullUrlWithQuery(['island' => $isl->id]);
                            @endphp

                            <a href="{{ $isUnlocked ? $islandLink : 'javascript:void(0)' }}"
                               style="
                                    display:inline-flex;align-items:center;gap:10px;
                                    padding:10px 12px;border-radius:16px;
                                    border:1px solid color-mix(in oklab, var(--line) 92%, transparent);
                                    background: rgba(255,255,255,.03);
                                    color: {{ $isUnlocked ? 'var(--txt-body)' : '#9ca3af' }};
                                    text-decoration:none;
                                    font-weight:950;
                                    opacity: {{ $isUnlocked ? '1' : '.7' }};
                                    cursor: {{ $isUnlocked ? 'pointer' : 'not-allowed' }};
                                ">
                                <span style="width:10px;height:10px;border-radius:999px;background:{{ $c }};display:inline-block; box-shadow: 0 0 14px rgba(0,0,0,.08);"></span>
                                <span>Bagian {{ $idx+1 }} — {{ $isl->subtitle ?? $isl->name }}</span>
                                <span style="opacity:.78;font-weight:900;">
                                    ({{ $isCompleted ? 'Selesai' : ($isUnlocked ? 'Terbuka' : 'Terkunci') }})
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

            </div>
        </section>
    </main>

    {{-- RIGHT SIDEBAR --}}
    <aside class="right" aria-label="Panel Status">
        <section class="panel">
            <div class="panel-head">
                <h3 class="panel-title">Status</h3>
            </div>

            <div class="top-metrics">
                <div class="metric xp" title="XP Total">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M13 2s2 3 2 6-2 4-2 4 4-1 4-6 2-4 2-4-1 7-4 10-1 8-1 8-6-3-6-8 4-10 5-10Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ (int)($player->xp_total ?? 0) }}</span>
                </div>

                <div class="metric heart" title="Hati">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 21s-7-4.6-9.2-9.1C1.4 8.9 3.4 6 6.6 6c1.8 0 3.1 1 3.9 2 0.8-1 2.1-2 3.9-2 3.2 0 5.2 2.9 3.8 5.9C19 16.4 12 21 12 21Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ (int)($player->hearts ?? 0) }}/{{ (int)($player->hearts_max ?? 5) }}</span>
                </div>

                <div class="metric money" title="Uang">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M3 7h18v10H3V7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M7 7V5h10v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M12 10.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span>{{ number_format((int)($player->coins ?? 0), 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="right-actions">
                <a href="{{ route('player.profile') }}">Profil</a>
                <form method="POST" action="{{ route('player.logout') }}">
                    @csrf
                    <button class="btn-logout" type="submit">Keluar</button>
                </form>
            </div>

            <div class="tier-row">
                Halo, <b style="color:var(--txt-body)">{{ $player->display_name }}</b><br>
                Tier: <b style="color:var(--txt-body)">{{ $tierLabel }}</b>
            </div>
        </section>
    </aside>

</div>

{{-- MODAL START LEVEL --}}
<div class="modal-wrap" id="startModal" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" style="--modal-accent:#f97316;">
        <div class="modal-inner">
            <div class="modal-title" id="startTitle">Mulai Level</div>
            <div class="modal-sub" id="startSub">Pelajaran 1 dari 5</div>

            <div style="display:grid;gap:10px;">
                <div style="padding:12px;border-radius:18px;border:1px solid color-mix(in oklab, var(--line) 92%, transparent);background:rgba(255,255,255,.03);font-weight:900;">
                    Maksimal XP di level ini: <span id="startMaxXp">10</span> XP
                </div>
                <div class="small-note">
                    Minimal lulus: 3 jawaban benar. Salah akan mengurangi 1 hati.
                </div>
            </div>

            <div class="modal-actions">
                <button class="btn" type="button" id="startCancel">Batal</button>
                <button class="btn primary" type="button" id="startGo">Mulai +<span id="startMaxXp2">10</span> XP</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL HATI HABIS (TIDAK BISA DITUTUP) --}}
<div class="modal-wrap {{ $heartsEmpty ? 'is-open' : '' }}" id="heartsModal" aria-hidden="{{ $heartsEmpty ? 'false' : 'true' }}">
    <div class="modal" role="dialog" aria-modal="true" style="--modal-accent:#ef4444;">
        <div class="modal-inner">
            <div class="modal-title">Hati kamu habis</div>
            <div class="modal-sub">
                Kamu tidak bisa lanjut bermain sekarang.
                <br>Silahkan tunggu hati terisi kembali atau isi ulang dengan 10 uang.
            </div>

            <div id="heartsModalMsg" class="small-note" style="display:none;"></div>

            <div class="modal-actions" style="justify-content:space-between;">
                <a class="btn" href="{{ route('home') }}" style="text-decoration:none;display:inline-flex;align-items:center;">
                    Kembali ke Beranda
                </a>
                <button class="btn danger" type="button" id="btnRefillHearts">
                    Isi Ulang Hati (10 Uang)
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    // =========================
    // Theme sync with HOME (cookie: theme)
    // =========================
    function getCookie(name){
        const m = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return m ? decodeURIComponent(m[2]) : null;
    }
    function setCookie(name, value, days){
        const d = new Date();
        d.setTime(d.getTime() + (days*24*60*60*1000));
        document.cookie = name + "=" + encodeURIComponent(value) + ";expires=" + d.toUTCString() + ";path=/;SameSite=Lax";
    }
    function applyTheme(theme){
        const t = (theme === 'light') ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', t);
        setCookie('theme', t, 365);

        const labelD = document.getElementById('themeLabelDesktop');
        const labelM = document.getElementById('themeLabelMobile');
        if(labelD) labelD.textContent = (t === 'dark') ? 'Dark' : 'Light';
        if(labelM) labelM.textContent = (t === 'dark') ? 'Dark' : 'Light';
    }
    function toggleTheme(){
        const cur = document.documentElement.getAttribute('data-theme') || getCookie('theme') || 'dark';
        applyTheme(cur === 'dark' ? 'light' : 'dark');
    }

    // init from cookie if needed
    const initial = document.documentElement.getAttribute('data-theme') || getCookie('theme') || 'dark';
    applyTheme(initial);

    const themeDesk = document.getElementById('themeToggleDesktop');
    const themeMob  = document.getElementById('themeToggleMobile');
    if(themeDesk) themeDesk.addEventListener('click', toggleTheme);
    if(themeMob) themeMob.addEventListener('click', toggleTheme);

    // =========================
    // Desktop sidebar toggle (B)
    // =========================
    const desktopToggle = document.getElementById('desktopToggleNav');
    if(desktopToggle){
        // persist collapsed state in localStorage (UI only)
        const saved = localStorage.getItem('learn_nav_collapsed');
        if(saved === '1') document.body.classList.add('nav-collapsed');

        desktopToggle.addEventListener('click', () => {
            document.body.classList.toggle('nav-collapsed');
            localStorage.setItem('learn_nav_collapsed', document.body.classList.contains('nav-collapsed') ? '1' : '0');
        });
    }

    // =========================
    // Mobile sidebar drawer
    // =========================
    const openBtn = document.getElementById('openSidebar');
    const nav = document.getElementById('mobileNav');
    const overlay = document.getElementById('drawerOverlay');
    const closeBtn = document.getElementById('closeMobileNav');

    function openNav(){
        if(!nav || !overlay) return;
        nav.classList.add('is-open');
        overlay.classList.add('is-open');
    }
    function closeNav(){
        if(!nav || !overlay) return;
        nav.classList.remove('is-open');
        overlay.classList.remove('is-open');
    }

    if(openBtn) openBtn.addEventListener('click', openNav);
    if(overlay) overlay.addEventListener('click', closeNav);
    if(closeBtn) closeBtn.addEventListener('click', closeNav);

    // =========================
    // Start Level Modal
    // =========================
    const startModal = document.getElementById('startModal');
    const startTitle = document.getElementById('startTitle');
    const startSub   = document.getElementById('startSub');
    const startMaxXp = document.getElementById('startMaxXp');
    const startMaxXp2= document.getElementById('startMaxXp2');
    const startCancel= document.getElementById('startCancel');
    const startGo    = document.getElementById('startGo');

    let pendingUrl = null;

    function openStartModal(opts){
        if(!startModal) return;
        const accent = opts.accent || '#f97316';
        startModal.querySelector('.modal').style.setProperty('--modal-accent', accent);

        startTitle.textContent = opts.title || 'Mulai Level';
        startSub.textContent = `Pelajaran ${opts.index} dari ${opts.totalQ}`;
        startMaxXp.textContent = opts.maxXp;
        startMaxXp2.textContent = opts.maxXp;

        pendingUrl = opts.url || null;
        startModal.classList.add('is-open');
    }
    function closeStartModal(){
        if(!startModal) return;
        startModal.classList.remove('is-open');
        pendingUrl = null;
    }

    document.querySelectorAll('[data-open-level="1"]').forEach(el => {
        el.addEventListener('click', function(e){
            e.preventDefault();

            const heartsNow = {{ (int)($player->hearts ?? 0) }};
            if(heartsNow <= 0) return;

            const title = this.getAttribute('data-level-title') || 'Level';
            const url = this.getAttribute('data-level-url') || '#';
            const idx = this.getAttribute('data-level-index') || '1';
            const totalQ = this.getAttribute('data-level-totalq') || '5';
            const maxXp = this.getAttribute('data-level-maxxp') || '10';
            const accent = this.getAttribute('data-accent') || '#f97316';

            openStartModal({title, url, index: idx, totalQ, maxXp, accent});
        });
    });

    if(startCancel) startCancel.addEventListener('click', closeStartModal);
    if(startGo) startGo.addEventListener('click', function(){
        if(pendingUrl && pendingUrl !== '#') window.location.href = pendingUrl;
    });

    // =========================
    // Hearts refill modal
    // =========================
    const btnRefill = document.getElementById('btnRefillHearts');
    const heartsMsg = document.getElementById('heartsModalMsg');

    async function refillHearts(){
        if(!btnRefill) return;
        btnRefill.disabled = true;
        btnRefill.style.opacity = '.7';

        try{
            const res = await fetch("{{ route('game.hearts.refill') }}", {
                method: "POST",
                headers: {
                    "Content-Type":"application/json",
                    "X-CSRF-TOKEN":"{{ csrf_token() }}",
                    "Accept":"application/json",
                },
                body: JSON.stringify({})
            });

            const json = await res.json();

            if(!res.ok || !json.ok){
                if(heartsMsg){
                    heartsMsg.style.display = 'block';
                    heartsMsg.textContent = json.message || 'Uang tidak cukup. Silahkan tunggu hati sampai penuh.';
                }
                btnRefill.disabled = false;
                btnRefill.style.opacity = '1';
                return;
            }

            window.location.reload();
        }catch(err){
            if(heartsMsg){
                heartsMsg.style.display = 'block';
                heartsMsg.textContent = 'Terjadi kesalahan. Coba lagi.';
            }
            btnRefill.disabled = false;
            btnRefill.style.opacity = '1';
        }
    }
    if(btnRefill) btnRefill.addEventListener('click', refillHearts);

    // =========================
    // CONNECT PATH (SVG curves)
    // =========================
    function buildPath(){
        const root = document.getElementById('pathRoot');
        const svg = document.getElementById('pathSvg');
        if(!root || !svg) return;

        // clear
        while(svg.firstChild) svg.removeChild(svg.firstChild);

        const nodes = Array.from(root.querySelectorAll('[data-node="1"]'));
        if(nodes.length <= 1) return;

        // set viewBox to root box
        const r = root.getBoundingClientRect();
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

            // cubic curve control points (duo-like bend)
            const midY = (a.y + b.y)/2;
            const dx = (b.x - a.x);
            const cp1 = { x: a.x, y: midY };
            const cp2 = { x: b.x, y: midY };

            const d = `M ${a.x} ${a.y} C ${cp1.x} ${cp1.y}, ${cp2.x} ${cp2.y}, ${b.x} ${b.y}`;

            const p1 = document.createElementNS('http://www.w3.org/2000/svg','path');
            p1.setAttribute('d', d);
            p1.setAttribute('class','path-stroke');
            svg.appendChild(p1);

            const p2 = document.createElementNS('http://www.w3.org/2000/svg','path');
            p2.setAttribute('d', d);
            p2.setAttribute('class','path-stroke glow');
            svg.appendChild(p2);
        }
    }

    // build after layout
    const ro = new ResizeObserver(() => buildPath());
    const pathRoot = document.getElementById('pathRoot');
    if(pathRoot) ro.observe(pathRoot);

    window.addEventListener('load', buildPath);
    window.addEventListener('resize', buildPath);

})();
</script>

</body>
</html>
