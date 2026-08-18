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
                        <p class="text-sm font-bold text-brand mt-0.5">Rp {{ number_format((float)($listing->price_per_unit ?? 0), 0, ',', '.') }} / {{ $listing->unit }}</p>
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
            {{-- Outgoing (Right) --}}
            <div class="flex flex-col items-end gap-1 group" data-msg-id="{{ $msg->id }}">
                <div class="flex items-end gap-2">
                    {{-- Delete button (own messages only) --}}
                    <button type="button" onclick="deleteMessage({{ $conversation->id }}, {{ $msg->id }}, this)" 
                        class="opacity-0 group-hover:opacity-100 transition-opacity text-gray-300 hover:text-red-400 p-1"
                        title="Hapus pesan">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                    <div class="max-w-[75%]">
                        @if($msg->message_type === 'image' && $msg->attachment_path)
                            <div class="rounded-2xl overflow-hidden rounded-tr-sm">
                                <img src="{{ asset('storage/' . $msg->attachment_path) }}" alt="Gambar" class="max-w-xs max-h-60 object-cover cursor-pointer rounded-2xl" onclick="openImagePreview('{{ asset('storage/' . $msg->attachment_path) }}')">
                            </div>
                        @else
                            <div class="px-5 py-3 rounded-2xl bg-brand text-white text-sm leading-relaxed rounded-tr-sm">
                                {{ $msg->message_text }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="text-[10px] text-gray-400 flex items-center gap-1">
                    {{ $msg->created_at->format('H:i') }} <i data-lucide="check-check" class="w-3 h-3"></i>
                </div>
            </div>
            @else
            {{-- Incoming (Left) --}}
            <div class="flex flex-col items-start gap-1" data-msg-id="{{ $msg->id }}">
                <div class="max-w-[75%]">
                    @if($msg->message_type === 'image' && $msg->attachment_path)
                        <div class="rounded-2xl overflow-hidden rounded-tl-sm">
                            <img src="{{ asset('storage/' . $msg->attachment_path) }}" alt="Gambar" class="max-w-xs max-h-60 object-cover cursor-pointer rounded-2xl" onclick="openImagePreview('{{ asset('storage/' . $msg->attachment_path) }}')">
                        </div>
                    @else
                        <div class="px-5 py-3 rounded-2xl bg-white border border-gray-100 text-gray-800 text-sm leading-relaxed rounded-tl-sm shadow-sm">
                            {{ $msg->message_text }}
                        </div>
                    @endif
                </div>
                <div class="text-[10px] text-gray-400">
                    {{ $msg->created_at->format('H:i') }}
                </div>
            </div>
            @endif
            @endforeach
            @endif
        </div>

        {{-- Emoji Picker --}}
        <div id="emoji-picker" class="hidden px-4 pb-2 bg-white border-t border-gray-100">
            <div class="p-3 bg-gray-50 rounded-2xl">
                <div class="flex flex-wrap gap-2 text-xl">
                    @foreach(['😊','😂','🥰','😍','😭','😅','🤣','🙏','👍','❤️','🔥','✅','🎉','😎','🤔','😬','🫡','👋','✨','💪','🤝','😱','😤','🥳','💯','🏆','🛠️','📦','♻️','💚','🌿'] as $emoji)
                        <button type="button" onclick="insertEmoji('{{ $emoji }}')" class="hover:scale-125 transition-transform cursor-pointer">{{ $emoji }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Image Preview Bar (before send) --}}
        <div id="image-preview-bar" class="hidden px-4 py-2 bg-white border-t border-gray-100">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <img id="image-preview-thumb" src="" alt="Preview" class="w-16 h-16 object-cover rounded-xl border border-gray-200">
                    <button type="button" onclick="clearImagePreview()" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs">×</button>
                </div>
                <p class="text-xs text-gray-500">Gambar siap dikirim</p>
            </div>
        </div>

        {{-- Input Form --}}
        <form method="POST" action="{{ route('conversations.messages.store', $conversation->id) }}" 
              class="p-4 bg-white border-t border-gray-100 flex items-center gap-3 shrink-0"
              id="chat-form"
              enctype="multipart/form-data">
            @csrf
            <input type="file" id="image-input" name="image" accept="image/*" class="hidden" onchange="handleImageSelect(this)">

            {{-- + Button: Upload Gambar --}}
            <button type="button" onclick="document.getElementById('image-input').click()" 
                class="text-gray-400 hover:text-brand transition-colors shrink-0"
                title="Kirim Gambar">
                <i data-lucide="image-plus" class="w-6 h-6"></i>
            </button>
            
            <div class="flex-1 relative flex items-center">
                <input type="text" name="message_text" id="message-input"
                    class="w-full pl-5 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand" 
                    placeholder="Ketik pesan..." autocomplete="off">
                {{-- Emoji Button --}}
                <button type="button" onclick="toggleEmojiPicker()" 
                    class="absolute right-4 text-gray-400 hover:text-brand transition-colors"
                    title="Emoji">
                    <i data-lucide="smile" class="w-5 h-5"></i>
                </button>
            </div>

            <button type="submit" class="w-10 h-10 bg-brand hover:bg-brand-hover text-white rounded-full flex items-center justify-center shrink-0 transition-colors shadow-sm">
                <i data-lucide="send" class="w-5 h-5 ml-0.5"></i>
            </button>
        </form>
    </div>
