<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameLevel;
use App\Models\GameQuestion;
use Illuminate\Http\Request;

class GameQuestionController extends Controller
{
    public function index(GameLevel $level)
    {
        $questions = $level->questions()->orderBy('order')->get();
        return view('admin.game-questions.index', compact('level','questions'));
    }

    public function store(Request $request, GameLevel $level)
    {
        $data = $request->validate([
            'type' => ['required','in:mcq,fill'],
            'question_text' => ['required','string'],
            'image_path' => ['nullable','string','max:255'],

            'option_a' => ['nullable','string','max:255'],
            'option_b' => ['nullable','string','max:255'],
            'option_c' => ['nullable','string','max:255'],
            'option_d' => ['nullable','string','max:255'],
            'correct_option' => ['nullable','in:A,B,C,D'],

            'correct_text' => ['nullable','string','max:50'],

            'order' => ['required','integer','min:1'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['game_level_id'] = $level->id;

        // aturan wajib berdasarkan type
        if ($data['type'] === 'mcq') {
            foreach (['option_a','option_b','option_c','option_d','correct_option'] as $k) {
                if (empty($data[$k])) {
                    return back()->with('error', 'MCQ wajib punya opsi A-D dan jawaban benar.');
                }
            }
            $data['correct_text'] = null;
        } else {
            if (empty($data['correct_text'])) {
                return back()->with('error', 'Soal isian wajib punya jawaban benar.');
            }
            $data['option_a'] = $data['option_b'] = $data['option_c'] = $data['option_d'] = null;
            $data['correct_option'] = null;
        }

        GameQuestion::create($data);

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function destroy(GameLevel $level, GameQuestion $question)
    {
        if ((int)$question->game_level_id !== (int)$level->id) abort(404);
        $question->delete();
        return back()->with('success', 'Soal dihapus.');
    }
}
