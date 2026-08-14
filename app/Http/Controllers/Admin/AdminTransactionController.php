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

    // ponytail: delete any order by admin
    public function destroy(Order $order)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
            if (method_exists($order, 'complaints')) {
                $order->complaints()->delete();
            }
            if (method_exists($order, 'reviews')) {
                $order->reviews()->delete();
            }
            if ($order->payment) {
                $order->payment()->delete();
            }
            $order->items()->delete();
            $order->delete();
        });

        return redirect()->route('admin.transactions.index')->with('success', "Transaksi {$order->order_code} berhasil dihapus.");
    }
}
