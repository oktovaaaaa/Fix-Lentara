{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.auth')

@section('title', 'Login Admin')

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
        <div class="auth-card" role="region" aria-label="Login Admin">

            {{-- Header --}}
            <div class="auth-head">
                <div class="auth-badge" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 2l7 4v6c0 5-3 9-7 10-4-1-7-5-7-10V6l7-4Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M9.5 12l1.7 1.8L14.7 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <h1 class="auth-title">Login Admin</h1>
            </div>

            {{-- Error --}}
            @if($errors->any())
                <div class="auth-error" role="alert">
                    <span class="auth-error__dot" aria-hidden="true"></span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login.post') }}" class="auth-form">
                @csrf

                <div class="auth-field">
                    <label class="auth-label" for="email">Email</label>
                    <div class="auth-inputWrap">
                        <span class="auth-ico" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                <path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            value="{{ old('email') }}"
                            placeholder=""
                            class="auth-input"
                            autocomplete="email"
                        >
                    </div>
                </div>

                <div class="auth-field">
                    <label class="auth-label" for="password">Password</label>
                    <div class="auth-inputWrap">
                        <span class="auth-ico" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M7 11V8a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M6 11h12v9H6v-9Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            placeholder=""
                            class="auth-input"
                            autocomplete="current-password"
                        >
                    </div>
                </div>

                <div class="auth-row">
                    <label class="auth-check">
                        <input type="checkbox" name="remember" class="auth-check__box">
                        <span class="auth-check__text">Ingat saya</span>
                    </label>

                    <a href="{{ route('home') }}" class="auth-back">← Kembali</a>
                </div>

                <button type="submit" class="auth-btn">
                    <span class="auth-btn__shine" aria-hidden="true"></span>
                    <span class="auth-btn__text">Masuk</span>
                    <span class="auth-btn__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M10 17l5-5-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4 12h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </span>
                </button>


            </form>

        </div>
    </div>
</div>

<style>
/* =========================================================
   AUTH LOGIN — LENTARA THEME (INTERNAL CSS)
   - Support dark/light via html[data-theme]
   - Neon ring + glass
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
  width: min(440px, 100%);
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
  padding: 7px; /* KETEBALAN RING (ubah ini kalau mau lebih tebal/tipis) */
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

/* Inner padding */
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

/* Error */
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

/* Form */
.auth-form{
  padding: 10px 22px 22px;
  display: grid;
  gap: 14px;
}

.auth-field{ display: grid; gap: 8px; }
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
}

.auth-check{
  display: inline-flex;
  align-items: center;
  gap: 10px;
}
.auth-check__box{
  width: 16px; height: 16px;
  border-radius: 6px;
  border: 1px solid color-mix(in oklab, var(--line) 100%, transparent);
  background: color-mix(in oklab, var(--bg-body) 80%, transparent);
  accent-color: var(--brand);
}
.auth-check__text{
  font-size: .82rem;
  color: color-mix(in oklab, var(--muted) 92%, transparent);
  font-weight: 800;
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

/* Button */
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

/* Footnote */
.auth-footnote{
  margin-top: 14px;
  display:grid;
  gap: 10px;
}
.auth-footnote__line{
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(255,107,0,.34), transparent);
}
.auth-footnote__text{
  text-align:center;
  font-size: .78rem;
  color: color-mix(in oklab, var(--muted) 92%, transparent);
  font-weight: 800;
}
.auth-mono{
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
  color: color-mix(in oklab, var(--txt-body) 90%, transparent);
}
.auth-dot{
  margin: 0 8px;
  opacity: .6;
}

/* Responsive */
@media (max-width: 420px){
  .auth-head{ padding: 20px 18px 10px; }
  .auth-form{ padding: 10px 18px 20px; }
  .auth-title{ font-size: 1.42rem; }
}
</style>
@endsection
