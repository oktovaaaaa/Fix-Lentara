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

            'options'                 => ['required', 'array', 'min:2', 'max:6'],
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

        // ✅ prompt_type=image: gambar WAJIB (text opsional)
        if ($data['prompt_type'] === 'image') {
            if (!$request->hasFile('prompt_image')) {
                return back()
                    ->withErrors(['prompt_image' => 'Soal image wajib diupload.'])
                    ->withInput();
            }
        }

        // ===== NORMALISASI OPSI (skip yang kosong) =====
        $normalizedOptions = [];
        foreach ($data['options'] as $idx => $opt) {
            $type = $opt['content_type'] ?? 'text';

            if ($type === 'text') {
                $text = trim((string) ($opt['content_text'] ?? ''));
                if ($text === '') continue;

                $normalizedOptions[] = [
                    'orig_index'   => $idx,
                    'content_type' => 'text',
                    'content_text' => $text,
                ];
            } else {
                // image
                if (!$request->hasFile("options.$idx.content_image")) continue;

                $normalizedOptions[] = [
                    'orig_index'   => $idx,
                    'content_type' => 'image',
                    'content_text' => null,
                ];
            }
        }

        if (count($normalizedOptions) < 2) {
            return back()
                ->withErrors(['options' => 'Minimal 2 opsi harus terisi (teks atau upload gambar).'])
                ->withInput();
        }

        // correct_index harus menunjuk opsi terisi
        $correctIndex = (int) $data['correct_index'];
        $existsInNormalized = false;
        foreach ($normalizedOptions as $n) {
            if ((int) $n['orig_index'] === $correctIndex) { $existsInNormalized = true; break; }
        }
        if (!$existsInNormalized) {
            return back()
                ->withErrors(['correct_index' => 'Jawaban benar harus dipilih dari opsi yang terisi.'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $quiz, $data, $normalizedOptions, $correctIndex) {

            $promptImagePath = null;
            if ($data['prompt_type'] === 'image') {
                $promptImagePath = $request->file('prompt_image')
                    ->store('quizzes/prompts', 'public');
            }

            // ✅ prompt_text boleh disimpan walau prompt_type=image (opsional)
            $promptText = trim((string) ($data['prompt_text'] ?? ''));
            if ($promptText === '') $promptText = null;

            $question = QuizQuestion::create([
                'quiz_id'      => $quiz->id,
                'prompt_type'  => $data['prompt_type'],
                'prompt_text'  => $data['prompt_type'] === 'text' ? $promptText : $promptText, // tetap simpan jika ada
                'prompt_image' => $promptImagePath,
                'order'        => (int) $data['order'],
                'explanation'  => isset($data['explanation']) ? trim((string) $data['explanation']) : null,
            ]);

            // ✅ AUTO ORDER berurutan: 0..N-1 (aman dengan data lama & sorting)
            foreach (array_values($normalizedOptions) as $pos => $opt) {
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
                    'order'            => $pos,
                ]);
            }
        });

        return redirect()
            ->route('admin.quizzes.edit', $quiz)
            ->with('success', 'Pertanyaan ditambahkan.');
    }

    /**
     * ✅ NEW: halaman edit pertanyaan (terpisah)
     */
    public function edit(Quiz $quiz, QuizQuestion $question)
    {
        abort_unless((int) $question->quiz_id === (int) $quiz->id, 404);

        $quiz->load('island');
        $question->load(['options' => function ($q) {
            $q->orderBy('order')->orderBy('id');
        }]);

        return view('admin.quizzes.questions.edit', compact('quiz', 'question'));
    }

    /**
     * ✅ NEW: update pertanyaan + opsi
     * - prompt_type=image boleh punya prompt_text (opsional)
     * - auto order opsi berurutan (0..N-1)
     * - opsi image boleh pakai gambar lama (tidak wajib upload ulang)
     */
    public function update(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        abort_unless((int) $question->quiz_id === (int) $quiz->id, 404);

        $question->load('options');

        $data = $request->validate([
            'order'        => ['required', 'integer', 'min:0'],
            'prompt_type'  => ['required', 'in:text,image'],
            'prompt_text'  => ['nullable', 'string'],
            'prompt_image' => ['nullable', 'image', 'max:2048'],
            'remove_prompt_image' => ['nullable', 'in:1'],

            'explanation'  => ['nullable', 'string'],

            // kita pakai 4 slot default (ABCD) dari view, tapi tetap fleksibel
            'options'                    => ['required', 'array', 'min:2', 'max:6'],
            'options.*.id'               => ['nullable', 'integer'],
            'options.*.content_type'     => ['required', 'in:text,image'],
            'options.*.content_text'     => ['nullable', 'string'],
            'options.*.content_image'    => ['nullable', 'image', 'max:2048'],
            'options.*.remove_image'     => ['nullable', 'in:1'],

            'correct_index' => ['required', 'integer', 'min:0'],
        ]);

        // Map option existing by id
        $existingOptions = $question->options->keyBy('id');

        // ===== VALIDASI PROMPT =====
        $promptText = trim((string) ($data['prompt_text'] ?? ''));
        if ($promptText === '') $promptText = null;

        if ($data['prompt_type'] === 'text') {
            if (!$promptText) {
                return back()
                    ->withErrors(['prompt_text' => 'Soal text wajib diisi.'])
                    ->withInput();
            }
        }

        if ($data['prompt_type'] === 'image') {
            $hasExistingImage = !empty($question->prompt_image);
            $hasNewUpload = $request->hasFile('prompt_image');

            // kalau prompt image dihapus tapi tetap image tanpa upload baru => error
            $wantsRemove = $request->boolean('remove_prompt_image');
            if ($wantsRemove) $hasExistingImage = false;

            if (!$hasExistingImage && !$hasNewUpload) {
                return back()
                    ->withErrors(['prompt_image' => 'Soal image wajib ada (upload gambar atau pakai gambar lama).'])
                    ->withInput();
            }
        }

        // ===== NORMALISASI OPSI (support gambar lama) =====
        $normalizedOptions = [];
        foreach ($data['options'] as $idx => $opt) {
            $type = $opt['content_type'] ?? 'text';
            $optId = isset($opt['id']) && $opt['id'] ? (int) $opt['id'] : null;
            $existing = $optId ? ($existingOptions[$optId] ?? null) : null;

            if ($type === 'text') {
                $text = trim((string) ($opt['content_text'] ?? ''));
                if ($text === '') {
                    // kosong = skip (tidak disimpan)
                    continue;
                }

                $normalizedOptions[] = [
                    'orig_index'   => $idx,
                    'id'           => $optId,
                    'content_type' => 'text',
                    'content_text' => $text,
                    'keep_image'   => false,
                ];
            } else {
                // image
                $remove = isset($opt['remove_image']) && ((string)$opt['remove_image'] === '1');

                $hasNew = $request->hasFile("options.$idx.content_image");
                $hasOld = $existing && !empty($existing->content_image);

                if ($remove) $hasOld = false;

                if (!$hasNew && !$hasOld) {
                    // tipe image tapi tidak ada file baru dan tidak ada file lama => skip
                    continue;
                }

                $normalizedOptions[] = [
                    'orig_index'   => $idx,
                    'id'           => $optId,
                    'content_type' => 'image',
                    'content_text' => null,
                    'remove_image' => $remove,
                    'keep_image'   => $hasOld && !$hasNew && !$remove,
                ];
            }
        }

        if (count($normalizedOptions) < 2) {
            return back()
                ->withErrors(['options' => 'Minimal 2 opsi harus terisi (teks atau gambar).'])
                ->withInput();
        }

        // correct_index harus menunjuk opsi terisi
        $correctIndex = (int) $data['correct_index'];
        $existsInNormalized = false;
        foreach ($normalizedOptions as $n) {
            if ((int) $n['orig_index'] === $correctIndex) { $existsInNormalized = true; break; }
        }
        if (!$existsInNormalized) {
            return back()
                ->withErrors(['correct_index' => 'Jawaban benar harus dipilih dari opsi yang terisi.'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $data, $question, $existingOptions, $normalizedOptions, $correctIndex, $promptText) {

            // ===== UPDATE QUESTION =====
            $newPromptImagePath = $question->prompt_image;

            // remove prompt image if requested
            if ($request->boolean('remove_prompt_image') && $question->prompt_image) {
                Storage::disk('public')->delete($question->prompt_image);
                $newPromptImagePath = null;
            }

            // replace prompt image if uploaded
            if ($request->hasFile('prompt_image')) {
                if ($question->prompt_image) {
                    Storage::disk('public')->delete($question->prompt_image);
                }
                $newPromptImagePath = $request->file('prompt_image')->store('quizzes/prompts', 'public');
            }

            // kalau prompt_type = text, tidak butuh gambar => boleh kita hapus (biar bersih)
            if ($data['prompt_type'] === 'text' && $newPromptImagePath) {
                Storage::disk('public')->delete($newPromptImagePath);
                $newPromptImagePath = null;
            }

            $question->update([
                'order'        => (int) $data['order'],
                'prompt_type'  => $data['prompt_type'],
                'prompt_text'  => $promptText, // ✅ tetap simpan jika ada (image pun boleh)
                'prompt_image' => $newPromptImagePath,
                'explanation'  => isset($data['explanation']) ? trim((string) $data['explanation']) : null,
            ]);

            // ===== UPSERT OPTIONS =====
            $usedOptionIds = [];

            foreach (array_values($normalizedOptions) as $pos => $opt) {
                $optId = $opt['id'] ?? null;
                $existing = $optId ? ($existingOptions[$optId] ?? null) : null;

                $contentImagePath = $existing?->content_image;

                if ($opt['content_type'] === 'text') {
                    // jika sebelumnya image, hapus file lamanya
                    if ($existing && $existing->content_type === 'image' && $existing->content_image) {
                        Storage::disk('public')->delete($existing->content_image);
                    }
                    $contentImagePath = null;
                } else {
                    // image: handle remove / replace
                    $remove = !empty($opt['remove_image']);

                    if ($remove && $contentImagePath) {
                        Storage::disk('public')->delete($contentImagePath);
                        $contentImagePath = null;
                    }

                    if ($request->hasFile("options.{$opt['orig_index']}.content_image")) {
                        // replace old if exists
                        if ($contentImagePath) {
                            Storage::disk('public')->delete($contentImagePath);
                        }
                        $contentImagePath = $request->file("options.{$opt['orig_index']}.content_image")
                            ->store('quizzes/options', 'public');
                    }

                    // jika keep_image: biarkan path lama
                }

                $payload = [
                    'content_type'  => $opt['content_type'],
                    'content_text'  => $opt['content_type'] === 'text' ? $opt['content_text'] : null,
                    'content_image' => $contentImagePath,
                    'is_correct'    => ((int) $opt['orig_index'] === $correctIndex),
                    'order'         => $pos, // ✅ auto berurutan
                ];

                if ($existing) {
                    $existing->update($payload);
                    $usedOptionIds[] = (int) $existing->id;
                } else {
                    $created = QuizOption::create(array_merge($payload, [
                        'quiz_question_id' => $question->id,
                    ]));
                    $usedOptionIds[] = (int) $created->id;
                }
            }

            // Hapus opsi lama yang tidak dipakai lagi
            $toDelete = $question->options->filter(function ($o) use ($usedOptionIds) {
                return !in_array((int) $o->id, $usedOptionIds, true);
            });

            foreach ($toDelete as $o) {
                if ($o->content_image) {
                    Storage::disk('public')->delete($o->content_image);
                }
                $o->delete();
            }
        });

        return redirect()
            ->route('admin.quizzes.edit', $quiz)
            ->with('success', 'Pertanyaan diupdate.');
    }

    public function destroy(Quiz $quiz, QuizQuestion $question)
    {
        abort_unless((int) $question->quiz_id === (int) $quiz->id, 404);

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
