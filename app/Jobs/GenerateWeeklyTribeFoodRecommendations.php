<?php

namespace App\Jobs;

use App\Models\Island;
use App\Models\TribeFoodRecommendation;
use App\Services\GeminiFoodRecommenderService;
use App\Services\WikiImageResolverService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class GenerateWeeklyTribeFoodRecommendations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(
        GeminiFoodRecommenderService $gemini,
        WikiImageResolverService $wiki
    ): void {
        $now = now();

        // ISO week key: contoh 2026-W01
        $weekKey = Carbon::now()->format('o-\WW');

        // 1) Ambil semua pulau aktif
        $islands = Island::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        foreach ($islands as $island) {
            $regionSlug = (string) $island->slug;

            // 2) Ambil tribes sesuai config/tribes.php
            $tribes = config('tribes.' . $regionSlug, []);

            if (empty($tribes)) {
                continue;
            }

            foreach ($tribes as $tribeKey) {
                $tribeKey = trim((string) $tribeKey);
                if ($tribeKey === '') continue;

                // 3) Generate dari Gemini
                $items = $gemini->generateFoodsForTribe($tribeKey, $regionSlug);

                // 4) Resolve image + wiki_url + summary
                $finalItems = [];
                foreach ($items as $it) {
                    $name = trim((string)($it['name'] ?? ''));
                    if ($name === '') continue;

                    $resolved = $wiki->resolve($name, $tribeKey);

                    $imageUrl = $resolved['image_url'] ?? null;
                    $sources  = $resolved['sources'] ?? [];
                    $wikiUrl  = $resolved['wiki_url'] ?? null;

                    // ✅ FIX UTAMA: summary key bisa beda-beda tergantung service
                    // - kamu simpan ke payload sebagai 'wiki_summary'
                    // - jadi ambil dari beberapa kemungkinan key supaya tidak null
                    $summary = $resolved['wiki_summary']
                        ?? $resolved['summary']
                        ?? $resolved['extract']
                        ?? null;

                    // fallback image saja boleh (biar UI tetap cakep)
                    if (!$imageUrl) {
                        $imageUrl = 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&q=80&w=1200';
                    }

                    // IMPORTANT: kalau sources kosong, BIARKAN kosong (jangan samain semua)
                    $sources = is_array($sources) ? array_values(array_filter($sources)) : [];

                    // ✅ pastikan wikiUrl jadi sumber pertama (spesifik per makanan)
                    if ($wikiUrl && is_string($wikiUrl)) {
                        array_unshift($sources, $wikiUrl);
                        $sources = array_values(array_unique(array_filter($sources)));
                    }

                    $finalItems[] = [
                        'name' => $name,
                        'description' => (string)($it['description'] ?? ''),
                        'price_range' => $it['price_range'] ?? null,
                        'rating_estimate' => $it['rating_estimate'] ?? null,
                        'region_hint' => $it['region_hint'] ?? null,
                        'where_to_find' => array_values(array_filter((array)($it['where_to_find'] ?? []))),
                        'tags' => array_values(array_filter((array)($it['tags'] ?? []))),
                        'category' => $it['category'] ?? null,

                        'image_url' => $imageUrl,
                        'sources' => $sources,

                        // ✅ NEW: info sejarah/asal usul dari Wikipedia intro
                        'wiki_url' => $wikiUrl,
                        'wiki_summary' => $summary,
                    ];
                }

                $payload = [
                    'island_slug' => $regionSlug,
                    'tribe_key' => $tribeKey,
                    'week_key' => $weekKey,
                    'generated_at' => $now->toISOString(),
                    'items' => array_slice($finalItems, 0, 10),
                ];

                TribeFoodRecommendation::updateOrCreate(
                    ['tribe_key' => $tribeKey, 'week_key' => $weekKey],
                    [
                        'region_slug' => $regionSlug,
                        'payload' => $payload,
                        'generated_at' => $now,
                    ]
                );
            }
        }
    }
}
