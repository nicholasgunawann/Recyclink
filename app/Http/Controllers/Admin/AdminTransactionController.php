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

    // ponytail: verify & approve payment manually by admin
    public function verifyPayment(Order $order)
    {
        $payment = $order->payment;
        if (!$payment) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($payment->payment_status === \App\Models\Payment::STATUS_PAID) {
            return redirect()->back()->with('info', 'Transaksi ini sudah lunas.');
        }

        app(\App\Services\PaymentService::class)->markAsPaid(auth()->user(), $payment);

        return redirect()->route('admin.transactions.show', $order->id)->with('success', "Pembayaran transaksi {$order->order_code} berhasil diverifikasi dan ditandai LUNAS.");
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
