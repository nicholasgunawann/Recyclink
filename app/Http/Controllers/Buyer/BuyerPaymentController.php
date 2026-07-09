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
                $apiUrl = env('DOMPETX_API_URL', 'https://api.dompetx.com/v1/payment');
                $merchantId = env('DOMPETX_MERCHANT_ID', '1111111111-1111-1111-1111-1111111111');

                // Payload sesuai dengan format dari DompetX Payload Tester
                $payload = [
                    'merchantId' => $merchantId,
                    'amount' => (int) $order->total_amount,
                    'currency' => 'IDR',
                    'settlementSpeed' => 'standard',
                    'reference' => $order->order_code,
                    'metadata' => [
                        'customer_id' => 'CUST-' . auth()->id(),
                        'order_type' => 'retail'
                    ],
                    'method' => strtoupper($method),
                    'chargeFeeToCustomer' => true
                ];

                // Kirim request ke API DompetX
                $response = \Illuminate\Support\Facades\Http::withToken($apiKey)->post($apiUrl, $payload);

                if ($response->successful() && isset($response['payment_url'])) {
                    // Jika DompetX mengembalikan URL pembayaran, lempar user ke sana
                    return redirect($response['payment_url']);
                }
            } catch (\Exception $e) {
                // Jika API gagal, kita bisa memilih untuk membiarkannya jatuh (fallback) ke halaman simulasi
            }
        }

        // Fallback / Sandbox Mode: Lempar ke halaman simulasi DompetX buatan kita
        return redirect()->route('buyer.dompetx.checkout', ['order' => $order->id, 'method' => $method]);
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
