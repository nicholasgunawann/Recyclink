@extends('admin.layouts.admin')

@section('title', 'Edit Konten Edukasi - Recyclink')
@section('header_title', 'Edit Konten Edukasi')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('admin.education-contents.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar
        </a>
        <h3 class="text-2xl font-bold text-gray-900 mt-4">Edit Konten</h3>
    </div>

    @php
        // ponytail: parse complex values directly inside blade
        $level = 'pemula';
        $link = '';
        if ($content->content_type === 'guide') {
            $parts = explode('|', $content->content . '|');
            $level = $parts[0] ?: 'pemula';
            $link = $parts[1] ?: '';
        } else {
            $link = $content->content;
        }
    @endphp

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 lg:p-8">
        <form action="{{ route('admin.education-contents.update', $content->id) }}" method="POST" enctype="multipart/form-data" id="education-form" onsubmit="prepareFormSubmit()">
            @csrf
            @method('PUT')
            
            {{-- Hidden input for parsed content field --}}
            <input type="hidden" name="content" id="content_hidden_input" value="{{ $content->content }}">

            <div class="space-y-6">
                {{-- Tipe Konten --}}
                <div>
                    <label for="content_type" class="block text-sm font-bold text-gray-700 mb-2">Tipe Konten</label>
                    <select name="content_type" id="content_type" onchange="toggleFields()" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900 font-medium">
                        <option value="article" {{ old('content_type', $content->content_type) == 'article' ? 'selected' : '' }}>Artikel & Tips</option>
                        <option value="video" {{ old('content_type', $content->content_type) == 'video' ? 'selected' : '' }}>Video Edukasi</option>
                        <option value="guide" {{ old('content_type', $content->content_type) == 'guide' ? 'selected' : '' }}>Panduan Pengelolaan Limbah</option>
                    </select>
                    @error('content_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Judul --}}
                <div>
                    <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Judul</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $content->title) }}" placeholder="Ketik judul konten..." required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900">
                    @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Kategori Panduan (Guide Level) - Only for Guide --}}
                <div id="guide_level_container" class="hidden">
                    <label for="guide_level" class="block text-sm font-bold text-gray-700 mb-2">Kategori Panduan</label>
                    <select id="guide_level" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900 font-medium">
                        <option value="pemula" {{ $level === 'pemula' ? 'selected' : '' }}>Pemula</option>
                        <option value="menengah" {{ $level === 'menengah' ? 'selected' : '' }}>Menengah</option>
                        <option value="lanjutan" {{ $level === 'lanjutan' ? 'selected' : '' }}>Lanjutan</option>
                    </select>
                </div>

                {{-- Deskripsi (Excerpt) - For Article & Guide --}}
                <div id="excerpt_container">
                    <label for="excerpt" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi / Ringkasan</label>
                    <textarea name="excerpt" id="excerpt" rows="3" placeholder="Ketik deskripsi singkat konten..." class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900">{{ old('excerpt', $content->excerpt) }}</textarea>
                    @error('excerpt') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Link input (Articles / Videos / Guides) --}}
                <div>
                    <label id="link_label" for="link_input" class="block text-sm font-bold text-gray-700 mb-2">Link Artikel & Tips</label>
                    <input type="url" id="link_input" value="{{ $link }}" placeholder="https://example.com/..." required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900">
                    <p class="text-xs text-gray-400 mt-1" id="link_hint">Masukkan URL eksternal artikel rujukan.</p>
                </div>

                {{-- Tanggal Penerbitan (Bulan & Tahun) --}}
                <div>
                    <label for="published_at" class="block text-sm font-bold text-gray-700 mb-2">Tanggal Upload (Bulan & Tahun)</label>
                    <input type="date" name="published_at" id="published_at" value="{{ old('published_at', $content->published_at ? $content->published_at->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900">
                    @error('published_at') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Gambar (Thumbnail) --}}
                <div>
                    <label for="thumbnail" class="block text-sm font-bold text-gray-700 mb-2">Gambar / Cover</label>
                    <div class="mb-3 w-40 h-28 rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                        <img src="{{ $content->display_thumbnail_url }}" class="w-full h-full object-cover" alt="Current Thumbnail">
                    </div>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition-all cursor-pointer">
                    @error('thumbnail') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-bold text-gray-700 mb-2">Status Publikasi</label>
                    <select name="status" id="status" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900 font-medium">
                        <option value="draft" {{ $content->status === 'draft' ? 'selected' : '' }}>Draft (Simpan saja)</option>
                        <option value="published" {{ $content->status === 'published' ? 'selected' : '' }}>Published (Langsung terbitkan)</option>
                        <option value="archived" {{ $content->status === 'archived' ? 'selected' : '' }}>Archived (Arsip)</option>
                    </select>
                    @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-8 flex items-center justify-end gap-6 mt-6 border-t border-gray-100">
                <a href="{{ route('admin.education-contents.index') }}" class="text-gray-700 font-bold hover:text-gray-900 px-2 py-2 transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-brand text-white font-bold rounded-xl hover:bg-brand-hover transition-all inline-flex items-center gap-2 shadow-sm">
                    <i data-lucide="check" class="w-4 h-4"></i> Perbarui Konten
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleFields() {
        const type = document.getElementById('content_type').value;
        document.getElementById('guide_level_container').classList.toggle('hidden', type !== 'guide');
        document.getElementById('excerpt_container').classList.toggle('hidden', type === 'video');
        document.getElementById('excerpt').required = (type !== 'video');

        const labels = {
            article: 'Link Artikel & Tips',
            video: 'Link Video Edukasi',
            guide: 'Link Panduan Pengelolaan Limbah'
        };
        document.getElementById('link_label').textContent = labels[type] || 'Link';
    }

    function prepareFormSubmit() {
        const type = document.getElementById('content_type').value;
        const link = document.getElementById('link_input').value;
        document.getElementById('content_hidden_input').value = (type === 'guide')
            ? `${document.getElementById('guide_level').value}|${link}`
            : link;
    }

    ['turbo:load', 'DOMContentLoaded'].forEach(ev => document.addEventListener(ev, toggleFields));
</script>
@endsection
