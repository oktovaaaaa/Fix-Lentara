<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TribePage extends Model
{
    protected $fillable = [
        'island_id',
        'tribe_key',
        'hero_title',
        'hero_description',
        'hero_image',
    ];

    public function island()
    {
        return $this->belongsTo(Island::class);
    }
}
