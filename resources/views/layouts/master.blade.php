<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="turbo-prefetch" content="true">
    <title>@yield('title', 'Recyclink')</title>

    {{-- Vite: CSS & JS (includes Tailwind CSS v4) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Hotwired Turbo for faster page transitions (SPA-like) --}}
    <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-umd.js"></script>

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    @stack('styles')
</head>
<body class="bg-white text-gray-700 antialiased overflow-y-scroll">

    {{-- Navbar --}}
    @include('layouts.navbar')

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('layouts.footer')

    @stack('scripts')

    @include('layouts.global-loader')

    <style>
        /* Turbo Progress Bar Styling */
        .turbo-progress-bar {
            height: 4px;
            background-color: #14b8a6; /* Brand color */
            z-index: 99999;
        }
    </style>

    {{-- Activate Lucide Icons & Turbo Loaders --}}
    <script>
        document.addEventListener("turbo:load", function() {
            lucide.createIcons();
        });

        // Initial load
        lucide.createIcons();
        
        window.showToast = function(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-24 right-6 z-[9999] flex items-center justify-between gap-4 min-w-[300px] max-w-sm bg-white border-l-4 border-brand shadow-2xl rounded-lg p-4 transition-all duration-300 transform translate-x-full opacity-0';
            toast.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 shrink-0 bg-brand/10 p-1 rounded-full text-brand">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 leading-snug">${message}</p>
                </div>
                <button type="button" class="shrink-0 text-gray-400 hover:text-gray-600 transition-colors" onclick="this.parentElement.remove()">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            `;
            document.body.appendChild(toast);
            
            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            });

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    toast.classList.add('translate-x-full', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        };
    </script>
    @include('layouts.sweetalert')
</body>
</html>
