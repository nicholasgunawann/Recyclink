<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\WasteListing;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\CancelOrderRequest;
use App\Services\OrderService;
use App\Exceptions\RecyclinkException;
use Illuminate\Routing\Controllers\HasMiddleware;

class BuyerOrderController extends Controller implements HasMiddleware
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
            'verified',
            'role:buyer',
        ];
    }

    // ponytail: view buyer orders with eager loading
    public function index()
    {
        $orders = auth()->user()->buyerOrders()->with(['seller', 'payment', 'items.listing'])->latest()->paginate(15);
        return view('buyer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['seller', 'items.listing', 'payment']);
        return view('buyer.orders.show', compact('order'));
    }

    // ponytail: place order on a listing
    public function store(StoreOrderRequest $request, WasteListing $wasteListing)
    {
        try {
            $order = $this->orderService->createOrder(auth()->user(), $wasteListing, $request->validated());
            return redirect()->route('buyer.orders.show', $order)->with('success', 'Order placed successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: cancel order
    public function cancel(CancelOrderRequest $request, Order $order)
    {
        $this->authorize('cancel', $order);
        try {
            $this->orderService->cancelOrder(auth()->user(), $order, $request->input('reason'));
            return redirect()->back()->with('success', 'Order cancelled successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: complete order (confirm delivery/pickup)
    public function complete(Order $order)
    {
        $this->authorize('complete', $order);
        try {
            $this->orderService->completeOrder(auth()->user(), $order);
            return redirect()->back()->with('success', 'Order completed successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
