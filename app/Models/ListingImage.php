<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ListingImage extends Model
{
    use HasFactory;

    protected $fillable = ['listing_id', 'image_url', 'disk', 'is_primary', 'sort_order'];

    protected function casts(): array { return ['is_primary' => 'boolean']; }

    protected static function booted(): void
    {
        // ponytail: automatically delete physical image asset from Cloudinary or local disk when DB record is destroyed
        static::deleting(function (ListingImage $image) {
            $image->deletePhysicalFile();
        });
    }

    // ponytail: accessor resolves disk-aware URL transparently using asset()
    public function getUrlAttribute(): string
    {
        if (empty($this->image_url)) return '';

        if (str_starts_with($this->image_url, 'http://') || str_starts_with($this->image_url, 'https://')) {
            return $this->image_url;
        }

        return asset('storage/' . ltrim($this->image_url, '/'));
    }

    // ponytail: extract Cloudinary public_id from secure_url or path
    public function getCloudinaryPublicId(): ?string
    {
        if (empty($this->image_url)) return null;

        if (str_contains($this->image_url, 'res.cloudinary.com') || str_contains($this->image_url, '/upload/')) {
            $path = parse_url($this->image_url, PHP_URL_PATH);
            if (!$path) return null;

            $uploadPos = strpos($path, '/upload/');
            if ($uploadPos === false) return null;

            $publicIdWithVer = substr($path, $uploadPos + 8);
            $publicId = preg_replace('/^v\d+\//', '', $publicIdWithVer);
            return preg_replace('/\.[^.]+$/', '', $publicId);
        }

        return preg_replace('/\.[^.]+$/', '', $this->image_url);
    }

    // ponytail: remove physical file asset from Cloudinary API or local disk storage
    public function deletePhysicalFile(): void
    {
        if ($this->disk === 'cloudinary') {
            $publicId = $this->getCloudinaryPublicId();
            if ($publicId) {
                try {
                    $cloudinaryUrl = env('CLOUDINARY_URL');
                    $cloudName = env('CLOUDINARY_CLOUD_NAME');

                    if ($cloudinaryUrl || ($cloudName && $cloudName !== 'YOUR_CLOUD_NAME_HERE')) {
                        $cloudinary = $cloudinaryUrl
                            ? new \Cloudinary\Cloudinary($cloudinaryUrl)
                            : new \Cloudinary\Cloudinary([
                                'cloud' => [
                                    'cloud_name' => $cloudName,
                                    'api_key'    => env('CLOUDINARY_API_KEY'),
                                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                                ]
                            ]);

                        $cloudinary->uploadApi()->destroy($publicId, [
                            'resource_type' => 'image',
                            'invalidate' => true,
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::warning("Failed to destroy Cloudinary asset {$publicId}: " . $e->getMessage());
                }
            }
        } elseif ($this->disk === 'public') {
            try {
                if (Storage::disk('public')->exists($this->image_url)) {
                    Storage::disk('public')->delete($this->image_url);
                }
            } catch (\Throwable $e) {
                Log::warning("Failed to delete local image file {$this->image_url}: " . $e->getMessage());
            }
        }
    }

    public function listing(): BelongsTo { return $this->belongsTo(WasteListing::class); }
}
