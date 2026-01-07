<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerIslandProgress extends Model
{
    protected $table = 'player_island_progress';

    protected $fillable = [
        'player_id','island_id','is_completed','completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function island()
    {
        return $this->belongsTo(Island::class);
    }
}
