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

    // ponytail: cached seller summary stats
    public function getCachedSellerSummary(): array
    {
        return Cache::remember("seller_dashboard_summary_{$this->id}", rand(120, 300), function () {
            return [
                'listings_count' => $this->wasteListings()->count(),
                'orders_count' => $this->sellerOrders()->count(),
            ];
        });
    }

    // ponytail: cached buyer summary stats
    public function getCachedBuyerSummary(): array
    {
        return Cache::remember("buyer_dashboard_summary_{$this->id}", rand(120, 300), function () {
            return [
                'orders_count' => $this->buyerOrders()->count(),
                'favorites_count' => $this->favoriteListings()->count(),
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
