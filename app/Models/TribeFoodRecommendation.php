<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TribeFoodRecommendation extends Model
{
    protected $fillable = [
        'tribe_key',
        'week_key',
        'region_slug',
        'payload',
        'generated_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'generated_at' => 'datetime',
    ];
}
