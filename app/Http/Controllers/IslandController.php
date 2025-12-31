<?php

namespace App\Http\Controllers;

use App\Models\Island;
use App\Models\IslandHistory;
use App\Models\Testimonial;
use App\Models\Quiz;

// ✅ WARISAN
use App\Models\TribePage;
use App\Models\HeritageItem;

use Illuminate\Http\Request;

class IslandController extends Controller
{
    public function index()
    {
        $islands = Island::orderBy('order')->get();
        return view('admin.stats.index', compact('islands'));
    }

    /**
     * Home (Budaya Indonesia)
     * Quiz di home = scope global
     */
    public function landing()
    {
        $islands = Island::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $carouselData = $islands->map(function (Island $island) {
            return [
                'place'       => $island->place_label,
                'title'       => $island->title,
                'title2'      => $island->subtitle,
                'description' => $island->short_description,
                'image'       => $island->image_url,
                'slug'        => $island->slug,
            ];
        });

        // ===== QUIZ GLOBAL (HOME) =====
        $quiz = Quiz::query()
            ->active()
            ->global()
            ->with(['questions.options'])
            ->latest()
            ->first();

        [$testimonials, $testimonialStats] = $this->getTestimonialsPayload();

        return view('home', [
            'carouselData'      => $carouselData,
            'selectedIsland'    => null,
            'featuresByType'    => [],
            'demographics'      => [
                'religion'  => collect(),
                'ethnicity' => collect(),
                'language'  => collect(),
            ],
            'testimonials'      => $testimonials,
            'testimonialStats'  => $testimonialStats,
            'quiz'              => $quiz,
        ]);
    }

    /**
     * Detail pulau (UNIVERSAL VIEW)
     * - tribes (tabs) ambil dari config tribes.php (prioritas)
     * - tribe dipilih via query: ?tribe=Aceh
     * - warisan per tribe (TribePage + HeritageItem)
     * - quiz per tribe (fallback globalQuiz)
     *
     * ✅ SINGLE VIEW: resources/views/islands.blade.php
     */
    public function show(Request $request, Island $island)
    {
        abort_unless($island->is_active, 404);

        // =============================
        // DATA GLOBAL UNTUK LAYOUT PULAU
        // =============================
        $islands = Island::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $carouselData = $islands->map(function (Island $item) {
            return [
                'place'       => $item->place_label,
                'title'       => $item->title,
                'title2'      => $item->subtitle,
                'description' => $item->short_description,
                'image'       => $item->image_url,
                'slug'        => $item->slug,
            ];
        });

        $island->load(['features', 'demographics']);

        $featuresByType = [
            'about'       => $island->features->where('type', 'about')->sortBy('order')->values(),
            'history'     => $island->features->where('type', 'history')->sortBy('order')->values(),
            'destination' => $island->features->where('type', 'destination')->sortBy('order')->values(),
            'food'        => $island->features->where('type', 'food')->sortBy('order')->values(),
            'culture'     => $island->features->where('type', 'culture')->sortBy('order')->values(),
        ];

        $demographics = [
            'religion'  => $island->demographics->where('type', 'religion')->sortBy('order')->values(),
            'ethnicity' => $island->demographics->where('type', 'ethnicity')->sortBy('order')->values(),
            'language'  => $island->demographics->where('type', 'language')->sortBy('order')->values(),
        ];

        // =============================
        // HISTORIES PER SUKU
        // =============================
        $histories = IslandHistory::query()
            ->where('island_id', $island->id)
            ->orderBy('order')
            ->orderBy('year_label')
            ->get();

        $historiesByTribe = $histories->groupBy('tribe');

        // =========================================================
        // ✅ AVAILABLE TRIBES (PRIORITAS: config tribes.php)
        // - aman untuk slug "papua&maluku"
        // =========================================================
        $availableTribes = $this->resolveTribesForIsland($island, $histories);

        // =============================
        // QUIZ PER SUKU (AKTIF)
        // =============================
        $quizRows = Quiz::query()
            ->active()
            ->where('scope', 'tribe')
            ->where('island_id', $island->id)
            ->whereNotNull('tribe')
            ->with(['questions.options'])
            ->latest()
            ->get();

        $quizzesByTribe = $quizRows->keyBy(fn ($q) => (string) $q->tribe);

        // fallback global
        $globalQuiz = Quiz::query()
            ->active()
            ->global()
            ->with(['questions.options'])
            ->latest()
            ->first();

        // =========================================================
        // ✅ WARISAN FLOW (PER SUKU)
        // =========================================================

        // 1) Ambil tribe dari query
        $tribeKey = trim((string) $request->query('tribe', ''));

        // 2) Kalau kosong, fallback ke tribe pertama
        if ($tribeKey === '' && !empty($availableTribes)) {
            $tribeKey = (string) $availableTribes[0];
        }

        // 3) Kalau tribeKey tidak valid, paksa balik ke pertama
        if ($tribeKey !== '' && !in_array($tribeKey, $availableTribes, true)) {
            $tribeKey = !empty($availableTribes) ? (string) $availableTribes[0] : '';
        }

        // 4) Default payload warisan
        $tribePage = null;
        $itemsByCategory = [
            'pakaian'           => collect(),
            'rumah_tradisi'     => collect(),
            'senjata_alatmusik' => collect(),
        ];

        // 5) Query DB warisan
        if ($tribeKey !== '') {
            $tribePage = TribePage::query()
                ->where('island_id', $island->id)
                ->where('tribe_key', $tribeKey)
                ->first();

            $items = HeritageItem::query()
                ->where('island_id', $island->id)
                ->where('tribe_key', $tribeKey)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            $itemsByCategory = [
                'pakaian'           => $items->where('category', 'pakaian')->values(),
                'rumah_tradisi'     => $items->where('category', 'rumah_tradisi')->values(),
                'senjata_alatmusik' => $items->where('category', 'senjata_alatmusik')->values(),
            ];
        }

        [$testimonials, $testimonialStats] = $this->getTestimonialsPayload();

        // =========================================================
        // ✅ RETURN VIEW: islands.blade.php (bukan islands.show)
        // =========================================================
        return view('islands', [
            'carouselData'      => $carouselData,
            'selectedIsland'    => $island,
            'featuresByType'    => $featuresByType,
            'demographics'      => $demographics,

            'historiesByTribe'  => $historiesByTribe,
            'availableTribes'   => $availableTribes,

            'testimonials'      => $testimonials,
            'testimonialStats'  => $testimonialStats,

            'quizzesByTribe'    => $quizzesByTribe,
            'globalQuiz'        => $globalQuiz,

            // ✅ WARISAN data
            'tribeKey'               => $tribeKey,
            'tribePage'              => $tribePage,
            'itemsByCategory'        => $itemsByCategory,
            'heritageCategoryLabels' => HeritageItem::CATEGORIES,
        ]);
    }

