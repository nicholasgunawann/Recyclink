<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('orders:auto-complete')]
#[Description('Automatically complete orders that have been paid or processing for more than 2 days.')]
class AutoCompleteOrders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(\App\Services\OrderService $orderService)
    {
        $twoDaysAgo = now()->subDays(2);
        
        $orders = \App\Models\Order::with('buyer')
            ->whereIn('order_status', [\App\Models\Order::STATUS_PAID, \App\Models\Order::STATUS_PROCESSING])
            ->where('updated_at', '<=', $twoDaysAgo)
            ->get();

        $count = 0;
        /** @var \App\Models\Order $order */
        foreach ($orders as $order) {
            try {
                $orderService->completeOrder($order->buyer, $order);
                $this->info("Order {$order->order_code} auto-completed.");
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to auto-complete order {$order->order_code}: " . $e->getMessage());
            }
        }

        $this->info("Successfully auto-completed {$count} orders.");
    }
}
