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

        $method = $request->input('payment_method', 'cash_on_delivery');

        // Jika metode adalah COD, kita biarkan logic aslinya berjalan (atau ubah status menjadi processing)
        if ($method === 'cash_on_delivery') {
            try {
                $payment = $this->paymentService->createManualPayment(auth()->user(), $order, $request->validated());
                // Untuk COD, biasanya tidak langsung paid, tapi kita ikuti flow asli yang menganggap paid/selesai divalidasi
                $this->paymentService->markAsPaid(auth()->user(), $payment);
                return redirect()->route('buyer.orders.show', $order)->with('success', 'Pesanan COD berhasil dikonfirmasi. Silakan temui penjual.');
            } catch (RecyclinkException $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        // Jika metode BUKAN COD, cek apakah kita berada di mode sandbox atau live
        $dompetxMode = env('DOMPETX_MODE', 'sandbox');

        if ($dompetxMode === 'live') {
            try {
                $apiKey = env('DOMPETX_API_KEY');
                // Endpoint checkout (hosted payment page) sesuai dokumentasi DompetX
                $apiUrl = 'https://api.dompetx.com/v1/payments/checkout';

                // Payload checkout hanya membutuhkan amount, currency, dan reference
                $payload = [
                    'amount' => (int) $order->total_amount,
                    'currency' => 'IDR',
                    'reference' => $order->order_code,
                ];

                $body = json_encode($payload);
                $timestamp = (string) time();
                $signatureData = $timestamp . '.' . $body;
                $signature = hash_hmac('sha256', $signatureData, $apiKey);

                // Idempotency-Key wajib ada untuk mencegah duplikat transaksi
                $idempotencyKey = 'checkout-' . $order->order_code . '-' . $timestamp;

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'X-DOMPAY-API-Key' => $apiKey,
                    'X-DOMPAY-Signature' => $signature,
                    'X-DOMPAY-Timestamp' => $timestamp,
                    'Idempotency-Key' => $idempotencyKey,
                    'Content-Type' => 'application/json',
                ])->post($apiUrl, $payload);

                $responseData = $response->json();

                // Cari URL redirect dari response
                $redirectLink = $responseData['paymentUrl']
                    ?? $responseData['payment_url']
                    ?? $responseData['checkoutUrl']
                    ?? $responseData['checkout_url']
                    ?? $responseData['data']['paymentUrl']
                    ?? $responseData['data']['checkout_url']
                    ?? null;

                if ($response->successful() && $redirectLink) {
                    return redirect($redirectLink);
                }

                // Log detail lengkap untuk debugging
                \Illuminate\Support\Facades\Log::error('DompetX Checkout Failed', [
                    'http_status' => $response->status(),
                    'response_body' => $responseData,
                    'request_url' => $apiUrl,
                    'request_payload' => $payload,
                    'idempotency_key' => $idempotencyKey,
                ]);

                $errorMsg = $responseData['message'] ?? $responseData['error'] ?? 'Gagal membuat tagihan pembayaran. Mohon coba lagi.';
                return redirect()->back()->with('error', $errorMsg);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('DompetX Checkout Exception', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return redirect()->back()->with('error', 'Sistem pembayaran sedang gangguan: ' . $e->getMessage());
            }
        }

        // Jika mode masih sandbox di env, tetap berikan error agar admin tahu harus mengubah ke live
        return redirect()->back()->with('error', 'Mode pembayaran belum disetting ke live. Silakan hubungi admin.');
    }

    // ponytail: show payment proof details
    public function show(Payment $payment)
    {
        $payment->load('order');
        if ($payment->order->buyer_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('buyer.payments.show', compact('payment'));
    }
}
