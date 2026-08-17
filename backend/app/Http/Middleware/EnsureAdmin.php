<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized. Admin role required.'], 403);
            }
            return redirect()->route('user.dashboard')->with('error', 'Akses ditolak: Hanya Admin Aplikator yang dapat mengakses halaman tersebut.');
        }

        return $next($request);
    }
}
