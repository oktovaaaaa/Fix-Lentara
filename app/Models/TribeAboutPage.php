<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TribeAboutPage extends Model
{
    protected $fillable = [
        'island_id',
        'tribe_key',
        'label_small',
        'hero_title',
        'hero_description',
        'more_link',
    ];

    public function island()
    {
        return $this->belongsTo(Island::class);
    }
}
