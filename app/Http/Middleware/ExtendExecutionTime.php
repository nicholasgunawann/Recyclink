<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExtendExecutionTime
{
    // ponytail: only extend PHP execution time for HTTP requests, not CLI/artisan processes
    public function handle(Request $request, Closure $next): Response
    {
        set_time_limit(300); // 5 menit, cukup untuk Railway remote DB latency
        return $next($request);
    }
}
