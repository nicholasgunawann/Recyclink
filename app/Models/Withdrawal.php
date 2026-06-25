<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'wallet_id', 'amount', 'bank_name', 'bank_account_number', 'bank_account_name',
        'status', 'admin_note', 'processed_at',
        // ponytail: keep — needed for admin approval flow + financial audit
        'user_id', 'approved_by', 'admin_fee', 'net_amount', 'withdrawal_number',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'admin_fee'    => 'decimal:2',
            'net_amount'   => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function isPending(): bool   { return $this->status === self::STATUS_PENDING; }
    public function isCompleted(): bool { return $this->status === self::STATUS_APPROVED || $this->status === self::STATUS_PAID; }
    public function isPaid(): bool      { return $this->status === self::STATUS_PAID; }

    public function wallet(): BelongsTo   { return $this->belongsTo(SellerWallet::class, 'wallet_id'); }
    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
}
