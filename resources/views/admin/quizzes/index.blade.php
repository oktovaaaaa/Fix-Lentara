@extends('layouts.admin')

@section('title', 'Admin - Quiz')
@section('page-title', 'Quiz')

@section('content')
<div class="max-w-6xl mx-auto">
    @if(session('success'))
        <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-bold">Daftar Quiz</h2>
            <p class="text-sm text-slate-500">Kelola quiz dan pertanyaannya.</p>
        </div>

        <a href="{{ route('admin.quizzes.create') }}"
           class="px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:opacity-90">
            + Buat Quiz
        </a>
    </div>

    <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left p-3">Judul</th>
                    <th class="text-left p-3">Status</th>
                    <th class="text-left p-3">Dibuat</th>
                    <th class="text-right p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quizzes as $q)
                    <tr class="border-t">
                        <td class="p-3 font-semibold">{{ $q->title }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $q->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $q->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-500">{{ $q->created_at->format('d/m/Y') }}</td>
                        <td class="p-3 text-right">
                            <a class="px-3 py-1 rounded-lg bg-amber-100 text-amber-800 font-bold"
                               href="{{ route('admin.quizzes.edit', $q) }}">
                                Edit
                            </a>
                            <form class="inline" method="POST" action="{{ route('admin.quizzes.destroy', $q) }}"
                                  onsubmit="return confirm('Hapus quiz ini?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 rounded-lg bg-red-100 text-red-700 font-bold">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-4 text-slate-500">Belum ada quiz.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $quizzes->links() }}</div>
</div>
@endsection
