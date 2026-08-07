@extends('layouts.master')
@section('title', $listing->title . ' – Recyclink')

@php
    $isFav = auth()->check() && auth()->user()->isBuyer()
        ? auth()->user()->favoriteListings()->where('listing_id', $listing->id)->exists()
        : false;
    $isAvailable = $listing->availability_status === 'available';
    $sellerPhone = $listing->seller->phone_number ?? null;
    $sellerAddress = $listing->seller->sellerProfile->address ?? null;
    $sellerCity = $listing->city ?? ($listing->seller->sellerProfile->city ?? '-');
    $sellerName = $listing->seller->sellerProfile->business_name ?? $listing->seller->name;
    $ref = request('ref', 'marketplace');
    $backLabel = $ref === 'store' ? 'Kembali ke Toko' : 'Kembali ke Marketplace';
    $backFallbackUrl = $ref === 'store' ? route('marketplace.store', $listing->seller->id) : route('marketplace.index');
@endphp

@section('content')
<div class="bg-gray-50/60 min-h-screen py-6 sm:py-10">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    {{-- Back Link & Breadcrumb Navigation --}}
    <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
      <a href="{{ $backFallbackUrl }}" id="btn-back" class="inline-flex items-center text-xs sm:text-sm font-bold text-gray-600 hover:text-brand transition-colors group">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform"></i>
        <span>{{ $backLabel }}</span>
      </a>

      <nav class="flex items-center gap-2 text-xs text-gray-400 font-medium" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-brand transition-colors">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-300"></i>
        <a href="{{ route('marketplace.index') }}" class="hover:text-brand transition-colors">Marketplace</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-300"></i>
        <span class="text-gray-700 font-bold truncate max-w-[200px] sm:max-w-[300px]">{{ $listing->title }}</span>
      </nav>
    </div>

    {{-- Skeleton Loader --}}
    <div id="skeleton-loader" class="animate-pulse">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-4 aspect-square bg-gray-200 rounded-2xl"></div>
            <div class="lg:col-span-5 space-y-4">
                <div class="w-2/3 h-8 bg-gray-200 rounded-xl"></div>
                <div class="w-1/3 h-10 bg-gray-200 rounded-xl"></div>
                <div class="w-full h-40 bg-gray-200 rounded-2xl"></div>
                <div class="w-full h-32 bg-gray-200 rounded-2xl"></div>
            </div>
            <div class="lg:col-span-3 h-80 bg-gray-200 rounded-2xl"></div>
        </div>
    </div>

    {{-- Main Product Layout (Tokopedia Style 3-Column Architecture) --}}
    <div id="main-content" class="hidden opacity-0 transition-opacity duration-500">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- ========================================================================= --}}
        {{-- COLUMN 1: STICKY PRODUCT GALLERY (Col 4) --}}
        {{-- ========================================================================= --}}
        <div class="lg:col-span-4 lg:sticky lg:top-24">
          <div class="bg-white border border-gray-200/80 rounded-2xl p-4 shadow-xs">
            
            {{-- Main Image Frame --}}
            <div class="aspect-square w-full rounded-xl overflow-hidden bg-gray-100 relative border border-gray-100 mb-3 group">
              <img src="{{ $listing->primaryImage ? (str_starts_with($listing->primaryImage->image_url, 'http') ? $listing->primaryImage->image_url : asset('storage/'.$listing->primaryImage->image_url)) : 'https://placehold.co/500x500?text=Limbah' }}" 
                   alt="{{ $listing->title }}"
                   class="w-full h-full object-cover object-center transition-all duration-300 group-hover:scale-105" 
                   id="mainImage">
              
              {{-- Category Pill Overlay --}}
              <span class="absolute top-3 left-3 bg-brand/90 backdrop-blur-md text-white text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-lg shadow-sm">
                {{ $listing->category_short_name }}
              </span>
            </div>

            {{-- Thumbnail Gallery Selector --}}
            @if($listing->images && $listing->images->count() > 1)
              <div class="flex gap-2.5 overflow-x-auto pb-1 hide-scrollbar">
                @foreach($listing->images as $image)
                  @php
                    $imgSrc = str_starts_with($image->image_url, 'http') ? $image->image_url : asset('storage/'.$image->image_url);
                  @endphp
                  <button type="button" 
                          class="relative w-16 h-16 rounded-lg overflow-hidden bg-gray-100 shrink-0 border-2 transition-all cursor-pointer gallery-thumbnail {{ $loop->first ? 'border-brand opacity-100 ring-2 ring-brand/20' : 'border-gray-200/80 opacity-60 hover:opacity-100' }}" 
                          onclick="changeMainImage('{{ $imgSrc }}', this)">
                    <img src="{{ $imgSrc }}" class="w-full h-full object-cover">
                  </button>
                @endforeach
              </div>
            @endif

          </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- COLUMN 2: PRODUCT DETAILS & SPECIFICATIONS (Col 5) --}}
        {{-- ========================================================================= --}}
        <div class="lg:col-span-5 flex flex-col space-y-6">
          
          {{-- Title & Stats Bar --}}
          <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-xs">
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-snug mb-3">
              {{ $listing->title }}
            </h1>

            <div class="flex items-center gap-3 text-xs text-gray-500 font-medium pb-4 border-b border-gray-100">
              <span class="flex items-center gap-1">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400"></i>
                <span class="font-bold text-gray-700">{{ $listing->city }}</span>
              </span>
              <span>&bull;</span>
              <span class="flex items-center gap-1 text-amber-500 font-bold">
                <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                5.0 (Terverifikasi)
              </span>
            </div>

            {{-- Price Display --}}
            <div class="pt-4 flex items-baseline gap-2">
              <span class="text-3xl sm:text-4xl font-extrabold text-brand tracking-tight">
                Rp {{ number_format((float)($listing->price_per_unit ?? 0), 0, ',', '.') }}
              </span>
              <span class="text-sm font-bold text-gray-400">/ {{ $listing->unit }}</span>
            </div>
          </div>

          {{-- Specifications Grid --}}
          <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-xs">
            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
              <i data-lucide="sliders" class="w-4 h-4 text-brand"></i>
              Spesifikasi Limbah
            </h3>

            <div class="grid grid-cols-2 gap-4 text-xs sm:text-sm">
              <div class="bg-gray-50/80 p-3.5 rounded-xl border border-gray-100">
                <span class="text-gray-400 font-semibold block mb-0.5">Kategori</span>
                <span class="font-bold text-gray-900">{{ $listing->category_short_name }}</span>
              </div>

              <div class="bg-gray-50/80 p-3.5 rounded-xl border border-gray-100">
                <span class="text-gray-400 font-semibold block mb-0.5">Stok / Volume</span>
                <span class="font-bold text-gray-900">{{ number_format((float)($listing->quantity ?? 0), 0, ',', '.') }} {{ $listing->unit }}</span>
              </div>

              <div class="bg-gray-50/80 p-3.5 rounded-xl border border-gray-100">
                <span class="text-gray-400 font-semibold block mb-0.5">Min. Pembelian</span>
                <span class="font-bold text-gray-900">{{ $listing->min_order ?? '1' }} {{ $listing->unit }}</span>
              </div>

              <div class="bg-gray-50/80 p-3.5 rounded-xl border border-gray-100">
                <span class="text-gray-400 font-semibold block mb-0.5">Status Produk</span>
                @if($isAvailable)
                  <span class="inline-flex items-center gap-1 font-bold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-md">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Tersedia
                  </span>
                @else
                  <span class="inline-flex items-center gap-1 font-bold text-gray-500 bg-gray-200 px-2 py-0.5 rounded-md">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Nonaktif / Habis
                  </span>
                @endif
              </div>

              <div class="col-span-2 bg-gray-50/80 p-3.5 rounded-xl border border-gray-100">
                <span class="text-gray-400 font-semibold block mb-0.5">Alamat / Lokasi Pengambilan</span>
                <span class="font-bold text-gray-900">{{ $sellerAddress ? $sellerAddress.', '.$sellerCity : $sellerCity }}</span>
              </div>
            </div>
          </div>

          {{-- Description --}}
          <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-xs">
            <h3 class="text-base font-bold text-gray-900 mb-3 flex items-center gap-2">
              <i data-lucide="file-text" class="w-4 h-4 text-brand"></i>
              Deskripsi Limbah
            </h3>
            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap font-normal">{{ $listing->description ?: 'Tidak ada deskripsi rincian dari penjual.' }}</p>
          </div>

          {{-- Seller Profile Card --}}
          <div class="bg-white border border-gray-200/80 rounded-2xl p-5 shadow-xs flex items-center justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
              @if($listing->seller->avatar)
                  <img src="{{ asset('storage/'.$listing->seller->avatar) }}" alt="Toko" class="w-12 h-12 rounded-full object-cover border border-gray-200 shrink-0">
              @else
                  <div class="w-12 h-12 bg-brand text-white flex items-center justify-center text-base font-bold rounded-full shrink-0 shadow-xs">
                      {{ strtoupper(substr($sellerName, 0, 2)) }}
                  </div>
              @endif
              <div class="min-w-0">
                <h4 class="text-sm font-extrabold text-gray-900 truncate">{{ $sellerName }}</h4>
                <p class="text-xs text-gray-400 flex items-center gap-1 mt-0.5 truncate">
                  <i data-lucide="map-pin" class="w-3 h-3 shrink-0"></i> {{ $sellerCity }}
                </p>
              </div>
            </div>

            <a href="{{ route('marketplace.store', $listing->seller->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-50 border border-gray-200 text-gray-700 font-bold text-xs rounded-xl hover:border-brand hover:text-brand hover:bg-brand/5 transition-all shrink-0">
              <i data-lucide="store" class="w-3.5 h-3.5"></i>
              <span>Kunjungi Toko</span>
            </a>
          </div>

          {{-- Escrow Security Note --}}
          <div class="bg-emerald-50/60 border border-emerald-200/60 rounded-2xl p-4 flex items-start gap-3 text-xs text-emerald-900">
            <i data-lucide="shield-check" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
            <div>
              <span class="font-bold block text-emerald-950 mb-0.5">Jaminan Pembayaran Escrow Recyclink</span>
              <span>Dana Anda disimpan aman oleh rekening bersama Recyclink sampai limbah diterima & diverifikasi sesuai kriteria.</span>
            </div>
          </div>

        </div>

        {{-- ========================================================================= --}}
        {{-- COLUMN 3: STICKY CHECKOUT / PURCHASE SIDEBAR CARD (Col 3) --}}
        {{-- ========================================================================= --}}
        <div class="lg:col-span-3 lg:sticky lg:top-24">
          <div class="bg-white border border-gray-200/80 rounded-2xl p-5 shadow-lg shadow-gray-200/50 space-y-4">
            
            <h4 class="font-extrabold text-gray-900 text-sm pb-3 border-b border-gray-100">
              Atur Jumlah Pesanan
            </h4>

            {{-- Mini Product Preview --}}
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden shrink-0 border border-gray-100">
                <img src="{{ $listing->primaryImage ? (str_starts_with($listing->primaryImage->image_url, 'http') ? $listing->primaryImage->image_url : asset('storage/'.$listing->primaryImage->image_url)) : '' }}" class="w-full h-full object-cover">
              </div>
              <p class="text-xs font-bold text-gray-800 line-clamp-2 leading-snug">{{ $listing->title }}</p>
            </div>

            {{-- Quantity Input Counter --}}
            <div>
              <div class="flex items-center justify-between text-xs mb-2">
                <span class="font-semibold text-gray-500">Jumlah ({{ $listing->unit }})</span>
                <span class="text-gray-400 font-medium">Stok: {{ number_format((float)($listing->quantity ?? 0), 0, ',', '.') }}</span>
              </div>
              
              <div class="flex items-center w-full h-11 border border-gray-200 rounded-xl overflow-hidden bg-white shadow-xs focus-within:border-brand">
                <button type="button" id="btn-minus" class="w-10 h-full flex items-center justify-center bg-gray-50 text-gray-600 hover:bg-gray-100 text-lg font-bold transition-colors border-r border-gray-200 cursor-pointer">-</button>
                <input type="number" id="quantity" name="quantity" min="{{ intval($listing->min_order ?? 1) }}" value="{{ intval($listing->min_order ?? 1) }}"
                       class="w-full h-full text-center border-0 focus:ring-0 text-sm font-extrabold text-gray-900 bg-white p-0" style="-moz-appearance: textfield;">
                <button type="button" id="btn-plus" class="w-10 h-full flex items-center justify-center bg-gray-50 text-gray-600 hover:bg-gray-100 text-lg font-bold transition-colors border-l border-gray-200 cursor-pointer">+</button>
              </div>
              <p class="text-[11px] text-gray-400 mt-1">Minimal pembelian {{ $listing->min_order ?? '1' }} {{ $listing->unit }}</p>
            </div>

            {{-- Subtotal Display --}}
            <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
              <span class="text-xs font-bold text-gray-500">Subtotal</span>
              <span class="text-lg font-extrabold text-gray-900" id="total-price">Rp 0</span>
            </div>

            {{-- Primary Action Buttons --}}
            <div class="space-y-2.5 pt-2">
              @auth
                @if(auth()->user()->isBuyer())
                  {{-- Add to Cart Button --}}
                  <form method="POST" action="{{ route('buyer.cart.store', $listing->id) }}" id="cart-form">
                    @csrf
                    <input type="hidden" name="quantity" id="cart-quantity" value="{{ intval($listing->min_order ?? 1) }}">
                    <button type="submit" class="w-full h-11 bg-brand text-white font-bold text-sm rounded-xl hover:bg-brand-hover transition-all flex items-center justify-center gap-2 shadow-md shadow-brand/15 cursor-pointer">
                      <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                      <span>+ Keranjang</span>
                    </button>
                  </form>
                  
                  {{-- Buy Directly Button (Triggers Modal) --}}
                  <button type="button" id="btn-order" class="w-full h-11 bg-white border-2 border-brand text-brand font-bold text-sm rounded-xl hover:bg-brand/5 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Beli Langsung</span>
                  </button>

                  {{-- Secondary Action Row --}}
                  <div class="flex items-center gap-2 pt-1">
                    {{-- Chat Seller --}}
                    <form method="POST" action="{{ route('conversations.start', $listing->id) }}" class="flex-1">
                      @csrf
                      <input type="hidden" name="message" value="Halo, saya tertarik dengan listing '{{ $listing->title }}'">
                      <button type="submit" class="w-full h-10 bg-gray-50 border border-gray-200 text-gray-700 font-bold text-xs rounded-xl hover:border-brand hover:text-brand hover:bg-white transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                        <span>Chat</span>
                      </button>
                    </form>

                    {{-- Wishlist / Favorite --}}
                    @if($isFav)
                    <form method="POST" action="{{ route('buyer.favorites.destroy', $listing->id) }}">
                      @csrf @method('DELETE')
                      <button type="submit" class="h-10 w-10 bg-rose-50 border border-rose-200 text-rose-500 flex items-center justify-center rounded-xl hover:bg-rose-100 transition-all cursor-pointer" title="Hapus dari Favorit">
                        <i data-lucide="heart" class="w-4 h-4 fill-current"></i>
                      </button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('buyer.favorites.store', $listing->id) }}">
                      @csrf
                      <button type="submit" class="h-10 w-10 bg-gray-50 border border-gray-200 text-gray-400 flex items-center justify-center rounded-xl hover:bg-rose-50 hover:border-rose-200 hover:text-rose-500 transition-all cursor-pointer" title="Simpan ke Favorit">
                        <i data-lucide="heart" class="w-4 h-4"></i>
                      </button>
                    </form>
                    @endif
                  </div>

                @else
                  <div class="p-3 bg-gray-50 text-gray-500 rounded-xl text-xs font-semibold text-center border border-gray-100">
                    Masuk sebagai Pembeli untuk memesan produk ini
                  </div>
                @endif
              @else
                <button type="button" onclick="showToast('Silakan masuk terlebih dahulu untuk memesan.')" class="w-full h-11 bg-brand text-white font-bold text-sm rounded-xl hover:bg-brand-hover transition-all flex items-center justify-center gap-2 shadow-md cursor-pointer">
                  <i data-lucide="log-in" class="w-4 h-4"></i>
                  <span>Masuk untuk Memesan</span>
                </button>
              @endauth
            </div>

          </div>
        </div>

      </div>
    </div> {{-- End Main Content --}}

  </div>
