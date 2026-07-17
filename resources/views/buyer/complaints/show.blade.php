@extends('buyer.layouts.buyer')
@section('title', 'Detail Komplain')
@section('header_title', 'Pusat Resolusi')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('buyer.complaints.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-brand transition-colors font-medium">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Pusat Resolusi
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informasi Komplain -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Detail Komplain</span>
                    @if($complaint->isOpen())
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">Menunggu (Diskusi)</span>
                    @elseif($complaint->status === 'under_review')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Ditinjau Admin</span>
                    @elseif($complaint->isResolved())
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Selesai</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">Ditolak</span>
                    @endif
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Nomor Komplain</p>
                        <p class="font-bold text-gray-900">{{ $complaint->complaint_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nomor Pesanan</p>
                        <a href="{{ route('buyer.orders.show', $complaint->order_id) }}" class="font-bold text-brand hover:underline">{{ $complaint->order->order_code }}</a>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Penjual</p>
                        <p class="font-bold text-gray-900">{{ $complaint->respondent->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Subjek</p>
                        <p class="font-bold text-gray-900">{{ $complaint->subject }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Alasan / Tipe</p>
                        <p class="font-medium text-gray-800">{{ $complaint->complaint_type }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Deskripsi Masalah</p>
                        <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-xl mt-1">{{ $complaint->description }}</p>
                    </div>

                    @if($complaint->evidence_url)
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Bukti Pendukung</p>
                            <a href="{{ asset('storage/' . $complaint->evidence_url) }}" target="_blank" class="block">
                                @if(Str::endsWith($complaint->evidence_url, '.mp4'))
                                    <video src="{{ asset('storage/' . $complaint->evidence_url) }}" class="w-full h-32 object-cover rounded-xl border border-gray-200" controls></video>
                                @else
                                    <img src="{{ asset('storage/' . $complaint->evidence_url) }}" class="w-full h-32 object-cover rounded-xl border border-gray-200 hover:opacity-90 transition-opacity">
                                @endif
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Area Diskusi / Chat -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-[600px]">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-t-2xl">
                    <h3 class="font-bold text-gray-900">Ruang Diskusi Resolusi</h3>
                    <p class="text-xs text-gray-500">Diskusi antara Anda dan Penjual</p>
                </div>

                <!-- Chat Messages -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" id="chat-container">
                    @forelse($complaint->messages as $msg)
                        <div class="flex {{ $msg->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[75%] flex gap-3 {{ $msg->user_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($msg->user->name) }}&background={{ $msg->user_id === auth()->id() ? '7A9C59' : '14b8a6' }}&color=fff" class="w-8 h-8 rounded-full shrink-0 mt-1">
                                <div class="{{ $msg->user_id === auth()->id() ? 'bg-brand text-white' : 'bg-white border border-gray-200 text-gray-800' }} p-3 rounded-2xl shadow-sm">
                                    <p class="text-xs font-bold mb-1 opacity-75 {{ $msg->user_id === auth()->id() ? 'text-right' : '' }}">{{ $msg->user->name }} {{ $msg->user->isAdmin() ? '(Admin)' : '' }}</p>
                                    <p class="text-sm whitespace-pre-wrap">{{ $msg->message }}</p>
                                    
                                    @if($msg->attachment_url)
                                        <a href="{{ asset('storage/' . $msg->attachment_url) }}" target="_blank" class="block mt-2">
                                            @if(Str::endsWith($msg->attachment_url, '.mp4'))
                                                <video src="{{ asset('storage/' . $msg->attachment_url) }}" class="w-full h-24 object-cover rounded-xl" controls></video>
                                            @else
                                                <img src="{{ asset('storage/' . $msg->attachment_url) }}" class="w-full h-24 object-cover rounded-xl">
                                            @endif
                                        </a>
                                    @endif
                                    
                                    <p class="text-[10px] mt-2 text-right opacity-60">{{ $msg->created_at->format('H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex items-center justify-center text-center">
                            <div>
                                <i data-lucide="message-square" class="w-10 h-10 text-gray-300 mx-auto mb-2"></i>
                                <p class="text-gray-500 text-sm">Belum ada diskusi. Silakan mulai percakapan untuk mencari jalan tengah.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Chat Input Form -->
                @if(!$complaint->isResolved())
                <div class="p-4 bg-white border-t border-gray-100 rounded-b-2xl">
                    <form action="{{ route('buyer.complaints.messages.store', $complaint->id) }}" method="POST" enctype="multipart/form-data" class="flex gap-3 items-end">
                        @csrf
                        <div class="flex-1">
                            <textarea name="message" rows="2" class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand px-4 py-3 text-sm resize-none bg-gray-50" placeholder="Ketik pesan Anda di sini..." required></textarea>
                            <input type="file" name="attachment" accept="image/jpeg,image/png,video/mp4,application/pdf" class="mt-2 text-xs text-gray-500">
                        </div>
                        <button type="submit" class="h-12 px-6 bg-brand hover:bg-brand-hover text-white font-bold rounded-xl transition-colors shadow shrink-0 flex items-center gap-2">
                            <i data-lucide="send" class="w-4 h-4"></i> Kirim
                        </button>
                    </form>
                </div>
                @else
                <div class="p-4 bg-gray-50 border-t border-gray-100 rounded-b-2xl text-center">
                    <p class="text-sm font-semibold text-gray-600">Komplain ini sudah diselesaikan/ditutup.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Scroll chat to bottom
    const chatContainer = document.getElementById('chat-container');
    if (chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
</script>
@endpush
@endsection
