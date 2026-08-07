<?php

namespace App\Services;

use App\Models\User;
use App\Models\WasteListing;
use App\Models\ListingImage;
use App\Exceptions\UnauthorizedBusinessActionException;
use App\Exceptions\InvalidOrderStatusException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class WasteListingService
{
    // ponytail: create a new listing for a seller
    public function createListing(User $seller, array $data): WasteListing
    {
        if (!$seller->isSeller()) {
            throw new UnauthorizedBusinessActionException("Only sellers can create listings.");
        }

        $data['seller_id'] = $seller->id;
        $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
        $data['verification_status'] = WasteListing::VERIFICATION_PENDING;
        $data['availability_status'] = WasteListing::AVAILABILITY_AVAILABLE;

        return WasteListing::create($data);
    }

    // ponytail: update an existing listing
    public function updateListing(User $seller, WasteListing $listing, array $data): WasteListing
    {
        if ($listing->seller_id !== $seller->id) {
            throw new UnauthorizedBusinessActionException("You can only edit your own listings.");
        }

        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
        }

        if ($listing->verification_status === WasteListing::VERIFICATION_REJECTED) {
            $data['verification_status'] = WasteListing::VERIFICATION_PENDING;
        }

        $listing->update($data);
        return $listing;
    }

    // ponytail: delete a listing
    public function deleteListing(User $seller, WasteListing $listing): bool
    {
        if ($listing->seller_id !== $seller->id) {
            throw new UnauthorizedBusinessActionException("You can only delete your own listings.");
        }

        return $listing->delete();
    }

    // ponytail: change listing availability status
    public function changeAvailability(User $seller, WasteListing $listing, string $status): WasteListing
    {
        if ($listing->seller_id !== $seller->id) {
            throw new UnauthorizedBusinessActionException("You can only modify your own listings.");
        }

        if (!in_array($status, [WasteListing::AVAILABILITY_AVAILABLE, WasteListing::AVAILABILITY_SOLD_OUT, WasteListing::AVAILABILITY_INACTIVE])) {
            throw new InvalidOrderStatusException("Invalid availability status.");
        }

        $listing->update(['availability_status' => $status]);
        return $listing;
    }

    // ponytail: upload listing images
    public function uploadListingImages(WasteListing $listing, array $images): array
    {
        $uploaded = [];
        $hasPrimary = $listing->images()->where('is_primary', true)->exists();

        foreach ($images as $index => $image) {
            if ($image instanceof \Illuminate\Http\UploadedFile) {
                $imageUrl = null;
                $disk = 'public';

                // Attempt Cloudinary upload if configured
                $cloudinaryUrl = env('CLOUDINARY_URL');
                $cloudName = env('CLOUDINARY_CLOUD_NAME');

                if ($cloudinaryUrl || ($cloudName && $cloudName !== 'YOUR_CLOUD_NAME_HERE')) {
                    try {
                        $cloudinary = $cloudinaryUrl
                            ? new \Cloudinary\Cloudinary($cloudinaryUrl)
                            : new \Cloudinary\Cloudinary([
                                'cloud' => [
                                    'cloud_name' => $cloudName,
                                    'api_key'    => env('CLOUDINARY_API_KEY'),
                                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                                ]
                            ]);

                        $response = $cloudinary->uploadApi()->upload($image->getRealPath(), [
                            'folder' => 'recyclink/listings',
                            'resource_type' => 'image',
                        ]);

                        if (!empty($response['secure_url'])) {
                            $imageUrl = $response['secure_url'];
                            $disk = 'cloudinary';
                        }
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("Cloudinary upload failed, falling back to local storage: " . $e->getMessage());
                    }
                }

                // Robust fallback to local disk storage if Cloudinary is not used or fails
                if (!$imageUrl) {
                    $imageUrl = $image->store('listings', 'public');
                    $disk = 'public';
                }

                $isPrimary = $index === 0 && !$hasPrimary;

                $uploaded[] = ListingImage::create([
                    'listing_id' => $listing->id,
                    'image_url' => $imageUrl,
                    'disk' => $disk,
                    'is_primary' => $isPrimary,
                    'sort_order' => $index,
                ]);
            }
        }

        return $uploaded;
    }

    // ponytail: set an image as primary for the listing
    public function setPrimaryImage(WasteListing $listing, ListingImage $image): void
    {
        if ($image->listing_id !== $listing->id) {
            throw new InvalidOrderStatusException("Image does not belong to this listing.");
        }

        DB::transaction(function () use ($listing, $image) {
            $listing->images()->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });
    }
}
