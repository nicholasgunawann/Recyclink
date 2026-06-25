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
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account is inactive or suspended.');
        }

        return $next($request);
    }
}
