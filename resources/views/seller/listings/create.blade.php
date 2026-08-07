@extends('seller.layouts.seller')

@section('title', 'Tambah Listing Limbah - Recyclink')
@section('header_title', 'Tambah Listing')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('seller.listings.index') }}" class="p-2 bg-white rounded-xl shadow-sm border border-gray-100 text-gray-500 hover:text-brand transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h3 class="text-2xl font-bold text-gray-900">Tambah Listing Limbah</h3>
            <p class="text-gray-600 mt-1">Isi detail limbah yang ingin Anda jual. Admin akan memverifikasi sebelum tayang.</p>
        </div>
    </div>

    <form id="listing-form" action="{{ route('seller.listings.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden" novalidate>
        @csrf
        
        <div class="p-6 md:p-8 space-y-6">
            {{-- Jenis Limbah --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <label class="block font-bold text-gray-900 mb-1">Judul & Kategori <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-500">Tulis nama spesifik limbah Anda dan pilih kategori utamanya.</p>
                </div>
                <div class="md:col-span-2">
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-xs font-semibold text-gray-600">Judul Listing</label>
                            <span id="title-counter" class="text-[11px] text-gray-400">0 / 150</span>
                        </div>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Contoh: Minyak Jelantah Bening Bekas Restoran" minlength="5" maxlength="150" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3 transition-colors" required>
                        <p id="title-error" class="text-red-500 text-xs mt-1 hidden"></p>
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kategori Limbah</label>
                        <select id="category_id" name="category_id" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3 transition-colors" required>
                            <option value="">-- Pilih Kategori Utama --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                        <p id="category-error" class="text-red-500 text-xs mt-1 hidden"></p>
                        @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Deskripsi --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <label class="block font-bold text-gray-900 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-500">Jelaskan kondisi, kebersihan, dan sumber limbah secara rinci.</p>
                </div>
                <div class="md:col-span-2">
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-semibold text-gray-600">Detail Rincian Limbah</label>
                        <span id="desc-counter" class="text-[11px] text-gray-400">0 karakter</span>
                    </div>
                    <textarea id="description" name="description" rows="4" minlength="10" placeholder="Contoh: Minyak jelantah dari usaha gorengan, disaring secara rutin setiap 2 hari. Warna bening kecokelatan tanpa ampas..." class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3 transition-colors" required>{{ old('description') }}</textarea>
                    <p id="desc-error" class="text-red-500 text-xs mt-1 hidden"></p>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Volume & Harga --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <label class="block font-bold text-gray-900 mb-1">Volume & Harga <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-500">Tentukan jumlah stok limbah yang tersedia dan harga per satuan.</p>
                </div>
                <div class="md:col-span-2 space-y-4">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Volume Stok</label>
                            <input type="number" step="0.01" min="0.01" id="quantity" name="quantity" value="{{ old('quantity') }}" placeholder="Misal: 20" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3 transition-colors" required>
                            <p id="quantity-error" class="text-red-500 text-xs mt-1 hidden"></p>
                            @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Satuan</label>
                            <select id="unit" name="unit" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3 transition-colors" required>
                                <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kg (Kilogram)</option>
                                <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liter</option>
                                <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pcs / Buah</option>
                                <option value="karung" {{ old('unit') == 'karung' ? 'selected' : '' }}>Karung</option>
                            </select>
                            @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Harga per Satuan (Rp)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm font-medium">Rp</span>
                            </div>
                            <input type="number" id="price_per_unit" name="price_per_unit" value="{{ old('price_per_unit') }}" min="1000" placeholder="Contoh: 4000" class="w-full pl-10 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3 transition-colors" required>
                        </div>
                        <div class="flex justify-between items-center mt-1">
                            <p id="price-error" class="text-red-500 text-xs hidden"></p>
                            <p id="price-preview" class="text-xs text-brand font-semibold ml-auto hidden"></p>
                        </div>
                        @error('price_per_unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Alamat Pengambilan --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <label class="block font-bold text-gray-900 mb-1">Alamat Pengambilan <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-500">Alamat lengkap titik penjemputan limbah oleh pembeli.</p>
                </div>
                <div class="md:col-span-2">
                    <textarea id="address" name="address" rows="3" minlength="5" placeholder="Jl. Tembalang Raya No. 20..." class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3 transition-colors" required>{{ old('address', auth()->user()->sellerProfile->address ?? '') }}</textarea>
                    <p id="address-error" class="text-red-500 text-xs mt-1 hidden"></p>
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    <div class="mt-3">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kota / Kabupaten</label>
                        <input type="text" id="city" name="city" value="{{ old('city', auth()->user()->sellerProfile->city ?? '') }}" placeholder="Contoh: Semarang" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3 transition-colors" required>
                        <p id="city-error" class="text-red-500 text-xs mt-1 hidden"></p>
                        @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Foto --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <label class="block font-bold text-gray-900 mb-1">Foto Limbah <span class="text-gray-400 font-normal">(Opsional)</span></label>
                    <p class="text-xs text-gray-500">Unggah foto limbah asli. Maksimal 2MB per file (Format: JPG, PNG, WEBP).</p>
                </div>
                <div class="md:col-span-2">
                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-brand transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i data-lucide="upload-cloud" class="w-8 h-8 text-gray-400 mb-3"></i>
                                <p class="mb-1 text-sm text-gray-500 font-semibold"><span class="text-brand">Klik untuk unggah</span> atau seret file kesini</p>
                                <p class="text-xs text-gray-500">PNG, JPG, JPEG, WEBP (Max 2MB per file)</p>
                            </div>
                            <input id="dropzone-file" type="file" name="images[]" multiple accept="image/png, image/jpeg, image/jpg, image/webp" class="hidden" />
                        </label>
                    </div>
                    <p id="image-error" class="text-red-500 text-xs mt-1 hidden"></p>
                    @error('images.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <div id="file-list" class="mt-3 text-sm text-gray-600 font-medium"></div>
                </div>
            </div>

        </div>

        <div class="p-6 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
            <a href="{{ route('seller.listings.index') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">Batal</a>
            <button type="submit" id="btn-submit" class="px-6 py-2.5 bg-brand text-white font-bold rounded-xl hover:bg-brand-hover transition-colors shadow-sm flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Listing
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('listing-form');
        const titleInput = document.getElementById('title');
        const titleCounter = document.getElementById('title-counter');
        const descInput = document.getElementById('description');
        const descCounter = document.getElementById('desc-counter');
        const priceInput = document.getElementById('price_per_unit');
        const pricePreview = document.getElementById('price-preview');
        const fileInput = document.getElementById('dropzone-file');
        const fileList = document.getElementById('file-list');

        // Title character counter & validation
        titleInput?.addEventListener('input', function() {
            const len = this.value.length;
            titleCounter.textContent = `${len} / 150`;
            if (len < 5 && len > 0) {
                showError('title', 'Judul minimal 5 karakter');
            } else {
                hideError('title');
            }
        });

        // Description character counter & validation
        descInput?.addEventListener('input', function() {
            const len = this.value.length;
            descCounter.textContent = `${len} karakter`;
            if (len < 10 && len > 0) {
                showError('desc', 'Deskripsi minimal 10 karakter');
            } else {
                hideError('desc');
            }
        });

        // Price preview & validation
        priceInput?.addEventListener('input', function() {
            const val = parseInt(this.value);
            if (!isNaN(val) && val > 0) {
                const unitVal = document.getElementById('unit')?.value || 'kg';
                pricePreview.textContent = `Preview: Rp ${val.toLocaleString('id-ID')} / ${unitVal}`;
                pricePreview.classList.remove('hidden');
                if (val < 1000) {
                    showError('price', 'Harga per satuan minimal Rp 1.000');
                } else {
                    hideError('price');
                }
            } else {
                pricePreview.classList.add('hidden');
                hideError('price');
            }
        });

        // File dropzone validation
        fileInput?.addEventListener('change', function() {
            const validExts = ['jpg', 'jpeg', 'png', 'webp', 'heic', 'heif'];
            const maxBytes = 10 * 1024 * 1024; // 10MB limit for mobile photos
            let hasError = false;
            let errorMessage = '';

            const selectedFiles = Array.from(this.files);

            for (let i = 0; i < selectedFiles.length; i++) {
                const file = selectedFiles[i];
                const ext = file.name.split('.').pop().toLowerCase();
                const isImage = file.type ? file.type.toLowerCase().startsWith('image/') : true;

                if (!isImage || !validExts.includes(ext)) {
                    hasError = true;
                    errorMessage = `File "${file.name}" tidak didukung! Format harus JPG, PNG, WEBP, atau HEIC.`;
                    break;
                }
                if (file.size > maxBytes) {
                    hasError = true;
                    errorMessage = `File "${file.name}" melebihi ukuran maksimal 10MB!`;
                    break;
                }
            }

            if (hasError) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Tidak Valid',
                        text: errorMessage,
                        confirmButtonColor: '#7A9C59'
                    });
                } else {
                    alert(errorMessage);
                }
                this.value = ''; // Reset input on error
                fileList.innerHTML = '';
                return;
            }

            fileList.innerHTML = '';
            if (this.files.length > 0) {
                fileList.innerHTML = `<span class="text-brand font-bold">${this.files.length} foto valid dipilih</span>`;
                for (let i = 0; i < this.files.length; i++) {
                    fileList.innerHTML += `<div class="text-xs mt-1 text-gray-500 truncate"><i data-lucide="image" class="w-3.5 h-3.5 inline mr-1 text-brand"></i>${this.files[i].name} (${(this.files[i].size / 1024 / 1024).toFixed(2)} MB)</div>`;
                }
                if (window.lucide) lucide.createIcons();
            }
        });

        // Form Submission Validation
        form?.addEventListener('submit', function(e) {
            let isValid = true;
            let firstInvalidEl = null;

            const categoryEl = document.getElementById('category_id');
            const titleEl = document.getElementById('title');
            const descEl = document.getElementById('description');
            const qtyEl = document.getElementById('quantity');
            const priceEl = document.getElementById('price_per_unit');
            const addrEl = document.getElementById('address');
            const cityEl = document.getElementById('city');

            // Category validation
            if (!categoryEl.value) {
                showError('category', 'Kategori limbah wajib dipilih.');
                isValid = false;
                if (!firstInvalidEl) firstInvalidEl = categoryEl;
            } else { hideError('category'); }

            // Title validation
            if (!titleEl.value.trim() || titleEl.value.trim().length < 5) {
                showError('title', 'Judul wajib diisi minimal 5 karakter.');
                isValid = false;
                if (!firstInvalidEl) firstInvalidEl = titleEl;
            } else { hideError('title'); }

            // Description validation
            if (!descEl.value.trim() || descEl.value.trim().length < 10) {
                showError('desc', 'Deskripsi wajib diisi minimal 10 karakter.');
                isValid = false;
                if (!firstInvalidEl) firstInvalidEl = descEl;
            } else { hideError('desc'); }

            // Quantity validation
            if (!qtyEl.value || parseFloat(qtyEl.value) <= 0) {
                showError('quantity', 'Volume stok harus lebih besar dari 0.');
                isValid = false;
                if (!firstInvalidEl) firstInvalidEl = qtyEl;
            } else { hideError('quantity'); }

            // Price validation
            if (!priceEl.value || parseInt(priceEl.value) < 1000) {
                showError('price', 'Harga per satuan minimal Rp 1.000.');
                isValid = false;
                if (!firstInvalidEl) firstInvalidEl = priceEl;
            } else { hideError('price'); }

            // Address validation
            if (!addrEl.value.trim() || addrEl.value.trim().length < 5) {
                showError('address', 'Alamat lengkap wajib diisi minimal 5 karakter.');
                isValid = false;
                if (!firstInvalidEl) firstInvalidEl = addrEl;
            } else { hideError('address'); }

            // City validation
            if (!cityEl.value.trim()) {
                showError('city', 'Kota / Kabupaten wajib diisi.');
                isValid = false;
                if (!firstInvalidEl) firstInvalidEl = cityEl;
            } else { hideError('city'); }

            if (!isValid) {
                e.preventDefault();
                firstInvalidEl?.focus();
                firstInvalidEl?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Form Belum Lengkap',
                        text: 'Mohon periksa kembali kolom yang berwarna merah dan lengkapi datanya.',
                        confirmButtonColor: '#7A9C59'
                    });
                }
            }
        });

        function showError(fieldKey, message) {
            const errorEl = document.getElementById(`${fieldKey}-error`);
            const inputEl = document.getElementById(fieldKey === 'category' ? 'category_id' : (fieldKey === 'desc' ? 'description' : (fieldKey === 'price' ? 'price_per_unit' : fieldKey)));
            if (errorEl) {
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
            }
            if (inputEl) {
                inputEl.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                inputEl.classList.remove('border-gray-200');
            }
        }

        function hideError(fieldKey) {
            const errorEl = document.getElementById(`${fieldKey}-error`);
            const inputEl = document.getElementById(fieldKey === 'category' ? 'category_id' : (fieldKey === 'desc' ? 'description' : (fieldKey === 'price' ? 'price_per_unit' : fieldKey)));
            if (errorEl) {
                errorEl.classList.add('hidden');
            }
            if (inputEl) {
                inputEl.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                inputEl.classList.add('border-gray-200');
            }
        }
    });
</script>
@endpush
@endsection
