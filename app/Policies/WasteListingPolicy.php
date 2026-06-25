<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WasteListing;

class WasteListingPolicy
{
    // ponytail: allows viewing a listing based on roles (guest/buyer can only see approved & available)
    public function view(?User $user, WasteListing $listing): bool
    {
        if ($user?->isAdmin()) {
            return true;
        }

        if ($user?->isSeller() && $listing->seller_id === $user->id) {
            return true;
        }

        return $listing->verification_status === WasteListing::VERIFICATION_APPROVED
            && $listing->availability_status === WasteListing::AVAILABILITY_AVAILABLE;
    }

    // ponytail: only sellers can create listings
    public function create(User $user): bool
    {
        return $user->isSeller();
    }

    // ponytail: sellers can only edit their own listings
    public function update(User $user, WasteListing $listing): bool
    {
        return $user->isSeller() && $listing->seller_id === $user->id;
    }

    // ponytail: sellers can only delete their own listings
    public function delete(User $user, WasteListing $listing): bool
    {
        return $user->isSeller() && $listing->seller_id === $user->id;
    }
}
