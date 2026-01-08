{{-- resources/views/player/learn/guide.blade.php (NEW FULL) --}}
@php
    $player = $player ?? (object)[
        'display_name' => 'Player',
        'xp_total' => 0,
        'coins' => 0,
        'hearts' => 0,
        'hearts_max' => 5,
    ];
    $tierLabel = $tierLabel ?? '—';
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ request()->cookie('theme', 'dark') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panduan — Lentara Nusantara</title>

    <style>
        :root{
            --bg-body:#fdfaf5;
            --txt-body:#0f172a;
            --card:rgba(255,255,255,.92);
            --card-2:rgba(255,255,255,.80);
            --line:#e9e1d6;
            --muted:#616161;

            --brand:#f97316;
            --brand-2:#f97316;

            --shadow:0 20px 50px rgba(0,0,0,.12);
            --overlay:rgba(15,23,42,.35);

            --nav-w:280px;
            --radius-xl:22px;
        }
        html[data-theme="dark"]{
            --bg-body:#020617;
            --txt-body:#e5e7eb;
            --card:rgba(2,6,23,.78);
            --card-2:rgba(2,6,23,.66);
            --line:#1f2937;
            --muted:#9ca3af;

            --brand:#f97316;
            --brand-2:#f97316;

            --shadow:0 25px 60px rgba(0,0,0,.65);
            --overlay:rgba(0,0,0,.55);
        }

        *{box-sizing:border-box}
        html,body{height:100%}
        body{
            margin:0;
            background:var(--bg-body);
            color:var(--txt-body);
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
        }

        .bg{
            position:fixed; inset:0; pointer-events:none; z-index:0;
            background:
                radial-gradient(circle at 18% 12%, rgba(249,115,22,.14), transparent 42%),
                radial-gradient(circle at 78% 18%, rgba(249,115,22,.10), transparent 50%);
            opacity:.95;
        }
        html[data-theme="dark"] .bg{
            background:
                radial-gradient(circle at 18% 12%, rgba(249,115,22,.16), transparent 42%),
                radial-gradient(circle at 78% 18%, rgba(249,115,22,.12), transparent 52%);
        }

        .shell{
            position:relative; z-index:1;
            display:grid;
            grid-template-columns: var(--nav-w) 1fr;
            gap:18px;
            padding:18px;
            min-height:100vh;
        }

        /* desktop collapse */
        body.nav-collapsed{ --nav-w: 92px; }
        body.nav-collapsed .brand-text,
        body.nav-collapsed .nav-item span.label,
        body.nav-collapsed .foot-text{ display:none; }
        body.nav-collapsed .nav-item{ justify-content:center; padding:12px 10px; }
        body.nav-collapsed .nav-head{ justify-content:center; }

        .nav{
            position:sticky; top:18px;
            height: calc(100vh - 36px);
            border-radius: var(--radius-xl);
            background: linear-gradient(180deg, color-mix(in oklab, var(--card) 92%, transparent), color-mix(in oklab, var(--card-2) 92%, transparent));
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            box-shadow: var(--shadow);
            overflow:hidden;
            display:flex;
            flex-direction:column;
        }
        .nav-head{
            padding:16px 14px 12px;
            display:flex; align-items:center; gap:12px;
            border-bottom:1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: linear-gradient(180deg, rgba(249,115,22,.08), transparent);
        }
        .brand-ico{
            width:54px;height:54px;border-radius:16px; overflow:hidden;
            background: linear-gradient(135deg, rgba(249,115,22,.95), rgba(251,146,60,.78));
            box-shadow: 0 16px 40px rgba(0,0,0,.18), 0 0 22px rgba(249,115,22,.22);
            flex:0 0 auto;
            display:grid; place-items:center;
        }
        .brand-ico img{width:100%;height:100%;object-fit:cover;display:block;}
        .brand-text{display:grid;gap:2px;line-height:1;}
        .brand-title{font-weight:950;font-size:18px;}
        .brand-sub{font-weight:900;font-size:12px;color:color-mix(in oklab, var(--muted) 92%, transparent);}

        .nav-top-actions{ margin-left:auto; display:flex; gap:8px; }
        .nav-toggle{
            width:38px;height:38px;border-radius:14px;
            border:1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: rgba(255,255,255,.03);
            color: var(--txt-body);
            cursor:pointer;
            display:grid;place-items:center;
        }
        .nav-toggle svg{width:18px;height:18px;}

        .nav-menu{ padding:12px 10px; display:grid; gap:8px; }
        .nav-item{
            display:flex;align-items:center;gap:12px;
            padding:12px 12px;border-radius:16px;
            text-decoration:none;
            color: color-mix(in oklab, var(--txt-body) 92%, transparent);
            font-weight:900;
            border:1px solid transparent;
        }
        .nav-item:hover{
            background: rgba(249,115,22,.08);
            border-color: color-mix(in oklab, var(--brand) 30%, var(--line));
        }
        .nav-item.is-active{
            background: rgba(249,115,22,.12);
            border-color: color-mix(in oklab, var(--brand) 40%, var(--line));
        }
        .nav-ico{
            width:38px;height:38px;border-radius:14px;
            display:grid;place-items:center;
            background: rgba(255,255,255,.03);
            border:1px solid color-mix(in oklab, var(--line) 86%, transparent);
            color: color-mix(in oklab, var(--muted) 86%, transparent);
            flex:0 0 auto;
        }
        .nav-ico svg{width:18px;height:18px;}

        .nav-foot{
            margin-top:auto;
            padding:12px 12px 14px;
            border-top:1px solid color-mix(in oklab, var(--line) 92%, transparent);
            display:grid;
            gap:10px;
        }
        .theme-toggle{
            width:100%;
            border-radius:16px;
            padding:10px 12px;
            font-weight:950;
            border:1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: rgba(255,255,255,.03);
            color: var(--txt-body);
            cursor:pointer;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
        }
        .theme-dot{width:10px;height:10px;border-radius:999px;background:var(--brand);box-shadow:0 0 14px rgba(249,115,22,.28);}

        .main{
            min-width:0;
        }

        .topbar{
            border-radius: var(--radius-xl);
            background: linear-gradient(135deg, rgba(249,115,22,.95), rgba(251,146,60,.80));
            border: 1px solid rgba(255,255,255,.22);
            box-shadow: 0 16px 55px rgba(0,0,0,.14);
            padding: 16px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:14px;
            overflow:hidden;
            position:relative;
            margin-bottom:14px;
        }
        .topbar::before{
            content:"";
            position:absolute;
            inset:-2px;
            background:
                radial-gradient(520px 200px at 20% 20%, rgba(255,255,255,.22), transparent 60%),
                radial-gradient(520px 200px at 85% 70%, rgba(255,255,255,.14), transparent 60%);
            pointer-events:none;
        }
        .top-left{display:flex;align-items:center;gap:12px;position:relative;z-index:1;}
        .back{
            width:36px;height:36px;border-radius:14px;
            display:grid;place-items:center;
            background: rgba(0,0,0,.16);
            border: 1px solid rgba(255,255,255,.26);
            color: rgba(255,255,255,.95);
            text-decoration:none;
        }
        .back svg{width:18px;height:18px;}
        .title{
            display:grid;gap:4px;
            color:#fff;
        }
        .title .small{font-weight:900;opacity:.95;font-size:14px;}
        .title .big{font-weight:950;font-size:26px;letter-spacing:.2px;}

        .metrics{
            display:flex;align-items:center;gap:10px;
            position:relative;z-index:1;
        }
        .metric{
            display:inline-flex;align-items:center;gap:8px;
            font-weight:950;
            border:1px solid rgba(255,255,255,.28);
            background: rgba(0,0,0,.12);
            padding:8px 10px;border-radius:16px;
            color:#fff;
        }
        .metric svg{width:18px;height:18px;opacity:.95;}

        .card{
            border-radius: var(--radius-xl);
            background: linear-gradient(180deg, color-mix(in oklab, var(--card) 92%, transparent), color-mix(in oklab, var(--card-2) 92%, transparent));
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            box-shadow: var(--shadow);
            padding:16px;
        }

        .hint{
            font-weight:900;
            color: color-mix(in oklab, var(--muted) 92%, transparent);
            line-height:1.5;
            margin-top:6px;
        }

        /* Q&A dropdown */
        .qa{
            display:grid;
            gap:10px;
            margin-top:12px;
        }
        .qa-item{
            border-radius: 18px;
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: rgba(255,255,255,.03);
            overflow:hidden;
        }
        .qa-q{
            width:100%;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            padding:12px 14px;
            cursor:pointer;
            background: transparent;
            border:0;
            color: var(--txt-body);
            font-weight:950;
            text-align:left;
        }
        .qa-q .left{
            display:flex; align-items:center; gap:10px;
        }
        .qa-ico{
            width:34px;height:34px;border-radius:14px;
            display:grid;place-items:center;
            background: rgba(249,115,22,.12);
            border:1px solid color-mix(in oklab, var(--brand) 30%, var(--line));
            color: color-mix(in oklab, var(--brand) 88%, #fff);
            flex:0 0 auto;
        }
        .qa-ico svg{width:18px;height:18px;}
        .chev{
            transition: transform .18s ease;
            opacity:.9;
        }
        .qa-item.open .chev{ transform: rotate(180deg); }

        .qa-a{
            padding: 0 14px 14px;
            color: color-mix(in oklab, var(--muted) 92%, transparent);
            font-weight:900;
            line-height:1.6;
            display:none;
        }
        .qa-item.open .qa-a{ display:block; }

        @media (max-width: 980px){
            .shell{ grid-template-columns: 1fr; }
            .nav{ position:relative; top:0; height:auto; }
        }
    </style>
</head>
<body>
<div class="bg" aria-hidden="true"></div>

<div class="shell">

    <aside class="nav" aria-label="Sidebar Panduan">
        <div class="nav-head">
            <div class="brand-ico" aria-hidden="true">
                <img src="{{ asset('images/icon/icon_lentara.png') }}" alt="Lentara" onerror="this.style.display='none'">
            </div>
            <div class="brand-text">
                <div class="brand-title">Lentara Nusantara</div>
                <div class="brand-sub">Panduan permainan</div>
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
            <a class="nav-item" href="{{ route('game.learn') }}">
                <span class="nav-ico" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v17.5H6.5A2.5 2.5 0 0 0 4 23V5.5Z" stroke="currentColor" stroke-width="2"/><path d="M20 3v17.5" stroke="currentColor" stroke-width="2"/></svg>
                </span>
                <span class="label">Belajar</span>
            </a>

            <a class="nav-item is-active" href="{{ route('game.guide') }}">
                <span class="nav-ico" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M9.1 9a3 3 0 1 1 5.8 1c0 2-3 2-3 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z" stroke="currentColor" stroke-width="2"/></svg>
                </span>
                <span class="label">Panduan</span>
            </a>

            <a class="nav-item" href="{{ route('game.leaderboard') }}">
                <span class="nav-ico" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M8 21h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 17a5 5 0 0 0 5-5V4H7v8a5 5 0 0 0 5 5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M17 6h3a2 2 0 0 1-2 3h-1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M7 6H4a2 2 0 0 0 2 3h1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </span>
                <span class="label">Papan Peringkat</span>
            </a>

            <a class="nav-item" href="{{ route('player.profile') }}">
                <span class="nav-ico" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" stroke="currentColor" stroke-width="2"/><path d="M20 21a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </span>
                <span class="label">Profil</span>
            </a>
        </nav>

        <div class="nav-foot">
            <button class="theme-toggle" type="button" id="themeToggle">
                <span style="display:inline-flex;align-items:center;gap:10px;">
                    <span class="theme-dot"></span> Mode
                </span>
                <span id="themeLabel">Dark</span>
            </button>

            <div class="foot-text" style="font-weight:900;color:color-mix(in oklab, var(--muted) 92%, transparent);font-size:12px;">
                Mode ini sama dengan Home (cookie: theme)
            </div>
        </div>
    </aside>

    <main class="main">
        <section class="topbar">
            <div class="top-left">
                <a class="back" href="{{ route('game.learn') }}" aria-label="Kembali">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <div class="title">
                    <div class="small">Panduan</div>
                    <div class="big">Lentara Nusantara</div>
                </div>
            </div>

            <div class="metrics">
                <div class="metric" title="XP Total">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M13 2s2 3 2 6-2 4-2 4 4-1 4-6 2-4 2-4-1 7-4 10-1 8-1 8-6-3-6-8 4-10 5-10Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ (int)($player->xp_total ?? 0) }}</span>
                </div>

                <div class="metric" title="Hati">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 21s-7-4.6-9.2-9.1C1.4 8.9 3.4 6 6.6 6c1.8 0 3.1 1 3.9 2 0.8-1 2.1-2 3.9-2 3.2 0 5.2 2.9 3.8 5.9C19 16.4 12 21 12 21Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ (int)($player->hearts ?? 0) }}/{{ (int)($player->hearts_max ?? 5) }}</span>
                </div>

                <div class="metric" title="Uang">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M3 7h18v10H3V7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M7 7V5h10v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M12 10.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span>{{ number_format((int)($player->coins ?? 0), 0, ',', '.') }}</span>
                </div>
            </div>
        </section>

        <section class="card">
            <div style="font-weight:950;font-size:18px;">FAQ Panduan (Dropdown)</div>
            <div class="hint">
                Klik pertanyaan untuk membuka jawaban.
                Semua aturan di sini mengikuti flow yang kamu buat di controller: XP, hearts, lulus/gagal, reward pulau, dan isi ulang hearts.
            </div>

            <div class="qa" id="qaRoot">

                <div class="qa-item">
                    <button class="qa-q" type="button">
                        <span class="left">
                            <span class="qa-ico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M13 2s2 3 2 6-2 4-2 4 4-1 4-6 2-4 2-4-1 7-4 10-1 8-1 8-6-3-6-8 4-10 5-10Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                            </span>
                            <span>Cara main & aturan XP itu gimana?</span>
                        </span>
                        <span class="chev">▾</span>
                    </button>
                    <div class="qa-a">
                        • Setiap soal benar memberi <b>2 XP</b>.<br>
                        • 1 level berisi <b>5 soal</b>.<br>
                        • Total maksimal XP per level: <b>10 XP</b> (5 benar × 2 XP).
                    </div>
                </div>

                <div class="qa-item">
                    <button class="qa-q" type="button">
                        <span class="left">
                            <span class="qa-ico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M12 21s-7-4.6-9.2-9.1C1.4 8.9 3.4 6 6.6 6c1.8 0 3.1 1 3.9 2 0.8-1 2.1-2 3.9-2 3.2 0 5.2 2.9 3.8 5.9C19 16.4 12 21 12 21Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                            </span>
                            <span>Aturan hearts berkurang & regen bagaimana?</span>
                        </span>
                        <span class="chev">▾</span>
                    </button>
                    <div class="qa-a">
                        • Jika jawaban salah, <b>hearts berkurang 1</b>.<br>
                        • Hearts akan <b>regen otomatis</b> dari server tiap <b>5 menit = 1 hati</b> (realtime).<br>
                        • Jika hearts habis (0), kamu <b>tidak bisa lanjut main</b> sampai regen atau isi ulang.
                    </div>
                </div>

                <div class="qa-item">
                    <button class="qa-q" type="button">
                        <span class="left">
                            <span class="qa-ico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z" stroke="currentColor" stroke-width="2"/></svg>
                            </span>
                            <span>Syarat lulus level itu apa?</span>
                        </span>
                        <span class="chev">▾</span>
                    </button>
                    <div class="qa-a">
                        • Dari 5 soal, kamu harus benar minimal <b>3</b> untuk dinyatakan <b>lulus</b>.<br>
                        • Kalau gagal (benar &lt; 3), progress level <b>tidak disimpan</b> (sesuai flow kamu).
                    </div>
                </div>

                <div class="qa-item">
                    <button class="qa-q" type="button">
                        <span class="left">
                            <span class="qa-ico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M8 21h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 17a5 5 0 0 0 5-5V4H7v8a5 5 0 0 0 5 5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M17 6h3a2 2 0 0 1-2 3h-1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M7 6H4a2 2 0 0 0 2 3h1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </span>
                            <span>Reward selesai pulau itu dapat apa?</span>
                        </span>
                        <span class="chev">▾</span>
                    </button>
                    <div class="qa-a">
                        Jika semua level dalam 1 pulau selesai (completed), maka pulau ditandai selesai dan kamu dapat reward <b>+20 coins</b>.
                        Setelah itu pulau berikutnya terbuka (unlock).
                    </div>
                </div>

                <div class="qa-item">
                    <button class="qa-q" type="button">
                        <span class="left">
                            <span class="qa-ico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M3 7h18v10H3V7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M12 10.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z" stroke="currentColor" stroke-width="2"/></svg>
                            </span>
                            <span>Apakah hearts bisa diisi ulang dengan coin?</span>
                        </span>
                        <span class="chev">▾</span>
                    </button>
                    <div class="qa-a">
                        Bisa. Kamu dapat isi ulang hearts menjadi penuh dengan biaya <b>10 coins</b>.
                        Jika coins tidak cukup, kamu harus menunggu regen hearts sampai penuh.
                    </div>
                </div>

            </div>

            <div class="hint" style="margin-top:14px;">
                Tip: Kalau kamu ingin, nanti aku bisa tambahkan kategori dropdown per bagian (XP / Hearts / Reward / Unlock) tanpa mengubah flow game.
            </div>
        </section>
    </main>
