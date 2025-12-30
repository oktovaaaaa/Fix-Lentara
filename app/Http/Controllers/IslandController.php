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
     * ✅ Tambahan: Warisan per suku (Header + 3 kategori)
     *   - pilih suku via query ?tribe=Aceh
     */
    public function show(Island $island, Request $request)
    {
        abort_unless($island->is_active, 404);

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

        // histories per suku
        $histories = IslandHistory::where('island_id', $island->id)
            ->orderBy('order')
            ->orderBy('year_label')
            ->get();

        $historiesByTribe = $histories->groupBy('tribe');

        $availableTribes = config("tribes.{$island->slug}") ?? [];
        if (empty($availableTribes)) {
            $availableTribes = $histories->pluck('tribe')->unique()->values()->all();
        }

        // ===== QUIZ PER SUKU (AKTIF) =====
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
        // - tribeKey dipilih dari query ?tribe=...
        // - fallback ke tribe pertama dari config tribes.php
        // - ambil header (TribePage) + items (HeritageItem) 3 kategori
        // =========================================================

        // 1) Ambil tribe dari query, kalau tidak ada -> tribe pertama
        $tribeKey = (string) $request->query('tribe', '');
        $tribeKey = trim($tribeKey);

        if ($tribeKey === '' && !empty($availableTribes)) {
            $tribeKey = (string) $availableTribes[0];
        }

        // 2) Validasi tribeKey harus ada di availableTribes (biar aman)
        if ($tribeKey !== '' && !in_array($tribeKey, $availableTribes, true)) {
            $tribeKey = !empty($availableTribes) ? (string) $availableTribes[0] : '';
        }

        // 3) Default payload warisan
        $tribePage = null;

        $itemsByCategory = [
            'pakaian'           => collect(),
            'rumah_tradisi'     => collect(),
            'senjata_alatmusik' => collect(),
        ];

        // 4) Kalau tribeKey valid, query DB warisan
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

        // =========================================================

        [$testimonials, $testimonialStats] = $this->getTestimonialsPayload();

        // ✅ View name:
        // - default: islands.{slug}
        // - tambahan: kalau slug mengandung karakter aneh, coba juga versi raw file yang kamu pakai (mis. papua&maluku)
        $viewName = 'islands.' . $island->slug;

        // fallback kalau view belum ada:
        if (!view()->exists($viewName)) {
            // coba versi slug aman (replace '-'<->'&' tidak kita tebak macam-macam),
            // tapi minimal ini bantu kalau kamu suatu saat rename.
            $alt = 'islands.' . str_replace('&', '&', $island->slug); // no-op, tapi disisakan supaya kamu gampang modif
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

            // ✅ WARISAN data dikirim ke view
            'tribeKey'              => $tribeKey,
            'tribePage'             => $tribePage,
            'itemsByCategory'       => $itemsByCategory,
            'heritageCategoryLabels'=> HeritageItem::CATEGORIES,
        ]);
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
