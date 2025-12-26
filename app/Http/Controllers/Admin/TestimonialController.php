<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $query = Testimonial::withCount('reports');

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('reported')) {
            $query->has('reports');
        }

        $testimonials = $query->latest()->paginate(15);
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return back();
    }
}
