<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameLevel;
use App\Models\Island;
use Illuminate\Http\Request;

class GameLevelController extends Controller
{
    public function index()
    {
        $islands = Island::orderBy('order')->get();
        $levels = GameLevel::with('island')
            ->orderBy('island_id')
            ->orderBy('order')
            ->get();

        return view('admin.game-levels.index', compact('islands','levels'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'island_id' => ['required','exists:islands,id'],
            'title'     => ['required','string','max:120'],
            'order'     => ['required','integer','min:1'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        GameLevel::create($data);

        return back()->with('success', 'Level berhasil dibuat.');
    }

    public function edit(GameLevel $gameLevel)
    {
        $islands = Island::orderBy('order')->get();
        return view('admin.game-levels.edit', compact('gameLevel','islands'));
    }

    public function update(Request $request, GameLevel $gameLevel)
    {
        $data = $request->validate([
            'island_id' => ['required','exists:islands,id'],
            'title'     => ['required','string','max:120'],
            'order'     => ['required','integer','min:1'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $gameLevel->update($data);

        return redirect()->route('admin.game-levels.index')->with('success', 'Level berhasil diupdate.');
    }

    public function destroy(GameLevel $gameLevel)
    {
        $gameLevel->delete();
        return back()->with('success', 'Level dihapus.');
    }
}
