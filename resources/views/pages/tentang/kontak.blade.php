<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-start">
            
            {{-- Left: Contact Form --}}
            <div class="bg-white rounded-3xl border border-gray-200 p-8 sm:p-10 shadow-sm">
                <h2 class="text-3xl font-extrabold text-gray-900 mb-8 tracking-tight">Hubungi Kami</h2>
                
                
                <form action="{{ route('kontak.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama Lengkap --}}
                        <div>
                            <label for="name" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Lengkap</label>
                            <input type="text" id="name" name="name" placeholder="John Doe" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all placeholder-gray-400">
                        </div>
                        
                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Email</label>
                            <input type="email" id="email" name="email" placeholder="john@company.com" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all placeholder-gray-400">
                        </div>
                    </div>
                    
                    {{-- Subjek --}}
                    <div>
                        <label for="subject" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Subjek</label>
                        <input type="text" id="subject" name="subject" placeholder="Kolaborasi Industri" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all placeholder-gray-400">
                    </div>
                    
                    {{-- Pesan --}}
                    <div>
                        <label for="message" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Pesan</label>
                        <textarea id="message" name="message" rows="5" placeholder="Bagaimana kami bisa membantu Anda?" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all placeholder-gray-400 resize-none"></textarea>
                    </div>
                    
                    {{-- Submit Button --}}
                    <button type="submit" class="w-full bg-brand hover:bg-brand-hover text-white font-bold py-4 rounded-xl transition-colors duration-300 shadow-sm">
                        Kirim Pesan
                    </button>
                </form>
            </div>
            
            {{-- Right: Contact Details & Map --}}
            <div class="h-full flex flex-col">
                <h2 class="text-3xl font-extrabold text-gray-900 mb-8 tracking-tight">Detail Kontak</h2>
                
                {{-- Contact Info List --}}
                <div class="space-y-8 mb-10">
                    
                    {{-- Address --}}
                    <div class="flex items-start gap-5">
                        <div class="w-12 h-12 bg-brand/10 rounded-full flex items-center justify-center shrink-0">
                            <i data-lucide="map-pin" class="w-5 h-5 text-brand"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1">Berbasis di</h4>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                Semarang, Jawa Tengah, Indonesia
                            </p>
                        </div>
                    </div>
                    
                    {{-- Phone Kei --}}
                    <div class="flex items-start gap-5">
                        <div class="w-12 h-12 bg-brand/10 rounded-full flex items-center justify-center shrink-0">
                            <i data-lucide="phone" class="w-5 h-5 text-brand"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1">Telepon (Kei)</h4>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                081381802703
                            </p>
                        </div>
                    </div>

                    {{-- Phone Shafa --}}
                    <div class="flex items-start gap-5">
                        <div class="w-12 h-12 bg-brand/10 rounded-full flex items-center justify-center shrink-0">
                            <i data-lucide="phone" class="w-5 h-5 text-brand"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1">Telepon (Shafa)</h4>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                082132772030
                            </p>
                        </div>
                    </div>
                    
                    {{-- Email --}}
                    <div class="flex items-start gap-5">
                        <div class="w-12 h-12 bg-brand/10 rounded-full flex items-center justify-center shrink-0">
                            <i data-lucide="mail" class="w-5 h-5 text-brand"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1">Email Dukungan</h4>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                therecyclink@gmail.com
                            </p>
                        </div>
                    </div>
                    
                </div>
                
                {{-- Map Placeholder --}}
                <div class="w-full h-64 bg-gradient-to-br from-gray-200 to-gray-300 rounded-3xl relative flex items-center justify-center mt-auto overflow-hidden shadow-inner">
                    <div class="bg-white px-4 py-2 rounded-full shadow-md flex items-center gap-2 relative z-10">
                        <i data-lucide="map-pin" class="w-4 h-4 text-gray-700"></i>
                        <span class="text-sm font-bold text-gray-700">HQ Recyclink</span>
                    </div>
                    {{-- Decorative pattern for map background --}}
                    <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#9ca3af 1px, transparent 1px); background-size: 20px 20px;"></div>
                </div>
                
            </div>
            
        </div>
    </div>
</section>
