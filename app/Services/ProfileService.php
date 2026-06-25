<?php

namespace App\Services;

use App\Models\User;
use App\Models\SellerProfile;
use App\Models\BuyerProfile;

class ProfileService
{
    // ponytail: update seller profile details
    public function updateSellerProfile(User $user, array $data): SellerProfile
    {
        if (isset($data['phone_number'])) {
            $user->update(['phone_number' => $data['phone_number']]);
        }

        $profile = $user->sellerProfile ?: new SellerProfile(['user_id' => $user->id]);
        $profile->fill($data);
        $profile->save();

        return $profile;
    }

    // ponytail: update buyer profile details
    public function updateBuyerProfile(User $user, array $data): BuyerProfile
    {
        $user->update([
            'name' => $data['full_name'] ?? $user->name,
            'phone_number' => $data['phone_number'] ?? $user->phone_number,
        ]);

        $profile = $user->buyerProfile ?: new BuyerProfile(['user_id' => $user->id]);
        $profile->fill([
            'address' => $data['shipping_address'] ?? $profile->address,
        ]);
        $profile->save();

        return $profile;
    }

    // ponytail: check if user profile is completed
    public function checkProfileCompletion(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isSeller()) {
            $profile = $user->sellerProfile;
            return $profile && !empty($profile->business_name) && !empty($profile->address);
        }

        if ($user->isBuyer()) {
            $profile = $user->buyerProfile;
            return $profile && !empty($profile->address);
        }

        return false;
    }
}
