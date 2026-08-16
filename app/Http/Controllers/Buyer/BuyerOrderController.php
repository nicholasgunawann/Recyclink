<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\WasteListing;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\CancelOrderRequest;
use App\Services\OrderService;
use App\Services\PaymentService;
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
            'role:buyer',
        ];
    }

    // ponytail: view buyer orders with eager loading
    public function index()
    {
        $orders = auth()->user()->buyerOrders()->with(['seller.sellerProfile', 'payment', 'items.listing.primaryImage'])->latest()->paginate(15);
        return view('buyer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['seller', 'items.listing', 'payment', 'complaints']);

        // ponytail: jika payment masih pending, cek status ke DompetX API langsung
        $this->syncPaymentStatus($order);

        return view('buyer.orders.show', compact('order'));
    }

    /**
     * ponytail: poll DompetX API untuk sinkronisasi status pembayaran
     * Jaga-jaga jika webhook gagal/lambat
     */
    private function syncPaymentStatus(Order $order): void
    {
        $payment = $order->payment;
        if (!$payment || $payment->payment_status !== Payment::STATUS_PENDING) {
            return;
        }
        if ($payment->payment_gateway !== 'dompetx' || !$payment->gateway_transaction_id) {
            return;
        }

        $apiKey = config('services.dompetx.api_key') ?: env('DOMPETX_API_KEY');
        if (!$apiKey) {
            return;
        }

        try {
            $apiUrl = config('services.dompetx.api_url') ?: (env('DOMPETX_API_URL') ?: 'https://api.dompetx.com/v1/payments');
            $apiUrl = rtrim(str_replace('/checkout', '', $apiUrl), '/');
            $checkUrl = $apiUrl . '/' . $payment->gateway_transaction_id;

            $timestamp = (string) time();
            $signature = hash_hmac('sha256', $timestamp . '.', $apiKey);

            $proxyUrl = config('services.dompetx.fixie_url') ?: (config('services.dompetx.quotaguardstatic_url') ?: (env('FIXIE_URL') ?: env('QUOTAGUARDSTATIC_URL')));
            $httpOptions = $proxyUrl ? ['proxy' => $proxyUrl] : [];

            $response = \Illuminate\Support\Facades\Http::withOptions($httpOptions)
                ->timeout(5)
                ->connectTimeout(3)
                ->withHeaders([
                    'X-DOMPAY-API-Key' => $apiKey,
                    'X-DOMPAY-Signature' => $signature,
                    'X-DOMPAY-Timestamp' => $timestamp,
                ])
                ->get($checkUrl);

            if (!$response->successful()) {
                return;
            }

            $data = $response->json();
            $status = strtoupper($data['status'] ?? $data['data']['status'] ?? '');

            if (in_array($status, ['SUCCESS', 'PAID', 'SETTLED', 'SUCCESSFUL', 'COMPLETED', 'BERHASIL'])) {
                $systemUser = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->first() ?: \App\Models\User::first();
                app(PaymentService::class)->markAsPaid($systemUser, $payment);
                $order->refresh();
            } elseif (in_array($status, ['FAILED', 'EXPIRED', 'CANCELED', 'CANCELLED', 'GAGAL'])) {
                app(PaymentService::class)->markAsFailed($payment);
                $order->refresh();
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('DompetX status check failed', ['error' => $e->getMessage()]);
            // gagal cek tidak masalah, jangan block halaman
        }
    }

    // ponytail: place order on a listing
    public function store(StoreOrderRequest $request, WasteListing $wasteListing)
    {
        try {
            $order = $this->orderService->createOrder(auth()->user(), $wasteListing, $request->validated());
            
            if (session()->has('cart')) {
                $cart = session('cart');
                if (isset($cart[$wasteListing->id])) {
                    unset($cart[$wasteListing->id]);
                    session(['cart' => $cart]);
                }
            }

            return redirect()->route('buyer.orders.show', $order)->with('success', 'Pesanan berhasil dibuat.');
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
