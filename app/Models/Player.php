<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Player extends Authenticatable
{
    use Notifiable;

    protected $table = 'players';

    protected $fillable = [
        'username','pin_hash','nickname','avatar_key',
        'xp_total','coins','hearts','hearts_max',
        'hearts_updated_at',
    ];

    protected $hidden = [
        'pin_hash','remember_token',
    ];

    protected $casts = [
        'hearts_updated_at' => 'datetime',
    ];

    public function levelProgress()
    {
        return $this->hasMany(PlayerLevelProgress::class);
    }

    public function islandProgress()
    {
        return $this->hasMany(PlayerIslandProgress::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->nickname ?: $this->username;
    }
}
