{{-- resources/views/player/learn/play.blade.php (REPLACE FULL) --}}
@php
    $player = $player ?? (object)[
        'xp_total' => 0,
        'coins' => 0,
        'hearts' => 5,
        'hearts_max' => 5,
        'display_name' => 'Player',
    ];

    $islandColors = $islandColors ?? [];

    $levelTitle = $level->title ?? 'Level';

    $islandSlug = $level->island?->slug ?? '';
    $accent = $islandColors[$islandSlug] ?? '#f97316';

    // payload untuk JS (jangan tampilkan 5 card sekaligus)
    $payload = [];
    foreach($questions as $q){
        $payload[] = [
            'id' => (int)$q->id,
            'type' => (string)$q->type,
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

    // TIME LIMIT (kamu belum punya kolom di game_levels, jadi kita set default 8 menit)
    // kalau nanti mau dinamis, kita tambah kolom di migration game_levels (kamu belum minta sekarang).
    $timeLimitSec = 8 * 60;
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ request()->cookie('theme', 'dark') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $levelTitle }} — Belajar</title>

    <style>
        :root{
            --bg:#020617;
            --card: rgba(15,23,42,.76);
            --line: rgba(148,163,184,.20);
            --txt: rgba(226,232,240,.95);
            --muted: rgba(148,163,184,.86);

            --accent: {{ $accent }};
            --danger: #ef4444;
            --ok: #22c55e;

            --shadow: 0 26px 90px rgba(0,0,0,.40);
            --overlay: rgba(0,0,0,.58);

            --r-xl: 22px;
            --r-lg: 18px;
            --r-md: 14px;
        }

        html[data-theme="light"]{
            --bg:#f8fafc;
            --card: rgba(255,255,255,.82);
            --line: rgba(15,23,42,.14);
            --txt: rgba(15,23,42,.94);
            --muted: rgba(71,85,105,.86);
            --shadow: 0 24px 70px rgba(2,6,23,.12);
            --overlay: rgba(2,6,23,.32);
        }

        *{ box-sizing:border-box; }
        html,body{ height:100%; }
        body{
            margin:0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
            background: var(--bg);
            color: var(--txt);
            overflow-x:hidden;
        }

        .bg{
            position: fixed;
            inset:0;
            pointer-events:none;
            z-index:0;
            opacity:.9;
            background:
                radial-gradient(circle at 20% 10%, color-mix(in oklab, var(--accent) 25%, transparent), transparent 40%),
                radial-gradient(circle at 85% 15%, rgba(59,130,246,.12), transparent 45%),
                radial-gradient(circle at 15% 85%, rgba(34,197,94,.10), transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(250,204,21,.10), transparent 45%);
        }
        .bg::after{
            content:"";
            position:absolute;
            inset:0;
            opacity:.18;
            background-image: radial-gradient(rgba(148,163,184,.45) 1px, transparent 1px);
            background-size: 24px 24px;
            mask-image: radial-gradient(circle at 52% 42%, #000 0%, rgba(0,0,0,.75) 40%, transparent 72%);
        }

        .wrap{
            position: relative;
            z-index: 1;
            max-width: 980px;
            margin: 18px auto;
            padding: 0 16px 26px;
        }

        .topbar{
            border-radius: var(--r-xl);
            background: linear-gradient(180deg, color-mix(in oklab, var(--card) 92%, transparent), color-mix(in oklab, var(--card) 84%, transparent));
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            box-shadow: var(--shadow);
            padding: 14px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap: 12px;
        }

        .top-left{
            display:flex;
            align-items:center;
            gap: 12px;
            min-width:0;
        }
        .back{
            width: 44px;
            height: 44px;
            border-radius: 16px;
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: rgba(255,255,255,.03);
            color: var(--txt);
            display:grid;
            place-items:center;
            text-decoration:none;
            flex:0 0 auto;
        }
        .back svg{ width:20px;height:20px; }

        .title{
            display:grid;
            gap: 2px;
            min-width:0;
        }
        .title small{
            color: color-mix(in oklab, var(--muted) 92%, transparent);
            font-weight: 900;
        }
        .title h1{
            margin:0;
            font-size: 18px;
            font-weight: 950;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
            max-width: 520px;
        }

        .top-right{
            display:flex;
            align-items:center;
            gap: 10px;
            flex:0 0 auto;
        }

        .pill{
            display:inline-flex;
            align-items:center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 16px;
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: rgba(255,255,255,.03);
            font-weight: 950;
        }
        .pill svg{ width:18px;height:18px; }
        .pill.heart{ color: var(--danger); }
        .pill.xp{ color: #93c5fd; }
        .pill.time{ color: color-mix(in oklab, var(--accent) 70%, #ffffff 30%); }

        .card{
            margin-top: 14px;
            border-radius: var(--r-xl);
            background: linear-gradient(180deg, color-mix(in oklab, var(--card) 92%, transparent), color-mix(in oklab, var(--card) 84%, transparent));
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            box-shadow: var(--shadow);
            overflow:hidden;
        }

        .card-head{
            padding: 14px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap: 10px;
            border-bottom: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
        }
        .qmeta{
            font-weight: 950;
        }
        .progress{
            font-weight: 950;
            color: color-mix(in oklab, var(--muted) 92%, transparent);
        }

        .card-body{
            padding: 14px;
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
            max-width: 100%;
            border-radius: 16px;
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            display:block;
        }

        .options{
            display:grid;
            gap: 10px;
        }
        .opt{
            display:flex;
            align-items:center;
            gap: 10px;
            padding: 12px;
            border-radius: 16px;
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: rgba(255,255,255,.03);
            cursor: pointer;
            transition: transform .15s ease, border-color .15s ease, background .15s ease;
            user-select:none;
        }
        .opt:hover{
            transform: translateY(-1px);
            border-color: color-mix(in oklab, var(--accent) 55%, var(--line));
        }
        .opt .badge{
            width: 36px;
            height: 36px;
            border-radius: 14px;
            display:grid;
            place-items:center;
            font-weight: 950;
            background: rgba(255,255,255,.03);
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
        }
        .opt .label{
            font-weight: 900;
        }
        .opt.is-selected{
            border-color: color-mix(in oklab, var(--accent) 65%, transparent);
            box-shadow: inset 0 0 0 1px color-mix(in oklab, var(--accent) 30%, transparent);
            background: color-mix(in oklab, var(--accent) 10%, rgba(255,255,255,.03));
        }
        .opt.is-disabled{
            cursor: not-allowed;
            opacity: .7;
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
            border-radius: 16px;
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: rgba(255,255,255,.02);
            color: var(--txt);
            font-weight: 950;
            outline: none;
            letter-spacing: .5px;
        }
        .fill input:focus{
            border-color: color-mix(in oklab, var(--accent) 60%, transparent);
            box-shadow: 0 0 0 3px color-mix(in oklab, var(--accent) 18%, transparent);
        }
        .hint{
            font-size: 12px;
            font-weight: 900;
            color: color-mix(in oklab, var(--muted) 92%, transparent);
        }

        .footer{
            padding: 14px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap: 12px;
            border-top: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: rgba(255,255,255,.01);
        }

        .feedback{
            display:none;
            align-items:center;
            gap: 10px;
            font-weight: 950;
            padding: 10px 12px;
            border-radius: 16px;
            border: 1px solid transparent;
            background: rgba(255,255,255,.03);
        }
        .feedback.ok{
            display:flex;
            border-color: rgba(34,197,94,.35);
            background: rgba(34,197,94,.10);
            color: rgba(167,243,208,.95);
        }
        .feedback.err{
            display:flex;
            border-color: rgba(239,68,68,.35);
            background: rgba(239,68,68,.10);
            color: rgba(254,202,202,.95);
        }
        html[data-theme="light"] .feedback.ok{ color: rgba(6,95,70,.95); }
        html[data-theme="light"] .feedback.err{ color: rgba(127,29,29,.95); }

        .btn{
            border-radius: 16px;
            padding: 12px 14px;
            font-weight: 950;
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: rgba(255,255,255,.03);
            color: var(--txt);
            cursor: pointer;
            transition: transform .12s ease, filter .12s ease, opacity .12s ease;
        }
        .btn:hover{ transform: translateY(-1px); filter:saturate(1.05); }
        .btn:disabled{ opacity:.55; cursor:not-allowed; transform:none; }
        .btn.primary{
            background: var(--accent);
            border-color: color-mix(in oklab, var(--accent) 60%, transparent);
            color: #111;
        }
        html[data-theme="light"] .btn.primary{ color:#0b1220; }

        /* Heart shake animation when wrong */
        @keyframes shake {
            0% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
            100% { transform: translateX(0); }
        }
        .shake{ animation: shake .35s ease; }

        /* MODALS */
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
            background: linear-gradient(180deg, color-mix(in oklab, var(--card) 95%, transparent), color-mix(in oklab, rgba(15,23,42,.60) 92%, transparent));
            box-shadow: 0 30px 110px rgba(0,0,0,.55);
            overflow: hidden;
            position: relative;
        }
        .modal::before{
            content:"";
            position:absolute;
            inset:-2px;
            background: radial-gradient(420px 220px at 30% 15%, color-mix(in oklab, var(--modal-accent) 40%, transparent), transparent 62%);
            pointer-events:none;
            opacity:.9;
        }
        .modal-inner{ position:relative; z-index:1; padding: 16px; }
        .modal-title{ font-weight: 950; font-size: 18px; margin: 0 0 6px; }
        .modal-sub{ margin: 0 0 12px; color: color-mix(in oklab, var(--muted) 92%, transparent); font-weight: 850; line-height: 1.4; }
        .modal-actions{ display:flex; gap: 10px; justify-content: space-between; flex-wrap: wrap; margin-top: 10px; }

        .stat-grid{
            display:grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        .stat{
            border-radius: 18px;
            border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
            background: rgba(255,255,255,.03);
            padding: 12px;
            text-align:center;
            font-weight: 950;
        }
        .stat .k{ font-size: 12px; color: color-mix(in oklab, var(--muted) 92%, transparent); font-weight: 900; margin-bottom: 6px; }
        .stat .v{ font-size: 18px; }

        @media (max-width: 560px){
            .title h1{ max-width: 220px; }
            .stat-grid{ grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="bg" aria-hidden="true"></div>

<div class="wrap">

    <div class="topbar">
        <div class="top-left">
            <a class="back" href="{{ route('game.learn') }}" aria-label="Kembali">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>

            <div class="title">
                <small>{{ $level->island?->subtitle ?? $level->island?->name ?? 'Bagian' }}</small>
                <h1>{{ $levelTitle }}</h1>
            </div>
        </div>

        <div class="top-right">
            <div class="pill time" title="Waktu">
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

            <div class="pill heart" title="Hati">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M12 21s-7-4.6-9.2-9.1C1.4 8.9 3.4 6 6.6 6c1.8 0 3.1 1 3.9 2 0.8-1 2.1-2 3.9-2 3.2 0 5.2 2.9 3.8 5.9C19 16.4 12 21 12 21Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                </svg>
                <span id="heartsText">{{ (int)($player->hearts ?? 0) }}/{{ (int)($player->hearts_max ?? 5) }}</span>
            </div>
        </div>
    </div>

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
                    <input id="fillInput" type="text" maxlength="3" autocomplete="off" autocapitalize="none" spellcheck="false">
                    <div class="hint" id="fillHint">Maks 3 huruf (harus pas)</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="feedback" id="feedback"></div>

            <div style="display:flex;gap:10px;align-items:center;">
                <button class="btn primary" id="btnCheck" disabled>Periksa</button>
                <button class="btn" id="btnNext" style="display:none;">Soal Berikutnya</button>
            </div>
        </div>
    </div>

</div>

{{-- MODAL HATI HABIS --}}
<div class="modal-wrap" id="modalHearts" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" style="--modal-accent: var(--danger);">
        <div class="modal-inner">
            <div class="modal-title">Hati kamu habis</div>
            <div class="modal-sub">
                Kamu tidak bisa lanjut bermain sekarang.<br>
                Silahkan tunggu hati terisi kembali atau isi ulang dengan 10 uang.
            </div>

            <div id="heartsModalMsg" class="modal-sub" style="display:none;"></div>

            <div class="modal-actions">
                <a class="btn" href="{{ route('home') }}" style="text-decoration:none;">Kembali ke Beranda</a>
                <button class="btn primary" id="btnRefill">Isi Ulang Hati (10 Uang)</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL WAKTU HABIS (HANYA 1 TOMBOL KEMBALI KE BERANDA) --}}
<div class="modal-wrap" id="modalTime" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" style="--modal-accent: var(--accent);">
        <div class="modal-inner">
            <div class="modal-title">Waktu habis</div>
            <div class="modal-sub">
                Waktu pengerjaan habis. Silahkan kembali ke beranda.
            </div>
            <div class="modal-actions" style="justify-content:flex-end;">
                <a class="btn primary" href="{{ route('home') }}" style="text-decoration:none;">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</div>

{{-- MODAL SUMMARY --}}
<div class="modal-wrap" id="modalSummary" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" style="--modal-accent: var(--ok);">
        <div class="modal-inner">
            <div class="modal-title" id="sumTitle">Selesai!</div>
            <div class="modal-sub" id="sumSub">Ringkasan hasil permainan.</div>

            <div class="stat-grid">
                <div class="stat">
                    <div class="k">TOTAL XP</div>
                    <div class="v" id="sumXp">0</div>
                </div>
                <div class="stat">
                    <div class="k">AKURASI</div>
                    <div class="v" id="sumAcc">0%</div>
                </div>
                <div class="stat">
                    <div class="k">WAKTU</div>
                    <div class="v" id="sumTime">00:00</div>
                </div>
            </div>

            <div class="modal-actions" style="justify-content:flex-end;">
                <a class="btn primary" id="btnClaim" href="{{ route('game.learn') }}" style="text-decoration:none;">KLAIM XP</a>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    const QUESTIONS = @json($payload);
    const TOTAL_Q = QUESTIONS.length;
    const TIME_LIMIT = {{ (int)$timeLimitSec }}; // detik

    const csrf = "{{ csrf_token() }}";
    const checkUrl = "{{ route('game.check', $level->id) }}";
    const refillUrl = "{{ route('game.hearts.refill') }}";

    let idx = 0;
    let selected = null;
    let typed = "";
    let locked = false; // after check, before next
    let answered = 0;
    let correctCount = 0;
    let wrongCount = 0;
    let runXp = 0;

    // timer
    const startedAt = Date.now();
    let timerTick = null;
    let timeOver = false;

    const elTime   = document.getElementById('timeText');
    const elXp     = document.getElementById('xpText');
    const elHearts = document.getElementById('heartsText');

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

    const card = document.getElementById('gameCard');

    const modalHearts = document.getElementById('modalHearts');
    const modalTime   = document.getElementById('modalTime');
    const modalSummary= document.getElementById('modalSummary');

    const btnRefill = document.getElementById('btnRefill');
    const heartsMsg = document.getElementById('heartsModalMsg');

    function mmss(sec){
        sec = Math.max(0, sec|0);
        const m = Math.floor(sec/60);
        const s = sec%60;
        return String(m).padStart(2,'0')+':'+String(s).padStart(2,'0');
    }

    function elapsedSec(){
        return Math.floor((Date.now() - startedAt)/1000);
    }

    function remainingSec(){
        return TIME_LIMIT - elapsedSec();
    }

    function setFeedback(type, text){
        elFeedback.className = 'feedback ' + (type === 'ok' ? 'ok' : 'err');
        elFeedback.textContent = text;
    }

    function clearFeedback(){
        elFeedback.className = 'feedback';
        elFeedback.textContent = '';
        elFeedback.style.display = 'none';
        // class toggled by css, but keep consistent:
        elFeedback.classList.remove('ok','err');
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

        // header
        elMeta.textContent = 'Soal ' + (idx+1);
        elProg.textContent = answered + '/' + TOTAL_Q;

        // text
        elText.textContent = q.text || '';

        // image
        if(q.image){
            elImgWrap.style.display = '';
            elImg.src = q.image;
        }else{
            elImgWrap.style.display = 'none';
            elImg.src = '';
        }

        // reset feedback/buttons
        elFeedback.style.display = 'none';
        elFeedback.className = 'feedback';
        elFeedback.textContent = '';
        btnNext.style.display = 'none';
        btnCheck.style.display = '';
        btnCheck.textContent = 'Periksa';

        // content type
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
                    document.querySelectorAll('.opt').forEach(x => x.classList.remove('is-selected'));
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
            elFillIn.focus({preventScroll:true});

            elFillIn.oninput = function(){
                if(locked || timeOver) return;
                // trim spasi, tapi tidak auto lower (biar user bebas; server case-insensitive)
                typed = (elFillIn.value || '').replace(/\s+/g,'');
                elFillIn.value = typed;
                enableCheckIfReady(q);
            };
        }

        enableCheckIfReady(q);
    }

    function escapeHtml(str){
        return String(str ?? '')
            .replaceAll('&','&amp;')
            .replaceAll('<','&lt;')
            .replaceAll('>','&gt;')
            .replaceAll('"','&quot;')
            .replaceAll("'","&#039;");
    }

    async function doCheck(){
        const q = QUESTIONS[idx];
        if(!q || locked || timeOver) return;

        enableCheckIfReady(q);
        if(btnCheck.disabled) return;

        locked = true;
        btnCheck.disabled = true;

        let ans = null;
        if(q.type === 'mcq'){
            ans = selected;
        }else{
            ans = typed;
        }

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

            const json = await res.json();

            if(!res.ok || !json.ok){
                // hati habis (403)
                if(json && json.code === 'HEARTS_EMPTY'){
                    openHeartsModal(json.message || 'Hati kamu habis.');
                    return;
                }
                showFeedbackErr(json.message || 'Terjadi kesalahan.');
                btnNext.style.display = '';
                btnNext.textContent = 'Lanjut';
                return;
            }

            // update UI values
            if(typeof json.xp_total === 'number'){
                elXp.textContent = String(json.xp_total);
            }
            if(typeof json.hearts === 'number'){
                elHearts.textContent = String(json.hearts) + '/' + String(json.hearts_max || {{ (int)($player->hearts_max ?? 5) }});
            }

            // update stats
            answered = json.answered_count || (answered + 1);

            correctCount = json.correct_count || correctCount;
            wrongCount   = json.wrong_count || wrongCount;

            // per soal xp
            if(typeof json.xp_gained === 'number'){
                runXp += json.xp_gained;
            }

            // feedback
            if(json.correct === true){
                showFeedbackOk('Benar! +' + (json.xp_gained || 0) + ' XP');
            }else{
                showFeedbackErr('Salah! Hati berkurang');
                // shake hearts pill
                elHearts.parentElement.classList.add('shake');
                setTimeout(()=> elHearts.parentElement.classList.remove('shake'), 400);
            }

            elProg.textContent = answered + '/' + TOTAL_Q;

            // kalau hati habis setelah jawab
            if(json.out_of_hearts){
                openHeartsModal('Hati kamu habis.');
                return;
            }

            // finished?
            if(json.finished){
                // tampilkan summary
                const acc = TOTAL_Q > 0 ? Math.round((correctCount / TOTAL_Q) * 100) : 0;
                openSummaryModal({
                    xp: runXp,
                    acc: acc,
                    time: mmss(elapsedSec()),
                    passed: !!json.passed
                });
                return;
            }

            // show next button
            btnNext.style.display = '';
            btnNext.textContent = 'Soal Berikutnya';

        }catch(err){
            showFeedbackErr('Terjadi kesalahan koneksi.');
            btnNext.style.display = '';
            btnNext.textContent = 'Lanjut';
        }
    }

    function next(){
        if(timeOver) return;
        idx += 1;
        if(idx >= TOTAL_Q){
            // should be handled by finished, but just in case:
            openSummaryModal({
                xp: runXp,
                acc: TOTAL_Q > 0 ? Math.round((correctCount / TOTAL_Q) * 100) : 0,
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
        btnNext.style.pointerEvents = 'none';
        btnCheck.style.pointerEvents = 'none';
        btnNext.style.opacity = '.6';
        btnCheck.style.opacity = '.6';
    }

    async function refill(){
        if(!btnRefill) return;
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

            // reload ke map (atau lanjut main? kamu minta bisa lanjut, tapi hati habis harus stop)
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

    function openSummaryModal(stat){
        if(timerTick) clearInterval(timerTick);

        const sumXp = document.getElementById('sumXp');
        const sumAcc= document.getElementById('sumAcc');
        const sumTime=document.getElementById('sumTime');
        const sumTitle=document.getElementById('sumTitle');
        const sumSub=document.getElementById('sumSub');

        if(sumXp) sumXp.textContent = String(stat.xp ?? 0);
        if(sumAcc) sumAcc.textContent = String(stat.acc ?? 0) + '%';
        if(sumTime) sumTime.textContent = String(stat.time ?? '00:00');

        // kalau tidak lulus, tetap tampilkan, tapi judul beda
        if(sumTitle){
            sumTitle.textContent = stat.passed ? 'Selesai!' : 'Belum Lulus';
        }
        if(sumSub){
            sumSub.textContent = stat.passed
                ? 'Kamu menyelesaikan level ini.'
                : 'Kamu belum mencapai minimal 3 jawaban benar.';
        }

        if(modalSummary){
            modalSummary.classList.add('is-open');
            modalSummary.setAttribute('aria-hidden','false');
        }

        locked = true;
        btnCheck.disabled = true;
        btnNext.disabled = true;
    }

    // timer loop
    function startTimer(){
        function tick(){
            const rem = remainingSec();
            if(elTime){
                // kamu minta waktu pengerjaan selesai (elapsed), tapi juga minta time limit habis.
                // di atas kita tampilkan elapsed (mmss(elapsed)). Kalau time limit habis, modal time.
                elTime.textContent = mmss(elapsedSec());
            }
            if(rem <= 0){
                if(timerTick) clearInterval(timerTick);
                openTimeModal();
            }
        }
        tick();
        timerTick = setInterval(tick, 500);
    }

    // bind buttons
    btnCheck.addEventListener('click', doCheck);
    btnNext.addEventListener('click', next);

    if(btnRefill) btnRefill.addEventListener('click', refill);

    // init
    if(TOTAL_Q !== 5){
        // fallback hard stop
        showFeedbackErr('Level ini belum siap (harus 5 soal).');
        btnCheck.disabled = true;
    }else{
        startTimer();
        render();
    }
})();
</script>

</body>
</html>
