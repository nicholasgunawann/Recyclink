@extends('admin.layouts.admin')

@section('title', 'Konten Edukasi - Recyclink')
@section('header_title', 'Konten Edukasi')

@section('content')
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-900">Manajemen Konten Edukasi</h3>
            <p class="text-gray-600 mt-1">Kelola artikel, video tutorial, dan panduan pengelolaan limbah untuk pengguna.</p>
        </div>
        
        <a href="{{ route('admin.education-contents.create') }}" class="px-5 py-2.5 bg-brand text-white font-bold text-sm rounded-xl hover:bg-brand-hover transition-colors flex items-center gap-2 shadow-sm whitespace-nowrap w-fit">
            <i data-lucide="plus-circle" class="w-5 h-5"></i> Tambah Konten
        </a>
    </div>

    
    

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        @if($contents->isEmpty())
            <div class="py-20 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 border border-gray-100">
                    <i data-lucide="book-open" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h4 class="font-bold text-gray-700">Belum Ada Konten</h4>
                <p class="text-sm text-gray-500 mt-1 max-w-sm">Mulai buat konten edukasi baru seperti artikel, video, atau panduan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50/50 border-b border-gray-200 text-gray-900 font-semibold">
                        <tr>
                            <th class="px-6 py-4">Konten</th>
                            <th class="px-6 py-4">Tipe</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Diterbitkan Pada</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($contents as $c)
                            @php
                                $typeLabels = [
                                    'article' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-100', 'label' => 'Artikel & Tips'],
                                    'video' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-100', 'label' => 'Video Edukasi'],
                                    'guide' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-100', 'label' => 'Panduan Limbah']
                                ];
                                $type = $typeLabels[$c->content_type] ?? ['bg' => 'bg-gray-50 text-gray-700 border-gray-100', 'label' => strtoupper($c->content_type)];
                                
                                $statusLabels = [
                                    'draft' => ['bg' => 'bg-gray-100 text-gray-600', 'label' => 'Draft'],
                                    'published' => ['bg' => 'bg-emerald-100 text-emerald-800', 'label' => 'Diterbitkan'],
                                    'archived' => ['bg' => 'bg-amber-100 text-amber-800', 'label' => 'Diarsipkan']
                                ];
                                $status = $statusLabels[$c->status] ?? ['bg' => 'bg-gray-100 text-gray-600', 'label' => strtoupper($c->status)];
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 border border-gray-100 bg-gray-50">
                                            <img src="{{ $c->thumbnail_url ? asset('storage/' . $c->thumbnail_url) : 'https://placehold.co/120x120?text=Edukasi' }}" class="w-full h-full object-cover" alt="">
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-gray-900 leading-snug truncate max-w-md">{{ $c->title }}</h4>
                                            <p class="text-xs text-gray-400 mt-1 truncate max-w-md">{{ $c->excerpt ?? 'Tidak ada deskripsi singkat.' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold border {{ $type['bg'] }}">
                                        {{ $type['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $status['bg'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    {{ $c->published_at ? $c->published_at->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($c->status === 'draft' || $c->status === 'archived')
                                            <form action="{{ route('admin.education-contents.publish', $c->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="p-2 hover:bg-emerald-50 text-emerald-600 rounded-xl transition-colors" title="Terbitkan">
                                                    <i data-lucide="send" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @elseif($c->status === 'published')
                                            <form action="{{ route('admin.education-contents.archive', $c->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="p-2 hover:bg-amber-50 text-amber-600 rounded-xl transition-colors" title="Arsip">
                                                    <i data-lucide="archive" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.education-contents.edit', $c->id) }}" class="p-2 hover:bg-gray-100 text-gray-600 rounded-xl transition-colors" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('admin.education-contents.destroy', $c->id) }}" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin menghapus konten ini?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 hover:bg-rose-50 text-rose-600 rounded-xl transition-colors" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($contents->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $contents->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
