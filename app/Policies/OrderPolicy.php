<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    // ponytail: rules for viewing order details
    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isBuyer() && $order->buyer_id === $user->id) {
            return true;
        }

        if ($user->isSeller() && $order->seller_id === $user->id) {
            return true;
        }

        return false;
    }

    // ponytail: buyer cancels their own order if pending or waiting_payment
    public function cancel(User $user, Order $order): bool
    {
        return $user->isBuyer() 
            && $order->buyer_id === $user->id 
            && in_array($order->order_status, [Order::STATUS_PENDING, Order::STATUS_WAITING_PAYMENT]);
    }

    // ponytail: seller accepts their own order if pending
    public function accept(User $user, Order $order): bool
    {
        return $user->isSeller() 
            && $order->seller_id === $user->id 
            && $order->order_status === Order::STATUS_PENDING;
    }

    // ponytail: seller rejects their own order if pending
    public function reject(User $user, Order $order): bool
    {
        return $user->isSeller() 
            && $order->seller_id === $user->id 
            && $order->order_status === Order::STATUS_PENDING;
    }

    // ponytail: seller marks order as processing if paid
    public function processing(User $user, Order $order): bool
    {
        return $user->isSeller() 
            && $order->seller_id === $user->id 
            && $order->order_status === Order::STATUS_PAID;
    }

    // ponytail: buyer confirms completion if paid or processing
    public function complete(User $user, Order $order): bool
    {
        return $user->isBuyer() 
            && $order->buyer_id === $user->id 
            && in_array($order->order_status, [Order::STATUS_PAID, Order::STATUS_PROCESSING]);
    }
}
