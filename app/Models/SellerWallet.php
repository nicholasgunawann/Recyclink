<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellerWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id', 'balance', 'pending_balance', 'total_earned', 'total_withdrawn',
    ];

    protected function casts(): array
    {
        return [
            'balance'          => 'decimal:2',
            'pending_balance'  => 'decimal:2',
            'total_earned'     => 'decimal:2',
            'total_withdrawn'  => 'decimal:2',
        ];
    }

    public function canWithdraw(float $amount): bool { return $this->balance >= $amount && $amount > 0; }

    // ponytail: handle credit transaction directly in model
    public function credit(float $amount, ?int $orderId = null, string $description = ''): void
    {
        $balanceBefore = $this->balance;
        $this->increment('balance', $amount);
        $this->increment('total_earned', $amount);
        $this->refresh();

        $this->transactions()->create([
            'order_id' => $orderId,
            'transaction_type' => 'credit',
            'amount' => $amount,
            'description' => $description,
            'reference_number' => 'TXN-' . now()->format('YmdHis') . '-' . rand(1000, 9999),
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
        ]);
    }

    // ponytail: handle debit transaction directly in model
    public function debit(float $amount, string $description = ''): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Amount must be greater than zero.");
        }

        if (!$this->canWithdraw($amount)) {
            throw new \App\Exceptions\InsufficientWalletBalanceException();
        }

        $balanceBefore = $this->balance;
        $this->decrement('balance', $amount);
        $this->increment('total_withdrawn', $amount);
        $this->refresh();

        $this->transactions()->create([
            'order_id' => null,
            'transaction_type' => 'debit',
            'amount' => $amount,
            'description' => $description,
            'reference_number' => 'TXN-' . now()->format('YmdHis') . '-' . rand(1000, 9999),
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
        ]);
    }

    public function seller(): BelongsTo        { return $this->belongsTo(User::class, 'seller_id'); }
    public function transactions(): HasMany    { return $this->hasMany(WalletTransaction::class, 'wallet_id')->latest(); }
    public function withdrawals(): HasMany     { return $this->hasMany(Withdrawal::class, 'wallet_id'); }
}
