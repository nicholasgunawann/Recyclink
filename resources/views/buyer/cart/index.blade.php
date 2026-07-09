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
                    <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-brand focus:ring-brand cursor-pointer" checked>
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
                <div class="p-4 border-b border-gray-100 last:border-0">
                    <!-- Store name header -->
                    <div class="flex items-center gap-3 mb-4">
                        <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-brand focus:ring-brand cursor-pointer" checked>
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="badge-check" class="w-4 h-4 text-purple-600 fill-purple-100"></i>
                            <span class="text-base font-bold text-gray-900">{{ $listing->seller->name ?? 'Toko' }}</span>
                        </div>
                    </div>
                    
                    <!-- Product body -->
                    <div class="flex items-start gap-4">
                        <div class="pt-6 shrink-0">
                            <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-brand focus:ring-brand cursor-pointer" checked>
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
                                <div class="text-lg font-extrabold text-gray-900 shrink-0">Rp{{ number_format($listing->price_per_unit, 0, ',', '.') }}</div>
                            </div>
                            
                            <div class="flex justify-end items-center gap-5 mt-4">
                                <!-- Favorite icon -->
                                <button class="text-gray-400 hover:text-rose-500 transition-colors">
                                    <i data-lucide="heart" class="w-5 h-5"></i>
                                </button>
                                
                                <!-- Delete from cart -->
                                <form method="POST" action="{{ route('buyer.cart.destroy', $listing->id) }}" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-gray-600 transition-colors flex items-center justify-center mt-1" title="Hapus dari keranjang">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </form>
                                
                                <!-- Quantity Control -->
                                <div class="flex items-center border border-gray-300 rounded-full px-2 py-1 ml-1 h-9">
                                    <button class="text-gray-400 hover:text-brand w-6 h-6 flex items-center justify-center cursor-pointer">
                                        <i data-lucide="minus" class="w-4 h-4"></i>
                                    </button>
                                    <span class="text-sm font-semibold text-gray-700 w-8 text-center">{{ $qty }}</span>
                                    <button class="text-gray-400 hover:text-brand w-6 h-6 flex items-center justify-center cursor-pointer">
                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                    </button>
                                </div>
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
                    <span class="text-lg font-bold text-gray-900">Rp{{ number_format($totalPrice ?? 0, 0, ',', '.') }}</span>
                </div>
                
                <form action="{{ route('buyer.cart.checkout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-brand text-white font-bold rounded-xl hover:bg-brand-hover transition-colors shadow-sm">
                        Beli ({{ $totalQty ?? 0 }})
                    </button>
                </form>
            </div>
        </div>
        
    </div>
    @endif
</div>
@endsection
