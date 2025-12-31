<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeritageItem;
use App\Models\Island;
use App\Models\TribePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class HeritageController extends Controller
{
    /**
     * Normalisasi list suku dari config tribes.php
     * - Kalau config-nya associative, kita ambil values-nya
     * - Trim biar aman dari spasi
     */
    private function normalizeTribes(array $tribes): array
    {
        // ambil values (aman untuk associative / indexed)
        $vals = array_values($tribes);

        // pastikan string + trim
        $vals = array_map(function ($v) {
            return is_string($v) ? trim($v) : (string) $v;
        }, $vals);

        // buang yang kosong
        $vals = array_values(array_filter($vals, fn($v) => $v !== ''));

        return $vals;
    }

    /**
     * Pastikan tribe_key valid untuk pulau, kalau tidak valid -> null
     */
    private function sanitizeSelectedTribe(?string $tribeKey, array $allowedTribes): ?string
    {
        $tribeKey = is_string($tribeKey) ? trim($tribeKey) : null;
        if (!$tribeKey) return null;

        // harus match persis (case-sensitive) sesuai config
        return in_array($tribeKey, $allowedTribes, true) ? $tribeKey : null;
    }

    public function index(Request $request)
    {
        $islands = Island::query()
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $selectedIslandId = $request->integer('island_id');

        // IMPORTANT: request('tribe') bisa jadi null/"" -> kita raw dulu, nanti disanitize
        $selectedTribeKeyRaw = $request->input('tribe'); // jangan pakai string()->toString() biar null tetap null

        $selectedIsland = null;
        $tribes = [];
        $tribePage = null;

        $itemsByCategory = [
            'pakaian' => collect(),
            'rumah_tradisi' => collect(),
            'senjata_alatmusik' => collect(),
        ];

        if ($selectedIslandId) {
            $selectedIsland = $islands->firstWhere('id', $selectedIslandId);

            if ($selectedIsland) {
                // normalisasi tribes supaya aman jika config associative
                $tribes = $this->normalizeTribes(config('tribes.' . $selectedIsland->slug, []));

                // sanitize pilihan suku dari query
                $selectedTribeKey = $this->sanitizeSelectedTribe(
                    is_string($selectedTribeKeyRaw) ? $selectedTribeKeyRaw : null,
                    $tribes
                );

                // kalau belum dipilih / tidak valid, default ke suku pertama (kalau ada)
                if (!$selectedTribeKey && count($tribes)) {
                    $selectedTribeKey = $tribes[0];
                }

                if ($selectedTribeKey) {
                    // === INI KUNCI: AMBIL HEADER HARUS island_id + tribe_key ===
                    $tribePage = TribePage::query()
                        ->where('island_id', $selectedIsland->id)
                        ->where('tribe_key', $selectedTribeKey)
                        ->first();

                    // === Ambil items juga harus island_id + tribe_key ===
                    $items = HeritageItem::query()
                        ->where('island_id', $selectedIsland->id)
                        ->where('tribe_key', $selectedTribeKey)
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->get();

                    $itemsByCategory = [
                        'pakaian' => $items->where('category', 'pakaian')->values(),
                        'rumah_tradisi' => $items->where('category', 'rumah_tradisi')->values(),
                        'senjata_alatmusik' => $items->where('category', 'senjata_alatmusik')->values(),
                    ];
                } else {
                    // tidak ada suku valid untuk pulau ini
                    $selectedTribeKey = null;
                }
            } else {
                $selectedTribeKey = null;
            }
        } else {
            $selectedTribeKey = null;
        }

        return view('admin.heritages.index', [
            'islands' => $islands,
            'selectedIsland' => $selectedIsland,
            'tribes' => $tribes,
            'selectedTribeKey' => $selectedTribeKey,
            'tribePage' => $tribePage,
            'itemsByCategory' => $itemsByCategory,
            'categoryLabels' => HeritageItem::CATEGORIES,
        ]);
    }

    public function savePage(Request $request)
    {
        $data = $request->validate([
            'island_id' => ['required', 'integer', 'exists:islands,id'],
            'tribe_key' => ['required', 'string', 'max:255'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_description' => ['nullable', 'string'],
            'hero_image' => ['nullable', 'image', 'max:2048'], // 2MB
        ]);

        $island = Island::findOrFail($data['island_id']);

        // normalisasi allowed tribes supaya match persis dengan yang di index
        $allowedTribes = $this->normalizeTribes(config('tribes.' . $island->slug, []));

        $tribeKey = trim($data['tribe_key'] ?? '');
        if ($tribeKey === '' || !in_array($tribeKey, $allowedTribes, true)) {
            return back()->withErrors(['tribe_key' => 'Suku tidak valid untuk pulau ini.'])->withInput();
        }

        // === INI KUNCI: SIMPAN HARUS island_id + tribe_key (sesuai unique index) ===
        $page = TribePage::query()->firstOrNew([
            'island_id' => $island->id,
            'tribe_key' => $tribeKey,
        ]);

        $page->hero_title = $data['hero_title'] ?? null;
        $page->hero_description = $data['hero_description'] ?? null;

        if ($request->hasFile('hero_image')) {
            if ($page->hero_image) {
                Storage::disk('public')->delete($page->hero_image);
            }
            $path = $request->file('hero_image')->store('tribe-pages', 'public');
            $page->hero_image = $path;
        }

        $page->save();

        // IMPORTANT: redirect harus bawa query tribe supaya gak balik ke suku pertama
        return redirect()->route('admin.heritages.index', [
            'island_id' => $island->id,
            'tribe' => $tribeKey,
        ])->with('success', 'Header suku berhasil disimpan.');
    }

    public function storeItem(Request $request)
    {
        $data = $request->validate([
            'island_id' => ['required', 'integer', 'exists:islands,id'],
            'tribe_key' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(array_keys(HeritageItem::CATEGORIES))],

            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:3072'], // 3MB
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);

        $island = Island::findOrFail($data['island_id']);

        $allowedTribes = $this->normalizeTribes(config('tribes.' . $island->slug, []));

        $tribeKey = trim($data['tribe_key'] ?? '');
        if ($tribeKey === '' || !in_array($tribeKey, $allowedTribes, true)) {
            return back()->withErrors(['tribe_key' => 'Suku tidak valid untuk pulau ini.'])->withInput();
        }

        $item = new HeritageItem();
        $item->island_id = $island->id;
        $item->tribe_key = $tribeKey;
        $item->category = $data['category'];
        $item->title = $data['title'];
        $item->description = $data['description'] ?? null;
        $item->sort_order = (int)($data['sort_order'] ?? 0);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('heritages', 'public');
            $item->image_path = $path;
        }

        $item->save();

        return redirect()->route('admin.heritages.index', [
            'island_id' => $island->id,
            'tribe' => $tribeKey,
        ])->with('success', 'Item warisan berhasil ditambahkan.');
    }

    public function updateItem(Request $request, HeritageItem $item)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:3072'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);

        $item->title = $data['title'];
        $item->description = $data['description'] ?? null;

        if (array_key_exists('sort_order', $data)) {
            $item->sort_order = (int)$data['sort_order'];
        }

        if ($request->hasFile('image')) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $path = $request->file('image')->store('heritages', 'public');
            $item->image_path = $path;
        }

        $item->save();

        return redirect()->route('admin.heritages.index', [
            'island_id' => $item->island_id,
            'tribe' => $item->tribe_key,
        ])->with('success', 'Item warisan berhasil diupdate.');
    }

    public function destroyItem(HeritageItem $item)
    {
        $islandId = $item->island_id;
        $tribeKey = $item->tribe_key;

        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();

        return redirect()->route('admin.heritages.index', [
            'island_id' => $islandId,
            'tribe' => $tribeKey,
        ])->with('success', 'Item warisan berhasil dihapus.');
    }
}
