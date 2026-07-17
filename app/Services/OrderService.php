<?php

namespace App\Services;

use App\Models\Order;
use App\Models\WasteListing;
use App\Models\User;
use App\Exceptions\UnauthorizedBusinessActionException;
use App\Exceptions\ListingNotApprovedException;
use App\Exceptions\ListingNotAvailableException;
use App\Exceptions\InsufficientListingQuantityException;
use App\Exceptions\InvalidOrderStatusException;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected NotificationService $notificationService;
    protected WalletService $walletService;
    protected ActivityLogService $activityLogService;

    public function __construct(
        NotificationService $notificationService,
        WalletService $walletService,
        ActivityLogService $activityLogService
    ) {
        $this->notificationService = $notificationService;
        $this->walletService = $walletService;
        $this->activityLogService = $activityLogService;
    }

    // ponytail: create a new order (buyer only, locks listing for updates)
    public function createOrder(User $buyer, WasteListing $listing, array $data): Order
    {
        if (!$buyer->isBuyer()) {
            throw new UnauthorizedBusinessActionException("Hanya buyer yang dapat membuat pesanan.");
        }

        if ($listing->seller_id === $buyer->id) {
            throw new UnauthorizedBusinessActionException("You cannot purchase your own listing.");
        }

        $quantity = (float) $data['quantity'];

        return DB::transaction(function () use ($buyer, $listing, $quantity, $data) {
            // Lock listing for update inside transaction
            $lockedListing = WasteListing::where('id', $listing->id)->lockForUpdate()->first();

            if (!$lockedListing) {
                throw new ListingNotAvailableException("Listing tidak ditemukan.");
            }

            if ($lockedListing->verification_status !== WasteListing::VERIFICATION_APPROVED) {
                throw new ListingNotApprovedException();
            }

            if ($lockedListing->availability_status !== WasteListing::AVAILABILITY_AVAILABLE) {
                throw new ListingNotAvailableException();
            }

            if ($quantity > $lockedListing->quantity) {
                throw new InsufficientListingQuantityException();
            }

            $subtotal = $lockedListing->price_per_unit * $quantity;
            $platformFee = $subtotal * 0.05;
            $shippingCost = 0.00;
            $totalAmount = $subtotal + $platformFee + $shippingCost;

            $order = Order::create([
                'order_code' => Order::generateOrderCode(),
                'buyer_id' => $buyer->id,
                'seller_id' => $lockedListing->seller_id,
                'order_status' => Order::STATUS_WAITING_PAYMENT,
                'subtotal' => $subtotal,
                'platform_fee' => $platformFee,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
                'pickup_method' => $data['pickup_method'] ?? null,
                'pickup_date' => $data['pickup_date'] ?? null,
                'pickup_time' => $data['pickup_time'] ?? null,
                'pickup_address' => $data['pickup_address'] ?? null,
                'buyer_note' => $data['buyer_note'] ?? null,
            ]);

            // Save snapshots to order items
            $order->items()->create([
                'listing_id' => $lockedListing->id,
                'waste_name_snapshot' => $lockedListing->title,
                'quantity' => $quantity,
                'unit' => $lockedListing->unit,
                'price_per_unit_snapshot' => $lockedListing->price_per_unit,
                'subtotal' => $subtotal,
            ]);

            $this->activityLogService->log('order.create', 'orders', $order->id, "Order {$order->order_code} created.");
            $this->notificationService->notifyOrderCreated($order);

            return $order;
        });
    }

    // ponytail: accept pending order (status: waiting_payment)
    public function acceptOrder(User $seller, Order $order): Order
    {
        if ($order->seller_id !== $seller->id) {
            throw new UnauthorizedBusinessActionException("You are not the seller of this order.");
        }

        if ($order->order_status !== Order::STATUS_PENDING) {
            throw new InvalidOrderStatusException("Only pending orders can be accepted.");
        }

        return DB::transaction(function () use ($seller, $order) {
            $order->update(['order_status' => Order::STATUS_WAITING_PAYMENT]);

            $this->activityLogService->log('order.accept', 'orders', $order->id, "Order accepted by seller.");
            $this->notificationService->notifyOrderStatusChanged($order);

            return $order;
        });
    }

    public function rejectOrder(User $seller, Order $order, ?string $reason = null): Order
    {
        if ($order->seller_id !== $seller->id) {
            throw new UnauthorizedBusinessActionException("You are not the seller of this order.");
        }

        if ($order->order_status !== Order::STATUS_PENDING) {
            throw new InvalidOrderStatusException("Only pending orders can be rejected.");
        }

        return DB::transaction(function () use ($seller, $order, $reason) {
            $order->update([
                'order_status' => Order::STATUS_REJECTED,
                'cancellation_reason' => $reason ?? 'Rejected by seller.',
                'cancelled_at' => now(),
            ]);

            $this->activityLogService->log('order.reject', 'orders', $order->id, "Order rejected by seller: {$reason}");
            $this->notificationService->notifyOrderStatusChanged($order);

            return $order;
        });
    }

    public function cancelOrder(User $buyer, Order $order, ?string $reason = null): Order
    {
        if ($order->buyer_id !== $buyer->id) {
            throw new UnauthorizedBusinessActionException("You are not the buyer of this order.");
        }

        if (!$order->isCancellable()) {
            throw new InvalidOrderStatusException("Order is not in a cancellable status.");
        }

        return DB::transaction(function () use ($buyer, $order, $reason) {
            $order->update([
                'order_status' => Order::STATUS_CANCELLED,
                'cancellation_reason' => $reason ?? 'Cancelled by buyer.',
                'cancelled_at' => now(),
            ]);

            $this->activityLogService->log('order.cancel', 'orders', $order->id, "Order cancelled by buyer: {$reason}");
            $this->notificationService->notifyOrderStatusChanged($order);

            return $order;
        });
    }

    // ponytail: mark order processing
    public function markAsProcessing(User $seller, Order $order): Order
    {
        if ($order->seller_id !== $seller->id) {
            throw new UnauthorizedBusinessActionException("You are not the seller of this order.");
        }

        if ($order->order_status !== Order::STATUS_PAID) {
            throw new InvalidOrderStatusException("Only paid orders can be marked as processing.");
        }

        return DB::transaction(function () use ($seller, $order) {
            $order->update(['order_status' => Order::STATUS_PROCESSING]);

            $this->activityLogService->log('order.processing', 'orders', $order->id, "Order set to processing.");
            $this->notificationService->notifyOrderStatusChanged($order);

            return $order;
        });
    }

    // ponytail: complete order (adds balance to seller, decrements listing quantity with locking)
    public function completeOrder(User $buyer, Order $order): Order
    {
        if ($order->buyer_id !== $buyer->id) {
            throw new UnauthorizedBusinessActionException("You are not the buyer of this order.");
        }

        if (!in_array($order->order_status, [Order::STATUS_PAID, Order::STATUS_PROCESSING])) {
            throw new InvalidOrderStatusException("Only paid or processing orders can be completed.");
        }

        return DB::transaction(function () use ($buyer, $order) {
            $order->update(['order_status' => Order::STATUS_COMPLETED]);

            // Decrement listing quantity with lockForUpdate
            foreach ($order->items as $item) {
                $listing = WasteListing::where('id', $item->listing_id)->lockForUpdate()->first();
                if ($listing) {
                    if ($listing->quantity < $item->quantity) {
                        throw new InsufficientListingQuantityException("Stok limbah '{$listing->title}' tidak mencukupi.");
                    }
                    $listing->decrement('quantity', $item->quantity);
                    if ($listing->quantity <= 0) {
                        $listing->update(['availability_status' => WasteListing::AVAILABILITY_SOLD_OUT]);
                    }
                }
            }

            // Credit seller wallet using WalletService (Only when order is COMPLETED and NOT COD)
            $paymentMethod = $order->payment ? $order->payment->payment_method : null;
            if ($paymentMethod !== 'cash_on_delivery') {
                $this->walletService->addEarnings(
                    $order->seller,
                    (float) ($order->subtotal + $order->shipping_cost),
                    $order,
                    "Earning from order {$order->order_code}."
                );
            }

            $this->activityLogService->log('order.complete', 'orders', $order->id, "Order completed.");
            $this->notificationService->notifyOrderStatusChanged($order);

            return $order;
        });
    }
}
