@extends('layouts.master')
@section('title', $listing->title . ' – Recyclink')
@php
    $isFav = auth()->check() && auth()->user()->isBuyer()
        ? auth()->user()->favoriteListings()->where('listing_id', $listing->id)->exists()
        : false;
@endphp
@section('content')
<div class="bg-gray-50 min-h-screen py-8">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    {{-- Skeleton Loader --}}
    <div id="skeleton-loader" class="animate-pulse">
        <div class="w-24 h-4 bg-gray-200 rounded mb-8"></div>
        <div class="bg-white border border-gray-100 rounded-2xl flex flex-col lg:flex-row overflow-hidden mb-12">
            <div class="w-full lg:w-5/12 aspect-square bg-gray-200"></div>
            <div class="w-full lg:w-7/12 p-6 lg:p-8 space-y-4">
                <div class="w-24 h-4 bg-gray-200 rounded"></div>
                <div class="w-3/4 h-8 bg-gray-200 rounded"></div>
                <div class="w-1/3 h-8 bg-gray-200 rounded mb-6"></div>
                <div class="w-full h-32 bg-gray-100 rounded-xl mb-6"></div>
                <div class="w-full h-24 bg-gray-100 rounded-xl mb-8"></div>
                <div class="flex gap-4 mt-auto pt-6 border-t border-gray-100">
                    <div class="w-1/3 h-12 bg-gray-200 rounded-xl"></div>
                    <div class="w-1/3 h-12 bg-gray-200 rounded-xl"></div>
                    <div class="w-1/3 h-12 bg-gray-200 rounded-xl"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div id="main-content" class="hidden opacity-0 transition-opacity duration-500">
        <!-- Back Button -->
        <div class="mb-8">
          @php
              $ref = request('ref', 'marketplace');
              $backLabel = $ref === 'store' ? 'Kembali ke Toko' : 'Kembali ke Marketplace';
              $backFallbackUrl = $ref === 'store' ? route('marketplace.store', $listing->seller->id) : route('marketplace.index');
          @endphp
          <a href="{{ $backFallbackUrl }}" id="btn-back" class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7h18"/></svg>
            <span id="back-label">{{ $backLabel }}</span>
          </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-12">
      <div class="flex flex-col lg:flex-row">
        
        <!-- Product Images -->
        <div class="w-full lg:w-5/12 p-6 lg:p-8 border-b lg:border-b-0 lg:border-r border-gray-100">
          <div class="aspect-w-1 aspect-h-1 w-full rounded-xl overflow-hidden bg-gray-100 relative mb-4">
            <img src="{{ $listing->primaryImage ? (str_starts_with($listing->primaryImage->image_url, 'http') ? $listing->primaryImage->image_url : asset('storage/'.$listing->primaryImage->image_url)) : '' }}" alt="{{ $listing->title }}"
                 class="w-full h-full object-cover object-center transition-opacity duration-300" id="mainImage">
            <span class="absolute top-4 left-4 bg-brand text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full shadow-sm">{{ $listing->category->category_name ?? 'Limbah' }}</span>
          </div>

          @if($listing->images && $listing->images->count() > 1)
          <div class="flex gap-3 overflow-x-auto pb-2 hide-scrollbar">
            @foreach($listing->images as $image)
              @php
                $imgSrc = str_starts_with($image->image_url, 'http') ? $image->image_url : asset('storage/'.$image->image_url);
              @endphp
              <button type="button" class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 shrink-0 border-2 transition-all gallery-thumbnail {{ $loop->first ? 'border-brand opacity-100' : 'border-transparent opacity-60 hover:opacity-100' }}" onclick="changeMainImage('{{ $imgSrc }}', this)">
                <img src="{{ $imgSrc }}" class="w-full h-full object-cover object-center">
              </button>
            @endforeach
          </div>
          <script>
            function changeMainImage(src, btn) {
                const mainImg = document.getElementById('mainImage');
                if(mainImg.src === src) return;
                
                mainImg.style.opacity = 0;
                setTimeout(() => {
                    mainImg.src = src;
                    mainImg.style.opacity = 1;
                }, 150);
                
                document.querySelectorAll('.gallery-thumbnail').forEach(el => {
                    el.classList.remove('border-brand', 'opacity-100');
                    el.classList.add('border-transparent', 'opacity-60');
                });
                
                btn.classList.remove('border-transparent', 'opacity-60');
                btn.classList.add('border-brand', 'opacity-100');
            }
          </script>
          <style>
              .hide-scrollbar::-webkit-scrollbar { display: none; }
              .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
          </style>
          @endif
        </div>

        <!-- Product Details -->
        <div class="w-full lg:w-7/12 p-6 lg:p-8 flex flex-col">
          <div class="mb-2 flex items-center gap-2 text-sm text-gray-500">
            <svg class="w-4 h-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>{{ $listing->city }}</span>
          </div>

          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 leading-tight">{{ $listing->title }}</h1>
          
          <div class="mb-6 flex items-end gap-3">
            <span class="text-3xl sm:text-4xl font-bold text-brand">Rp {{ number_format((float)($listing->price_per_unit ?? 0), 0, ',', '.') }}</span>
            <span class="text-lg text-gray-500 mb-1">/ {{ $listing->unit }}</span>
          </div>
          
          {{-- Info Grid --}}
          @php
            $isAvailable = $listing->availability_status === 'available';
            $sellerPhone = $listing->seller->phone_number ?? null;
            $sellerAddress = $listing->seller->sellerProfile->address ?? null;
            $sellerCity = $listing->city ?? ($listing->seller->sellerProfile->city ?? '-');
          @endphp

          <div class="bg-brand/5 border border-brand/10 rounded-xl p-5 mb-6">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-5">

              {{-- Volume --}}
              <div>
                <p class="text-xs text-brand-hover uppercase font-semibold tracking-wider mb-1">Volume</p>
                <p class="text-sm font-bold text-gray-900">{{ number_format((float)($listing->quantity ?? 0), 0, ',', '.') }} {{ $listing->unit }}</p>
              </div>

              {{-- Harga per satuan --}}
              <div>
                <p class="text-xs text-brand-hover uppercase font-semibold tracking-wider mb-1">Harga / Satuan</p>
                <p class="text-sm font-bold text-gray-900">Rp {{ number_format((float)($listing->price_per_unit ?? 0), 0, ',', '.') }} / {{ $listing->unit }}</p>
              </div>

              {{-- Min. Order --}}
              <div>
                <p class="text-xs text-brand-hover uppercase font-semibold tracking-wider mb-1">Min. Order</p>
                <p class="text-sm font-bold text-gray-900">{{ $listing->min_order ?? '1' }} {{ $listing->unit }}</p>
              </div>

              {{-- Lokasi --}}
              <div class="col-span-2 sm:col-span-3">
                <p class="text-xs text-brand-hover uppercase font-semibold tracking-wider mb-1">Lokasi</p>
                <p class="text-sm font-bold text-gray-900">
                  {{ $sellerAddress ? $sellerAddress.', '.$sellerCity : $sellerCity }}
                </p>
              </div>

              {{-- Kontak --}}
              <div>
                <p class="text-xs text-brand-hover uppercase font-semibold tracking-wider mb-1">Kontak Penjual</p>
                @if($sellerPhone)
                <p class="text-sm font-bold text-gray-900">{{ $sellerPhone }}</p>
                @else
                <p class="text-sm font-bold text-gray-400 italic">Tidak tersedia</p>
                @endif
              </div>

              {{-- Status --}}
              <div>
                <p class="text-xs text-brand-hover uppercase font-semibold tracking-wider mb-1">Status</p>
                @if($isAvailable)
                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span> Tersedia
                </span>
                @else
                <span class="inline-flex items-center gap-1 text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span> Habis
                </span>
                @endif
              </div>

            </div>
          </div>



          <div class="prose prose-sm sm:prose-base text-gray-700 mb-8 max-w-none">
            <h3 class="text-lg font-bold text-gray-900 mb-3">Deskripsi Limbah</h3>
            <p class="whitespace-pre-wrap">{{ $listing->description }}</p>
          </div>

          {{-- Seller Card --}}
          <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5 mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
              @if($listing->seller->avatar)
                  <img src="{{ asset('storage/'.$listing->seller->avatar) }}" alt="Toko" class="w-14 h-14 rounded-full shadow-sm object-cover">
              @else
                  <div class="w-14 h-14 bg-brand text-white flex items-center justify-center text-xl font-bold rounded-full shadow-sm">
                      {{ strtoupper(substr($listing->seller->sellerProfile->business_name ?? $listing->seller->name, 0, 2)) }}
                  </div>
              @endif
              <div>
                <h4 class="text-base font-bold text-gray-900">{{ $listing->seller->sellerProfile->business_name ?? $listing->seller->name }}</h4>
                <p class="text-xs text-gray-500 flex items-center gap-1 mt-0.5"><i data-lucide="map-pin" class="w-3 h-3"></i> {{ $listing->city ?? ($listing->seller->sellerProfile->city ?? '-') }}</p>
              </div>
            </div>
            <a href="{{ route('marketplace.store', $listing->seller->id) }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 font-semibold text-sm rounded-xl hover:border-brand hover:text-brand transition-colors">
              <i data-lucide="store" class="w-4 h-4"></i> Kunjungi Toko
            </a>
          </div>

          <div class="mt-auto pt-6 border-t border-gray-100">
            
            <div class="flex flex-col sm:flex-row gap-4 items-end mb-5">
              <!-- Quantity Input -->
              <div class="w-full sm:w-auto">
                <label for="quantity" class="block text-sm font-semibold text-gray-900 mb-2">Kuantitas Pesanan ({{ $listing->unit }})</label>
                <div class="flex items-center w-full sm:w-36 h-12 border border-gray-300 rounded-xl overflow-hidden bg-white shadow-sm">
                  <button type="button" id="btn-minus" class="w-12 h-full flex items-center justify-center text-gray-500 hover:bg-gray-200 hover:text-brand transition-colors bg-gray-50 font-medium text-xl border-r border-gray-200">-</button>
                  <input type="number" id="quantity" name="quantity" min="{{ intval($listing->min_order ?? 1) }}" value="{{ intval($listing->min_order ?? 1) }}"
                         class="w-full h-full text-center border-0 focus:ring-0 text-base font-bold p-0 text-gray-900 bg-white" style="-moz-appearance: textfield;">
                  <button type="button" id="btn-plus" class="w-12 h-full flex items-center justify-center text-gray-500 hover:bg-gray-200 hover:text-brand transition-colors bg-gray-50 font-medium text-xl border-l border-gray-200">+</button>
                </div>
              </div>

              <!-- Total Price Display -->
              <div class="flex-none w-full sm:w-48 flex flex-col justify-center items-center text-center h-12 bg-brand/5 rounded-xl border border-brand/20 px-4">
                 <span class="text-[11px] text-brand-hover uppercase font-bold tracking-wider mb-0.5">Total Harga</span>
                 <span class="text-lg font-bold text-brand leading-none truncate" id="total-price">Rp 0</span>
              </div>
            </div>

          <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 w-full">
              @auth
                @if(auth()->user()->isBuyer())
                  {{-- Masukkan Keranjang --}}
                  <form method="POST" action="{{ route('buyer.cart.store', $listing->id) }}" class="flex-1" id="cart-form">
                    @csrf
                    <input type="hidden" name="quantity" id="cart-quantity" value="{{ intval($listing->min_order ?? 1) }}">
                    <button type="submit" class="w-full h-12 bg-white border-2 border-brand text-brand flex justify-center gap-2 items-center px-4 rounded-xl font-semibold hover:bg-brand/5 transition-all duration-200 shadow-sm transform hover:-translate-y-0.5">
                      <i data-lucide="shopping-cart" class="w-5 h-5 shrink-0"></i>
                      <span class="truncate">Keranjang</span>
                    </button>
                  </form>
                  
                  {{-- Pesan Sekarang (Modal Trigger) --}}
                  <button type="button" id="btn-order" class="flex-1 h-12 bg-brand flex justify-center gap-2 items-center text-white px-4 rounded-xl font-semibold hover:bg-brand-hover transition-all duration-200 shadow hover:shadow-md transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="truncate">Beli</span>
                  </button>
                  {{-- Chat Penjual --}}
                  <form method="POST" action="{{ route('conversations.start', $listing->id) }}" class="flex-1">
                    @csrf
                    <input type="hidden" name="message" value="Halo, saya tertarik dengan listing '{{ $listing->title }}'">
                    <button type="submit" class="w-full h-12 bg-white border-2 border-brand text-brand flex justify-center gap-2 items-center px-4 rounded-xl font-semibold hover:bg-brand/5 transition-all duration-200 shadow-sm transform hover:-translate-y-0.5">
                      <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                      <span class="truncate">Chat Penjual</span>
                    </button>
                  </form>
                  {{-- Favorit --}}
                  @if($isFav)
                  <form method="POST" action="{{ route('buyer.favorites.destroy', $listing->id) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="h-12 w-12 flex-shrink-0 bg-rose-50 border-2 border-rose-200 text-rose-500 flex items-center justify-center rounded-xl hover:bg-rose-100 transition-all" title="Hapus dari Favorit">
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    </button>
                  </form>
                  @else
                  <form method="POST" action="{{ route('buyer.favorites.store', $listing->id) }}">
                    @csrf
                    <button type="submit" class="h-12 w-12 flex-shrink-0 bg-white border-2 border-gray-200 text-gray-400 flex items-center justify-center rounded-xl hover:bg-rose-50 hover:border-rose-200 hover:text-rose-400 transition-all" title="Simpan ke Favorit">
                      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </button>
                  </form>
                  @endif
                @else
                  {{-- Seller cannot order --}}
                  <div class="flex-1 h-12 bg-gray-100 text-gray-500 flex items-center justify-center rounded-xl text-sm font-semibold">Masuk sebagai Pembeli untuk memesan</div>
                @endif
              @else
                {{-- Guest --}}
                <button type="button" onclick="showToast('Anda tidak bisa memesan, segera login terlebih dahulu atau daftar.')" class="w-full flex-1 h-12 bg-brand flex justify-center gap-2 items-center text-white px-4 rounded-xl font-semibold hover:bg-brand-hover transition-all duration-200 shadow">
                  <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                  <span class="truncate">Masuk untuk Memesan</span>
                </button>
              @endauth
            </div>
          </div>
          
        </div>
      </div>
    </div>
    </div> <!-- End Main Content -->
  </div>
