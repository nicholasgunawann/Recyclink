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

    <form action="{{ route('seller.listings.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
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
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Listing</label>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Minyak Jelantah Bening Bekas Restoran" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3" required>
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kategori Limbah</label>
                        <select name="category_id" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3" required>
                            <option value="">-- Pilih Kategori Utama --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Deskripsi --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <label class="block font-bold text-gray-900 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-500">Jelaskan kondisi dan sumber limbah secara rinci.</p>
                </div>
                <div class="md:col-span-2">
                    <textarea name="description" rows="4" placeholder="Contoh: Minyak jelantah dari usaha gorengan, disaring secara rutin..." class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3" required>{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Volume & Harga --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <label class="block font-bold text-gray-900 mb-1">Volume & Harga <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-500">Tentukan jumlah yang tersedia dan harga jualnya.</p>
                </div>
                <div class="md:col-span-2 space-y-4">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Volume</label>
                            <input type="number" step="0.01" name="quantity" value="{{ old('quantity') }}" placeholder="Misal: 20" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3" required>
                            @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Satuan</label>
                            <select name="unit" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3" required>
                                <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kg</option>
                                <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liter</option>
                                <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                <option value="karung" {{ old('unit') == 'karung' ? 'selected' : '' }}>Karung</option>
                            </select>
                            @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Harga per Satuan (Rp)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm font-medium">Rp</span>
                            </div>
                            <input type="number" name="price_per_unit" value="{{ old('price_per_unit') }}" min="1000" placeholder="4000" class="w-full pl-10 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3" required>
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
                    <p class="text-xs text-gray-500">Lokasi limbah siap diambil.</p>
                </div>
                <div class="md:col-span-2">
                    <textarea name="address" rows="3" placeholder="Jl. Tembalang Raya No. 20..." class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3" required>{{ old('address', auth()->user()->sellerProfile->address ?? '') }}</textarea>
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    <div class="mt-3">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kota / Kabupaten</label>
                        <input type="text" name="city" value="{{ old('city', auth()->user()->sellerProfile->city ?? '') }}" placeholder="Semarang" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-brand focus:border-brand block p-3" required>
                        @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Foto --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <label class="block font-bold text-gray-900 mb-1">Foto Limbah <span class="text-gray-400 font-normal">(Opsional)</span></label>
                    <p class="text-xs text-gray-500">Tambahkan foto visual dari limbah agar pembeli lebih yakin.</p>
                </div>
                <div class="md:col-span-2">
                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-brand transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i data-lucide="upload-cloud" class="w-8 h-8 text-gray-400 mb-3"></i>
                                <p class="mb-1 text-sm text-gray-500 font-semibold"><span class="text-brand">Klik untuk unggah</span> atau seret file kesini</p>
                                <p class="text-xs text-gray-500">PNG, JPG or JPEG (Max. 2MB)</p>
                            </div>
                            <input id="dropzone-file" type="file" name="images[]" multiple accept="image/*" class="hidden" />
                        </label>
                    </div>
                    @error('images.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <div id="file-list" class="mt-3 text-sm text-gray-600 font-medium"></div>
                </div>
            </div>

        </div>

        <div class="p-6 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
            <a href="{{ route('seller.listings.index') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-brand text-white font-bold rounded-xl hover:bg-brand-hover transition-colors shadow-sm flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Listing
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const dt = new DataTransfer();

    document.getElementById('dropzone-file').addEventListener('change', function(e) {
        // Append new files to DataTransfer
        for(let i = 0; i < this.files.length; i++) {
            dt.items.add(this.files[i]);
        }
        
        // Update the input files with the accumulated files
        this.files = dt.files;

        const fileList = document.getElementById('file-list');
        fileList.innerHTML = '';
        if(this.files.length > 0) {
            fileList.innerHTML = `<span class="text-brand font-bold">${this.files.length} foto dipilih</span>`;
            for(let i = 0; i < this.files.length; i++) {
                fileList.innerHTML += `<div class="text-xs mt-1 text-gray-500 truncate"><i data-lucide="image" class="w-3 h-3 inline mr-1"></i>${this.files[i].name}</div>`;
            }
            lucide.createIcons();
        }
    });
</script>
@endpush
@endsection
