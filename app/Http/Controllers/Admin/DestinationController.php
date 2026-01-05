<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Island;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $islands = Island::orderBy('order')->orderBy('name')->get();

        $selectedIslandSlug = (string) $request->query('island', '');
        $selectedIsland = null;

        if ($selectedIslandSlug !== '') {
            $selectedIsland = $islands->firstWhere('slug', $selectedIslandSlug);
        }
        if (!$selectedIsland && $islands->count()) {
            $selectedIsland = $islands->first();
        }

        $tribes = $selectedIsland
            ? (config('tribes.' . $selectedIsland->slug, []) ?: [])
            : [];

        $selectedTribe = trim((string) $request->query('tribe', ''));
        if ($selectedTribe === '' && !empty($tribes)) {
            $selectedTribe = (string) $tribes[0];
        }
        if ($selectedTribe !== '' && !in_array($selectedTribe, $tribes, true)) {
            $selectedTribe = !empty($tribes) ? (string) $tribes[0] : '';
        }

        $rows = collect();
        if ($selectedIsland && $selectedTribe !== '') {
            $rows = Destination::query()
                ->where('island_id', $selectedIsland->id)
                ->where('tribe_key', $selectedTribe)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        return view('admin.destinations.index', [
            'islands' => $islands,
            'selectedIsland' => $selectedIsland,
            'tribes' => $tribes,
            'selectedTribe' => $selectedTribe,
            'rows' => $rows,
        ]);
    }

    public function create(Request $request)
    {
        $islands = Island::orderBy('order')->orderBy('name')->get();

        $selectedIslandSlug = (string) $request->query('island', '');
        $selectedIsland = $islands->firstWhere('slug', $selectedIslandSlug) ?: $islands->first();

        $tribes = $selectedIsland ? (config('tribes.' . $selectedIsland->slug, []) ?: []) : [];
        $selectedTribe = trim((string) $request->query('tribe', ''));

        if ($selectedTribe === '' && !empty($tribes)) {
            $selectedTribe = (string) $tribes[0];
        }
        if ($selectedTribe !== '' && !in_array($selectedTribe, $tribes, true)) {
            $selectedTribe = !empty($tribes) ? (string) $tribes[0] : '';
        }

        return view('admin.destinations.create', [
            'islands' => $islands,
            'selectedIsland' => $selectedIsland,
            'tribes' => $tribes,
            'selectedTribe' => $selectedTribe,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'island_id'   => ['required', 'exists:islands,id'],
            'tribe_key'   => ['required', 'string', 'max:120'],
            'name'        => ['required', 'string', 'max:180'],
            'location'    => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'image_url'   => ['nullable', 'string', 'max:1000'],
            'image_file'  => ['nullable', 'image', 'max:4096'], // max 4MB
            'rating'      => ['nullable', 'numeric', 'min:0', 'max:5'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $dest = new Destination();
        $dest->island_id   = (int) $validated['island_id'];
        $dest->tribe_key   = (string) $validated['tribe_key'];
        $dest->name        = (string) $validated['name'];
        $dest->location    = $validated['location'] ?? null;
        $dest->description = $validated['description'] ?? null;
        $dest->image_url   = $validated['image_url'] ?? null;
        $dest->rating      = isset($validated['rating']) ? (float) $validated['rating'] : 0.0;
        $dest->sort_order  = isset($validated['sort_order']) ? (int) $validated['sort_order'] : 0;
        $dest->is_active   = (bool) ($validated['is_active'] ?? true);

        // Upload file jika ada
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('destinations', 'public');
            $dest->image_path = $path;
        }

        $dest->save();

        // Balik ke index dengan context pulau+tribe
        $island = Island::find($dest->island_id);
        $slug = $island?->slug ?? '';

        return redirect()
            ->route('admin.destinations.index', ['island' => $slug, 'tribe' => $dest->tribe_key])
            ->with('status', 'Destinasi berhasil ditambahkan.');
    }

    public function edit(Request $request, Destination $destination)
    {
        $islands = Island::orderBy('order')->orderBy('name')->get();
        $selectedIsland = $islands->firstWhere('id', $destination->island_id) ?: $islands->first();

        $tribes = $selectedIsland ? (config('tribes.' . $selectedIsland->slug, []) ?: []) : [];
        $selectedTribe = $destination->tribe_key;

        return view('admin.destinations.edit', [
            'destination' => $destination,
            'islands' => $islands,
            'selectedIsland' => $selectedIsland,
            'tribes' => $tribes,
            'selectedTribe' => $selectedTribe,
        ]);
    }

    public function update(Request $request, Destination $destination)
    {
        $validated = $request->validate([
            'island_id'   => ['required', 'exists:islands,id'],
            'tribe_key'   => ['required', 'string', 'max:120'],
            'name'        => ['required', 'string', 'max:180'],
            'location'    => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'image_url'   => ['nullable', 'string', 'max:1000'],
            'image_file'  => ['nullable', 'image', 'max:4096'],
            'remove_upload' => ['nullable', 'boolean'],
            'rating'      => ['nullable', 'numeric', 'min:0', 'max:5'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $destination->island_id   = (int) $validated['island_id'];
        $destination->tribe_key   = (string) $validated['tribe_key'];
        $destination->name        = (string) $validated['name'];
        $destination->location    = $validated['location'] ?? null;
        $destination->description = $validated['description'] ?? null;
        $destination->image_url   = $validated['image_url'] ?? null;
        $destination->rating      = isset($validated['rating']) ? (float) $validated['rating'] : 0.0;
        $destination->sort_order  = isset($validated['sort_order']) ? (int) $validated['sort_order'] : 0;
        $destination->is_active   = (bool) ($validated['is_active'] ?? false);

        // Hapus upload lama jika dicentang
        if (!empty($validated['remove_upload']) && $destination->image_path) {
            Storage::disk('public')->delete($destination->image_path);
            $destination->image_path = null;
        }

        // Upload baru jika ada
        if ($request->hasFile('image_file')) {
            // delete old
            if ($destination->image_path) {
                Storage::disk('public')->delete($destination->image_path);
            }
            $path = $request->file('image_file')->store('destinations', 'public');
            $destination->image_path = $path;
        }

        $destination->save();

        $island = Island::find($destination->island_id);
        $slug = $island?->slug ?? '';

        return redirect()
            ->route('admin.destinations.index', ['island' => $slug, 'tribe' => $destination->tribe_key])
            ->with('status', 'Destinasi berhasil diperbarui.');
    }

    public function destroy(Request $request, Destination $destination)
    {
        // hapus file upload jika ada
        if ($destination->image_path) {
            Storage::disk('public')->delete($destination->image_path);
        }

        $island = Island::find($destination->island_id);
        $slug = $island?->slug ?? '';
        $tribe = $destination->tribe_key;

        $destination->delete();

        return redirect()
            ->route('admin.destinations.index', ['island' => $slug, 'tribe' => $tribe])
            ->with('status', 'Destinasi berhasil dihapus.');
    }
}
