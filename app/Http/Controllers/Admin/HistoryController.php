<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\IslandHistory;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * List semua history (bisa difilter per pulau & suku).
     */
    public function index(Request $request)
    {
        $islandId = $request->island_id;
        $tribe    = $request->tribe;

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

        if ($tribe) {
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

    /**
     * Form tambah history.
     */
    public function create()
    {
        $islands = Island::orderBy('order')->get();

        $tribesConfig = config('tribes') ?? [];

        $selectedIslandId = old('island_id') ?? $islands->first()?->id;
        $selectedIsland   = $islands->firstWhere('id', $selectedIslandId);

        $tribes = $selectedIsland
            ? ($tribesConfig[$selectedIsland->slug] ?? [])
            : [];

        return view('admin.histories.create', [
            'islands'          => $islands,
            'tribes'           => $tribes,
            'tribesConfig'     => $tribesConfig,
            'selectedIslandId' => $selectedIslandId,
        ]);
    }

    /**
     * SIMPAN history baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'island_id'  => 'required|exists:islands,id',
            'tribe'      => 'required|string|max:100',
            'year_label' => 'required|string|max:100',
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'more_link'  => 'nullable|url',
            'order'      => 'nullable|integer|min:0',
        ]);

        if (! isset($validated['order'])) {
            $validated['order'] = 0;
        }

        IslandHistory::create($validated);

        return redirect()
            ->route('admin.histories.index')
            ->with('success', 'History berhasil ditambahkan.');
    }

    /**
     * Form edit history.
     */
    public function edit(IslandHistory $history)
    {
        $islands = Island::orderBy('order')->get();
        $history->load('island');

        $tribes = [];
        if ($history->island) {
            $tribes = config("tribes." . $history->island->slug, []);
        }

        return view('admin.histories.edit', [
            'history' => $history,
            'islands' => $islands,
            'tribes'  => $tribes,
        ]);
    }

    /**
     * UPDATE history.
     */
    public function update(Request $request, IslandHistory $history)
    {
        $validated = $request->validate([
            'island_id'  => 'required|exists:islands,id',
            'tribe'      => 'required|string|max:100',
            'year_label' => 'required|string|max:100',
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'more_link'  => 'nullable|url',
            'order'      => 'nullable|integer|min:0',
        ]);

        if (! isset($validated['order'])) {
            $validated['order'] = 0;
        }

        $history->update($validated);

        return redirect()
            ->route('admin.histories.index')
            ->with('success', 'History berhasil diperbarui.');
    }

    /**
     * HAPUS history.
     */
    public function destroy(IslandHistory $history)
    {
        $history->delete();

        return redirect()
            ->route('admin.histories.index')
            ->with('success', 'History berhasil dihapus.');
    }
}
