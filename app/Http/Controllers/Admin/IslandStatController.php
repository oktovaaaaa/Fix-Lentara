<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\IslandDemographic;
use Illuminate\Http\Request;

class IslandStatController extends Controller
{
    public function index(Request $request)
    {
        // Semua pulau buat picker
        $islands = Island::where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        // Pulau aktif
        $activeSlug   = $request->query('island', $islands->first()?->slug);
        $activeIsland = Island::where('slug', $activeSlug)->firstOrFail();

        // Data demografi per kategori
        $religions = $activeIsland->demographics()
            ->where('type', 'religion')
            ->orderBy('order')
            ->get();

        $ethnicities = $activeIsland->demographics()
            ->where('type', 'ethnicity')
            ->orderBy('order')
            ->get();

        $languages = $activeIsland->demographics()
            ->where('type', 'language')
            ->orderBy('order')
            ->get();

        return view('admin.stats.index', [
            'islands'      => $islands,
            'activeIsland' => $activeIsland,
            'religions'    => $religions,
            'ethnicities'  => $ethnicities,
            'languages'    => $languages,
        ]);
    }

    // update jumlah penduduk
    public function updatePopulation(Request $request, Island $island)
    {
        $data = $request->validate([
            'population' => ['nullable', 'integer', 'min:0'],
        ]);

        $island->update($data);

        return back()->with('status', 'Jumlah penduduk diperbarui.');
    }

    // tambah data agama/suku/bahasa
    public function storeDemographic(Request $request, Island $island)
    {
        $data = $request->validate([
            'type'       => ['required', 'in:religion,ethnicity,language'],
            'label'      => ['required', 'string', 'max:255'],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'order'      => ['nullable', 'integer', 'min:0'],
        ]);

        $data['island_id'] = $island->id;

        IslandDemographic::create($data);

        return back()->with('status', 'Data demografi ditambahkan.');
    }

    // update satu baris demografi (kalau nanti mau form khusus/edit)
    public function updateDemographic(Request $request, Island $island, IslandDemographic $demographic)
    {
        abort_unless($demographic->island_id === $island->id, 404);

        $data = $request->validate([
            'label'      => ['required', 'string', 'max:255'],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'order'      => ['nullable', 'integer', 'min:0'],
        ]);

        $demographic->update($data);

        return back()->with('status', 'Data demografi diperbarui.');
    }

    // hapus data
    public function destroyDemographic(Island $island, IslandDemographic $demographic)
    {
        abort_unless($demographic->island_id === $island->id, 404);

        $demographic->delete();

        return back()->with('status', 'Data demografi dihapus.');
    }
}
