<div
    id="nusantara-widget"
    class="fixed bottom-4 right-4 z-50 font-sans"
>
    {{-- TOMBOL KARTU MELAYANG --}}
    <button
        id="nusantara-toggle"
        class="flex items-center gap-3 px-4 py-3 rounded-full shadow-lg
               bg-gradient-to-r from-amber-600 via-orange-500 to-red-500
               text-white hover:shadow-xl transition transform hover:-translate-y-0.5
               w-full max-w-xs sm:max-w-sm"
    >
        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-white/20">
            <span class="text-xl">🌏</span>
        </div>

        <div class="flex-1 text-left">
            <div class="text-sm font-semibold leading-tight">Nusantara AI</div>
            <div class="text-[11px] opacity-80">
                Budaya Nusantara & Ekonomi Indonesia
            </div>
        </div>

        <span class="ml-2 text-xl font-bold">+</span>
    </button>

    {{-- PANEL CHAT (ABSOLUTE, TIDAK NGEDORONG TOMBOL) --}}
    <div
    id="nusantara-panel"
    class="fixed bottom-4 right-4
           w-[calc(100vw-2rem)] max-w-md rounded-3xl bg-slate-900 text-slate-50
           shadow-2xl overflow-hidden border border-slate-800
           transform origin-bottom-right
           transition-all duration-200 ease-out
           opacity-0 translate-y-2 scale-95
           pointer-events-none"
    >
        {{-- Header --}}
        <div class="px-4 py-3 bg-gradient-to-r from-amber-600 via-orange-500 to-red-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <span>🌏</span>
                    </div>
                    <div>
                        <div class="text-sm font-semibold">Nusantara AI</div>
                        <div class="text-[11px] text-white/80">
                            Tanya tentang budaya & ekonomi
                        </div>
                    </div>
                </div>
                <button
                    id="nusantara-close"
                    class="text-white/80 hover:text-white text-lg leading-none"
                >
                    ×
                </button>
            </div>
        </div>

        {{-- Body chat --}}
        <div class="flex flex-col h-80 sm:h-96">
            <div
                id="nusantara-messages"
                class="flex-1 px-4 py-3 space-y-2 overflow-y-auto text-sm bg-slate-950/60"
            >
                <div class="text-xs text-slate-400 text-center my-2">
                    Selamat datang di Nusantara AI 🇮🇩<br>
                    Tanya apa saja seputar budaya Nusantara dan pertumbuhan ekonomi Indonesia.
                </div>
            </div>

            {{-- Form input --}}
            <form id="nusantara-form" class="border-t border-slate-800 p-3 flex gap-2">
                @csrf
                <input
                    id="nusantara-input"
                    type="text"
                    placeholder="Tulis pertanyaanmu..."
                    class="flex-1 bg-slate-800/80 text-sm px-3 py-2 rounded-full
                           focus:outline-none focus:ring-2 focus:ring-amber-500"
                    autocomplete="off"
                />
                <button
                    type="submit"
                    class="px-3 py-2 rounded-full text-sm font-semibold
                           bg-amber-500 hover:bg-amber-600 text-white
                           disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    Kirim
                </button>
            </form>
        </div>
    </div>
</div>
