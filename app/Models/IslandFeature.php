<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IslandFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'island_id',
        'type',        // 'history', 'destination', 'food', 'culture', dll
        'title',
        'description',
        'image_url',
        'order',
    ];

    public function island()
    {
        return $this->belongsTo(Island::class);
    }

    protected $casts = [
        'order' => 'integer',
    ];
}
