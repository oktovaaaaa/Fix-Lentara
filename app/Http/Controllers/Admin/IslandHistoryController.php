<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\IslandHistory;
use Illuminate\Http\Request;

class IslandHistoryController extends Controller
{
    /**
     * List semua history (bisa difilter per pulau & suku).
     */
    public function index(Request $request)
    {
        $islandId = $request->get('island_id');
        $tribe    = $request->get('tribe');

        $query = IslandHistory::with('island')->orderBy('order')->orderBy('id');

        if ($islandId) {
            $query->where('island_id', $islandId);
        }

        if ($tribe) {
            $query->where('tribe', $tribe);
        }

        $histories = $query->paginate(15);
        $islands   = Island::orderBy('order')->get();

        // daftar suku khusus Sumatera (Aceh / Batak / Minangkabau)
        $sumateraTribes = ['Aceh', 'Batak', 'Minangkabau'];

        return view('admin.histories.index', compact(
            'histories',
            'islands',
            'sumateraTribes',
            'islandId',
            'tribe'
        ));
    }

    /**
     * Form tambah history baru.
     */
    public function create()
    {
        $islands = Island::orderBy('order')->get();
        $sumateraTribes = ['Aceh', 'Batak', 'Minangkabau'];

        return view('admin.histories.create', compact('islands', 'sumateraTribes'));
    }

    /**
     * Simpan history baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'island_id'  => ['required', 'exists:islands,id'],
            'tribe'      => ['required', 'string', 'max:50'], // untuk semua pulau, isinya bebas; Sumatera pakai dropdown
            'year_label' => ['required', 'string', 'max:100'],
            'title'      => ['required', 'string', 'max:255'],
            'content'    => ['required', 'string'],
            'more_link'  => ['nullable', 'url'],
            'order'      => ['nullable', 'integer', 'min:0'],
        ]);

        IslandHistory::create($validated);

        return redirect()
            ->route('admin.histories.index')
            ->with('success', 'History berhasil ditambahkan.');
    }

    /**
     * Form edit.
     */
    public function edit(IslandHistory $history)
    {
        $islands = Island::orderBy('order')->get();
        $sumateraTribes = ['Aceh', 'Batak', 'Minangkabau'];

        return view('admin.histories.edit', [
            'history'        => $history,
            'islands'        => $islands,
            'sumateraTribes' => $sumateraTribes,
        ]);
    }

    /**
     * Update history.
     */
    public function update(Request $request, IslandHistory $history)
    {
        $validated = $request->validate([
            'island_id'  => ['required', 'exists:islands,id'],
            'tribe'      => ['required', 'string', 'max:50'],
            'year_label' => ['required', 'string', 'max:100'],
            'title'      => ['required', 'string', 'max:255'],
            'content'    => ['required', 'string'],
            'more_link'  => ['nullable', 'url'],
            'order'      => ['nullable', 'integer', 'min:0'],
        ]);

        $history->update($validated);

        return redirect()
            ->route('admin.histories.index')
            ->with('success', 'History berhasil diperbarui.');
    }

    /**
     * Hapus history.
     */
    public function destroy(IslandHistory $history)
    {
        $history->delete();

        return redirect()
            ->route('admin.histories.index')
            ->with('success', 'History berhasil dihapus.');
    }
}
