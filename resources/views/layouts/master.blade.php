<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="turbo-prefetch" content="true">
    <title>@yield('title', 'Recyclink')</title>

    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    {{-- Vite: CSS & JS (includes Tailwind CSS v4) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Hotwired Turbo for faster page transitions (SPA-like) --}}
    <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-umd.js"></script>

    {{-- Lucide Icons --}}
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.460.0/dist/umd/lucide.min.js"></script>

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
    </script>
    @include('layouts.toast')
    @include('layouts.sweetalert')
</body>
</html>
