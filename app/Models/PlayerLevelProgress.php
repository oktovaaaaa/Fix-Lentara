<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerLevelProgress extends Model
{
    protected $table = 'player_level_progress';

    protected $fillable = [
        'player_id','game_level_id','best_correct','is_completed','completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function level()
    {
        return $this->belongsTo(GameLevel::class, 'game_level_id');
    }
}
