<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameQuestion extends Model
{
    protected $fillable = [
        'game_level_id',
        'type',
        'question_text',
        'image_path',
        'option_a','option_b','option_c','option_d',
        'correct_option',
        'correct_text',
        'order',
        'is_active',
    ];

    public function level()
    {
        return $this->belongsTo(GameLevel::class, 'game_level_id');
    }

    /**
     * Max length input untuk tipe fill
     * - mengikuti panjang correct_text (tanpa spasi)
     * - minimal 1
     */
    public function fillMaxLength(): int
    {
        $truth = (string) ($this->correct_text ?? '');
        $truth = preg_replace('/\s+/', '', $truth);
        $n = strlen((string)$truth);
        return max(1, $n);
    }
}
