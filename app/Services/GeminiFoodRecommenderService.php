<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiFoodRecommenderService
{
    public function generateFoodsForTribe(string $tribeKey, string $islandNameOrSlug = ''): array
    {
        // ✅ Ambil dari config/services.php
        $apiKey = config('services.gemini.key');
        $model  = config('services.gemini.model', 'gemini-2.5-flash');

        if (!$apiKey) {
            throw new \RuntimeException('GEMINI_API_KEY belum di-set di .env (services.gemini.key kosong).');
        }

        // ✅ Anti double "models/"
        $model = preg_replace('#^models/#', '', (string) $model);
        $model = trim($model);
        if ($model === '') {
            $model = 'gemini-2.5-flash';
        }

        // 1) Try normal (10 items)
        $items = $this->callGeminiAndParse(
            apiKey: $apiKey,
            model: $model,
            tribeKey: $tribeKey,
            islandNameOrSlug: $islandNameOrSlug,
            itemCount: 6,
            maxTokens: 4096,
            shortMode: false
        );

        if (!empty($items)) {
            return array_slice($items, 0, 10);
        }

        // 2) Retry short mode (lebih pendek biar ga kepotong)
        $items = $this->callGeminiAndParse(
            apiKey: $apiKey,
            model: $model,
            tribeKey: $tribeKey,
            islandNameOrSlug: $islandNameOrSlug,
            itemCount: 8,
            maxTokens: 4096,
            shortMode: true
        );

        if (empty($items)) {
            throw new \RuntimeException('Gemini menghasilkan data kosong setelah retry.');
        }

        return array_slice($items, 0, 10);
    }

    private function callGeminiAndParse(
        string $apiKey,
        string $model,
        string $tribeKey,
        string $islandNameOrSlug,
        int $itemCount,
        int $maxTokens,
        bool $shortMode
    ): array {
        $islandHint = trim((string) $islandNameOrSlug);
        $islandLine = $islandHint !== '' ? "\nKonteks pulau/region: \"{$islandHint}\" (gunakan hanya sebagai petunjuk region_hint jika relevan)." : '';

        $extraRules = $shortMode
            ? <<<RULES

BATASAN PANJANG (WAJIB):
- description: 1 kalimat singkat (maks 18 kata)
- where_to_find: maksimal 2 lokasi
- tags: maksimal 4 tags
- region_hint: maksimal 2 kata (contoh "Aceh" / "Sumatera Utara")
RULES
            : '';

        $prompt = <<<PROMPT
Kamu adalah asisten kurator kuliner Nusantara.$islandLine

Buat {$itemCount} rekomendasi makanan/minuman yang sangat identik dengan Suku "{$tribeKey}" di Indonesia.
Fokus pada nama makanan (dish). Jika ada varian yang terkait tempat (contoh: "BPK Balige"), isi "where_to_find" dengan 1-2 lokasi yang wajar (kota/daerah), tanpa alamat lengkap.

Kembalikan output HANYA JSON VALID (tanpa markdown, tanpa teks tambahan).
Format output boleh SALAH SATU dari dua opsi ini (pilih salah satu saja):
1) Array langsung: [ { ... }, { ... } ]
ATAU
2) Object wrapper: { "items": [ { ... }, { ... } ] }

Setiap item WAJIB object dengan field:
- name (string, wajib)
- description (string, wajib)
- price_range (string|null, contoh "20k–40k", boleh null)
- rating_estimate (number|null, range 4.0-4.9, boleh null)
- region_hint (string|null, contoh "Sumatera Utara", boleh null)
- where_to_find (array of strings, boleh empty)
- tags (array of strings, boleh empty)
- category (string|null, contoh "main_course"/"drink"/"snack", boleh null)

{$extraRules}

