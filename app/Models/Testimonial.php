<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi (mass assignment)
     */
    protected $fillable = [
        'name',
        'rating',
        'message',
        'photo',
        'session_id',
    ];

    /**
     * Relasi:
     * 1 Testimoni bisa memiliki banyak report
     */
    public function reports()
    {
        return $this->hasMany(TestimonialReport::class);
    }

    /**
     * Helper:
     * cek apakah testimoni ini milik user (berdasarkan session)
     */
    public function isOwnedBySession(string $sessionId): bool
    {
        return $this->session_id === $sessionId;
    }
}
