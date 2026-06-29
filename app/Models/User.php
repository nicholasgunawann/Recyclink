<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_PENDING = 'pending';

    protected $fillable = [
        'name', 'email', 'password', 'phone_number', 'avatar', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ponytail: role helpers — one-liner guards used everywhere
    public function isActive(): bool { return $this->status === self::STATUS_ACTIVE; }
    public function isSeller(): bool { return $this->hasRole('seller'); }
    public function isBuyer(): bool  { return $this->hasRole('buyer'); }
    public function isAdmin(): bool  { return $this->hasRole('admin'); }

    // ponytail: cached seller summary — one combined query to cut remote DB round trips
    public function getCachedSellerSummary(): array
    {
        return Cache::remember("seller_dashboard_summary_{$this->id}", rand(120, 300), function () {
            $row = DB::selectOne('
                SELECT
                    (SELECT COUNT(*) FROM waste_listings WHERE seller_id = ? AND deleted_at IS NULL) as listings_count,
                    (SELECT COUNT(*) FROM orders WHERE seller_id = ? AND deleted_at IS NULL) as orders_count
            ', [$this->id, $this->id]);
            return [
                'listings_count' => (int) $row->listings_count,
                'orders_count'   => (int) $row->orders_count,
            ];
        });
    }

    // ponytail: cached buyer summary — one combined query to cut remote DB round trips
    public function getCachedBuyerSummary(): array
    {
        return Cache::remember("buyer_dashboard_summary_{$this->id}", rand(120, 300), function () {
            $row = DB::selectOne('
                SELECT
                    (SELECT COUNT(*) FROM orders WHERE buyer_id = ? AND deleted_at IS NULL) as orders_count,
                    (SELECT COUNT(*) FROM favorite_listings WHERE buyer_id = ?) as favorites_count
            ', [$this->id, $this->id]);
            return [
                'orders_count'    => (int) $row->orders_count,
                'favorites_count' => (int) $row->favorites_count,
            ];
        });
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function sellerProfile(): HasOne { return $this->hasOne(SellerProfile::class); }
    public function buyerProfile(): HasOne  { return $this->hasOne(BuyerProfile::class); }
    public function wallet(): HasOne        { return $this->hasOne(SellerWallet::class, 'seller_id'); }

    public function wasteListings(): HasMany          { return $this->hasMany(WasteListing::class, 'seller_id'); }
    public function buyerOrders(): HasMany            { return $this->hasMany(Order::class, 'buyer_id'); }
    public function sellerOrders(): HasMany           { return $this->hasMany(Order::class, 'seller_id'); }
    public function messages(): HasMany               { return $this->hasMany(Message::class, 'sender_id'); }
    public function reviewsGiven(): HasMany           { return $this->hasMany(Review::class, 'reviewer_id'); }
    public function reviewsReceived(): HasMany        { return $this->hasMany(Review::class, 'reviewed_user_id'); }
    public function complaints(): HasMany             { return $this->hasMany(Complaint::class, 'complainant_id'); }
    public function notifications(): HasMany          { return $this->hasMany(Notification::class); }
    public function activityLogs(): HasMany           { return $this->hasMany(ActivityLog::class); }
    public function educationContents(): HasMany      { return $this->hasMany(EducationContent::class, 'admin_id'); }
    public function conversationsAsBuyer(): HasMany   { return $this->hasMany(Conversation::class, 'buyer_id'); }
    public function conversationsAsSeller(): HasMany  { return $this->hasMany(Conversation::class, 'seller_id'); }

    public function favoriteListings(): HasMany
    {
        return $this->hasMany(FavoriteListing::class, 'buyer_id');
    }
}