    /**
     * Ambil tribes dari config tribes.php dengan beberapa kandidat key
     * agar tidak kosong kalau slug tidak match persis.
     *
     * Aman untuk:
     * - "sunda-kecil" vs "sundakecil"
     * - "papua&maluku" tetap kebaca (sesuai config kamu)
     */
    private function resolveTribesForIsland(Island $island, $histories): array
    {
        $slug = (string) $island->slug;

        $candidates = array_values(array_unique([
            $slug,
            strtolower($slug),
            str_replace('-', '', strtolower($slug)),
            str_replace('-', '_', strtolower($slug)),
            str_replace('_', '', strtolower($slug)),
            str_replace(['-', '_'], '&', strtolower($slug)),
            str_replace(['-', '_'], '', str_replace('&', '', strtolower($slug))),
        ]));

        $tribes = [];

        foreach ($candidates as $key) {
            $tribes = config('tribes.' . $key, []);
            if (!empty($tribes)) {
                break;
            }
        }

        // fallback: dari histories (kalau config kosong)
        if (empty($tribes)) {
            $tribes = $histories->pluck('tribe')->filter()->unique()->values()->all();
        }

        // normalize string
        $tribes = array_values(array_filter(array_map(function ($t) {
            $t = trim((string) $t);
            return $t !== '' ? $t : null;
        }, $tribes)));

        return $tribes;
    }

    private function getTestimonialsPayload(): array
    {
        $testimonials = Testimonial::query()
            ->latest()
            ->take(50)
            ->get();

        $ratingCounts = Testimonial::query()
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        $counts = [];
        for ($i = 1; $i <= 5; $i++) {
            $counts[$i] = (int) ($ratingCounts[$i] ?? 0);
        }

        $total = array_sum($counts);

        $avg = $total > 0
            ? round((1*$counts[1] + 2*$counts[2] + 3*$counts[3] + 4*$counts[4] + 5*$counts[5]) / $total, 1)
            : 0.0;

        $percent = [];
        for ($i = 1; $i <= 5; $i++) {
            $percent[$i] = $total > 0 ? round(($counts[$i] / $total) * 100) : 0;
        }

        return [$testimonials, [
            'counts'  => $counts,
            'percent' => $percent,
            'total'   => $total,
            'avg'     => $avg,
        ]];
    }
}
