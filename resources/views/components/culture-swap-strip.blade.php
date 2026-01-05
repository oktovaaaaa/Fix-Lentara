{{-- resources/views/components/culture-swap-strip.blade.php --}}
<style>
    /* public/css/culture-swap-strip.css */

/* Wrapper */
.culture-strip-wrap{
  width: 100%;
  padding: 28px 16px;
  background: transparent;
}

/* Row strip */
.culture-strip{
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: flex-end;
  gap: 64px;              /* jarak antar gambar */
  flex-wrap: nowrap;
  overflow: hidden;
}

/* Item clickable */
.culture-item{
  border: 0;
  background: transparent;
  padding: 0;
  cursor: pointer;
  position: relative;
  display: grid;
  place-items: center;
  outline: none;
}

/* Default: abu-abu */
.culture-img{
  height: 92px;           /* atur sesuai kebutuhan */
  width: auto;
  opacity: .22;
  filter: grayscale(100%);
  transform: translateY(0) scale(1);
  transition:
    opacity .22s ease,
    filter .22s ease,
    transform .22s ease;
}

/* Active / Hover / Focus -> berwarna */
.culture-item.is-active .culture-img,
.culture-item:hover .culture-img,
.culture-item:focus-visible .culture-img{
  opacity: 1;
  filter: grayscale(0%);
  transform: translateY(-2px) scale(1.02);
}

/* Label kecil (nama) */
.culture-label{
  position: absolute;
  left: 50%;
  bottom: -18px;
  transform: translateX(-50%);
  white-space: nowrap;

  font-size: 11px;
  font-weight: 800;
  letter-spacing: .2px;

  padding: 5px 10px;
  border-radius: 999px;

  /* theme-aware */
  background: color-mix(in oklab, var(--card) 88%, transparent);
  border: 1px solid color-mix(in oklab, var(--brand) 35%, transparent);
  color: var(--txt-body);

  opacity: 0;
  pointer-events: none;
  transition: opacity .18s ease, transform .18s ease;
}

/* Label muncul saat hover / focus / active */
.culture-item.is-active .culture-label,
.culture-item:hover .culture-label,
.culture-item:focus-visible .culture-label{
  opacity: 1;
  transform: translateX(-50%) translateY(-2px);
}

/* Focus ring rapi */
.culture-item:focus-visible{
  outline: 3px solid color-mix(in oklab, var(--brand) 45%, transparent);
  outline-offset: 6px;
  border-radius: 16px;
}

/* Responsive */
@media (max-width: 900px){
  .culture-strip{ gap: 34px; }
  .culture-img{ height: 76px; }
}
@media (max-width: 520px){
  .culture-strip{ gap: 20px; }
  .culture-img{ height: 62px; }
}

</style>
@php
  // 7 gambar contoh (ganti src + name sesuai aset kamu)
  $items = $items ?? [
    ['name' => 'Tari Tradisional',   'src' => asset('images/strip/1.png')],
    ['name' => 'Pakaian Adat',       'src' => asset('images/strip/2.png')],
    ['name' => 'Kain Tenun',         'src' => asset('images/strip/3.png')],
    ['name' => 'Rumah Adat',         'src' => asset('images/strip/4.png')],
    ['name' => 'Tari Berpasangan',   'src' => asset('images/strip/5.png')],
    ['name' => 'Alat Musik',         'src' => asset('images/strip/6.png')],
    ['name' => 'Kesenian Daerah',    'src' => asset('images/strip/7.png')],
  ];
@endphp

<section class="culture-strip-wrap" aria-label="Culture swap strip">
  <div class="culture-strip" data-interval="500">
    @foreach($items as $idx => $it)
      <button
        type="button"
        class="culture-item {{ $idx === 0 ? 'is-active' : '' }}"
        data-index="{{ $idx }}"
        aria-label="{{ $it['name'] }}"
        title="{{ $it['name'] }}"
      >
        <img
          src="{{ $it['src'] }}"
          alt="{{ $it['name'] }}"
          class="culture-img"
          draggable="false"
          loading="lazy"
        />
        <span class="culture-label">{{ $it['name'] }}</span>
      </button>
    @endforeach
  </div>
</section>
<script>
    // public/js/culture-swap-strip.js
(function(){
  function initStrip(strip){
    const items = Array.from(strip.querySelectorAll('.culture-item'));
    if (!items.length) return;

    const intervalMs = parseInt(strip.dataset.interval || '500', 10);
    let idx = 0;
    let timer = null;
    let lockedIndex = null; // kalau user klik, lock di item itu

    function setActive(i){
      items.forEach((el, n) => el.classList.toggle('is-active', n === i));
      idx = i;
    }

    function next(){
      if (lockedIndex !== null) return; // kalau lock, jangan auto jalan
      const n = (idx + 1) % items.length;
      setActive(n);
    }

    function start(){
      stop();
      timer = setInterval(next, intervalMs);
    }

    function stop(){
      if (timer) clearInterval(timer);
      timer = null;
    }

    // Hover: langsung aktifkan (tapi auto tetap jalan setelah hover selesai)
    items.forEach((btn, i) => {
      btn.addEventListener('mouseenter', () => setActive(i));
      btn.addEventListener('focus', () => setActive(i));

      // Klik: lock/unlock
      btn.addEventListener('click', () => {
        if (lockedIndex === i) {
          lockedIndex = null; // unlock
          start();            // lanjut auto
        } else {
          lockedIndex = i;    // lock di item ini
          setActive(i);
          stop();             // berhenti auto
        }
      });
    });

    // Kalau mouse keluar strip dan tidak lock, lanjut auto
    strip.addEventListener('mouseleave', () => {
      if (lockedIndex === null) start();
    });

    // Init
    setActive(0);
    start();
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.culture-strip').forEach(initStrip);
  });
})();

</script>
