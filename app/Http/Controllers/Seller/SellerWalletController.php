<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;

class SellerWalletController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'role:seller',
        ];
    }

    // ponytail: view wallet overview
    public function index()
    {
        $wallet = auth()->user()->wallet()->firstOrCreate([]);
        return view('seller.wallet.index', compact('wallet'));
    }

    // ponytail: view wallet transactions
    public function transactions()
    {
        $wallet = auth()->user()->wallet()->firstOrCreate([]);
        $transactions = $wallet->transactions()->paginate(15);
        return view('seller.wallet.transactions', compact('transactions', 'wallet'));
    }
}
