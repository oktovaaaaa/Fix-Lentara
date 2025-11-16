<?php

namespace App\Http\Controllers;

use App\Models\Island;
use App\Models\IslandHistory;
use Illuminate\Http\Request;

class IslandController extends Controller
{
    /**
     * (Opsional) index untuk admin statistik
     */
    public function index()
    {
        $islands = Island::orderBy('order')->get();

        return view('admin.stats.index', compact('islands'));
    }

    /**
     * Halaman landing (home) dengan carousel pulau
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

        return view('home', [
            'carouselData'   => $carouselData,
            'selectedIsland' => null,
            'featuresByType' => [],
            'demographics'   => [
                'religion'  => collect(),
                'ethnicity' => collect(),
                'language'  => collect(),
            ],
        ]);
    }

    /**
     * Halaman detail tiap pulau (Sumatera, Jawa, dll)
     */
    public function show(Island $island)
    {
        // pastikan pulau aktif
        abort_unless($island->is_active, 404);

        // ====== DATA UNTUK CAROUSEL DI HERO ======
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

        // ====== RELASI FEATURES & DEMOGRAPHICS ======
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

        // ====== HISTORY PULAU & SUKU (untuk Sumatera) ======
        $historiesByTribe = collect();

        if ($island->slug === 'sumatera') {
            // ambil semua history untuk pulau Sumatera (island_id = id Sumatera)
            $histories = IslandHistory::where('island_id', $island->id)
                ->orderBy('order')
                ->orderBy('id')
                ->get();

            // group by kolom `tribe` -> 'Aceh', 'Batak', 'Minangkabau'
            $historiesByTribe = $histories->groupBy('tribe');
        }

        // ====== PILIH VIEW BERDASARKAN SLUG ======
        $viewName = 'islands.' . $island->slug;

        // kalau file khusus belum ada, pakai view default
        if (!view()->exists($viewName)) {
            $viewName = 'islands.default';
        }

        return view($viewName, [
            'carouselData'   => $carouselData,
            'selectedIsland' => $island,
            'featuresByType' => $featuresByType,
            'demographics'   => $demographics,
            'historiesByTribe' => $historiesByTribe, // ⬅️ DIKIRIM KE VIEW
        ]);
    }
}
