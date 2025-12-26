{{-- resources/views/partials/quiz-section.blade.php --}}
@php
  $quiz = $quiz ?? null;
@endphp

@if(!$quiz || $quiz->questions->count() === 0)
  <div class="border border-[var(--line)] rounded-2xl p-5 bg-[var(--card)] shadow-sm">
    <p class="text-sm text-[var(--muted)]">Belum ada quiz global yang aktif.</p>
  </div>
@else
  <div class="space-y-4" id="globalQuizWrap">
    @foreach($quiz->questions as $idx => $q)
      <div class="border border-[var(--line)] rounded-2xl p-5 bg-[var(--card)] shadow-sm quiz-question"
           data-qindex="{{ $idx }}" style="{{ $idx === 0 ? '' : 'display:none' }}">
        <div class="flex items-start justify-between gap-3 mb-3">
          <div class="font-extrabold text-base">
            Soal {{ $idx + 1 }} / {{ $quiz->questions->count() }}
          </div>
          <div class="text-xs text-[var(--muted)]">{{ $quiz->title }}</div>
        </div>

        @if($q->prompt_type === 'text')
          <p class="font-semibold mb-3">{{ $q->prompt_text }}</p>
        @else
          <p class="font-semibold mb-3">Pilih jawaban yang benar dari gambar berikut:</p>
          <img src="{{ asset('storage/'.$q->prompt_image) }}"
               class="w-full max-w-md rounded-2xl border border-[var(--line)] mb-3"
               alt="Soal gambar">
        @endif

        <div class="grid sm:grid-cols-2 gap-3">
          @foreach($q->options as $opt)
            <button type="button"
              class="quiz-opt border border-[var(--line)] rounded-xl p-3 text-left hover:opacity-90"
              data-correct="{{ $opt->is_correct ? '1' : '0' }}">
              @if($opt->content_type === 'text')
                <div class="text-sm font-semibold">{{ $opt->content_text }}</div>
              @else
                <img src="{{ asset('storage/'.$opt->content_image) }}"
                     class="w-full rounded-xl border border-[var(--line)]"
                     alt="Opsi gambar">
              @endif
            </button>
          @endforeach
        </div>

        <div class="mt-4 flex items-center justify-between gap-3">
          <div class="text-sm" data-feedback></div>

          <div class="flex gap-2">
            <button type="button" class="px-4 py-2 rounded-xl border border-[var(--line)]"
                    data-prev {{ $idx === 0 ? 'disabled' : '' }}>
              Prev
            </button>
            <button type="button" class="px-4 py-2 rounded-xl bg-[var(--brand)] text-white font-bold"
                    data-next>
              Next
            </button>
          </div>
        </div>

        @if($q->explanation)
          <p class="mt-3 text-xs text-[var(--muted)]" data-explain style="display:none;">
            <span class="font-semibold">Penjelasan:</span> {{ $q->explanation }}
          </p>
        @endif
      </div>
    @endforeach
  </div>

  <script>
  (function(){
    const wrap = document.getElementById('globalQuizWrap');
    if(!wrap) return;

    const items = Array.from(wrap.querySelectorAll('.quiz-question'));

    function show(i){
      items.forEach((el, idx) => el.style.display = (idx === i ? '' : 'none'));
    }

    items.forEach((box, idx) => {
      const feedback = box.querySelector('[data-feedback]');
      const explain = box.querySelector('[data-explain]');
      const opts = box.querySelectorAll('.quiz-opt');

      opts.forEach(btn => {
        btn.addEventListener('click', () => {
          const correct = btn.getAttribute('data-correct') === '1';
          feedback.textContent = correct ? '✅ Benar!' : '❌ Salah.';
          if (explain) explain.style.display = '';
        });
      });

      box.querySelector('[data-prev]')?.addEventListener('click', () => {
        show(Math.max(0, idx - 1));
      });

      box.querySelector('[data-next]')?.addEventListener('click', () => {
        show(Math.min(items.length - 1, idx + 1));
      });
    });

    show(0);
  })();
  </script>
@endif
