<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NusantaraChatController extends Controller
{
    public function chat(Request $request)
    {
        // Validasi data dari frontend
        $validated = $request->validate([
            'messages' => 'required|array',
            'messages.*.role' => 'required|string',
            'messages.*.content' => 'required|string',
        ]);

        $messages = $validated['messages'];

        // 1) System instruction: ketat hanya topik Indonesia/Nusantara + paksa plain text (tanpa markdown)
        $systemInstruction = <<<TXT
Kamu adalah "Nusantara AI", asisten digital khusus untuk website yang membahas Indonesia dan Nusantara.

CAKUPAN TOPIK YANG BOLEH DIJAWAB (HANYA INI):
1) Budaya Nusantara/Indonesia:
   - adat istiadat, tradisi, upacara adat, kesenian, tarian, musik, pakaian adat
   - bahasa daerah, suku-suku di Indonesia, kearifan lokal
   - kuliner/makanan-minuman khas daerah di Indonesia
   - sejarah Indonesia (tokoh, kerajaan, peristiwa sejarah) dan budaya daerah
   - pariwisata Indonesia (destinasi, budaya lokal, kuliner daerah)

2) Ekonomi Indonesia:
   - UMKM, ekonomi kreatif, pariwisata sebagai sektor ekonomi
   - industri di Indonesia, peluang usaha di Indonesia, perkembangan ekonomi Indonesia
   - istilah ekonomi yang relevan dengan konteks Indonesia

ATURAN WAJIB:
A) Selalu jawab dengan Bahasa Indonesia yang sopan, hangat, dan mudah dipahami.
B) Fokus pada konteks Indonesia/Nusantara. Sebut contoh daerah (Jawa, Sumatera, Sulawesi, Papua, Bali, NTT, Kalimantan) jika relevan.
C) Jika pertanyaan DI LUAR topik Indonesia/Nusantara (misalnya:
   - matematika murni, coding/teknis umum tanpa konteks Indonesia
   - sains umum, gosip, selebriti luar, dewasa/vulgar, kekerasan, senjata, narkoba, hal ilegal
   - topik negara lain tanpa kaitan Indonesia
   maka kamu WAJIB menolak dan JANGAN menjawab isi pertanyaan.
   Balas dengan template ini (boleh sedikit variasi tapi maknanya harus sama):

   "Maaf ya, Nusantara AI hanya bisa menjawab seputar Nusantara/Indonesia (budaya, suku, kuliner, pariwisata, dan ekonomi Indonesia). Coba tanya hal lain yang masih dalam topik itu ya 🙂"

D) Jika user memaksa, tetap ulangi penolakan dengan sopan.

FORMAT OUTPUT (WAJIB):
- Output HARUS teks biasa (plain text), tanpa Markdown sama sekali.
- Jangan gunakan **tebal**, *miring*, heading (#), atau code block (```).
- Jangan gunakan penomoran otomatis 1. 2. 3. yang panjang.
- Jika butuh daftar, gunakan dash "-" saja:
  - Poin satu
  - Poin dua
- Maksimal 2–6 paragraf pendek. Jika perlu, pakai daftar dash di akhir.
TXT;

        // 2) Gabungkan riwayat chat menjadi teks (sederhana untuk Gemini)
        $historyText = '';
        foreach ($messages as $msg) {
            $role = $msg['role'] === 'user' ? 'Pengguna' : 'Nusantara AI';
            $historyText .= "{$role}: {$msg['content']}\n";
        }

        $prompt = $historyText ?: 'Pengguna: Jelaskan secara singkat tentang budaya Nusantara.';

        try {
            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) {
                throw new \RuntimeException('GEMINI_API_KEY belum di-set di .env');
            }

            // Model Gemini yang cepat & cocok untuk chat
            $model = 'gemini-2.5-flash'; // kalau error model, bisa diganti 'gemini-1.5-flash'
            $url   = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

            $response = Http::withHeaders([
                    'Content-Type'   => 'application/json',
                    'x-goog-api-key' => $apiKey,
                ])
                ->post($url, [
                    'systemInstruction' => [
                        'parts' => [
                            ['text' => $systemInstruction],
                        ],
                    ],
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    // Optional: aktifkan kalau mau jawaban lebih stabil
                    // 'generationConfig' => [
                    //     'temperature' => 0.6,
                    //     'topP' => 0.9,
                    //     'maxOutputTokens' => 600,
                    // ],
                ]);

            if (!$response->successful()) {
                throw new \RuntimeException(
                    'Gemini error: ' . $response->status() . ' ' . $response->body()
                );
            }

            $data = $response->json();

            // Ambil teks jawaban pertama dari kandidat pertama
            $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$reply) {
                $reply = 'Maaf, Nusantara AI belum bisa menjawab. Coba lagi sebentar lagi ya.';
            }

            // 3) Bersihkan output supaya tidak ada *** / markdown / poin berantakan
            $reply = $this->cleanReply($reply);

            return response()->json([
                'reply' => $reply,
            ]);

        } catch (\Throwable $e) {
            // Log supaya kamu bisa cek di storage/logs/laravel.log
            \Log::error('NusantaraChat Gemini error: '.$e->getMessage());

            // Pesan ramah ke user
            return response()->json([
                'reply' => 'Maaf, server Nusantara AI sedang bermasalah atau kuota gratis hari ini sudah habis. Coba lagi nanti ya 🙏',
                // 'debug' => $e->getMessage(), // boleh aktifkan sementara kalau mau lihat errornya
            ], 500);
        }
    }

    /**
     * Bersihkan Markdown / format liar dari model agar rapi di bubble chat yang plain text.
     */
    private function cleanReply(string $text): string
    {
        // Normalisasi line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Hilangkan code fences ```...``` (kalau model ngeyel)
        $text = preg_replace('/```[\s\S]*?```/m', '', $text);

        // Hilangkan bold/italic markdown
        $text = preg_replace('/\*\*(.*?)\*\*/s', '$1', $text);
        $text = preg_replace('/\*(.*?)\*/s', '$1', $text);
        $text = preg_replace('/__(.*?)__/s', '$1', $text);
        $text = preg_replace('/_(.*?)_/s', '$1', $text);

        // Hilangkan heading markdown (#, ##, ###)
        $text = preg_replace('/^\s{0,3}#{1,6}\s+/m', '', $text);

        // Ubah numbered list "1. " jadi dash "- "
        $text = preg_replace('/^\s*\d+\.\s+/m', "- ", $text);

        // Ubah bullet aneh jadi dash standar
        $text = preg_replace('/^\s*[•●◦▪︎]+\s+/m', "- ", $text);

        // Hapus garis horizontal markdown
        $text = preg_replace('/^\s*(-{3,}|\*{3,}|_{3,})\s*$/m', '', $text);

        // Rapikan whitespace
        $text = preg_replace("/[ \t]+\n/", "\n", $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }
}
