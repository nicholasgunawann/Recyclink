<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\WasteListing;
use App\Exceptions\RecyclinkException;
use Illuminate\Routing\Controllers\HasMiddleware;

class BuyerFavoriteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'verified',
            'role:buyer',
        ];
    }

    // ponytail: view favorite listings
    public function index()
    {
        $favorites = auth()->user()->favoriteListings()->with('listing.seller')->latest()->paginate(15);
        return view('buyer.favorites.index', compact('favorites'));
    }

    // ponytail: add listing to favorites
    public function store(WasteListing $wasteListing)
    {
        auth()->user()->favoriteListings()->firstOrCreate([
            'listing_id' => $wasteListing->id,
        ]);
        return redirect()->back()->with('success', 'Listing added to favorites.');
    }

    // ponytail: remove listing from favorites
    public function destroy(WasteListing $wasteListing)
    {
        auth()->user()->favoriteListings()->where('listing_id', $wasteListing->id)->delete();
        return redirect()->back()->with('success', 'Listing removed from favorites.');
    }
}
