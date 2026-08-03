<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;

class BuyerDashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'role:buyer',
        ];
    }

    // ponytail: load buyer overview statistics and recent transactions
    public function index()
    {
        $user = auth()->user()->load(['buyerProfile']);
        $summary = $user->getCachedBuyerSummary();
        $recentOrders = \App\Models\Order::where('buyer_id', $user->id)
            ->with(['seller.sellerProfile', 'items'])
            ->latest()
            ->take(5)
            ->get();

        return view('buyer.dashboard', array_merge($summary, compact('user', 'recentOrders')));
    }
}