</div>

{{-- Modal Orders --}}
@auth
@if(auth()->user()->isBuyer())
<div id="order-modal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="modal-backdrop"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md z-10 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <h3 class="text-base font-extrabold text-gray-900">Konfirmasi Pesanan</h3>
            <button id="modal-close" class="text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('buyer.orders.store', $listing->id) }}" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Jumlah Pesanan ({{ $listing->unit }})</label>
                <div class="flex items-center h-11 border border-gray-200 rounded-xl overflow-hidden">
                    <button type="button" id="modal-minus" class="w-11 h-full flex items-center justify-center bg-gray-50 border-r border-gray-200 text-gray-500 hover:bg-gray-100 text-xl font-bold transition-colors cursor-pointer">-</button>
                    <input type="number" name="quantity" id="modal-qty" min="{{ intval($listing->min_order ?? 1) }}" value="{{ intval($listing->min_order ?? 1) }}" class="flex-1 h-full text-center border-0 focus:ring-0 text-base font-extrabold text-gray-900" style="-moz-appearance:textfield;">
                    <button type="button" id="modal-plus" class="w-11 h-full flex items-center justify-center bg-gray-50 border-l border-gray-200 text-gray-500 hover:bg-gray-100 text-xl font-bold transition-colors cursor-pointer">+</button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Metode Pengambilan</label>
                <input type="hidden" name="pickup_method" value="self_pickup">
                <div class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-xs bg-gray-50 text-gray-700 font-bold flex items-center justify-between">
                    Ambil Sendiri (Self Pickup / COD)
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Ambil</label>
                    <input type="date" name="pickup_date" min="{{ date('Y-m-d') }}" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Jam Ambil</label>
                    <input type="time" name="pickup_time" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand font-medium">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Catatan Pesanan (opsional)</label>
                <textarea name="buyer_note" rows="2" class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand resize-none" placeholder="Instruksi spesifik pengangkutan..."></textarea>
            </div>

            <div class="bg-brand/5 border border-brand/10 rounded-xl px-4 py-3 flex items-center justify-between">
                <span class="text-xs text-gray-600 font-bold">Total Estimasi</span>
                <span class="text-base font-extrabold text-brand" id="modal-total">Rp 0</span>
            </div>

            <button type="submit" class="w-full h-11 bg-brand hover:bg-brand-hover text-white font-bold text-sm rounded-xl transition-colors shadow-md shadow-brand/15 cursor-pointer">
                Konfirmasi & Buat Pesanan
            </button>
        </form>
    </div>
