<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Routing\Controllers\HasMiddleware;

class AdminTransactionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'verified',
            'role:admin',
        ];
    }

    // ponytail: view all orders in system with eager loading
    public function index()
    {
        $orders = Order::with(['buyer', 'seller', 'payment', 'items.listing'])->latest()->paginate(15);
        return view('admin.transactions.index', compact('orders'));
    }

    // ponytail: view order details
    public function show(Order $order)
    {
        $order->load(['buyer', 'seller', 'items.listing', 'payment']);
        return view('admin.transactions.show', compact('order'));
    }
}
