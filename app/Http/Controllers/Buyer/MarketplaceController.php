<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\WasteListing;
use App\Models\WasteCategory;
use App\Models\User;
use App\Services\MarketplaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MarketplaceController extends Controller
{
    protected MarketplaceService $marketplaceService;

    public function __construct(MarketplaceService $marketplaceService)
    {
        $this->marketplaceService = $marketplaceService;
    }

    // Marketplace index – supports paginated AJAX queries
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            if ($request->input('tab') === 'toko') {
                $query = User::role('seller')->whereHas('sellerProfile')->with('sellerProfile');
                
                if ($request->filled('lokasi')) {
                    $query->whereHas('sellerProfile', function($q) use ($request) {
                        $q->where('city', 'like', '%' . $request->input('lokasi') . '%');
                    });
                }

                if ($request->has('categories')) {
                    $catNames = (array)$request->input('categories');
                    $query->whereHas('wasteListings', function($q) use ($catNames) {
                        $q->whereHas('category', function($q2) use ($catNames) {
                            $q2->whereIn(\DB::raw('LOWER(category_name)'), array_map('strtolower', $catNames));
                        });
                    });
                }
                
                $paginator = $query->paginate(9);
                $items = collect($paginator->items())->map(function($s) {
                    return [
                        'id' => $s->id,
                        'name' => $s->sellerProfile->business_name ?? $s->name,
                        'city' => $s->sellerProfile->city ?? 'Lokasi tidak diketahui',
                        'type' => $s->sellerProfile->business_type ?? 'Toko',
                        'avatar' => $s->avatar ? (str_starts_with($s->avatar, 'http') ? $s->avatar : asset('storage/'.$s->avatar)) : 'https://ui-avatars.com/api/?name='.urlencode($s->name).'&background=7A9C59&color=fff',
                    ];
                });
            } else {
                $query = WasteListing::verified()->with(['category', 'primaryImage', 'seller.sellerProfile']);
                
                if ($request->input('available_only', 1) == 1) {
                    $query->available();
                }

                if ($request->has('categories')) {
                    $catNames = (array)$request->input('categories');
                    $query->whereHas('category', function($q) use ($catNames) {
                        $q->whereIn(\DB::raw('LOWER(category_name)'), array_map('strtolower', $catNames));
                    });
                }
                if ($request->filled('lokasi')) {
                    $query->where('city', 'like', '%' . $request->input('lokasi') . '%');
                }
                if ($request->filled('volume_min')) {
                    $query->where('quantity', '>=', $request->input('volume_min'));
                }
                if ($request->filled('harga_min')) {
                    $query->where('price_per_unit', '>=', $request->input('harga_min'));
                }
                if ($request->filled('harga_max')) {
                    $query->where('price_per_unit', '<=', $request->input('harga_max'));
                }
                
                $sort = $request->input('sort', 'terbaru');
                if ($sort === 'harga-asc') $query->orderBy('price_per_unit', 'asc');
                elseif ($sort === 'harga-desc') $query->orderBy('price_per_unit', 'desc');
                elseif ($sort === 'stok-desc') $query->orderBy('quantity', 'desc');
                elseif ($sort === 'jarak-asc') $query->orderBy('city', 'asc');
                else $query->latest();
                
                $paginator = $query->paginate(9);
                $items = collect($paginator->items())->map(function($l) {
                    return [
                        'id' => $l->id,
                        'title' => $l->title,
                        'categoryLabel' => $l->category->category_name ?? 'Limbah',
                        'city' => $l->city,
                        'price' => (float)$l->price_per_unit,
                        'unit' => $l->unit,
                        'stock' => (float)$l->quantity,
                        'image' => $l->primaryImage ? $l->primaryImage->url : ''
                    ];
                });
            }
            
            return response()->json([
                'data' => $items,
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ]);
        }

        $categories = Cache::remember('marketplace_categories', 3600, function () {
            return WasteCategory::all();
        });
        return view('pages.MarketplaceLimbah.index', compact('categories'));
    }

    // Marketplace detail – use route model binding
    public function show(WasteListing $wasteListing)
    {
        // Ensure only approved listings are visible
        if ($wasteListing->verification_status !== WasteListing::VERIFICATION_APPROVED) {
            abort(404, 'Listing not found or not approved.');
        }

        $wasteListing->load(['category', 'images', 'seller.sellerProfile']);
        
        return view('pages.MarketplaceLimbah.show', ['listing' => $wasteListing]);
    }

    public function store(Request $request, User $user)
    {
        // Ensure user is a seller
        if (!$user->hasRole('seller')) {
            abort(404, 'Toko tidak ditemukan.');
        }

        $user->load('sellerProfile');
        
        $tab = $request->input('tab');
        
        $query = WasteListing::with(['category', 'primaryImage'])
            ->where('seller_id', $user->id)
            ->where('verification_status', WasteListing::VERIFICATION_APPROVED);
            
        if ($tab === 'terjual') {
            $query->where('availability_status', WasteListing::AVAILABILITY_SOLD_OUT);
        } else {
            $query->where('availability_status', WasteListing::AVAILABILITY_AVAILABLE);
        }
        
        // Fetch store listings
        $listings = $query->latest()->get();

        return view('pages.MarketplaceLimbah.store', compact('user', 'listings', 'tab'));
    }
}

