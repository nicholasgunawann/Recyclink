@extends('layouts.master')
@section('title', 'Marketplace Limbah – Recyclink')
@section('content')
<div class="bg-gray-50/70 min-h-screen pb-16">
    
    {{-- Top Search & Filter Sticky Bar (Aligned Top 80px under Navbar h-20) --}}
    <div class="bg-white/95 backdrop-blur-md border-b border-gray-100 sticky top-[80px] z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
            <div class="flex items-center gap-3">
                
                {{-- Search Bar Input Group --}}
                <div class="relative flex-1 flex items-center">
                    <div class="absolute left-3.5 inset-y-0 flex items-center pointer-events-none z-10">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" id="search-input" placeholder="Cari limbah (misal: plastik, kertas, logam)..." 
                        class="w-full h-11 pl-10 pr-10 bg-gray-50/80 border border-gray-200/80 rounded-xl text-sm text-gray-900 placeholder:text-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition-all">
                    <button id="btn-search-clear" class="hidden absolute right-3 text-gray-400 hover:text-gray-600 p-1 rounded-lg flex items-center justify-center">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                {{-- Filter Dropdown Trigger & Floating Panel (Tokopedia Auto Drop on Hover) --}}
                <div class="relative shrink-0 group" id="filter-dropdown-container">
                    <button id="btn-filter-trigger" type="button" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 group-hover:border-brand hover:border-brand text-gray-700 group-hover:text-brand hover:text-brand font-bold text-sm rounded-xl transition-all shadow-xs cursor-pointer focus:outline-none">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4 text-brand"></i>
                        <span>Filter</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform group-hover:rotate-180" id="filter-chevron"></i>
                    </button>

                    {{-- Floating Filter Menu Panel --}}
                    <div id="filter-dropdown-menu" 
                        class="hidden absolute right-0 top-full pt-2 z-50">
                        <div class="w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-gray-100 p-5">
                        
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                <i data-lucide="filter" class="w-4 h-4 text-brand"></i>
                                Filter Produk & Toko
                            </h3>
                            <button type="button" id="btn-reset-dropdown" class="text-xs font-semibold text-brand hover:underline">
                                Reset Semua
                            </button>
                        </div>

                        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-1">
                            
                            {{-- Filter Kategori --}}
                            <div>
                                <label class="text-xs font-bold text-gray-900 uppercase tracking-wider block mb-2">Kategori Limbah</label>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($categories as $category)
                                        @php
                                            $catName = data_get($category, 'category_name') ?? data_get($category, 'name') ?? (is_string($category) ? $category : '');
                                        @endphp
                                        @if($catName)
                                        <label class="flex items-center gap-2 cursor-pointer p-1.5 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-colors">
                                            <input type="checkbox" value="{{ strtolower($catName) }}" class="category-filter accent-brand w-4 h-4 rounded cursor-pointer border-gray-300 text-brand focus:ring-brand">
                                            <span class="text-xs text-gray-700 font-medium truncate">{{ $catName }}</span>
                                        </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            {{-- Filter Lokasi Kota --}}
                            <div>
                                <label class="text-xs font-bold text-gray-900 uppercase tracking-wider block mb-2">Lokasi Kota</label>
                                <div class="relative">
                                    <input type="text" id="search-lokasi" placeholder="Cari kota (misal: Semarang)..." 
                                        class="w-full text-xs sm:text-sm border border-gray-200 rounded-lg pl-8 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-3"></i>
                                </div>
                            </div>

                            {{-- Filter Rentang Harga --}}
                            <div id="filter-harga-section">
                                <label class="text-xs font-bold text-gray-900 uppercase tracking-wider block mb-2">Rentang Harga (Rp)</label>
                                <div class="flex items-center gap-2">
                                    <div class="relative w-full">
                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                        <input type="number" id="harga-min" min="0" placeholder="Min" 
                                            class="w-full text-xs border border-gray-200 rounded-lg pl-7 pr-2 py-2 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                    </div>
                                    <span class="text-gray-300 font-medium text-xs">–</span>
                                    <div class="relative w-full">
                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                        <input type="number" id="harga-max" min="0" placeholder="Max" 
                                            class="w-full text-xs border border-gray-200 rounded-lg pl-7 pr-2 py-2 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                    </div>
                                </div>
                            </div>

                            {{-- Filter Minimal Volume --}}
                            <div id="filter-volume-section">
                                <label class="text-xs font-bold text-gray-900 uppercase tracking-wider block mb-2">Minimal Volume (Stok)</label>
                                <input type="number" id="volume-min" min="0" placeholder="Contoh: 100" 
                                    class="w-full text-xs sm:text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            </div>

                            {{-- Filter Status Available --}}
                            <div id="filter-status-section">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" id="filter-status" checked class="accent-brand w-4 h-4 rounded cursor-pointer border-gray-300 text-brand focus:ring-brand">
                                    <span class="text-xs font-medium text-gray-700">Hanya tampilkan stok yang tersedia</span>
                                </label>
                            </div>

                        </div>

                        <div class="pt-4 mt-4 border-t border-gray-100">
                            <button type="button" id="btn-apply-dropdown" 
                                class="w-full bg-brand hover:bg-brand-hover text-white font-bold text-xs sm:text-sm py-2.5 rounded-xl transition-all shadow-xs">
                                Terapkan Filter
                            </button>
                        </div>

                        </div>{{-- end inner panel div --}}
                    </div>{{-- end filter-dropdown-menu --}}
                </div>{{-- end filter-dropdown-container --}}

            </div>

            {{-- Active Filter Chips --}}
            <div id="active-filters-bar" class="hidden flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                <span class="text-xs text-gray-400 font-medium">Filter Aktif:</span>
                <div id="active-filters-chips" class="flex flex-wrap items-center gap-1.5"></div>
            </div>
        </div>
    </div>

    {{-- Main Container --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        
        {{-- Navigation Tabs & Sorting --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            {{-- Tabs --}}
            <nav class="flex items-center gap-2" aria-label="Tabs">
                <button id="tab-produk" type="button" class="tab-btn active-tab bg-brand text-white font-bold px-5 py-2.5 rounded-full shadow-xs flex items-center gap-2 text-xs sm:text-sm transition-all cursor-pointer border-0">
                    <i data-lucide="package" class="w-4 h-4"></i>
                    <span>Produk Limbah</span>
                </button>
                <button id="tab-toko" type="button" class="tab-btn bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900 font-medium px-5 py-2.5 rounded-full flex items-center gap-2 text-xs sm:text-sm transition-all cursor-pointer border-0">
                    <i data-lucide="store" class="w-4 h-4"></i>
                    <span>Pengepul / Toko</span>
                </button>
            </nav>

            {{-- Result Count & Sorting --}}
            <div class="flex items-center justify-between sm:justify-end gap-4">
                <p id="result-count" class="text-xs sm:text-sm text-gray-500">
                    Menampilkan <span class="font-bold text-gray-900" id="count-number">0</span> <span id="count-label">produk</span>
                </p>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs text-gray-400 hidden sm:inline">Urutkan:</span>
                    <select id="sort-select" class="text-xs sm:text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-white text-gray-700 font-medium focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition cursor-pointer">
                        <option value="terbaru">Terbaru</option>
                        <option value="harga-asc">Harga Terendah</option>
                        <option value="harga-desc">Harga Tertinggi</option>
                        <option value="stok-desc">Stok Terbanyak</option>
                        <option value="jarak-asc">Jarak Terdekat</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Compact Card Grid --}}
        <div id="card-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4 mb-10"></div>
        
        {{-- Store Grid --}}
        <div id="toko-grid" class="hidden grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4 mb-10"></div>
        
        {{-- Pagination --}}
        <div id="pagination" class="flex items-center justify-center gap-1.5 mt-10"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let state = { 
        tab: 'produk', 
        search: '',
        categories: [], 
        searchLokasi: '', 
        volumeMin: null, 
        hargaMin: null, 
        hargaMax: null, 
        statusAvailable: true, 
        sort: 'terbaru', 
        page: 1, 
        perPage: 18 
    };

    function debounce(fn, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    async function fetchData() {
        showSkeletons(state.tab);
        updateActiveFilterChips();

        const params = new URLSearchParams();
        params.append('tab', state.tab);
        params.append('page', state.page);
        params.append('sort', state.sort);
        if (state.search) params.append('search', state.search);
        if (state.searchLokasi) params.append('lokasi', state.searchLokasi);
        if (state.volumeMin) params.append('volume_min', state.volumeMin);
        if (state.hargaMin) params.append('harga_min', state.hargaMin);
        if (state.hargaMax) params.append('harga_max', state.hargaMax);
        params.append('available_only', state.statusAvailable ? 1 : 0);
        state.categories.forEach(cat => params.append('categories[]', cat));

        try {
            const res = await fetch(`/marketplace?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const result = await res.json();
            
            const isProd = state.tab === 'produk';
            const cardGrid = document.getElementById('card-grid');
            const tokoGrid = document.getElementById('toko-grid');
            if (cardGrid) cardGrid.classList.toggle('hidden', !isProd);
            if (tokoGrid) tokoGrid.classList.toggle('hidden', isProd);

            if (isProd) {
                renderCards(result.data, result.total);
            } else {
                renderSellers(result.data, result.total);
            }
            renderPagination(result.total, result.last_page);
        } catch (err) {
            console.error("Error fetching data:", err);
            const isProd = state.tab === 'produk';
            const targetGrid = document.getElementById(isProd ? 'card-grid' : 'toko-grid');
            if (targetGrid) {
                targetGrid.innerHTML = `
                    <div class="col-span-full text-center py-16 border border-dashed border-gray-200 rounded-2xl bg-white p-8">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-2 text-gray-400">
                            <i data-lucide="alert-circle" class="w-6 h-6"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-700">Gagal memuat data</p>
                        <p class="text-xs text-gray-400 mt-1">Silakan coba beberapa saat lagi atau segarkan halaman.</p>
                    </div>`;
                if (window.lucide) lucide.createIcons();
            }
        }
    }

    function showSkeletons(type) {
        const isProd = type === 'produk';
        const cardGrid = document.getElementById('card-grid');
        const tokoGrid = document.getElementById('toko-grid');
        if (cardGrid) cardGrid.classList.toggle('hidden', !isProd);
        if (tokoGrid) tokoGrid.classList.toggle('hidden', isProd);

        const targetGrid = isProd ? cardGrid : tokoGrid;
        if (!targetGrid) return;

        let html = '';
        const count = state.perPage || 18;
        for (let i = 0; i < count; i++) {
            html += isProd ? `
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden animate-pulse flex flex-col h-full shadow-xs">
                <div class="aspect-square bg-gray-200/80 w-full"></div>
                <div class="p-3 flex flex-col gap-2 flex-1 justify-between">
                    <div class="space-y-1.5">
                        <div class="h-3.5 bg-gray-200 rounded-md w-full"></div>
                        <div class="h-3.5 bg-gray-200 rounded-md w-3/4"></div>
                    </div>
                    <div class="h-4 bg-brand/20 rounded-md w-1/2 mt-1"></div>
                    <div class="flex items-center justify-between mt-1">
                        <div class="h-3 bg-gray-100 rounded w-1/3"></div>
                        <div class="h-3 bg-gray-100 rounded w-1/4"></div>
                    </div>
                </div>
            </div>` : `
            <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-xs animate-pulse flex flex-col items-center text-center h-full">
                <div class="w-20 h-20 rounded-full bg-gray-200/80 mb-3"></div>
                <div class="h-4 bg-gray-200 rounded-md w-3/4 mb-2"></div>
                <div class="h-3 bg-gray-100 rounded-md w-1/2 mb-3"></div>
                <div class="h-6 bg-brand/10 rounded-full w-1/3 mb-4"></div>
                <div class="mt-auto h-9 bg-gray-100 rounded-xl w-full"></div>
            </div>`;
        }
        targetGrid.innerHTML = html;
        const pg = document.getElementById('pagination');
        if (pg) pg.innerHTML = '';
    }

    function renderCards(data, total) {
        const grid = document.getElementById('card-grid');
        document.getElementById('count-number').textContent = total;
        document.getElementById('count-label').textContent = 'produk limbah';
        
        if (data.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-20 border border-dashed border-gray-200 rounded-2xl bg-white p-8">
                    <div class="w-14 h-14 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="package-search" class="w-7 h-7 text-gray-300"></i>
                    </div>
                    <h4 class="text-sm font-bold text-gray-800">Tidak ada produk limbah ditemukan</h4>
                    <p class="text-xs text-gray-400 mt-1">Coba kata kunci lain atau reset filter pencarian Anda.</p>
                </div>`;
            if (window.lucide) lucide.createIcons();
            return;
        }
        
        grid.innerHTML = data.map(l => `
            <a href="/marketplace/${l.id}?ref=marketplace"
               class="group bg-transparent hover:bg-white rounded-xl overflow-hidden border border-gray-200/80 shadow-2xs hover:shadow-xl hover:shadow-brand/10 hover:-translate-y-1 hover:border-brand/40 transition-all duration-300 flex flex-col h-full">

                {{-- Square 1:1 Image Frame (Full edge-to-edge fit without whitespace) --}}
                <div class="relative w-full aspect-square bg-gray-100 overflow-hidden shrink-0">
                    <img
                        src="${l.image || 'https://placehold.co/400x400?text=Limbah'}"
                        alt="${l.title}"
                        loading="lazy"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out"
                        onerror="this.src='https://placehold.co/400x400?text=Limbah'">
                    <span class="absolute top-2 left-2 bg-brand/90 backdrop-blur-xs text-white text-[9px] font-bold px-2 py-0.5 rounded uppercase tracking-wide shadow-xs">
                        ${l.categoryLabel}
                    </span>
                </div>

                {{-- Card Body (Seamless Tokopedia Style) --}}
                <div class="p-2.5 sm:p-3 flex flex-col justify-between grow gap-1.5">
                    <div>
                        <h5 class="text-xs sm:text-sm font-normal text-gray-900 line-clamp-2 leading-snug group-hover:text-brand transition-colors duration-200">${l.title}</h5>
                        <p class="text-sm sm:text-base font-bold text-gray-900 mt-1">Rp ${l.price.toLocaleString('id-ID')}<span class="text-[11px] font-normal text-gray-400"> / ${l.unit}</span></p>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
                        <span class="truncate flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-3 h-3 text-gray-400 shrink-0"></i>
                            <span class="truncate">${l.city}</span>
                        </span>
                        <span class="shrink-0 font-normal text-gray-400">Stok: ${l.stock !== undefined ? l.stock.toLocaleString('id-ID') : '-'}</span>
                    </div>
                </div>
            </a>
        `).join('');
        if (window.lucide) lucide.createIcons();
    }

    function renderSellers(data, total) {
        const grid = document.getElementById('toko-grid');
        document.getElementById('count-number').textContent = total;
        document.getElementById('count-label').textContent = 'toko pengepul';

        if (data.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-20 border border-dashed border-gray-200 rounded-2xl bg-white p-8">
                    <div class="w-14 h-14 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="store" class="w-7 h-7 text-gray-300"></i>
                    </div>
                    <h4 class="text-sm font-bold text-gray-800">Tidak ada toko ditemukan</h4>
                    <p class="text-xs text-gray-400 mt-1">Coba kata kunci atau lokasi kota lain.</p>
                </div>`;
            if (window.lucide) lucide.createIcons();
            return;
        }
        grid.innerHTML = data.map(s => `
            <div class="group bg-transparent hover:bg-white border border-gray-200/80 rounded-xl p-4 shadow-2xs hover:shadow-xl hover:shadow-brand/10 hover:-translate-y-1 hover:border-brand/40 transition-all duration-300 flex flex-col items-center text-center h-full">
                <div class="w-20 h-20 rounded-full overflow-hidden mb-3 border-2 border-gray-100 shrink-0">
                    <img src="${s.avatar}" alt="${s.name}" class="w-full h-full object-cover">
                </div>
                <h5 class="text-sm font-bold text-gray-900 line-clamp-1 mb-1 group-hover:text-brand transition-colors">${s.name}</h5>
                <div class="flex items-center gap-1 text-xs text-gray-400 mb-3 truncate">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400 shrink-0"></i> 
                    <span class="truncate">${s.city}</span>
                </div>
                <span class="inline-block bg-brand/10 text-brand text-xs font-bold px-3 py-1 rounded-full mb-4">${s.type}</span>
                <a href="/toko/${s.id}" class="mt-auto block w-full text-xs font-bold border border-brand text-brand hover:bg-brand hover:text-white py-2 rounded-xl transition-colors text-center">Lihat Toko</a>
            </div>
        `).join('');
        if (window.lucide) lucide.createIcons();
    }

    function renderPagination(total, totalPages) {
        const pg = document.getElementById('pagination');
        if (totalPages <= 1) { pg.innerHTML = ''; return; }
        const btn = (active, content, onclick, disabled=false) =>
            `<button onclick="${onclick}" ${disabled?'disabled':''} class="w-9 h-9 flex items-center justify-center rounded-xl text-xs font-medium transition-all ${active ? 'bg-brand text-white shadow-xs font-bold' : 'bg-white border border-gray-200 text-gray-600 hover:border-brand hover:text-brand'} disabled:opacity-40 disabled:cursor-not-allowed">${content}</button>`;
        let html = btn(false, '&#8249;', `goPage(${state.page-1})`, state.page===1);
        for (let i = 1; i <= totalPages; i++) {
            if (i===1 || i===totalPages || (i>=state.page-1 && i<=state.page+1))
                html += btn(i===state.page, i, `goPage(${i})`);
            else if (i===state.page-2 || i===state.page+2)
                html += `<span class="w-9 h-9 flex items-center justify-center text-gray-400 text-xs">…</span>`;
        }
        html += btn(false, '&#8250;', `goPage(${state.page+1})`, state.page===totalPages);
        pg.innerHTML = html;
    }

    function updateActiveFilterChips() {
        const chipsContainer = document.getElementById('active-filters-chips');
        const bar = document.getElementById('active-filters-bar');
        let chips = [];

        state.categories = Array.from(new Set(state.categories));

        if (state.search) chips.push({ label: `Pencarian: "${state.search}"`, type: 'search' });
        if (state.searchLokasi) chips.push({ label: `Kota: ${state.searchLokasi}`, type: 'lokasi' });
        if (state.hargaMin) chips.push({ label: `Min: Rp ${state.hargaMin.toLocaleString('id-ID')}`, type: 'hargaMin' });
        if (state.hargaMax) chips.push({ label: `Max: Rp ${state.hargaMax.toLocaleString('id-ID')}`, type: 'hargaMax' });
        if (state.volumeMin) chips.push({ label: `Min Stok: ${state.volumeMin}`, type: 'volumeMin' });
        state.categories.forEach(cat => chips.push({ label: `Kategori: ${cat}`, type: 'category', value: cat }));

        if (chips.length > 0) {
            bar.classList.remove('hidden');
            chipsContainer.innerHTML = chips.map(c => `
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-brand/10 text-brand border border-brand/20">
                    ${c.label}
                    <button type="button" onclick="removeFilter('${c.type}', '${c.value || ''}')" class="hover:text-red-600 transition-colors">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </span>
            `).join('');
            if (window.lucide) lucide.createIcons();
        } else {
            bar.classList.add('hidden');
            chipsContainer.innerHTML = '';
        }
    }

    window.removeFilter = function(type, val) {
        if (type === 'search') { state.search = ''; document.getElementById('search-input').value = ''; }
        else if (type === 'lokasi') { state.searchLokasi = ''; document.getElementById('search-lokasi').value = ''; }
        else if (type === 'hargaMin') { state.hargaMin = null; document.getElementById('harga-min').value = ''; }
        else if (type === 'hargaMax') { state.hargaMax = null; document.getElementById('harga-max').value = ''; }
        else if (type === 'volumeMin') { state.volumeMin = null; document.getElementById('volume-min').value = ''; }
        else if (type === 'category') {
            state.categories = state.categories.filter(c => c !== val);
            document.querySelectorAll('.category-filter').forEach(cb => {
                if (cb.value === val) cb.checked = false;
            });
        }
        refresh();
    };

    window.goPage = function(p) {
        state.page = p;
        showSkeletons(state.tab);
        fetchData();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    function refresh() {
        state.page = 1;
        showSkeletons(state.tab);
        fetchData();
    }

    function updateTabUI(tab) {
        const isProd = tab === 'produk';
        const activeClass = "tab-btn active-tab bg-brand text-white font-bold px-5 py-2.5 rounded-full shadow-xs flex items-center gap-2 text-xs sm:text-sm transition-all cursor-pointer border-0";
        const inactiveClass = "tab-btn bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900 font-medium px-5 py-2.5 rounded-full flex items-center gap-2 text-xs sm:text-sm transition-all cursor-pointer border-0";
        
        const btnProduk = document.getElementById('tab-produk');
        const btnToko = document.getElementById('tab-toko');
        if (btnProduk) btnProduk.className = isProd ? activeClass : inactiveClass;
        if (btnToko) btnToko.className = !isProd ? activeClass : inactiveClass;
        
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.placeholder = isProd 
                ? "Cari limbah (misal: plastik, kertas, logam)..." 
                : "Cari pengepul / toko (misal: UD Jaya, Semarang)...";
        }

        const cardGrid = document.getElementById('card-grid');
        const tokoGrid = document.getElementById('toko-grid');
        if (cardGrid) cardGrid.classList.toggle('hidden', !isProd);
        if (tokoGrid) tokoGrid.classList.toggle('hidden', isProd);

        ['filter-harga-section', 'filter-volume-section', 'filter-status-section'].forEach(id => {
            const section = document.getElementById(id);
            if (section) section.style.display = isProd ? 'block' : 'none';
        });
    }

    function initMarketplace() {
        const el = id => document.getElementById(id);
        if (!el('card-grid')) return;

        // Parse initial URL query params to sync tab state on refresh or direct links
        const urlParams = new URLSearchParams(window.location.search);
        const initialTab = urlParams.get('tab') === 'toko' ? 'toko' : 'produk';
        state.tab = initialTab;

        const initialSearch = urlParams.get('search') || urlParams.get('q') || '';
        if (initialSearch) {
            state.search = initialSearch;
            const sInput = el('search-input');
            if (sInput) {
                sInput.value = initialSearch;
                if (el('btn-search-clear')) el('btn-search-clear').classList.remove('hidden');
            }
        }

        updateTabUI(initialTab);

        const toggleTabs = (tab) => {
            if (state.tab === tab) return;
            state.tab = tab;
            state.page = 1;

            const url = new URL(window.location.href);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url.toString());

            updateTabUI(tab);
            showSkeletons(tab);
            fetchData();
        };

        if (el('tab-produk')) el('tab-produk').onclick = () => toggleTabs('produk');
        if (el('tab-toko')) el('tab-toko').onclick = () => toggleTabs('toko');

        const debouncedRefresh = debounce(refresh, 350);

        // Search bar inputs with live debounced search
        const searchInput = el('search-input');
        const searchClear = el('btn-search-clear');
        
        if (searchInput) {
            searchInput.oninput = e => {
                state.search = e.target.value;
                if (searchClear) searchClear.classList.toggle('hidden', !e.target.value);
                state.page = 1;
                showSkeletons(state.tab);
                debouncedRefresh();
            };
            searchInput.onkeydown = e => {
                if (e.key === 'Enter') refresh();
            };
        }
        
        if (searchClear) {
            searchClear.onclick = () => {
                if (searchInput) searchInput.value = '';
                state.search = '';
                searchClear.classList.add('hidden');
                state.page = 1;
                refresh();
            };
        }

        // Filter dropdown Tokopedia-style hover & click toggle
        const filterContainer = el('filter-dropdown-container');
        const filterTrigger = el('btn-filter-trigger');
        const filterMenu = el('filter-dropdown-menu');
        const filterChevron = el('filter-chevron');

        let dropdownTimeout;

        function openFilterMenu() {
            clearTimeout(dropdownTimeout);
            filterMenu?.classList.remove('hidden');
            if (filterChevron) filterChevron.classList.add('rotate-180');
        }

        function closeFilterMenu() {
            dropdownTimeout = setTimeout(() => {
                filterMenu?.classList.add('hidden');
                if (filterChevron) filterChevron.classList.remove('rotate-180');
            }, 200);
        }

        if (filterContainer) {
            filterContainer.onmouseenter = openFilterMenu;
            filterContainer.onmouseleave = closeFilterMenu;
        }

        if (filterTrigger) {
            filterTrigger.onclick = (e) => {
                e.stopPropagation();
                const isHidden = filterMenu?.classList.contains('hidden');
                if (isHidden) openFilterMenu();
                else closeFilterMenu();
            };
        }

        document.onclick = (e) => {
            if (!filterContainer?.contains(e.target)) {
                closeFilterMenu();
            }
        };

        // Category checkboxes
        document.querySelectorAll('.category-filter').forEach(cb => {
            cb.onchange = e => {
                const val = e.target.value;
                if (e.target.checked) {
                    if (!state.categories.includes(val)) state.categories.push(val);
                } else {
                    state.categories = state.categories.filter(c => c !== val);
                }
                state.categories = Array.from(new Set(state.categories));
                state.page = 1;
                showSkeletons(state.tab);
                debouncedRefresh();
            };
        });

        if (el('search-lokasi')) el('search-lokasi').oninput = e => { state.searchLokasi = e.target.value; state.page = 1; showSkeletons(state.tab); debouncedRefresh(); };
        if (el('volume-min')) el('volume-min').oninput = e => { state.volumeMin = e.target.value ? parseFloat(e.target.value) : null; state.page = 1; showSkeletons(state.tab); debouncedRefresh(); };
        if (el('harga-min')) el('harga-min').oninput = e => { state.hargaMin = e.target.value ? parseInt(e.target.value) : null; state.page = 1; showSkeletons(state.tab); debouncedRefresh(); };
        if (el('harga-max')) el('harga-max').oninput = e => { state.hargaMax = e.target.value ? parseInt(e.target.value) : null; state.page = 1; showSkeletons(state.tab); debouncedRefresh(); };
        if (el('filter-status')) el('filter-status').onchange = e => { state.statusAvailable = e.target.checked; state.page = 1; refresh(); };
        if (el('sort-select')) el('sort-select').onchange = e => { state.sort = e.target.value; state.page = 1; refresh(); };

        if (el('btn-apply-dropdown')) {
            el('btn-apply-dropdown').onclick = () => {
                filterMenu?.classList.add('hidden');
                if (filterChevron) filterChevron.classList.remove('rotate-180');
                refresh();
            };
        }

        if (el('btn-reset-dropdown')) {
            el('btn-reset-dropdown').onclick = () => {
                state = { ...state, search: '', categories: [], searchLokasi: '', volumeMin: null, hargaMin: null, hargaMax: null, statusAvailable: true, page: 1 };
                if (searchInput) searchInput.value = '';
                if (searchClear) searchClear.classList.add('hidden');
                document.querySelectorAll('.category-filter').forEach(cb => cb.checked = false);
                ['search-lokasi', 'volume-min', 'harga-min', 'harga-max'].forEach(id => { if (el(id)) el(id).value = ''; });
                if (el('filter-status')) el('filter-status').checked = true;
                refresh();
            };
        }

        refresh();
    }

    let isMarketplaceInit = false;
    function safeInitMarketplace() {
        if (isMarketplaceInit) return;
        isMarketplaceInit = true;
        initMarketplace();
    }

    document.addEventListener('turbo:load', () => { isMarketplaceInit = false; safeInitMarketplace(); });
    if (document.readyState !== 'loading') {
        safeInitMarketplace();
    } else {
        document.addEventListener('DOMContentLoaded', safeInitMarketplace, { once: true });
    }
</script>
@endpush
