@extends('layouts.admin')

@section('title', 'Admin - Edit Quiz')
@section('page-title', 'Edit Quiz')

@section('content')
<div class="max-w-6xl mx-auto">
    @if(session('success'))
        <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="bg-white rounded-xl shadow border p-5 lg:col-span-1">
            <h2 class="text-xl font-bold mb-1">Pengaturan Quiz</h2>
            <p class="text-sm text-slate-500 mb-4">Update judul & status.</p>

            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700">
                    <div class="font-bold mb-1">Gagal:</div>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}" class="space-y-3">
                @csrf @method('PUT')

                <div>
                    <label class="text-sm font-bold">Judul</label>
                    <input class="w-full border rounded-lg px-3 py-2" name="title"
                           value="{{ old('title', $quiz->title) }}" />
                </div>

                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $quiz->is_active) ? 'checked' : '' }}>
                    <span class="font-semibold">Aktif</span>
                </label>

                <button class="px-4 py-2 rounded-lg bg-slate-900 text-white font-bold hover:opacity-90">
                    Update
                </button>
            </form>

            <div class="mt-5">
                <a href="{{ route('admin.quiz-questions.create', $quiz) }}"
                   class="block text-center px-4 py-2 rounded-lg bg-amber-400 text-slate-900 font-extrabold hover:opacity-90">
                    + Tambah Pertanyaan
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-bold">Pertanyaan</h3>
                <span class="text-sm text-slate-500">{{ $quiz->questions->count() }} item</span>
            </div>

            <div class="space-y-3">
                @forelse($quiz->questions as $q)
                    <div class="border rounded-xl p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <div class="text-xs text-slate-500 mb-1">Order: {{ $q->order }}</div>
                                <div class="font-extrabold">
                                    @if($q->prompt_type === 'text')
                                        {{ $q->prompt_text }}
                                    @else
                                        <div class="font-bold mb-2">[Soal Gambar]</div>
                                        <img class="w-full max-w-sm rounded-lg border"
                                             src="{{ asset('storage/'.$q->prompt_image) }}" alt="prompt">
                                    @endif
                                </div>

                                <div class="mt-3 grid sm:grid-cols-2 gap-2">
                                    @foreach($q->options as $opt)
                                        <div class="p-3 rounded-lg border {{ $opt->is_correct ? 'border-green-400 bg-green-50' : 'border-slate-200' }}">
                                            <div class="text-xs font-bold mb-1">
                                                {{ $opt->is_correct ? '✅ Benar' : '• Opsi' }}
                                            </div>

                                            @if($opt->content_type === 'text')
                                                <div>{{ $opt->content_text }}</div>
                                            @else
                                                <img class="w-full rounded-lg border"
                                                     src="{{ asset('storage/'.$opt->content_image) }}" alt="option">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <form method="POST"
                                  action="{{ route('admin.quiz-questions.destroy', [$quiz, $q]) }}"
                                  onsubmit="return confirm('Hapus pertanyaan ini?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-2 rounded-lg bg-red-100 text-red-700 font-bold">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-slate-500">Belum ada pertanyaan. Klik “Tambah Pertanyaan”.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
