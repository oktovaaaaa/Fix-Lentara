<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestimonialReport extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi
     */
    protected $fillable = [
        'testimonial_id',
        'reason',
        'note',
    ];

    /**
     * Relasi:
     * Report ini milik satu Testimoni
     */
    public function testimonial()
    {
        return $this->belongsTo(Testimonial::class);
    }
}
