<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBuyerProfileRequest;
use App\Services\ProfileService;
use App\Exceptions\RecyclinkException;
use Illuminate\Routing\Controllers\HasMiddleware;

class BuyerProfileController extends Controller implements HasMiddleware
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
            'role:buyer',
        ];
    }

    // ponytail: profile view
    public function index()
    {
        $profile = auth()->user()->buyerProfile;
        return view('buyer.profile.index', compact('profile'));
    }

    // ponytail: edit profile form
    public function edit()
    {
        $profile = auth()->user()->buyerProfile;
        return view('buyer.profile.edit', compact('profile'));
    }

    // ponytail: update profile details
    public function update(UpdateBuyerProfileRequest $request)
    {
        try {
            $this->profileService->updateBuyerProfile(auth()->user(), $request->validated());
            return redirect()->route('buyer.profile.index')->with('success', 'Profile updated successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