</div>
@endif
@endauth

<style>
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

@push('scripts')
<script>
(function() {
    // 1. Back button logic
    const backBtn = document.getElementById('btn-back');
    if (backBtn) {
        backBtn.addEventListener('click', function(e) {
            if (window.history.length > 1) {
                e.preventDefault();
                window.history.back();
            }
        });
    }

    // Helper: Format Rupiah
    const pricePerUnit = {{ $listing->price_per_unit }};
    const formatRupiah = n => new Intl.NumberFormat('id-ID').format(n);

    // 2. Quantity Counter helper
    function setupQuantityCounter(inputId, minusId, plusId, onUpdate) {
        const input = document.getElementById(inputId);
        const minus = document.getElementById(minusId);
        const plus = document.getElementById(plusId);
        if (!input) return;

        const update = () => {
            let val = parseInt(input.value);
            if (isNaN(val) || val < 1) val = 1;
            onUpdate(val);
        };

        minus?.addEventListener('click', () => {
            const min = parseInt(input.min) || 1;
            if (parseInt(input.value) > min) {
                input.value = parseInt(input.value) - 1;
                update();
            }
        });
        plus?.addEventListener('click', () => {
            input.value = parseInt(input.value) + 1;
            update();
        });
        input.addEventListener('input', update);
        update();
    }

    // Main page counter initialization
    const totalPriceEl = document.getElementById('total-price');
    setupQuantityCounter('quantity', 'btn-minus', 'btn-plus', (qty) => {
        if (totalPriceEl) totalPriceEl.textContent = 'Rp ' + formatRupiah(qty * pricePerUnit);
        const cartQty = document.getElementById('cart-quantity');
        if (cartQty) cartQty.value = qty;
    });

    // 3. Modal Order dialog handling
    const modalEl    = document.getElementById('order-modal');
    const btnOrder   = document.getElementById('btn-order');
    const modalClose = document.getElementById('modal-close');
    const modalBd    = document.getElementById('modal-backdrop');
    const modalQty   = document.getElementById('modal-qty');
    const modalTotal = document.getElementById('modal-total');

    if (modalEl && btnOrder) {
        btnOrder.addEventListener('click', () => {
            modalEl.classList.remove('hidden');
            const mainQty = document.getElementById('quantity');
            if (mainQty && modalQty) {
                modalQty.value = mainQty.value;
                modalQty.dispatchEvent(new Event('input'));
            }
        });
        modalClose?.addEventListener('click', () => modalEl.classList.add('hidden'));
        modalBd?.addEventListener('click', () => modalEl.classList.add('hidden'));

        setupQuantityCounter('modal-qty', 'modal-minus', 'modal-plus', (qty) => {
            if (modalTotal) modalTotal.textContent = 'Rp ' + formatRupiah(qty * pricePerUnit);
        });
    }

    // 4. Image gallery switcher function
    window.changeMainImage = function(src, btn) {
        const mainImg = document.getElementById('mainImage');
        if(!mainImg || mainImg.src === src) return;
        
        mainImg.style.opacity = 0;
        setTimeout(() => {
            mainImg.src = src;
            mainImg.style.opacity = 1;
        }, 150);
        
        document.querySelectorAll('.gallery-thumbnail').forEach(el => {
            el.classList.remove('border-brand', 'opacity-100', 'ring-2', 'ring-brand/20');
            el.classList.add('border-gray-200/80', 'opacity-60');
        });
        
        btn.classList.remove('border-gray-200/80', 'opacity-60');
        btn.classList.add('border-brand', 'opacity-100', 'ring-2', 'ring-brand/20');
    };

    // 5. Skeleton Loader handling
    const skeleton = document.getElementById('skeleton-loader');
    const mainContent = document.getElementById('main-content');
    const mainImg = document.getElementById('mainImage');
    
    function showContent() {
        skeleton?.classList.add('hidden');
        mainContent?.classList.remove('hidden', 'opacity-0');
    }

    if (mainImg && !mainImg.complete) {
        mainImg.addEventListener('load', showContent, { once: true });
        mainImg.addEventListener('error', showContent, { once: true });
    } else {
        showContent();
    }

    // 6. Loading state handler for forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                if (btn.disabled) {
                    e.preventDefault();
                    return;
                }
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-wait');
            }
        });
    });
})();
</script>
@endpush
@endsection