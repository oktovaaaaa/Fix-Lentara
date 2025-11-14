{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('title', 'Login Admin')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-900 text-white">
    <div class="w-full max-w-md bg-slate-800/80 rounded-2xl p-6 shadow-xl border border-white/10">
        <h1 class="text-2xl font-semibold mb-4 text-center">Login Admin</h1>

        @if($errors->any())
            <div class="mb-4 text-sm text-red-300 bg-red-900/30 border border-red-600 rounded-lg px-3 py-2">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm mb-1" for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    value="{{ old('email') }}"
                    class="w-full px-3 py-2 rounded-lg bg-slate-900/70 border border-slate-600 focus:outline-none focus:ring-2 focus:ring-amber-400"
                    placeholder="admin@gmail.com"
                >
            </div>

            <div>
                <label class="block text-sm mb-1" for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    class="w-full px-3 py-2 rounded-lg bg-slate-900/70 border border-slate-600 focus:outline-none focus:ring-2 focus:ring-amber-400"
                    placeholder="••••••••"
                >
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded border-slate-600 bg-slate-900">
                    <span>Ingat saya</span>
                </label>

                <a href="{{ route('home') }}" class="text-amber-300 hover:text-amber-200 text-xs">
                    ← Kembali ke Beranda
                </a>
            </div>

            <button
                type="submit"
                class="w-full mt-2 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full bg-amber-400 text-slate-900 font-semibold hover:bg-amber-300 transition"
            >
                <span>Masuk</span>
            </button>
        </form>

        <p class="mt-4 text-xs text-center text-slate-400">
            Gunakan akun admin yang sudah di-seed:<br>
            <span class="font-mono">admin@gmail.com</span> / <span class="font-mono">password123</span>
        </p>
    </div>
</div>
@endsection
