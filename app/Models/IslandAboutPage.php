<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IslandAboutPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'island_id',
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

