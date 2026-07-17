<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Cache;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        $clearCache = function ($order) {
            // ponytail: invalidate seller, buyer and admin dashboard caches on order changes
//             Cache::forget('admin_dashboard_summary');
//             Cache::forget("seller_dashboard_summary_{$order->seller_id}");
//             Cache::forget("buyer_dashboard_summary_{$order->buyer_id}");
        };

        static::created($clearCache);
        static::updated($clearCache);
        static::deleted($clearCache);
    }

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_WAITING_PAYMENT = 'waiting_payment';
    public const STATUS_PAID = 'paid';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_DISPUTED = 'disputed';

    protected $fillable = [
        'order_code', 'buyer_id', 'seller_id', 'order_status', 'total_amount',
        'pickup_method', 'pickup_date', 'pickup_time', 'pickup_address',
        'buyer_note', 'seller_note', 'cancelled_at',
        // ponytail: keep financial fields
        'subtotal', 'shipping_cost', 'platform_fee', 'tracking_number', 'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'total_amount'  => 'decimal:2',
            'subtotal'      => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'platform_fee'  => 'decimal:2',
            'pickup_date'   => 'date',
            'cancelled_at'  => 'datetime',
        ];
    }

    public function isPending(): bool    { return $this->order_status === self::STATUS_PENDING; }
    public function isCompleted(): bool  { return $this->order_status === self::STATUS_COMPLETED; }
    public function isCancellable(): bool { return in_array($this->order_status, [self::STATUS_PENDING, self::STATUS_WAITING_PAYMENT]); }

    /** Format: RL-YYYYMM-000001 */
    public static function generateOrderCode(): string
    {
        $prefix = 'RL-' . now()->format('Ym') . '-';
        $last   = static::where('order_code', 'like', $prefix . '%')->lockForUpdate()->orderByDesc('id')->first();
        return $prefix . str_pad($last ? (int) substr($last->order_code, -6) + 1 : 1, 6, '0', STR_PAD_LEFT);
    }

    public function buyer(): BelongsTo      { return $this->belongsTo(User::class, 'buyer_id'); }
    public function seller(): BelongsTo     { return $this->belongsTo(User::class, 'seller_id'); }
    public function items(): HasMany      { return $this->hasMany(OrderItem::class); }
    public function payment(): HasOne     { return $this->hasOne(Payment::class); }
    public function reviews(): HasMany    { return $this->hasMany(Review::class); }
    public function complaints(): HasMany { return $this->hasMany(Complaint::class); }
}
