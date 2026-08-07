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
            'role:admin',
        ];
    }

    // ponytail: list listings awaiting verification or all with eager loading
    public function index()
    {
        $listings = WasteListing::with(['seller.sellerProfile', 'category', 'primaryImage'])->latest()->paginate(15);
        return view('admin.listings.index', compact('listings'));
    }

    // ponytail: show listing details for review
    public function show(WasteListing $wasteListing)
    {
        $wasteListing->load(['seller.sellerProfile', 'category', 'images']);
        return view('admin.listings.show', ['listing' => $wasteListing]);
    }

    // ponytail: approve listing
    public function approve(Request $request, WasteListing $wasteListing)
    {
        try {
            $this->verificationService->approveListing(auth()->user(), $wasteListing, $request->input('reason'));
            return redirect()->back()->with('success', 'Listing berhasil disetujui dan ditayangkan.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: reject listing
    public function reject(RejectListingRequest $request, WasteListing $wasteListing)
    {
        try {
            $this->verificationService->rejectListing(auth()->user(), $wasteListing, $request->input('reason'));
            return redirect()->back()->with('success', 'Listing berhasil ditolak.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: deactivate verified listing
    public function deactivate(Request $request, WasteListing $wasteListing)
    {
        try {
            $this->verificationService->deactivateListing(auth()->user(), $wasteListing, $request->input('reason'));
            return redirect()->back()->with('success', 'Listing berhasil dinonaktifkan.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: activate/reactivate listing
    public function activate(Request $request, WasteListing $wasteListing)
    {
        try {
            $this->verificationService->activateListing(auth()->user(), $wasteListing, $request->input('reason'));
            return redirect()->back()->with('success', 'Listing berhasil diaktifkan kembali.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
