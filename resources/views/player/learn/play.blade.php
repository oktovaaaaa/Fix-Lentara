{{-- resources/views/player/learn/play.blade.php (REPLACE FULL) --}}

@extends('layouts.game')
@section('title', $level->title ?? 'Level')

@php
    $player = $player ?? (object)[
        'xp_total' => 0,
        'coins' => 0,
        'hearts' => 5,
        'hearts_max' => 5,
        'display_name' => 'Player',
        'nickname' => null,
        'avatar_key' => 1,
    ];

    $islandColors = $islandColors ?? [];

    $levelTitle = $level->title ?? 'Level';

    $islandSlug = $level->island?->slug ?? '';
    $accent = $islandColors[$islandSlug] ?? '#f97316';

    $islandLabel = strtoupper($level->island?->subtitle ?? $level->island?->name ?? 'PULAU');

    // ✅ pakai nickname sebagai nama tampil
    $nickname = (string)($player->nickname ?? $player->display_name ?? 'Player');

    $avatarKey = (int)($player->avatar_key ?? 1);
    if ($avatarKey < 1) $avatarKey = 1;
    if ($avatarKey > 5) $avatarKey = 5;
    $avatarUrl = asset('images/avatars/avatar-'.$avatarKey.'.png');

    // payload untuk JS
    $payload = [];
    foreach(($questions ?? []) as $q){
        $payload[] = [
            'id' => (int)$q->id,
            'type' => (string)$q->type, // mcq / fill
            'text' => (string)$q->question_text,
            'image' => $q->image_path ? asset($q->image_path) : null,
            'options' => [
                'A' => $q->option_a,
                'B' => $q->option_b,
                'C' => $q->option_c,
                'D' => $q->option_d,
            ],
            'fillMax' => (int) $q->fillMaxLength(),
        ];
    }

    // default 8 menit (atau ambil dari level jika ada)
    $timeLimitSec = (int)($level->time_limit_seconds ?? 0);
    if ($timeLimitSec <= 0) $timeLimitSec = 8 * 60;

    /**
     * ✅ PENTING:
     * Paksa URL check ke route yang benar supaya tidak pernah POST ke /belajar/level/{id}
     * (Yang GET-only)
     */
    $checkUrl  = route('game.check', $level->id);          // POST /belajar/level/{level}/check
    $refillUrl = route('game.hearts.refill');              // POST /hati/isi-ulang
    $profileUrl= route('player.profile');                  // GET /profil

    // ✅ SFX (request user)
    $sfxCorrect = asset('audio/benar.M4A');
    $sfxWrong   = asset('audio/salah.M4A');
@endphp


@push('styles')
<style>
    /* =========================================================
       THEME GLOBAL (SOLID — TANPA TRANSPARAN / GRADIENT)
    ========================================================= */
    :root{
        --bg-body: #fdfaf5;
        --txt-body: #0f172a;
        --card: #ffffff;
        --line: #e9e1d6;
        --muted: #616161;

        --brand: #b7410e;
        --brand-2: #f4c842;

        /* ✅ Alert solid jelas */
        --success-bg: #16a34a;
        --success-border: #15803d;

        --error-bg: #dc2626;
        --error-border: #b91c1c;

        --accent: {{ $accent }};

        --r-xl: 26px;
        --r-lg: 18px;
        --r-md: 14px;

        /* footer image height */
        --footer-h: 96px; /* ~ h-24 */
    }

    html[data-theme="dark"]{
        --bg-body: #020617;
        --txt-body: #e5e7eb;
        --card: #020617;
        --line: #1f2937;
        --muted: #9ca3af;

        --brand: #f97316;
        --brand-2: #fde68a;
    }

    *{ box-sizing:border-box; }
    html,body{ height:100%; }
body{
    margin:0;
    font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
    background: var(--bg-body);
    color: var(--txt-body);
    overflow-x:hidden;

    /* ✅ bikin footer bawah bisa nempel bawah kalau konten pendek */
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}


    /* =========================================================
       ✅ FOOTER IMAGE (TOP & BOTTOM) — FIXED, DI BELAKANG KONTEN
       - full width
       - tidak gerak (fixed)
       - komponen menimpa gambar (z-index konten lebih besar)
    ========================================================= */
/* TOP: tetap fixed di atas */
.bg-footer.top{
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: var(--footer-h);
    object-fit: cover;
    display:block;
    z-index: 0;
    pointer-events: none;
}

/* BOTTOM: ikut halaman (bukan fixed) */
.bg-footer.bottom{
    position: static;
    width: 100%;
    height: var(--footer-h);
    object-fit: cover;
    display:block;

    /* ✅ ini kuncinya: nempel bawah kalau konten pendek */
    margin-top: auto;

    /* optional biar gak “ketarik” mengecil */
    flex-shrink: 0;

    pointer-events: none;
}


    /* =========================================================
       WRAP
       - kasih ruang agar konten tidak ketutup footer image fixed
    ========================================================= */
