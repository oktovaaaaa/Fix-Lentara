@extends('layouts.admin')

@section('title', 'Admin - Buat Quiz')
@section('page-title', 'Buat Quiz')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow border p-5">
        <h2 class="text-xl font-bold mb-1">Buat Quiz Baru</h2>
        <p class="text-sm text-slate-500 mb-4">Isi judul quiz dan status aktif.</p>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700">
                <div class="font-bold mb-1">Gagal:</div>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.quizzes.store') }}" class="space-y-3">
            @csrf

            <div>
                <label class="text-sm font-bold">Judul</label>
                <input class="w-full border rounded-lg px-3 py-2"
                       name="title" value="{{ old('title', 'Kuis Budaya Indonesia') }}" />
            </div>

            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                <span class="font-semibold">Aktifkan quiz</span>
            </label>

            <button class="px-4 py-2 rounded-lg bg-slate-900 text-white font-bold hover:opacity-90">
                Simpan
            </button>
        </form>
    </div>
</div>
@endsection
