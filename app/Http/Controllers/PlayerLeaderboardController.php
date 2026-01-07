<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Support\Facades\Auth;

class PlayerLeaderboardController extends Controller
{
    public function index()
    {
        $player = Auth::guard('player')->user();

        // WAJIB include id, supaya $r->id ada di view
        $rows = Player::query()
            ->select(['id','username','nickname','avatar_key','xp_total'])
            ->orderByDesc('xp_total')
            ->orderBy('id')
            ->limit(50)
            ->get();

        // hitung rank player (simple, aman)
        $myRank = null;
        foreach ($rows as $i => $r) {
            if ((int)$r->id === (int)$player->id) {
                $myRank = $i + 1;
                break;
            }
        }

        return view('player.leaderboard.index', compact('rows', 'player', 'myRank'));
    }
}
