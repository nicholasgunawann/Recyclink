<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Support\Facades\Cache;

class WasteCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id', 'category_name', 'slug', 'description',
        'is_active', 'icon', 'color', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        // ponytail: invalidate cache on category changes
        $clear = fn() => \Illuminate\Support\Facades\Cache::forget('waste_categories');
        static::created($clear);
        static::updated($clear);
        static::deleted($clear);
    }

    public static function getActiveCached()
    {
        // ponytail: store as plain array to prevent Eloquent Collection deserialization issues, hydrate to models on retrieve
        $data = Cache::remember('waste_categories', 3600, fn() =>
            static::active()->orderBy('sort_order')->get(['id', 'category_name', 'slug', 'icon', 'color'])->toArray()
        );
        return static::hydrate($data);
    }

    public function scopeActive($query)        { return $query->where('is_active', true); }
    public function scopeRootCategories($query){ return $query->whereNull('parent_id'); }

    public function parent(): BelongsTo    { return $this->belongsTo(WasteCategory::class, 'parent_id'); }
    public function children(): HasMany    { return $this->hasMany(WasteCategory::class, 'parent_id')->orderBy('sort_order'); }
    public function listings(): HasMany    { return $this->hasMany(WasteListing::class, 'category_id'); }

    public function getShortNameAttribute(): string
    {
        $name = $this->category_name ?? '';
        if (str_starts_with($name, 'Lainnya')) {
            return 'Lainnya';
        }
        return $name;
    }
}
