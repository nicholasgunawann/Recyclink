<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WasteListing;
use App\Http\Requests\RejectListingRequest;
use App\Services\ListingVerificationService;
use App\Exceptions\RecyclinkException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class AdminListingVerificationController extends Controller implements HasMiddleware
{
    protected ListingVerificationService $verificationService;

    public function __construct(ListingVerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
            'verified',
            'role:admin',
        ];
    }

    // ponytail: list listings awaiting verification or all with eager loading
    public function index()
    {
        $listings = WasteListing::with(['seller', 'category', 'primaryImage'])->latest()->paginate(15);
        return view('admin.listings.index', compact('listings'));
    }

    // ponytail: show listing details for review
    public function show(WasteListing $listing)
    {
        $listing->load(['seller', 'category']);
        return view('admin.listings.show', compact('listing'));
    }

    // ponytail: approve listing
    public function approve(Request $request, WasteListing $listing)
    {
        try {
            $this->verificationService->approveListing(auth()->user(), $listing, $request->input('reason'));
            return redirect()->back()->with('success', 'Listing approved successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: reject listing
    public function reject(RejectListingRequest $request, WasteListing $listing)
    {
        try {
            $this->verificationService->rejectListing(auth()->user(), $listing, $request->input('reason'));
            return redirect()->back()->with('success', 'Listing rejected successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: deactivate verified listing
    public function deactivate(Request $request, WasteListing $listing)
    {
        try {
            $this->verificationService->deactivateListing(auth()->user(), $listing, $request->input('reason'));
            return redirect()->back()->with('success', 'Listing deactivated successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
