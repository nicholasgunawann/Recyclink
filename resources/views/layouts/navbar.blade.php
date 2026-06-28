<nav class="sticky top-0 z-50 bg-white border-b border-gray-100" id="navbar">
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
                        <a href="{{ route('buyer.favorites.index') ?? '#' }}" class="text-base font-bold text-gray-600 hover:text-brand transition-colors flex items-center gap-2">
                            <i data-lucide="shopping-cart" class="w-5 h-5"></i> Keranjang
                        </a>
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
                    <a href="{{ route('buyer.favorites.index') ?? '#' }}" class="text-base font-bold text-gray-700 hover:text-brand py-1.5 flex items-center gap-2">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i> Keranjang
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
    const toggle    = document.getElementById('nav-toggle');
    const menu      = document.getElementById('mobile-menu');
    const iconOpen  = document.getElementById('icon-open');
    const iconClose = document.getElementById('icon-close');

    toggle.addEventListener('click', () => {
        const isHidden = menu.classList.toggle('hidden');
        iconOpen.classList.toggle('hidden', !isHidden);
        iconClose.classList.toggle('hidden', isHidden);
    });
</script>
