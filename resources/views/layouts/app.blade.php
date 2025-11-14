{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- penting buat fetch POST --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Lentara Islands</title>

    {{-- Tailwind via CDN (tanpa Vite) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- CSS Navbar --}}
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
</head>
<body class="text-slate-100 antialiased">
    <div class="min-h-screen flex flex-col">

        {{-- NAVBAR UTAMA --}}
        @include('partials.navbar')

        {{-- KONTEN HALAMAN --}}
        <main class="flex-1">
            @yield('hero')
            @yield('content')
        </main>

        <footer class="border-t border-slate-800 py-4 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} Lentara.
        </footer>
    </div>

    {{-- === CHATBOT FLOATING NUSANTARA AI (di luar container utama) === --}}
    @include('components.nusantara-chatbot')

    {{-- scripts tambahan dari child view --}}
    @stack('scripts')

    {{-- JS Navbar (theme toggle, drawer, indikator) --}}
    <script src="{{ asset('js/navbar.js') }}"></script>

    {{-- SCRIPT CHATBOT NUSANTARA AI --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn   = document.getElementById('nusantara-toggle');
    const panel       = document.getElementById('nusantara-panel');
    const closeBtn    = document.getElementById('nusantara-close');
    const form        = document.getElementById('nusantara-form');
    const input       = document.getElementById('nusantara-input');
    const messagesBox = document.getElementById('nusantara-messages');

    if (!toggleBtn || !panel) return;

    let messages = []; // riwayat chat
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // === FUNGSI BUKA/TUTUP DENGAN ANIMASI + HIDE/SHOW BUTTON ===
    function openPanel() {
        // sembunyikan tombol floating
        toggleBtn.classList.add('hidden');

        // tampilkan panel dengan animasi
        panel.classList.remove('pointer-events-none');
        panel.classList.add('opacity-100', 'translate-y-0', 'scale-100');
        panel.classList.remove('opacity-0', 'translate-y-2', 'scale-95');
    }

    function closePanel() {
        // animasi keluar dulu
        panel.classList.add('opacity-0', 'translate-y-2', 'scale-95');
        panel.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
        panel.classList.add('pointer-events-none');

        // setelah animasi, munculkan tombol floating lagi
        // (200ms = sama dengan duration-200)
        setTimeout(() => {
            toggleBtn.classList.remove('hidden');
        }, 200);
    }

    function togglePanel() {
        const isClosed = panel.classList.contains('pointer-events-none');
        if (isClosed) {
            openPanel();
        } else {
            closePanel();
        }
    }

    function addMessage(role, content) {
        const wrapper = document.createElement('div');
        wrapper.className = 'flex mb-1 ' + (role === 'user' ? 'justify-end' : 'justify-start');

        const bubble = document.createElement('div');
        bubble.className =
            'max-w-[80%] px-3 py-2 rounded-2xl text-xs leading-relaxed ' +
            (role === 'user'
                ? 'bg-amber-600 text-white rounded-br-sm'
                : 'bg-slate-800 text-slate-50 rounded-bl-sm');

        bubble.textContent = content;
        wrapper.appendChild(bubble);
        messagesBox.appendChild(wrapper);
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    // event tombol buka
    toggleBtn.addEventListener('click', () => {
        openPanel();
        // auto fokus input ketika dibuka
        setTimeout(() => input?.focus(), 220);
    });

    // event tombol close (X)
    closeBtn.addEventListener('click', () => {
        closePanel();
    });

    // submit form
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;

        // tampilkan pesan user
        addMessage('user', text);
        messages.push({ role: 'user', content: text });
        input.value = '';

        // tampilkan "sedang mengetik..."
        const loadingId = 'nusantara-loading-' + Date.now();
        const loadingWrapper = document.createElement('div');
        loadingWrapper.id = loadingId;
        loadingWrapper.className = 'flex mb-1 justify-start';
        loadingWrapper.innerHTML = `
            <div class="max-w-[80%] px-3 py-2 rounded-2xl text-xs bg-slate-800 text-slate-300 rounded-bl-sm">
                Nusantara AI sedang memikirkan jawabannya...
            </div>
        `;
        messagesBox.appendChild(loadingWrapper);
        messagesBox.scrollTop = messagesBox.scrollHeight;

        try {
            const res = await fetch('{{ route('nusantara.chat') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ messages }),
            });

            const data = await res.json();
            loadingWrapper.remove();

            if (data.reply) {
                messages.push({ role: 'assistant', content: data.reply });
                addMessage('assistant', data.reply);
            } else {
                addMessage('assistant', 'Maaf, Nusantara AI lagi bingung menjawab. Coba lagi ya.');
            }
        } catch (error) {
            console.error(error);
            loadingWrapper.remove();
            addMessage('assistant', 'Terjadi kesalahan jaringan. Coba lagi sebentar lagi ya.');
        }
    });
});
</script>
    
</body>
</html>
