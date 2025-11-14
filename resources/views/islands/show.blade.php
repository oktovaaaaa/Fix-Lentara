{{-- resources/views/islands/show.blade.php --}}
@extends('layouts.app')

@section('title', ($island->title ?? $island->name) . ' – Lentara')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-10">
        <div class="mb-6">
            <a href="{{ route('landing') }}"
               class="inline-flex items-center gap-2 text-sm text-slate-300 hover:text-emerald-300 transition">
                <span>←</span>
                Kembali ke halaman utama
            </a>
        </div>

        <header class="mb-6">
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-300 mb-2">
                Pulau Lentara
            </p>
            <h1 class="text-3xl md:text-4xl font-bold mb-2">
                {{ $island->title ?? $island->name }}
                @if($island->subtitle)
                    <span class="block text-emerald-300">{{ $island->subtitle }}</span>
                @endif
            </h1>
            @if($island->place_label)
                <p class="text-slate-300">{{ $island->place_label }}</p>
            @endif
        </header>

        @if($island->image_url)
            <div class="mb-8 rounded-3xl overflow-hidden border border-slate-800 bg-slate-900">
                <img
                    src="{{ $island->image_url }}"
                    alt="{{ $island->title ?? $island->name }}"
                    class="w-full h-[260px] md:h-[360px] object-cover"
                >
            </div>
        @endif

        <div class="grid md:grid-cols-3 gap-8">
            <article class="md:col-span-2 space-y-4 text-slate-200 leading-relaxed">
                @if($island->short_description)
                    <p class="text-lg text-slate-100 font-medium">
                        {{ $island->short_description }}
                    </p>
                @else
                    <p class="text-slate-400 text-sm">
                        Deskripsi singkat belum diisi untuk pulau ini.
                    </p>
                @endif
            </article>

            <aside class="space-y-4">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-4">
                    <h2 class="text-sm font-semibold text-slate-100 mb-3">
                        Info Pulau
                    </h2>
                    <dl class="space-y-2 text-sm text-slate-300">
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-400">Nama internal</dt>
                            <dd class="text-right">{{ $island->name }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-400">Slug</dt>
                            <dd class="text-right text-xs">{{ $island->slug }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-400">Aktif</dt>
                            <dd class="text-right">{{ $island->is_active ? 'Ya' : 'Tidak' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-400">Urutan</dt>
                            <dd class="text-right">{{ $island->order ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </aside>
        </div>
    </div>
@endsection

