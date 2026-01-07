<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlayerProfileController extends Controller
{
    public function edit()
    {
        $player = Auth::guard('player')->user();
        return view('player.profile.edit', compact('player'));
    }

    public function update(Request $request)
    {
        $player = Auth::guard('player')->user();

        $data = $request->validate([
            'nickname'    => ['required','string','min:2','max:30'],
            'avatar_key'  => ['required','integer','in:1,2,3,4,5'],
        ]);

        $player->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