</div>


@auth
@if(auth()->user()->isBuyer())
{{-- ===== MODAL PESAN SEKARANG ===== --}}
<div id="order-modal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="modal-backdrop"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md z-10">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Buat Pesanan</h3>
            <button id="modal-close" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('buyer.orders.store', $listing->id) }}" class="px-6 py-5 space-y-5">
            @csrf
            {{-- Jumlah --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jumlah ({{ $listing->unit }})</label>
                <div class="flex items-center h-11 border border-gray-200 rounded-xl overflow-hidden">
                    <button type="button" id="modal-minus" class="w-11 h-full flex items-center justify-center bg-gray-50 border-r border-gray-200 text-gray-500 hover:bg-gray-100 text-xl font-medium transition-colors">-</button>
                    <input type="number" name="quantity" id="modal-qty" min="{{ intval($listing->min_order ?? 1) }}" value="{{ intval($listing->min_order ?? 1) }}" class="flex-1 h-full text-center border-0 focus:ring-0 text-base font-bold text-gray-900" style="-moz-appearance:textfield;">
                    <button type="button" id="modal-plus" class="w-11 h-full flex items-center justify-center bg-gray-50 border-l border-gray-200 text-gray-500 hover:bg-gray-100 text-xl font-medium transition-colors">+</button>
                </div>
                <p class="text-xs text-gray-400 mt-1">Min. order: {{ $listing->min_order ?? 1 }} {{ $listing->unit }}</p>
            </div>
            {{-- Metode --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Metode Pengambilan</label>
                <input type="hidden" name="pickup_method" value="self_pickup">
                <div class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 text-gray-600 font-medium">
                    Ambil Sendiri
                </div>
            </div>
            {{-- Jadwal --}}
            <div class="grid grid-cols-2 gap-3">
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
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan (opsional)</label>
                <textarea name="buyer_note" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand resize-none" placeholder="Instruksi tambahan untuk penjual..."></textarea>
            </div>
            {{-- Total --}}
            <div class="bg-brand/5 border border-brand/10 rounded-xl px-4 py-3 flex items-center justify-between">
                <span class="text-sm text-gray-600 font-semibold">Total Estimasi</span>
                <span class="text-base font-bold text-brand" id="modal-total">Rp 0</span>
            </div>
            <button type="submit" class="w-full h-12 bg-brand hover:bg-brand-hover text-white font-bold text-sm rounded-xl transition-colors shadow">
                Konfirmasi Pesanan
            </button>
        </form>
    </div>
</div>
@endif
@endauth


<style>
/* Hilangkan panah spinner bawaan input number */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>

@push('scripts')
<script>
(function() {
    // 1. Smart back button logic (history.back to preserve state)
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

    // 2. Generic Quantity Counter setup to avoid code duplication
    function setupQuantityCounter(inputId, minusId, plusId, onUpdate) {
        const input = document.getElementById(inputId);
        const minus = document.getElementById(minusId);
        const plus = document.getElementById(plusId);
        if (!input) return;

        const update = () => {
            let val = parseInt(input.value);
            if (isNaN(val) || val < 1) val = 0;
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

    // Initialize main page counter
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
                modalQty.dispatchEvent(new Event('input')); // trigger update
            }
        });
        modalClose?.addEventListener('click', () => modalEl.classList.add('hidden'));
        modalBd?.addEventListener('click', () => modalEl.classList.add('hidden'));

        // Initialize modal counter
        setupQuantityCounter('modal-qty', 'modal-minus', 'modal-plus', (qty) => {
            if (modalTotal) modalTotal.textContent = 'Rp ' + formatRupiah(qty * pricePerUnit);
        });
    }

    // 4. Skeleton Loader handling
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
})();
</script>
@endpush
@endsection