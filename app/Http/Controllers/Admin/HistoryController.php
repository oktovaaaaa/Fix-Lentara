<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\IslandHistory;
use App\Models\TribePage;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $islandId = $request->get('island_id');
        $tribe    = $request->get('tribe');

        $islands   = Island::orderBy('order')->get();
        $histories = IslandHistory::with('island');
        $tribes    = [];

        if ($islandId) {
            $island = Island::find($islandId);

            if ($island) {
                $histories->where('island_id', $islandId);
                $tribes = config("tribes.{$island->slug}", []);
            }
        }

        if (!empty($tribe)) {
            $histories->where('tribe', $tribe);
        }

        $histories = $histories
            ->orderBy('order')
            ->orderBy('year_label')
            ->paginate(15);

        return view('admin.histories.index', [
            'histories' => $histories,
            'islands'   => $islands,
            'tribes'    => $tribes,
            'islandId'  => $islandId,
            'tribe'     => $tribe,
        ]);
    }

    public function create()
    {
        $islands = Island::orderBy('order')->get();
        $tribesConfig = config('tribes') ?? [];

        $selectedIslandId = old('island_id') ?? $islands->first()?->id;
        $selectedIsland   = $islands->firstWhere('id', $selectedIslandId);

        $tribes = $selectedIsland
            ? ($tribesConfig[$selectedIsland->slug] ?? [])
            : [];

        $oldTribe  = old('tribe');
        $tribePage = null;

        if ($selectedIslandId && $oldTribe) {
            $tribePage = TribePage::where('island_id', $selectedIslandId)
                ->where('tribe_key', $oldTribe)
                ->first();
        }

        return view('admin.histories.create', [
            'islands'          => $islands,
            'tribes'           => $tribes,
            'tribesConfig'     => $tribesConfig,
            'selectedIslandId' => $selectedIslandId,
            'tribePage'        => $tribePage,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'island_id'        => 'required|exists:islands,id',
            'tribe'            => 'required|string|max:100',
            'year_label'       => 'required|string|max:100',
            'title'            => 'required|string|max:255',
            'content'          => 'required|string',
            'more_link'        => 'nullable|url',
            'order'            => 'nullable|integer|min:0',

            'hero_title'       => 'nullable|string|max:255',
            'hero_description' => 'nullable|string',
            'hero_image'       => 'nullable|string|max:255',
        ]);

        $validated['order'] = $validated['order'] ?? 0;

        TribePage::updateOrCreate(
            [
                'island_id' => $validated['island_id'],
                'tribe_key' => $validated['tribe'],
            ],
            [
                'hero_title'       => $validated['hero_title'] ?? null,
                'hero_description' => $validated['hero_description'] ?? null,
                'hero_image'       => $validated['hero_image'] ?? null,
            ]
        );

        unset($validated['hero_title'], $validated['hero_description'], $validated['hero_image']);

        IslandHistory::create($validated);

        return redirect()
            ->route('admin.histories.index')
            ->with('success', 'History berhasil ditambahkan.');
    }

    public function edit(IslandHistory $history)
    {
        $islands = Island::orderBy('order')->get();
        $history->load('island');

        $tribes = [];
        if ($history->island) {
            $tribes = config("tribes." . $history->island->slug, []);
        }

        $tribePage = TribePage::where('island_id', $history->island_id)
            ->where('tribe_key', $history->tribe)
            ->first();

        return view('admin.histories.edit', [
            'history'   => $history,
            'islands'   => $islands,
            'tribes'    => $tribes,
            'tribePage' => $tribePage,
        ]);
    }

    public function update(Request $request, IslandHistory $history)
    {
        $validated = $request->validate([
            'island_id'        => 'required|exists:islands,id',
            'tribe'            => 'required|string|max:100',
            'year_label'       => 'required|string|max:100',
            'title'            => 'required|string|max:255',
            'content'          => 'required|string',
            'more_link'        => 'nullable|url',
            'order'            => 'nullable|integer|min:0',

            'hero_title'       => 'nullable|string|max:255',
            'hero_description' => 'nullable|string',
            'hero_image'       => 'nullable|string|max:255',
        ]);

        $validated['order'] = $validated['order'] ?? 0;

        TribePage::updateOrCreate(
            [
                'island_id' => $validated['island_id'],
                'tribe_key' => $validated['tribe'],
            ],
            [
                'hero_title'       => $validated['hero_title'] ?? null,
                'hero_description' => $validated['hero_description'] ?? null,
                'hero_image'       => $validated['hero_image'] ?? null,
            ]
        );

        unset($validated['hero_title'], $validated['hero_description'], $validated['hero_image']);

        $history->update($validated);

        return redirect()
            ->route('admin.histories.index')
            ->with('success', 'History berhasil diperbarui.');
    }

    public function destroy(IslandHistory $history)
    {
        $history->delete();

        return redirect()
            ->route('admin.histories.index')
            ->with('success', 'History berhasil dihapus.');
    }

    /**
     * GET /admin/tribe-pages/lookup?island_id=1&tribe_key=Aceh
     */
    public function lookupTribePage(Request $request)
    {
        $islandId = $request->get('island_id');
        $tribeKey = $request->get('tribe_key');

        if (!$islandId || !$tribeKey) {
            return response()->json(null);
        }

        $page = TribePage::where('island_id', $islandId)
            ->where('tribe_key', $tribeKey)
            ->first();

        return response()->json($page);
    }
}
