<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\IslandAboutPage;
use App\Models\IslandAboutItem;
use App\Models\IslandDemographic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class IslandAboutStatsController extends Controller
{
    public function index(Request $request)
    {
        $islands = Island::query()->orderBy('order')->orderBy('name')->get();

        // island aktif berdasarkan picker (fallback pertama)
        $slug = (string) $request->query('island', $islands->first()?->slug);
        $activeIsland = $islands->firstWhere('slug', $slug) ?? $islands->first();

        abort_unless($activeIsland, 404);

        // ABOUT header + items
        $aboutPage = IslandAboutPage::query()
            ->where('island_id', $activeIsland->id)
            ->first();

        $aboutItems = IslandAboutItem::query()
            ->where('island_id', $activeIsland->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // Statistik (data sama seperti halaman stats kamu)
        $religions = IslandDemographic::query()
            ->where('island_id', $activeIsland->id)
            ->where('type', 'religion')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $ethnicities = IslandDemographic::query()
            ->where('island_id', $activeIsland->id)
            ->where('type', 'ethnicity')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $languages = IslandDemographic::query()
            ->where('island_id', $activeIsland->id)
            ->where('type', 'language')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('admin.islands.about-stats', compact(
            'islands',
            'activeIsland',
            'aboutPage',
            'aboutItems',
            'religions',
            'ethnicities',
            'languages'
        ));
    }

    public function upsertAboutPage(Request $request, Island $island)
    {
        $data = $request->validate([
            'label_small' => ['nullable','string','max:255'],
            'hero_title' => ['nullable','string','max:255'],
            'hero_description' => ['nullable','string'],
            'more_link' => ['nullable','string','max:2048'],
        ]);

        IslandAboutPage::updateOrCreate(
            ['island_id' => $island->id],
            $data
        );

        return back()->with('status', 'Header About Pulau disimpan.');
    }

public function storeItem(Request $request, Island $island)
{
    $data = $request->validate([
        'title'       => ['nullable','string','max:255'],
        'description' => ['required','string'],
        'points'      => ['nullable','string'],

        // baru:
        'image_url'   => ['nullable','string','max:2048'],
        'image_file'  => ['nullable','image','max:4096'], // 4MB

        'more_link'   => ['nullable','string','max:2048'],
        'sort_order'  => ['nullable','integer'],
    ]);

    // pilih image: upload > link
    $image = null;

    if ($request->hasFile('image_file')) {
        $path  = $request->file('image_file')->store('about-items', 'public');
        $image = '/storage/' . $path;
    } else {
        $url = trim((string)($data['image_url'] ?? ''));
        $image = $url !== '' ? $url : null;
    }

    IslandAboutItem::create([
        'island_id'   => $island->id,
        'title'       => $data['title'] ?? null,
        'description' => $data['description'],
        'points'      => $data['points'] ?? null,
        'image'       => $image,
        'more_link'   => $data['more_link'] ?? null,
        'sort_order'  => $data['sort_order'] ?? 0,
    ]);

    return back()->with('status', 'Item About ditambahkan.');
}


public function updateItem(Request $request, Island $island, IslandAboutItem $item)
{
    abort_unless($item->island_id === $island->id, 404);

    $data = $request->validate([
        'title'       => ['nullable','string','max:255'],
        'description' => ['required','string'],
        'points'      => ['nullable','string'],

        // baru:
        'image_url'   => ['nullable','string','max:2048'],
        'image_file'  => ['nullable','image','max:4096'],

        'more_link'   => ['nullable','string','max:2048'],
        'sort_order'  => ['nullable','integer'],
    ]);

    $payload = [
        'title'       => $data['title'] ?? null,
        'description' => $data['description'],
        'points'      => $data['points'] ?? null,
        'more_link'   => $data['more_link'] ?? null,
        'sort_order'  => $data['sort_order'] ?? $item->sort_order,
    ];

    // hanya update image kalau user isi salah satu
    if ($request->hasFile('image_file')) {
        $path = $request->file('image_file')->store('about-items', 'public');
        $payload['image'] = '/storage/' . $path;
    } else {
        $url = trim((string)($data['image_url'] ?? ''));
        if ($url !== '') {
            $payload['image'] = $url;
        }
    }

    $item->update($payload);

    return back()->with('status', 'Item About diupdate.');
}


    public function destroyItem(Request $request, Island $island, IslandAboutItem $item)
    {
        abort_unless($item->island_id === $island->id, 404);

        $item->delete();

        return back()->with('status', 'Item About dihapus.');
    }
}
