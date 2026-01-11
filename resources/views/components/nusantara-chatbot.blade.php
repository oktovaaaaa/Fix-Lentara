<div
    id="nusantara-widget"
    class="fixed bottom-4 right-4 z-50 font-sans"
>
    {{-- TOMBOL KARTU MELAYANG --}}
    <button
        id="nusantara-toggle"
        class="flex items-center gap-3 shadow-lg hover:shadow-xl
               transition-all duration-300 ease-out
               transform hover:-translate-y-1 active:scale-95
               w-16 h-16 rounded-full justify-center
               sm:w-auto sm:max-w-xs sm:justify-start sm:px-5 sm:py-3 sm:rounded-2xl
               bg-gradient-to-r from-amber-600 via-orange-500 to-red-500
               text-white border-2 border-white/30"
        aria-label="Buka Lentara AI"
    >
        {{-- ICON BESAR (DIPERBESAR) --}}
        <div class="flex items-center justify-center w-14 h-14 rounded-full
                   bg-white/30 backdrop-blur-sm shadow-inner">
            <img
                src="{{ asset('images/icon/lentaraai.PNG') }}"
                alt="Lentara AI"
                class="w-12 h-12 object-contain"
                draggable="false"
            />
        </div>

        {{-- TEKS: HANYA MUNCUL DI DESKTOP --}}
        <div class="hidden sm:block flex-1 text-left">
            <div class="text-sm font-bold leading-tight">Lentara AI</div>
            <div class="text-xs opacity-90">
                Budaya & Ekonomi Indonesia
            </div>
        </div>

        {{-- ICON PLUS --}}
        <span class="hidden sm:inline ml-2 text-xl font-bold opacity-80">+</span>
    </button>

    {{-- PANEL CHAT --}}
    <div
        id="nusantara-panel"
        class="fixed bottom-4 right-4
               w-[calc(100vw-2rem)] max-w-md h-[32rem] sm:h-[36rem]
               rounded-2xl sm:rounded-3xl
               shadow-2xl overflow-hidden border
               transform origin-bottom-right
               transition-all duration-300 ease-out
               opacity-0 translate-y-4 scale-95
               pointer-events-none"
    >
        {{-- Header --}}
        <div class="px-5 py-4 bg-gradient-to-r from-amber-600 via-orange-500 to-red-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    {{-- ICON BESAR di header (DIPERBESAR) --}}
                    <div class="w-16 h-16 rounded-full
                               bg-white/30 backdrop-blur-sm flex items-center justify-center
                               shadow-lg ring-2 ring-white/30">
                        <img
                            src="{{ asset('images/icon/lentaraai.PNG') }}"
                            alt="Lentara AI"
                            class="w-14 h-14 object-contain"
                            draggable="false"
                        />
                    </div>
                    <div>
                        <div class="font-bold text-white text-lg">Lentara AI</div>
                        <div class="text-xs text-white/90">
                            Asisten Budaya & Ekonomi Lentara
                        </div>
                    </div>
                </div>
                <button
                    id="nusantara-close"
                    class="w-10 h-10 rounded-full flex items-center justify-center
                           bg-white/20 hover:bg-white/30 text-white transition-colors"
                    aria-label="Tutup"
                    type="button"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body chat --}}
        <div class="flex flex-col h-[calc(100%-5rem)]">
            <div
                id="nusantara-messages"
                class="flex-1 px-4 py-4 overflow-y-auto
                       scrollbar-thin scrollbar-thumb-amber-500/20 scrollbar-track-transparent"
            >
                {{-- Pesan selamat datang --}}
                <div class="text-center mb-4">
                    <div class="inline-flex flex-col items-center gap-3
                               px-5 py-4 rounded-2xl
                               max-w-xs mx-auto nusai-welcome">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center
                                   bg-gradient-to-r from-amber-600 to-red-500
                                   p-2 border-2 border-white/30">
                            <img
                                src="{{ asset('images/icon/lentaraai.PNG') }}"
                                alt="Lentara AI"
                                class="w-full h-full object-contain"
                                draggable="false"
                            />
                        </div>
                        <div>
                            <div class="font-bold text-lg nusai-title">
                                Selamat datang! 👋
                            </div>
                            <div class="text-sm mt-1 nusai-subtitle">
                                Tanya tentang budaya atau sejarah Indonesia
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Chat messages akan muncul di sini --}}
            </div>

            {{-- Form input --}}
            <div class="p-4 border-t nusai-inputbar">
                <form id="nusantara-form" class="flex gap-2">
                    @csrf
                    <input type="hidden" id="chat-endpoint" value="{{ route('nusantara.chat') }}">

                    <div class="flex-1 relative">
                        <input
                            id="nusantara-input"
                            type="text"
                            placeholder="Tulis pertanyaanmu..."
                            class="w-full text-sm px-4 py-3 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-amber-500
                                   pr-12 nusai-input"
                            autocomplete="off"
                        />
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 nusai-input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <button
                        type="submit"
                        class="px-4 py-3 rounded-xl text-sm font-bold
                               bg-gradient-to-r from-amber-500 to-red-500
                               hover:opacity-90 text-white
                               disabled:opacity-50 disabled:cursor-not-allowed
                               transition-all duration-200
                               shadow-lg hover:shadow-xl
                               transform hover:-translate-y-0.5 active:translate-y-0
                               flex items-center justify-center min-w-[44px]"
                        id="send-button"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>

                <div class="text-xs text-center mt-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full nusai-footer-pill">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs">Lentara AI • Piforrr</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* =========================================================
       NUSANTARA AI THEME (FOLLOW :root & html[data-theme="dark"])
       - Pakai variabel global: --bg-body, --txt-body, --card, --line, --muted, --brand, --brand-2, --shadow
       - Tidak bergantung pada class .dark (karena kamu pakai data-theme)
    ========================================================= */

    /* ===== PANEL BASE ===== */
    #nusantara-panel {
        background: var(--card);
        color: var(--txt-body);
        border-color: color-mix(in oklab, var(--line) 90%, transparent);
        box-shadow: var(--shadow);
    }

    /* ===== BODY BACKDROP / GRADIENT HALUS ===== */
    #nusantara-messages {
        background:
            radial-gradient(900px 600px at 85% 0%,
                color-mix(in oklab, var(--brand) 18%, transparent),
                transparent 55%),
            radial-gradient(900px 600px at 10% 100%,
                color-mix(in oklab, var(--brand-2) 14%, transparent),
                transparent 55%),
            linear-gradient(180deg,
                color-mix(in oklab, var(--bg-body) 55%, transparent),
                color-mix(in oklab, var(--bg-body) 85%, transparent));
    }

    /* ===== WELCOME CARD ===== */
    .nusai-welcome {
        background:
            linear-gradient(145deg,
                color-mix(in oklab, var(--card) 92%, transparent),
                color-mix(in oklab, var(--bg-body) 70%, transparent));
        border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
        box-shadow: 0 10px 25px color-mix(in oklab, #000 10%, transparent);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .nusai-title { color: var(--txt-body); }
    .nusai-subtitle { color: color-mix(in oklab, var(--txt-body) 70%, var(--muted)); }

    /* ===== INPUT BAR ===== */
    .nusai-inputbar {
        background: color-mix(in oklab, var(--card) 92%, transparent);
        border-top-color: color-mix(in oklab, var(--line) 95%, transparent);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    .nusai-input {
        background: color-mix(in oklab, var(--bg-body) 70%, var(--card));
        color: var(--txt-body);
        border: 1px solid color-mix(in oklab, var(--line) 95%, transparent);
        box-shadow: inset 0 1px 0 color-mix(in oklab, #fff 30%, transparent);
    }
    .nusai-input::placeholder {
        color: color-mix(in oklab, var(--muted) 90%, transparent);
    }
    .nusai-input:focus {
        border-color: color-mix(in oklab, var(--brand) 55%, transparent);
    }
    .nusai-input-icon {
        color: color-mix(in oklab, var(--muted) 90%, transparent);
    }

    /* ===== FOOTER PILL ===== */
    .nusai-footer-pill {
        background: linear-gradient(135deg,
            color-mix(in oklab, var(--brand) 14%, transparent),
            color-mix(in oklab, var(--brand-2) 12%, transparent));
        border: 1px solid color-mix(in oklab, var(--line) 90%, transparent);
        color: color-mix(in oklab, var(--txt-body) 70%, var(--muted));
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    /* ===== Scrollbar (tetap) ===== */
    .scrollbar-thin { scrollbar-width: thin; }
    .scrollbar-thin::-webkit-scrollbar { width: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: color-mix(in oklab, var(--brand) 35%, transparent);
        border-radius: 3px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: color-mix(in oklab, var(--brand) 55%, transparent);
    }

    /* ===== Bubble chat (MENYESUAIKAN THEME) ===== */
    .user-bubble {
        background: linear-gradient(135deg,
            color-mix(in oklab, var(--brand) 92%, #0000),
            color-mix(in oklab, #dc2626 70%, var(--brand)));
        color: #fff;
        border-radius: 18px 18px 4px 18px;
        padding: 12px 16px;
        max-width: 85%;
        margin-left: auto;
        box-shadow: 0 10px 25px color-mix(in oklab, #000 18%, transparent);
        border: 1px solid rgba(255,255,255,0.20);
    }

    .ai-bubble {
        background: color-mix(in oklab, var(--card) 92%, var(--bg-body));
        color: var(--txt-body);
        border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
        border-radius: 18px 18px 18px 4px;
        padding: 12px 16px;
        max-width: 85%;
        box-shadow: 0 6px 18px color-mix(in oklab, #000 10%, transparent);
        white-space: pre-line;
        line-height: 1.5;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    /* Timestamp */
    .message-time {
        font-size: 11px;
        opacity: 0.75;
        margin-top: 4px;
    }
    .user-bubble .message-time {
        text-align: right;
        color: rgba(255,255,255,0.85);
    }
    .ai-bubble .message-time {
        color: color-mix(in oklab, var(--muted) 90%, transparent);
    }

    /* Animasi bubble */
    @keyframes messageIn {
        from { opacity: 0; transform: translateY(10px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .message-in { animation: messageIn 0.3s ease-out; }

    /* Typing indicator (MENYESUAIKAN THEME) */
    .typing-indicator {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 12px 16px;
        background: color-mix(in oklab, var(--card) 92%, var(--bg-body));
        border-radius: 18px;
        width: fit-content;
        border: 1px solid color-mix(in oklab, var(--line) 92%, transparent);
        margin-left: 8px;
        box-shadow: 0 6px 18px color-mix(in oklab, #000 10%, transparent);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        color: var(--txt-body);
    }

    .typing-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: color-mix(in oklab, var(--muted) 90%, transparent);
        animation: typing 1.4s infinite ease-in-out;
    }
    .typing-dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-dot:nth-child(2) { animation-delay: -0.16s; }
    .typing-dot:nth-child(3) { animation-delay: 0s; }

    @keyframes typing {
        0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
        40% { transform: scale(1.2); opacity: 1; }
    }

    /* Spinner */
    .spinner {
        width: 16px;
        height: 16px;
        border: 2px solid color-mix(in oklab, var(--line) 70%, transparent);
        border-top: 2px solid color-mix(in oklab, var(--brand) 80%, transparent);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('nusantara-toggle');
    const panel = document.getElementById('nusantara-panel');
    const closeBtn = document.getElementById('nusantara-close');
    const form = document.getElementById('nusantara-form');
    const input = document.getElementById('nusantara-input');
    const messages = document.getElementById('nusantara-messages');
    const sendButton = document.getElementById('send-button');
    const chatEndpoint = document.getElementById('chat-endpoint').value;

    let isOpen = false;
    let chatHistory = [];

    // Toggle panel
    toggleBtn.addEventListener('click', () => {
        isOpen = !isOpen;
        if (isOpen) {
            panel.classList.remove('opacity-0', 'translate-y-4', 'scale-95', 'pointer-events-none');
            panel.classList.add('opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto');
            input.focus();

            setTimeout(() => {
                messages.scrollTop = messages.scrollHeight;
            }, 100);
        } else {
            panel.classList.remove('opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto');
            panel.classList.add('opacity-0', 'translate-y-4', 'scale-95', 'pointer-events-none');
        }
    });

    // Close panel
    closeBtn.addEventListener('click', () => {
        isOpen = false;
        panel.classList.remove('opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto');
        panel.classList.add('opacity-0', 'translate-y-4', 'scale-95', 'pointer-events-none');
    });

    // Handle form submission - MENGIRIM KE CONTROLLER LARAVEL
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = input.value.trim();
        if (!message) return;

        // Add user message ke UI
        addMessage(message, 'user');

        // Tambah ke history
        chatHistory.push({ role: 'user', content: message });

        input.value = '';
        input.disabled = true;
        sendButton.disabled = true;
        sendButton.innerHTML = '<div class="spinner"></div>';

        // Show typing indicator
        showTypingIndicator();

        try {
            // Kirim ke controller Laravel
            const response = await fetch(chatEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    messages: chatHistory
                })
            });

            const data = await response.json();

            // Remove typing indicator
            removeTypingIndicator();

            if (response.ok) {
                // Tambah response AI ke UI
                addMessage(data.reply, 'ai');

                // Tambah ke history
                chatHistory.push({ role: 'assistant', content: data.reply });
            } else {
                // Error handling
                addMessage('Maaf, terjadi kesalahan. Silakan coba lagi.', 'ai');
                console.error('Error:', data);
            }

        } catch (error) {
            removeTypingIndicator();
            addMessage('Maaf, koneksi internet bermasalah. Silakan coba lagi.', 'ai');
            console.error('Fetch error:', error);
        } finally {
            input.disabled = false;
            sendButton.disabled = false;
            sendButton.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            `;
            input.focus();
        }
    });

    // Auto-detect enter untuk submit
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
    });

    // Close on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen) {
            closeBtn.click();
        }
    });

    // Click outside to close
    document.addEventListener('click', (e) => {
        if (isOpen &&
            !panel.contains(e.target) &&
            !toggleBtn.contains(e.target) &&
            panel.classList.contains('opacity-100')) {
            closeBtn.click();
        }
    });

    // Helper functions
    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message-in flex ${sender === 'user' ? 'justify-end' : 'justify-start'} mb-4`;

        if (sender === 'user') {
            messageDiv.innerHTML = `
                <div class="user-bubble">
                    <div class="font-medium">${escapeHtml(text)}</div>
                    <div class="message-time">${getCurrentTime()}</div>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="flex items-start gap-3 max-w-full">
                    <div class="w-10 h-10 rounded-full flex-shrink-0 mt-1
                               bg-gradient-to-r from-amber-600 to-red-500
                               p-1 border border-white/20">
                        <img src="{{ asset('images/icon/lentaraai.PNG') }}"
                             alt="AI" class="w-full h-full object-contain">
                    </div>
                    <div class="ai-bubble">
                        <div class="font-medium whitespace-pre-line">${escapeHtml(text)}</div>
                        <div class="message-time">${getCurrentTime()}</div>
                    </div>
                </div>
            `;
        }

        messages.appendChild(messageDiv);
        messages.scrollTop = messages.scrollHeight;
    }

    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'typing-indicator mb-4';
        typingDiv.id = 'typing-indicator';
        typingDiv.innerHTML = `
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <span class="text-xs ml-2" style="color: color-mix(in oklab, var(--muted) 90%, transparent);">
                Lentara AI sedang mengetik...
            </span>
        `;
        messages.appendChild(typingDiv);
        messages.scrollTop = messages.scrollHeight;
    }

    function removeTypingIndicator() {
        const typing = document.getElementById('typing-indicator');
        if (typing) typing.remove();
    }

    function getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Tambahkan welcome message ke history
    chatHistory.push({
        role: 'assistant',
        content: 'Halo! Saya Lentara AI, asisten digital khusus untuk membahas budaya, ekonomi, dan kekayaan Lentara. Ada yang bisa saya bantu?'
    });
});
</script>
