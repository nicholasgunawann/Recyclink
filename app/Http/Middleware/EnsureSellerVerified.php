<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerVerified
{
    // ponytail: verify seller is approved, redirect to dashboard if not
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isSeller()) {
            $profile = $user->sellerProfile;
            if (!$profile || !$profile->isVerified()) {
                return redirect()->route('seller.dashboard')->with('error', 'Your shop profile is awaiting administrator verification.');
            }
        }

        return $next($request);
    }
}
