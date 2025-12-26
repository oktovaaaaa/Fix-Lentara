<?php

namespace App\Http\Controllers;

use App\Models\Testimonial; // ✅ WAJIB
use App\Models\TestimonialReport; // kalau kamu pakai model report
use Illuminate\Http\Request;
class TestimonialReportController extends Controller
{
    public function store(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'reason'=>'required|string',
            'note'=>'nullable|string|max:500'
        ]);

        $testimonial->reports()->create($data);

        return back()->with('success','Laporan dikirim');
    }
}
