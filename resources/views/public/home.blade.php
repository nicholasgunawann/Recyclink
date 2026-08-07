@extends('layouts.master')

@section('title', 'Recyclink — Cuan Bertambah, Lingkungan Terjaga')

@section('content')

    {{-- 1. Hero & Search --}}
    @include('pages.beranda.hero')

    {{-- 2. Kategori Limbah --}}
    @include('pages.beranda.kategori')

    {{-- 3. Limbah Terbaru --}}
    @include('pages.beranda.limbah-terbaru')

    {{-- 4. Keunggulan Recyclink --}}
    @include('pages.beranda.keunggulan')

    {{-- 5. Dampak & Tujuan --}}
    @include('pages.beranda.cara-kerja')

    {{-- 6. Testimoni & Mitra --}}
    @include('pages.beranda.testimoni')

@endsection

@push('scripts')
<script>
    (function() {
        let observer = null;

        function initScrollAnimations() {
            if (observer) {
                observer.disconnect();
            }

            observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    // Exclude limbah-terbaru section completely
                    if (entry.target.closest('#limbah-terbaru')) return;

                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    } else {
                        // Remove class when leaving viewport so scroll up/down re-triggers animation
                        entry.target.classList.remove('is-visible');
                    }
                });
            }, {
                threshold: 0.12,
                rootMargin: '0px 0px -30px 0px'
            });

            const elements = document.querySelectorAll('.scroll-reveal, .scroll-reveal-left, .scroll-reveal-right');
            elements.forEach(el => {
                if (!el.closest('#limbah-terbaru')) {
                    observer.observe(el);
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initScrollAnimations);
        } else {
            initScrollAnimations();
        }
        document.addEventListener('turbo:load', initScrollAnimations);
    })();
</script>
@endpush
