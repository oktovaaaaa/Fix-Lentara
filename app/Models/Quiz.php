<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'scope',
        'title',
        'island_id',
        'tribe',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===== RELATIONS =====

    public function island(): BelongsTo
    {
        return $this->belongsTo(Island::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    // ===== SCOPES =====

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeGlobal(Builder $q): Builder
    {
        return $q->where('scope', 'global')->whereNull('island_id')->whereNull('tribe');
    }

    public function scopeForTribe(Builder $q, int $islandId, string $tribe): Builder
    {
        return $q->where('scope', 'tribe')
            ->where('island_id', $islandId)
            ->where('tribe', $tribe);
    }
}
