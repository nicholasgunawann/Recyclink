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

        $basePlatformFee = $order->subtotal * 0.05;
        $baseTotal = $order->subtotal + $order->shipping_cost + $basePlatformFee;

        // Dynamic fee rules
        $methods = [
            'bca' => ['fee' => 4300, 'min' => 10000],
            'bni' => ['fee' => 3000, 'min' => 15000],
            'bri' => ['fee' => 3000, 'min' => 15000],
            'bsi' => ['fee' => 3900, 'min' => 10000],
            'qris' => ['fee' => ceil($baseTotal * 0.007) + 500, 'min' => 1000],
            'cash_on_delivery' => ['fee' => 0, 'min' => 0],
        ];

        if (isset($methods[$method])) {
            $rule = $methods[$method];
            if ($baseTotal < $rule['min']) {
                return redirect()->back()->with('error', 'Total transaksi belum memenuhi minimum untuk metode pembayaran ini.');
            }

            // Update order with dynamic fee if not already applied
            // To prevent double adding if user submits multiple times, we check if payment is already pending/created.
            // But since payment is created later, we can just apply it.
            // Wait, what if they fail to checkout and come back? We need to ensure we don't add fee twice.
            // A safer way is to recalculate from subtotal + shipping_cost + base 5% platform fee.
            $basePlatformFee = $order->subtotal * 0.05;
            $newPlatformFee = $basePlatformFee + $rule['fee'];
            $newTotalAmount = $order->subtotal + $order->shipping_cost + $newPlatformFee;

            if ($order->total_amount !== $newTotalAmount) {
                $order->update([
                    'platform_fee' => $newPlatformFee,
                    'total_amount' => $newTotalAmount,
                ]);
                $order->refresh();
            }
        }

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
                if (empty($apiKey)) {
                    return redirect()->back()->with('error', 'Konfigurasi API Key DompetX belum diatur. Hubungi admin.');
                }

                // Gunakan Direct API (bukan checkout) agar user tetap di website kita (White-label)
                $apiUrl = env('DOMPETX_API_URL', 'https://api.dompetx.com/v1/payments');
                // PASTIKAN menghapus suffix /checkout jika ada di Environment Variables Railway
                $apiUrl = str_replace('/checkout', '', $apiUrl);

                // Tambahkan suffix attempt untuk menghindari 409 duplicate transaction reference dari DompetX jika user mencoba bayar ulang
                $referenceCode = $order->order_code . '_attempt_' . time();

                // Payload checkout
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
                $timestamp = (string) time();
                $signatureData = $timestamp . '.' . $body;
                $signature = hash_hmac('sha256', $signatureData, $apiKey);

                // Idempotency-Key wajib ada untuk mencegah duplikat transaksi
                $idempotencyKey = 'checkout-' . $order->order_code . '-' . $timestamp;

                $proxyUrl = env('FIXIE_URL') ?: env('QUOTAGUARDSTATIC_URL');
                $httpOptions = [];
                if ($proxyUrl) {
                    $httpOptions['proxy'] = $proxyUrl;
                }

                $response = \Illuminate\Support\Facades\Http::withOptions($httpOptions)
                    ->timeout(15)
                    ->connectTimeout(10)
                    ->withHeaders([
                        'X-DOMPAY-API-Key' => $apiKey,
                        'X-DOMPAY-Signature' => $signature,
                        'X-DOMPAY-Timestamp' => $timestamp,
                        'Idempotency-Key' => $idempotencyKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->post($apiUrl, $payload);

                $responseData = $response->json();

                if ($response->successful()) {
                    // Simpan data VA / QRIS ke tabel Payments
                    $paymentData = [
                        'payment_method' => $method,
                        'payment_gateway' => 'dompetx',
                        'payment_reference' => $responseData['id'] ?? $referenceCode,
                        'amount' => $order->total_amount,
                        'payment_status' => Payment::STATUS_PENDING,
                        'payment_number' => 'PAY-' . now()->format('YmdHis') . '-' . rand(1000, 9999),
                        'gateway_transaction_id' => $responseData['id'] ?? null,
                        'gateway_response' => json_encode($responseData),
                    ];

                    if (isset($responseData['vaData']['va_number'])) {
                        $paymentData['virtual_account_number'] = $responseData['vaData']['va_number'];
                        $paymentData['qris_url'] = null; // reset if changing method
                    } elseif (isset($responseData['qrData']['qrImage'])) {
                        $paymentData['qris_url'] = $responseData['qrData']['qrImage'];
                        $paymentData['virtual_account_number'] = null;
                    }

                    Payment::updateOrCreate(
                        ['order_id' => $order->id],
                        $paymentData
                    );

                    // Redirect ke halaman detail pesanan, user akan melihat VA/QRIS di sana!
                    return redirect()->route('buyer.orders.show', $order->id)->with('success', 'Instruksi pembayaran berhasil dibuat!');
                }

                \Illuminate\Support\Facades\Log::error('DompetX Checkout Failed', [
                    'http_status' => $response->status(),
                    'response_body' => $responseData,
                    'request_url' => $apiUrl,
                    'request_payload' => $payload,
                    'idempotency_key' => $idempotencyKey,
                ]);

                // Tampilkan error detail ke user agar bisa di-diagnosa
                $apiError = $responseData['message'] ?? $responseData['error'] ?? json_encode($responseData);
                $errorMsg = "Pembayaran gagal (HTTP {$response->status()}): {$apiError}";
                
                return redirect()->back()->with('error', $errorMsg);

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                \Illuminate\Support\Facades\Log::error('DompetX Connection Error', [
                    'message' => $e->getMessage(),
                ]);
                return redirect()->back()->with('error', 'Tidak dapat terhubung ke server pembayaran. Coba lagi nanti.');

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
