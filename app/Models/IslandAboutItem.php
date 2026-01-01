<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IslandAboutItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'island_id',
        'title',
        'description',
        'points',
        'image',
        'more_link',
        'sort_order',
    ];

    public function island()
    {
        return $this->belongsTo(Island::class);
    }

    public function pointsArray(): array
    {
        if (!$this->points) return [];
        $lines = preg_split("/\r\n|\n|\r/", (string) $this->points);
        $lines = array_map(fn($s) => trim((string)$s), $lines);
        return array_values(array_filter($lines, fn($s) => $s !== ''));
    }
}
