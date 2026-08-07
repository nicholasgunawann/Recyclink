<section class="py-12 bg-gray-50/50 min-h-[60vh]">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Section Header --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 bg-brand/10 text-brand px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3">
                    <i data-lucide="play-circle" class="w-3.5 h-3.5"></i> Video Tutorial
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Panduan Visual Operasional</h2>
                <p class="text-gray-500 text-sm sm:text-base mt-1 max-w-xl">
                    Pelajari proses teknis daur ulang industrial secara mendalam melalui rangkaian video tutorial eksklusif kami.
                </p>
            </div>
        </div>

        {{-- Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($videos as $video)
            <a href="{{ $video->formatted_content_url }}" target="_blank" class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col overflow-hidden group cursor-pointer">
                
                {{-- Video Thumbnail --}}
                <div class="w-full h-52 bg-[#0a0a0a] relative flex items-center justify-center overflow-hidden">
                    <img src="{{ $video->display_thumbnail_url }}"
                         class="absolute inset-0 w-full h-full object-cover opacity-85 group-hover:scale-105 transition-transform duration-500"
                         alt="{{ $video->title }}"
                         onerror="if(this.src.includes('hqdefault.jpg')){this.src=this.src.replace('hqdefault.jpg','mqdefault.jpg');}else{this.src='https://placehold.co/640x360/1a2e1a/7A9C59?text=Video+Tutorial';}">
                    
                    {{-- Gradient Overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>

                    {{-- Floating YouTube Badge --}}
                    <span class="absolute top-4 left-4 bg-red-600/90 backdrop-blur-md text-white text-[10px] font-extrabold px-2.5 py-1 rounded-lg uppercase tracking-wider shadow-xs flex items-center gap-1">
                        <i data-lucide="play" class="w-3 h-3 fill-current"></i> Video HD
                    </span>

                    {{-- Animated Play Button --}}
                    <div class="relative z-10 w-14 h-14 bg-brand text-white rounded-full flex items-center justify-center group-hover:scale-110 group-hover:bg-brand-hover transition-all duration-300 shadow-xl border-2 border-white/40">
                        <i data-lucide="play" class="w-6 h-6 ml-0.5 fill-current"></i>
                    </div>
                </div>
                
                {{-- Content --}}
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex items-center justify-between text-xs text-gray-400 font-medium mb-3">
                        <span class="flex items-center gap-1">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                            {{ $video->published_at ? $video->published_at->diffForHumans() : 'Baru saja' }}
                        </span>
                        <span class="text-brand font-bold flex items-center gap-1">
                            Tonton di YouTube <i data-lucide="external-link" class="w-3 h-3"></i>
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-brand transition-colors leading-snug line-clamp-2">
                        {{ $video->title }}
                    </h3>
                </div>
                
            </a>
            @empty
                <div class="col-span-3 py-16 text-center bg-white rounded-2xl border border-gray-200/80 shadow-xs">
                    <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-400 border border-gray-100">
                        <i data-lucide="video" class="w-7 h-7"></i>
                    </div>
                    <h4 class="font-bold text-gray-700">Belum Ada Video</h4>
                    <p class="text-sm text-gray-500 mt-1">Video tutorial edukasi akan ditayangkan di sini.</p>
                </div>
            @endforelse
        </div>
        
    </div>
</section>
