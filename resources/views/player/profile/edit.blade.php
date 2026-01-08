{{-- resources/views/player/profile/edit.blade.php --}}
@extends('layouts.auth')

@section('title', 'Profil')

@section('content')
<div class="auth-page">

    {{-- TOP BANNER IMAGE --}}
    <div class="auth-banner auth-banner--top" aria-hidden="true">
        <img
            src="{{ asset('images/icon/footer.JPEG') }}"
            alt="Lentara Top Banner"
            class="auth-banner__img"
            loading="lazy"
        />
        <div class="auth-banner__overlay auth-banner__overlay--top"></div>
    </div>

    {{-- BOTTOM BANNER IMAGE --}}
    <div class="auth-banner auth-banner--bottom" aria-hidden="true">
        <div class="auth-banner__overlay auth-banner__overlay--bottom"></div>
        <img
            src="{{ asset('images/icon/footer.JPEG') }}"
            alt="Lentara Bottom Banner"
            class="auth-banner__img"
            loading="lazy"
        />
    </div>

    {{-- BACKGROUND GLOW / ORNAMENT --}}
    <div class="auth-bg" aria-hidden="true">
        <div class="auth-glow auth-glow--a"></div>
        <div class="auth-glow auth-glow--b"></div>
        <div class="auth-grid"></div>
    </div>

    {{-- CENTER WRAP --}}
    <div class="auth-center">
        <div class="auth-card auth-card--wide" role="region" aria-label="Edit Profil Player">

            {{-- Header --}}
            <div class="auth-head">
                <div class="auth-badge" aria-hidden="true">
                    {{-- icon profile --}}
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M20 21a8 8 0 0 0-16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                </div>

                <h1 class="auth-title">Profil</h1>
                <p class="auth-subtitle">Ubah nama panggilan dan pilih avatar kamu</p>
            </div>

            {{-- Success / Error (session + validation) --}}
            @if(session('success'))
                <div class="auth-success" role="status">
                    <span class="auth-success__dot" aria-hidden="true"></span>
                    <span>{{ session('success') }}</span>
                </div>
            @elseif(session('error'))
                <div class="auth-error" role="alert">
                    <span class="auth-error__dot" aria-hidden="true"></span>
                    <span>{{ session('error') }}</span>
                </div>
            @elseif($errors->any())
                <div class="auth-error" role="alert">
                    <span class="auth-error__dot" aria-hidden="true"></span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- FORM UPDATE PROFILE (FUNGSI TETAP) --}}
            <form method="POST" action="{{ route('player.profile.update') }}" class="auth-form">
                @csrf

                {{-- Nickname --}}
                <div class="auth-field">
                    <label class="auth-label" for="nickname">Nama panggilan</label>
                    <div class="auth-inputWrap">
                        <span class="auth-ico" aria-hidden="true">
                            {{-- icon edit/user --}}
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M20 21a8 8 0 0 0-16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                <path d="M16.5 3.5l4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <input
                            id="nickname"
                            name="nickname"
                            value="{{ old('nickname', $player->nickname) }}"
                            required
                            placeholder=""
                            class="auth-input"
                            autocomplete="nickname"
                        >
                    </div>
                </div>

                {{-- Avatar Picker --}}
                <div class="auth-field">
                    <div class="auth-labelRow">
                        <label class="auth-label" style="margin:0;">Pilih Avatar</label>

                    </div>

                    <div class="avatar-grid" role="radiogroup" aria-label="Pilihan Avatar">
                        @for($i=1;$i<=5;$i++)
                            <label class="avatar-item" data-checked="{{ (int)$player->avatar_key === $i ? '1' : '0' }}">
                                <input
                                    class="avatar-radio"
                                    type="radio"
                                    name="avatar_key"
                                    value="{{ $i }}"
                                    {{ (int)$player->avatar_key === $i ? 'checked' : '' }}
                                    aria-label="Avatar {{ $i }}"
                                />
                                <span class="avatar-imgWrap" aria-hidden="true">
                                    <img
                                        src="{{ asset('images/avatars/avatar-'.$i.'.PNG') }}"
                                        alt="Avatar {{ $i }}"
                                        class="avatar-img"
                                        loading="lazy"
                                    />
                                </span>
                                <span class="avatar-check" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </label>
                        @endfor
                    </div>
                </div>



                {{-- Button Save --}}
                <button type="submit" class="auth-btn">
                    <span class="auth-btn__shine" aria-hidden="true"></span>
                    <span class="auth-btn__text">Simpan</span>
                    <span class="auth-btn__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M19 21H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h11l5 5v9a2 2 0 0 1-2 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M17 21v-8H7v8" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M7 5v4h8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </button>

            </form>

