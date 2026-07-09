@php
    $isSeller = auth()->check() && auth()->user()->isSeller();
    $isBuyer = auth()->check() && auth()->user()->isBuyer();
    $layout = $isSeller ? 'seller.layouts.seller' : ($isBuyer ? 'buyer.layouts.buyer' : 'layouts.master');
    $partner = auth()->id() === $conversation->buyer_id ? $conversation->seller : $conversation->buyer;
    $listing = $conversation->listing;
@endphp

@extends($layout)

@section('title', 'Chat – ' . ($partner->name ?? 'Pengguna') . ' - Recyclink')
@if($isSeller)
@section('header_title', 'Chat')
@endif

@section('content')
<div class="{{ ($isSeller || $isBuyer) ? 'h-full flex flex-col bg-gray-50 -m-6 lg:-m-10 p-6 lg:p-10' : 'min-h-screen bg-gray-50 flex flex-col py-6' }}" style="{{ ($isSeller || $isBuyer) ? 'height: calc(100vh - 5rem);' : '' }}">
    
    <div class="{{ ($isSeller || $isBuyer) ? 'flex-1 bg-white border border-gray-200 rounded-2xl shadow-sm flex flex-col overflow-hidden max-w-4xl w-full mx-auto' : 'max-w-4xl w-full mx-auto flex-1 flex flex-col border border-gray-200 rounded-2xl shadow-sm bg-white overflow-hidden' }}">
        
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-white shrink-0">
            <div class="flex items-center gap-4">
                <a href="{{ route('conversations.index') }}" class="chat-back-btn text-brand hover:text-brand-hover transition-colors">
                    <i data-lucide="arrow-left" class="w-6 h-6"></i>
                </a>
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($partner->name ?? 'U') }}&background=e2e8f0&color=64748b" class="w-10 h-10 rounded-full shrink-0" alt="{{ $partner->name }}">
                    <p class="text-base font-bold text-gray-900">{{ $partner->name ?? 'Pengguna' }}</p>
                </div>
            </div>
            <button class="text-gray-500 hover:text-gray-700">
                <i data-lucide="more-vertical" class="w-5 h-5"></i>
            </button>
        </div>

        {{-- Product Block --}}
        @if($listing)
        <div class="px-4 py-3">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg overflow-hidden shrink-0 border border-gray-100">
                        <img src="{{ $listing->primaryImage ? (str_starts_with($listing->primaryImage->image_url, 'http') ? $listing->primaryImage->image_url : asset('storage/'.$listing->primaryImage->image_url)) : '' }}" class="w-full h-full object-cover" alt="">
                    </div>
                    <div>
                        <p class="text-sm text-gray-800">{{ $listing->title }}</p>
                        <p class="text-sm font-bold text-brand mt-0.5">Rp {{ number_format($listing->price_per_unit, 0, ',', '.') }} / {{ $listing->unit }}</p>
                    </div>
                </div>
                <a href="{{ route('marketplace.show', $listing->id) }}" target="_blank" class="px-4 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shrink-0">
                    Lihat
                </a>
            </div>
        </div>
        <hr class="border-gray-100">
        @endif

        {{-- Messages --}}
        <div id="chat-messages" class="flex-1 bg-white overflow-y-auto p-5 space-y-6">
            @if($messages->isEmpty())
            <div class="flex flex-col items-center justify-center h-full text-center py-10">
                <i data-lucide="message-circle" class="w-10 h-10 text-gray-200 mb-3"></i>
                <p class="text-sm text-gray-400">Belum ada pesan. Mulailah percakapan!</p>
            </div>
            @else
            
            <div class="flex justify-center mb-6">
                <span class="px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold rounded-full tracking-wide">HARI INI</span>
            </div>

            @foreach($messages as $msg)
            @php $isMine = $msg->sender_id === auth()->id(); @endphp
            @if($isMine)
            <!-- Outgoing (Right) -->
            <div class="flex flex-col items-end gap-1">
                <div class="max-w-[75%] px-5 py-3 rounded-2xl bg-brand text-white text-sm leading-relaxed rounded-tr-sm">
                    {{ $msg->message_text }}
                </div>
                <div class="text-[10px] text-gray-400 flex items-center gap-1">
                    {{ $msg->created_at->format('H:i') }} <i data-lucide="check-check" class="w-3 h-3"></i>
                </div>
            </div>
            @else
            <!-- Incoming (Left) -->
            <div class="flex flex-col items-start gap-1">
                <div class="max-w-[75%] px-5 py-3 rounded-2xl bg-white border border-gray-100 text-gray-800 text-sm leading-relaxed rounded-tl-sm shadow-sm">
                    {{ $msg->message_text }}
                </div>
                <div class="text-[10px] text-gray-400">
                    {{ $msg->created_at->format('H:i') }}
                </div>
            </div>
            @endif
            @endforeach
            @endif
        </div>

        {{-- Flash error --}}
        

        {{-- Input Form --}}
        <form method="POST" action="{{ route('conversations.messages.store', $conversation->id) }}" class="p-4 bg-white border-t border-gray-100 flex items-center gap-3 shrink-0">
            @csrf
            <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors shrink-0">
                <i data-lucide="plus-circle" class="w-6 h-6"></i>
            </button>
            
            <div class="flex-1 relative flex items-center">
                <input type="text" name="message_text" class="w-full pl-5 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand" placeholder="Ketik pesan..." required autocomplete="off">
                <button type="button" class="absolute right-4 text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="smile" class="w-5 h-5"></i>
                </button>
            </div>

            <button type="submit" class="w-10 h-10 bg-brand hover:bg-brand-hover text-white rounded-full flex items-center justify-center shrink-0 transition-colors shadow-sm">
                <i data-lucide="send" class="w-5 h-5 ml-0.5"></i>
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Auto-scroll to bottom
const chatMessages = document.getElementById('chat-messages');
if (chatMessages) {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Smart back button logic
const backButtons = document.querySelectorAll('.chat-back-btn');
const defaultBackUrl = "{{ route('conversations.index') }}";
const convId = "{{ $conversation->id }}";

if (document.referrer && !document.referrer.includes('/conversations')) {
    sessionStorage.setItem('chat_referrer_' + convId, document.referrer);
}

backButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const savedReferrer = sessionStorage.getItem('chat_referrer_' + convId);
        
        if (savedReferrer && (savedReferrer.includes('/marketplace') || savedReferrer.includes('/toko') || savedReferrer.includes('/orders'))) {
            window.location.href = savedReferrer;
        } else {
            window.location.href = defaultBackUrl;
        }
    });
});
</script>
@endpush
@endsection
