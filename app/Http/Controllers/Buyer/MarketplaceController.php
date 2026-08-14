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
                $query = User::whereHas('sellerProfile')->with(['sellerProfile', 'wasteListings']);
                
                if ($request->filled('search') || $request->filled('q')) {
                    $search = $request->input('search') ?? $request->input('q');
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                          ->orWhereHas('sellerProfile', function($sp) use ($search) {
                              $sp->where('business_name', 'like', '%' . $search . '%')
                                 ->orWhere('city', 'like', '%' . $search . '%')
                                 ->orWhere('business_type', 'like', '%' . $search . '%');
                          });
                    });
                }

                if ($request->filled('lokasi')) {
                    $lokasi = $request->input('lokasi');
                    $query->whereHas('sellerProfile', function($q) use ($lokasi) {
                        $q->where('city', 'like', '%' . $lokasi . '%');
                    });
                }

                if ($request->has('categories')) {
                    $catNames = (array)$request->input('categories');
                    $query->whereHas('wasteListings', function($q) use ($catNames) {
                        $q->whereHas('category', function($q2) use ($catNames) {
                            $q2->where(function($subQ) use ($catNames) {
                                foreach ($catNames as $cat) {
                                    $subQ->orWhere('category_name', 'like', '%' . $cat . '%')
                                         ->orWhere('slug', 'like', '%' . $cat . '%');
                                }
                            });
                        });
                    });
                }
                
                $sort = $request->input('sort', 'terbaru');
                if ($sort === 'jarak-asc') {
                    $query->join('seller_profiles', 'users.id', '=', 'seller_profiles.user_id')
                          ->orderBy('seller_profiles.city', 'asc')
                          ->select('users.*');
                } else {
                    $query->latest('users.id');
                }

                $paginator = $query->paginate(18);
                $items = collect($paginator->items())->map(function($s) {
                    return [
                        'id' => $s->id,
                        'name' => $s->sellerProfile->business_name ?? $s->name,
                        'city' => $s->sellerProfile->city ?? 'Lokasi tidak diketahui',
                        'type' => $s->sellerProfile->business_type ?? 'Pengepul / Toko',
                        'avatar' => $s->avatar ? (str_starts_with($s->avatar, 'http') ? $s->avatar : asset('storage/'.$s->avatar)) : 'https://ui-avatars.com/api/?name='.urlencode($s->sellerProfile->business_name ?? $s->name).'&background=7A9C59&color=fff',
                    ];
                });
            } else {
                $query = WasteListing::verified()
                    ->where('availability_status', '!=', WasteListing::AVAILABILITY_INACTIVE)
                    ->with(['category', 'primaryImage', 'images', 'seller.sellerProfile']);
                
                if ($request->input('available_only', 1) == 1) {
                    $query->available();
                }

                if ($request->filled('search') || $request->filled('q')) {
                    $search = $request->input('search') ?? $request->input('q');
                    $query->where(function($q) use ($search) {
                        $q->where('title', 'like', '%' . $search . '%')
                          ->orWhere('description', 'like', '%' . $search . '%');
                    });
                }

                if ($request->has('categories')) {
                    $catNames = (array)$request->input('categories');
                    $query->whereHas('category', function($q) use ($catNames) {
                        $q->where(function($subQ) use ($catNames) {
                            foreach ($catNames as $cat) {
                                $subQ->orWhere('category_name', 'like', '%' . $cat . '%')
                                     ->orWhere('slug', 'like', '%' . $cat . '%');
                            }
                        });
                    });
                }
                if ($request->filled('lokasi')) {
                    $query->where('city', 'like', '%' . $request->input('lokasi') . '%');
                }
                if ($request->filled('volume_min')) {
                    $query->where('quantity', '>=', max(0, (float)$request->input('volume_min')));
                }
                if ($request->filled('harga_min')) {
                    $query->where('price_per_unit', '>=', max(0, (float)$request->input('harga_min')));
                }
                if ($request->filled('harga_max')) {
                    $query->where('price_per_unit', '<=', max(0, (float)$request->input('harga_max')));
                }
                
                $sort = $request->input('sort', 'terbaru');
                if ($sort === 'harga-asc') $query->orderBy('price_per_unit', 'asc');
                elseif ($sort === 'harga-desc') $query->orderBy('price_per_unit', 'desc');
                elseif ($sort === 'stok-desc') $query->orderBy('quantity', 'desc');
                elseif ($sort === 'jarak-asc') $query->orderBy('city', 'asc');
                else $query->latest();
                
                $paginator = $query->paginate(18);
                $items = collect($paginator->items())->map(function($l) {
                    return [
                        'id' => $l->id,
                        'title' => $l->title,
                        'categoryLabel' => $l->category_short_name,
                        'city' => $l->city,
                        'price' => (float)$l->price_per_unit,
                        'unit' => $l->unit,
                        'stock' => (float)$l->quantity,
                        'sellerName' => $l->seller && $l->seller->sellerProfile ? $l->seller->sellerProfile->business_name : ($l->seller->name ?? 'Penjual'),
                        'image' => $l->primary_image_url,
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

        $categories = WasteCategory::getActiveCached();
        return view('pages.MarketplaceLimbah.index', compact('categories'));
    }

    // Marketplace detail – use route model binding
    public function show(WasteListing $wasteListing)
    {
        // Ensure only approved and active listings are visible
        if ($wasteListing->verification_status !== WasteListing::VERIFICATION_APPROVED || $wasteListing->availability_status === WasteListing::AVAILABILITY_INACTIVE) {
            abort(404, 'Listing not found, not approved, or inactive.');
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

