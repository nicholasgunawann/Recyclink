<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\WasteListing;
use App\Services\MarketplaceService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class MarketplaceController extends Controller implements HasMiddleware
{
    protected MarketplaceService $marketplaceService;

    public function __construct(MarketplaceService $marketplaceService)
    {
        $this->marketplaceService = $marketplaceService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
            'verified',
        ];
    }

    // ponytail: browse verified listings
    public function index(Request $request)
    {
        $listings = $this->marketplaceService->getApprovedAvailableListings($request->all());
        return view('buyer.marketplace.index', compact('listings'));
    }

    // ponytail: view listing details page
    public function show(WasteListing $wasteListing)
    {
        if ($wasteListing->verification_status !== WasteListing::VERIFICATION_APPROVED) {
            abort(404);
        }

        $wasteListing = $this->marketplaceService->getListingDetail($wasteListing);
        $relatedListings = $this->marketplaceService->getRelatedListings($wasteListing);

        return view('buyer.marketplace.show', compact('wasteListing', 'relatedListings'));
    }
}
