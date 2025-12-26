@php
    $quiz = $quiz ?? null;

    // Build payload dulu biar Blade gak "meledak" di @json pada atribut HTML
    $quizPayload = null;

    if ($quiz && $quiz->relationLoaded('questions')) {
        $quizPayload = [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'questions' => $quiz->questions->map(function($q) {
                return [
                    'id' => $q->id,
                    'prompt_type' => $q->prompt_type,
                    'prompt_text' => $q->prompt_text,
                    'prompt_image' => $q->prompt_image ? asset('storage/'.$q->prompt_image) : null,
                    'explanation' => $q->explanation,
                    'options' => $q->options->values()->map(function($o){
                        return [
                            'id' => $o->id,
                            'content_type' => $o->content_type,
                            'content_text' => $o->content_text,
                            'content_image' => $o->content_image ? asset('storage/'.$o->content_image) : null,
                            'is_correct' => (bool) $o->is_correct,
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ];
    } elseif ($quiz) {
        // kalau belum eager-load (jaga-jaga)
        $quiz->load(['questions.options']);
        $quizPayload = [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'questions' => $quiz->questions->map(function($q) {
                return [
                    'id' => $q->id,
                    'prompt_type' => $q->prompt_type,
                    'prompt_text' => $q->prompt_text,
                    'prompt_image' => $q->prompt_image ? asset('storage/'.$q->prompt_image) : null,
                    'explanation' => $q->explanation,
                    'options' => $q->options->values()->map(function($o){
                        return [
                            'id' => $o->id,
                            'content_type' => $o->content_type,
                            'content_text' => $o->content_text,
                            'content_image' => $o->content_image ? asset('storage/'.$o->content_image) : null,
                            'is_correct' => (bool) $o->is_correct,
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ];
    }
@endphp

<style>
/* ===== QUIZ THEME (light/dark safe + neon) ===== */
:root[data-theme="light"]{
  --q-bg1: rgba(56, 189, 248, .18);
  --q-bg2: rgba(249, 115, 22, .14);
  --q-card: color-mix(in oklab, var(--card) 88%, white 12%);
  --q-line: color-mix(in oklab, var(--line) 85%, transparent);
  --q-glow: rgba(56,189,248,.55);
  --q-glow2: rgba(249,115,22,.40);
}
:root[data-theme="dark"]{
  --q-bg1: rgba(56, 189, 248, .12);
  --q-bg2: rgba(249, 115, 22, .10);
  --q-card: color-mix(in oklab, var(--card) 92%, transparent);
  --q-line: rgba(255,255,255,.10);
  --q-glow: rgba(56,189,248,.45);
  --q-glow2: rgba(249,115,22,.35);
}

.q-wrap{
  position: relative;
  border-radius: 22px;
  padding: 18px;
  overflow: hidden;
  border: 1px solid var(--q-line);
  background: radial-gradient(circle at 10% 10%, var(--q-bg1), transparent 55%),
              radial-gradient(circle at 90% 20%, var(--q-bg2), transparent 60%),
              linear-gradient(180deg, color-mix(in oklab, var(--bg-body) 65%, transparent), transparent);
  box-shadow: 0 18px 40px rgba(0,0,0,.18);
}

/* pattern halus mirip contoh gambar */
.q-wrap::before{
  content:"";
  position:absolute; inset:0;
  background-image:
    radial-gradient(circle at 25px 25px, rgba(255,255,255,.10) 2px, transparent 3px),
    radial-gradient(circle at 10px 10px, rgba(255,255,255,.06) 1px, transparent 2px);
  background-size: 48px 48px, 28px 28px;
  opacity: .55;
  pointer-events:none;
}

/* neon ring */
@property --q-angle {
  syntax: "<angle>";
  inherits: false;
  initial-value: 0deg;
}
.q-glow{
  position:absolute; inset:-6px;
  border-radius: 26px;
  padding: 10px;
  pointer-events:none;
  background: conic-gradient(from var(--q-angle),
      rgba(56,189,248,0) 0deg,
      rgba(56,189,248,.20) 55deg,
      rgba(56,189,248,.9) 90deg,
      rgba(249,115,22,.8) 140deg,
      rgba(249,115,22,.15) 210deg,
      rgba(56,189,248,.35) 300deg,
      rgba(56,189,248,0) 360deg
  );
  -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  filter: blur(4px);
  opacity: .85;
  animation: qspin 9s linear infinite;
}
@keyframes qspin{ to{ --q-angle: 360deg; } }

.q-card{
  position:relative;
  z-index:1;
  border-radius: 18px;
  background: var(--q-card);
  border: 1px solid var(--q-line);
  padding: 16px;
  color: var(--txt-body);
}

.q-top{
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap: 10px;
  margin-bottom: 10px;
}
.q-pill{
  font-size: 12px;
  font-weight: 800;
  padding: 6px 10px;
  border-radius: 999px;
  border: 1px solid var(--q-line);
  background: color-mix(in oklab, var(--q-card) 85%, transparent);
  color: var(--muted);
}
.q-title{
  font-weight: 900;
  font-size: 18px;
}

.q-prompt{
  margin-top: 8px;
  font-weight: 800;
  line-height: 1.4;
}
.q-img{
  margin-top: 10px;
  width: 100%;
  max-height: 280px;
  object-fit: cover;
  border-radius: 14px;
  border: 1px solid var(--q-line);
}

.q-opts{
  margin-top: 14px;
  display:grid;
  gap: 10px;
}
.q-opt{
  width:100%;
  text-align:left;
  border-radius: 16px;
  padding: 12px;
  border: 1px solid var(--q-line);
  background: color-mix(in oklab, var(--q-card) 92%, transparent);
  color: var(--txt-body);
  display:flex;
  gap: 10px;
  align-items:center;
  transition: transform .14s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
  cursor:pointer;
}
.q-opt:hover{ transform: translateY(-1px); box-shadow: 0 14px 26px rgba(0,0,0,.12); }
.q-opt[disabled]{ opacity: .9; cursor: not-allowed; transform:none; box-shadow:none; }
.q-opt .q-letter{
  width: 34px; height: 34px;
  border-radius: 12px;
  display:grid; place-items:center;
  font-weight: 900;
  border: 1px solid var(--q-line);
  color: var(--txt-body);
  background: color-mix(in oklab, var(--bg-body) 30%, transparent);
}
.q-opt .q-content{ flex: 1; }
.q-opt img{
  width: 100%;
  max-width: 260px;
  border-radius: 12px;
  border: 1px solid var(--q-line);
}

/* result colors */
.q-opt.is-correct{
  border-color: rgba(34,197,94,.55);
  background: color-mix(in oklab, rgba(34,197,94,.18) 60%, var(--q-card) 40%);
}
.q-opt.is-wrong{
  border-color: rgba(239,68,68,.55);
  background: color-mix(in oklab, rgba(239,68,68,.16) 60%, var(--q-card) 40%);
}

.q-actions{
  margin-top: 14px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap: 10px;
}
.q-btn{
  border-radius: 14px;
  padding: 10px 14px;
  font-weight: 900;
  border: 1px solid var(--q-line);
  background: color-mix(in oklab, var(--q-card) 92%, transparent);
  color: var(--txt-body);
}
.q-next{
  border: 0;
  border-radius: 14px;
  padding: 11px 16px;
  font-weight: 1000;
  color: #fff;
  background: linear-gradient(135deg, rgba(56,189,248,.95), rgba(249,115,22,.95));
  box-shadow: 0 18px 30px rgba(56,189,248,.12);
  transition: transform .14s ease, filter .18s ease;
}
.q-next:disabled{
  opacity: .55;
  filter: grayscale(.2);
  cursor:not-allowed;
  transform:none;
}
.q-next:not(:disabled):hover{ transform: translateY(-1px); filter: brightness(1.05); }

.q-note{
  font-size: 12px;
  color: var(--muted);
  margin-top: 10px;
}
</style>

<div class="q-wrap max-w-5xl mx-auto">
  <div class="q-glow" aria-hidden="true"></div>

  <div class="q-card" id="quizRoot"
       data-quiz='@json($quizPayload)'>
    <div class="q-top">
      <div>
        <div class="q-title" id="quizTitle">{{ $quiz?->title ?? 'Kuis Budaya Indonesia' }}</div>
        <div class="q-note">Pilih 1 jawaban. Setelah menjawab, tekan “Berikutnya”.</div>
      </div>
      <div class="q-pill" id="quizMeta">Soal 0/0 • Benar: 0</div>
    </div>

    <div id="quizBody">
      @if(!$quiz || !$quizPayload || empty($quizPayload['questions']))
        <div class="q-note">Belum ada quiz aktif / pertanyaan belum dibuat oleh admin.</div>
      @endif
    </div>
  </div>
</div>

<script>
(function(){
  const root = document.getElementById('quizRoot');
  if(!root) return;

  const payload = JSON.parse(root.dataset.quiz || 'null');
  const body = document.getElementById('quizBody');
  const meta = document.getElementById('quizMeta');

  if(!payload || !payload.questions || payload.questions.length === 0) return;

  const letters = ['A','B','C','D','E','F'];
  let idx = 0;
  let correctCount = 0;
  let answered = false;

  function setMeta(){
    meta.textContent = `Soal ${idx+1}/${payload.questions.length} • Benar: ${correctCount}`;
  }

  function render(){
    const q = payload.questions[idx];
    answered = false;

    const promptHtml = q.prompt_type === 'text'
      ? `<div class="q-prompt">${escapeHtml(q.prompt_text || '')}</div>`
      : `
        <div class="q-prompt">Perhatikan gambar berikut:</div>
        <img class="q-img" src="${q.prompt_image}" alt="Soal gambar">
      `;

    const optionsHtml = (q.options || []).map((o,i)=>{
      const content = o.content_type === 'text'
        ? `<div class="q-content">${escapeHtml(o.content_text || '')}</div>`
        : `<div class="q-content"><img src="${o.content_image}" alt="Opsi gambar"></div>`;

      return `
        <button type="button" class="q-opt" data-i="${i}">
          <div class="q-letter">${letters[i] || (i+1)}</div>
          ${content}
        </button>
      `;
    }).join('');

    body.innerHTML = `
      ${promptHtml}
      <div class="q-opts" id="qOpts">${optionsHtml}</div>
      <div class="q-actions">
        <button type="button" class="q-btn" id="qReset">Ulangi Soal</button>
        <button type="button" class="q-next" id="qNext" disabled>Berikutnya ➜</button>
      </div>
      <div class="q-note" id="qExplain" style="display:none;"></div>
    `;

    setMeta();

    const optsWrap = document.getElementById('qOpts');
    const nextBtn = document.getElementById('qNext');
    const resetBtn = document.getElementById('qReset');
    const explain = document.getElementById('qExplain');

    function lock(){
      optsWrap.querySelectorAll('.q-opt').forEach(b => b.setAttribute('disabled','disabled'));
    }

    function reveal(correctIndex, pickedIndex){
      const buttons = Array.from(optsWrap.querySelectorAll('.q-opt'));
      buttons.forEach((b, i)=>{
        b.classList.remove('is-correct','is-wrong');
        if(i === pickedIndex){
          b.classList.add(i === correctIndex ? 'is-correct' : 'is-wrong');
        }
        if(pickedIndex !== correctIndex && i === correctIndex){
          b.classList.add('is-correct');
        }
      });
    }

    optsWrap.querySelectorAll('.q-opt').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        if(answered) return;
        answered = true;

        const picked = parseInt(btn.dataset.i, 10);
        const correctIndex = (q.options || []).findIndex(x => x.is_correct);

        if(picked === correctIndex) correctCount++;

        reveal(correctIndex, picked);
        lock();

        if(q.explanation){
          explain.style.display = 'block';
          explain.innerHTML = `<strong>Penjelasan:</strong> ${escapeHtml(q.explanation)}`;
        }

        nextBtn.disabled = false;
        setMeta();
      });
    });

    resetBtn.addEventListener('click', ()=>{
      render(); // ulangi soal ini
    });

    nextBtn.addEventListener('click', ()=>{
      if(!answered) return;

      if(idx < payload.questions.length - 1){
        idx++;
        render();
      } else {
        body.innerHTML = `
          <div class="q-prompt">Selesai 🎉</div>
          <div class="q-note">Skor kamu: <strong>${correctCount}</strong> dari <strong>${payload.questions.length}</strong></div>
          <div class="q-actions">
            <button type="button" class="q-next" id="qRestart">Ulangi Quiz</button>
          </div>
        `;
        const r = document.getElementById('qRestart');
        r && r.addEventListener('click', ()=>{
          idx = 0; correctCount = 0;
          render();
        });
        setMeta();
      }
    });
  }

  function escapeHtml(str){
    return String(str)
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'","&#039;");
  }

  render();
})();
</script>
