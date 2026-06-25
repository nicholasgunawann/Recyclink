<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class WasteListing extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        $clearCache = function () {
            // ponytail: clear marketplace cache tags when listing changes
            Cache::tags(['marketplace_listings'])->flush();
        };

        static::created($clearCache);
        static::updated($clearCache);
        static::deleted($clearCache);
    }

    public const VERIFICATION_PENDING = 'pending';
    public const VERIFICATION_APPROVED = 'approved';
    public const VERIFICATION_REJECTED = 'rejected';

    public const AVAILABILITY_AVAILABLE = 'available';
    public const AVAILABILITY_SOLD_OUT = 'sold_out';
    public const AVAILABILITY_INACTIVE = 'inactive';

    protected $fillable = [
        'seller_id', 'category_id', 'title', 'slug', 'description',
        'quantity', 'unit', 'price_per_unit', 'address', 'city', 'province',
        'latitude', 'longitude', 'availability_status', 'verification_status',
        'admin_note', 'min_order', 'waste_condition', 'view_count', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'price_per_unit' => 'decimal:2',
            'quantity'       => 'decimal:2',
            'min_order'      => 'decimal:2',
            'latitude'       => 'decimal:8',
            'longitude'      => 'decimal:8',
            'published_at'   => 'datetime',
        ];
    }

    public function scopeAvailable($query) { return $query->where('availability_status', self::AVAILABILITY_AVAILABLE); }
    public function scopeVerified($query)  { return $query->where('verification_status', self::VERIFICATION_APPROVED); }
    public function scopeByCity($query, $city) { return $query->where('city', $city); }

    public function seller(): BelongsTo  { return $this->belongsTo(User::class, 'seller_id'); }
    public function category(): BelongsTo { return $this->belongsTo(WasteCategory::class); }
    public function orderItems(): HasMany { return $this->hasMany(OrderItem::class, 'listing_id'); }
    public function conversations(): HasMany { return $this->hasMany(Conversation::class, 'listing_id'); }

    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class, 'listing_id')->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ListingImage::class, 'listing_id')->where('is_primary', true);
    }

    public function favoritedBy(): HasMany
    {
        return $this->hasMany(FavoriteListing::class, 'listing_id');
    }

    public function incrementViewCount(): void { $this->increment('view_count'); }
}