{{-- ✅ Tombol bawah: Kembali Belajar --}}
<div class="logout-form">
    <a href="{{ route('game.learn') }}" class="logout-btn" style="text-decoration:none;">
        <span class="logout-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <span>Kembali Belajar</span>
    </a>
</div>


        </div>
    </div>
</div>

<style>
/* =========================================================
   PLAYER PROFILE EDIT — LENTARA THEME (INTERNAL CSS)
   (MENGIKUTI STYLE REGISTER/LOGIN)
========================================================= */

/* fallback variable kalau navbar.css tidak ada */
html{
  --bg-body: #0b1220;
  --txt-body: rgba(226,232,240,.96);
  --muted: rgba(148,163,184,.92);
  --line: rgba(148,163,184,.20);
  --card: rgba(15,23,42,.74);

  --brand: #ff6b00;
  --brand2:#ff8c42;
  --brand3:#ffaa6b;
}

html[data-theme="light"]{
  --bg-body: #f8fafc;
  --txt-body: rgba(15,23,42,.95);
  --muted: rgba(71,85,105,.90);
  --line: rgba(15,23,42,.14);
  --card: rgba(255,255,255,.78);

  --brand: #ff6b00;
  --brand2:#ff8c42;
  --brand3:#ffaa6b;
}

.auth-page{
  min-height: 100vh;
  position: relative;
  overflow: hidden;
  background: var(--bg-body);
  color: var(--txt-body);
}

/* Banners */
.auth-banner{
  position: absolute;
  left: 0; right: 0;
  height: 96px;
  z-index: 0;
}
.auth-banner--top{ top: 0; }
.auth-banner--bottom{ bottom: 0; }

.auth-banner__img{
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  opacity: .92;
  filter: saturate(1.04);
}

.auth-banner__overlay{
  position: absolute;
  inset: 0;
  pointer-events: none;
}
.auth-banner__overlay--top{
  background: linear-gradient(to bottom, rgba(0,0,0,.22), rgba(0,0,0,.06), transparent);
}
.auth-banner__overlay--bottom{
  background: linear-gradient(to top, rgba(0,0,0,.26), rgba(0,0,0,.08), transparent);
}

/* Background ornaments */
.auth-bg{
  position: absolute;
  inset: 0;
  z-index: 0;
  pointer-events: none;
}

.auth-glow{
  position: absolute;
  width: 520px;
  height: 520px;
  border-radius: 999px;
  filter: blur(60px);
  opacity: .28;
}
.auth-glow--a{
  top: -160px;
  left: -160px;
  background: radial-gradient(circle, rgba(255,107,0,.75), transparent 60%);
}
.auth-glow--b{
  bottom: -180px;
  right: -160px;
  background: radial-gradient(circle, rgba(255,140,66,.70), transparent 60%);
}

