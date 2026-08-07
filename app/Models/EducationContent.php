<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationContent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id', 'title', 'slug', 'content', 'thumbnail_url',
        'content_type', 'status', 'published_at',
        // ponytail: keep
        'excerpt', 'view_count', 'is_featured',
    ];

    protected function casts(): array
    {
        return ['is_featured' => 'boolean', 'published_at' => 'datetime'];
    }

    public function scopePublished($query) { return $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now()); }
    public function scopeFeatured($query)  { return $query->where('is_featured', true); }

    public function admin(): BelongsTo { return $this->belongsTo(User::class, 'admin_id'); }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(WasteCategory::class, 'education_content_category');
    }

    public function getYoutubeIdAttribute(): ?string
    {
        $raws = [$this->thumbnail_url, $this->content];
        foreach ($raws as $rawUrl) {
            if (!$rawUrl) continue;
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $rawUrl, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    public function getDisplayThumbnailUrlAttribute(): string
    {
        // 1. Check if video is YouTube and get CDN thumbnail from i.ytimg.com
        $ytId = $this->youtube_id;
        if ($ytId) {
            return "https://i.ytimg.com/vi/{$ytId}/hqdefault.jpg";
        }

        // 2. If custom image thumbnail is uploaded (not a youtube link)
        if ($this->thumbnail_url && !str_contains($this->thumbnail_url, 'youtube') && !str_contains($this->thumbnail_url, 'youtu.be')) {
            return str_starts_with($this->thumbnail_url, 'http')
                ? $this->thumbnail_url
                : asset('storage/' . $this->thumbnail_url);
        }

        // 3. Fallback image placeholder
        return 'https://placehold.co/640x360/1a2e1a/7A9C59?text=' . urlencode($this->title ?? 'Video Edukasi');
    }

    public function getContentUrlAttribute(): string
    {
        $raw = $this->content;
        if ($this->content_type === 'guide') {
            $parts = explode('|', $raw . '|');
            $raw = $parts[1] ?? '';
        }
        return $raw ?? '';
    }

    public function getFormattedContentUrlAttribute(): string
    {
        $url = trim($this->content_url);
        if (empty($url)) {
            return '#';
        }
        if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
            return 'https://' . $url;
        }
        return $url;
    }
}
