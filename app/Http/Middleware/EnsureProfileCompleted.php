<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    // ponytail: check if buyer/seller profile has required info, redirect if not
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->isAdmin()) {
            return $next($request);
        }

        if (!app(\App\Services\ProfileService::class)->checkProfileCompletion($user)) {
            if ($user->isSeller()) {
                return redirect()->route('seller.profile.edit')->with('error', 'Please complete your store profile first.');
            }
            if ($user->isBuyer()) {
                return redirect()->route('buyer.profile.edit')->with('error', 'Please complete your profile details first.');
            }
        }

        return $next($request);
    }
}