.auth-grid{
  position: absolute;
  inset: 0;
  opacity: .10;
  background-image:
    linear-gradient(to right, rgba(255,107,0,.18) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(255,107,0,.14) 1px, transparent 1px);
  background-size: 44px 44px;
  mask-image: radial-gradient(circle at 50% 45%, #000 0%, rgba(0,0,0,.45) 45%, transparent 70%);
}

/* Center */
.auth-center{
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 22px;
  position: relative;
  z-index: 1;
}

/* Neon ring animation */
@property --auth-angle{
  syntax: "<angle>";
  inherits: false;
  initial-value: 0deg;
}
@keyframes authSpin { to { --auth-angle: 360deg; } }
@keyframes authTitleGlow { to { background-position: 200% center; } }

/* Card */
.auth-card{
  width: min(520px, 100%);
  border-radius: 24px;
  position: relative;
  overflow: hidden;
  background:
    linear-gradient(180deg,
      color-mix(in oklab, var(--card) 88%, transparent),
      color-mix(in oklab, var(--card) 95%, transparent)
    );
  border: 1px solid color-mix(in oklab, var(--line) 90%, transparent);
  box-shadow: 0 26px 90px rgba(0,0,0,.26);
  backdrop-filter: blur(14px);
  isolation: isolate;
}

/* Neon ring */
.auth-card::before{
  content:"";
  position:absolute;
  inset:0;
  border-radius: inherit;
  padding: 7px; /* ketebalan ring */
  pointer-events:none;
  z-index:0;

  background: conic-gradient(
    from var(--auth-angle),
    rgba(255,107,0,0) 0deg,
    rgba(255,107,0,.20) 28deg,
    #ff6b00 60deg,
    #22d3ee 120deg,
    #34d399 180deg,
    rgba(34,211,238,.18) 245deg,
    #ff8c42 315deg,
    rgba(255,107,0,0) 360deg
  );

  -webkit-mask:
    linear-gradient(#000 0 0) content-box,
    linear-gradient(#000 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;

  filter: blur(6px);
  opacity: .95;
  animation: authSpin 7.5s linear infinite;
}

/* ensure content above ring */
.auth-card > *{
  position: relative;
  z-index: 1;
}

/* Head */
.auth-head{
  padding: 22px 22px 10px;
  text-align: center;
}

.auth-badge{
  width: 52px;
  height: 52px;
  margin: 0 auto 10px;
  border-radius: 16px;
  display: grid;
  place-items: center;
  color: #fff;
  background: linear-gradient(135deg, var(--brand), var(--brand2));
  box-shadow: 0 18px 40px rgba(0,0,0,.22), 0 0 22px rgba(255,107,0,.18);
}
.auth-badge svg{ width: 26px; height: 26px; opacity: .95; }

.auth-title{
  margin: 0;
  font-size: 1.55rem;
  font-weight: 950;
  letter-spacing: .01em;
  line-height: 1.1;
  background: linear-gradient(90deg, var(--brand), var(--brand2), var(--brand3), var(--brand2), var(--brand));
  background-size: 200% auto;
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  animation: authTitleGlow 5.2s linear infinite;
}

.auth-subtitle{
  margin: 8px 0 0;
  font-size: .82rem;
  color: color-mix(in oklab, var(--muted) 92%, transparent);
}

/* Alerts */
.auth-error{
  margin: 0 22px 10px;
  padding: 10px 12px;
  border-radius: 14px;
  border: 1px solid rgba(239,68,68,.45);
  background: rgba(239,68,68,.12);
  display: flex;
  gap: 10px;
  align-items: flex-start;
  color: color-mix(in oklab, #fecaca 88%, white);
  font-size: .86rem;
}
.auth-error__dot{
  width: 10px;
  height: 10px;
  border-radius: 999px;
  margin-top: 4px;
  background: #ef4444;
  box-shadow: 0 0 14px rgba(239,68,68,.35);
}

.auth-success{
  margin: 0 22px 10px;
  padding: 10px 12px;
  border-radius: 14px;
  border: 1px solid rgba(52,211,153,.45);
  background: rgba(52,211,153,.10);
  display: flex;
  gap: 10px;
  align-items: flex-start;
  color: color-mix(in oklab, #bbf7d0 88%, white);
  font-size: .86rem;
}
.auth-success__dot{
  width: 10px;
  height: 10px;
  border-radius: 999px;
  margin-top: 4px;
  background: #34d399;
  box-shadow: 0 0 14px rgba(52,211,153,.28);
}

/* Form */
.auth-form{
  padding: 10px 22px 18px;
  display: grid;
  gap: 14px;
}

.auth-field{ display: grid; gap: 8px; }

.auth-labelRow{
  display:flex;
  align-items: baseline;
  justify-content: space-between;
  gap: 10px;
  flex-wrap: wrap;
}

.auth-hint{
  font-size: .78rem;
  color: color-mix(in oklab, var(--muted) 92%, transparent);
  opacity: .95;
}
.auth-hint code{
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
  font-size: .76rem;
  padding: 1px 6px;
  border-radius: 999px;
  border: 1px solid color-mix(in oklab, var(--line) 86%, transparent);
  background: color-mix(in oklab, var(--bg-body) 86%, transparent);
  color: color-mix(in oklab, var(--txt-body) 92%, transparent);
}

.auth-label{
  font-size: .86rem;
  font-weight: 850;
  color: color-mix(in oklab, var(--txt-body) 88%, transparent);
}

.auth-inputWrap{
  position: relative;
  border-radius: 16px;
  border: 1px solid rgba(255,107,0,.22);
  background: color-mix(in oklab, var(--bg-body) 86%, transparent);
  overflow: hidden;
  transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
}

.auth-inputWrap:focus-within{
  border-color: rgba(255,107,0,.58);
  box-shadow: 0 0 0 4px rgba(255,107,0,.16);
  transform: translateY(-1px);
}

.auth-ico{
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 34px;
  height: 34px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  color: var(--brand2);
  background: rgba(255,107,0,.10);
  border: 1px solid rgba(255,107,0,.18);
}
.auth-ico svg{ width: 18px; height: 18px; opacity: .95; }

.auth-input{
  width: 100%;
  border: 0;
  outline: none;
  background: transparent;
  color: var(--txt-body);
  padding: 12px 12px 12px 54px;
  font-size: .92rem;
}
.auth-input::placeholder{
  color: color-mix(in oklab, var(--muted) 92%, transparent);
}

/* Row */
.auth-row{
  display:flex;
  align-items:center;
  justify-content: space-between;
  gap: 14px;
  margin-top: 2px;
  flex-wrap: wrap;
}

.auth-back{
  font-size: .82rem;
  font-weight: 950;
  color: var(--brand2);
  text-decoration: none;
  transition: filter .2s ease, transform .2s ease;
}
.auth-back:hover{
  filter: saturate(1.12);
  transform: translateY(-1px);
}

.auth-mini{
  font-size: .78rem;
  color: color-mix(in oklab, var(--muted) 92%, transparent);
}

/* Button Save */
.auth-btn{
  margin-top: 6px;
  width: 100%;
  border: 0;
  cursor: pointer;
  border-radius: 999px;
  padding: 11px 14px;
  display:flex;
  align-items:center;
  justify-content:center;
  gap: 10px;

  background: linear-gradient(135deg, var(--brand), var(--brand2));
  color: #0b1220;
  font-weight: 950;
  letter-spacing: .01em;

  box-shadow: 0 18px 50px rgba(0,0,0,.22), 0 0 26px rgba(255,107,0,.16);
  position: relative;
  overflow: hidden;
  transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
}
.auth-btn:hover{
  transform: translateY(-2px);
  box-shadow: 0 24px 70px rgba(0,0,0,.26), 0 0 32px rgba(255,107,0,.20);
  filter: saturate(1.06);
}
.auth-btn:active{ transform: translateY(0px) scale(.99); }

.auth-btn__shine{
  position:absolute;
  inset:-2px;
  background: radial-gradient(220px 80px at 18% 0%, rgba(255,255,255,.40), transparent 60%);
  opacity: .0;
  transition: opacity .2s ease;
  pointer-events:none;
}
.auth-btn:hover .auth-btn__shine{ opacity: 1; }

.auth-btn__text{ font-size: .94rem; }
.auth-btn__icon{
  width: 22px;
  height: 22px;
  display:grid;
  place-items:center;
  border-radius: 999px;
  background: rgba(255,255,255,.30);
  border: 1px solid rgba(0,0,0,.08);
}
.auth-btn__icon svg{ width: 18px; height: 18px; color: rgba(2,6,23,.90); }

/* Avatar grid */
.avatar-grid{
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 12px;
}

.avatar-item{
  position: relative;
  width: 100%;
  aspect-ratio: 1 / 1; /* ✅ kotak sempurna */
  border-radius: 18px;
  border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
  background: color-mix(in oklab, var(--bg-body) 86%, transparent);
  padding: 20px;
  cursor: pointer;

  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center; /* ✅ CENTER VERTIKAL */

  gap: 6px;
  transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
  user-select: none;
  overflow: hidden;
}


.avatar-item:hover{
  transform: translateY(-2px);
  border-color: rgba(255,107,0,.45);
  box-shadow: 0 18px 50px rgba(0,0,0,.22), 0 0 18px rgba(255,107,0,.12);
}

.avatar-radio{
  position: absolute;
  inset: 0;
  opacity: 0;
  pointer-events: none;
}

.avatar-imgWrap{
  width: 140%;
  max-width: 86px;
  aspect-ratio: 1 / 1;
  border-radius: 16px;

  display: flex;
  align-items: center;
  justify-content: center; /* ✅ CENTER MATEMATIS */

  background: rgba(255,107,0,.06);
  border: 1px solid rgba(255,107,0,.14);
}


.avatar-img{
  width: 72px;
  height: 72px;
  object-fit: contain;
  display: block;
  margin: 0 auto;
  transform: translateZ(0); /* anti blur */
}


.avatar-name{
  font-size: .82rem;
  font-weight: 900;
  color: color-mix(in oklab, var(--txt-body) 92%, transparent);
  text-align: center;
  line-height: 1.1;
  margin-top: 4px;
}


/* Check badge */
.avatar-check{
  position: absolute;
  top: 10px;
  right: 10px;
  width: 26px;
  height: 26px;
  border-radius: 999px;
  display: grid;
  place-items: center;
  color: rgba(2,6,23,.92);
  background: linear-gradient(135deg, var(--brand), var(--brand2));
  box-shadow: 0 14px 32px rgba(0,0,0,.22), 0 0 18px rgba(255,107,0,.14);
  transform: scale(.92);
  opacity: 0;
  transition: transform .18s ease, opacity .18s ease;
}
.avatar-check svg{ width: 16px; height: 16px; }

/* Selected style: pure CSS via :has() */
.avatar-item:has(.avatar-radio:checked){
  border-color: rgba(255,107,0,.62);
  background: color-mix(in oklab, rgba(255,107,0,.10) 35%, var(--bg-body));
  box-shadow: 0 0 0 3px rgba(255,107,0,.22);
}

.avatar-item:has(.avatar-radio:checked) .avatar-check{
  opacity: 1;
  transform: scale(1);
}
.avatar-item:has(.avatar-radio:checked) .avatar-imgWrap{
  border-color: rgba(255,107,0,.45);
  background: rgba(255,107,0,.08);
}

/* Fallback for browsers without :has() (still works, just without fancy selected border) */
@supports not selector(:has(*)){
  .avatar-item[data-checked="1"]{
    border-color: rgba(255,107,0,.62);
    background: rgba(255,107,0,.08);
    box-shadow: 0 22px 70px rgba(0,0,0,.26), 0 0 26px rgba(255,107,0,.16);
  }
  .avatar-item[data-checked="1"] .avatar-check{
    opacity: 1;
    transform: scale(1);
  }
}

/* Logout */
.logout-form{
  padding: 0 22px 22px;
  margin-top: 8px;
}

.logout-btn{
  width: 100%;
  border-radius: 16px;
  padding: 11px 14px;
  border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
  background: color-mix(in oklab, var(--bg-body) 86%, transparent);
  color: color-mix(in oklab, var(--txt-body) 92%, transparent);
  font-weight: 950;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease, filter .18s ease;
}
.logout-btn:hover{
  transform: translateY(-2px);
  border-color: rgba(255,107,0,.35);
  box-shadow: 0 18px 60px rgba(0,0,0,.22);
  filter: saturate(1.06);
}
.logout-btn:active{ transform: translateY(0px) scale(.99); }

.logout-ico{
  width: 28px;
  height: 28px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  color: var(--brand2);
  background: rgba(255,107,0,.10);
  border: 1px solid rgba(255,107,0,.18);
}
.logout-ico svg{ width: 18px; height: 18px; }

/* Responsive */
@media (max-width: 640px){
  .avatar-grid{ grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 420px){
  .auth-head{ padding: 20px 18px 10px; }
  .auth-form{ padding: 10px 18px 16px; }
  .logout-form{ padding: 0 18px 18px; }
  .auth-title{ font-size: 1.42rem; }
  .avatar-imgWrap{ width: 84px; height: 84px; }
}
.avatar-name{ display:none !important; }
.avatar-item{ gap: 0; }

</style>
@endsection