.wrap{
    position: relative;
    z-index: 2;
    max-width: 1180px;
    margin: 16px auto;
    padding: calc(var(--footer-h) + 10px) 18px 18px; /* ✅ bawah cukup kecil */
    flex: 1 0 auto; /* ✅ dorong footer ke bawah saat konten pendek */

}


    /* =========================================================
       TOP BAR (SOLID)
    ========================================================= */
    .topbar{
        border-radius: 999px;
        background: var(--card);
        border: 2px solid var(--line);
        padding: 12px 14px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap: 12px;
    }

    .top-left{
        display:flex;
        align-items:center;
        gap: 12px;
        min-width: 260px;
    }

    .back{
        width: 44px;
        height: 44px;
        border-radius: 999px;
        border: 2px solid var(--line);
        background: var(--card);
        color: var(--txt-body);
        display:grid;
        place-items:center;
        text-decoration:none;
        flex:0 0 auto;
        transition: transform .14s ease, border-color .14s ease;
    }
    .back:hover{
        transform: translateY(-1px);
        border-color: var(--accent);
    }
    .back svg{ width:20px;height:20px; }

    .title{
        display:grid;
        gap: 2px;
        min-width:0;
    }
    .title small{
        color: var(--muted);
        font-weight: 900;
        letter-spacing: .14em;
        font-size: 11px;
    }
    .title h1{
        margin:0;
        font-size: 18px;
        font-weight: 950;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .top-mid{
        flex: 1 1 auto;
        display:flex;
        justify-content:center;
        min-width: 260px;
    }

    .soal-pill{
        width: min(460px, 100%);
        border-radius: 999px;
        border: 2px solid var(--line);
        background: var(--card);
        padding: 10px 12px;
        display:flex;
        align-items:center;
        gap: 12px;
    }
    .soal-meta{
        font-weight: 950;
        letter-spacing:.10em;
        font-size: 11px;
        color: var(--muted);
        display:flex;
        flex-direction:column;
        line-height: 1.05;
        min-width: 68px;
    }
    .soal-meta b{
        color: var(--txt-body);
        letter-spacing: 0;
        font-size: 12px;
        margin-top: 4px;
    }

    .soal-bar{
        height: 10px;
        border-radius: 999px;
        background: var(--line);
        overflow:hidden;
        flex: 1 1 auto;
        position: relative;
    }
    .soal-bar > i{
        position:absolute;
        inset:0;
        width: 0%;
        border-radius: inherit;
        background: var(--accent); /* ✅ solid */
        transition: width .25s ease;
    }

    .top-right{
        display:flex;
        align-items:center;
        justify-content:flex-end;
        gap: 10px;
        min-width: 420px;
        flex-wrap: wrap;
    }

    .pill{
        display:inline-flex;
        align-items:center;
        gap: 8px;
        padding: 10px 12px;
        border-radius: 999px;
        border: 2px solid var(--line);
        background: var(--card);
        font-weight: 950;
        white-space: nowrap;
    }
    .pill svg{ width:18px;height:18px; }

    .pill.time{ color: var(--txt-body); }
    .pill.xp{ color: #3b82f6; }
    .pill.coin{ color: #22c55e; }
    .pill.heart{ color: #dc2626; }

    .profile-pill{
        gap: 10px;
        text-decoration:none;
        color: inherit;
        transition: transform .14s ease, border-color .14s ease;
    }
    .profile-pill:hover{
        transform: translateY(-1px);
        border-color: var(--accent);
    }
    .avatar{
        width: 28px;
        height: 28px;
        border-radius: 999px;
        border: 2px solid var(--line);
        background: var(--card);
        object-fit: cover;
        display:block;
    }
    .pname{
        font-weight: 950;
        max-width: 160px;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    /* =========================================================
       GAME CARD
       ✅ Aksen beda per pulau: border kiri + border atas solid
    ========================================================= */
    .card{
        margin-top: 14px;
        border-radius: var(--r-xl);
        background: var(--card);
        border: 2px solid var(--line);
        border-left: 10px solid var(--accent);
        overflow:hidden;
    }
    .card-head{
        padding: 16px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap: 10px;
        border-bottom: 2px solid var(--line);
        /* aksen tipis atas juga */
        box-shadow: inset 0 6px 0 0 var(--accent);
    }
    .qmeta{ font-weight: 950; font-size: 16px; }
    .progress{ font-weight: 950; color: var(--muted); }

    .card-body{
        padding: 16px;
        display:grid;
        gap: 12px;
    }

    .qtext{
        font-size: 16px;
        font-weight: 900;
        line-height: 1.4;
        white-space: pre-wrap;
    }

.qimg img{
    max-width: 320px;   /* ⬅️ BATAS UKURAN GAMBAR */
    width: 100%;
    max-height: 320px;  /* ⬅️ BATAS TINGGI */
    object-fit: contain;
    margin: 0 auto;
    border-radius: 16px;
    border: 2px solid var(--line);
    display:block;
}


    .options{ display:grid; gap: 10px; }
    .opt{
        display:flex;
        align-items:center;
        gap: 10px;
        padding: 12px;
        border-radius: 18px;
        border: 2px solid var(--line);
        background: var(--card);
        cursor: pointer;
        transition: transform .15s ease, border-color .15s ease;
        user-select:none;
    }
    .opt:hover{
        transform: translateY(-1px);
        border-color: var(--accent);
    }
    .opt .badge{
        width: 36px;
        height: 36px;
        border-radius: 14px;
        display:grid;
        place-items:center;
        font-weight: 950;
        background: var(--card);
        border: 2px solid var(--line);
    }
    .opt .label{ font-weight: 900; }
    .opt.is-selected{
        border-color: var(--accent);
        background: var(--card);
        box-shadow: inset 0 0 0 2px var(--accent);
    }

    .fill{
        display:flex;
        gap: 10px;
        align-items:flex-end;
        flex-wrap: wrap;
    }
    .fill label{
        display:block;
        font-weight: 950;
        margin-bottom: 6px;
    }
    .fill input{
        width: 280px;
        max-width: 100%;
        padding: 12px;
        border-radius: 18px;
        border: 2px solid var(--line);
        background: var(--card);
        color: var(--txt-body);
        font-weight: 950;
        outline: none;
        letter-spacing: .5px;
    }
    .fill input:focus{
        border-color: var(--accent);
        box-shadow: inset 0 0 0 2px var(--accent);
    }
    .hint{
        font-size: 12px;
        font-weight: 900;
        color: var(--muted);
    }

    .footer{
        padding: 16px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap: 12px;
        border-top: 2px solid var(--line);
        background: var(--card);
    }

    /* ✅ alert benar/salah solid, merah/hijau jelas */
    .feedback{
        display:none;
        align-items:center;
        gap: 10px;
        font-weight: 950;
        padding: 10px 12px;
        border-radius: 999px;
        border: 2px solid var(--line);
        background: var(--card);
        color: var(--txt-body);
    }
    .feedback.ok{
        display:flex;
        background: var(--success-bg);
        border-color: var(--success-border);
        color: #ffffff;
    }
    .feedback.err{
        display:flex;
        background: var(--error-bg);
        border-color: var(--error-border);
        color: #ffffff;
    }

    .btn{
        border-radius: 999px;
        padding: 12px 16px;
        font-weight: 950;
        border: 2px solid var(--line);
        background: var(--card);
        color: var(--txt-body);
        cursor: pointer;
        transition: transform .12s ease, opacity .12s ease;
        text-decoration:none;
        display:inline-flex;
        align-items:center;
        justify-content:center;
    }
    .btn:hover{ transform: translateY(-1px); }
    .btn:disabled{ opacity:.55; cursor:not-allowed; transform:none; }
    .btn.primary{
        background: var(--accent);
        border-color: var(--accent);
        color: #0b1220;
    }
    html[data-theme="dark"] .btn.primary{ color:#0b1220; }

    @keyframes shake {
        0% { transform: translateX(0); }
        20% { transform: translateX(-6px); }
        40% { transform: translateX(6px); }
        60% { transform: translateX(-4px); }
        80% { transform: translateX(4px); }
        100% { transform: translateX(0); }
    }
    .shake{ animation: shake .35s ease; }

    /* =========================================================
       MODALS (SOLID — TANPA TRANSPARAN)
    ========================================================= */
    .modal-wrap{
        position: fixed;
        inset: 0;
        z-index: 90;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: #0b1220; /* solid overlay */
    }
    .modal-wrap.is-open{ display:flex; }

    .modal{
        width: min(680px, 92vw);
        border-radius: 28px;
        border: 2px solid var(--line);
        background: var(--card);
        overflow: hidden;
        position: relative;
    }

    .modal-inner{ padding: 18px; }

    .modal-title{
        font-weight: 950;
        font-size: 22px;
        margin: 0 0 6px;
    }
    .modal-sub{
        margin: 0 0 12px;
        color: var(--muted);
        font-weight: 850;
        line-height: 1.4;
        font-size: 15px;
    }

    .modal-actions{
        display:flex;
        gap: 12px;
        justify-content: flex-end;
        flex-wrap: wrap;
        margin-top: 14px;
    }

    /* =========================================================
       RESULT POPUP
    ========================================================= */
    .result-head{
        text-align:center;
        padding-top: 6px;
    }
    .result-head h2{
        margin: 0;
        font-size: 22px;
        font-weight: 950;
        letter-spacing: .2px;
    }

    .mascot-wrap{
        display:flex;
        align-items:center;
        justify-content:center;
        padding: 8px 0 2px;
    }
    .mascot-avatar{
        width: min(220px, 70vw);
        height: min(220px, 70vw);
        border-radius: 999px;
        border: 3px solid var(--accent);
        background: var(--card);
        overflow: hidden;
        display: grid;
        place-items: center;
    }
    .mascot-avatar img{
        width: 100%;
        height: 100%;
        object-fit: cover;
        display:block;
    }

    .result-message{
        margin-top: 10px;
        text-align: center;
        padding: 0 18px;
    }
    .result-message-line1{
        font-weight: 900;
        font-size: 18px;
        letter-spacing: .2px;
    }
    .result-message-line2{
        margin-top: 6px;
        font-weight: 600;
        font-size: 14px;
    }

    .result-stats{
        display:grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-top: 12px;
    }
    @media (max-width: 520px){
        .result-stats{ grid-template-columns: 1fr; }
    }

    .rstat{
        border-radius: 18px;
        border: 2px solid var(--line);
        background: var(--card);
        padding: 12px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap: 12px;
    }
    .rstat .k{
        font-size: 12px;
        font-weight: 900;
        color: var(--muted);
        letter-spacing: .10em;
        text-transform: uppercase;
    }
    .rstat .v{
        font-size: 18px;
        font-weight: 950;
    }

    .rbadge{
        width: 40px; height: 40px;
        border-radius: 14px;
        border: 2px solid var(--line);
        background: var(--card);
        display:grid;
        place-items:center;
        flex: 0 0 auto;
        color: var(--txt-body);
    }
    .rbadge svg{ width:20px;height:20px; }

    /* responsive topbar */
    @media (max-width: 980px){
        .top-right{ min-width: unset; }
        .top-left{ min-width: unset; }
    }
    @media (max-width: 820px){
        .topbar{ flex-wrap: wrap; border-radius: 22px; }
        .top-left{ flex: 1 1 100%; }
        .top-mid{ flex: 1 1 100%; }
        .top-right{ flex: 1 1 100%; justify-content: flex-start; }
        .pname{ max-width: 240px; }
    }
</style>
@endpush

@section('content')

{{-- ✅ TOP FOOTER IMAGE (BACKGROUND) --}}
<img
    src="{{ asset('images/icon/footer.JPEG') }}"
    alt="Lentara Footer Top"
    class="bg-footer top"
    loading="lazy"
/>



<div
    class="wrap"
    id="quizApp"
    data-sfx-correct="{{ $sfxCorrect }}"
    data-sfx-wrong="{{ $sfxWrong }}"
>

    {{-- TOPBAR --}}
    <div class="topbar">
        <div class="top-left">
<button class="back" type="button" id="btnExit" aria-label="Keluar">
    <svg viewBox="0 0 24 24" fill="none">
        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</button>


            <div class="title">
                <small>{{ $islandLabel }}</small>
                <h1>{{ $levelTitle }}</h1>
            </div>
        </div>

        <div class="top-mid">
            <div class="soal-pill" aria-label="Progress Soal">
                <div class="soal-meta">
                    <span>SOAL</span>
                    <b id="soalText">0/5</b>
                </div>
                <div class="soal-bar" aria-hidden="true">
                    <i id="soalBar"></i>
                </div>
            </div>
        </div>

        <div class="top-right">
            <div class="pill time" title="Waktu Berjalan">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M12 8v5l3 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 22a10 10 0 1 0-10-10 10 10 0 0 0 10 10Z" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span id="timeText">00:00</span>
            </div>

            <div class="pill xp" title="XP Total">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M13 2s2 3 2 6-2 4-2 4 4-1 4-6 2-4 2-4-1 7-4 10-1 8-1 8-6-3-6-8 4-10 5-10Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                </svg>
                <span id="xpText">{{ (int)($player->xp_total ?? 0) }}</span>
            </div>

            {{-- PROFILE --}}
            <a class="pill profile-pill" href="{{ $profileUrl }}" title="Profil">
                <img class="avatar" src="{{ $avatarUrl }}" alt="Avatar">
                <span class="pname">{{ $nickname }}</span>
            </a>

            <div class="pill coin" title="Uang">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M20 12c0 4-4 7-8 7s-8-3-8-7 4-7 8-7 8 3 8 7Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 9v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M9.5 12H14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span id="coinsText">{{ (int)($player->coins ?? 0) }}</span>
            </div>

            <div class="pill heart" title="Hati">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M12 21s-7-4.6-9.2-9.1C1.4 8.9 3.4 6 6.6 6c1.8 0 3.1 1 3.9 2 0.8-1 2.1-2 3.9-2 3.2 0 5.2 2.9 3.8 5.9C19 16.4 12 21 12 21Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                </svg>
                <span id="heartsText">{{ (int)($player->hearts ?? 0) }}/{{ (int)($player->hearts_max ?? 5) }}</span>
            </div>
        </div>
    </div>

    {{-- GAME CARD --}}
    <div class="card" id="gameCard">
        <div class="card-head">
            <div class="qmeta" id="qMeta">Soal 1</div>
            <div class="progress" id="qProgress">0/5</div>
        </div>

        <div class="card-body">
            <div class="qtext" id="qText"></div>

            <div class="qimg" id="qImg" style="display:none;">
                <img id="qImgEl" src="" alt="Gambar soal">
            </div>

            <div class="options" id="qOptions" style="display:none;"></div>

            <div class="fill" id="qFill" style="display:none;">
                <div>
                    <label for="fillInput">Jawaban</label>
                    <input id="fillInput" type="text" maxlength="50" autocomplete="off" autocapitalize="none" spellcheck="false">
                    <div class="hint" id="fillHint">Maks 3 huruf (harus pas)</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="feedback" id="feedback"></div>

            <div style="display:flex;gap:10px;align-items:center;">
                <button type="button" class="btn primary" id="btnCheck" disabled>Periksa</button>
                <button type="button" class="btn" id="btnNext" style="display:none;">Soal Berikutnya</button>
            </div>
        </div>
    </div>

</div>
{{-- ✅ BOTTOM FOOTER IMAGE (BACKGROUND) --}}
<img
    src="{{ asset('images/icon/footer.JPEG') }}"
    alt="Lentara Footer Bottom"
    class="bg-footer bottom"
    loading="lazy"
/>
{{-- MODAL ROUTE ERROR --}}
<div class="modal-wrap" id="modalRouteErr" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true">
        <div class="modal-inner">
            <div class="modal-title">Sistem belum siap</div>
            <div class="modal-sub" id="routeErrText">
                Endpoint cek jawaban belum tersedia / tidak valid.
            </div>
            <div class="modal-actions" style="justify-content:flex-end;">
                <a class="btn primary" href="{{ route('game.learn') }}">Kembali</a>
            </div>
        </div>
    </div>
</div>

{{-- MODAL HATI HABIS --}}
<div class="modal-wrap" id="modalHearts" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true">
        <div class="modal-inner">
            <div class="modal-title">Hati kamu habis</div>
            <div class="modal-sub">
                Kamu tidak bisa lanjut bermain sekarang.<br>
                Tunggu hati terisi kembali atau isi ulang dengan 10 uang.
            </div>

            <div id="heartsModalMsg" class="modal-sub" style="display:none;"></div>

            <div class="modal-actions" style="justify-content: space-between;">
                <a class="btn" href="{{ route('game.learn') }}">Kembali</a>
                <button type="button" class="btn primary" id="btnRefill">Isi Ulang Hati (10 Uang)</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL WAKTU HABIS --}}
<div class="modal-wrap" id="modalTime" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true">
        <div class="modal-inner">
            <div class="modal-title">Waktu habis</div>
            <div class="modal-sub">Waktu pengerjaan habis.</div>
            <div class="modal-actions" style="justify-content: space-between;">
                <a class="btn primary" href="{{ route('game.play', $level->id) }}">Coba Lagi</a>
                <a class="btn" href="{{ route('game.learn') }}">Kembali</a>
            </div>
        </div>
    </div>
</div>

{{-- ✅ MODAL SUMMARY --}}
<div class="modal-wrap" id="modalSummary" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true">
        <div class="modal-inner">

            <div class="result-head">
                <h2 id="sumCongrats">Semangat {{ $nickname }}</h2>
            </div>

            <div class="mascot-wrap" aria-hidden="true">
                <div class="mascot-avatar" title="Avatar">
                    <img src="{{ $avatarUrl }}" alt="Avatar {{ $nickname }}">
                </div>
            </div>

            <div class="result-message">
                <div class="result-message-line1" id="sumMsg1"></div>
                <div class="result-message-line2" id="sumMsg2"></div>
            </div>

            <div class="result-stats">
                <div class="rstat">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="rbadge" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M13 2s2 3 2 6-2 4-2 4 4-1 4-6 2-4 2-4-1 7-4 10-1 8-1 8-6-3-6-8 4-10 5-10Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <div class="k">XP</div>
                            <div class="v" id="sumXp">0</div>
                        </div>
                    </div>
                </div>

                <div class="rstat">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="rbadge" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M12 21s-7-4.6-9.2-9.1C1.4 8.9 3.4 6 6.6 6c1.8 0 3.1 1 3.9 2 0.8-1 2.1-2 3.9-2 3.2 0 5.2 2.9 3.8 5.9C19 16.4 12 21 12 21Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <div class="k">Salah</div>
                            <div class="v" id="sumWrong">0</div>
                        </div>
                    </div>
                </div>

                <div class="rstat">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="rbadge" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M20 7l-9 9-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <div>
                            <div class="k">Akurasi</div>
                            <div class="v" id="sumAcc">0%</div>
                        </div>
                    </div>
                </div>

                <div class="rstat">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="rbadge" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M12 8v5l3 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 22a10 10 0 1 0-10-10 10 10 0 0 0 10 10Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <div>
                            <div class="k">Waktu</div>
                            <div class="v" id="sumTime">00:00</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-actions" id="sumActions" style="justify-content: space-between;">
                {{-- injected by JS --}}
            </div>
        </div>
    </div>
</div>

{{-- ✅ MODAL KONFIRMASI KELUAR --}}
<div class="modal-wrap" id="modalExit" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true">
        <div class="modal-inner">
            <div class="modal-title">Yakin mau keluar?</div>
            <div class="modal-sub">
                Progres soal yang sedang berjalan bisa hilang kalau kamu keluar sekarang.
            </div>

            <div class="modal-actions" style="justify-content: space-between;">
                <button type="button" class="btn primary" id="btnExitStay">Lanjut Belajar</button>
                <a class="btn" id="btnExitGoHome" href="{{ route('game.learn') }}">Keluar</a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function(){
    const QUESTIONS = @json($payload);
    const TOTAL_Q = QUESTIONS.length;

    // ✅ tetap ada limit, tapi UI timer COUNT UP (mulai 0 naik)
    const TIME_LIMIT = {{ (int)$timeLimitSec }};

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "{{ csrf_token() }}";

    const checkUrl = @json($checkUrl);
    const refillUrl = @json($refillUrl);

    const modalExit   = document.getElementById('modalExit');
const btnExit     = document.getElementById('btnExit');
const btnExitStay = document.getElementById('btnExitStay');

    const routeErrModal = document.getElementById('modalRouteErr');
    const routeErrText  = document.getElementById('routeErrText');

    let idx = 0;
    let selected = null;
    let typed = "";
    let locked = false;
    let answered = 0;
    let correctCount = 0;
    let wrongCount = 0;

    let runXp = 0;
    let xpTotal = {{ (int)($player->xp_total ?? 0) }};
    let coinsTotal = {{ (int)($player->coins ?? 0) }};
    let heartsNow  = {{ (int)($player->hearts ?? 0) }};
    const heartsMax = {{ (int)($player->hearts_max ?? 5) }};

    const startedAt = Date.now();
    let timerTick = null;
    let timeOver = false;

    const elApp    = document.getElementById('quizApp');
    const sfxOkUrl = elApp?.getAttribute('data-sfx-correct') || "";
    const sfxNgUrl = elApp?.getAttribute('data-sfx-wrong') || "";

    // pre-create audio (best effort; browser will allow after user click)
    const sfxOk = sfxOkUrl ? new Audio(sfxOkUrl) : null;
    const sfxNg = sfxNgUrl ? new Audio(sfxNgUrl) : null;
    if(sfxOk) sfxOk.preload = "auto";
    if(sfxNg) sfxNg.preload = "auto";

    function playSfx(which){
        try{
            const a = (which === 'ok') ? sfxOk : sfxNg;
            if(!a) return;
            a.pause();
            a.currentTime = 0;
            const p = a.play();
            if(p && typeof p.catch === 'function') p.catch(()=>{});
        }catch(e){}
    }

    const elTime   = document.getElementById('timeText');
    const elXp     = document.getElementById('xpText');
    const elCoins  = document.getElementById('coinsText');
    const elHearts = document.getElementById('heartsText');

    const elSoalText = document.getElementById('soalText');
    const elSoalBar  = document.getElementById('soalBar');

    const elMeta = document.getElementById('qMeta');
    const elProg = document.getElementById('qProgress');
    const elText = document.getElementById('qText');

    const elImgWrap = document.getElementById('qImg');
    const elImg     = document.getElementById('qImgEl');

    const elOpts    = document.getElementById('qOptions');
    const elFill    = document.getElementById('qFill');
    const elFillIn  = document.getElementById('fillInput');
    const elFillHint= document.getElementById('fillHint');

    const elFeedback= document.getElementById('feedback');
    const btnCheck  = document.getElementById('btnCheck');
    const btnNext   = document.getElementById('btnNext');

    const modalHearts  = document.getElementById('modalHearts');
    const modalTime    = document.getElementById('modalTime');
    const modalSummary = document.getElementById('modalSummary');

    const btnRefill = document.getElementById('btnRefill');
    const heartsMsg = document.getElementById('heartsModalMsg');

function openExitModal(){
    if(modalExit){
        modalExit.classList.add('is-open');
        modalExit.setAttribute('aria-hidden','false');
    }
}

function closeExitModal(){
    if(modalExit){
        modalExit.classList.remove('is-open');
        modalExit.setAttribute('aria-hidden','true');
    }
}
if(btnExit){
    btnExit.addEventListener('click', function(){
        // kalau modal lain sedang terbuka, biarkan normal saja
        openExitModal();
    });
}

if(btnExitStay){
    btnExitStay.addEventListener('click', function(){
        closeExitModal();
        // lanjut main: tidak reset apa-apa
    });
}
if(modalExit){
    modalExit.addEventListener('click', function(e){
        if(e.target === modalExit) closeExitModal();
    });
}


    function mmss(sec){
        sec = Math.max(0, sec|0);
        const m = Math.floor(sec/60);
        const s = sec%60;
        return String(m).padStart(2,'0')+':'+String(s).padStart(2,'0');
    }
    function elapsedSec(){
        return Math.floor((Date.now() - startedAt)/1000);
    }
    function escapeHtml(str){
        return String(str ?? '')
            .replaceAll('&','&amp;')
            .replaceAll('<','&lt;')
            .replaceAll('>','&gt;')
            .replaceAll('"','&quot;')
            .replaceAll("'","&#039;");
    }

    function openRouteErr(msg){
        if(routeErrText) routeErrText.innerHTML = msg || 'Sistem belum siap.';
        if(routeErrModal){
            routeErrModal.classList.add('is-open');
            routeErrModal.setAttribute('aria-hidden','false');
        }
    }

    function updateTotalsUI(){
        if(elXp) elXp.textContent = String(xpTotal);
        if(elCoins) elCoins.textContent = String(coinsTotal);
        if(elHearts) elHearts.textContent = String(heartsNow) + '/' + String(heartsMax);
    }

    function updateTopSoal(){
        const shown = Math.min(TOTAL_Q, answered);
        if(elSoalText) elSoalText.textContent = `${shown}/${TOTAL_Q}`;
        if(elSoalBar){
            const pct = TOTAL_Q > 0 ? Math.round((shown / TOTAL_Q) * 100) : 0;
            elSoalBar.style.width = pct + '%';
        }
    }

    function showFeedbackOk(text){
        elFeedback.style.display = 'flex';
        elFeedback.classList.remove('err');
        elFeedback.classList.add('ok');
        elFeedback.textContent = text;
    }
    function showFeedbackErr(text){
        elFeedback.style.display = 'flex';
        elFeedback.classList.remove('ok');
        elFeedback.classList.add('err');
        elFeedback.textContent = text;
    }

    function enableCheckIfReady(q){
        if(locked || timeOver) {
            btnCheck.disabled = true;
            return;
        }
        if(q.type === 'mcq'){
            btnCheck.disabled = !(selected && ['A','B','C','D'].includes(selected));
        }else{
            const max = q.fillMax || 1;
            btnCheck.disabled = !(typed && typed.length === max);
        }
    }

    function render(){
        const q = QUESTIONS[idx];
        if(!q) return;

        selected = null;
        typed = "";
        locked = false;

        elMeta.textContent = 'Soal ' + (idx+1);
        elProg.textContent = answered + '/' + TOTAL_Q;
        elText.textContent = q.text || '';

        if(q.image){
            elImgWrap.style.display = '';
            elImg.src = q.image;
        }else{
            elImgWrap.style.display = 'none';
            elImg.src = '';
        }

        elFeedback.style.display = 'none';
        elFeedback.className = 'feedback';
        elFeedback.textContent = '';
        btnNext.style.display = 'none';
        btnCheck.style.display = '';
        btnCheck.textContent = 'Periksa';

        if(q.type === 'mcq'){
            elFill.style.display = 'none';
            elOpts.style.display = '';

            elOpts.innerHTML = '';
            const keys = ['A','B','C','D'];
            keys.forEach(k => {
                const v = (q.options && q.options[k]) ? q.options[k] : '';
                const div = document.createElement('div');
                div.className = 'opt';
                div.setAttribute('data-k', k);

                div.innerHTML = `
                    <div class="badge">${k}</div>
                    <div class="label">${escapeHtml(v)}</div>
                `;

                div.addEventListener('click', function(){
                    if(locked || timeOver) return;
                    Array.from(elOpts.querySelectorAll('.opt')).forEach(x => x.classList.remove('is-selected'));
                    div.classList.add('is-selected');
                    selected = k;
                    enableCheckIfReady(q);
                });

                elOpts.appendChild(div);
            });

        }else{
            elOpts.style.display = 'none';
            elFill.style.display = '';

            const max = q.fillMax || 1;
            elFillIn.value = '';
            elFillIn.maxLength = max;
            elFillHint.textContent = 'Maks ' + max + ' huruf (harus pas)';

            // ✅ guard Enter supaya tidak ada submit "nyasar"
            elFillIn.onkeydown = function(e){
                if(e.key === 'Enter'){
                    e.preventDefault();
                    if(!btnCheck.disabled) doCheck();
                }
            };

            try { elFillIn.focus({preventScroll:true}); } catch(e){}

            elFillIn.oninput = function(){
                if(locked || timeOver) return;
                typed = (elFillIn.value || '').replace(/\s+/g,'');
                elFillIn.value = typed;
                enableCheckIfReady(q);
            };
        }

        enableCheckIfReady(q);
    }

    async function safeJson(res){
        const ct = (res.headers.get('content-type') || '').toLowerCase();
        if(ct.includes('application/json')){
            try { return await res.json(); } catch(e){ return null; }
        }
        try { await res.text(); } catch(e){}
        return null;
    }

    async function doCheck(){
        const q = QUESTIONS[idx];
        if(!q || locked || timeOver) return;

        if(!checkUrl || typeof checkUrl !== 'string' || !checkUrl.includes('/check')){
            openRouteErr('URL cek jawaban tidak valid. Harus mengarah ke <b>/belajar/level/{level}/check</b>.');
            return;
        }

        enableCheckIfReady(q);
        if(btnCheck.disabled) return;

        locked = true;
        btnCheck.disabled = true;

        const ans = (q.type === 'mcq') ? selected : typed;

        try{
            const res = await fetch(checkUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    question_id: q.id,
                    type: q.type,
                    answer: ans,
                })
            });

            const json = await safeJson(res);

            if(!res.ok || !json || !json.ok){
                if(json && json.code === 'HEARTS_EMPTY'){
                    openHeartsModal(json.message || 'Hati kamu habis.');
                    return;
                }
                showFeedbackErr((json && json.message) ? json.message : 'Terjadi kesalahan.');
                btnNext.style.display = '';
                btnNext.textContent = 'Lanjut';
                locked = false;
                enableCheckIfReady(q);
                return;
            }

            // update totals dari server jika ada
            if(typeof json.hearts === 'number') heartsNow = json.hearts;
            if(typeof json.coins === 'number') coinsTotal = json.coins;
            if(typeof json.xp_total === 'number') xpTotal = json.xp_total;

            // progress/stats
            answered = (typeof json.answered_count === 'number') ? json.answered_count : (answered + 1);
            if(typeof json.correct_count === 'number') correctCount = json.correct_count;
            if(typeof json.wrong_count === 'number') wrongCount = json.wrong_count;

            if(typeof json.xp_gained === 'number'){
                runXp += json.xp_gained;
                if(typeof json.xp_total !== 'number') xpTotal += json.xp_gained;
            }

            if(typeof json.correct_count !== 'number' && typeof json.correct === 'boolean'){
                if(json.correct) correctCount += 1;
                else wrongCount += 1;
            }

            updateTotalsUI();

            if(json.correct === true){
                playSfx('ok');
                showFeedbackOk('Benar! +' + (json.xp_gained || 0) + ' XP');
            }else{
                playSfx('ng');
                showFeedbackErr('Salah! Hati berkurang');
                const heartPill = elHearts && elHearts.closest('.pill');
                if(heartPill){
                    heartPill.classList.add('shake');
                    setTimeout(()=> heartPill.classList.remove('shake'), 400);
                }
            }

            elProg.textContent = answered + '/' + TOTAL_Q;
            updateTopSoal();

            if(json.out_of_hearts){
                openHeartsModal(json.message || 'Hati kamu habis.');
                return;
            }

            if(json.finished){
                const acc = TOTAL_Q > 0 ? Math.round((correctCount / TOTAL_Q) * 100) : 0;
                openSummaryModal({
                    xp: runXp,
                    acc: acc,
                    time: mmss(elapsedSec()),
                    passed: !!json.passed
                });
                return;
            }

            btnNext.style.display = '';
            btnNext.textContent = 'Soal Berikutnya';

        }catch(err){
            showFeedbackErr('Terjadi kesalahan koneksi.');
            btnNext.style.display = '';
            btnNext.textContent = 'Lanjut';
            locked = false;
            enableCheckIfReady(q);
        }
    }

    function next(){
        if(timeOver) return;

        idx += 1;
        if(idx >= TOTAL_Q){
            const acc = TOTAL_Q > 0 ? Math.round((correctCount / TOTAL_Q) * 100) : 0;
            openSummaryModal({
                xp: runXp,
                acc: acc,
                time: mmss(elapsedSec()),
                passed: (correctCount >= 3)
            });
            return;
        }
        render();
    }

    function openHeartsModal(msg){
        if(modalHearts){
            modalHearts.classList.add('is-open');
            modalHearts.setAttribute('aria-hidden','false');
        }
        if(heartsMsg){
            heartsMsg.style.display = msg ? 'block' : 'none';
            heartsMsg.textContent = msg || '';
        }
        locked = true;
        btnCheck.disabled = true;
        btnNext.disabled = true;
    }

    async function refill(){
        if(!btnRefill) return;

        if(!refillUrl){
            if(heartsMsg){
                heartsMsg.style.display = 'block';
                heartsMsg.textContent = 'URL refill tidak valid.';
            }
            return;
        }

        btnRefill.disabled = true;
        btnRefill.style.opacity = '.7';

        try{
            const res = await fetch(refillUrl, {
                method: "POST",
                headers: {
                    "Content-Type":"application/json",
                    "X-CSRF-TOKEN": csrf,
                    "Accept":"application/json",
                },
                body: JSON.stringify({})
            });

            const json = await safeJson(res);

            if(!res.ok || !json || !json.ok){
                if(heartsMsg){
                    heartsMsg.style.display = 'block';
                    heartsMsg.textContent = (json && json.message) ? json.message : 'Uang tidak cukup / terjadi kesalahan.';
                }
                btnRefill.disabled = false;
                btnRefill.style.opacity = '1';
                return;
            }

            window.location.href = "{{ route('game.learn') }}";
        }catch(err){
            if(heartsMsg){
                heartsMsg.style.display = 'block';
                heartsMsg.textContent = 'Terjadi kesalahan. Coba lagi.';
            }
            btnRefill.disabled = false;
            btnRefill.style.opacity = '1';
        }
    }

    function openTimeModal(){
        timeOver = true;
        if(modalTime){
            modalTime.classList.add('is-open');
            modalTime.setAttribute('aria-hidden','false');
        }
        locked = true;
        btnCheck.disabled = true;
        btnNext.disabled = true;
    }

    function getResultMessage(accPercent, wrongCount){
        const acc = Math.max(0, Math.min(100, parseInt(accPercent || 0, 10)));
        const wrong = (typeof wrongCount === 'number') ? wrongCount : null;

        const isPerfect = (acc >= 100) && (wrong === 0 || wrong === null);

        if(isPerfect){
            return { line1: 'Kamu memperoleh nilai sempurna!', line2: 'Luar biasa, terus pertahankan' };
        }else if(acc >= 80){
            return { line1: 'Hasil yang sangat bagus!', line2: 'Sedikit lagi menuju sempurna' };
        }else if(acc >= 60){
            return { line1: 'Usaha yang bagus!', line2: 'Coba ulangi supaya makin mantap' };
        }else{
            return { line1: 'Jangan sedih, ini bagian dari proses belajar', line2: 'Coba lagi ya, kamu pasti bisa!' };
        }
    }

    function openSummaryModal(stat){
        if(timerTick) clearInterval(timerTick);

        const sumXp   = document.getElementById('sumXp');
        const sumAcc  = document.getElementById('sumAcc');
        const sumTime = document.getElementById('sumTime');
        const sumWrong= document.getElementById('sumWrong');

        if(sumXp)   sumXp.textContent = String(stat.xp ?? 0);
        if(sumAcc)  sumAcc.textContent = String(stat.acc ?? 0) + '%';
        if(sumTime) sumTime.textContent = String(stat.time ?? '00:00');
        if(sumWrong)sumWrong.textContent = String(wrongCount);

        const msg = getResultMessage(stat.acc, wrongCount);
        const elMsg1 = document.getElementById('sumMsg1');
        const elMsg2 = document.getElementById('sumMsg2');
        if(elMsg1) elMsg1.textContent = msg.line1;
        if(elMsg2) elMsg2.textContent = msg.line2;

        const congrats = document.getElementById('sumCongrats');
        if(congrats) congrats.textContent = "Selamat {{ $nickname }}";

        const actions = document.getElementById('sumActions');
        if(actions){
            if(stat.passed){
                actions.innerHTML = `
                    <a class="btn primary" href="{{ route('game.learn') }}">Klaim XP</a>
                    <a class="btn" href="{{ route('game.learn') }}">Kembali</a>
                `;
            }else{
                actions.innerHTML = `
                    <a class="btn primary" href="{{ route('game.play', $level->id) }}">Coba Lagi</a>
                    <a class="btn" href="{{ route('game.learn') }}">Kembali</a>
                `;
            }
        }

        if(modalSummary){
            modalSummary.classList.add('is-open');
            modalSummary.setAttribute('aria-hidden','false');
        }

        locked = true;
        btnCheck.disabled = true;
        btnNext.disabled = true;
    }

    // ✅ TIMER COUNT UP: mulai 00:00 naik
    // tetap ada limit: kalau elapsed >= TIME_LIMIT -> modal waktu habis
    function startTimer(){
        function tick(){
            const el = elapsedSec();
            if(elTime) elTime.textContent = mmss(el);

            if(TIME_LIMIT > 0 && el >= TIME_LIMIT){
                if(timerTick) clearInterval(timerTick);
                openTimeModal();
            }
        }
        tick();
        timerTick = setInterval(tick, 500);
    }

    // events
    btnCheck.addEventListener('click', doCheck);
    btnNext.addEventListener('click', next);
    if(btnRefill) btnRefill.addEventListener('click', refill);

    // init
    updateTotalsUI();

    if(TOTAL_Q !== 5){
        showFeedbackErr('Level ini belum siap (harus 5 soal).');
        btnCheck.disabled = true;
        updateTopSoal();
    }else{
        startTimer();
        updateTopSoal();
        render();
    }
})();
</script>
@endpush
