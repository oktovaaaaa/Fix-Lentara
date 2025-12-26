{{-- resources/views/islands/partials/quiz-by-tribe.blade.php --}}
@props([
    'tribeName',
    'quiz' => null,
    'fallback' => null,
])

@php
    $activeQuiz = $quiz ?: $fallback;
@endphp

@if(!$activeQuiz)
    <p class="text-sm text-[var(--muted)]">
        Belum ada kuis untuk suku <b>{{ $tribeName }}</b>.
    </p>
@else
    <x-quiz.neon
        :quiz="$activeQuiz"
        :contextLabel="$tribeName"
        storagePrefix="storage/"
    />
@endif
