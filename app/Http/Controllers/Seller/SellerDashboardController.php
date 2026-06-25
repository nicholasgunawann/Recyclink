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
            'verified',
            'role:seller',
        ];
    }

    // ponytail: load seller overview statistics with caching
    public function index()
    {
        $user = auth()->user()->load(['wallet', 'sellerProfile']);
        $summary = $user->getCachedSellerSummary();
        $listingsCount = $summary['listings_count'];
        $ordersCount = $summary['orders_count'];

        return view('seller.dashboard', compact('user', 'listingsCount', 'ordersCount'));
    }
}
