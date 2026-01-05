{{-- resources/views/components/music-toggle.blade.php --}}

<style>
  /* =========================================================
     FLOATING MUSIC BUTTON
     - circle orange (brand)
     - icon theme aware (light: black / dark: white)
     - animated toggle (play <-> pause)
     - glow while playing
  ========================================================= */

  .music-fab {
    position: fixed;
    right: 20px;
    bottom: 92px; /* biar gak nabrak chatbot */
    z-index: 40;

    width: 58px;
    height: 58px;
    border-radius: 999px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    background: var(--brand);
    border: 1px solid color-mix(in oklab, var(--brand) 70%, transparent);

    box-shadow:
      0 18px 38px rgba(0,0,0,.25),
      0 0 0 0 rgba(255, 107, 0, 0);

    cursor: pointer;
    user-select: none;

    transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
  }

  .music-fab:hover {
    transform: translateY(-2px);
    box-shadow:
      0 24px 50px rgba(0,0,0,.32),
      0 0 0 0 rgba(255, 107, 0, 0);
    filter: brightness(1.05);
  }

  .music-fab:active {
    transform: translateY(0);
    filter: brightness(0.98);
  }

  .music-fab:focus { outline: none; }
  .music-fab:focus-visible {
    outline: 3px solid color-mix(in oklab, var(--brand-2) 45%, transparent);
    outline-offset: 5px;
  }

  .music-icon-wrap{
    position: relative;
    width: 26px;
    height: 26px;
    display: grid;
    place-items: center;
  }

  .music-ico{
    position: absolute;
    inset: 0;
    width: 26px;
    height: 26px;
    display: block;

    fill: var(--txt-body); /* LIGHT => hitam, DARK => putih */

    opacity: 0;
    transform: scale(.85) rotate(-8deg);
    filter: drop-shadow(0 10px 18px rgba(0,0,0,.22));
    transition:
      opacity .18s ease,
      transform .22s cubic-bezier(.2,.9,.2,1),
      filter .18s ease;
  }

  .music-fab.is-paused .ico-play{
    opacity: 1;
    transform: scale(1) rotate(0deg);
  }

  .music-fab.is-playing .ico-pause{
    opacity: 1;
    transform: scale(1) rotate(0deg);
  }

  .music-fab.is-toggling .music-ico{
    transform: scale(1.08) rotate(2deg);
    filter: drop-shadow(0 14px 26px rgba(0,0,0,.25));
  }

  .music-fab.is-playing{
    animation: musicGlow 1.6s ease-in-out infinite;
  }

  @keyframes musicGlow{
    0%,100%{
      box-shadow:
        0 18px 38px rgba(0,0,0,.25),
        0 0 0 0 rgba(255, 107, 0, 0.0);
    }
    50%{
      box-shadow:
        0 22px 50px rgba(0,0,0,.28),
        0 0 0 12px rgba(255, 107, 0, 0.18);
    }
  }

  @media (max-width: 480px) {
    .music-fab { width: 54px; height: 54px; right: 16px; bottom: 86px; }
    .music-icon-wrap, .music-ico { width: 24px; height: 24px; }
  }

  @media (prefers-reduced-motion: reduce){
    .music-fab, .music-ico{
      transition: none !important;
      animation: none !important;
    }
  }
</style>

{{-- AUDIO: loop terus sampai user klik pause --}}
<audio id="lentaraMusic" preload="none" loop>
  <source src="{{ asset('audio/pesonaindonesia.M4A') }}" type="audio/mp4">
</audio>

<button
  type="button"
  id="musicFab"
  class="music-fab is-paused"
  aria-label="Play music"
  title="Play music"
>
  <span class="music-icon-wrap" aria-hidden="true">
    {{-- PLAY --}}
    <svg class="music-ico ico-play" viewBox="0 0 24 24">
      <path d="M9.2 7.1c0-1.1 1.2-1.8 2.1-1.2l8 4.9c.9.6.9 1.8 0 2.4l-8 4.9c-.9.6-2.1 0-2.1-1.2V7.1z"/>
    </svg>

    {{-- PAUSE --}}
    <svg class="music-ico ico-pause" viewBox="0 0 24 24">
      <path d="M7.5 6.5c0-.6.4-1 1-1h2c.6 0 1 .4 1 1v11c0 .6-.4 1-1 1h-2c-.6 0-1-.4-1-1v-11z"/>
      <path d="M13.5 6.5c0-.6.4-1 1-1h2c.6 0 1 .4 1 1v11c0 .6-.4 1-1 1h-2c-.6 0-1-.4-1-1v-11z"/>
    </svg>
  </span>
</button>

<script>
(function(){
  const btn   = document.getElementById('musicFab');
  const audio = document.getElementById('lentaraMusic');

  if (!btn || !audio) return;

  function setState(isPlaying){
    btn.classList.toggle('is-playing', isPlaying);
    btn.classList.toggle('is-paused', !isPlaying);

    btn.setAttribute('aria-label', isPlaying ? 'Pause music' : 'Play music');
    btn.setAttribute('title', isPlaying ? 'Pause music' : 'Play music');
  }

  function togglePop(){
    btn.classList.add('is-toggling');
    setTimeout(() => btn.classList.remove('is-toggling'), 180);
  }

  // initial
  setState(false);

  btn.addEventListener('click', async () => {
    togglePop();

    try {
      if (audio.paused) {
        await audio.play();
        setState(true);
      } else {
        audio.pause();
        setState(false);
      }
    } catch (e) {
      console.warn('Music play blocked or error:', e);
      setState(false);
      alert('Audio tidak bisa diputar. Pastikan file audio ada dan formatnya didukung (‘.m4a’ biasanya type-nya audio/mp4).');
    }
  });

  // kalau user pause atau browser stop, UI ikut balik
  audio.addEventListener('pause', () => {
    if (audio.currentTime > 0 && !audio.ended) setState(false);
  });

  // loop sudah di-handle oleh atribut loop, jadi tidak perlu ended handler
})();
</script>
