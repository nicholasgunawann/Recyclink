<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Recyclink')</title>

    {{-- Vite: CSS & JS (includes Tailwind CSS v4) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Hotwired Turbo for faster page transitions (SPA-like) --}}
    <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-umd.js"></script>

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    @stack('styles')
</head>
<body class="bg-white text-gray-700 antialiased">

    {{-- Navbar --}}
    @include('layouts.navbar')

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('layouts.footer')

    @stack('scripts')

    {{-- Activate Lucide Icons --}}
    <script>
        document.addEventListener("turbo:load", function() {
            lucide.createIcons();
        });
        // Initial load
        lucide.createIcons();
    </script>
</body>
</html>
