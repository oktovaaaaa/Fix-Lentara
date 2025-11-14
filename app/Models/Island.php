<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Island extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'place_label',       // "Masyarakat Bali", "Danau Toba", dll
        'title',             // baris besar 1 (misal: PULAU)
        'subtitle',          // baris besar 2 (misal: SUMATRA)
        'short_description', // bisa dipakai untuk halaman detail
        'image_url',
        'order',
        'is_active',
    ];

    // Route model binding pakai slug: /islands/sumatra
    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function features()
    {
        return $this->hasMany(\App\Models\IslandFeature::class);
    }
    public function demographics()
    {
        return $this->hasMany(IslandDemographic::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];
}
