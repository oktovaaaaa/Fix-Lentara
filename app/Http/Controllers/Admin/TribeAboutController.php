<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\TribeAboutItem;
use App\Models\TribeAboutPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TribeAboutController extends Controller
{
    public function index(Request $request)
    {
        $islands = Island::query()->orderBy('order')->orderBy('name')->get();

        // tribes config (dipakai JS picker)
        $tribesConfig = config('tribes', []);

        $selectedIslandId = $request->query('island_id');
        $selectedTribeKey = $request->query('tribe_key');

        $aboutPage = null;
        $aboutItems = collect();

        if ($selectedIslandId && $selectedTribeKey) {
            $aboutPage = TribeAboutPage::query()
                ->where('island_id', $selectedIslandId)
                ->where('tribe_key', $selectedTribeKey)
                ->first();

            $aboutItems = TribeAboutItem::query()
                ->where('island_id', $selectedIslandId)
                ->where('tribe_key', $selectedTribeKey)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        return view('admin.abouts.index', compact(
            'islands',
            'tribesConfig',
            'selectedIslandId',
            'selectedTribeKey',
            'aboutPage',
            'aboutItems'
        ));
    }

    /**
     * JSON lookup untuk auto-load header:
     * GET /admin/about-pages/lookup?island_id=1&tribe_key=Aceh
     */
    public function lookupAboutPage(Request $request)
    {
        $islandId = (int) $request->query('island_id', 0);
        $tribeKey = trim((string) $request->query('tribe_key', ''));

        if (!$islandId || $tribeKey === '') {
            return response()->json(null);
        }

        $page = TribeAboutPage::query()
            ->where('island_id', $islandId)
            ->where('tribe_key', $tribeKey)
            ->first();

        return response()->json($page);
    }

    /**
     * simpan/update header (sekali per pulau+suku)
     * ✅ HEADER TANPA GAMBAR (tidak ada hero_image)
     */
    public function savePage(Request $request)
    {
        $data = $request->validate([
            'island_id'        => ['required', 'exists:islands,id'],
            'tribe_key'        => ['required', 'string', 'max:120'],

            'label_small'      => ['nullable', 'string', 'max:180'],
            'hero_title'       => ['nullable', 'string', 'max:255'],
            'hero_description' => ['nullable', 'string'],

            'more_link'        => ['nullable', 'url', 'max:2048'],
        ]);

        TribeAboutPage::updateOrCreate(
            [
                'island_id' => $data['island_id'],
                'tribe_key' => $data['tribe_key'],
            ],
            $data
        );

        return back()->with('success', 'Header About berhasil disimpan.');
    }

    /**
     * create item about (bisa banyak)
     * ✅ image UPLOAD (bukan string)
     * ✅ points multi-line opsional
     */
    public function storeItem(Request $request)
    {
        $data = $request->validate([
            'island_id'    => ['required', 'exists:islands,id'],
            'tribe_key'    => ['required', 'string', 'max:120'],

            'title'        => ['nullable', 'string', 'max:255'],
            'description'  => ['required', 'string'],

            // points opsional (isi per baris)
            'points'       => ['nullable', 'string'],

            // ✅ file upload
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'more_link'    => ['nullable', 'url', 'max:2048'],
            'sort_order'   => ['nullable', 'integer'],
        ]);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        // ✅ simpan image upload -> path string
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('about-items', 'public');
            $data['image'] = '/storage/' . $path;
        } else {
            unset($data['image']);
        }

        TribeAboutItem::create($data);

        return back()->with('success', 'Item About berhasil ditambahkan.');
    }

    /**
     * update item
     * ✅ bisa replace image upload
     * ✅ bisa remove image
     */
    public function updateItem(Request $request, TribeAboutItem $item)
    {
        $data = $request->validate([
            'title'        => ['nullable', 'string', 'max:255'],
            'description'  => ['required', 'string'],
            'points'       => ['nullable', 'string'],

            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image' => ['nullable', 'in:1'],

            'more_link'    => ['nullable', 'url', 'max:2048'],
            'sort_order'   => ['nullable', 'integer'],
        ]);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        // remove image (kalau dicentang)
        if (($data['remove_image'] ?? null) === '1') {
            if ($item->image) {
                $this->deletePublicStoragePath($item->image);
            }
            $data['image'] = null;
        }
        unset($data['remove_image']);

        // replace image (kalau upload baru)
        if ($request->hasFile('image')) {
            if ($item->image) {
                $this->deletePublicStoragePath($item->image);
            }
            $path = $request->file('image')->store('about-items', 'public');
            $data['image'] = '/storage/' . $path;
        } else {
            // jangan override image jika tidak upload & tidak remove
            if (!array_key_exists('image', $data)) {
                unset($data['image']);
            }
        }

        $item->update($data);

        return back()->with('success', 'Item About berhasil diupdate.');
    }

    public function destroyItem(TribeAboutItem $item)
    {
        if ($item->image) {
            $this->deletePublicStoragePath($item->image);
        }

        $item->delete();

        return back()->with('success', 'Item About berhasil dihapus.');
    }

    /**
     * Hapus file storage public dari value yang disimpan sebagai "/storage/xxx"
     */
    private function deletePublicStoragePath(string $publicUrl): void
    {
        $path = ltrim($publicUrl, '/'); // storage/about-items/xxx.webp
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/')); // about-items/xxx.webp
        }

        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
