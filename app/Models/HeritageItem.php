<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeritageItem extends Model
{
    protected $fillable = [
        'island_id',
        'tribe_key',
        'category',
        'title',
        'description',
        'location',     // ✅ baru
        'detail_url',   // ✅ baru
        'image_path',
        'sort_order',
    ];

    public const CATEGORIES = [
        'pakaian' => 'Pakaian Adat',
        'rumah_tradisi' => 'Rumah Adat & Tradisi',
        'senjata_alatmusik' => 'Senjata Tradisional & Alat Musik',
    ];

    public function island()
    {
        return $this->belongsTo(Island::class);
    }
}
