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
     * Detail pulau
     * Quiz di pulau = per suku (scope tribe)
     *
     * ✅ Warisan per suku (Header + 3 kategori)
     *   - pilih suku via query ?tribe=Aceh
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
            'about'       => $island->features->where('type', 'about')->sortBy('order'),
            'history'     => $island->features->where('type', 'history')->sortBy('order'),
            'destination' => $island->features->where('type', 'destination')->sortBy('order'),
            'food'        => $island->features->where('type', 'food')->sortBy('order'),
            'culture'     => $island->features->where('type', 'culture')->sortBy('order'),
        ];

        $demographics = [
            'religion'  => $island->demographics->where('type', 'religion')->sortBy('order')->values(),
            'ethnicity' => $island->demographics->where('type', 'ethnicity')->sortBy('order')->values(),
            'language'  => $island->demographics->where('type', 'language')->sortBy('order')->values(),
        ];

        // =============================
        // HISTORIES PER SUKU
        // =============================
        $histories = IslandHistory::where('island_id', $island->id)
            ->orderBy('order')
            ->orderBy('year_label')
            ->get();

        $historiesByTribe = $histories->groupBy('tribe');

        // =========================================================
        // ✅ AVAILABLE TRIBES (PRIORITAS: config tribes.php)
        // - Masalah umum: slug island kadang beda dengan key config
        // - Kita coba beberapa kandidat key biar nggak kosong
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
        // ✅ WARISAN FLOW (PER SUKU) - FIX UTAMA
        // - tribeKey dipilih dari query ?tribe=...
        // - VALIDASI: harus termasuk availableTribes
        // - Query header + items pakai (island_id + tribe_key)
        // =========================================================

        // 1) Ambil tribe dari query
        $tribeKey = (string) $request->query('tribe', '');
        $tribeKey = trim($tribeKey);

        // 2) Kalau kosong, fallback ke tribe pertama
        if ($tribeKey === '' && !empty($availableTribes)) {
            $tribeKey = (string) $availableTribes[0];
        }

        // 3) Kalau tribeKey tidak valid, paksa balik ke pertama
        if ($tribeKey !== '' && !in_array($tribeKey, $availableTribes, true)) {
            $tribeKey = !empty($availableTribes) ? (string) $availableTribes[0] : '';
        }

        // 4) Default payload
        $tribePage = null;
        $itemsByCategory = [
            'pakaian'           => collect(),
            'rumah_tradisi'     => collect(),
            'senjata_alatmusik' => collect(),
        ];

        // 5) Query DB warisan
        if ($tribeKey !== '') {
            // ✅ header per suku (bukan per pulau)
            $tribePage = TribePage::query()
                ->where('island_id', $island->id)
                ->where('tribe_key', $tribeKey)
                ->first();

            // ✅ item per suku
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

        // =========================================================

        [$testimonials, $testimonialStats] = $this->getTestimonialsPayload();

        // =========================================================
        // ✅ VIEW NAME FIX:
        // - slug seperti "papua&maluku" bikin nama file view jadi aneh
        // - prioritas: islands.{slug}
        // - fallback: islands.{slug_sanitized}
        // - fallback terakhir: islands.default
        // =========================================================
        $viewName = 'islands.' . $island->slug;

        if (!view()->exists($viewName)) {
            $sanitized = $this->sanitizeViewKey($island->slug);
            $alt = 'islands.' . $sanitized;

            if (view()->exists($alt)) {
                $viewName = $alt;
            } else {
                $viewName = 'islands.default';
            }
        }

        return view($viewName, [
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
     * agar tidak “kosong” kalau slug tidak match persis.
     */
    private function resolveTribesForIsland(Island $island, $histories)
    {
        $slug = (string) $island->slug;

        // kandidat key untuk config('tribes.<key>')
        $candidates = array_values(array_unique([
            $slug,
            strtolower($slug),
            str_replace('-', '', strtolower($slug)),
            str_replace('-', '_', strtolower($slug)),
            str_replace('_', '', strtolower($slug)),
            str_replace(['-', '_'], '&', strtolower($slug)), // jaga-jaga ada yang pakai &
            str_replace(['-', '_'], '', str_replace('&', '', strtolower($slug))),
        ]));

        $tribes = [];

        foreach ($candidates as $key) {
            $tribes = config('tribes.' . $key, []);
            if (!empty($tribes)) {
                break;
            }
        }

        // fallback: dari histories
        if (empty($tribes)) {
            $tribes = $histories->pluck('tribe')->filter()->unique()->values()->all();
        }

        // pastikan tipe string rapi
        $tribes = array_values(array_filter(array_map(function ($t) {
            $t = trim((string) $t);
            return $t !== '' ? $t : null;
        }, $tribes)));

        return $tribes;
    }

    /**
     * Sanitize slug untuk nama view.
     * Contoh: "papua&maluku" -> "papua_maluku"
     */
    private function sanitizeViewKey(string $slug): string
    {
        $slug = strtolower($slug);
        $slug = str_replace(['&', ' '], '_', $slug);
        $slug = preg_replace('/[^a-z0-9_\-]/', '_', $slug);
        $slug = preg_replace('/_+/', '_', $slug);
        return trim($slug, '_');
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
