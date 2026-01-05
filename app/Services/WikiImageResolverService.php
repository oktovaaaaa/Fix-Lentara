<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WikiImageResolverService
{
    /**
     * Ambil image + wiki_url + summary (sejarah/asl-usul singkat) dari Wikipedia.
     * Lebih akurat daripada REST summary langsung karena:
     * - bisa redirects
     * - bisa pageimages
     * - bisa extracts intro
     * - bisa fullurl
     */
    public function resolve(string $foodName, string $tribeKey = ''): array
    {
        $foodName = trim($foodName);
        $tribeKey = trim($tribeKey);

        if ($foodName === '') {
            return $this->fallback();
        }

        // Query yang lebih akurat: pakai nama makanan + tribe (sebagai hint)
        // tapi prioritas tetap nama makanannya.
        $searchVariants = array_values(array_unique(array_filter([
            $foodName,
            ($tribeKey !== '' ? ($foodName . ' ' . $tribeKey) : null),
            // kadang Wikipedia pakai ejaan alternatif:
            str_replace(['–', '—'], '-', $foodName),
        ])));

        foreach ($searchVariants as $q) {
            $data = $this->queryWikipedia($q);
            if ($data !== null) {
                return $data;
            }
        }

        // kalau gagal semua
        return $this->fallback();
    }

    private function queryWikipedia(string $query): ?array
    {
        $query = trim($query);
        if ($query === '') return null;

        // 1) Cari halaman paling relevan via "search"
        // lalu ambil detail page (extract + pageimage + fullurl) via pageid.
        $search = Http::timeout(20)
            ->withHeaders([
                // penting untuk beberapa konfigurasi hosting/proxy
                'Accept' => 'application/json',
                'User-Agent' => 'LentaraPiforrr/1.0 (contact: admin@localhost)',
            ])
            ->get("https://id.wikipedia.org/w/api.php", [
                'action'   => 'query',
                'list'     => 'search',
                'srsearch' => $query,
                'srlimit'  => 1,
                'format'   => 'json',
            ]);

        if (!$search->ok()) return null;

        $searchJson = $search->json();
        $pageId = data_get($searchJson, 'query.search.0.pageid');

        if (!$pageId) {
            return null;
        }

        // 2) Ambil info halaman + intro extract + thumbnail + fullurl
        $detail = Http::timeout(20)
            ->withHeaders([
                'Accept' => 'application/json',
                'User-Agent' => 'LentaraPiforrr/1.0 (contact: admin@localhost)',
            ])
            ->get("https://id.wikipedia.org/w/api.php", [
                'action'        => 'query',
                'pageids'       => $pageId,
                'prop'          => 'extracts|pageimages|info',
                'exintro'       => 1,
                'explaintext'   => 1,
                'inprop'        => 'url',
                'redirects'     => 1,
                'pithumbsize'   => 900,
                'format'        => 'json',
            ]);

        if (!$detail->ok()) return null;

        $json = $detail->json();
        $page = data_get($json, "query.pages.$pageId");

        if (!is_array($page)) return null;

        $fullUrl  = data_get($page, 'fullurl');
        $extract  = data_get($page, 'extract');
        $thumb    = data_get($page, 'thumbnail.source');

        // normalize
        $wikiUrl = is_string($fullUrl) && trim($fullUrl) !== '' ? trim($fullUrl) : null;
        $summary = is_string($extract) && trim($extract) !== '' ? trim($extract) : null;
        $imageUrl = is_string($thumb) && trim($thumb) !== '' ? trim($thumb) : null;

        // kalau wikiUrl saja ada, itu sudah berguna (linknya benar)
        if ($wikiUrl || $summary || $imageUrl) {
            return [
                'image_url'     => $imageUrl,
                'sources'       => $wikiUrl ? [$wikiUrl] : [],
                'wiki_url'      => $wikiUrl,
                'wiki_summary'  => $summary,
            ];
        }

        return null;
    }

    private function fallback(): array
    {
        return [
            'image_url'    => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&q=80&w=1200',
            'sources'      => ['https://id.wikipedia.org/'],
            'wiki_url'     => null,
            'wiki_summary' => null,
        ];
    }
}
