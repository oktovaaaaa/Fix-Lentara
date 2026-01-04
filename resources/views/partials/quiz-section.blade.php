{{-- resources/views/partials/quiz-section.blade.php --}}
@php
  $quiz = $quiz ?? null;
@endphp

@if(!$quiz || $quiz->questions->count() === 0)
  {{-- <div class="border border-[var(--line)] rounded-2xl p-5 bg-[var(--card)] shadow-sm">
    <p class="text-sm text-[var(--muted)]">Belum ada quiz global yang aktif.</p> --}}
  </div>
@else
  @php
    // pastikan relasi terurut (kalau belum kamu handle di controller)
    $questions = $quiz->questions->sortBy('order')->values();
  @endphp

  <div id="globalQuizWrap"
       class="quiz-neon-card"
       data-total="{{ $questions->count() }}">

    {{-- ================= HEADER / HUD ================= --}}
    <div class="quiz-header">
      <div>
        <div class="quiz-title">{{ $quiz->title }}</div>
        <div class="quiz-meta">
          Mode: {{ $quiz->scope === 'global' ? 'Global' : ($quiz->scope === 'tribe' ? 'Suku' : 'Pulau') }}
        </div>
        <div class="quiz-meta">
          Soal <span data-hud-q>1</span>/<span data-hud-total>{{ $questions->count() }}</span>
          • Benar <span data-hud-correct>0</span>/<span data-hud-total2>{{ $questions->count() }}</span>
        </div>
      </div>

      <div class="quiz-score-badge">
        <div class="quiz-score-label">Skor</div>
        <div class="quiz-score-value"><span data-mini-score>0</span>%</div>
      </div>
    </div>

    {{-- progress (JANGAN DIKURANGI) --}}
    <div class="quiz-progress">
      <div class="quiz-progress-bar" data-progressbar style="width:0%"></div>
      <div class="quiz-progress-glow"></div>
    </div>

    <div class="q-divider"></div>

    {{-- ================= BODY ================= --}}
    <div data-questions>
      @foreach($questions as $idx => $q)
        @php
          $opts = $q->options->sortBy('order')->values();
        @endphp

        <div class="quiz-question"
             data-qindex="{{ $idx }}"
             style="{{ $idx === 0 ? '' : 'display:none' }}">

          <div class="flex items-center justify-between gap-3 mb-3">
            <div class="q-pill">
              Soal {{ $idx + 1 }} / {{ $questions->count() }}
            </div>
            <div class="text-xs text-[var(--muted)]">
              <span data-status>Belum dijawab</span>
            </div>
          </div>

          @if($q->prompt_type === 'text')
            <h3 class="q-title">{{ $q->prompt_text }}</h3>
          @else
            <h3 class="q-title">Pilih jawaban yang benar dari gambar berikut:</h3>
            <img src="{{ asset('storage/'.$q->prompt_image) }}"
                 class="w-full max-w-md rounded-2xl border border-[var(--line)] mt-3"
                 alt="Soal gambar">
          @endif

          <div class="grid sm:grid-cols-2 gap-3 mt-4" data-options>
            @foreach($opts as $optIndex => $opt)
              @php $letter = chr(65 + $optIndex); @endphp

              <button type="button"
                      class="q-opt"
                      data-correct="{{ $opt->is_correct ? '1' : '0' }}"
                      aria-label="Opsi {{ $letter }}">

                <div class="q-opt__inner">
                  <div class="q-opt__badge">{{ $letter }}</div>

                  <div class="q-opt__content">
                    @if($opt->content_type === 'text')
                      <div class="q-opt__text">{{ $opt->content_text }}</div>
                    @else
                      <img src="{{ asset('storage/'.$opt->content_image) }}"
                           class="q-opt__img"
                           alt="Opsi gambar">
                    @endif
                  </div>

                  <div class="q-opt__mark" aria-hidden="true">
                    <span class="q-opt__mark-ok">✓</span>
                    <span class="q-opt__mark-no">✕</span>
                  </div>
                </div>
              </button>
            @endforeach
          </div>

          <div class="mt-4 q-feedback" data-feedback></div>

          @if($q->explanation)
            <div class="q-explain" data-explain style="display:none;">
              <div class="q-explain__title">Penjelasan</div>
              <div class="q-explain__text">{{ $q->explanation }}</div>
            </div>
          @endif

          <div class="mt-5 flex items-center justify-between gap-3">
            <button type="button"
                    class="q-btn q-btn-ghost"
                    data-prev {{ $idx === 0 ? 'disabled' : '' }}>
              Prev
            </button>

            <button type="button"
                    class="q-btn q-btn-primary"
                    data-next
                    disabled>
              {{ $idx === $questions->count() - 1 ? 'Finish' : 'Next' }}
            </button>
          </div>
        </div>
      @endforeach
    </div>

    {{-- ===== RESULT (dalam card) ===== --}}
    <div data-result style="display:none;">
      <div class="q-result">
        <div class="q-result__spark"></div>

        <div class="text-xs text-[var(--muted)]">Hasil Kuis</div>
        <div class="text-2xl sm:text-3xl font-extrabold mt-1">
          Skor Kamu: <span data-score>0</span>%
        </div>

        <div class="text-sm text-[var(--muted)] mt-2">
          Benar <b data-correct>0</b> dari <b data-total>0</b> soal.
        </div>

        <div class="mt-4 w-full max-w-md mx-auto">
          <div class="q-progress q-progress--big">
            <div data-scorebar class="q-progress__bar" style="width:0%"></div>
            <div class="q-progress__glow"></div>
          </div>
          <div class="mt-2 text-xs text-[var(--muted)]" data-rank>—</div>
        </div>

        <div class="mt-6 flex items-center justify-center gap-2 flex-wrap">
          <button type="button" data-restart-2 class="q-btn q-btn-primary">
            Main Lagi
          </button>
          <button type="button" data-back class="q-btn q-btn-ghost">
            Kembali
          </button>
        </div>
      </div>
    </div>

  </div>

  <style>
    /* =========================================================
       NEON RING BORDER (ANIM) - TETAP ADA
    ========================================================= */
    @property --neon-angle {
      syntax: "<angle>";
      inherits: false;
      initial-value: 0deg;
    }

    .quiz-neon-card{
      position:relative;
      border-radius:26px;
      padding:20px;
      background:var(--card);
      box-shadow:0 0 0 1px rgba(255,255,255,.06),
                 0 30px 60px rgba(0,0,0,.45);

      /* IMPORTANT: hilangkan kotak overlay/glow */
      overflow:hidden;
    }

    .quiz-neon-card::before{
      content:"";
      position:absolute;
      inset:-6px;
      border-radius:inherit;
      padding:10px;
      pointer-events:none;
      z-index:0;

      background: conic-gradient(
        from var(--neon-angle),
        rgba(249,115,22,0) 0deg,
        rgba(249,115,22,.20) 22deg,
        #f97316 55deg,
        #22d3ee 110deg,
        #34d399 165deg,
        rgba(34,211,238,.20) 220deg,
        #f97316 300deg,
        rgba(249,115,22,0) 360deg
      );

      -webkit-mask:
        linear-gradient(#000 0 0) content-box,
        linear-gradient(#000 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;

      filter: blur(6px);
      opacity: .95;
      animation: neon-spin 7.5s linear infinite;
    }
    @keyframes neon-spin { to { --neon-angle: 360deg; } }

    /* pastikan semua konten di atas neon ring */
    .quiz-neon-card > *{ position:relative; z-index:1; }

    /* =========================================================
       HEADER
    ========================================================= */
    .quiz-header{display:flex;justify-content:space-between;align-items:center;gap:12px}
    .quiz-title{font-size:1.2rem;font-weight:800}
    .quiz-meta{font-size:.8rem;color:var(--muted)}
    .quiz-score-badge{
      border-radius:14px;padding:10px 14px;text-align:center;color:#fff;
      background: linear-gradient(135deg, #f97316, #fb923c);
      box-shadow: 0 18px 34px rgba(249,115,22,.22);
      border: 1px solid rgba(255,255,255,.06);
    }
    .quiz-score-value{font-weight:900}
    .q-divider{height:1px;background:color-mix(in oklab, var(--line) 80%, transparent);margin:16px 0 0}

    /* =========================================================
       PROGRESS (TIDAK DIKURANGI) + NEON SHIMMER
    ========================================================= */
    .quiz-progress{
      margin:14px 0 12px;
      height:10px;
      border-radius:999px;
      background: color-mix(in oklab, var(--bg-body) 78%, var(--card) 22%);
      border: 1px solid color-mix(in oklab, var(--line) 85%, transparent);
      overflow:hidden;
      position:relative;
    }
    .quiz-progress-bar{
      height:100%;
      width:0%;
      border-radius:999px;
      background: linear-gradient(90deg, #f97316, #22d3ee, #34d399);
      transition: width .45s ease;
      position:relative;
      overflow:hidden;
    }
    .quiz-progress-bar::after{
      content:"";
      position:absolute;
      inset:-40% -20%;
      background:
        linear-gradient(115deg,
          transparent 0%,
          rgba(255,255,255,.20) 18%,
          transparent 36%,
          rgba(255,255,255,.12) 52%,
          transparent 70%);
      transform: translateX(-60%);
      animation: quizShimmer 1.25s linear infinite;
      opacity:.75;
      pointer-events:none;
      mix-blend-mode: screen;
    }
    .quiz-progress-glow{
      position:absolute; inset:0;
      background:
        radial-gradient(circle at 25% 50%, rgba(34,211,238,.22), transparent 55%),
        radial-gradient(circle at 55% 50%, rgba(249,115,22,.20), transparent 58%),
        radial-gradient(circle at 80% 50%, rgba(52,211,153,.16), transparent 55%);
      pointer-events:none;
      opacity:.75;
      filter: blur(0.2px);
    }
    @keyframes quizShimmer{ to { transform: translateX(140%); } }

    /* =========================================================
       BUTTONS - ORANGE ONLY
    ========================================================= */
    .q-btn{
      border-radius: 14px;
      padding: 10px 14px;
      font-weight: 800;
      border: 1px solid color-mix(in oklab, var(--line) 85%, transparent);
      transition: transform .14s ease, opacity .18s ease, box-shadow .18s ease, filter .18s ease;
      user-select:none;
    }
    .q-btn:active{ transform: translateY(1px); }
    .q-btn:disabled{ opacity:.45; cursor:not-allowed; }

    .q-btn-ghost{
      background: color-mix(in oklab, var(--card) 92%, transparent);
      color: var(--txt-body);
    }
    .q-btn-ghost:hover{
      border-color: rgba(249,115,22,.55);
      box-shadow: 0 0 0 4px rgba(249,115,22,.12);
    }

    .q-btn-primary{
      border: 0;
      color:#fff;
      background: linear-gradient(135deg, #f97316, #fb923c);
      box-shadow:
        0 18px 34px rgba(249,115,22,.22),
        0 0 0 1px rgba(255,255,255,.06);
    }
    .q-btn-primary:hover{
      filter: brightness(1.05);
      box-shadow:
        0 20px 40px rgba(249,115,22,.30),
        0 0 0 1px rgba(255,255,255,.08);
    }

    /* =========================================================
       OPTIONS - ORANGE THEME
    ========================================================= */
    .q-opt{
      text-align:left;
      border-radius: 18px;
      border: 1px solid rgba(249,115,22,.22);
      background: color-mix(in oklab, var(--card) 92%, transparent);
      padding: 0;
      transition: transform .15s ease, box-shadow .18s ease, border-color .18s ease, filter .18s ease;
      position: relative;
      overflow:hidden;
    }
    .q-opt:hover{
      transform: translateY(-1px);
      box-shadow: 0 16px 34px rgba(249,115,22,.18);
      border-color: rgba(249,115,22,.45);
      filter: brightness(1.03);
    }
    .q-opt.is-locked{ pointer-events:none; opacity:.94; }

    .q-opt__inner{display:flex;gap:12px;align-items:flex-start;padding:14px;position:relative;}
    .q-opt__badge{
      width: 34px; height: 34px; border-radius: 12px;
      display:grid; place-items:center;
      font-weight: 900;
      color: #0b1220;
      background: linear-gradient(135deg, #f97316, #fb923c);
      box-shadow: 0 10px 18px rgba(249,115,22,.16);
      flex: 0 0 auto;
    }
    .q-opt__content{ flex:1 1 auto; min-width:0; }
    .q-opt__text{ font-size:14px;font-weight:800;color:var(--txt-body);line-height:1.35;word-break:break-word; }
    .q-opt__img{ width:100%; border-radius:14px; border:1px solid color-mix(in oklab, var(--line) 85%, transparent); display:block; }

    .q-opt::after{
      content:"";
      position:absolute; inset:0;
      background: radial-gradient(circle at top right, rgba(249,115,22,.14), transparent 55%);
      opacity:0;
      transition: opacity .18s ease;
      pointer-events:none;
    }
    .q-opt:hover::after{ opacity:1; }

    .q-opt__mark{
      flex: 0 0 auto;
      width: 30px;
      display:grid;
      place-items:center;
      opacity: 0;
      transform: translateX(6px);
      transition: all .18s ease;
      font-weight: 900;
    }
    .q-opt__mark-ok, .q-opt__mark-no{ display:none; font-size: 18px; }

    .q-opt.is-correct{
      border-color: rgba(34,197,94,.7);
      box-shadow: 0 0 0 4px rgba(34,197,94,.12), 0 18px 34px rgba(0,0,0,.18);
    }
    .q-opt.is-correct .q-opt__mark{ opacity:1; transform:translateX(0); color: rgba(34,197,94,1); }
    .q-opt.is-correct .q-opt__mark-ok{ display:inline; }

    .q-opt.is-wrong{
      border-color: rgba(239,68,68,.7);
      box-shadow: 0 0 0 4px rgba(239,68,68,.10), 0 18px 34px rgba(0,0,0,.18);
    }
    .q-opt.is-wrong .q-opt__mark{ opacity:1; transform:translateX(0); color: rgba(239,68,68,1); }
    .q-opt.is-wrong .q-opt__mark-no{ display:inline; }

    /* =========================================================
       TYPO + EXPLAIN + ANIM
    ========================================================= */
    .q-pill{
      font-size:12px;font-weight:800;padding:6px 10px;border-radius:999px;
      border:1px solid color-mix(in oklab, var(--line) 85%, transparent);
      background: color-mix(in oklab, var(--bg-body) 70%, var(--card) 30%);
      color: var(--txt-body);
    }
    .q-title{ font-size:18px; line-height:1.35; font-weight:900; letter-spacing:-.01em; }

    .q-feedback{ font-weight:900;font-size:14px;min-height:20px;margin-top:14px;display:flex;align-items:center;gap:10px; }
    .q-feedback .ok{ color:#22c55e; }
    .q-feedback .no{ color:#ef4444; }
    .q-feedback .meta{ font-weight:700;color:var(--muted);font-size:12px; }

/* ===================== EXPLANATION (lebih menarik) ===================== */
@keyframes explainIn {
  from { opacity: 0; transform: translateY(10px); }
  to   { opacity: 1; transform: translateY(0); }
}

.q-explain{
  margin-top: 14px;
  border-radius: 18px;
  padding: 14px 16px;
  position: relative;
  overflow: hidden;

  /* base */
  background: color-mix(in oklab, var(--bg-body) 68%, var(--card) 32%);
  border: 1px solid color-mix(in oklab, var(--line) 80%, transparent);
  box-shadow:
    0 14px 30px rgba(0,0,0,.22),
    0 0 0 1px rgba(255,255,255,.06);

  animation: explainIn .28s ease-out both;
}

/* neon accent ring tipis (orange) */
.q-explain::before{
  content:"";
  position:absolute;
  inset:-2px;
  border-radius: inherit;
  padding: 2px;
  pointer-events:none;

  background: linear-gradient(135deg,
    rgba(249,115,22,.0) 0%,
    rgba(249,115,22,.45) 35%,
    rgba(34,211,238,.22) 55%,
    rgba(249,115,22,.35) 75%,
    rgba(249,115,22,.0) 100%
  );

  -webkit-mask:
    linear-gradient(#000 0 0) content-box,
    linear-gradient(#000 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;

  filter: blur(.6px);
  opacity: .9;
}

/* glow soft di pojok */
.q-explain::after{
  content:"";
  position:absolute;
  inset:-40px -60px auto -60px;
  height: 140px;
  background:
    radial-gradient(circle at 30% 55%, rgba(249,115,22,.22), transparent 60%),
    radial-gradient(circle at 70% 45%, rgba(34,211,238,.16), transparent 62%);
  pointer-events:none;
  opacity:.9;
}

/* header: judul + icon */
.q-explain__title{
  display:flex;
  align-items:center;
  gap:10px;

  font-size: 12px;
  font-weight: 900;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: color-mix(in oklab, var(--txt-body) 85%, #fff 15%);
  margin-bottom: 6px;
  position: relative;
  z-index: 1;
}

/* icon bulat orange */
.q-explain__title::before{
  content:"i";
  width: 26px;
  height: 26px;
  border-radius: 999px;
  display:grid;
  place-items:center;

  font-weight: 1000;
  color: #0b1220;
  background: linear-gradient(135deg, #f97316, #fb923c);
  box-shadow: 0 12px 22px rgba(249,115,22,.22);
}

/* isi penjelasan */
.q-explain__text{
  font-size: 13px;
  line-height: 1.65;
  color: color-mix(in oklab, var(--muted) 92%, #fff 8%);
  position: relative;
  z-index: 1;
}

/* optional: highlight keyword (kalau ada <strong> di text) */
.q-explain__text strong{
  color: #fff;
}

    @keyframes quizIn { from{opacity:0; transform: translateY(10px);} to{opacity:1; transform: translateY(0);} }
    @keyframes pop { 0%{transform:scale(.98)} 60%{transform:scale(1.01)} 100%{transform:scale(1)} }
    .quiz-anim-in{ animation: quizIn .28s ease-out both; }
    .quiz-pop{ animation: pop .28s ease-out; }

    .q-result{
      text-align:center;
      padding: 22px 6px 6px;
      animation: quizIn .28s ease-out both;
      position:relative;
      overflow:hidden;          /* FIX: supaya spark ga jadi kotak */
      border-radius:18px;
    }
    .q-result__spark{
      position:absolute;
      inset:-40px;
      height:200px;
      background:
        radial-gradient(circle at 20% 50%, rgba(34,211,238,.20), transparent 60%),
        radial-gradient(circle at 50% 30%, rgba(249,115,22,.22), transparent 60%),
        radial-gradient(circle at 80% 55%, rgba(52,211,153,.16), transparent 60%);
      pointer-events:none;
      opacity:.9;
      filter: blur(0px);
    }
  </style>

  <script>
  (function(){
    const wrap = document.getElementById('globalQuizWrap');
    if(!wrap) return;

    const total = parseInt(wrap.getAttribute('data-total') || '0', 10);

    const questionsWrap = wrap.querySelector('[data-questions]');
    const resultWrap = wrap.querySelector('[data-result]');
    const items = Array.from(wrap.querySelectorAll('.quiz-question'));

    // HUD
    const hudQ = wrap.querySelector('[data-hud-q]');
    const hudTotal = wrap.querySelector('[data-hud-total]');
    const hudTotal2 = wrap.querySelector('[data-hud-total2]');
    const hudCorrect = wrap.querySelector('[data-hud-correct]');
    const miniScore = wrap.querySelector('[data-mini-score]');
    const progressBar = wrap.querySelector('[data-progressbar]');

    // Result nodes (FIX: SCOPE ke resultWrap, biar ga nabrak data-correct option)
    const rScore   = resultWrap?.querySelector('[data-score]');
    const rCorrect = resultWrap?.querySelector('[data-correct]');
    const rTotal   = resultWrap?.querySelector('[data-total]');
    const rRank    = resultWrap?.querySelector('[data-rank]');
    const scoreBar = resultWrap?.querySelector('[data-scorebar]');

    // Buttons
    const restartBtn2 = wrap.querySelector('[data-restart-2]');
    const backBtn = wrap.querySelector('[data-back]');

    // State
    let index = 0;
    let correctCount = 0;
    const answered = new Array(total).fill(false);

    function clamp(n,a,b){ return Math.max(a, Math.min(b,n)); }

    function computeScore(){
      return total ? Math.round((correctCount / total) * 100) : 0;
    }

    function rankText(score){
      if (score >= 90) return '🏆 Legendaris! Kamu jago banget.';
      if (score >= 75) return '🔥 Keren! Tinggal sedikit lagi sempurna.';
      if (score >= 50) return '✨ Lumayan! Ayo coba lagi biar makin tinggi.';
      return '🌱 Santai, coba lagi—kamu pasti bisa.';
    }

    function updateHud(){
      const done = answered.filter(Boolean).length;
      const pct = total ? Math.round((done / total) * 100) : 0;

      hudQ.textContent = String(index + 1);
      hudTotal.textContent = String(total);
      hudTotal2.textContent = String(total);
      hudCorrect.textContent = String(correctCount);

      progressBar.style.width = pct + '%';

      const score = computeScore();
      miniScore.textContent = String(score);
    }

    function showQuestion(i){
      index = clamp(i, 0, total - 1);

      if (resultWrap) resultWrap.style.display = 'none';
      if (questionsWrap) questionsWrap.style.display = '';

      items.forEach((el, idx) => {
        el.style.display = (idx === index ? '' : 'none');
        if (idx === index) {
          el.classList.remove('quiz-anim-in');
          void el.offsetWidth;
          el.classList.add('quiz-anim-in');
        }
      });

      updateHud();
    }

    function showResult(){
      if (questionsWrap) questionsWrap.style.display = 'none';
      if (resultWrap) resultWrap.style.display = '';

      const score = computeScore();
      if (rScore) rScore.textContent = String(score);
      if (rCorrect) rCorrect.textContent = String(correctCount);
      if (rTotal) rTotal.textContent = String(total);
      if (rRank) rRank.textContent = rankText(score);

      if (scoreBar) scoreBar.style.width = score + '%';

      if (progressBar) progressBar.style.width = '100%';
      if (miniScore) miniScore.textContent = String(score);
    }

    function resetAll(){
      index = 0;
      correctCount = 0;
      for (let i=0;i<total;i++) answered[i] = false;

      items.forEach(box => {
        const feedback = box.querySelector('[data-feedback]');
        if (feedback) feedback.innerHTML = '';

        const explain = box.querySelector('[data-explain]');
        if (explain) explain.style.display = 'none';

        const status = box.querySelector('[data-status]');
        if (status) status.textContent = 'Belum dijawab';

        const nextBtn = box.querySelector('[data-next]');
        if (nextBtn) nextBtn.disabled = true;

        const opts = Array.from(box.querySelectorAll('.q-opt'));
        opts.forEach(b => b.classList.remove('is-correct','is-wrong','is-locked'));
      });

      updateHud();
      showQuestion(0);
    }

    // per question handlers
    items.forEach((box, idx) => {
      const feedback = box.querySelector('[data-feedback]');
      const explain  = box.querySelector('[data-explain]');
      const status   = box.querySelector('[data-status]');

      const opts = Array.from(box.querySelectorAll('.q-opt'));
      const prevBtn = box.querySelector('[data-prev]');
      const nextBtn = box.querySelector('[data-next]');

      function lockAll(){ opts.forEach(b => b.classList.add('is-locked')); }

      opts.forEach(btn => {
        btn.addEventListener('click', () => {
          if (answered[idx]) return;

          const isCorrect = btn.getAttribute('data-correct') === '1';
          answered[idx] = true;
          if (isCorrect) correctCount++;

          opts.forEach(b => b.classList.remove('is-correct','is-wrong'));
          btn.classList.add(isCorrect ? 'is-correct' : 'is-wrong');

          const trueBtn = opts.find(b => b.getAttribute('data-correct') === '1');
          if (trueBtn) trueBtn.classList.add('is-correct');

          lockAll();

          if (feedback) {
            feedback.innerHTML = isCorrect
              ? `<span class="ok">✅ Benar!</span> <span class="meta">+1 poin</span>`
              : `<span class="no">❌ Salah.</span> <span class="meta">Cek penjelasan ya</span>`;
          }
          if (status) status.textContent = isCorrect ? 'Benar ✅' : 'Salah ❌';
          if (explain) explain.style.display = '';

          if (nextBtn) nextBtn.disabled = false;

          box.classList.add('quiz-pop');
          setTimeout(() => box.classList.remove('quiz-pop'), 300);

          updateHud();
        });
      });

      prevBtn?.addEventListener('click', () => showQuestion(idx - 1));

      nextBtn?.addEventListener('click', () => {
        if (idx >= total - 1) {
          const allAnswered = answered.every(Boolean);
          if (!allAnswered) return;
          return showResult();
        }
        showQuestion(idx + 1);
      });
    });

    restartBtn2?.addEventListener('click', resetAll);
    backBtn?.addEventListener('click', () => {
      if (questionsWrap) questionsWrap.style.display = '';
      if (resultWrap) resultWrap.style.display = 'none';
      showQuestion(index);
    });

    updateHud();
    showQuestion(0);
  })();
  </script>
@endif
