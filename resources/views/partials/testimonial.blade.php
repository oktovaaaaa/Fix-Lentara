{{-- ================= TESTIMONI ================= --}}
<section id="testimoni" class="py-12">
    @php
        $testimonials = $testimonials ?? collect();
        $testimonialStats = $testimonialStats ?? ['counts'=>[1=>0,2=>0,3=>0,4=>0,5=>0],'total'=>0,'avg'=>0];

        $counts = $testimonialStats['counts'];
        $total  = (int) $testimonialStats['total'];
        $avg    = (float) $testimonialStats['avg'];

        // helper % bar
        $pct = function($n) use ($total) {
            return $total > 0 ? round(($n / $total) * 100) : 0;
        };
    @endphp

    <style>
        /* =========================================================
           TESTIMONI THEME SAFE (LIGHT/DARK) + FIX OVERFLOW + NO CIRCLE STAR
           FIXES:
           1) Bintang rating tanpa lingkaran / tanpa tombol bulat
           2) Teks komentar tidak boleh keluar card: wrap panjang (wwww...)
           3) Flex child tidak overflow: min-width:0
        ========================================================= */

        .t-wrap { color: var(--txt-body); }

        .t-card {
            background: linear-gradient(145deg, var(--card), color-mix(in oklab, var(--card-bg-dark) 90%, transparent));
            border: 1px solid rgba(255, 107, 0, 0.2);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            border-radius: 20px;
            padding: 1.5rem;
        }

        /* Dark/Light mode adjustment */
        html[data-theme="dark"] .t-card {
            background: linear-gradient(145deg, #111827, #020617);
        }
        html[data-theme="light"] .t-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
        }

        .t-soft {
            background: linear-gradient(145deg, var(--card), color-mix(in oklab, var(--card-bg-dark) 90%, transparent));
            border: 1px solid rgba(255, 107, 0, 0.1);
            border-radius: 16px;
            padding: 1.25rem;
        }

        html[data-theme="dark"] .t-soft { background: linear-gradient(145deg, #111827, #020617); }
        html[data-theme="light"] .t-soft { background: linear-gradient(145deg, #ffffff, #f8fafc); }

        .t-muted { color: var(--muted); }

        .t-input, .t-textarea {
            width: 100%;
            border-radius: 12px;
            border: 1px solid rgba(255, 107, 0, 0.2);
            background: color-mix(in oklab, var(--bg-body) 95%, transparent);
            color: var(--txt-body);
            padding: 12px 14px;
            outline: none;
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        }

        html[data-theme="dark"] .t-input,
        html[data-theme="dark"] .t-textarea { background: rgba(255, 255, 255, 0.05); }

        html[data-theme="light"] .t-input,
        html[data-theme="light"] .t-textarea { background: rgba(0, 0, 0, 0.02); }

        .t-input::placeholder, .t-textarea::placeholder { color: rgba(156, 163, 175, 0.7); }

        .t-input:focus, .t-textarea:focus {
            border-color: #ff6b00;
            box-shadow: 0 0 0 4px rgba(255, 107, 0, 0.2);
            transform: translateY(-1px);
        }

        /* ===============================
           RATING STARS (CLICK) - NO CIRCLE
           =============================== */
        .star-row {
            display: inline-flex;
            gap: 10px;
            align-items: center;
        }

        /* tombolnya tetap button biar aksesibel, tapi TANPA lingkaran/background */
        .star-btn {
            width: auto;
            height: auto;
            border: 0;
            background: transparent;
            padding: 0;
            cursor: pointer;
            user-select: none;
            line-height: 1;
        }

        .star-btn:focus { outline: none; }
        .star-btn:focus-visible {
            outline: 2px solid rgba(255,107,0,.55);
            outline-offset: 4px;
            border-radius: 10px;
        }

        .star {
            font-size: 28px;
            line-height: 1;
            color: rgba(156, 163, 175, 0.55);
            transition: transform .12s ease, color .18s ease, filter .18s ease;
            display: inline-block;
        }

        .star-btn:hover .star {
            transform: translateY(-1px) scale(1.08);
            filter: drop-shadow(0 10px 20px rgba(255,107,0,.25));
        }

        .star.is-on { color: #f59e0b; transform: scale(1.06); }

        /* Progress bars */
        .t-bar {
            height: 12px;
            border-radius: 999px;
            background: rgba(255, 107, 0, 0.1);
            overflow: hidden;
        }
        .t-bar > span {
            display: block;
            height: 100%;
            width: 0%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f59e0b, #ff6b00);
            transition: width .5s ease;
        }

        /* Scroll area */
        .t-scroll {
            max-height: 480px;
            overflow: auto;
            padding-right: 8px;
        }
        .t-scroll::-webkit-scrollbar { width: 10px; }
        .t-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 107, 0, 0.3);
            border-radius: 999px;
        }

        .t-file {
            width: 100%;
            border-radius: 12px;
            border: 2px dashed rgba(255, 107, 0, 0.3);
            background: rgba(255, 107, 0, 0.05);
            color: var(--txt-body);
            padding: 12px 14px;
            transition: all .18s ease;
        }
        .t-file:hover {
            border-color: #ff6b00;
            background: rgba(255, 107, 0, 0.1);
        }

        .t-btn {
            border-radius: 14px;
            padding: 14px 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #ff6b00, #ff8c42);
            color: white;
            border: 0;
            box-shadow: 0 18px 30px rgba(255, 107, 0, 0.25);
            transition: all .18s ease;
            font-size: 1rem;
        }
        .t-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 35px rgba(255, 107, 0, 0.35);
            filter: brightness(1.1);
        }
        .t-btn:active { transform: translateY(0px); }

        /* small meta in card */
        .t-chip {
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid rgba(255, 107, 0, 0.3);
            background: rgba(255, 107, 0, 0.1);
            color: #ff8c42;
            font-weight: 600;
        }

        /* Feedback items */
        .feedback-item {
            background: color-mix(in oklab, var(--card) 95%, transparent);
            border: 1px solid rgba(255, 107, 0, 0.1);
            overflow: hidden; /* jaga-jaga glow/teks panjang */
        }
        html[data-theme="dark"] .feedback-item { background: rgba(255, 255, 255, 0.03); }
        html[data-theme="light"] .feedback-item { background: rgba(0, 0, 0, 0.01); }

        /* ===============================
           FIX OVERFLOW TEKS PANJANG (WWWW...)
           =============================== */

        /* Penting: flex child harus min-width:0 supaya wrap jalan */
        .feedback-item .flex-1 { min-width: 0; }

        /* Nama & teks: jangan overflow */
        .t-name,
        .t-comment,
        .t-anywhere {
            overflow-wrap: anywhere; /* modern */
            word-break: break-word;  /* fallback */
        }

        /* Pesan: biar kalau ada enter tetap rapi, dan panjang menyambung ke bawah */
        .t-comment {
            white-space: pre-wrap;
        }

        /* ===============================
           FIX: MODAL REPORT DARK MODE
           (select & dropdown option list)
        =============================== */

        /* Modal card: pastikan benar-benar dark saat dark mode */
        html[data-theme="dark"] #reportModal .t-card{
            background: linear-gradient(145deg, #0b1220, #020617) !important;
            border: 1px solid rgba(255,107,0,.22) !important;
            box-shadow: 0 26px 70px rgba(0,0,0,.55) !important;
        }

        /* Teks di modal biar kontras */
        html[data-theme="dark"] #reportModal h3,
        html[data-theme="dark"] #reportModal label{
            color: rgba(255,255,255,.92) !important;
        }
        html[data-theme="dark"] #reportModal .t-muted{
            color: rgba(255,255,255,.62) !important;
        }

        /* Select di modal (field dropdown) */
        #reportModal select.t-input{
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-clip: padding-box;
        }

        /* Dark mode: paksa background & text untuk select */
        html[data-theme="dark"] #reportModal select.t-input{
            background-color: rgba(255,255,255,.06) !important;
            color: rgba(255,255,255,.92) !important;
            border-color: rgba(255,107,0,.28) !important;
        }

        /* Dark mode: paksa background & text untuk OPTION LIST (dropdown) */
        html[data-theme="dark"] #reportModal select.t-input option{
            background-color: #0b1220 !important;
            color: rgba(255,255,255,.92) !important;
        }

        html[data-theme="dark"] #reportModal select.t-input option:checked,
        html[data-theme="dark"] #reportModal select.t-input option:hover{
            background-color: #111b2f !important;
            color: rgba(255,255,255,.95) !important;
        }

        html[data-theme="dark"] #reportModal select.t-input:focus{
            border-color: #ff6b00 !important;
            box-shadow: 0 0 0 4px rgba(255,107,0,.22) !important;
        }

        /* Textarea di modal juga dipaksa dark biar konsisten */
        html[data-theme="dark"] #reportModal textarea.t-textarea{
            background-color: rgba(255,255,255,.06) !important;
            color: rgba(255,255,255,.92) !important;
            border-color: rgba(255,107,0,.22) !important;
        }
        html[data-theme="dark"] #reportModal textarea.t-textarea::placeholder{
            color: rgba(255,255,255,.45) !important;
        }

        /* Tombol Batal (yang pakai t-input) di modal biar gak kelihatan seperti input light */
        html[data-theme="dark"] #reportModal button.t-input{
            background-color: rgba(255,255,255,.06) !important;
            color: rgba(255,255,255,.88) !important;
            border-color: rgba(255,107,0,.18) !important;
        }
        html[data-theme="dark"] #reportModal button.t-input:hover{
            background-color: rgba(255,255,255,.09) !important;
            border-color: rgba(255,107,0,.28) !important;
        }
    </style>

    <div class="t-wrap">
        <h2 class="neon-title">
            Testimoni Pengunjung
        </h2>
        <div class="title-decoration"></div>
        <p class="neon-subtitle">
            Bagikan pengalamanmu, bantu kami jadi lebih baik.
        </p>

        {{-- ===== SUMMARY (TOP) ===== --}}
        <div class="grid gap-6 lg:grid-cols-3 mb-8">
            {{-- Left: distribution --}}
            <div class="t-card lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-bold text-lg">Ringkasan Rating</div>
                    <div class="t-chip">{{ $total }} Rating</div>
                </div>

                @for($r = 5; $r >= 1; $r--)
                    @php $p = $pct($counts[$r]); @endphp
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-20 text-sm font-bold tracking-wide" style="color: #ff8c42;">
                            {{ $r }} ★
                        </div>
                        <div class="flex-1 t-bar">
                            <span style="width: {{ $p }}%"></span>
                        </div>
                        <div class="w-16 text-right text-sm font-semibold" style="color: #ff8c42;">
                            {{ $counts[$r] }} ({{ $p }}%)
                        </div>
                    </div>
                @endfor
            </div>

            {{-- Right: average --}}
            <div class="t-card flex flex-col items-center justify-center text-center">
                <div class="text-5xl font-extrabold" style="color: #ff6b00;">
                    {{ number_format($avg, 1) }}
                </div>

                <div class="mt-3 flex items-center justify-center gap-1">
                    @php
                        $rounded = (int) round($avg);
                    @endphp
                    @for($i=1; $i<=5; $i++)
                        <span class="text-3xl" style="color: {{ $i <= $rounded ? '#f59e0b' : 'rgba(156, 163, 175, 0.3)' }};">★</span>
                    @endfor
                </div>

                <div class="mt-2 t-muted text-sm">
                    Dari {{ $total }} rating
                </div>
            </div>
        </div>

        {{-- ===== MAIN (BOTTOM): left list + right form ===== --}}
        <div class="grid gap-6 lg:grid-cols-2">
            {{-- LEFT: recent feedbacks --}}
            <div class="t-card">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-bold text-xl">Recent Feedbacks</div>
                    <div class="t-chip">Terbaru</div>
                </div>

                <div class="t-scroll space-y-4">
                    @forelse($testimonials as $t)
                        <div class="t-soft feedback-item">
                            <div class="flex items-start gap-4">
                                <img
                                    class="w-16 h-16 rounded-full object-cover border border-[#ff6b00]"
                                    src="{{ $t->photo ? asset('storage/'.$t->photo) : asset('images/avatar.png') }}"
                                    alt="Avatar {{ $t->name }}"
                                />

                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-lg t-name t-anywhere">{{ $t->name }}</div>
                                            <div class="t-muted text-sm">
                                                {{ $t->created_at->translatedFormat('d F Y') }}
                                            </div>
                                        </div>

                                        <div class="text-right flex-shrink-0">
                                            <div class="flex justify-end gap-0.5">
                                                @for($i=1; $i<=5; $i++)
                                                    <span style="color: {{ $i <= $t->rating ? '#f59e0b' : 'rgba(156, 163, 175, 0.3)' }};">★</span>
                                                @endfor
                                            </div>
                                            <div class="t-muted text-xs mt-1">
                                                {{ $t->created_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>

                                    <p class="mt-3 text-sm leading-relaxed t-muted t-comment">
                                        {{ $t->message }}
                                    </p>

                                    <div class="mt-4 flex justify-end">
                                        <button
                                            type="button"
                                            onclick="openReportModal('{{ route('testimonials.report', $t) }}')"
                                            class="text-xs font-bold text-red-500 hover:underline">
                                            Laporkan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="t-muted text-center py-8">Belum ada testimoni. Jadilah yang pertama 😊</div>
                    @endforelse
                </div>
            </div>

            {{-- RIGHT: add a review --}}
            <div class="t-card">
                <div class="font-bold text-xl mb-4">Add a Review</div>

                {{-- flash message --}}
                @if(session('success'))
                    <div class="mb-4 t-soft p-4 text-sm" style="border-color: #ff6b00;">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                {{-- error bag --}}
                @if($errors->any())
                    <div class="mb-4 t-soft p-4 text-sm" style="border-color: #ef4444;">
                        <div class="font-bold mb-2">Gagal mengirim:</div>
                        <ul class="list-disc list-inside t-muted space-y-1">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('testimonials.store') }}"
                      enctype="multipart/form-data"
                      class="space-y-4"
                      id="testimonialForm">
                    @csrf

                    {{-- Honeypot --}}
                    <input type="text" name="website" value="" autocomplete="off" tabindex="-1"
                           style="position:absolute;left:-9999px;top:-9999px;height:1px;width:1px;opacity:0;">

                    {{-- Rating --}}
                    <div>
                        <label class="text-sm font-bold">Rating <span class="text-red-500">*</span></label>
                        <input type="hidden" name="rating" id="ratingValue" value="{{ old('rating', 0) }}">

                        <div class="mt-2 star-row" id="starRow" aria-label="Pilih rating bintang">
                            @for($i=1; $i<=5; $i++)
                                <button
                                    type="button"
                                    class="star-btn"
                                    data-star="{{ $i }}"
                                    aria-label="Pilih {{ $i }} bintang"
                                    title="Pilih {{ $i }} bintang"
                                >
                                    <span class="star">★</span>
                                </button>
                            @endfor
                        </div>
                        <div class="t-muted text-xs mt-2">Klik bintang untuk memilih rating.</div>
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label class="text-sm font-bold">Nama <span class="text-red-500">*</span></label>
                        <input class="t-input" name="name" value="{{ old('name') }}" placeholder="Nama kamu">
                    </div>

                    {{-- Pesan --}}
                    <div>
                        <label class="text-sm font-bold">Pesan <span class="text-red-500">*</span></label>
                        <textarea class="t-textarea" name="message" rows="5" placeholder="Tulis pengalamanmu...">{{ old('message') }}</textarea>
                    </div>

                    {{-- Foto (opsional) --}}
                    <div>
                        <label class="text-sm font-bold">Foto Profil <span class="t-muted text-xs">(opsional, max 5MB)</span></label>
                        <input class="t-file" type="file" name="photo" accept="image/png,image/jpeg,image/jpg">
                        <div class="t-muted text-xs mt-2">Format: JPG / JPEG / PNG.</div>
                    </div>

                    <button class="t-btn w-full mt-4">Kirim Testimoni</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ================= MODAL REPORT ================= --}}
    <div id="reportModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4" style="backdrop-filter: blur(4px);">
        <div class="t-card w-full max-w-md p-6"
             style="animation: scaleIn .18s ease-out;">
            <h3 class="text-lg font-extrabold mb-1">Laporkan Testimoni</h3>
            <p class="t-muted text-xs mb-4">Pilih alasan laporan. Admin akan meninjau laporan ini.</p>

            <form id="reportForm" method="POST">
                @csrf

                <label class="text-sm font-bold">Alasan</label>
                <select name="reason" required class="t-input mt-1 mb-3">
                    <option value="Spam">Spam</option>
                    <option value="Ujaran kebencian">Ujaran kebencian</option>
                    <option value="Tidak pantas">Tidak pantas</option>
                    <option value="Penipuan">Penipuan</option>
                    <option value="Lainnya">Lainnya</option>
                </select>

                <label class="text-sm font-bold">
                    Catatan tambahan <span class="t-muted text-xs">(opsional)</span>
                </label>
                <textarea name="note" rows="3" class="t-textarea mt-1"
                          placeholder="Tulis catatan tambahan bila perlu..."></textarea>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeReportModal()"
                            class="t-input !w-auto !px-5 !py-2 font-bold">
                        Batal
                    </button>

                    <button class="t-btn !w-auto !px-5 !py-2">
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ===== modal report =====
        function openReportModal(actionUrl) {
            const modal = document.getElementById('reportModal');
            const form  = document.getElementById('reportForm');
            form.action = actionUrl;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeReportModal() {
            const modal = document.getElementById('reportModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        document.getElementById('reportModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'reportModal') closeReportModal();
        });

        // ===== rating stars =====
        (function () {
            const row = document.getElementById('starRow');
            const input = document.getElementById('ratingValue');
            if (!row || !input) return;

            function paint(v) {
                const stars = row.querySelectorAll('.star-btn .star');
                stars.forEach((el, idx) => {
                    const n = idx + 1;
                    el.classList.toggle('is-on', n <= v);
                });
            }

            // init (old value)
            const initial = parseInt(input.value || '0', 10);
            paint(initial);

            row.querySelectorAll('.star-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const v = parseInt(btn.dataset.star, 10);
                    input.value = v;
                    paint(v);
                });

                // optional: hover preview (tanpa ngubah value)
                btn.addEventListener('mouseenter', () => {
                    const v = parseInt(btn.dataset.star, 10);
                    paint(v);
                });
            });

            // balik lagi ke value asli saat mouse keluar row
            row.addEventListener('mouseleave', () => {
                const v = parseInt(input.value || '0', 10);
                paint(v);
            });

            // prevent submit rating=0
            document.getElementById('testimonialForm')?.addEventListener('submit', (e) => {
                const v = parseInt(input.value || '0', 10);
                if (!v || v < 1) {
                    e.preventDefault();
                    alert('Silakan pilih rating bintang dulu.');
                }
            });
        })();

        // ===== anim keyframes =====
        const style = document.createElement('style');
        style.textContent = `
            @keyframes scaleIn {
                from { opacity:0; transform: scale(.94); }
                to   { opacity:1; transform: scale(1); }
            }
        `;
        document.head.appendChild(style);
    </script>
</section>
