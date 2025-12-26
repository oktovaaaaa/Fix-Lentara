<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::latest()->paginate(20);
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['is_active'] = (bool) $request->boolean('is_active');

        $quiz = Quiz::create($data);

        return redirect()->route('admin.quizzes.edit', $quiz)
            ->with('success', 'Quiz dibuat.');
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load(['questions.options']);
        return view('admin.quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['is_active'] = (bool) $request->boolean('is_active');

        $quiz->update($data);

        return back()->with('success', 'Quiz diperbarui.');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return back()->with('success', 'Quiz dihapus.');
    }
}
