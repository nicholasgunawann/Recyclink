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

        // Dynamic fee rules matching user's DompetX merchant dashboard
        $methods = [
            'qris'             => ['fee' => ceil($baseTotal * 0.007) + 500, 'min' => 1000, 'max' => 8000000],
            'bca'              => ['fee' => 4300, 'min' => 10000, 'max' => 10000000],
            'bri'              => ['fee' => 3000, 'min' => 15000, 'max' => 10000000],
            'bni'              => ['fee' => 3000, 'min' => 15000, 'max' => 1000000],
            'bsi'              => ['fee' => 3900, 'min' => 10000, 'max' => 50000000],
            'dompetx_checkout' => ['fee' => 3000, 'min' => 10000, 'max' => 50000000],
        ];

        if (!isset($methods[$method])) {
            return redirect()->back()->with('error', 'Metode pembayaran tidak valid.');
        }

        $rule = $methods[$method];
        if ($baseTotal < $rule['min']) {
            return redirect()->back()->with('error', 'Total transaksi belum memenuhi minimum (Rp ' . number_format($rule['min'], 0, ',', '.') . ') untuk metode pembayaran ini.');
        }

        if (isset($rule['max']) && $baseTotal > $rule['max']) {
            return redirect()->back()->with('error', 'Total transaksi melebihi batas maksimum (Rp ' . number_format($rule['max'], 0, ',', '.') . ') untuk metode pembayaran ini.');
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
            $proxyUrl = config('services.dompetx.fixie_url') ?: (config('services.dompetx.quotaguardstatic_url') ?: (env('FIXIE_URL') ?: env('QUOTAGUARDSTATIC_URL')));
            $httpOptions = $proxyUrl ? ['proxy' => $proxyUrl] : [];

            $timestamp = (string) time();
            $idempotencyKey = 'checkout-' . $order->order_code . '-' . $timestamp;

            if ($isCheckoutPage) {
                // Panggil endpoint checkout DompetX
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

                $body = json_encode($payload);
                $signature = hash_hmac('sha256', $timestamp . '.' . $body, $apiKey);

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
                    ->post('https://api.dompetx.com/v1/payments/checkout', $payload);

                $responseData = $response->json();

                if ($response->successful() && !empty($responseData['payment_url'])) {
                    Payment::updateOrCreate(['order_id' => $order->id], [
                        'payment_method' => $method,
                        'payment_gateway' => 'dompetx',
                        'payment_reference' => $responseData['id'] ?? $referenceCode,
                        'amount' => $order->total_amount,
                        'payment_status' => Payment::STATUS_PENDING,
                        'payment_number' => 'PAY-' . now()->format('YmdHis') . '-' . rand(1000, 9999),
                        'gateway_transaction_id' => $responseData['id'] ?? null,
                        'gateway_response' => $responseData,
                    ]);

                    return redirect()->away($responseData['payment_url']);
                }

                return redirect()->back()->with('error', 'Gagal membuat sesi pembayaran DompetX: ' . ($responseData['message'] ?? 'Silakan coba lagi.'));
            }

            // Direct payment method (QRIS / BRI / BNI / BCA / BSI)
            $payload = [
                'amount' => (int) $order->total_amount,
                'currency' => 'IDR',
                'reference' => $referenceCode,
                'method' => strtoupper($method),
                'callback_url' => route('webhook.dompetx'),
                'return_url' => route('buyer.orders.show', $order->id),
                'metadata' => [
                    'order_id' => $order->id,
                    'buyer_name' => auth()->user()->name,
                ],
            ];

            $body = json_encode($payload);
            $signature = hash_hmac('sha256', $timestamp . '.' . $body, $apiKey);

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
                ->post('https://api.dompetx.com/v1/payments', $payload);

            $responseData = $response->json();

            if ($response->successful()) {
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
                return redirect()->route('buyer.orders.show', $order->id)->with('success', 'Instruksi pembayaran berhasil dibuat.');
            }

            // Resilient Fallback: Jika direct API gateway merespons error (misal upstream BCA/BSI sedang busy), alihkan ke Checkout Page DompetX
            \Illuminate\Support\Facades\Log::warning("Direct {$method} failed, falling back to DompetX checkout", [
                'status' => $response->status(),
                'response' => $responseData,
            ]);

            $chkPayload = [
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
            $chkBody = json_encode($chkPayload);
            $chkSig = hash_hmac('sha256', $timestamp . '.' . $chkBody, $apiKey);

            $chkResponse = \Illuminate\Support\Facades\Http::withOptions($httpOptions)
                ->timeout(10)
                ->connectTimeout(5)
                ->withHeaders([
                    'X-DOMPAY-API-Key' => $apiKey,
                    'X-DOMPAY-Signature' => $chkSig,
                    'X-DOMPAY-Timestamp' => $timestamp,
                    'Idempotency-Key' => 'chk-' . $idempotencyKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.dompetx.com/v1/payments/checkout', $chkPayload);

            $chkData = $chkResponse->json();

            if ($chkResponse->successful() && !empty($chkData['payment_url'])) {
                Payment::updateOrCreate(['order_id' => $order->id], [
                    'payment_method' => $method,
                    'payment_gateway' => 'dompetx',
                    'payment_reference' => $chkData['id'] ?? $referenceCode,
                    'amount' => $order->total_amount,
                    'payment_status' => Payment::STATUS_PENDING,
                    'payment_number' => 'PAY-' . now()->format('YmdHis') . '-' . rand(1000, 9999),
                    'gateway_transaction_id' => $chkData['id'] ?? null,
                    'gateway_response' => $chkData,
                ]);

                return redirect()->away($chkData['payment_url']);
            }

            return redirect()->back()->with('error', 'Gagal memproses pembayaran via DompetX: ' . ($responseData['message'] ?? 'Silakan coba lagi.'));

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

