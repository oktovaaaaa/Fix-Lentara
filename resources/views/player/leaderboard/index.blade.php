{{-- resources/views/player/leaderboard/index.blade.php --}}
@php
    $player = $player ?? (object)['display_name'=>'Player','xp_total'=>0];
    $rows = $rows ?? collect();
    $myRank = $myRank ?? 1;
@endphp

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Papan Peringkat — Galaxy Aksara</title>

    <style>
        :root{
            --bg:#020617;
            --card: rgba(15,23,42,.76);
            --line: rgba(148,163,184,.20);
            --txt: rgba(226,232,240,.95);
            --muted: rgba(148,163,184,.86);
            --brand:#f97316;
            --shadow: 0 26px 90px rgba(0,0,0,.40);
        }
        *{box-sizing:border-box;}
        body{margin:0;background:var(--bg);color:var(--txt);font-family: ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Arial;}
        .wrap{max-width:900px;margin:18px auto;padding:0 16px;}
        .top{
            border-radius:22px;background:var(--card);border:1px solid var(--line);box-shadow:var(--shadow);
            padding:14px;display:flex;justify-content:space-between;align-items:center;gap:12px;
        }
        .top a{color:#93c5fd;text-decoration:none;font-weight:900;}
        h1{margin:0;font-size:18px;font-weight:950;}
        .me{margin-top:12px;border-radius:22px;background:var(--card);border:1px solid var(--line);box-shadow:var(--shadow);padding:14px;}
        .me b{font-weight:950;}
        .list{margin-top:12px;border-radius:22px;background:var(--card);border:1px solid var(--line);box-shadow:var(--shadow);overflow:hidden;}
        .row{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px 14px;border-top:1px solid var(--line);}
        .row:first-child{border-top:none;}
        .left{display:flex;align-items:center;gap:10px;min-width:0;}
        .rank{width:38px;height:38px;border-radius:14px;display:grid;place-items:center;font-weight:950;background:rgba(255,255,255,.03);border:1px solid var(--line);}
        .name{font-weight:950;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:420px;}
        .xp{font-weight:950;color:#93c5fd;}
        .you{background:rgba(249,115,22,.10);}
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <a href="{{ route('game.learn') }}">← Kembali</a>
        <h1>Papan Peringkat</h1>
        <div style="opacity:.85;font-weight:900;">XP</div>
    </div>

    <div class="me">
        Halo, <b>{{ $player->display_name }}</b><br>
        Rank kamu: <b>#{{ (int)$myRank }}</b> • Total XP: <b>{{ (int)$player->xp_total }}</b>
    </div>

    <div class="list">
        @forelse($rows as $i => $r)
            @php
                $isMe = ((int)$r->id === (int)$player->id);
                $nm = $r->nickname ?: $r->username;
            @endphp
            <div class="row {{ $isMe ? 'you' : '' }}">
                <div class="left">
                    <div class="rank">{{ $i+1 }}</div>
                    <div class="name">{{ $nm }}</div>
                </div>
                <div class="xp">{{ (int)$r->xp_total }} XP</div>
            </div>
        @empty
            <div class="row">
                <div style="opacity:.85;font-weight:900;">Belum ada data.</div>
            </div>
        @endforelse
    </div>
</div>
</body>
</html>
