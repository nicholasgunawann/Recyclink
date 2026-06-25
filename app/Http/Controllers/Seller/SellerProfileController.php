<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSellerProfileRequest;
use App\Services\ProfileService;
use App\Exceptions\RecyclinkException;
use Illuminate\Routing\Controllers\HasMiddleware;

class SellerProfileController extends Controller implements HasMiddleware
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
            'verified',
            'role:seller',
        ];
    }

    // ponytail: edit profile form
    public function edit()
    {
        $profile = auth()->user()->sellerProfile;
        return view('seller.profile.edit', compact('profile'));
    }

    // ponytail: update profile details
    public function update(UpdateSellerProfileRequest $request)
    {
        try {
            $this->profileService->updateSellerProfile(auth()->user(), $request->validated());
            return redirect()->back()->with('success', 'Profile updated successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
