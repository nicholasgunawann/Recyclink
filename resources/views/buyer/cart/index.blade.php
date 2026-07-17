@extends('buyer.layouts.buyer')
@section('title', 'Keranjang - Recyclink')
@section('header_title', 'Keranjang')

@section('content')
<div class="p-6 lg:p-8">

    {{-- Flash Messages --}}
    

    {{-- Navigation Back --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- Using url()->previous() for back navigation as requested -->
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('marketplace.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-brand hover:border-brand shadow-sm transition-colors" title="Kembali">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-900">Keranjang</h2>
        </div>
    </div>

    @if($cartItems->isEmpty())
    <div class="bg-white border border-gray-200 border-dashed rounded-2xl py-20 flex flex-col items-center justify-center text-center mt-4">
        <div class="w-16 h-16 bg-brand/10 rounded-2xl flex items-center justify-center mb-4">
            <i data-lucide="shopping-cart" class="w-8 h-8 text-brand"></i>
        </div>
        <h3 class="text-base font-bold text-gray-700 mb-1">Keranjang Masih Kosong</h3>
        <p class="text-sm text-gray-400 max-w-xs">Ayo cari limbah yang Anda butuhkan di marketplace!</p>
        <a href="{{ route('marketplace.index') }}" class="mt-5 px-5 py-2.5 bg-brand text-white text-sm font-bold rounded-xl hover:bg-brand-hover transition-colors">
            Mulai Belanja
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-4">
        
        <!-- Left Column: Cart Items -->
        <div class="lg:col-span-8 flex flex-col gap-0">
            <!-- Header Card (Pilih Semua) -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center justify-between mb-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-brand focus:ring-brand cursor-pointer select-all" checked>
                    <span class="text-base font-bold text-gray-900">Pilih Semua <span class="font-normal text-gray-500">({{ $cartItems->count() }})</span></span>
                </div>
                <button class="text-base font-bold text-brand hover:text-brand-hover">Hapus</button>
            </div>

            <!-- Store/Products Card -->
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-4 shadow-sm">
                @php $totalPrice = 0; $totalQty = 0; @endphp
                @foreach($cartItems as $item)
                @php 
                    $listing = $item->listing; 
                    $qty = $item->quantity ?? 1;
                    if($listing) {
                        $totalPrice += ($listing->price_per_unit * $qty);
                        $totalQty += $qty;
                    }
                @endphp
                @if($listing)
                <div class="p-4 border-b border-gray-100 last:border-0 store-block">
                    <!-- Store name header -->
                    <div class="flex items-center gap-3 mb-4">
                        <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-brand focus:ring-brand cursor-pointer store-checkbox" checked>
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="store" class="w-4 h-4 text-gray-500"></i>
                            <span class="text-base font-bold text-gray-900">{{ $listing->seller->sellerProfile->business_name ?? $listing->seller->name ?? 'Toko' }}</span>
                        </div>
                    </div>
                    
                    <!-- Product body -->
                    <div class="flex items-start gap-4">
                        <div class="pt-6 shrink-0">
                            <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-brand focus:ring-brand cursor-pointer item-checkbox" value="{{ $listing->id }}" data-price="{{ $listing->price_per_unit }}" data-qty="{{ $qty }}" checked>
                        </div>
                        
                        <a href="{{ route('marketplace.show', $listing->id) }}" class="w-20 h-20 shrink-0 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 block">
                            <img src="{{ $listing->primaryImage ? (str_starts_with($listing->primaryImage->image_url, 'http') ? $listing->primaryImage->image_url : asset('storage/'.$listing->primaryImage->image_url)) : '' }}"
                                 alt="{{ $listing->title }}"
                                 class="w-full h-full object-cover"
                                 onerror="this.style.display='none'">
                        </a>
                        
                        <div class="flex-1 min-w-0 flex flex-col justify-between py-1">
                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                <div>
                                    <a href="{{ route('marketplace.show', $listing->id) }}" class="text-base text-gray-700 hover:text-brand line-clamp-2 leading-relaxed max-w-xl">{{ $listing->title }}</a>
                                    <p class="text-sm text-gray-500 mt-2">{{ $listing->category->category_name ?? '' }}</p>
                                </div>
                                <div class="text-lg font-extrabold text-gray-900 shrink-0">Rp{{ number_format((float)($listing->price_per_unit ?? 0), 0, ',', '.') }}</div>
                            </div>
                            
                            <div class="flex justify-end items-center gap-5 mt-4">
                                <!-- Favorite icon -->
                                @php
                                    $isFav = auth()->user()->favoriteListings()->where('listing_id', $listing->id)->exists();
                                @endphp
                                @if($isFav)
                                <form method="POST" action="{{ route('buyer.favorites.destroy', $listing->id) }}" class="m-0 relative top-0.5">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:text-rose-600 transition-colors mt-1" title="Hapus dari Favorit">
                                        <i data-lucide="heart" class="w-5 h-5 fill-rose-500"></i>
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('buyer.favorites.store', $listing->id) }}" class="m-0 relative top-0.5">
                                    @csrf
                                    <button type="submit" class="text-gray-400 hover:text-rose-500 transition-colors mt-1" title="Simpan ke Favorit">
                                        <i data-lucide="heart" class="w-5 h-5"></i>
                                    </button>
                                </form>
                                @endif
                                
                                <!-- Delete from cart -->
                                <form method="POST" action="{{ route('buyer.cart.destroy', $listing->id) }}" class="m-0 relative top-0.5">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-gray-600 transition-colors flex items-center justify-center mt-1" title="Hapus dari keranjang">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </form>
                                
                                <!-- Quantity Control -->
                                <form method="POST" action="{{ route('cart.update', $listing->id) }}" class="flex items-center m-0">
                                    @csrf @method('PUT')
                                    <div class="flex items-center border border-gray-300 rounded-full px-2 py-1 ml-1 h-9">
                                        <button type="submit" name="quantity" value="{{ max(1, $qty - 1) }}" class="text-gray-400 hover:text-brand w-6 h-6 flex items-center justify-center cursor-pointer" {{ $qty <= 1 ? 'disabled' : '' }}>
                                            <i data-lucide="minus" class="w-4 h-4"></i>
                                        </button>
                                        <span class="text-sm font-semibold text-gray-700 w-8 text-center">{{ $qty }}</span>
                                        <button type="submit" name="quantity" value="{{ $qty + 1 }}" class="text-gray-400 hover:text-brand w-6 h-6 flex items-center justify-center cursor-pointer">
                                            <i data-lucide="plus" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            
            {{-- Pagination (if any) --}}
            <div class="mt-4">
                {{ $cartItems->links() }}
            </div>
        </div>

        <!-- Right Column: Order Summary Card -->
        <div class="lg:col-span-4 relative">
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm sticky top-6">
                <h3 class="text-base font-bold text-gray-900 mb-4">Ringkasan belanja</h3>
                
                <div class="flex items-center justify-between mb-6">
                    <span class="text-gray-600 text-sm">Total</span>
                    <span class="text-lg font-bold text-gray-900" id="total-price">Rp{{ number_format($totalPrice ?? 0, 0, ',', '.') }}</span>
                </div>
                
                <form action="{{ route('cart.checkout') }}" method="POST" id="checkout-form">
                    @csrf
                    
                    {{-- Metode --}}
                    <div class="mb-4 text-left">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Metode Pengambilan</label>
                        <select name="pickup_method" id="cart-pickup-method" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand bg-white text-gray-700" onchange="toggleCartAddressField(this.value)">
                            <option value="self_pickup">Ambil Sendiri (Pickup)</option>
                            <option value="delivery">Kirim ke Lokasi Buyer</option>
                        </select>
                    </div>
                    
                    {{-- Alamat (Hidden by default) --}}
                    <div id="cart-address-container" class="hidden mb-4 text-left">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Pengiriman</label>
                        <textarea name="pickup_address" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand resize-none" placeholder="Masukkan alamat lengkap Anda..."></textarea>
                    </div>
                    
                    {{-- Jadwal --}}
                    <div class="grid grid-cols-2 gap-3 mb-4 text-left">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Ambil</label>
                            <input type="date" name="pickup_date" min="{{ date('Y-m-d') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jam Ambil</label>
                            <input type="time" name="pickup_time" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                        </div>
                    </div>
                    
                    {{-- Catatan --}}
                    <div class="mb-5 text-left">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan (opsional)</label>
                        <textarea name="buyer_note" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand resize-none" placeholder="Instruksi tambahan untuk penjual..."></textarea>
                    </div>

                    <button type="submit" id="total-qty" class="w-full py-3 bg-brand text-white font-bold rounded-xl hover:bg-brand-hover transition-colors shadow-sm">
                        Beli ({{ $totalQty ?? 0 }})
                    </button>
                </form>
            </div>
        </div>
        
    </div>
    @endif
</div>

@push('scripts')
<script>
window.toggleCartAddressField = function(val) {
    const el = document.getElementById('cart-address-container');
    if(val === 'delivery') {
        el.classList.remove('hidden');
    } else {
        el.classList.add('hidden');
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckboxes = document.querySelectorAll('.select-all');
    const storeCheckboxes = document.querySelectorAll('.store-checkbox');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const totalPriceEl = document.getElementById('total-price');
    const totalQtyEl = document.getElementById('total-qty');
    const checkoutForm = document.getElementById('checkout-form');

    function calculateTotal() {
        let total = 0;
        let qty = 0;
        itemCheckboxes.forEach(cb => {
            if (cb.checked) {
                total += (parseInt(cb.dataset.price) * parseInt(cb.dataset.qty));
                qty += parseInt(cb.dataset.qty);
            }
        });
        if(totalPriceEl) totalPriceEl.textContent = 'Rp' + total.toLocaleString('id-ID').replace(/,/g, '.');
        if(totalQtyEl) totalQtyEl.textContent = 'Beli (' + qty + ')';
    }

    selectAllCheckboxes.forEach(selectAll => {
        selectAll.addEventListener('change', function() {
            const isChecked = this.checked;
            itemCheckboxes.forEach(cb => cb.checked = isChecked);
            storeCheckboxes.forEach(cb => cb.checked = isChecked);
            calculateTotal();
        });
    });

    storeCheckboxes.forEach(storeCb => {
        storeCb.addEventListener('change', function() {
            const isChecked = this.checked;
            const storeBlock = this.closest('.store-block');
            if(storeBlock) {
                storeBlock.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = isChecked);
            }
            calculateTotal();
            
            if (!isChecked) {
                selectAllCheckboxes.forEach(cb => cb.checked = false);
            }
        });
    });

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            calculateTotal();
            if (!this.checked) {
                selectAllCheckboxes.forEach(cb => cb.checked = false);
                const storeBlock = this.closest('.store-block');
                if (storeBlock) {
                    const storeCb = storeBlock.querySelector('.store-checkbox');
                    if(storeCb) storeCb.checked = false;
                }
            }
        });
    });
    
    // Initial calc
    calculateTotal();

    // Checkout form sync
    if(checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            checkoutForm.querySelectorAll('input[name="selected_items[]"]').forEach(i => i.remove());
            
            let hasSelection = false;
            itemCheckboxes.forEach(cb => {
                if(cb.checked) {
                    hasSelection = true;
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected_items[]';
                    input.value = cb.value;
                    checkoutForm.appendChild(input);
                }
            });
            
            if(!hasSelection) {
                e.preventDefault();
                alert('Pilih setidaknya satu barang untuk dibeli');
            }
        });
    }
});
</script>
@endpush
@endsection
