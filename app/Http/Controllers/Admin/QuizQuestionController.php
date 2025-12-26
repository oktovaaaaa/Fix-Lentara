<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class QuizQuestionController extends Controller
{
    public function create(Quiz $quiz)
    {
        return view('admin.quizzes.questions.create', compact('quiz'));
    }

    public function store(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'order' => ['nullable','integer','min:0'],
            'prompt_type' => ['required', Rule::in(['text','image'])],
            'prompt_text' => ['nullable','string'],
            'prompt_image' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'explanation' => ['nullable','string'],

            'options' => ['required','array','min:2','max:6'],
            'options.*.order' => ['nullable','integer','min:0'],
            'options.*.content_type' => ['required', Rule::in(['text','image'])],
            'options.*.content_text' => ['nullable','string'],
            'options.*.content_image' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],

            'correct_index' => ['required','integer','min:0'],
        ]);

        // Validasi prompt sesuai tipe
        if ($validated['prompt_type'] === 'text' && blank($request->prompt_text)) {
            return back()->withErrors(['prompt_text' => 'Soal teks wajib diisi.'])->withInput();
        }
        if ($validated['prompt_type'] === 'image' && !$request->hasFile('prompt_image')) {
            return back()->withErrors(['prompt_image' => 'Soal gambar wajib diupload.'])->withInput();
        }

        // Validasi opsi sesuai tipe
        foreach ($request->options as $i => $opt) {
            $type = $opt['content_type'] ?? 'text';
            if ($type === 'text' && blank($opt['content_text'] ?? null)) {
                return back()->withErrors(["options.$i.content_text" => "Opsi #".($i+1)." teks wajib diisi."])->withInput();
            }
            if ($type === 'image' && !$request->hasFile("options.$i.content_image")) {
                return back()->withErrors(["options.$i.content_image" => "Opsi #".($i+1)." gambar wajib diupload."])->withInput();
            }
        }

        $q = new QuizQuestion();
        $q->quiz_id = $quiz->id;
        $q->prompt_type = $validated['prompt_type'];
        $q->prompt_text = $validated['prompt_type'] === 'text' ? $request->prompt_text : null;
        $q->order = (int) ($validated['order'] ?? 0);
        $q->explanation = $request->explanation;

        if ($validated['prompt_type'] === 'image' && $request->hasFile('prompt_image')) {
            $q->prompt_image = $request->file('prompt_image')->store('quiz', 'public');
        }

        $q->save();

        $correctIndex = (int) $validated['correct_index'];

        foreach ($request->options as $i => $opt) {
            $o = new QuizOption();
            $o->quiz_question_id = $q->id;
            $o->content_type = $opt['content_type'];
            $o->order = (int) ($opt['order'] ?? $i);
            $o->is_correct = ($i === $correctIndex);

            if ($o->content_type === 'text') {
                $o->content_text = $opt['content_text'] ?? null;
            } else {
                if ($request->hasFile("options.$i.content_image")) {
                    $o->content_image = $request->file("options.$i.content_image")->store('quiz', 'public');
                }
            }

            $o->save();
        }

        return redirect()->route('admin.quizzes.edit', $quiz)
            ->with('success', 'Pertanyaan ditambahkan.');
    }

    public function destroy(Quiz $quiz, QuizQuestion $question)
    {
        abort_if($question->quiz_id !== $quiz->id, 404);

        $question->load('options');

        if ($question->prompt_image) {
            Storage::disk('public')->delete($question->prompt_image);
        }
        foreach ($question->options as $opt) {
            if ($opt->content_image) {
                Storage::disk('public')->delete($opt->content_image);
            }
        }

        $question->delete();

        return back()->with('success', 'Pertanyaan dihapus.');
    }
}
