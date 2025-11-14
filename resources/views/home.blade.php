{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('title', 'Lentara Islands')

@php
    // di home tidak ada selectedIsland
    $featuresByType = $featuresByType ?? [];
@endphp

@section('content')
    {{-- HERO + ANIMASI KARTU (anchor #home untuk navbar) --}}
    <section id="home">
        @include('partials.landing-hero')
    </section>

    {{-- SECTION KONTEN HOME (Budaya Indonesia) --}}
    <section class="relative z-[10] bg-[#050505] text-white py-12 sm:py-16 px-4 sm:px-6">
        <div class="max-w-5xl mx-auto space-y-12">

            {{-- ISLANDS LIST / JELAJAH PULAU (anchor #islands untuk navbar "Pulau") --}}
            <section id="islands">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Jelajahi Pulau-Pulau Indonesia
                </h2>
                <p class="text-sm sm:text-base text-white/80 leading-relaxed mb-4">
                    Pilih salah satu pulau dari menu di atas atau dari daftar berikut untuk menjelajahi budaya,
                    destinasi, kuliner, dan warisan khas masing-masing pulau.
                </p>

                {{-- Di sini nanti bisa diisi daftar kartu pulau dari database --}}
                {{-- Contoh placeholder sederhana --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="border border-white/10 rounded-xl p-4 bg-white/5">
                        <h3 class="font-semibold text-base mb-1">Sumatera</h3>
                        <p class="text-xs text-white/70">
                            Pulau dengan beragam budaya dan kuliner khas seperti rendang.
                        </p>
                    </div>
                    <div class="border border-white/10 rounded-xl p-4 bg-white/5">
                        <h3 class="font-semibold text-base mb-1">Jawa</h3>
                        <p class="text-xs text-white/70">
                            Pusat sejarah dan kebudayaan dengan beragam tradisi dan bahasa.
                        </p>
                    </div>
                    <div class="border border-white/10 rounded-xl p-4 bg-white/5">
                        <h3 class="font-semibold text-base mb-1">Kalimantan</h3>
                        <p class="text-xs text-white/70">
                            Pulau besar dengan kekayaan hutan tropis dan budaya Dayak.
                        </p>
                    </div>
                    {{-- dst: nanti bisa diganti loop @foreach islands --}}
                </div>
            </section>

            {{-- ABOUT INDONESIA --}}
            <section id="about">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Tentang Budaya Indonesia
                </h2>
                <p class="text-sm sm:text-base text-white/80 leading-relaxed">
                    Indonesia adalah negara kepulauan dengan ratusan suku, bahasa, dan tradisi. Halaman ini
                    mengajakmu menjelajahi keragaman budaya dari Sabang sampai Merauke melalui pulau-pulau utama.
                </p>
            </section>

            {{-- HISTORY INDONESIA --}}
            <section id="history">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Sejarah Singkat Nusantara
                </h2>
                <p class="text-sm sm:text-base text-white/80 leading-relaxed">
                    Dari kerajaan-kerajaan kuno hingga masa modern, Nusantara tumbuh sebagai titik temu budaya,
                    perdagangan, dan kepercayaan. Kamu bisa memperkaya konten ini dari database nanti.
                </p>
            </section>

           {{-- STATISTIK INDONESIA --}}
<section id="stats" class="mt-10">
    {{-- CSS khusus section ini saja --}}
    <style>
        #stats .stat-card {
            position: relative;
            border-radius: 18px;
            padding: 18px 18px 16px 18px;
            background: linear-gradient(135deg, #111827, #020617);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.55);
            cursor: pointer;
            overflow: hidden;

            /* animasi masuk */
            opacity: 0;
            transform: translateY(16px);
            animation: statsFadeUp 0.7s ease-out forwards;
        }

        #stats .stat-card-main {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
        }

        #stats .stat-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.12), transparent 55%);
            opacity: 0;
            transition: opacity 0.25s ease-out;
        }

        #stats .stat-card:hover::after {
            opacity: 1;
        }

        #stats .stat-number {
            font-size: 2.5rem;
            line-height: 1;
            font-weight: 800;
        }

        #stats .stat-label {
            font-size: 0.95rem;
            font-weight: 500;
        }

        #stats .stat-more {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        #stats .stat-more-icon {
            transition: transform 0.2s ease-out;
        }

        #stats .stat-card:hover .stat-more-icon {
            transform: translateX(4px);
        }

        /* animasi stagger */
        #stats .stat-card:nth-child(1) { animation-delay: 0.05s; }
        #stats .stat-card:nth-child(2) { animation-delay: 0.15s; }
        #stats .stat-card:nth-child(3) { animation-delay: 0.25s; }
        #stats .stat-card:nth-child(4) { animation-delay: 0.35s; }

        @keyframes statsFadeUp {
            from {
                opacity: 0;
                transform: translateY(18px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ===== Modal (popup) ===== */
        #stats-modal-backdrop {
            display: none;
        }

        #stats-modal-backdrop.is-open {
            display: flex;
        }

        #stats-modal {
            transform: translateY(12px) scale(0.96);
            opacity: 0;
            transition: all 0.22s ease-out;
        }

        #stats-modal-backdrop.is-open #stats-modal {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        @media (max-width: 640px) {
            #stats .stat-number {
                font-size: 2.1rem;
            }
        }
    </style>

    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3 text-white">
        Statistik Budaya Indonesia
    </h2>
    <p class="text-sm sm:text-base text-white/70 mb-4 max-w-3xl">
        Gambaran singkat keragaman Indonesia: jumlah pulau, suku, bahasa daerah,
        dan komposisi agama. Angka di bawah ini adalah ringkasan data nasional terbaru.
    </p>

    {{-- KARTU STATISTIK --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Jumlah pulau (kartu utama seperti contoh gambar merah) --}}
        <button
            type="button"
            class="stat-card stat-card-main text-left text-white"
            data-stat="islands"
        >
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="stat-number">17.000+</div>
                    <div class="stat-label mt-1">Pulau di Indonesia</div>
                    <p class="mt-2 text-xs text-white/80 max-w-[210px]">
                        Indonesia adalah negara kepulauan dengan puluhan ribu pulau besar
                        dan kecil tersebar dari Sabang sampai Merauke.
                    </p>
                </div>
                <div class="opacity-70">
                    {{-- icon pie kecil --}}
                    <svg viewBox="0 0 24 24" class="w-10 h-10">
                        <path fill="currentColor"
                              d="M11 3a9 9 0 1 0 9 9h-9z"/>
                        <path fill="currentColor"
                              d="M13 3.055V11h7.945A9.002 9.002 0 0 0 13 3.055z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-more text-white/90">
                More info
                <span class="stat-more-icon">➜</span>
            </div>
        </button>

        {{-- Suku bangsa --}}
        <button
            type="button"
            class="stat-card text-left text-white"
            data-stat="ethnic"
        >
            <div class="stat-number">600+</div>
            <div class="stat-label mt-1">Kelompok etnis / suku</div>
            <p class="mt-2 text-xs text-white/80">
                Dari Jawa, Sunda, Batak, Bugis, Dayak, hingga ratusan suku lain
                yang tersebar di berbagai pulau.
            </p>
            <div class="stat-more text-white/90">
                More info
                <span class="stat-more-icon">➜</span>
            </div>
        </button>

        {{-- Bahasa daerah --}}
        <button
            type="button"
            class="stat-card text-left text-white"
            data-stat="languages"
        >
            <div class="stat-number">700+</div>
            <div class="stat-label mt-1">Bahasa daerah hidup</div>
            <p class="mt-2 text-xs text-white/80">
                Salah satu negara paling kaya bahasa di dunia, setelah Papua Nugini.
            </p>
            <div class="stat-more text-white/90">
                More info
                <span class="stat-more-icon">➜</span>
            </div>
        </button>

        {{-- Agama --}}
        <button
            type="button"
            class="stat-card text-left text-white"
            data-stat="religion"
        >
            <div class="stat-number">6+</div>
            <div class="stat-label mt-1">Agama & kepercayaan</div>
            <p class="mt-2 text-xs text-white/80">
                Islam, Kristen, Katolik, Hindu, Buddha, Konghucu, dan beragam
                kepercayaan lokal.
            </p>
            <div class="stat-more text-white/90">
                More info
                <span class="stat-more-icon">➜</span>
            </div>
        </button>
    </div>

    <p class="mt-3 text-[11px] text-white/50">
        *Perkiraan jumlah pulau dan bahasa dapat sedikit berbeda antar sumber resmi,
        namun kisaran angkanya tetap sama.
    </p>

    {{-- POPUP DETAIL STATISTIK --}}
    <div
        id="stats-modal-backdrop"
        class="fixed inset-0 z-40 bg-black/60 items-center justify-center px-4"
        aria-hidden="true"
    >
        <div
            id="stats-modal"
            class="max-w-lg w-full bg-[#020617] text-white rounded-2xl border border-white/10 p-5 sm:p-6 relative"
        >
            <button
                type="button"
                id="stats-modal-close"
                class="absolute right-4 top-3 text-white/60 hover:text-white text-xl leading-none"
                aria-label="Tutup"
            >
                ×
            </button>

            <h3 id="stats-modal-title" class="text-lg sm:text-xl font-semibold mb-2">
                Detail Statistik
            </h3>

            <div id="stats-modal-body" class="text-sm text-white/80 space-y-2 leading-relaxed">
                {{-- konten akan diinject via JS --}}
            </div>

            <p class="mt-4 text-[11px] text-white/40">
                Ringkasan berdasarkan data lembaga resmi Indonesia dan publikasi internasional.
            </p>
        </div>
    </div>

    {{-- SCRIPT UNTUK POPUP & ISI DETAIL --}}
    <script>
        (function () {
            const detailMap = {
                islands: {
                    title: 'Jumlah Pulau di Indonesia',
                    body: `
                        <p>Indonesia terdiri dari lebih dari <strong>17.000 pulau</strong>.
                        Beberapa sumber resmi menyebut kisaran antara <strong>17.000–18.000 pulau</strong>,
                        tergantung metode pendataan (apakah termasuk pulau kecil yang muncul saat surut, dsb).</p>
                        <ul class="mt-2 list-disc list-inside space-y-1">
                            <li>Pulau besar utama: Jawa, Sumatra, Kalimantan, Sulawesi, Papua.</li>
                            <li>Ribuan pulau kecil tersebar dari Sabang hingga Merauke.</li>
                            <li>Banyak pulau yang belum berpenghuni tetap.</li>
                        </ul>
                    `
                },
                ethnic: {
                    title: 'Keragaman Suku Bangsa',
                    body: `
                        <p>Terdapat lebih dari <strong>600 kelompok etnis/suku</strong> di Indonesia.
                        Beberapa suku terbesar antara lain:</p>
                        <ul class="mt-2 list-disc list-inside space-y-1">
                            <li><strong>Jawa</strong> (sekitar 40% penduduk).</li>
                            <li><strong>Sunda</strong>, <strong>Batak</strong>, <strong>Betawi</strong>,
                                <strong>Minangkabau</strong>, <strong>Bugis</strong>, <strong>Dayak</strong>, dan lain-lain.</li>
                            <li>Banyak suku memiliki bahasa, adat, dan pakaian tradisional sendiri.</li>
                        </ul>
                    `
                },
                languages: {
                    title: 'Bahasa Daerah di Indonesia',
                    body: `
                        <p>Indonesia memiliki lebih dari <strong>700 bahasa hidup</strong>,
                        menjadikannya salah satu negara dengan keragaman bahasa terbesar di dunia.</p>
                        <ul class="mt-2 list-disc list-inside space-y-1">
                            <li>Mayoritas termasuk rumpun <strong>Austronesia</strong> (misalnya Jawa, Sunda, Bugis).</li>
                            <li>Di Papua dan Maluku terdapat ratusan bahasa <strong>Papua</strong> yang sangat beragam.</li>
                            <li><strong>Bahasa Indonesia</strong> dipakai sebagai bahasa persatuan di seluruh nusantara.</li>
                        </ul>
                    `
                },
                religion: {
                    title: 'Komposisi Agama di Indonesia',
                    body: `
                        <p>Indonesia mengakui beberapa agama resmi dan juga kepercayaan lokal.
                        Perkiraan komposisi penduduk secara nasional:</p>
                        <ul class="mt-2 list-disc list-inside space-y-1">
                            <li><strong>Islam</strong> &plusmn; 87%.</li>
                            <li><strong>Protestan</strong> sekitar 7,4%.</li>
                            <li><strong>Katolik</strong> sekitar 3%.</li>
                            <li><strong>Hindu</strong> sekitar 1,7%.</li>
                            <li><strong>Buddha</strong> sekitar 0,7%.</li>
                            <li><strong>Konghucu</strong> dan kepercayaan adat lainnya &lt; 1%.</li>
                        </ul>
                        <p class="mt-2">
                            Meskipun mayoritas Muslim, Indonesia memiliki tradisi toleransi dan keragaman
                            praktik keagamaan di berbagai daerah.
                        </p>
                    `
                }
            };

            const backdrop = document.getElementById('stats-modal-backdrop');
            const modalTitle = document.getElementById('stats-modal-title');
            const modalBody = document.getElementById('stats-modal-body');
            const closeBtn = document.getElementById('stats-modal-close');

            function openModal(statKey) {
                const data = detailMap[statKey];
                if (!data) return;

                modalTitle.textContent = data.title;
                modalBody.innerHTML = data.body;

                backdrop.classList.add('is-open');
                document.body.classList.add('overflow-hidden');
            }

            function closeModal() {
                backdrop.classList.remove('is-open');
                document.body.classList.remove('overflow-hidden');
            }

            // event untuk kartu
            document.querySelectorAll('#stats .stat-card').forEach(function (card) {
                card.addEventListener('click', function () {
                    const statKey = card.getAttribute('data-stat');
                    openModal(statKey);
                });
            });

            // event untuk close
            closeBtn.addEventListener('click', closeModal);
            backdrop.addEventListener('click', function (e) {
                if (e.target === backdrop) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });
        })();
    </script>
</section>


            {{-- QUIZ INDONESIA --}}
            <section id="quiz">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-3">
                    Kuis Budaya Indonesia
                </h2>
                <p class="text-sm sm:text-base text-white/80">
                    Di sini nanti bisa jadi area kuis umum tentang Nusantara — misalnya pilihan ganda tentang
                    pulau, suku, atau rumah adat.
                    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                </p>
            </section>
        </div>
    </section>
@endsection
