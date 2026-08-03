<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;

class SellerDashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'role:seller',
        ];
    }

    // ponytail: load seller overview statistics and recent transactions
    public function index()
    {
        $user = auth()->user()->load(['wallet', 'sellerProfile']);
        $summary = $user->getCachedSellerSummary();
        $recentOrders = \App\Models\Order::where('seller_id', $user->id)
            ->with(['buyer.buyerProfile', 'items.listing.primaryImage'])
            ->latest()
            ->take(5)
            ->get();

        return view('seller.dashboard', array_merge($summary, compact('user', 'recentOrders')));
    }
}
