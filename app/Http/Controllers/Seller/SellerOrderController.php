<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Requests\RejectOrderRequest;
use App\Services\OrderService;
use App\Exceptions\RecyclinkException;
use Illuminate\Routing\Controllers\HasMiddleware;

class SellerOrderController extends Controller implements HasMiddleware
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
            'role:seller',
        ];
    }

    // ponytail: view received orders with eager loading
    public function index()
    {
        $orders = auth()->user()->sellerOrders()->with(['buyer', 'payment', 'items.listing'])->latest()->paginate(15);
        return view('seller.orders.index', compact('orders'));
    }

    // ponytail: view single order details
    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['buyer', 'items.listing', 'payment']);
        return view('seller.orders.show', compact('order'));
    }

    // ponytail: accept pending order
    public function accept(Order $order)
    {
        $this->authorize('accept', $order);
        try {
            $this->orderService->acceptOrder(auth()->user(), $order);
            return redirect()->back()->with('success', 'Order accepted successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: reject pending order
    public function reject(RejectOrderRequest $request, Order $order)
    {
        $this->authorize('reject', $order);
        try {
            $this->orderService->rejectOrder(auth()->user(), $order, $request->input('reason'));
            return redirect()->back()->with('success', 'Order rejected successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: mark order as processing
    public function processing(Order $order)
    {
        $this->authorize('processing', $order);
        try {
            $this->orderService->markAsProcessing(auth()->user(), $order);
            return redirect()->back()->with('success', 'Order status updated to processing.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
