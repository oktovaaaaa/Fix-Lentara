<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IslandDemographic extends Model
{
    protected $fillable = [
        'island_id',
        'type',
        'label',
        'percentage',
        'order',
    ];

    public function island()
    {
        return $this->belongsTo(Island::class);
    }
}
