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
        $clearCache = function () {
            // ponytail: clear active categories cache on modifications
            Cache::forget('waste_categories_active');
        };

        static::created($clearCache);
        static::updated($clearCache);
        static::deleted($clearCache);
    }

    public static function getActiveCached()
    {
        return Cache::remember('waste_categories_active', 3600, function () {
            return static::active()->orderBy('sort_order')->get();
        });
    }

    public function scopeActive($query)        { return $query->where('is_active', true); }
    public function scopeRootCategories($query){ return $query->whereNull('parent_id'); }

    public function parent(): BelongsTo    { return $this->belongsTo(WasteCategory::class, 'parent_id'); }
    public function children(): HasMany    { return $this->hasMany(WasteCategory::class, 'parent_id')->orderBy('sort_order'); }
    public function listings(): HasMany    { return $this->hasMany(WasteListing::class, 'category_id'); }
}
