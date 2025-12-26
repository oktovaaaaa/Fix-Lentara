<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuizQuestionController extends Controller
{
    public function create(Quiz $quiz)
    {
        $quiz->load('island');

        return view('admin.quizzes.questions.create', compact('quiz'));
    }

    public function store(Request $request, Quiz $quiz)
    {
        // ===== VALIDASI DASAR =====
        $data = $request->validate([
            'order'        => ['required', 'integer', 'min:0'],
            'prompt_type'  => ['required', 'in:text,image'],
            'prompt_text'  => ['nullable', 'string'],
            'prompt_image' => ['nullable', 'image', 'max:2048'],
            'explanation'  => ['nullable', 'string'],

            'options'              => ['required', 'array', 'min:2', 'max:6'],
            'options.*.content_type'  => ['required', 'in:text,image'],
            'options.*.content_text'  => ['nullable', 'string'],
            'options.*.content_image' => ['nullable', 'image', 'max:2048'],
            'options.*.order'         => ['nullable', 'integer', 'min:0'],

            'correct_index' => ['required', 'integer', 'min:0'],
        ]);

        // ===== VALIDASI PROMPT =====
        if ($data['prompt_type'] === 'text') {
            $promptText = trim((string) ($data['prompt_text'] ?? ''));
            if ($promptText === '') {
                return back()
                    ->withErrors(['prompt_text' => 'Soal text wajib diisi.'])
                    ->withInput();
            }
        }

        if ($data['prompt_type'] === 'image') {
            if (!$request->hasFile('prompt_image')) {
                return back()
                    ->withErrors(['prompt_image' => 'Soal image wajib diupload.'])
                    ->withInput();
            }
        }

        // ===== NORMALISASI OPSI: hanya ambil opsi yang benar-benar terisi =====
        // (ini penting supaya opsi kosong tidak ikut masuk DB)
        $normalizedOptions = [];
        foreach ($data['options'] as $idx => $opt) {
            $type = $opt['content_type'] ?? 'text';

            if ($type === 'text') {
                $text = trim((string) ($opt['content_text'] ?? ''));
                if ($text === '') {
                    continue; // skip opsi kosong
                }

                $normalizedOptions[] = [
                    'orig_index'   => $idx,
                    'content_type' => 'text',
                    'content_text' => $text,
                    'order'        => isset($opt['order']) ? (int) $opt['order'] : count($normalizedOptions),
                ];
            } else {
                // image
                if (!$request->hasFile("options.$idx.content_image")) {
                    continue; // skip jika tipe image tapi tidak upload file
                }

                $normalizedOptions[] = [
                    'orig_index'   => $idx,
                    'content_type' => 'image',
                    'content_text' => null,
                    'order'        => isset($opt['order']) ? (int) $opt['order'] : count($normalizedOptions),
                ];
            }
        }

        // Minimal 2 opsi terisi
        if (count($normalizedOptions) < 2) {
            return back()
                ->withErrors(['options' => 'Minimal 2 opsi harus terisi (teks atau upload gambar).'])
                ->withInput();
        }

        // correct_index harus menunjuk opsi yang benar-benar terisi
        $correctIndex = (int) $data['correct_index'];
        $existsInNormalized = false;
        foreach ($normalizedOptions as $n) {
            if ((int) $n['orig_index'] === $correctIndex) {
                $existsInNormalized = true;
                break;
            }
        }
        if (!$existsInNormalized) {
            return back()
                ->withErrors(['correct_index' => 'Jawaban benar harus dipilih dari opsi yang terisi.'])
                ->withInput();
        }

        // ===== SIMPAN DALAM TRANSAKSI =====
        DB::transaction(function () use ($request, $quiz, $data, $normalizedOptions, $correctIndex) {

            // simpan prompt image jika perlu
            $promptImagePath = null;
            if ($data['prompt_type'] === 'image') {
                $promptImagePath = $request->file('prompt_image')
                    ->store('quizzes/prompts', 'public');
            }

            $question = QuizQuestion::create([
                'quiz_id'      => $quiz->id,
                'prompt_type'  => $data['prompt_type'],
                'prompt_text'  => $data['prompt_type'] === 'text' ? trim((string) $data['prompt_text']) : null,
                'prompt_image' => $promptImagePath,
                'order'        => (int) $data['order'],
                'explanation'  => isset($data['explanation']) ? trim((string) $data['explanation']) : null,
            ]);

            // simpan opsi
            foreach ($normalizedOptions as $pos => $opt) {

                $contentImagePath = null;
                if ($opt['content_type'] === 'image') {
                    $origIndex = (int) $opt['orig_index'];
                    $contentImagePath = $request->file("options.$origIndex.content_image")
                        ->store('quizzes/options', 'public');
                }

                QuizOption::create([
                    'quiz_question_id' => $question->id,
                    'content_type'     => $opt['content_type'],
                    'content_text'     => $opt['content_type'] === 'text' ? $opt['content_text'] : null,
                    'content_image'    => $contentImagePath,
                    'is_correct'       => ((int) $opt['orig_index'] === $correctIndex),
                    'order'            => (int) $opt['order'],
                ]);
            }
        });

        return redirect()
            ->route('admin.quizzes.edit', $quiz)
            ->with('success', 'Pertanyaan ditambahkan.');
    }

    public function destroy(Quiz $quiz, QuizQuestion $question)
    {
        // pastikan pertanyaan benar milik quiz ini
        abort_unless((int) $question->quiz_id === (int) $quiz->id, 404);

        // pastikan options ter-load supaya bisa delete gambar option
        $question->load('options');

        // hapus file prompt + option images
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
