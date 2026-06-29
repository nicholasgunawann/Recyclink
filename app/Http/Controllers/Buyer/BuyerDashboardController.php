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

    // ponytail: load buyer overview statistics with caching
    public function index()
    {
        $user = auth()->user()->load(['buyerProfile']);
        $summary = $user->getCachedBuyerSummary();
        $ordersCount = $summary['orders_count'];
        $favoritesCount = $summary['favorites_count'];

        return view('buyer.dashboard', compact('user', 'ordersCount', 'favoritesCount'));
    }
}
