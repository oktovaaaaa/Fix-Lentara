<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function store(Request $request)
    {
        // ===============================
        // HONEYPOT ANTI BOT (DITAMBAHKAN DI SINI)
        // ===============================
        if ($request->filled('website')) {
            abort(403, 'Bot detected');
        }

        // ===============================
        // VALIDASI
        // ===============================
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'rating'  => 'required|integer|min:1|max:5',
            'message' => 'required|string|max:1000',
            'photo'   => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // ===============================
        // UPLOAD FOTO (OPSIONAL)
        // ===============================
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('testimonials', 'public');
        }

        // ===============================
        // SESSION UNTUK OWNERSHIP
        // ===============================
        $data['session_id'] = $request->session()->getId();

        // ===============================
        // SIMPAN
        // ===============================
        Testimonial::create($data);

        return back()->with('success', 'Terima kasih atas testimoni Anda!');
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        // contoh update (kalau kamu pakai)
    }
}
