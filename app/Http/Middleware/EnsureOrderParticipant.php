<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Order;

class EnsureOrderParticipant
{
    // ponytail: restrict access to orders only to related buyer, seller or admin
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $order = $request->route('order');

        if (is_scalar($order)) {
            $order = Order::find($order);
        }

        if ($order && $user) {
            if ($user->isAdmin() || $user->id === $order->buyer_id || $user->id === $order->seller_id) {
                return $next($request);
            }
            abort(403, 'Unauthorized order action.');
        }

        return $next($request);
    }
}
