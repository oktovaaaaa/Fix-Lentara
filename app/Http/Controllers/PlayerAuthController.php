<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PlayerAuthController extends Controller
{
    public function showRegister()
    {
        return view('player.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => ['required','string','min:3','max:20','alpha_dash','unique:players,username'],
            'pin'      => ['required','digits:4'],
        ], [
            'username.alpha_dash' => 'Username hanya boleh huruf/angka/underscore/dash.',
            'pin.digits' => 'PIN harus 4 digit angka.',
        ]);

        $player = Player::create([
            'username'   => $data['username'],
            'pin_hash'   => Hash::make($data['pin']),
            'nickname'   => $data['username'],
            'avatar_key' => 1,
            'xp_total'   => 0,
            'coins'      => 100,
            'hearts'     => 5,
            'hearts_max' => 5,
        ]);

        Auth::guard('player')->login($player);
        $request->session()->regenerate();

        return redirect()->route('game.learn')->with('success', 'Akun berhasil dibuat. Selamat bermain!');
    }

    public function showLogin()
    {
        return view('player.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required','string'],
            'pin'      => ['required','digits:4'],
        ], [
            'pin.digits' => 'PIN harus 4 digit angka.',
        ]);

        $player = Player::where('username', $data['username'])->first();

        if (!$player || !Hash::check($data['pin'], $player->pin_hash)) {
            return back()->withErrors(['username' => 'Username atau PIN salah.'])->withInput();
        }

        Auth::guard('player')->login($player);
        $request->session()->regenerate();

        return redirect()->route('game.learn');
    }

    public function logout(Request $request)
    {
        Auth::guard('player')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
