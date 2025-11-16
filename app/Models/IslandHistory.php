<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IslandHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'island_id',
        'tribe',
        'year_label',
        'title',
        'content',
        'more_link',
        'order',
    ];

    public function island()
    {
        return $this->belongsTo(Island::class);
    }
}
    