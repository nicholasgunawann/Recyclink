<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Services\PaymentService;
use App\Exceptions\RecyclinkException;
use Illuminate\Routing\Controllers\HasMiddleware;

class BuyerPaymentController extends Controller implements HasMiddleware
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
            'role:buyer',
        ];
    }

    // ponytail: show payment page
    public function create(Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('buyer.payments.create', compact('order'));
    }

    public function store(StorePaymentRequest $request, Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $method = strtolower($request->input('payment_method'));
        $baseTotal = $order->subtotal + $order->shipping_cost;

        // Dynamic fee rules for DompetX supported methods
        $methods = [
            'qris'             => ['fee' => ceil($baseTotal * 0.007) + 500, 'min' => 1000],
            'bri'              => ['fee' => 3000, 'min' => 10000],
            'bni'              => ['fee' => 3000, 'min' => 10000],
            'dompetx_checkout' => ['fee' => 3000, 'min' => 10000],
        ];

        if (!isset($methods[$method])) {
            return redirect()->back()->with('error', 'Metode pembayaran tidak valid.');
        }

        $rule = $methods[$method];
        if ($baseTotal < $rule['min']) {
            return redirect()->back()->with('error', 'Total transaksi belum memenuhi minimum untuk metode pembayaran ini.');
        }

        // Update order platform fee & total
        $newPlatformFee = $rule['fee'];
        $newTotalAmount = $order->subtotal + $order->shipping_cost + $newPlatformFee;

        if ($order->total_amount != $newTotalAmount || $order->platform_fee != $newPlatformFee) {
            $order->update([
                'platform_fee' => $newPlatformFee,
                'total_amount' => $newTotalAmount,
            ]);
            $order->refresh();
        }

        // DompetX Payment Gateway
        $apiKey = config('services.dompetx.api_key') ?: env('DOMPETX_API_KEY');
        $referenceCode = $order->order_code . '_attempt_' . time();

        try {
            $isCheckoutPage = ($method === 'dompetx_checkout');
            $apiUrl = $isCheckoutPage
                ? 'https://api.dompetx.com/v1/payments/checkout'
                : 'https://api.dompetx.com/v1/payments';

            $payload = [
                'amount' => (int) $order->total_amount,
                'currency' => 'IDR',
                'reference' => $referenceCode,
                'callback_url' => route('webhook.dompetx'),
                'return_url' => route('buyer.orders.show', $order->id),
                'metadata' => [
                    'order_id' => $order->id,
                    'buyer_name' => auth()->user()->name,
                ],
            ];

            if (!$isCheckoutPage) {
                $payload['method'] = strtoupper($method);
            }

            $body = json_encode($payload);
            $timestamp = (string) time();
            $signature = hash_hmac('sha256', $timestamp . '.' . $body, $apiKey);
            $idempotencyKey = 'checkout-' . $order->order_code . '-' . $timestamp;

            $proxyUrl = config('services.dompetx.fixie_url') ?: (config('services.dompetx.quotaguardstatic_url') ?: (env('FIXIE_URL') ?: env('QUOTAGUARDSTATIC_URL')));
            $httpOptions = $proxyUrl ? ['proxy' => $proxyUrl] : [];

            $response = \Illuminate\Support\Facades\Http::withOptions($httpOptions)
                ->timeout(10)
                ->connectTimeout(5)
                ->withHeaders([
                    'X-DOMPAY-API-Key' => $apiKey,
                    'X-DOMPAY-Signature' => $signature,
                    'X-DOMPAY-Timestamp' => $timestamp,
                    'Idempotency-Key' => $idempotencyKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($apiUrl, $payload);

            $responseData = $response->json();

            if (!$response->successful()) {
                \Illuminate\Support\Facades\Log::error('DompetX Gateway Error', [
                    'method' => $method,
                    'status' => $response->status(),
                    'response' => $responseData,
                ]);

                return redirect()->back()->with('error', 'Gagal memproses pembayaran via DompetX: ' . ($responseData['message'] ?? 'Silakan coba lagi.'));
            }

            $paymentData = [
                'payment_method' => $method,
                'payment_gateway' => 'dompetx',
                'payment_reference' => $responseData['id'] ?? $referenceCode,
                'amount' => $order->total_amount,
                'payment_status' => Payment::STATUS_PENDING,
                'payment_number' => 'PAY-' . now()->format('YmdHis') . '-' . rand(1000, 9999),
                'gateway_transaction_id' => $responseData['id'] ?? null,
                'gateway_response' => $responseData,
                'virtual_account_number' => $responseData['vaData']['va_number'] ?? null,
                'qris_url' => $responseData['qrData']['qrImage'] ?? null,
            ];

            Payment::updateOrCreate(['order_id' => $order->id], $paymentData);

            // Jika memilih checkout page dan payment_url tersedia, redirect langsung ke halaman DompetX
            if ($isCheckoutPage && !empty($responseData['payment_url'])) {
                return redirect()->away($responseData['payment_url']);
            }

            return redirect()->route('buyer.orders.show', $order->id)->with('success', 'Instruksi pembayaran berhasil dibuat.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DompetX Connection Failed', ['msg' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Tidak dapat terhubung ke gateway pembayaran DompetX: ' . $e->getMessage());
        }
    }

    // ponytail: simulate sandbox payment directly from order page for testing
    public function simulatePayment(Order $order)
    {
        if ($order->buyer_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $payment = $order->payment;
        if (!$payment || $payment->payment_status === Payment::STATUS_PAID) {
            return redirect()->route('buyer.orders.show', $order->id)->with('info', 'Pesanan sudah lunas.');
        }

        $systemUser = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->first() ?: auth()->user();
        $this->paymentService->markAsPaid($systemUser, $payment);

        return redirect()->route('buyer.orders.show', $order->id)->with('success', 'Pembayaran berhasil disimulasikan! Status pesanan kini SUDAH DIBAYAR.');
    }

    // ponytail: upload bukti transfer bank manual
    public function uploadProof(\Illuminate\Http\Request $request, Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        if ($order->payment) {
            $order->payment->update(['payment_reference' => $path]);
        }

        return redirect()->route('buyer.orders.show', $order->id)->with('success', 'Bukti transfer berhasil diunggah! Admin akan segera memverifikasi pesanan Anda.');
    }

    // ponytail: show payment proof details
    public function show(Payment $payment)
    {
        $payment->load('order');
        if ($payment->order->buyer_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('buyer.payments.show', compact('payment'));
    }
}

