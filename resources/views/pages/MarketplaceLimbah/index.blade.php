@extends('layouts.master')
@section('title', 'Marketplace Limbah – Recyclink')
@section('content')
<div class="bg-gray-50 min-h-screen">
    {{-- Page Header --}}
    <div class="border-b border-gray-100 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight mb-2">Marketplace Limbah</h1>
            <p class="text-gray-500 text-sm md:text-base max-w-xl">
                Temukan limbah industri berkualitas dari seller terverifikasi untuk kebutuhan produksi berkelanjutan Anda.
            </p>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- SIDEBAR FILTER --}}
            <aside class="w-full lg:w-64 shrink-0">
                <div class="bg-white border border-gray-200 rounded-2xl p-5 sticky top-24">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2">
                            <i data-lucide="sliders-horizontal" class="w-4 h-4 text-gray-500"></i>
                            <span class="font-semibold text-gray-900">Filter</span>
                        </div>
                        <button id="btn-reset" class="text-xs font-medium text-brand hover:underline">Reset</button>
                    </div>
                    {{-- Kategori --}}
                    <div class="mb-6">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Kategori</p>
                        <div class="space-y-2.5" id="filter-kategori">
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="kategori" value="logam" class="accent-brand w-4 h-4 rounded cursor-pointer">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">Logam & Metal</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="kategori" value="plastik" class="accent-brand w-4 h-4 rounded cursor-pointer">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">Plastik</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="kategori" value="kertas" class="accent-brand w-4 h-4 rounded cursor-pointer">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">Kertas & Karton</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="kategori" value="elektronik" class="accent-brand w-4 h-4 rounded cursor-pointer">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">Elektronik (E-Waste)</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="kategori" value="kayu" class="accent-brand w-4 h-4 rounded cursor-pointer">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">Kayu & Biomassa</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="kategori" value="tekstil" class="accent-brand w-4 h-4 rounded cursor-pointer">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">Tekstil & Kain</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="kategori" value="minyak" class="accent-brand w-4 h-4 rounded cursor-pointer">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">Minyak & Cairan</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="kategori" value="organik" class="accent-brand w-4 h-4 rounded cursor-pointer">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">Limbah Organik</span>
                            </label>
                        </div>
                    </div>
                    {{-- Harga --}}
                    <div class="mb-6">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Harga (Rp)</p>
                        <div class="flex items-center gap-2">
                            <input type="number" id="harga-min" placeholder="Min"
                                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition placeholder-gray-400">
                            <span class="text-gray-300 shrink-0">–</span>
                            <input type="number" id="harga-max" placeholder="Max"
                                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition placeholder-gray-400">
                        </div>
                    </div>
                    {{-- Satuan --}}
                    <div class="mb-6">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Satuan</p>
                        <div class="flex flex-wrap gap-2" id="filter-satuan">
                            <button data-satuan="semua" class="satuan-btn text-xs font-medium border px-3 py-1.5 rounded-lg transition-all bg-brand text-white border-brand">Semua</button>
                            <button data-satuan="kg"    class="satuan-btn text-xs font-medium border px-3 py-1.5 rounded-lg transition-all bg-white text-gray-600 border-gray-200 hover:border-brand hover:text-brand">kg</button>
                            <button data-satuan="liter" class="satuan-btn text-xs font-medium border px-3 py-1.5 rounded-lg transition-all bg-white text-gray-600 border-gray-200 hover:border-brand hover:text-brand">liter</button>
                            <button data-satuan="pcs"   class="satuan-btn text-xs font-medium border px-3 py-1.5 rounded-lg transition-all bg-white text-gray-600 border-gray-200 hover:border-brand hover:text-brand">pcs</button>
                            <button data-satuan="ton"   class="satuan-btn text-xs font-medium border px-3 py-1.5 rounded-lg transition-all bg-white text-gray-600 border-gray-200 hover:border-brand hover:text-brand">ton</button>
                        </div>
                    </div>
                    <button id="btn-apply"
                        class="w-full bg-brand hover:bg-brand-hover text-white font-semibold text-sm py-2.5 rounded-xl transition-colors">
                        Terapkan Filter
                    </button>
                </div>
            </aside>
            {{-- MAIN CONTENT --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                    <p id="result-count" class="text-sm text-gray-500">
                        Menampilkan <span class="font-semibold text-gray-800" id="count-number">18</span> produk limbah
                    </p>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-sm text-gray-500">Urutkan:</span>
                        <select id="sort-select"
                            class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition cursor-pointer">
                            <option value="terbaru">Terbaru</option>
                            <option value="harga-asc">Harga Terendah</option>
                            <option value="harga-desc">Harga Tertinggi</option>
                            <option value="stok-desc">Stok Terbanyak</option>
                        </select>
                    </div>
                </div>
                <div id="card-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8"></div>
                <div id="pagination" class="flex items-center justify-center gap-1.5 mt-8"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ALL_LISTINGS = [
    { id: 1,  title: 'Styrofoam / EPS Bekas – Bongkar Gudang',         category: 'plastik',   categoryLabel: 'Plastik',        city: 'Surakarta',  price: 3000,  unit: 'kg',    stock: 8000,   desc: 'Styrofoam / EPS bekas dari pembongkaran gudang. Kondisi bersih, siap dikirim dalam jumlah besar. Cocok untuk daur ulang dan produksi ulang bahan bangunan.', seller: 'CV Maju Jaya', kondisi: 'Bekas layak pakai', moq: '500 kg', image: 'https://images.unsplash.com/photo-1628177142898-93e36e4e3a50?w=600&q=80' },
    { id: 2,  title: 'Sekam Padi – Bahan Bakar Boiler',                 category: 'organik',   categoryLabel: 'Limbah Organik', city: 'Madiun',     price: 800,   unit: 'kg',    stock: 100000, desc: 'Sekam padi kering hasil penggilingan. Kalori tinggi, cocok sebagai bahan bakar boiler industri atau campuran pakan ternak.', seller: 'UD Tani Makmur', kondisi: 'Kering & bersih', moq: '1000 kg', image: 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=600&q=80' },
    { id: 3,  title: 'E-Waste Kabel & PCB Campuran',                    category: 'elektronik',categoryLabel: 'Elektronik',     city: 'Tangerang',  price: 18000, unit: 'kg',    stock: 3000,   desc: 'Limbah elektronik berupa kabel campuran dan PCB bekas. Mengandung tembaga, aluminium, dan logam berharga lainnya.', seller: 'PT Recycle Tech', kondisi: 'Campuran', moq: '100 kg', image: 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&q=80' },
    { id: 4,  title: 'Kain Perca Katun – Sisa Konveksi',                category: 'tekstil',   categoryLabel: 'Tekstil & Kain', city: 'Solo',       price: 1500,  unit: 'kg',    stock: 5000,   desc: 'Sisa kain perca katun dari konveksi pakaian. Berwarna-warni, cocok untuk kerajinan, keset, atau bahan isian.', seller: 'Konveksi Solo Jaya', kondisi: 'Sisa produksi', moq: '50 kg', image: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80' },
    { id: 5,  title: 'Kardus & Karton Bekas Pabrik – Grade A',          category: 'kertas',    categoryLabel: 'Kertas & Karton',city: 'Yogyakarta', price: 2200,  unit: 'kg',    stock: 20000,  desc: 'Kardus dan karton bekas dari pabrik. Grade A – kondisi masih kuat dan layak press. Tersedia dalam jumlah besar dan konsisten.', seller: 'PT Karton Nusantara', kondisi: 'Grade A', moq: '200 kg', image: 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=600&q=80' },
    { id: 6,  title: 'Plastik PET Botol Bersih Siap Cacah',             category: 'plastik',   categoryLabel: 'Plastik PET',   city: 'Depok',      price: 5500,  unit: 'kg',    stock: 15000,  desc: 'Plastik PET dari botol minuman yang telah dipilah dan dibersihkan. Siap masuk mesin pencacah. Tidak ada campuran sampah lain.', seller: 'Bank Sampah Depok', kondisi: 'Bersih terpilah', moq: '300 kg', image: 'https://images.unsplash.com/photo-1530587191325-3db32d826c18?w=600&q=80' },
    { id: 7,  title: 'Besi Tua Campuran – Ton Timbang di Tempat',       category: 'logam',     categoryLabel: 'Besi & Baja',   city: 'Medan',      price: 3800,  unit: 'kg',    stock: 30000,  desc: 'Besi tua campuran dari bongkaran bangunan dan mesin pabrik. Timbang di tempat. Harga bisa negosiasi untuk volume besar.', seller: 'Besi Tua Sumatera', kondisi: 'Campuran bongkaran', moq: '1000 kg', image: 'https://images.unsplash.com/photo-1558346547-4439467bd1d5?w=600&q=80' },
    { id: 8,  title: 'Limbah Tembaga Kabel Stripping – Kadar Tinggi',   category: 'logam',     categoryLabel: 'Tembaga',       city: 'Bekasi',     price: 72000, unit: 'kg',    stock: 2000,   desc: 'Tembaga murni hasil stripping kabel industri. Kadar tinggi, cocok untuk peleburan ulang. Tersedia sertifikat kadar.', seller: 'PT Metal Prima', kondisi: 'Siap lebur', moq: '50 kg', image: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80' },
    { id: 9,  title: 'Serbuk Kayu Halus – Biomassa Energi',             category: 'kayu',      categoryLabel: 'Kayu & Biomassa',city: 'Bandung',    price: 400,   unit: 'kg',    stock: 50000,  desc: 'Serbuk kayu halus dari industri meubel dan pengergajian. Kering, kadar air rendah. Ideal untuk briket atau bahan bakar biomassa.', seller: 'UD Kayu Lestari', kondisi: 'Kering', moq: '2000 kg', image: 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=600&q=80' },
    { id: 10, title: 'Aluminium Kaleng Cacah – Siap Lebur',             category: 'logam',     categoryLabel: 'Aluminium',     city: 'Tangerang',  price: 14500, unit: 'kg',    stock: 8000,   desc: 'Kaleng aluminium bekas minuman yang sudah dicacah dan dibersihkan. Siap masuk tungku peleburan. Kemurnian tinggi.', seller: 'CV Aluminium Utama', kondisi: 'Cacah bersih', moq: '200 kg', image: 'https://images.unsplash.com/photo-1558346547-4439467bd1d5?w=600&q=80' },
    { id: 11, title: 'Minyak Jelantah (UCO) – Food Grade',              category: 'minyak',    categoryLabel: 'Minyak',        city: 'Bandung',    price: 8500,  unit: 'liter', stock: 10000,  desc: 'Minyak jelantah dari restoran dan industri makanan. Sudah disaring, cocok untuk produksi biodiesel atau sabun industri.', seller: 'CV Energi Hijau', kondisi: 'Tersaring', moq: '200 liter', image: 'https://images.unsplash.com/photo-1510498468133-c97f0e0dcdbe?w=600&q=80' },
    { id: 12, title: 'Limbah Kardus Press – Grade B',                   category: 'kertas',    categoryLabel: 'Kardus',        city: 'Medan',      price: 2800,  unit: 'kg',    stock: 25000,  desc: 'Kardus bekas dalam kondisi press dan terikat rapi. Grade B dengan sedikit campuran kertas. Siap kirim ke pabrik kertas.', seller: 'Pengepul Medan Baru', kondisi: 'Press & ikat', moq: '500 kg', image: 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=600&q=80' },
    { id: 13, title: 'Ban Bekas Truk & Alat Berat',                     category: 'plastik',   categoryLabel: 'Ban Bekas',     city: 'Surabaya',   price: 35000, unit: 'pcs',   stock: 500,    desc: 'Ban bekas truk dan alat berat. Kondisi layak retreading atau untuk crumb rubber. Ukuran bervariasi, tersedia pilihan.', seller: 'UD Ban Jaya', kondisi: 'Layak retreading', moq: '10 pcs', image: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80' },
    { id: 14, title: 'Serbuk Besi (Iron Powder) Sisa Produksi',         category: 'logam',     categoryLabel: 'Logam',         city: 'Cikarang',   price: 6200,  unit: 'kg',    stock: 12000,  desc: 'Serbuk besi sisa mesin gerinda pabrik. Kemurnian besi tinggi, cocok untuk industri metalurgi dan magnet.', seller: 'PT Cikarang Metal', kondisi: 'Serbuk halus', moq: '100 kg', image: 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&q=80' },
    { id: 15, title: 'Limbah Kaca Pecah – Industri Botol',              category: 'elektronik',categoryLabel: 'Kaca',          city: 'Semarang',   price: 1200,  unit: 'kg',    stock: 40000,  desc: 'Pecahan kaca dari industri botol dan kemasan. Bersih tanpa campuran keramik. Siap masuk dapur peleburan kaca.', seller: 'CV Kaca Semarang', kondisi: 'Bersih', moq: '500 kg', image: 'https://images.unsplash.com/photo-1628177142898-93e36e4e3a50?w=600&q=80' },
    { id: 16, title: 'Ampas Tebu (Bagasse) – Kering',                   category: 'organik',   categoryLabel: 'Limbah Organik',city: 'Malang',     price: 600,   unit: 'kg',    stock: 80000,  desc: 'Ampas tebu kering dari pabrik gula. Nilai kalori baik untuk bahan bakar boiler. Tersedia dalam karung atau curah.', seller: 'PG Malang Baru', kondisi: 'Kering curah', moq: '3000 kg', image: 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=600&q=80' },
    { id: 17, title: 'Kulit Imitasi Sisa Jahit – Lembaran',             category: 'tekstil',   categoryLabel: 'Tekstil',       city: 'Sidoarjo',   price: 3500,  unit: 'kg',    stock: 3000,   desc: 'Sisa lembaran kulit imitasi (PU leather) dari pabrik tas dan sepatu. Warna bervariasi, bisa untuk kerajinan kecil atau sol sepatu.', seller: 'Pabrik Tas Sidoarjo', kondisi: 'Sisa potong', moq: '50 kg', image: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80' },
    { id: 18, title: 'Plastik HDPE Giling – Natural / Putih',           category: 'plastik',   categoryLabel: 'Plastik HDPE',  city: 'Bogor',      price: 7800,  unit: 'kg',    stock: 18000,  desc: 'Plastik HDPE giling dari botol susu dan jerigen. Warna natural/putih bersih, kemurnian tinggi. Siap masuk extruder.', seller: 'CV Plastik Bogor', kondisi: 'Giling bersih', moq: '200 kg', image: 'https://images.unsplash.com/photo-1530587191325-3db32d826c18?w=600&q=80' },
];
let state = { kategori:[], satuan:'semua', hargaMin:null, hargaMax:null, sort:'terbaru', page:1, perPage:9 };
function applyFilters() {
    let data = [...ALL_LISTINGS];
    if (state.kategori.length > 0) data = data.filter(l => state.kategori.includes(l.category));
    if (state.satuan   !== 'semua') data = data.filter(l => l.unit === state.satuan);
    if (state.hargaMin !== null)    data = data.filter(l => l.price >= state.hargaMin);
    if (state.hargaMax !== null)    data = data.filter(l => l.price <= state.hargaMax);
    if (state.sort === 'harga-asc')  data.sort((a,b) => a.price - b.price);
    if (state.sort === 'harga-desc') data.sort((a,b) => b.price - a.price);
    if (state.sort === 'stok-desc')  data.sort((a,b) => b.stock - a.stock);
    return data;
}
function renderCards(data) {
    const grid = document.getElementById('card-grid');
    const start = (state.page - 1) * state.perPage;
    const slice = data.slice(start, start + state.perPage);
    document.getElementById('count-number').textContent = data.length;
    if (slice.length === 0) {
        grid.innerHTML = `<div class="col-span-3 text-center py-24 border border-dashed border-gray-200 rounded-2xl bg-white">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/></svg>
            <p class="text-sm text-gray-400">Tidak ada produk ditemukan.</p></div>`;
        return;
    }
    grid.innerHTML = slice.map(l => `
        <a href="/marketplace/${l.id}" class="group bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 flex flex-col">
            <div class="relative h-52 bg-gray-100 shrink-0 overflow-hidden">
                <img src="${l.image}" alt="${l.title}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                     onerror="this.style.display='none'">
                <span class="absolute top-3 left-3 bg-brand text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full shadow-sm">${l.categoryLabel}</span>
            </div>
            <div class="p-4 flex flex-col grow">
                <h5 class="text-base font-bold text-gray-900 line-clamp-2 leading-snug mb-1 group-hover:text-brand transition-colors">${l.title}</h5>
                <div class="flex items-center gap-1 text-xs text-gray-400 mb-4">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>${l.city}</span>
                </div>
                <div class="grow"></div>
                <div class="flex items-end justify-between gap-3">
                    <div>
                        <p class="text-xl font-bold text-brand leading-tight">
                            Rp ${l.price.toLocaleString('id-ID')} <span class="text-xs font-normal text-gray-400">/ ${l.unit}</span>
                        </p>
                        <div class="flex items-center gap-1 text-xs text-gray-400 mt-0.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            Stok: ${l.stock.toLocaleString('id-ID')} ${l.unit}
                        </div>
                    </div>
                    <span class="shrink-0 text-xs font-semibold border border-gray-200 text-gray-600 group-hover:bg-brand group-hover:text-white group-hover:border-brand px-4 py-1.5 rounded-lg transition-all">Detail</span>
                </div>
            </div>
        </a>`).join('');
}
function renderPagination(total) {
    const totalPages = Math.ceil(total / state.perPage);
    const pg = document.getElementById('pagination');
    if (totalPages <= 1) { pg.innerHTML = ''; return; }
    const btn = (active, content, onclick, disabled=false) =>
        `<button onclick="${onclick}" ${disabled?'disabled':''} class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-all ${active ? 'bg-brand text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:border-brand hover:text-brand'} disabled:opacity-40 disabled:cursor-not-allowed">${content}</button>`;
    let html = btn(false, '&#8249;', `goPage(${state.page-1})`, state.page===1);
    for (let i = 1; i <= totalPages; i++) {
        if (i===1 || i===totalPages || (i>=state.page-1 && i<=state.page+1))
            html += btn(i===state.page, i, `goPage(${i})`);
        else if (i===state.page-2 || i===state.page+2)
            html += `<span class="w-9 h-9 flex items-center justify-center text-gray-400 text-sm">…</span>`;
    }
    html += btn(false, '&#8250;', `goPage(${state.page+1})`, state.page===totalPages);
    pg.innerHTML = html;
}
window.goPage = function(p) {
    const filtered = applyFilters();
    const max = Math.ceil(filtered.length / state.perPage);
    if (p < 1 || p > max) return;
    state.page = p;
    renderCards(filtered);
    renderPagination(filtered.length);
    window.scrollTo({ top: 0, behavior: 'smooth' });
};
function refresh() {
    state.page = 1;
    const filtered = applyFilters();
    renderCards(filtered);
    renderPagination(filtered.length);
}
document.addEventListener('DOMContentLoaded', () => {
    function updateKategoriState() {
        state.kategori = Array.from(document.querySelectorAll('input[name="kategori"]:checked')).map(cb => cb.value);
    }
    document.querySelectorAll('input[name="kategori"]').forEach(r => r.addEventListener('change', updateKategoriState));

    function setSatuan(val) {
        state.satuan = val;
        document.querySelectorAll('.satuan-btn').forEach(b => {
            const active = b.dataset.satuan === val;
            if (active) {
                b.classList.add('bg-brand', 'text-white', 'border-brand');
                b.classList.remove('bg-white', 'text-gray-600', 'border-gray-200');
            } else {
                b.classList.remove('bg-brand', 'text-white', 'border-brand');
                b.classList.add('bg-white', 'text-gray-600', 'border-gray-200');
            }
        });
    }

    document.querySelectorAll('.satuan-btn').forEach(btn => {
        btn.addEventListener('click', () => setSatuan(btn.dataset.satuan));
    });

    document.getElementById('harga-min').addEventListener('input', e => state.hargaMin = e.target.value ? parseInt(e.target.value) : null);
    document.getElementById('harga-max').addEventListener('input', e => state.hargaMax = e.target.value ? parseInt(e.target.value) : null);
    document.getElementById('sort-select').addEventListener('change', e => { state.sort = e.target.value; refresh(); });
    document.getElementById('btn-apply').addEventListener('click', refresh);

    document.getElementById('btn-reset').addEventListener('click', () => {
        state = { ...state, kategori:[], satuan:'semua', hargaMin:null, hargaMax:null, page:1 };
        document.querySelectorAll('input[name="kategori"]').forEach(cb => cb.checked = false);
        document.getElementById('harga-min').value = '';
        document.getElementById('harga-max').value = '';
        setSatuan('semua');
        refresh();
    });

    refresh();
});
</script>
@endpush
