@extends('layouts.admin')

@section('title', 'Admin - Tambah Pertanyaan')
@section('page-title', 'Tambah Pertanyaan Quiz')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow border p-5">
        <h2 class="text-xl font-bold mb-1">Tambah Pertanyaan</h2>

        <div class="text-sm text-slate-600 mb-4">
            <div class="font-bold">{{ $quiz->title }}</div>
            @php
                $scopeLabel = 'Global';
                if ($quiz->island_id && $quiz->tribe) $scopeLabel = 'Suku';
                elseif ($quiz->island_id) $scopeLabel = 'Pulau';
            @endphp
            <div class="text-xs text-slate-500 mt-1">
                Cakupan: <span class="font-bold">{{ $scopeLabel }}</span>
                @if($quiz->island)
                    • Pulau: <span class="font-bold">{{ $quiz->island->subtitle ?: $quiz->island->name }}</span>
                @endif
                @if($quiz->tribe)
                    • Suku: <span class="font-bold">{{ $quiz->tribe }}</span>
                @endif
            </div>
            <div class="text-xs text-slate-500 mt-1">
                Soal & opsi bisa teks atau gambar. Pilih <span class="font-bold">1 jawaban benar</span>.
            </div>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700">
                <div class="font-bold mb-1">Gagal:</div>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.quiz-questions.store', $quiz) }}"
              enctype="multipart/form-data" class="space-y-4" id="questionForm">
            @csrf

            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-bold">Order (angka)</label>
                    <input class="w-full border rounded-lg px-3 py-2" name="order" value="{{ old('order', 0) }}" />
                </div>
                <div>
                    <label class="text-sm font-bold">Tipe Soal</label>
                    <select class="w-full border rounded-lg px-3 py-2" name="prompt_type" id="promptType">
                        <option value="text" {{ old('prompt_type','text')==='text'?'selected':'' }}>Text</option>
                        <option value="image" {{ old('prompt_type')==='image'?'selected':'' }}>Image</option>
                    </select>
                </div>
            </div>

            <div id="promptTextWrap">
                <label class="text-sm font-bold">Soal (Text)</label>
                <textarea class="w-full border rounded-lg px-3 py-2" name="prompt_text" rows="3">{{ old('prompt_text') }}</textarea>
            </div>

            <div id="promptImageWrap" class="hidden">
                <label class="text-sm font-bold">Soal (Image)</label>
                <input class="w-full border rounded-lg px-3 py-2" type="file" name="prompt_image" accept="image/*">
                <div class="text-xs text-slate-500 mt-1">JPG/PNG/WEBP max 2MB</div>
            </div>

            <div>
                <label class="text-sm font-bold">Penjelasan (opsional)</label>
                <textarea class="w-full border rounded-lg px-3 py-2" name="explanation" rows="2">{{ old('explanation') }}</textarea>
            </div>

            <hr>

            <div class="flex items-center justify-between">
                <h3 class="font-extrabold">Opsi Jawaban</h3>
                <span class="text-xs text-slate-500">Minimal 2, maksimal 6</span>
            </div>

            @for($i=0; $i<4; $i++)
                <div class="border rounded-xl p-4 space-y-2 option-block" data-index="{{ $i }}">
                    <div class="flex items-center justify-between gap-3">
                        <div class="font-bold">Opsi #{{ $i+1 }}</div>

                        <label class="inline-flex items-center gap-2 font-semibold">
                            <input type="radio" name="correct_index" value="{{ $i }}" {{ (string)old('correct_index','0')===(string)$i ? 'checked' : '' }}>
                            Jawaban Benar
                        </label>
                    </div>

                    <div class="grid sm:grid-cols-3 gap-3">
                        <div>
                            <label class="text-sm font-bold">Tipe</label>
                            <select class="w-full border rounded-lg px-3 py-2 optType" name="options[{{ $i }}][content_type]">
                                <option value="text" {{ old("options.$i.content_type",'text')==='text'?'selected':'' }}>Text</option>
                                <option value="image" {{ old("options.$i.content_type")==='image'?'selected':'' }}>Image</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-bold">Order</label>
                            <input class="w-full border rounded-lg px-3 py-2" name="options[{{ $i }}][order]" value="{{ old("options.$i.order",$i) }}">
                        </div>
                        <div class="text-xs text-slate-500 flex items-end">
                            (opsional) urutan custom
                        </div>
                    </div>

                    <div class="optTextWrap">
                        <label class="text-sm font-bold">Isi (Text)</label>
                        <input class="w-full border rounded-lg px-3 py-2" name="options[{{ $i }}][content_text]" value="{{ old("options.$i.content_text") }}">
                    </div>

                    <div class="optImageWrap hidden">
                        <label class="text-sm font-bold">Isi (Image)</label>
                        <input class="w-full border rounded-lg px-3 py-2" type="file" name="options[{{ $i }}][content_image]" accept="image/*">
                    </div>
                </div>
            @endfor

            <div class="flex gap-2">
                <a href="{{ route('admin.quizzes.edit', $quiz) }}"
                   class="px-4 py-2 rounded-lg border font-bold">
                    Kembali
                </a>

                <button class="px-4 py-2 rounded-lg bg-slate-900 text-white font-extrabold">
                    Simpan Pertanyaan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function(){
    const promptType = document.getElementById('promptType');
    const promptTextWrap = document.getElementById('promptTextWrap');
    const promptImageWrap = document.getElementById('promptImageWrap');

    function syncPrompt(){
        const v = promptType.value;
        promptTextWrap.classList.toggle('hidden', v !== 'text');
        promptImageWrap.classList.toggle('hidden', v !== 'image');
    }
    promptType.addEventListener('change', syncPrompt);
    syncPrompt();

    document.querySelectorAll('.option-block').forEach(block => {
        const select = block.querySelector('.optType');
        const t = block.querySelector('.optTextWrap');
        const i = block.querySelector('.optImageWrap');

        function syncOpt(){
            const v = select.value;
            t.classList.toggle('hidden', v !== 'text');
            i.classList.toggle('hidden', v !== 'image');
        }
        select.addEventListener('change', syncOpt);
        syncOpt();
    });
})();
</script>
@endpush
@endsection
