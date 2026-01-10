{{-- resources/views/islands/partials/history.blade.php --}}

@php
    // ===============================
    // SAFETY DEFAULTS (JANGAN HAPUS)
    // ===============================
    $tribeKey               = $tribeKey ?? '';
    $currentTribeHistories  = $currentTribeHistories ?? collect();
    $historyFeatures        = $historyFeatures ?? collect();
@endphp

<section id="history" class="history-section py-12">
    <div class="history-container">
        {{-- HEADER TITLE: pakai sistem title global (SAMA seperti islands/home) --}}
        <div class="history-title-wrap">
            <h2 class="neon-title">
                Sejarah Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}
            </h2>
            <div class="title-decoration"></div>

            <p class="neon-subtitle">
                Timeline sejarah yang membentuk identitas budaya dan perjalanan Suku {{ $tribeKey !== '' ? $tribeKey : '—' }}.
            </p>
        </div>

        @if($currentTribeHistories->count())
            <div class="timeline">
                @foreach($currentTribeHistories as $item)
                    <div class="timeline-item">
                        <div class="timeline-card">
                            <div class="timeline-card-glow"></div>

                            <div class="timeline-card-inner">
                                <div class="timeline-badge">
                                    {{ !empty($item->year_label) ? $item->year_label : 'Jejak Sejarah' }}
                                </div>

                                <h3 class="timeline-heading">
                                    {{ $item->title }}
                                </h3>

                                <p class="timeline-text">
                                    {{ $item->content }}
                                </p>

                                @if(!empty($item->more_link))
                                    <a href="{{ $item->more_link }}"
                                       target="_blank"
                                       rel="noopener"
                                       class="timeline-link">
                                        Lihat selengkapnya →
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="history-empty" style="text-align: center">
                Belum ada data sejarah {{ $tribeKey !== '' ? $tribeKey : 'suku' }} yang diinput dari admin.
            </p>
        @endif

        {{-- OPTIONAL: Sejarah pulau-level dari features (kalau kamu pakai) --}}
        @if($historyFeatures->count())
            <div class="history-features space-y-3 mt-8">
                <h3 class="history-mini-title">Sejarah Pulau (Umum)</h3>
                <p class="history-mini-subtitle">
                    Ringkasan sejarah umum untuk pulau ini (opsional).
                </p>

                @foreach($historyFeatures as $f)
                    <div class="feature-card">
                        <h4>{{ $f->title }}</h4>
                        <p>{{ $f->content }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
