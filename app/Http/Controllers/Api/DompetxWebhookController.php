<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DompetxWebhookController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function handle(Request $request)
    {
        // 1. Catat payload webhook ke file log untuk keperluan debugging/tracing
        Log::info('DompetX Webhook Received', $request->all());

        // 2. Ambil reference (order_code) dan status dari DompetX (dukung format flat atau nested di dalam 'data')
        $reference = $request->input('reference') ?? $request->input('data.reference') ?? $request->input('merchant_ref');
        $status = $request->input('status') ?? $request->input('data.status') ?? $request->input('transaction_status');

        if (!$reference) {
            // Coba ambil dari transactionId jika tidak ada reference (fallback)
            $reference = $request->input('transactionId') ?? $request->input('data.transactionId');
            if (!$reference) {
                return response()->json(['message' => 'Missing reference'], 400);
            }
        }

        // 3. Cari pesanan berdasarkan order_code (bersihkan dari suffix _attempt_ jika ada)
        $originalOrderCode = explode('_attempt_', $reference)[0];
        $order = Order::where('order_code', $originalOrderCode)->first();

        // Jika tidak ketemu berdasarkan order_code, coba cari berdasarkan payment_reference / gateway_transaction_id
        if (!$order) {
            $payment = \App\Models\Payment::where('payment_reference', $reference)
                ->orWhere('gateway_transaction_id', $reference)
                ->first();
            if ($payment) {
                $order = $payment->order;
            }
        }

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Pastikan order memiliki payment, jika tidak kita buatkan dummy untuk ditandai lunas
        if (!$order->payment) {
            // Karena ini webhook, kita buatkan record payment yang menandakan pembayaran berhasil dari DompetX
            $systemUser = User::whereHas('roles', function($q){ $q->where('name', 'admin'); })->first() ?: User::first();
            
            $payment = \App\Models\Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->input('method', 'dompetx'),
                'payment_gateway' => 'dompetx',
                'payment_reference' => $request->input('transactionId', 'dompetx-'.$reference),
                'amount' => $order->total_amount,
                'payment_status' => \App\Models\Payment::STATUS_PENDING,
                'payment_number' => 'PAY-' . now()->format('YmdHis') . '-' . rand(1000, 9999),
            ]);
            $order->load('payment');
        }

        // 4. Proses status webhook
        try {
            $systemUser = User::whereHas('roles', function($q){ $q->where('name', 'admin'); })->first() ?: User::first();
            
            if (in_array(strtoupper($status), ['SUCCESS', 'PAID', 'SETTLED', 'SUCCESSFUL', 'COMPLETED', 'BERHASIL'])) {
                // Jangan markAsPaid kalau sudah paid
                if ($order->payment->payment_status !== \App\Models\Payment::STATUS_PAID) {
                    $this->paymentService->markAsPaid($systemUser, $order->payment);
                }
            } elseif (in_array(strtoupper($status), ['FAILED', 'EXPIRED', 'CANCELED', 'CANCELLED', 'GAGAL'])) {
                if ($order->payment->payment_status !== \App\Models\Payment::STATUS_FAILED && $order->payment->payment_status !== \App\Models\Payment::STATUS_PAID) {
                    $this->paymentService->markAsFailed($order->payment);
                }
            }
            
            return response()->json(['message' => 'Webhook handled successfully'], 200);
        } catch (\Exception $e) {
            Log::error('DompetX Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error handling webhook'], 500);
        }
    }
}
