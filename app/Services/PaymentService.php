<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ActivityLog;
use App\Exceptions\InvalidOrderStatusException;
use App\Exceptions\PaymentAlreadyProcessedException;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // ponytail: create manual payment for order (only if status is waiting_payment)
    public function createManualPayment(User $buyer, Order $order, array $data): Payment
    {
        if ($order->buyer_id !== $buyer->id) {
            throw new \App\Exceptions\UnauthorizedBusinessActionException();
        }

        if ($order->order_status !== Order::STATUS_WAITING_PAYMENT) {
            throw new InvalidOrderStatusException("Payment can only be created for orders waiting payment.");
        }

        return DB::transaction(function () use ($order, $data, $buyer) {
            $payment = $order->payment;

            if ($payment && in_array($payment->payment_status, [Payment::STATUS_PENDING, Payment::STATUS_PAID])) {
                throw new PaymentAlreadyProcessedException();
            }

            $proofUrl = 'manual-payment';
            if (isset($data['payment_proof']) && $data['payment_proof'] instanceof \Illuminate\Http\UploadedFile) {
                $proofUrl = $data['payment_proof']->store('payment_proofs', 'public');
            }

            $payment = Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_method' => $data['payment_method'] ?? 'manual',
                    'payment_gateway' => 'manual',
                    'payment_reference' => $proofUrl,
                    'amount' => $order->total_amount,
                    'payment_status' => Payment::STATUS_PENDING,
                    'payment_number' => 'PAY-' . now()->format('YmdHis') . '-' . rand(1000, 9999),
                ]
            );

            ActivityLog::record('payment.submit', 'payments', $payment->id, "Payment confirmation submitted.");

            return $payment;
        });
    }

    // ponytail: mark payment as paid (admin or system, updates order status to paid)
    public function markAsPaid(User $adminOrSystem, Payment $payment): Payment
    {
        if ($payment->payment_status === Payment::STATUS_PAID) {
            throw new PaymentAlreadyProcessedException();
        }

        return DB::transaction(function () use ($adminOrSystem, $payment) {
            $payment->update([
                'payment_status' => Payment::STATUS_PAID,
                'paid_at' => now(),
            ]);

            $order = $payment->order;
            $order->update(['order_status' => Order::STATUS_PAID]);

            ActivityLog::record(
                'payment.approve',
                'payments',
                $payment->id,
                "Payment approved by " . ($adminOrSystem->isAdmin() ? 'Admin' : 'System')
            );

            $this->notificationService->notifyPaymentSuccess($order);

            return $payment;
        });
    }

    // ponytail: mark payment as failed (does not cancel order directly)
    public function markAsFailed(Payment $payment): Payment
    {
        if (in_array($payment->payment_status, [Payment::STATUS_PAID, Payment::STATUS_FAILED])) {
            throw new PaymentAlreadyProcessedException("Cannot mark a processed payment as failed.");
        }

        return DB::transaction(function () use ($payment) {
            $payment->update(['payment_status' => Payment::STATUS_FAILED]);

            ActivityLog::record(
                'payment.fail',
                'payments',
                $payment->id,
                "Payment marked as failed."
            );

            return $payment;
        });
    }

    // ponytail: handle external payment gateway callbacks (mock implementation)
    public function handlePaymentCallback(array $payload): void
    {
        $paymentReference = $payload['payment_reference'] ?? null;
        if (!$paymentReference) {
            throw new InvalidOrderStatusException("Missing payment reference.");
        }

        $payment = Payment::where('payment_number', $paymentReference)
            ->orWhere('payment_reference', $paymentReference)
            ->first();

        if (!$payment) {
            throw new InvalidOrderStatusException("Payment not found.");
        }

        $status = $payload['status'] ?? 'failed';

        if ($status === 'success') {
            $systemUser = User::whereHas('roles', function($q){ $q->where('name', 'admin'); })->first() ?: new User();
            $this->markAsPaid($systemUser, $payment);
        } else {
            $this->markAsFailed($payment);
        }
    }
}
