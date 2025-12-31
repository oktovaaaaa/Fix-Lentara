<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TribeAboutItem extends Model
{
    protected $fillable = [
        'island_id',
        'tribe_key',
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

    /**
     * Helper: points => array (split per baris)
     */
    public function pointsArray(): array
    {
        $raw = (string) ($this->points ?? '');
        $raw = str_replace("\r\n", "\n", $raw);

        $arr = array_values(array_filter(array_map(function ($line) {
            $line = trim((string) $line);
            return $line !== '' ? $line : null;
        }, explode("\n", $raw))));

        return $arr;
    }
}
