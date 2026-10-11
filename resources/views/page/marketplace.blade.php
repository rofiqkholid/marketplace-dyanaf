@extends('layouts.app')

@section('content')
<div class="space-y-3">

    <!-- Hero Banner Carousel (Auto-rotating with Manual Controls) -->
    <div class="relative rounded-none overflow-hidden border border-slate-300 bg-slate-900 group"
         x-data="{
             activeSlide: 0,
             slides: [
                 '{{ asset('assets/banner/banner-one.svg') }}',
                 '{{ asset('assets/banner/banner-two.svg') }}'
             ],
             timer: null,
             startTimer() {
                 this.stopTimer();
                 this.timer = setInterval(() => {
                     this.next();
                 }, 4000);
             },
             stopTimer() {
                 if (this.timer) clearInterval(this.timer);
             },
             next() {
                 this.activeSlide = (this.activeSlide + 1) % this.slides.length;
             },
             prev() {
                 this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length;
             },
             goTo(index) {
                 this.activeSlide = index;
                 this.startTimer();
             }
         }"
         x-init="startTimer()"
         @mouseenter="stopTimer()"
         @mouseleave="startTimer()">
        
        <!-- Slides Container (Single Cell Grid Overlay to Prevent Stacking) -->
        <div class="grid grid-cols-1 grid-rows-1 w-full overflow-hidden">
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="activeSlide === index"
                     x-transition:enter="transition opacity ease-out duration-500"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition opacity ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="col-start-1 row-start-1 w-full">
                    <img :src="slide" :alt="'Dyanaf Creative Banner ' + (index + 1)" class="w-full h-auto block rounded-none select-none">
                </div>
            </template>
        </div>

        <!-- Left Arrow Button -->
        <button @click="prev(); startTimer()" 
                class="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 bg-slate-900/60 hover:bg-slate-900 text-white flex items-center justify-center border border-white/20 transition-all opacity-0 group-hover:opacity-100 cursor-pointer z-10"
                aria-label="Previous Slide">
            <i class="fa-solid fa-chevron-left text-xs"></i>
        </button>

        <!-- Right Arrow Button -->
        <button @click="next(); startTimer()" 
                class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 bg-slate-900/60 hover:bg-slate-900 text-white flex items-center justify-center border border-white/20 transition-all opacity-0 group-hover:opacity-100 cursor-pointer z-10"
                aria-label="Next Slide">
            <i class="fa-solid fa-chevron-right text-xs"></i>
        </button>

        <!-- Pagination Bar Indicators -->
        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center space-x-2 z-10">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="goTo(index)" 
                        :class="activeSlide === index ? 'w-6 bg-blue-500 border-blue-400' : 'w-2.5 bg-white/60 hover:bg-white border-transparent'" 
                        class="h-2 rounded-none transition-all border cursor-pointer"></button>
            </template>
        </div>
    </div>

    <!-- Combined Main Content Box (Quick Brand Bar + Featured Products Grid) -->
    <div class="bg-transparent sm:bg-[#f8fafc] p-0 sm:p-3 rounded-none border-0 sm:border sm:border-slate-300/80">
        
        <!-- Quick Brand & Tools Bar Grid -->
        <div class="grid grid-cols-3 sm:grid-cols-9 gap-3 w-full">
            <!-- Netflix -->
            <a href="{{ route('brand.detail', 'netflix') }}" class="flex flex-col items-center justify-center p-3 rounded-none bg-white hover:bg-slate-50 border border-slate-300/70 transition-colors group aspect-square w-full">
                <div class="w-full h-10 sm:h-12 flex items-center justify-center mb-1 group-hover:scale-105 transition-transform px-1">
                    <img src="{{ asset('assets/logo/Netflix_2015_logo.svg') }}" alt="Netflix" class="h-8 sm:h-10 max-w-full object-contain">
                </div>
                <span class="text-[11px] font-semibold text-slate-700 truncate max-w-full">Netflix</span>
            </a>

            <!-- ChatGPT -->
            <a href="{{ route('brand.detail', 'chatgpt') }}" class="flex flex-col items-center justify-center p-3 rounded-none bg-white hover:bg-slate-50 border border-slate-300/70 transition-colors group aspect-square w-full">
                <div class="w-full h-10 sm:h-12 flex items-center justify-center mb-1 group-hover:scale-105 transition-transform px-1">
                    <img src="{{ asset('assets/logo/OpenAI_logo_2025_(symbol).svg') }}" alt="ChatGPT" class="h-8 sm:h-10 max-w-full object-contain">
                </div>
                <span class="text-[11px] font-semibold text-slate-700 truncate max-w-full">ChatGPT</span>
            </a>

            <!-- Claude -->
            <a href="{{ route('brand.detail', 'claude') }}" class="flex flex-col items-center justify-center p-3 rounded-none bg-white hover:bg-slate-50 border border-slate-300/70 transition-colors group aspect-square w-full">
                <div class="w-full h-10 sm:h-12 flex items-center justify-center mb-1 group-hover:scale-105 transition-transform px-1">
                    <img src="{{ asset('assets/logo/Claude_AI_logo.svg') }}" alt="Claude" class="h-8 sm:h-10 max-w-full object-contain">
                </div>
                <span class="text-[11px] font-semibold text-slate-700 truncate max-w-full">Claude</span>
            </a>

            <!-- Gemini -->
            <a href="{{ route('brand.detail', 'gemini') }}" class="flex flex-col items-center justify-center p-3 rounded-none bg-white hover:bg-slate-50 border border-slate-300/70 transition-colors group aspect-square w-full">
                <div class="w-full h-10 sm:h-12 flex items-center justify-center mb-1 group-hover:scale-105 transition-transform px-1">
                    <img src="{{ asset('assets/logo/Google_Gemini_logo.svg') }}" alt="Gemini" class="h-8 sm:h-10 max-w-full object-contain">
                </div>
                <span class="text-[11px] font-semibold text-slate-700 truncate max-w-full">Gemini</span>
            </a>

            <!-- Spotify -->
            <a href="{{ route('brand.detail', 'spotify') }}" class="flex flex-col items-center justify-center p-3 rounded-none bg-white hover:bg-slate-50 border border-slate-300/70 transition-colors group aspect-square w-full">
                <div class="w-full h-10 sm:h-12 flex items-center justify-center mb-1 group-hover:scale-105 transition-transform px-1">
                    <img src="{{ asset('assets/logo/2024_Spotify_Logo.svg') }}" alt="Spotify" class="h-8 sm:h-10 max-w-full object-contain">
                </div>
                <span class="text-[11px] font-semibold text-slate-700 truncate max-w-full">Spotify</span>
            </a>

            <!-- Canva -->
            <a href="{{ route('brand.detail', 'canva') }}" class="flex flex-col items-center justify-center p-3 rounded-none bg-white hover:bg-slate-50 border border-slate-300/70 transition-colors group aspect-square w-full">
                <div class="w-full h-10 sm:h-12 flex items-center justify-center mb-1 group-hover:scale-105 transition-transform px-1">
                    <img src="{{ asset('assets/logo/Canva_logo.svg') }}" alt="Canva" class="h-8 sm:h-10 max-w-full object-contain">
                </div>
                <span class="text-[11px] font-semibold text-slate-700 truncate max-w-full">Canva</span>
            </a>

            <!-- Instagram -->
            <a href="{{ route('brand.detail', 'instagram') }}" class="flex flex-col items-center justify-center p-3 rounded-none bg-white hover:bg-slate-50 border border-slate-300/70 transition-colors group aspect-square w-full">
                <div class="w-full h-10 sm:h-12 flex items-center justify-center mb-1 group-hover:scale-105 transition-transform px-1">
                    <img src="{{ asset('assets/logo/Instagram_logo_2022.svg') }}" alt="Instagram" class="h-8 sm:h-10 max-w-full object-contain">
                </div>
                <span class="text-[11px] font-semibold text-slate-700 truncate max-w-full">Instagram</span>
            </a>

            <!-- VS Code / Copilot -->
            <a href="{{ route('brand.detail', 'copilot') }}" class="flex flex-col items-center justify-center p-3 rounded-none bg-white hover:bg-slate-50 border border-slate-300/70 transition-colors group aspect-square w-full">
                <div class="w-full h-10 sm:h-12 flex items-center justify-center mb-1 group-hover:scale-105 transition-transform px-1">
                    <img src="{{ asset('assets/logo/Visual_Studio_Code_1.35_icon.svg') }}" alt="VS Code" class="h-8 sm:h-10 max-w-full object-contain">
                </div>
                <span class="text-[11px] font-semibold text-slate-700 truncate max-w-full">Copilot</span>
            </a>

            <!-- Figma -->
            <a href="{{ route('brand.detail', 'figma') }}" class="flex flex-col items-center justify-center p-3 rounded-none bg-white hover:bg-slate-50 border border-slate-300/70 transition-colors group aspect-square w-full">
                <div class="w-full h-10 sm:h-12 flex items-center justify-center mb-1 group-hover:scale-105 transition-transform px-1">
                    <img src="{{ asset('assets/logo/figma-logo.svg') }}" alt="Figma" class="h-8 sm:h-10 max-w-full object-contain">
                </div>
                <span class="text-[11px] font-semibold text-slate-700 truncate max-w-full">Figma</span>
            </a>
        </div>

        <!-- Full Horizontal Divider Line (Edge to Edge) -->
        <div class="border-b border-slate-300/80 mx-0 sm:-mx-3 my-5"></div>

        <!-- Section Header (Centered) -->
        <div class="text-center mb-5">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 font-['Outfit'] tracking-tight">
                Featured Creative & Business Services
            </h3>
            <p class="text-xs text-slate-500 mt-1">
                Solusi kreatif, akademik, branding, dan kebutuhan bisnis digital by <span class="font-semibold text-slate-700">Dyanaf Creative</span>.
            </p>
        </div>

        <!-- Featured Products Grid (Dinamis dari Database Produk) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @php
                // Mapping harga coret / diskon untuk produk
                $originalPrices = [
                    'Makalah (Tanpa Materi)' => 140000,
                    'Makalah (Ada Materi)' => 98000,
                    'Jurnal Ilmiah' => 182000,
                    'CV Kreatif' => 35000,
                    'CV ATS-Friendly' => 84000,
                    'Surat Lamaran Kerja' => 28000,
                    'Gabung & Rapikan PDF' => 14000,
                    'Web Statis (Profile / Portfolio)' => 840000,
                    'Web Dinamis (Business / Custom System)' => 2100000,
                    'Desain Grafis & Promo' => 140000,
                    'Data Entry & Spreadsheet' => 210000,
                    'Jasa Ketik Word' => 140000,
                    'Jasa Excel & Analisis Data' => 182000,
                ];
            @endphp

            @forelse($products as $product)
                @php
                    $originalPrice = $originalPrices[$product->name] ?? null;
                @endphp
                <div class="group relative rounded-none bg-white hover:bg-slate-50 border border-slate-300/80 p-3 transition-colors flex flex-col justify-between overflow-hidden">
                    <div>
                        <div class="h-40 rounded-none bg-[#eef2f6] border border-slate-300/70 flex flex-col items-center justify-center text-slate-400 mb-3 overflow-hidden relative">
                            @if($product->image)
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-regular fa-image text-3xl mb-1"></i>
                                <span class="text-[11px] font-medium text-slate-400">No Image</span>
                            @endif

                            @if($product->category)
                                <span class="absolute top-2 left-2 px-2 py-0.5 bg-slate-900/80 text-white text-[10px] font-medium rounded-none">
                                    {{ $product->category }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 transition-colors leading-snug">{{ $product->name }}</h4>
                            <p class="text-xs text-slate-600 mt-1 line-clamp-2 leading-relaxed">{{ $product->description }}</p>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-slate-300/70 flex items-end justify-between gap-2">
                        <div>
                            @if($originalPrice && $originalPrice > $product->price)
                                <span class="text-[11px] text-slate-400 line-through block leading-none mb-1">
                                    Rp {{ number_format($originalPrice, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-[10px] text-slate-500 block font-medium leading-none mb-1">Harga</span>
                            @endif
                            <span class="text-sm font-bold text-[#1e293b] leading-none">
                                @if($product->price >= 1000000)
                                    Rp {{ rtrim(rtrim(number_format($product->price / 1000000, 1, ',', '.'), '0'), ',') }}JT
                                @else
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                        <a href="https://wa.me/6285881721193?text={{ urlencode('Halo Dyanaf Creative, saya tertarik memesan ' . $product->name) }}"
                           target="_blank"
                           title="Order {{ $product->name }}"
                           class="text-blue-600 hover:text-blue-700 text-lg transition-colors p-1">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-500 text-sm">
                    Belum ada produk yang tersedia.
                </div>
            @endforelse
        </div>

    </div>

</div>
@endsection
