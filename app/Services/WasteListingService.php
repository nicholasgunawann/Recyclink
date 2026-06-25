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
        
        foreach ($images as $index => $image) {
            if ($image instanceof \Illuminate\Http\UploadedFile) {
                $path = $image->store('listings', 'public');
                $isPrimary = $index === 0 && !$listing->images()->where('is_primary', true)->exists();

                $uploaded[] = ListingImage::create([
                    'listing_id' => $listing->id,
                    'image_url' => $path,
                    'disk' => 'public',
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