WAJIB:
- Pastikan JSON valid dan lengkap sampai penutup akhir (']' dan '}' jika ada).
- Jangan memotong kalimat di tengah.
- Jangan pakai trailing comma.
PROMPT;

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $resp = Http::timeout(60)->post($url, [
            "contents" => [
                [
                    "role"  => "user",
                    "parts" => [
                        ["text" => $prompt],
                    ],
                ],
            ],
            "generationConfig" => [
                "temperature"      => $shortMode ? 0.4 : 0.7,
                "maxOutputTokens"  => $maxTokens,
                "responseMimeType" => "application/json",
                "candidateCount"   => 1,
            ],
            "safetySettings" => [
                ["category" => "HARM_CATEGORY_HARASSMENT",        "threshold" => "BLOCK_NONE"],
                ["category" => "HARM_CATEGORY_HATE_SPEECH",       "threshold" => "BLOCK_NONE"],
                ["category" => "HARM_CATEGORY_SEXUALLY_EXPLICIT", "threshold" => "BLOCK_NONE"],
                ["category" => "HARM_CATEGORY_DANGEROUS_CONTENT", "threshold" => "BLOCK_NONE"],
            ],
        ]);

        if (!$resp->ok()) {
            throw new \RuntimeException("Gemini HTTP {$resp->status()} error: " . $resp->body());
        }

        $jsonResp = $resp->json();
        $finishReason = data_get($jsonResp, 'candidates.0.finishReason');
        $text = data_get($jsonResp, 'candidates.0.content.parts.0.text');

        if (!is_string($text) || trim($text) === '') {
            $altPart = data_get($jsonResp, 'candidates.0.content.parts.0');
            throw new \RuntimeException('Gemini tidak mengembalikan text output. finishReason=' . json_encode($finishReason) . ' part=' . json_encode($altPart));
        }

        $raw = trim($text);

        // bersihkan ```json ... ``` jika ada
        $raw = preg_replace('/^```(?:json)?\s*/i', '', $raw);
        $raw = preg_replace('/\s*```$/', '', $raw);
        $raw = trim($raw);

        // decode langsung
        $decoded = json_decode($raw, true);

        // kalau gagal, coba extract JSON pertama
        if (!is_array($decoded)) {
            $extracted = $this->extractFirstJson($raw);
            if ($extracted !== null) {
                $decoded = json_decode($extracted, true);
            }
        }

        if (!is_array($decoded)) {
            // biasanya karena kepotong (MAX_TOKENS)
            throw new \RuntimeException(
                "Output Gemini bukan JSON valid. finishReason=" . json_encode($finishReason) .
                ". Raw: " . substr($raw, 0, 700)
            );
        }

        // support 2 format
        $items = (array_key_exists('items', $decoded) && is_array($decoded['items']))
            ? $decoded['items']
            : $decoded;

        if (!is_array($items) || empty($items)) {
            throw new \RuntimeException(
                "Output Gemini tidak punya items array yang valid. finishReason=" . json_encode($finishReason) .
                ". Raw: " . substr($raw, 0, 700)
            );
        }

        // normalisasi item biar aman untuk DB/UI
        $normalized = [];
        foreach ($items as $it) {
            if (!is_array($it)) continue;

            $name = trim((string) ($it['name'] ?? ''));
            $desc = trim((string) ($it['description'] ?? ''));

            if ($name === '' || $desc === '') continue;

            $priceRange = $it['price_range'] ?? null;
            $priceRange = is_string($priceRange) ? trim($priceRange) : null;
            if ($priceRange === '') $priceRange = null;

            $rating = $it['rating_estimate'] ?? null;
            if (is_string($rating)) {
                $rating = trim($rating);
                $rating = is_numeric($rating) ? (float) $rating : null;
            }
            if (is_int($rating) || is_float($rating)) {
                if ($rating < 4.0) $rating = 4.0;
                if ($rating > 4.9) $rating = 4.9;
                $rating = round((float) $rating, 1);
            } else {
                $rating = null;
            }

            $regionHint = $it['region_hint'] ?? null;
            $regionHint = is_string($regionHint) ? trim($regionHint) : null;
            if ($regionHint === '') $regionHint = null;

            $where = $it['where_to_find'] ?? [];
            if (is_string($where)) $where = [$where];
            if (!is_array($where)) $where = [];
            $where = array_values(array_filter(array_map(function ($v) {
                $v = trim((string) $v);
                return $v !== '' ? $v : null;
            }, $where)));

            $tags = $it['tags'] ?? [];
            if (is_string($tags)) $tags = [$tags];
            if (!is_array($tags)) $tags = [];
            $tags = array_values(array_filter(array_map(function ($v) {
                $v = trim((string) $v);
                return $v !== '' ? $v : null;
            }, $tags)));

            $category = $it['category'] ?? null;
            $category = is_string($category) ? trim($category) : null;
            if ($category === '') $category = null;

            $normalized[] = [
                'name'            => $name,
                'description'     => $desc,
                'price_range'     => $priceRange,
                'rating_estimate' => $rating,
                'region_hint'     => $regionHint,
                'where_to_find'   => $where,
                'tags'            => $tags,
                'category'        => $category,
            ];
        }

        return $normalized;
    }

    /**
     * Extract JSON pertama (array/object) dari string jika ada teks tambahan.
     */
    private function extractFirstJson(string $raw): ?string
    {
        $raw = trim($raw);
        if ($raw === '') return null;

        $firstObj = strpos($raw, '{');
        $firstArr = strpos($raw, '[');

        if ($firstObj === false && $firstArr === false) {
            return null;
        }

        $start = null;
        $close = null;

        if ($firstObj !== false && ($firstArr === false || $firstObj < $firstArr)) {
            $start = $firstObj;
            $close = '}';
        } else {
            $start = $firstArr;
            $close = ']';
        }

        $candidate = substr($raw, $start);

        $endPos = strrpos($candidate, $close);
        if ($endPos === false) {
            return null;
        }

        $json = substr($candidate, 0, $endPos + 1);
        $json = trim($json);

        return $json !== '' ? $json : null;
    }
}
