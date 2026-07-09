<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="turbo-prefetch" content="true">
    <title>@yield('title', 'Dashboard Pembeli - Recyclink')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-umd.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans flex h-screen overflow-hidden">

    <!-- Mobile Sidebar Backdrop -->
    <div id="mobile-sidebar-backdrop" class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden hidden transition-opacity opacity-0"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-200 flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300 lg:static lg:flex-shrink-0">
        
        <div class="h-20 flex items-center justify-between px-6 border-b border-gray-100 shrink-0">
            <a href="{{ route('buyer.dashboard') }}" class="flex items-center gap-3">
                <div class="bg-brand p-1.5 rounded-xl shadow-sm">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-7 w-auto">
                </div>
                <span class="text-xl font-bold text-gray-900 tracking-tight">Pembeli</span>
            </a>
            <button id="close-sidebar" class="lg:hidden text-gray-500 hover:text-gray-800 p-2 rounded-xl hover:bg-gray-50 transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5">
            <a href="{{ route('buyer.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold {{ request()->routeIs('buyer.dashboard') ? 'bg-brand/10 text-brand' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                Dashboard
            </a>
            <a href="{{ route('buyer.orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold {{ request()->routeIs('buyer.orders.*') ? 'bg-brand/10 text-brand' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors">
                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                Pesanan Saya
            </a>
            <a href="{{ route('buyer.cart.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold {{ request()->routeIs('buyer.cart.*') ? 'bg-brand/10 text-brand' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors">
                <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                Keranjang
            </a>
            <a href="{{ route('buyer.favorites.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold {{ request()->routeIs('buyer.favorites.*') ? 'bg-brand/10 text-brand' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors">
                <i data-lucide="heart" class="w-5 h-5"></i>
                Tersimpan
            </a>
            <a href="{{ route('conversations.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold {{ request()->routeIs('conversations.*') ? 'bg-brand/10 text-brand' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors">
                <i data-lucide="message-circle" class="w-5 h-5"></i>
                Pesan
            </a>
            <a href="{{ route('buyer.profile.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold {{ request()->routeIs('buyer.profile.*') ? 'bg-brand/10 text-brand' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} transition-colors">
                <i data-lucide="user" class="w-5 h-5"></i>
                Profil
            </a>

            <div class="pt-4 mt-4 border-t border-gray-100">
                <a href="{{ route('marketplace.index') }}" class="flex items-center justify-between px-4 py-3 rounded-xl font-bold text-gray-700 bg-gray-50 hover:bg-brand hover:text-white border border-gray-200 hover:border-brand transition-all group">
                    <div class="flex items-center gap-3">
                        <i data-lucide="store" class="w-5 h-5"></i>
                        Beli Limbah
                    </div>
                    <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors"></i>
                </a>
            </div>
        </nav>


        <div class="p-6 border-t border-gray-100">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 px-4 py-3 rounded-xl font-bold text-red-600 bg-red-50 hover:bg-red-100 transition-colors">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    Keluar Sistem
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content wrapper -->
    <div class="flex-1 flex flex-col min-w-0 bg-[#F8FAFC]">
        
        <!-- Topbar -->
        <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-6 lg:px-10 shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-4">
                <button id="open-sidebar" class="lg:hidden text-gray-500 hover:text-gray-900 p-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h2 class="text-2xl font-bold text-gray-800 hidden sm:block">@yield('header_title', 'Dashboard')</h2>
            </div>
            
            <div class="flex items-center gap-5">
                @include('layouts.notification-dropdown')
                
                <div class="flex items-center gap-3 pl-5 border-l border-gray-200">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-bold text-gray-700">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                        <p class="text-xs text-brand font-medium">Pembeli</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=7A9C59&color=fff" alt="User" class="w-10 h-10 rounded-xl object-cover shadow-sm border border-brand/20">
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto flex flex-col relative">
            <!-- Skeleton Loader -->
            <div id="dashboard-skeleton" class="absolute inset-0 bg-[#F8FAFC] z-50 hidden p-6 lg:p-10">
                <div class="animate-pulse flex flex-col gap-6">
                    <div class="h-8 bg-gray-200 rounded-lg w-1/4 mb-4"></div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="h-32 bg-gray-200 rounded-2xl"></div>
                        <div class="h-32 bg-gray-200 rounded-2xl"></div>
                        <div class="h-32 bg-gray-200 rounded-2xl"></div>
                    </div>
                    <div class="h-64 bg-gray-200 rounded-2xl mt-4"></div>
                </div>
            </div>

            <div class="p-6 lg:p-10 flex-1 transition-opacity duration-200" id="dashboard-content">
                @yield('content')
            </div>
            <!-- Footer -->
            <footer class="py-5 text-center text-sm text-gray-500 border-t border-gray-200/60 bg-white">
                &copy; {{ date('Y') }} Recyclink. Hak Cipta Dilindungi.
            </footer>
        </main>
    </div>

    <script>
        document.addEventListener("turbo:load", initBuyerScripts);
        if (!window.Turbo) initBuyerScripts();

        function initBuyerScripts() {
            lucide.createIcons();
            const sidebar = document.getElementById('sidebar');
            const openBtn = document.getElementById('open-sidebar');
            const closeBtn = document.getElementById('close-sidebar');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            
            if (!sidebar) return;

            function toggleSidebar() {
            const isOpen = !sidebar.classList.contains('-translate-x-full');
            if (isOpen) {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                setTimeout(() => backdrop.classList.remove('opacity-100'), 10);
            } else {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                setTimeout(() => backdrop.classList.add('opacity-100'), 10);
            }
        }
        openBtn?.addEventListener('click', toggleSidebar);
        closeBtn?.addEventListener('click', toggleSidebar);
        backdrop?.addEventListener('click', toggleSidebar);


        // Skeleton logic
        document.addEventListener("turbo:visit", function() {
            document.getElementById('dashboard-skeleton')?.classList.remove('hidden');
            document.getElementById('dashboard-content')?.classList.add('opacity-0');
        });
        document.addEventListener("turbo:load", function() {
            setTimeout(() => {
                document.getElementById('dashboard-skeleton')?.classList.add('hidden');
                document.getElementById('dashboard-content')?.classList.remove('opacity-0');
            }, 50); // slight delay for smooth transition
        });
        } // close initBuyerScripts
    </script>
    @stack('scripts')
    @include('layouts.global-loader')
    @include('layouts.sweetalert')
</body>
</html>