</div>

<script>
(function(){
    // theme cookie sync
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

        const label = document.getElementById('themeLabel');
        if(label) label.textContent = (t === 'dark') ? 'Dark' : 'Light';
    }
    function toggleTheme(){
        const cur = document.documentElement.getAttribute('data-theme') || getCookie('theme') || 'dark';
        applyTheme(cur === 'dark' ? 'light' : 'dark');
    }
    const initial = document.documentElement.getAttribute('data-theme') || getCookie('theme') || 'dark';
    applyTheme(initial);

    const themeBtn = document.getElementById('themeToggle');
    if(themeBtn) themeBtn.addEventListener('click', toggleTheme);

    // desktop sidebar collapse
    const toggleNav = document.getElementById('desktopToggleNav');
    if(toggleNav){
        const saved = localStorage.getItem('guide_nav_collapsed');
        if(saved === '1') document.body.classList.add('nav-collapsed');

        toggleNav.addEventListener('click', () => {
            document.body.classList.toggle('nav-collapsed');
            localStorage.setItem('guide_nav_collapsed', document.body.classList.contains('nav-collapsed') ? '1' : '0');
        });
    }

    // dropdown Q&A
    const items = Array.from(document.querySelectorAll('.qa-item'));
    items.forEach(item => {
        const btn = item.querySelector('.qa-q');
        if(!btn) return;
        btn.addEventListener('click', () => {
            // toggle only one open at a time (feel like FAQ)
            items.forEach(x => { if(x !== item) x.classList.remove('open'); });
            item.classList.toggle('open');
        });
    });
})();
</script>
</body>
</html>
