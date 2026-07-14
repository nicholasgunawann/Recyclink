@php
    // ponytail: cache notifications briefly to avoid 2 DB queries on every page load
    $userId = auth()->id();
    $unreadCount = \Illuminate\Support\Facades\Cache::remember("notif_unread_{$userId}", 60, function () {
        return auth()->user()->notifications()->where('is_read', false)->count();
    });
    $recentNotifications = \Illuminate\Support\Facades\Cache::remember("notif_recent_{$userId}", 60, function () {
        return auth()->user()->notifications()->latest()->take(5)->get();
    });
    
    $rolePrefix = null;
    if (auth()->user()->isSeller()) {
        $rolePrefix = 'seller';
    } elseif (auth()->user()->isBuyer()) {
        $rolePrefix = 'buyer';
    }
@endphp

<div class="relative group" id="notification-dropdown-wrapper">
    <button type="button" id="notification-btn" class="p-2.5 text-gray-400 group-hover:text-brand relative rounded-xl group-hover:bg-gray-50 transition-colors inline-block focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-1 cursor-pointer">
        <i data-lucide="bell" class="w-5 h-5"></i>
        @if($unreadCount > 0)
            <span class="absolute top-2 right-2.5 w-2.5 h-2.5 bg-red-500 rounded-full border border-white"></span>
        @endif
    </button>
    
    <!-- Dropdown Menu Wrapper (Invisible bridge for hover) -->
    <div class="absolute right-0 top-full pt-2 z-50 invisible opacity-0 scale-95 origin-top-right transition-all duration-200 group-hover:visible group-hover:opacity-100 group-hover:scale-100">
        <div id="notification-menu" class="w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="font-bold text-gray-900">Notifikasi</h3>
                @if($unreadCount > 0)
                    <span class="bg-brand text-white text-xs font-bold px-2 py-1 rounded-full">{{ $unreadCount }} Baru</span>
                @endif
            </div>
            <div class="max-h-80 overflow-y-auto">
                @forelse($recentNotifications as $notification)
                    @php
                        $bgColor = $notification->is_read ? 'bg-white' : 'bg-brand/10';
                        if ($notification->is_read && $notification->created_at->diffInDays(now()) > 0) {
                            $bgColor = 'bg-gray-100 opacity-60';
                        }
                    @endphp
                    
                    @if(!$notification->is_read && $rolePrefix)
                        <form action="{{ route($rolePrefix . '.notifications.read', $notification->id) }}" method="POST" class="m-0 p-0">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full text-left flex items-start gap-3 p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors {{ $bgColor }}">
                    @else
                        <div class="w-full text-left flex items-start gap-3 p-4 border-b border-gray-100 transition-colors {{ $bgColor }}">
                    @endif
                    
                        <div class="w-10 h-10 rounded-full shrink-0 flex items-center justify-center text-white bg-brand">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ $notification->title ?? 'Pemberitahuan' }}</p>
                            <p class="text-xs text-gray-600 line-clamp-2 mt-0.5">{{ $notification->message }}</p>
                            <p class="text-[10px] text-gray-400 mt-1.5 flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i> {{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        
                    @if(!$notification->is_read && $rolePrefix)
                            </button>
                        </form>
                    @else
                        </div>
                    @endif
                @empty
                    <div class="p-8 text-center text-sm text-gray-500">
                        <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2 text-gray-400"></i>
                        Belum ada notifikasi
                    </div>
                @endforelse
            </div>
            <div class="p-3 bg-gray-50 border-t border-gray-100 text-center">
                @if($rolePrefix)
                    <a href="{{ route($rolePrefix . '.notifications.index') }}" class="text-sm font-bold text-brand hover:underline block">
                        Lihat Semua Notifikasi
                    </a>
                @else
                    <span class="text-sm font-bold text-gray-400 block">
                        Sistem Notifikasi
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
