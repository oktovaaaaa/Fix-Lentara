<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * RATE LIMITER KHUSUS TESTIMONI & REPORT
         * - 5 request / menit
         * - per IP + per SESSION
         */
        RateLimiter::for('testimonials', function (Request $request) {
            return Limit::perMinute(5)->by(
                ($request->ip() ?? 'unknown') . '|' . $request->session()->getId()
            );
        });
    }
}
