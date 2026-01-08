<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameLevel;
use App\Models\GameQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
// upload file dari input name="image"
'image' => ['nullable','image','max:2048'], // max 2MB
// optional fallback kalau kamu masih mau support path manual
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

        // kalau upload gambar, simpan ke storage/public dan set image_path = "storage/..."
if ($request->hasFile('image')) {
    $stored = $request->file('image')->store('game/questions', 'public');
    $data['image_path'] = 'storage/' . $stored;
}


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

    // ✅ NEW: edit page
    public function edit(GameLevel $level, GameQuestion $question)
    {
        if ((int)$question->game_level_id !== (int)$level->id) abort(404);

        return view('admin.game-questions.edit', compact('level', 'question'));
    }

    // ✅ NEW: update
    public function update(Request $request, GameLevel $level, GameQuestion $question)
    {
        if ((int)$question->game_level_id !== (int)$level->id) abort(404);

        $data = $request->validate([
            'type' => ['required','in:mcq,fill'],
            'question_text' => ['required','string'],

            'image' => ['nullable','image','max:2048'],
'image_path' => ['nullable','string','max:255'],
'remove_image' => ['nullable','boolean'],

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

        // hapus gambar jika user centang "remove_image"
if ($request->boolean('remove_image')) {
    $this->deletePublicImagePath($question->image_path);
    $data['image_path'] = null;
}

// kalau upload gambar baru, hapus gambar lama lalu set image_path baru
if ($request->hasFile('image')) {
    $this->deletePublicImagePath($question->image_path);

    $stored = $request->file('image')->store('game/questions', 'public');
    $data['image_path'] = 'storage/' . $stored;
}


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

        $question->update($data);

        return redirect()
            ->route('admin.game-levels.edit', $level->id)
            ->with('success', 'Soal berhasil diupdate.');
    }

    public function destroy(GameLevel $level, GameQuestion $question)
    {
        if ((int)$question->game_level_id !== (int)$level->id) abort(404);
        $question->delete();
        return back()->with('success', 'Soal dihapus.');
    }

    private function deletePublicImagePath(?string $imagePath): void
{
    if (!$imagePath) return;

    // kalau formatnya "storage/xxx", delete di disk public
    if (str_starts_with($imagePath, 'storage/')) {
        $relative = substr($imagePath, strlen('storage/'));
        Storage::disk('public')->delete($relative);
    }
}

}
