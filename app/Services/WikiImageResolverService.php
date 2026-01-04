<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WikiImageResolverService
{
    /**
     * Cari image + source URL gratis via Wikipedia REST API.
     * Kalau tidak ketemu, fallback ke Wikimedia search sederhana.
     */
    public function resolve(string $foodName, string $tribeKey = ''): array
    {
        // 1) Wikipedia summary (bahasa Indonesia)
        $title = trim($foodName);
        $wikiTitle = str_replace(' ', '_', $title);

        $wikiUrl = "https://id.wikipedia.org/api/rest_v1/page/summary/" . rawurlencode($wikiTitle);

        $res = Http::timeout(20)->get($wikiUrl);

        if ($res->ok()) {
            $json = $res->json();

            $pageUrl = data_get($json, 'content_urls.desktop.page');
            $thumb   = data_get($json, 'thumbnail.source'); // biasanya ada

            if ($pageUrl && $thumb) {
                return [
                    'image_url' => $thumb,
                    'sources'   => [$pageUrl],
                ];
            }

            if ($pageUrl) {
                // punya source tapi tidak punya thumbnail
                // nanti image fallback
                return [
                    'image_url' => null,
                    'sources'   => [$pageUrl],
                ];
            }
        }

        // 2) Fallback: coba Wikipedia search (opensearch)
        $search = Http::timeout(20)->get("https://id.wikipedia.org/w/api.php", [
            'action' => 'opensearch',
            'search' => $foodName . ' ' . $tribeKey,
            'limit'  => 1,
            'namespace' => 0,
            'format' => 'json',
        ]);

        if ($search->ok()) {
            $arr = $search->json();
            // format: [query, [titles], [descs], [urls]]
            $url = $arr[3][0] ?? null;
            if ($url) {
                return [
                    'image_url' => null,
                    'sources'   => [$url],
                ];
            }
        }

        // 3) Final fallback: placeholder (kamu bisa ganti ke asset sendiri)
        return [
            'image_url' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&q=80&w=1200',
            'sources'   => ['https://id.wikipedia.org/'],
        ];
    }
}
