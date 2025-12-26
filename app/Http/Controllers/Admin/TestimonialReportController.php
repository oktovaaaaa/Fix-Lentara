<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestimonialReport;
use Illuminate\Http\Request;

class TestimonialReportController extends Controller
{
    public function index()
    {
        $reports = TestimonialReport::with('testimonial')
                    ->latest()->paginate(20);
        return view('admin.testimonial-reports.index', compact('reports'));
    }

    public function destroy(TestimonialReport $testimonialReport)
    {
        $testimonialReport->delete();
        return back();
    }
}
