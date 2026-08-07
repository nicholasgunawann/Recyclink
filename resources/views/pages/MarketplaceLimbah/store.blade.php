@extends('layouts.master')
@section('title', 'Toko ' . ($user->sellerProfile->business_name ?? $user->name) . ' - Recyclink')
@section('content')
<div class="bg-gray-50 min-h-screen pb-20">
    
    {{-- Store Page Container --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

        {{-- Breadcrumb & Back Navigation --}}
        <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
            <a href="{{ route('marketplace.index') }}" id="btn-back" class="inline-flex items-center text-xs sm:text-sm font-bold text-gray-600 hover:text-brand transition-colors group">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform"></i>
                <span>Kembali ke Marketplace</span>
            </a>

            <nav class="flex items-center gap-2 text-xs text-gray-400 font-medium" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-brand transition-colors">Beranda</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-300"></i>
                <a href="{{ route('marketplace.index') }}" class="hover:text-brand transition-colors">Marketplace</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-300"></i>
                <span class="text-gray-700 font-bold truncate max-w-[200px] sm:max-w-[300px]">
                    Toko {{ $user->sellerProfile->business_name ?? $user->name }}
                </span>
            </nav>
        </div>

        {{-- Store Skeleton Loader --}}
        <div id="store-skeleton" class="hidden animate-pulse">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-xs mb-6">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 md:w-24 md:h-24 rounded-full bg-gray-200 shrink-0"></div>
                    <div class="space-y-2">
                        <div class="h-6 bg-gray-200 rounded-md w-48"></div>
                        <div class="h-4 bg-gray-200 rounded-md w-32"></div>
                        <div class="h-9 bg-gray-200 rounded-lg w-28 mt-2"></div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col lg:flex-row gap-8 mt-6">
                <div class="w-full lg:w-64 shrink-0 h-40 bg-white rounded-2xl border border-gray-100 p-4 space-y-3">
                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
                    <div class="h-8 bg-gray-100 rounded-lg w-full"></div>
                    <div class="h-8 bg-gray-100 rounded-lg w-full"></div>
                </div>
                <div class="flex-1 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4">
                    @for($i = 0; $i < 6; $i++)
                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden flex flex-col h-full">
                        <div class="aspect-square bg-gray-200 w-full"></div>
                        <div class="p-3 space-y-2 flex-1 justify-between">
                            <div class="h-3 bg-gray-200 rounded w-full"></div>
                            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        {{-- Store Real Content Container --}}
        <div id="store-content">
            
            {{-- Store Header Profile --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-xs mb-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-full overflow-hidden border border-gray-100 shrink-0 shadow-xs relative">
                            @if($user->avatar)
                                <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-brand text-white flex items-center justify-center text-3xl font-bold">
                                    {{ strtoupper(substr($user->sellerProfile->business_name ?? $user->name, 0, 2)) }}
                                </div>
                            @endif
                            <div class="absolute bottom-0 right-0 bg-brand text-white rounded-full p-1 border-2 border-white">
                                <i data-lucide="star" class="w-3 h-3 fill-white"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2">
                                {{ $user->sellerProfile->business_name ?? $user->name }}
                            </h1>
                            <p class="text-sm text-gray-500 flex items-center gap-1.5 mb-4">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400"></i>
                                {{ $user->sellerProfile->city ?? 'Lokasi tidak diketahui' }}
                            </p>
                            <div class="flex flex-wrap gap-2 md:gap-3">
                                @auth
                                <form method="POST" action="{{ route('conversations.start', $user->id) }}">
                                    @csrf
                                    <button type="submit" class="bg-brand hover:bg-brand-hover text-white font-bold text-xs sm:text-sm px-4 py-2 rounded-xl transition-all shadow-xs flex items-center gap-2">
                                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                                        Chat Penjual
                                    </button>
                                </form>
                                @else
                                <button type="button" onclick="showToast('Anda harus login terlebih dahulu untuk melakukan chat.')" class="bg-brand hover:bg-brand-hover text-white font-bold text-xs sm:text-sm px-4 py-2 rounded-xl transition-all shadow-xs flex items-center gap-2">
                                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                                    Chat Penjual
                                </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Store Content & Etalase --}}
            <div class="flex flex-col lg:flex-row gap-8 mt-6">
                {{-- Left Sidebar --}}
                <aside class="w-full lg:w-64 shrink-0">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden sticky top-24">
                        <div class="p-4 border-b border-gray-100 bg-white">
                            <h3 class="font-bold text-gray-900 text-sm">Etalase Toko</h3>
                        </div>
                        <ul class="p-2 space-y-1 bg-white">
                            <li>
                                <a href="{{ route('marketplace.store', $user->id) }}" class="block px-4 py-2.5 rounded-xl {{ !isset($tab) || $tab !== 'terjual' ? 'bg-brand/10 text-brand font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium' }} text-xs sm:text-sm transition-all">Semua Produk</a>
                            </li>
                            <li>
                                <a href="{{ route('marketplace.store', ['user' => $user->id, 'tab' => 'terjual']) }}" class="block px-4 py-2.5 rounded-xl {{ isset($tab) && $tab === 'terjual' ? 'bg-brand/10 text-brand font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium' }} text-xs sm:text-sm transition-all">Produk Terjual</a>
                            </li>
                        </ul>
                    </div>
                </aside>

                {{-- Main Products Grid (Seamless Tokopedia Style) --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900">{{ isset($tab) && $tab === 'terjual' ? 'Produk Terjual' : 'Semua Produk Limbah' }}</h2>
                    </div>

                    @if($listings->isEmpty())
                    <div class="text-center py-20 bg-white border border-gray-200 border-dashed rounded-2xl p-8">
                        <div class="w-14 h-14 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-3">
                            <i data-lucide="package-x" class="w-7 h-7 text-gray-300"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-800">{{ isset($tab) && $tab === 'terjual' ? 'Toko ini belum memiliki produk terjual.' : 'Toko ini belum memiliki produk limbah.' }}</p>
                    </div>
                    @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4">
                        @foreach($listings as $l)
                        <a href="{{ route('marketplace.show', $l->id) }}?ref=store" class="group bg-transparent hover:bg-white rounded-xl overflow-hidden border border-gray-200/80 shadow-2xs hover:shadow-xl hover:shadow-brand/10 hover:-translate-y-1 hover:border-brand/40 transition-all duration-300 flex flex-col h-full">
                            <div class="relative w-full aspect-square bg-gray-100 overflow-hidden shrink-0">
                                <img src="{{ $l->primaryImage ? (str_starts_with($l->primaryImage->image_url, 'http') ? $l->primaryImage->image_url : asset('storage/'.$l->primaryImage->image_url)) : 'https://placehold.co/400x400?text=Limbah' }}" 
                                    alt="{{ $l->title }}" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out"
                                    onerror="this.src='https://placehold.co/400x400?text=Limbah'">
                                <span class="absolute top-2 left-2 bg-brand/90 backdrop-blur-xs text-white text-[9px] font-bold px-2 py-0.5 rounded uppercase tracking-wide shadow-xs">{{ $l->category_short_name }}</span>
                            </div>
                            <div class="p-2.5 sm:p-3 flex flex-col justify-between grow gap-1.5">
                                <div>
                                    <h5 class="text-xs sm:text-sm font-normal text-gray-900 line-clamp-2 leading-snug group-hover:text-brand transition-colors duration-200">{{ $l->title }}</h5>
                                    <p class="text-sm sm:text-base font-bold text-gray-900 mt-1">
                                        Rp {{ number_format((float)($l->price_per_unit ?? 0), 0, ',', '.') }} <span class="text-[11px] font-normal text-gray-400">/ {{ $l->unit }}</span>
                                    </p>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
                                    <span class="truncate flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3 h-3 text-gray-400 shrink-0"></i>
                                        <span class="truncate">{{ $l->city }}</span>
                                    </span>
                                    <span class="shrink-0 font-normal text-gray-400">Stok: {{ number_format((float)($l->quantity ?? 0), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function initStorePage() {
        const skeleton = document.getElementById('store-skeleton');
        const content = document.getElementById('store-content');

        if (!skeleton || !content) return;

        // Check if page is loaded from Turbo cache or visited back/forward
        const isTurboPreview = document.documentElement.hasAttribute('data-turbo-preview');
        const storeCacheKey = 'recyclink_store_cached_' + window.location.pathname;
        const isMemoryCached = sessionStorage.getItem(storeCacheKey) === 'true';

        if (isTurboPreview || isMemoryCached) {
            // Immediate display if already cached!
            skeleton.classList.add('hidden');
            content.classList.remove('hidden', 'opacity-0');
        } else {
            // First time visit: show skeleton briefly for smooth UX
            skeleton.classList.remove('hidden');
            content.classList.add('opacity-0');
            
            setTimeout(() => {
                skeleton.classList.add('hidden');
                content.classList.remove('opacity-0');
                sessionStorage.setItem(storeCacheKey, 'true');
            }, 120);
        }

        if (window.lucide) lucide.createIcons();
    }

    document.addEventListener('turbo:load', initStorePage);
    if (document.readyState !== 'loading') {
        initStorePage();
    } else {
        document.addEventListener('DOMContentLoaded', initStorePage);
    }
</script>
@endpush
