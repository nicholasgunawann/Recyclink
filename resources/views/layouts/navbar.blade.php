<nav class="sticky top-0 z-50 bg-white border-b border-gray-100" id="navbar">
    @php
        $favCount = 0;
        $cartCount = 0;
        $favoritesList = collect();
        $cartList = collect();
        if (auth()->check() && auth()->user()->isBuyer()) {
            $favoritesList = auth()->user()->favoriteListings()->with('listing.primaryImage', 'listing.category')->latest()->take(4)->get();
            $favCount = $favoritesList->count();
        }
        $cartData = [];
        if (session()->has('cart')) {
            $cartData = session('cart');
            $cartCount = count($cartData);
            if ($cartCount > 0) {
                $cartIds = array_keys($cartData);
                $cartList = \App\Models\WasteListing::with('primaryImage', 'category')->whereIn('id', array_slice($cartIds, -4))->get();
            }
        }
    @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-3 shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Recyclink Logo" class="h-10 w-auto">
                <span class="text-2xl font-bold text-gray-900 tracking-tight">Recyclink</span>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden lg:flex items-center gap-9">
                <a href="{{ url('/') }}"              class="text-base font-medium {{ request()->is('/') ? 'text-brand font-bold' : 'text-gray-600' }} hover:text-brand transition-colors">Beranda</a>
                <a href="{{ url('/marketplace') }}"  class="text-base font-medium {{ request()->is('marketplace*') ? 'text-brand font-bold' : 'text-gray-600' }} hover:text-brand transition-colors">Marketplace</a>
                <a href="{{ url('/education') }}"    class="text-base font-medium {{ request()->is('education*') ? 'text-brand font-bold' : 'text-gray-600' }} hover:text-brand transition-colors">Edukasi</a>
                <a href="{{ url('/tentang') }}"       class="text-base font-medium {{ request()->is('tentang*') ? 'text-brand font-bold' : 'text-gray-600' }} hover:text-brand transition-colors">Tentang & Kontak</a>
            </div>

            {{-- CTA & Auth --}}
            <div class="hidden lg:flex items-center gap-5">
                @auth
                    @if(auth()->user()->isBuyer())
                        <a href="{{ route('buyer.dashboard') }}" class="text-base font-bold text-gray-600 hover:text-brand transition-colors">Dashboard</a>
                        <div class="relative group/nav-fav">
                            <a href="{{ route('buyer.favorites.index') }}" class="relative text-gray-600 hover:text-brand transition-colors flex items-center p-1">
                                <i data-lucide="heart" class="w-6 h-6"></i>
                                @if($favCount > 0)
                                <span class="absolute top-0 right-0 transform translate-x-1/4 -translate-y-1/4 bg-rose-500 text-white text-[10px] font-bold rounded-full h-4 min-w-[16px] px-1 flex items-center justify-center">{{ $favCount > 99 ? '99+' : $favCount }}</span>
                                @endif
                            </a>
                            <div class="absolute right-0 top-full pt-2 w-[360px] invisible opacity-0 translate-y-1 group-hover/nav-fav:visible group-hover/nav-fav:opacity-100 group-hover/nav-fav:translate-y-0 transition-all duration-200 z-50">
                                <div class="bg-white rounded-lg shadow-[0_4px_20px_rgba(0,0,0,0.12)] border border-gray-200 overflow-hidden">
                                    {{-- Header --}}
                                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                        <h3 class="text-sm font-bold text-gray-900">Favorit <span class="font-normal text-gray-500">({{ $favCount }})</span></h3>
                                        <a href="{{ route('buyer.favorites.index') }}" class="text-sm font-bold text-brand hover:text-brand-hover transition-colors">Lihat</a>
                                    </div>
                                    {{-- Items --}}
                                    <div class="max-h-[320px] overflow-y-auto">
                                        @if($favoritesList->isEmpty())
                                            <div class="py-10 flex flex-col items-center justify-center text-center px-4">
                                                <i data-lucide="heart" class="w-10 h-10 text-gray-200 mb-3"></i>
                                                <p class="text-sm text-gray-400">Belum ada barang favorit</p>
                                            </div>
                                        @else
                                            @foreach($favoritesList as $fav)
                                            @if($fav->listing)
                                            <a href="{{ route('marketplace.show', $fav->listing->id) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                                                <div class="w-14 h-14 rounded-md bg-gray-100 border border-gray-200 shrink-0 overflow-hidden">
                                                    <img src="{{ $fav->listing->primaryImage ? (str_starts_with($fav->listing->primaryImage->image_url, 'http') ? $fav->listing->primaryImage->image_url : asset('storage/'.$fav->listing->primaryImage->image_url)) : '' }}" alt="" class="w-full h-full object-cover" onerror="this.style.display='none'">
                                                </div>
                                                <div class="flex-1 min-w-0 flex justify-between items-start gap-2">
                                                    <div class="min-w-0">
                                                        <p class="text-[13px] text-gray-800 line-clamp-2 leading-snug">{{ $fav->listing->title }}</p>
                                                        <p class="text-xs text-gray-400 mt-1">{{ $fav->listing->category->category_name ?? '' }}</p>
                                                    </div>
                                                    <p class="text-[13px] font-bold text-gray-900 shrink-0 whitespace-nowrap">Rp{{ number_format((float)($fav->listing->price_per_unit ?? 0), 0, ',', '.') }}</p>
                                                </div>
                                            </a>
                                            @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keranjang Dropdown (Tokopedia style) -->
                        <div class="relative group/nav-cart">
                            <a href="{{ route('buyer.cart.index') }}" class="relative text-gray-600 hover:text-brand transition-colors flex items-center p-1">
                                <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                                @if($cartCount > 0)
                                <span class="absolute top-0 right-0 transform translate-x-1/4 -translate-y-1/4 bg-rose-500 text-white text-[10px] font-bold rounded-full h-4 min-w-[16px] px-1 flex items-center justify-center">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                                @endif
                            </a>
                            <div class="absolute right-0 top-full pt-2 w-[360px] invisible opacity-0 translate-y-1 group-hover/nav-cart:visible group-hover/nav-cart:opacity-100 group-hover/nav-cart:translate-y-0 transition-all duration-200 z-50">
                                <div class="bg-white rounded-lg shadow-[0_4px_20px_rgba(0,0,0,0.12)] border border-gray-200 overflow-hidden">
                                    {{-- Header --}}
                                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                        <h3 class="text-sm font-bold text-gray-900">Keranjang <span class="font-normal text-gray-500">({{ $cartCount }})</span></h3>
                                        <a href="{{ route('buyer.cart.index') }}" class="text-sm font-bold text-brand hover:text-brand-hover transition-colors">Lihat</a>
                                    </div>
                                    {{-- Items --}}
                                    <div class="max-h-[320px] overflow-y-auto">
                                        @if($cartList->isEmpty())
                                            <div class="py-10 flex flex-col items-center justify-center text-center px-4">
                                                <i data-lucide="shopping-cart" class="w-10 h-10 text-gray-200 mb-3"></i>
                                                <p class="text-sm text-gray-400">Keranjang masih kosong</p>
                                            </div>
                                        @else
                                            @foreach($cartList as $item)
                                            <a href="{{ route('marketplace.show', $item->id) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                                                <div class="w-14 h-14 rounded-md bg-gray-100 border border-gray-200 shrink-0 overflow-hidden">
                                                    <img src="{{ $item->primaryImage ? (str_starts_with($item->primaryImage->image_url, 'http') ? $item->primaryImage->image_url : asset('storage/'.$item->primaryImage->image_url)) : '' }}" alt="" class="w-full h-full object-cover" onerror="this.style.display='none'">
                                                </div>
                                                <div class="flex-1 min-w-0 flex justify-between items-start gap-2">
                                                    <div class="min-w-0">
                                                        <p class="text-[13px] text-gray-800 line-clamp-2 leading-snug">{{ $item->title }}</p>
                                                        <p class="text-xs text-gray-400 mt-1">{{ $item->category->category_name ?? '' }}</p>
                                                    </div>
                                                    <p class="text-[13px] font-bold text-gray-900 shrink-0 whitespace-nowrap">{{ $cartData[$item->id]['quantity'] ?? 1 }} x Rp{{ number_format((float)($item->price_per_unit ?? 0), 0, ',', '.') }}</p>
                                                </div>
                                            </a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif(auth()->user()->isSeller())
                        <a href="{{ route('seller.dashboard') }}" class="text-base font-bold text-gray-600 hover:text-brand transition-colors">Dashboard</a>
                        <a href="{{ route('seller.orders.index') ?? '#' }}" class="text-base font-bold text-gray-600 hover:text-brand transition-colors flex items-center gap-2">
                            <i data-lucide="clipboard-list" class="w-5 h-5"></i> Kelola Pesanan
                        </a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-base font-bold text-gray-600 hover:text-brand transition-colors">Dashboard Admin</a>
                    @endif
                    <div class="h-6 w-px bg-gray-200 mx-2"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-base font-bold text-red-500 hover:text-red-700 transition-colors">Keluar</button>
                    </form>
                @else
                    <a href="{{ url('/login') }}"
                       class="text-base font-bold text-gray-600 hover:text-gray-900 transition-colors">Masuk</a>
                    <a href="{{ url('/register') }}"
                       class="inline-flex items-center gap-1.5 bg-brand hover:bg-brand-hover text-white text-base font-bold px-6 py-3 rounded-xl transition-colors shadow-sm">
                        Daftar Gratis
                    </a>
                @endauth
            </div>

            {{-- Mobile Hamburger --}}
            <button id="nav-toggle"
                    class="lg:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-50 transition-colors"
                    aria-label="Toggle menu">
                <i data-lucide="menu" id="icon-open" class="w-6 h-6"></i>
                <i data-lucide="x"    id="icon-close" class="w-6 h-6 hidden"></i>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="lg:hidden hidden border-t border-gray-100 bg-white shadow-lg">
        <div class="px-4 py-5 flex flex-col gap-4">
            <a href="{{ url('/') }}"              class="text-base font-medium text-gray-700 hover:text-brand py-1.5">Beranda</a>
            <a href="{{ url('/marketplace') }}"  class="text-base font-medium text-gray-700 hover:text-brand py-1.5">Marketplace</a>
            <a href="{{ url('/education') }}"    class="text-base font-medium text-gray-700 hover:text-brand py-1.5">Edukasi</a>
            <a href="{{ url('/tentang') }}"       class="text-base font-medium text-gray-700 hover:text-brand py-1.5">Tentang & Kontak</a>
            <hr class="border-gray-100 my-2">
            @auth
                @if(auth()->user()->isBuyer())
                    <a href="{{ route('buyer.dashboard') }}" class="text-base font-bold text-gray-700 hover:text-brand py-1.5">Dashboard</a>
                    <a href="{{ route('buyer.favorites.index') }}" class="text-base font-bold text-gray-700 hover:text-brand py-1.5 flex items-center justify-between">
                        <div class="flex items-center gap-2"><i data-lucide="heart" class="w-5 h-5"></i> Favorit</div>
                        @if($favCount > 0)<span class="bg-rose-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $favCount }}</span>@endif
                    </a>
                    <a href="{{ route('buyer.cart.index') }}" class="text-base font-bold text-gray-700 hover:text-brand py-1.5 flex items-center justify-between">
                        <div class="flex items-center gap-2"><i data-lucide="shopping-cart" class="w-5 h-5"></i> Keranjang</div>
                        @if($cartCount > 0)<span class="bg-brand text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $cartCount }}</span>@endif
                    </a>
                @elseif(auth()->user()->isSeller())
                    <a href="{{ route('seller.dashboard') }}" class="text-base font-bold text-gray-700 hover:text-brand py-1.5">Dashboard</a>
                    <a href="{{ route('seller.orders.index') ?? '#' }}" class="text-base font-bold text-gray-700 hover:text-brand py-1.5 flex items-center gap-2">
                        <i data-lucide="clipboard-list" class="w-5 h-5"></i> Kelola Pesanan
                    </a>
                @elseif(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="text-base font-bold text-gray-700 hover:text-brand py-1.5">Dashboard Admin</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full text-left text-base font-bold text-red-500 hover:text-red-700 py-1.5">Keluar</button>
                </form>
            @else
                <a href="{{ url('/login') }}"
                   class="text-base font-bold text-gray-700 hover:text-brand py-1.5">Masuk</a>
                <a href="{{ url('/register') }}"
                   class="bg-brand hover:bg-brand-hover text-white text-base font-bold px-5 py-3.5 rounded-xl text-center transition-colors shadow-sm mt-2">
                    Daftar Gratis
                </a>
            @endauth
        </div>
    </div>
</nav>

<script>
    document.addEventListener('click', function(e) {
        const toggle = e.target.closest('#nav-toggle');
        if (!toggle) return;

        const menu = document.getElementById('mobile-menu');
        if (!menu) return;

        const isHidden = menu.classList.toggle('hidden');
        
        // Mengambil ulang element karena Lucide JS seringkali menimpa (replace) tag <i> menjadi <svg>
        const iconOpen = document.getElementById('icon-open');
        const iconClose = document.getElementById('icon-close');
        
        if (iconOpen) iconOpen.classList.toggle('hidden', !isHidden);
        if (iconClose) iconClose.classList.toggle('hidden', isHidden);
    });
</script>
