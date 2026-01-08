{{-- resources/views/player/learn/guide.blade.php (REPLACE FULL) --}}
@php
    $player = $player ?? (object)[
        'display_name' => 'Player',
        'nickname' => null,
        'xp_total' => 0,
        'coins' => 0,
        'hearts' => 0,
        'hearts_max' => 5,
    ];

    $tierLabel = $tierLabel ?? '—';

    $nickname = (string)($player->nickname ?? $player->display_name ?? 'Player');

    $safeRoute = function($name, $params = []) {
        if (\Illuminate\Support\Facades\Route::has($name)) return route($name, $params);
        return '#';
    };

    // ✅ samakan icon key dengan partial sidebar (book/help/gear/trophy)
    $menu = [
        ['label'=>'Belajar','route'=>'game.learn','active'=>false,'icon'=>'book'],
        ['label'=>'Panduan','route'=>'game.guide','active'=>true,'icon'=>'help'],
        ['label'=>'Papan Peringkat','route'=>'game.leaderboard','active'=>false,'icon'=>'trophy'],
        ['label'=>'Profil','route'=>'player.profile','active'=>false,'icon'=>'gear'],
    ];
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panduan — Lentara Nusantara</title>

    {{-- ✅ THEME CONNECT (SAMA DENGAN INDEX): localStorage piforrr-theme --}}
    <script>
        (function () {
            const saved = localStorage.getItem('piforrr-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', saved);
        })();
    </script>

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
          --right-w: 0px; /* ✅ guide tidak pakai right panel */

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

        /* background glow halus */
        .bg{
            position:fixed; inset:0; pointer-events:none; z-index:0;
            background:
                radial-gradient(circle at 18% 12%, rgba(249,115,22,.14), transparent 42%),
                radial-gradient(circle at 78% 18%, rgba(249,115,22,.10), transparent 50%),
                radial-gradient(circle at 15% 85%, rgba(59,130,246,.08), transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(34,197,94,.06), transparent 45%);
            opacity:.95;
        }
        html[data-theme="dark"] .bg{
            background:
                radial-gradient(circle at 18% 12%, rgba(249,115,22,.16), transparent 42%),
                radial-gradient(circle at 78% 18%, rgba(249,115,22,.12), transparent 52%),
                radial-gradient(circle at 15% 85%, rgba(59,130,246,.10), transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(34,197,94,.08), transparent 45%);
        }

        /* ✅ override layout untuk guide: 2 kolom (sidebar + main) */
        .shell{
            position: relative;
            z-index: 1;
            grid-template-columns: var(--nav-w) 1fr !important;
        }

        /* ===== Main Guide ===== */
        .main{ min-width:0; position:relative; z-index:1; }

        .topbar{
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

        .top-left{
            display:flex;
            align-items:center;
            gap: 12px;
            min-width:0;
        }

        .back{
            width: 42px; height: 42px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: color-mix(in oklab, var(--card) 92%, transparent);
            color: var(--txt-body);
            display:grid;
            place-items:center;
            text-decoration:none;
            flex:0 0 auto;
        }
        .back svg{ width:18px;height:18px; }

        .title{
            display:grid;
            gap: 2px;
            min-width:0;
        }
        .title .small{
            font-weight: 950;
            font-size: 12px;
            color: var(--muted);
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }
        .title .big{
            font-weight: 950;
            font-size: 18px;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }

        .content-card{
            border-radius: var(--r-xl);
            border: 1px solid var(--line);
            background: color-mix(in oklab, var(--card) 92%, transparent);
            box-shadow: var(--shadow);
            padding: 16px;
        }

        .hint{
            font-weight: 900;
            color: var(--muted);
            line-height: 1.5;
            margin-top: 6px;
        }

        /* Q&A dropdown */
        .qa{ display:grid; gap:10px; margin-top:12px; }
        .qa-item{
            border-radius: 18px;
            border: 1px solid var(--line);
            background: color-mix(in oklab, var(--card) 92%, transparent);
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
            min-width:0;
        }
        .qa-q .left span:last-child{
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
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
        .chev{ transition: transform .18s ease; opacity:.9; font-weight:950; }
        .qa-item.open .chev{ transform: rotate(180deg); }
        .qa-a{
            padding: 0 14px 14px;
            color: var(--muted);
            font-weight:900;
            line-height:1.6;
            display:none;
        }
        .qa-item.open .qa-a{ display:block; }

        /* mobile spacing */
        @media (max-width: 1024px){
            .main{ padding-bottom: 14px; }
        }
    </style>

    {{-- ✅ SIDEBAR CSS (INCLUDE) --}}
    @include('player.partials.learn-sidebar', ['mode' => 'css'])
</head>

<body>
<div class="bg" aria-hidden="true"></div>

{{-- ✅ MOBILE BAR + MOBILE DRAWER (INCLUDE) --}}
@include('player.partials.learn-sidebar', ['mode' => 'mobile'])

<div class="shell">
    {{-- ✅ DESKTOP SIDEBAR (INCLUDE) --}}
    @include('player.partials.learn-sidebar', ['mode' => 'desktop'])

    {{-- MAIN --}}
    <main class="main">
        <section class="topbar">
            <div class="top-left">
                <a class="back" href="{{ $safeRoute('game.learn') }}" aria-label="Kembali">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <div class="title">
                    <div class="small">Panduan</div>
                    <div class="big">Lentara Nusantara</div>
                </div>
            </div>

            {{-- sengaja kosong: indikator XP/Hati/Uang sudah ada di bar atas (mobile) & panel lain --}}
            <div style="width:1px;height:1px;opacity:0;" aria-hidden="true"></div>
        </section>

        <section class="content-card">
            <div style="font-weight:950;font-size:18px;">FAQ Panduan (Dropdown)</div>
            <div class="hint">
                Klik pertanyaan untuk membuka jawaban. Semua aturan mengikuti flow di controller kamu: XP, hearts, lulus/gagal, reward pulau, dan isi ulang hearts.
            </div>

            <div class="qa" id="qaRoot">

                <div class="qa-item">
                    <button class="qa-q" type="button">
                        <span class="left">
                            <span class="qa-ico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M13 2s2 3 2 6-2 4-2 4 4-1 4-6 2-4 2-4-1 7-4 10-1 8-1 8-6-3-6-8 4-10 5-10Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                </svg>
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
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M12 21s-7-4.6-9.2-9.1C1.4 8.9 3.4 6 6.6 6c1.8 0 3.1 1 3.9 2 0.8-1 2.1-2 3.9-2 3.2 0 5.2 2.9 3.8 5.9C19 16.4 12 21 12 21Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span>Aturan hearts berkurang & regen bagaimana?</span>
                        </span>
                        <span class="chev">▾</span>
                    </button>
                    <div class="qa-a">
                        • Jika jawaban salah, <b>hearts berkurang 1</b>.<br>
                        • Hearts <b>regen otomatis</b> dari server tiap <b>5 menit = 1 hati</b>.<br>
                        • Jika hearts habis (0), kamu <b>tidak bisa lanjut</b> sampai regen atau isi ulang.
                    </div>
                </div>

                <div class="qa-item">
                    <button class="qa-q" type="button">
                        <span class="left">
                            <span class="qa-ico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M9 12l2 2 4-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </span>
                            <span>Syarat lulus level itu apa?</span>
                        </span>
                        <span class="chev">▾</span>
                    </button>
                    <div class="qa-a">
                        • Dari 5 soal, kamu harus benar minimal <b>3</b> untuk <b>lulus</b>.<br>
                        • Kalau gagal (benar &lt; 3), progress level <b>tidak disimpan</b> (sesuai flow kamu).
                    </div>
                </div>

                <div class="qa-item">
                    <button class="qa-q" type="button">
                        <span class="left">
                            <span class="qa-ico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M8 21h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M12 17a5 5 0 0 0 5-5V4H7v8a5 5 0 0 0 5 5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    <path d="M17 6h3a2 2 0 0 1-2 3h-1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M7 6H4a2 2 0 0 0 2 3h1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <span>Reward selesai pulau itu dapat apa?</span>
                        </span>
                        <span class="chev">▾</span>
                    </button>
                    <div class="qa-a">
                        Jika semua level dalam 1 pulau selesai, pulau ditandai selesai dan kamu dapat reward <b>+20 coins</b>. Lalu pulau berikutnya terbuka.
                    </div>
                </div>

                <div class="qa-item">
                    <button class="qa-q" type="button">
                        <span class="left">
                            <span class="qa-ico" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M3 7h18v10H3V7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    <path d="M12 10.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </span>
                            <span>Apakah hearts bisa diisi ulang dengan coin?</span>
                        </span>
                        <span class="chev">▾</span>
                    </button>
                    <div class="qa-a">
                        Bisa. Isi ulang hearts menjadi penuh dengan biaya <b>10 coins</b>.
                        Jika coins tidak cukup, kamu harus menunggu regen hearts.
                    </div>
                </div>

            </div>

            <div class="hint" style="margin-top:14px;">
                Tip: Kalau kamu mau, nanti bisa aku tambahkan kategori dropdown (XP / Hearts / Reward / Unlock) tanpa mengubah flow game.
            </div>
        </section>
    </main>
</div>

{{-- ✅ SIDEBAR JS (INCLUDE) --}}
@include('player.partials.learn-sidebar', ['mode' => 'scripts'])

<script>
(function(){
    // =========================================
    // FAQ DROPDOWN
    // =========================================
    const items = Array.from(document.querySelectorAll('.qa-item'));
    items.forEach(item => {
        const btn = item.querySelector('.qa-q');
        if(!btn) return;
        btn.addEventListener('click', () => {
            items.forEach(x => { if(x !== item) x.classList.remove('open'); });
            item.classList.toggle('open');
        });
    });
})();
</script>
</body>
</html>
