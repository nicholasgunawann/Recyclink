<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class BuyerCartController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'role:buyer',
        ];
    }

    public function index()
    {
        $cartData = session()->get('cart', []);
        $cartIds = array_keys($cartData);
        
        // Fetch listings based on session IDs with full eager loading
        $cartItemsRaw = \App\Models\WasteListing::with(['seller.sellerProfile', 'category', 'primaryImage'])
                            ->whereIn('id', $cartIds)
                            ->get();
        
        $favoriteListingIds = auth()->check()
            ? \Illuminate\Support\Facades\Cache::remember("buyer_fav_ids_" . auth()->id(), 300, function () {
                return auth()->user()->favoriteListings()->pluck('listing_id')->toArray();
            })
            : [];

        $cartItems = $cartItemsRaw->map(function ($listing) use ($cartData) {
            return (object) [
                'listing' => $listing,
                'quantity' => $cartData[$listing->id]['quantity'] ?? 1
            ];
        });

        // We wrap it in a LengthAwarePaginator just to make the links() method not fail in the view
        $cartItems = new \Illuminate\Pagination\LengthAwarePaginator($cartItems, $cartItems->count(), 15, 1);

        return view('buyer.cart.index', compact('cartItems', 'favoriteListingIds'));
    }

    public function store(Request $request, \App\Models\WasteListing $wasteListing)
    {
        $cart = session()->get('cart', []);
        $quantity = max(1, (int) $request->input('quantity', 1));
        
        if (isset($cart[$wasteListing->id])) {
            $cart[$wasteListing->id]['quantity'] += $quantity;
        } else {
            $cart[$wasteListing->id] = ['quantity' => $quantity];
        }
        
        session()->put('cart', $cart);
        
        return redirect()->back()->with('success', 'Barang berhasil dimasukkan ke keranjang.');
    }

    public function update(Request $request, \App\Models\WasteListing $wasteListing)
    {
        $cart = session()->get('cart', []);
        $quantity = max(1, (int) $request->input('quantity', 1));
        
        if (isset($cart[$wasteListing->id])) {
            $cart[$wasteListing->id]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }
        
        return redirect()->back();
    }
    
    public function destroy(\App\Models\WasteListing $wasteListing)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$wasteListing->id])) {
            unset($cart[$wasteListing->id]);
            session()->put('cart', $cart);
        }
        
        return redirect()->back()->with('success', 'Barang berhasil dihapus dari keranjang.');
    }

    public function destroyMultiple(Request $request)
    {
        $cart = session()->get('cart', []);
        $selectedIds = $request->input('selected_items', []);

        if (empty($selectedIds)) {
            session()->forget('cart');
            return redirect()->back()->with('success', 'Semua barang berhasil dihapus dari keranjang.');
        }

        foreach ($selectedIds as $id) {
            unset($cart[$id]);
        }
        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Barang terpilih berhasil dihapus dari keranjang.');
    }

    public function checkout(Request $request)
    {
        $cartData = session()->get('cart', []);
        $selectedIds = $request->input('selected_items', []);
        
        if (empty($cartData)) {
            return redirect()->back()->with('error', 'Keranjang belanja Anda kosong.');
        }
        
        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Pilih setidaknya satu barang untuk dibeli.');
        }

        $orderService = app(\App\Services\OrderService::class);
        $orders = [];
        $lastError = null;

        foreach ($selectedIds as $listingId) {
            if (!isset($cartData[$listingId])) continue;
            
            $item = $cartData[$listingId];
            $listing = \App\Models\WasteListing::find($listingId);
            if ($listing) {
                try {
                    $order = $orderService->createOrder(auth()->user(), $listing, [
                        'quantity' => $item['quantity'],
                        'pickup_method' => $request->input('pickup_method', 'self_pickup'), 
                        'pickup_date' => $request->input('pickup_date'),
                        'pickup_time' => $request->input('pickup_time'),
                        'pickup_address' => $request->input('pickup_address'),
                        'buyer_note' => $request->input('buyer_note'),
                    ]);
                    $orders[] = $order;
                    // Remove processed item from cart
                    unset($cartData[$listingId]);
                } catch (\Exception $e) {
                    $lastError = $e->getMessage();
                }
            }
        }

        // Update cart with remaining items
        session()->put('cart', $cartData);

        if (count($orders) === 1) {
            return redirect()->route('buyer.orders.payment.create', $orders[0]->id)->with('success', 'Pesanan berhasil dibuat! Silakan lanjutkan pembayaran.');
        } elseif (count($orders) > 1) {
            return redirect()->route('buyer.orders.index')->with('success', 'Pesanan berhasil dibuat untuk semua item! Silakan lakukan pembayaran satu per satu.');
        } else {
            return redirect()->back()->with('error', $lastError ?? 'Gagal membuat pesanan. Stok mungkin habis atau listing tidak tersedia.');
        }
    }
}
