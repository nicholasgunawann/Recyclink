<?php

namespace App\Services;

use App\Models\User;
use App\Models\SellerProfile;
use App\Models\BuyerProfile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

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

        // ponytail: invalidate profile completion cache so middleware re-checks
        Cache::forget("profile_complete_{$user->id}");

        return $profile;
    }

    // ponytail: update buyer profile details
    public function updateBuyerProfile(User $user, array $data): BuyerProfile
    {
        $userData = [
            'name'         => $data['name'] ?? $user->name,
            'phone_number' => $data['phone_number'] ?? $user->phone_number,
        ];

        if (isset($data['email'])) {
            $userData['email'] = $data['email'];
        }

        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        $user->update($userData);

        $profile = $user->buyerProfile ?: new BuyerProfile(['user_id' => $user->id]);
        $profile->fill([
            'address'       => $data['address'] ?? $profile->address,
            'company_name'  => $data['company_name'] ?? $profile->company_name,
            'city'          => $data['city'] ?? $profile->city,
            'industry_type' => $data['industry_type'] ?? $profile->industry_type,
            'province'      => $data['province'] ?? $profile->province,
            'postal_code'   => $data['postal_code'] ?? $profile->postal_code,
        ]);
        $profile->save();

        // ponytail: invalidate profile completion cache so middleware re-checks
        Cache::forget("profile_complete_{$user->id}");

        return $profile;
    }

    // ponytail: check profile completion — cached in Redis so middleware doesn't hit DB every request
    public function checkProfileCompletion(User $user): bool
    {
        return Cache::remember("profile_complete_{$user->id}", 300, function () use ($user) {
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
        });
    }
}

