<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\SellerWallet;

class WalletService
{
    // ponytail: get or initialize seller wallet
    public function getWallet(User $seller): SellerWallet
    {
        return $seller->wallet ?: $seller->wallet()->create([
            'balance' => 0.00,
            'pending_balance' => 0.00,
            'total_earned' => 0.00,
            'total_withdrawn' => 0.00,
        ]);
    }

    // ponytail: add money to wallet on order completion
    public function addEarnings(User $seller, float $amount, ?Order $order = null, string $description = ''): void
    {
        $wallet = $this->getWallet($seller);
        $wallet->credit($amount, $order?->id, $description);
    }

    // ponytail: withdraw funds from wallet
    public function withdraw(User $seller, float $amount, string $description = ''): void
    {
        $wallet = $this->getWallet($seller);
        $wallet->debit($amount, $description);
    }
}