</div>

{{-- Image Lightbox Preview --}}
<div id="lightbox" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4" onclick="closeLightbox()">
    <img id="lightbox-img" src="" alt="" class="max-w-full max-h-full rounded-2xl object-contain">
</div>

{{-- Delete CSRF Form (hidden) --}}
<form id="delete-form" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
// Auto-scroll to bottom
const chatMessages = document.getElementById('chat-messages');
if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;

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

// Emoji picker
function toggleEmojiPicker() {
    const picker = document.getElementById('emoji-picker');
    picker.classList.toggle('hidden');
}

function insertEmoji(emoji) {
    const input = document.getElementById('message-input');
    const pos = input.selectionStart;
    const val = input.value;
    input.value = val.slice(0, pos) + emoji + val.slice(pos);
    input.focus();
    input.setSelectionRange(pos + emoji.length, pos + emoji.length);
}

// Close emoji picker when clicking outside
document.addEventListener('click', function(e) {
    const picker = document.getElementById('emoji-picker');
    if (!picker.classList.contains('hidden') && !picker.contains(e.target) && !e.target.closest('[onclick="toggleEmojiPicker()"]')) {
        picker.classList.add('hidden');
    }
});

// Image select & preview
function handleImageSelect(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('image-preview-thumb').src = e.target.result;
        document.getElementById('image-preview-bar').classList.remove('hidden');
        // When image selected, clear text requirement
        document.getElementById('message-input').removeAttribute('required');
    };
    reader.readAsDataURL(file);
}

function clearImagePreview() {
    document.getElementById('image-input').value = '';
    document.getElementById('image-preview-bar').classList.add('hidden');
    document.getElementById('image-preview-thumb').src = '';
}

// Image lightbox
function openImagePreview(url) {
    document.getElementById('lightbox-img').src = url;
    document.getElementById('lightbox').classList.remove('hidden');
}

function closeLightbox() {
    document.getElementById('lightbox').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox();
});

// Delete message
function deleteMessage(convId, msgId, btn) {
    if (!confirm('Hapus pesan ini?')) return;
    
    const form = document.getElementById('delete-form');
    form.action = `/conversations/${convId}/messages/${msgId}`;
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('[name="_token"]', form).value,
            'Accept': 'application/json',
        },
        body: new FormData(form),
    }).then(r => r.json()).then(data => {
        if (data.success) {
            const msgEl = btn.closest('[data-msg-id]');
            msgEl?.remove();
        }
    }).catch(() => {
        // Fallback: form submit
        form.submit();
    });
}
</script>
@endpush
@endsection
