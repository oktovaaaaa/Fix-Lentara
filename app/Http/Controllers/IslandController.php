<?php

namespace App\Http\Controllers;

use App\Models\Island;

class IslandController extends Controller
{

    public function index()
{
    $islands = Island::orderBy('order')->get();

    return view('admin.stats.index', compact('islands'));
}

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

    return view('landing', [
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



public function show(Island $island)
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

    // load relasi features + demographics
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

    return view('landing', [
        'carouselData'   => $carouselData,
        'selectedIsland' => $island,
        'featuresByType' => $featuresByType,
        'demographics'   => $demographics,
    ]);
}

}
