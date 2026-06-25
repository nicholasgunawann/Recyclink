<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Support\Facades\Cache;

class Payment extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::updated(function ($payment) {
            // ponytail: clear dashboard summaries when payment transitions to paid
            if ($payment->isDirty('payment_status') && $payment->payment_status === static::STATUS_PAID) {
                Cache::forget('admin_dashboard_summary');
                if ($payment->order) {
                    Cache::forget("seller_dashboard_summary_{$payment->order->seller_id}");
                    Cache::forget("buyer_dashboard_summary_{$payment->order->buyer_id}");
                }
                Cache::forget('admin_reports');
            }
        });
    }

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'order_id', 'payment_method', 'payment_gateway', 'payment_reference',
        'amount', 'payment_status', 'paid_at', 'expired_at',
        // ponytail: keep gateway fields
        'payment_number', 'payment_channel', 'gateway_transaction_id',
        'gateway_response', 'virtual_account_number', 'qris_url',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'gateway_response' => 'array',
            'paid_at'          => 'datetime',
            'expired_at'       => 'datetime',
        ];
    }

    public function isPaid(): bool    { return $this->payment_status === self::STATUS_PAID; }
    public function isExpired(): bool { return $this->payment_status === self::STATUS_EXPIRED || ($this->expired_at?->isPast()); }

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
