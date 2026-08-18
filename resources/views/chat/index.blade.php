@php
    $isSeller = auth()->check() && auth()->user()->isSeller();
    $isBuyer = auth()->check() && auth()->user()->isBuyer();
    $layout = $isSeller ? 'seller.layouts.seller' : ($isBuyer ? 'buyer.layouts.buyer' : 'layouts.master');

    // Grouping logic for 30 minutes
    $recentThreshold = \Carbon\Carbon::now()->subMinutes(30);
    $recentConversations = collect();
    $olderConversations = collect();

    foreach($conversations as $conv) {
        if ($conv->last_message_at && \Carbon\Carbon::parse($conv->last_message_at)->gte($recentThreshold)) {
            $recentConversations->push($conv);
        } else {
            $olderConversations->push($conv);
        }
    }
    
    // Sort both desc
    $recentConversations = $recentConversations->sortByDesc('last_message_at');
    $olderConversations = $olderConversations->sortByDesc('last_message_at');

    $hasRecent = $recentConversations->isNotEmpty();
@endphp

@extends($layout)

@section('title', 'Pesan - Recyclink')
@section('header_title', 'Pesan')

@section('content')

{{-- Container depending on layout --}}
<div class="{{ (!$isSeller && !$isBuyer) ? 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 min-h-screen' : '' }}">
    
    @if(!$isSeller && !$isBuyer)
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Pesan</h1>
        <p class="text-sm text-gray-500 mb-8">Riwayat percakapan Anda</p>
    @elseif($isSeller)
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900">Pesan</h2>
            <p class="text-sm text-gray-500 mt-1">Riwayat percakapan Anda dengan pembeli</p>
        </div>
    @elseif($isBuyer)
        <div class="mb-6 hidden lg:block">
            <h2 class="text-xl font-bold text-gray-900">Pesan</h2>
            <p class="text-sm text-gray-500 mt-1">Riwayat percakapan Anda dengan penjual</p>
        </div>
    @endif

    @if($conversations->isEmpty())
    <div class="bg-white border border-gray-200 border-dashed rounded-2xl py-20 flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 bg-brand/5 rounded-2xl flex items-center justify-center mb-4">
            <i data-lucide="message-circle" class="w-8 h-8 text-brand/30"></i>
        </div>
        <h3 class="text-base font-bold text-gray-700 mb-1">Belum Ada Percakapan</h3>
        <p class="text-sm text-gray-400 max-w-xs mb-5">
            {{ $isSeller ? 'Percakapan akan muncul di sini ketika pembeli menghubungi Anda.' : 'Klik "Chat Penjual" pada halaman detail listing untuk mulai berkomunikasi.' }}
        </p>
        @if(!$isSeller)
        <a href="{{ route('marketplace.index') }}" class="px-5 py-2.5 bg-brand text-white text-sm font-bold rounded-xl hover:bg-brand-hover transition-colors">
            Jelajahi Marketplace
        </a>
        @endif
    </div>
    @else
        
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
            
            @if($hasRecent)
                {{-- Mode with Recent/Old groups --}}
                
                {{-- Chat Terbaru --}}
                <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Chat Terbaru</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($recentConversations as $conv)
                        @include('chat.partials.conversation_item', ['conv' => $conv])
                    @endforeach
                </div>

                {{-- Chat Lama --}}
                @if($olderConversations->isNotEmpty())
                    <div class="bg-gray-50 px-5 py-3 border-y border-gray-200 mt-4">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Chat Lama</span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($olderConversations as $conv)
                            @include('chat.partials.conversation_item', ['conv' => $conv])
                        @endforeach
                    </div>
                @endif

            @else
                {{-- Normal mode without headers --}}
                <div class="divide-y divide-gray-100">
                    @foreach($conversations as $conv)
                        @include('chat.partials.conversation_item', ['conv' => $conv])
                    @endforeach
                </div>
            @endif

        </div>

    @endif
</div>

{{-- Hidden form for DELETE conversation --}}
<form id="conv-delete-form" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function hideConversation(convId, btn) {
    if (!confirm('Hapus percakapan dari daftar? Pesan tidak dihapus permanen.')) return;

    const form = document.getElementById('conv-delete-form');
    form.action = `/conversations/${convId}`;

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('[name="_token"]', form).value,
            'Accept': 'application/json',
        },
        body: new FormData(form),
    }).then(r => r.json()).then(data => {
        if (data.success) {
            const item = btn.closest('[data-conv-id]');
            item?.remove();
        }
    }).catch(() => { form.submit(); });
}
</script>
@endpush
@endsection
