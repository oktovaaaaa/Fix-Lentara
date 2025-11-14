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

        // 1) System instruction: batasi topik dan gaya bahasa
        $systemInstruction = <<<TXT
Kamu adalah "Nusantara AI", asisten digital untuk website yang mengenalkan:
- Budaya Nusantara (adat istiadat, tradisi, bahasa daerah, kuliner, musik, sejarah, dll.)
- Pertumbuhan dan perkembangan ekonomi Indonesia (UMKM, pariwisata, ekonomi kreatif, industri, dsb.)

Aturan penting:
1. Jawab selalu dengan bahasa Indonesia yang sopan dan hangat.
2. Berikan penjelasan yang mudah dipahami, boleh pakai poin-poin atau contoh singkat.
3. Jika pertanyaan di luar topik budaya Nusantara atau ekonomi Indonesia
   (misalnya matematika murni seperti "20 x 10 berapa", gosip, hal vulgar, atau topik berbahaya),
   JANGAN menjawab isi pertanyaannya.
   Cukup balas seperti ini (boleh variasi dikit):

   "Maaf ya, Nusantara AI hanya bisa menjawab seputar budaya Nusantara dan pertumbuhan ekonomi Indonesia. Coba tanya hal lain yang masih dalam topik itu ya 🙂"

4. Jangan pernah memberikan jawaban matematika murni atau topik teknis yang sama sekali tidak berkaitan.
5. Tolak dengan sopan semua permintaan yang berbahaya atau melanggar hukum.
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
}
