<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Destination extends Model
{
    protected $fillable = [
        'island_id',
        'tribe_key',
        'name',
        'location',
        'description',
        'image_url',
        'image_path',
        'rating',
        'sort_order',
        'is_active',

        // ===== 360 / Google Maps Embed =====
        'pano_embed_url', // contoh: https://www.google.com/maps/embed?pb=...
        'pano_maps_url',  // contoh: https://maps.app.goo.gl/xxxx atau link maps panjang
        'pano_label',     // contoh: "360° Bukit Holbung"
    ];

    protected $casts = [
        'rating' => 'float',
        'is_active' => 'boolean',
    ];

    public function island(): BelongsTo
    {
        return $this->belongsTo(Island::class);
    }

    /**
     * URL gambar yang dipakai untuk display:
     * - prioritas upload (image_path)
     * - fallback ke link (image_url)
     */
    public function getImageDisplayUrlAttribute(): ?string
    {
        $path = trim((string) ($this->image_path ?? ''));

        if ($path !== '' && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        $url = trim((string) ($this->image_url ?? ''));
        return $url !== '' ? $url : null;
    }

    /**
     * Safety helper: embed URL yang valid untuk iframe.
     */
    public function getPanoEmbedDisplayUrlAttribute(): ?string
    {
        $url = trim((string) ($this->pano_embed_url ?? ''));
        return $url !== '' ? $url : null;
    }

    /**
     * Safety helper: link "View on Google Maps" (opsional).
     */
    public function getPanoMapsDisplayUrlAttribute(): ?string
    {
        $url = trim((string) ($this->pano_maps_url ?? ''));
        return $url !== '' ? $url : null;
    }

    /**
     * Helper boolean: dipakai view untuk menentukan
     * "pakai layar penuh 360" atau "pakai popup biasa".
     */
    public function getHasPanoAttribute(): bool
    {
        return $this->getPanoEmbedDisplayUrlAttribute() !== null;
    }

    /**
     * Label 360 yang aman untuk ditampilkan.
     */
    public function getPanoLabelDisplayAttribute(): ?string
    {
        $label = trim((string) ($this->pano_label ?? ''));
        return $label !== '' ? $label : null;
    }
}
