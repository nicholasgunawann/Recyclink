@php
    $partner = auth()->id() === $conv->buyer_id ? $conv->seller : $conv->buyer;
    $listing = $conv->listing;
    $lastMsg = $conv->latestMessage ?? $conv->messages->last();
@endphp
<a href="{{ route('conversations.show', $conv->id) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-colors group">
    <div class="w-12 h-12 rounded-xl {{ auth()->user()->isBuyer() ? 'bg-brand/10 text-brand' : 'bg-gray-100 text-gray-500' }} overflow-hidden shrink-0 flex items-center justify-center">
        @if($partner)
            <img src="https://ui-avatars.com/api/?name={{ urlencode($partner->name ?? 'U') }}&background=7A9C59&color=fff" class="w-full h-full object-cover" alt="{{ $partner->name }}">
        @else
            <i data-lucide="user" class="w-6 h-6"></i>
        @endif
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between mb-0.5">
            <p class="text-sm font-bold text-gray-900 truncate group-hover:text-brand transition-colors">{{ $partner->name ?? 'Pengguna' }}</p>
            @if($conv->last_message_at)
            <p class="text-[11px] text-gray-400 shrink-0 ml-2 font-medium">{{ \Carbon\Carbon::parse($conv->last_message_at)->diffForHumans() }}</p>
            @endif
        </div>
        @if($listing)
        <p class="text-xs text-brand font-bold truncate mb-0.5">{{ $listing->title }}</p>
        @endif
        @if($lastMsg)
        <p class="text-xs text-gray-500 truncate">{{ $lastMsg->message_text }}</p>
        @else
        <p class="text-xs text-gray-400 italic">Belum ada pesan</p>
        @endif
    </div>
    <div class="shrink-0 ml-3">
        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-brand transition-colors"></i>
    </div>
</a>
