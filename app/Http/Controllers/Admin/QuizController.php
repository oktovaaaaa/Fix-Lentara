<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $islands = Island::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $q = Quiz::query()->with('island');

        $scope = $request->string('scope')->toString();
        if (in_array($scope, ['global', 'island', 'tribe'], true)) {
            $q->where('scope', $scope);
        }

        if ($request->filled('island_id')) {
            $q->where('island_id', (int) $request->input('island_id'));
        }

        if ($request->filled('tribe')) {
            $q->where('tribe', $request->input('tribe'));
        }

        $quizzes = $q->latest()->paginate(20);

        return view('admin.quizzes.index', [
            'quizzes' => $quizzes,
            'islands' => $islands,
            'tribesConfig' => config('tribes'),
        ]);
    }

    public function create()
    {
        $islands = Island::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('admin.quizzes.create', [
            'islands' => $islands,
            'tribesConfig' => config('tribes'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'scope' => ['required', 'in:global,island,tribe'],
            'island_id' => ['nullable', 'integer', 'exists:islands,id'],
            'tribe' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        // NORMALISASI
        if ($data['scope'] === 'global') {
            $data['island_id'] = null;
            $data['tribe'] = null;
        }

        if ($data['scope'] === 'island') {
            $data['tribe'] = null;
            if (empty($data['island_id'])) {
                return back()->withErrors(['island_id' => 'Pulau wajib dipilih untuk scope Pulau.'])->withInput();
            }
        }

        if ($data['scope'] === 'tribe') {
            if (empty($data['island_id'])) {
                return back()->withErrors(['island_id' => 'Pulau wajib dipilih untuk scope Suku.'])->withInput();
            }
            if (empty($data['tribe'])) {
                return back()->withErrors(['tribe' => 'Suku wajib dipilih untuk scope Suku.'])->withInput();
            }
        }

        $quiz = Quiz::create($data);

        // UX: langsung tambah pertanyaan
        return redirect()->route('admin.quiz-questions.create', $quiz)
            ->with('success', 'Quiz dibuat. Silakan tambah pertanyaan.');
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load(['island', 'questions.options']);

        return view('admin.quizzes.edit', [
            'quiz' => $quiz,
        ]);
    }

    public function update(Request $request, Quiz $quiz)
    {
        // KUNCI scope/island/tribe
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $quiz->update($data);

        return back()->with('success', 'Quiz diupdate.');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return back()->with('success', 'Quiz dihapus.');
    }
}
