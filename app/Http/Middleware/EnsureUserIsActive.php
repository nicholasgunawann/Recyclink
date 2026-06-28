<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    // ponytail: check user is active, logout and redirect if not
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->isActive()) {
            if ($user->status === \App\Models\User::STATUS_PENDING) {
                if (!$request->routeIs('verification.pending') && !$request->routeIs('logout')) {
                    return redirect()->route('verification.pending');
                }
                return $next($request);
            }
            if ($user->status === \App\Models\User::STATUS_INACTIVE || $user->status === \App\Models\User::STATUS_SUSPENDED) {
                if (!$request->routeIs('verification.rejected') && !$request->routeIs('logout')) {
                    return redirect()->route('verification.rejected');
                }
                return $next($request);
            }
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda tidak aktif atau ditangguhkan.');
        }

        // If user is active but has no role, force them to choose a role
        if ($user && $user->roles->count() === 0) {
            if (!$request->routeIs('choose.role') && !$request->routeIs('choose.role.store') && !$request->routeIs('logout')) {
                return redirect()->route('choose.role');
            }
            return $next($request);
        }

        return $next($request);
    }
}
