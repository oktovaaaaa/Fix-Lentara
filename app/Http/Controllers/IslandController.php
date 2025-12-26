<?php

namespace App\Http\Controllers;

use App\Models\Island;
use App\Models\IslandHistory;
use App\Models\Testimonial;
use App\Models\Quiz;
use Illuminate\Http\Request;

class IslandController extends Controller
{
    public function index()
    {
        $islands = Island::orderBy('order')->get();

        return view('admin.stats.index', compact('islands'));
    }

    /**
     * Halaman utama (Budaya Indonesia)
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

        // ===== QUIZ (AKTIF) =====
        $quiz = Quiz::query()
            ->where('is_active', true)
            ->with(['questions.options'])
            ->latest()
            ->first();

        // ===== TESTIMONI (LIST + STATS) =====
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

            // wajib supaya blade tidak undefined
            'testimonials'      => $testimonials,
            'testimonialStats'  => $testimonialStats,

            // QUIZ untuk section #quiz di home
            'quiz'              => $quiz,
        ]);
    }

    /**
     * Halaman detail tiap pulau (Sumatera, Jawa, dll)
     */
    public function show(Island $island)
    {
        abort_unless($island->is_active, 404);

        // ===== CAROUSEL DATA UNTUK SEMUA PULAU =====
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

        // ===== FEATURES & DEMOGRAPHICS UNTUK PULAU TERPILIH =====
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

        // ====== HISTORY PER SUKU UNTUK PULAU INI ======
        $histories = IslandHistory::where('island_id', $island->id)
            ->orderBy('order')
            ->orderBy('year_label')
            ->get();

        $historiesByTribe = $histories->groupBy('tribe');

        $availableTribes = config("tribes.{$island->slug}") ?? [];
        if (empty($availableTribes)) {
            $availableTribes = $histories->pluck('tribe')->unique()->values()->all();
        }

        // ===== QUIZ (AKTIF) =====
        $quiz = Quiz::query()
            ->where('is_active', true)
            ->with(['questions.options'])
            ->latest()
            ->first();

        // ===== TESTIMONI (LIST + STATS) =====
        // kalau kamu memang menampilkan section testimoni di halaman pulau,
        // variabel ini harus ikut dikirim supaya blade aman.
        [$testimonials, $testimonialStats] = $this->getTestimonialsPayload();

        // ====== TENTUKAN NAMA VIEW BERDASARKAN SLUG ======
        $viewName = 'islands.' . $island->slug;
        if (!view()->exists($viewName)) {
            $viewName = 'islands.default';
        }

        return view($viewName, [
            'carouselData'      => $carouselData,
            'selectedIsland'    => $island,
            'featuresByType'    => $featuresByType,
            'demographics'      => $demographics,
            'historiesByTribe'  => $historiesByTribe,
            'availableTribes'   => $availableTribes,

            // wajib kalau section testimoni dipakai juga di view pulau
            'testimonials'      => $testimonials,
            'testimonialStats'  => $testimonialStats,

            // QUIZ untuk section #quiz di halaman pulau juga (kalau dipakai)
            'quiz'              => $quiz,
        ]);
    }

    /**
     * Ambil list testimoni + statistik rating untuk UI.
     * Return: [Collection $testimonials, array $stats]
     */
    private function getTestimonialsPayload(): array
    {
        // LIST: ambil yang terbaru (kamu bisa ganti paginate kalau mau)
        $testimonials = Testimonial::query()
            ->latest()
            ->take(50) // biar gak berat, list bisa scroll di UI
            ->get();

        // COUNT per rating
        $ratingCounts = Testimonial::query()
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        // rapikan 1..5
        $counts = [];
        for ($i = 1; $i <= 5; $i++) {
            $counts[$i] = (int) ($ratingCounts[$i] ?? 0);
        }

        $total = array_sum($counts);

        $avg = $total > 0
            ? round(
                (1 * $counts[1] + 2 * $counts[2] + 3 * $counts[3] + 4 * $counts[4] + 5 * $counts[5]) / $total,
                1
            )
            : 0.0;

        // persentase untuk progress bar
        $percent = [];
        for ($i = 1; $i <= 5; $i++) {
            $percent[$i] = $total > 0 ? round(($counts[$i] / $total) * 100) : 0;
        }

        $stats = [
            'counts'  => $counts,   // [1=>..,2=>..,3=>..,4=>..,5=>..]
            'percent' => $percent,  // [1=>..%,..]
            'total'   => $total,    // total rating
            'avg'     => $avg,      // avg rating (1 desimal)
        ];

        return [$testimonials, $stats];
    }
}
