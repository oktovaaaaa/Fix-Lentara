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
        if (!empty($this->image_path) && Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }
        return $this->image_url ?: null;
    }
}
