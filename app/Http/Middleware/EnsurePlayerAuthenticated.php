<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePlayerAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('player')->check()) {
            return redirect()->route('player.login')->with('error', 'Silakan login terlebih dahulu.');
        }
        return $next($request);
    }
}
