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
            @php
                $quickBrandSlugs = ['netflix', 'chatgpt', 'claude', 'gemini', 'spotify', 'canva', 'instagram', 'copilot', 'figma'];
                $defaultLogos = [
                    'netflix' => 'assets/logo/Netflix_2015_logo.svg',
                    'chatgpt' => 'assets/logo/OpenAI_logo_2025_(symbol).svg',
                    'claude' => 'assets/logo/Claude_AI_logo.svg',
                    'gemini' => 'assets/logo/Google_Gemini_logo.svg',
                    'spotify' => 'assets/logo/2024_Spotify_Logo.svg',
                    'canva' => 'assets/logo/Canva_logo.svg',
                    'instagram' => 'assets/logo/Instagram_logo_2022.svg',
                    'copilot' => 'assets/logo/Visual_Studio_Code_1.35_icon.svg',
                    'figma' => 'assets/logo/figma-logo.svg',
                ];
                $defaultNames = [
                    'netflix' => 'Netflix',
                    'chatgpt' => 'ChatGPT',
                    'claude' => 'Claude',
                    'gemini' => 'Gemini',
                    'spotify' => 'Spotify',
                    'canva' => 'Canva',
                    'instagram' => 'Instagram',
                    'copilot' => 'Copilot',
                    'figma' => 'Figma',
                ];
            @endphp

            @foreach($quickBrandSlugs as $slug)
                @php
                    $brandItem = $brands->get($slug);
                    $brandName = $brandItem ? $brandItem->name : ($defaultNames[$slug] ?? ucfirst($slug));
                    $brandLogo = ($brandItem && $brandItem->logo) ? asset($brandItem->logo) : asset($defaultLogos[$slug]);
                    $isOutOfStock = $brandItem ? ($brandItem->stock <= 0) : false;
                @endphp
                <a href="{{ route('brand.detail', $slug) }}" class="relative overflow-hidden flex flex-col items-center justify-center p-3 rounded-none bg-white hover:bg-slate-50 border border-slate-300/70 transition-colors group aspect-square w-full {{ $isOutOfStock ? 'max-sm:opacity-85' : '' }}">
                    @if($isOutOfStock)
                        <!-- Diagonal Sold Badge / Ribbon Miring (Hanya di Mobile) -->
                        <div class="sm:hidden absolute top-0 right-0 w-16 h-16 overflow-hidden pointer-events-none z-10">
                            <div class="absolute top-[10px] right-[-26px] w-24 bg-red-600 text-white text-[8px] font-extrabold uppercase tracking-wider text-center py-0.5 rotate-45 shadow-sm">
                                HABIS
                            </div>
                        </div>
                    @endif

                    <div class="w-full h-10 sm:h-12 flex items-center justify-center mb-1 group-hover:scale-105 transition-transform px-1">
                        <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-8 sm:h-10 max-w-full object-contain">
                    </div>
                    <span class="text-[11px] font-semibold text-slate-700 truncate max-w-full text-center">{{ $defaultNames[$slug] ?? $brandName }}</span>
                </a>
            @endforeach
        </div>

        <!-- Full Horizontal Divider Line (Edge to Edge) -->
        <div class="border-b border-slate-300/80 -mx-4 md:-mx-6 my-5"></div>

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
                    'Website Landing Page' => 2000000,
                    'Web Operasional & POS' => 10000000,
                    'Desain Grafis & Promo' => 140000,
                    'Data Entry & Spreadsheet' => 210000,
                    'Jasa Ketik Word' => 140000,
                    'Jasa Excel & Analisis Data' => 182000,
                ];
            @endphp

            @forelse($products->where('is_trending', true) as $product)
                @php
                    $originalPrice = $originalPrices[$product->name] ?? null;
                @endphp
                <div class="group relative rounded-none bg-white hover:bg-slate-50 border border-slate-300/80 p-3 transition-colors flex flex-col justify-between overflow-hidden cursor-pointer">
                    <!-- Overlay link to product detail -->
                    <a href="{{ route('product.detail', $product) }}" class="absolute inset-0 z-0" aria-label="{{ $product->name }}"></a>

                    <div>
                        <a href="{{ route('product.detail', $product) }}" class="block h-40 rounded-none bg-[#eef2f6] border border-slate-300/70 overflow-hidden relative mb-3 cursor-pointer">
                            @if($product->image)
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400">
                                    <i class="fa-regular fa-image text-3xl mb-1"></i>
                                    <span class="text-[11px] font-medium text-slate-400">No Image</span>
                                </div>
                            @endif

                            @if($product->category)
                                <span class="absolute top-2 left-2 px-2 py-0.5 bg-slate-900/80 text-white text-[10px] font-medium rounded-none pointer-events-none z-10">
                                    {{ $product->category }}
                                </span>
                            @endif
                        </a>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors leading-snug">{{ $product->name }}</h4>
                            <p class="text-xs text-slate-600 mt-1 line-clamp-2 leading-relaxed">{{ $product->description }}</p>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-slate-300/70 flex items-end justify-between gap-2 relative z-10">
                        <div>
                            @if($originalPrice && $originalPrice > $product->price)
                                <span class="text-[11px] text-slate-400 line-through block leading-none mb-1">
                                    Rp {{ number_format($originalPrice, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-[10px] text-slate-500 block font-medium leading-none mb-1">Harga</span>
                            @endif
                            <span class="text-sm font-bold text-[#1e293b] leading-none group-hover:text-blue-600 transition-colors">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
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

        <!-- Center Button: Lihat Semua Produk -->
        <div class="mt-6 flex justify-center">
            <a href="{{ route('all-product') }}" 
               class="group inline-flex items-center justify-center space-x-2 px-6 py-2.5 bg-white text-slate-800 border border-slate-300 rounded-none text-xs font-semibold">
                <span>Lihat Semua Produk</span>
                <i class="fa-solid fa-arrow-right text-[11px] transition-transform duration-200 group-hover:translate-x-1"></i>
            </a>
        </div>

        <!-- Full Width Rating & Trust Strip -->
        <div class="bg-white border-y border-slate-300/80 -mx-4 sm:mx-0 my-6 sm:my-8 py-5 sm:py-6 px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-6 md:gap-8 text-sm text-slate-700">
            <!-- Rating Information -->
            <div class="flex items-center space-x-2.5 font-medium">
                <i class="fa-solid fa-star text-amber-400 text-lg sm:text-xl shrink-0"></i>
                <span class="text-slate-900 font-extrabold text-base">4.9/5</span>
                <span class="text-slate-500 text-xs sm:text-sm">dari 250+ pengguna</span>
            </div>

            <!-- Dot separator on sm screens -->
            <span class="hidden sm:inline-block text-slate-300 font-bold text-sm select-none">•</span>

            <!-- Instant Delivery & 24/7 Support -->
            <div class="flex items-center space-x-4 sm:space-x-6 font-semibold text-slate-800 text-xs sm:text-sm">
                <div class="flex items-center space-x-1.5">
                    <i class="fa-solid fa-bolt text-blue-600 text-sm"></i>
                    <span>Instan</span>
                </div>
                <span class="text-slate-300 font-bold text-sm select-none">•</span>
                <div class="flex items-center space-x-1.5">
                    <i class="fa-solid fa-headset text-emerald-600 text-sm"></i>
                    <span>Support 24/7</span>
                </div>
            </div>
        </div>

        <!-- Section Header: Developer Portfolio (Centered) -->
        <div class="text-center mb-5">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 font-['Outfit'] tracking-tight">
                Meet the Developer
            </h3>
            <p class="text-xs text-slate-500 mt-1">
                Portofolio resmi pengembang dan arsitek sistem platform <span class="font-semibold text-slate-700">Dyanaf Creative</span>.
            </p>
        </div>

        <!-- Portfolio & Profile Section: Rofiq Kholid (Layout Rapi, Terstruktur & Menarik) -->
        <div class="py-6 sm:py-8 px-3 sm:px-4 max-w-4xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-7 md:gap-8 items-center">
                
                <!-- Sisi Kiri: Profil, Info Singkat & Tombol Tautan -->
                <div class="md:col-span-6 flex flex-col items-center md:items-start text-center md:text-left space-y-5">
                    <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4.5 text-center sm:text-left">
                        <!-- Silhouette Avatar Bulat -->
                        <div class="w-14 h-14 rounded-full bg-white border border-slate-300 text-slate-400 flex items-center justify-center shrink-0 mx-auto sm:mx-0">
                            <i class="fa-solid fa-user text-2xl text-slate-400"></i>
                        </div>
                        <div>
                            <h4 class="text-lg sm:text-xl font-bold text-slate-900 font-['Outfit'] leading-tight">Rofiq Kholid</h4>
                            <p class="text-xs text-slate-500 font-medium mt-1.5">Software Engineer • Cybersecurity Enthusiast • DevOps</p>
                        </div>
                    </div>

                    <!-- Ringkasan Bio -->
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-md sm:max-w-none pt-1">
                        Building responsive, high-performance, secure, and user-friendly web systems powered by modern technologies.
                    </p>

                    <!-- Tombol Link Sosial & Portfolio Resmi (Icon Only) -->
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 pt-2 w-full sm:w-auto">
                        <a href="https://rofiqkholid.dyanaf.com" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           title="Website: rofiqkholid.Dyanaf.com"
                           class="inline-flex items-center justify-center w-8 h-8 bg-white text-slate-700 hover:text-blue-600 border border-slate-300 rounded-none transition-colors">
                            <i class="fa-solid fa-link text-xs"></i>
                        </a>

                        <a href="https://github.com/rofiqkholid" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           title="GitHub: rofiqkholid"
                           class="inline-flex items-center justify-center w-8 h-8 bg-white text-slate-700 hover:text-black border border-slate-300 rounded-none transition-colors">
                            <i class="fa-brands fa-github text-sm"></i>
                        </a>

                        <a href="https://www.linkedin.com/in/rofiq-kholid/" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           title="LinkedIn: Rofiq Kholid"
                           class="inline-flex items-center justify-center w-8 h-8 bg-white text-slate-700 hover:text-[#0A66C2] border border-slate-300 rounded-none transition-colors">
                            <i class="fa-brands fa-linkedin text-sm"></i>
                        </a>

                        <a href="https://instagram.com/dhenrofiq" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           title="Instagram: @dhenrofiq"
                           class="inline-flex items-center justify-center w-8 h-8 bg-white text-slate-700 hover:text-pink-600 border border-slate-300 rounded-none transition-colors">
                            <i class="fa-brands fa-instagram text-sm"></i>
                        </a>

                        <a href="mailto:kholidrofiq@gmail.com" 
                           title="Email: kholidrofiq@gmail.com"
                           class="inline-flex items-center justify-center w-8 h-8 bg-white text-slate-700 hover:text-emerald-600 border border-slate-300 rounded-none transition-colors">
                            <i class="fa-solid fa-envelope text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Sisi Kanan: Metrik & Statistik Profesional (Bersih, Berdampingan) -->
                <div class="md:col-span-6 border-t md:border-t-0 md:border-l border-slate-200/90 pt-6 md:pt-2 md:pl-8">
                    <div class="grid grid-cols-2 gap-y-5 gap-x-4 text-center md:text-left">
                        <div class="p-1">
                            <span class="text-xl sm:text-2xl font-semibold text-slate-800 font-['Outfit'] block leading-none">8+</span>
                            <span class="text-[11px] sm:text-xs text-slate-500 mt-1 block">Years in IT</span>
                        </div>
                        <div class="p-1">
                            <span class="text-xl sm:text-2xl font-semibold text-slate-800 font-['Outfit'] block leading-none">3+</span>
                            <span class="text-[11px] sm:text-xs text-slate-500 mt-1 block">Years in Cybersecurity</span>
                        </div>
                        <div class="p-1">
                            <span class="text-xl sm:text-2xl font-semibold text-slate-800 font-['Outfit'] block leading-none">150+</span>
                            <span class="text-[11px] sm:text-xs text-slate-500 mt-1 block">Projects Completed</span>
                        </div>
                        <div class="p-1">
                            <span class="text-xl sm:text-2xl font-semibold text-slate-800 font-['Outfit'] block leading-none">100%</span>
                            <span class="text-[11px] sm:text-xs text-slate-500 mt-1 block">Security & Performance</span>
                        </div>
                        <div class="col-span-2 pt-3 border-t border-slate-100 flex items-center justify-center md:justify-start space-x-2">
                            <span class="text-xl sm:text-2xl font-semibold text-slate-800 font-['Outfit'] leading-none">99%</span>
                            <span class="text-[11px] sm:text-xs text-slate-500 leading-tight">UI/UX Satisfaction Rating</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Full Horizontal Divider Line (Edge to Edge) -->
        <div class="border-b border-slate-300/80 -mx-4 md:-mx-6 my-5"></div>

        <!-- Section Header: FAQ (Centered di atas sama seperti yang lain) -->
        <div class="text-center mb-5">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 font-['Outfit'] tracking-tight">
                Pertanyaan Yang Sering Diajukan
            </h3>
            <p class="text-xs text-slate-500 mt-1">
                Jawaban seputar layanan, akun premium, pengerjaan tugas, dan keamanan transaksi di Dyanaf Creative.
            </p>
        </div>

        <!-- FAQ Section (Accordion List & Note) -->
        <div class="py-2 px-1" x-data="{ activeFaq: null }">
            <div class="max-w-3xl mx-auto space-y-2.5">
                    
                    <!-- FAQ 1 -->
                    <div class="bg-white border border-slate-300/80 rounded-none overflow-hidden transition-colors">
                        <button type="button" 
                                @click="activeFaq = (activeFaq === 1 ? null : 1)" 
                                class="w-full py-3.5 px-4 flex items-center justify-between text-left cursor-pointer transition-colors hover:bg-slate-50/80">
                            <span class="text-xs sm:text-sm font-bold text-slate-900">Dyanaf Creative itu apa?</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200 shrink-0 ml-3"
                               :class="activeFaq === 1 ? 'rotate-180 text-blue-600' : ''"></i>
                        </button>
                        <div x-show="activeFaq === 1" 
                             x-collapse 
                             x-cloak 
                             class="px-4 pb-3.5 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/40">
                            Dyanaf Creative adalah platform kreatif terpadu yang menyediakan aneka gift & kado spesial (bouquet bunga/hadiah, kartu ucapan custom), layanan digital & tools kreatif premium (Netflix, ChatGPT, Canva, Spotify), pembuatan website, hingga bantuan pengerjaan tugas terpercaya.
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="bg-white border border-slate-300/80 rounded-none overflow-hidden transition-colors">
                        <button type="button" 
                                @click="activeFaq = (activeFaq === 2 ? null : 2)" 
                                class="w-full py-3.5 px-4 flex items-center justify-between text-left cursor-pointer transition-colors hover:bg-slate-50/80">
                            <span class="text-xs sm:text-sm font-bold text-slate-900">Apakah layanan dan akun ini aman digunakan?</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200 shrink-0 ml-3"
                               :class="activeFaq === 2 ? 'rotate-180 text-blue-600' : ''"></i>
                        </button>
                        <div x-show="activeFaq === 2" 
                             x-collapse 
                             x-cloak 
                             class="px-4 pb-3.5 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/40">
                            Sangat aman. Setiap akun premium dan lisensi software diaktifkan secara resmi (invite email pribadi / shared private profile PIN) serta dilengkapi garansi penuh selama masa aktif paket yang Anda pilih.
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="bg-white border border-slate-300/80 rounded-none overflow-hidden transition-colors">
                        <button type="button" 
                                @click="activeFaq = (activeFaq === 3 ? null : 3)" 
                                class="w-full py-3.5 px-4 flex items-center justify-between text-left cursor-pointer transition-colors hover:bg-slate-50/80">
                            <span class="text-xs sm:text-sm font-bold text-slate-900">Bagaimana cara akses dan pemakaiannya setelah order?</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200 shrink-0 ml-3"
                               :class="activeFaq === 3 ? 'rotate-180 text-blue-600' : ''"></i>
                        </button>
                        <div x-show="activeFaq === 3" 
                             x-collapse 
                             x-cloak 
                             class="px-4 pb-3.5 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/40">
                            Setelah pemesanan terkonfirmasi, instruksi akses (link invite email, login akun, atau dokumen pengerjaan) akan dikirimkan secara instan langsung melalui WhatsApp admin atau menu Download Asset di akun Anda.
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="bg-white border border-slate-300/80 rounded-none overflow-hidden transition-colors">
                        <button type="button" 
                                @click="activeFaq = (activeFaq === 4 ? null : 4)" 
                                class="w-full py-3.5 px-4 flex items-center justify-between text-left cursor-pointer transition-colors hover:bg-slate-50/80">
                            <span class="text-xs sm:text-sm font-bold text-slate-900">Bisa dipakai di perangkat apa saja?</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200 shrink-0 ml-3"
                               :class="activeFaq === 4 ? 'rotate-180 text-blue-600' : ''"></i>
                        </button>
                        <div x-show="activeFaq === 4" 
                             x-collapse 
                             x-cloak 
                             class="px-4 pb-3.5 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/40">
                            Bisa digunakan di seluruh perangkat: Smartphone (Android & iOS), Tablet, Laptop, PC, hingga Smart TV tergantung jenis paket langganan yang Anda pilih.
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="bg-white border border-slate-300/80 rounded-none overflow-hidden transition-colors">
                        <button type="button" 
                                @click="activeFaq = (activeFaq === 5 ? null : 5)" 
                                class="w-full py-3.5 px-4 flex items-center justify-between text-left cursor-pointer transition-colors hover:bg-slate-50/80">
                            <span class="text-xs sm:text-sm font-bold text-slate-900">Kalau ada kendala atau akun error, hubungi ke mana?</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200 shrink-0 ml-3"
                               :class="activeFaq === 5 ? 'rotate-180 text-blue-600' : ''"></i>
                        </button>
                        <div x-show="activeFaq === 5" 
                             x-collapse 
                             x-cloak 
                             class="px-4 pb-3.5 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/40">
                            Tim support kami aktif melayani 24/7. Anda cukup menghubungi customer support via WhatsApp resmi Dyanaf Creative untuk klaim garansi cepat, pergantian akun, atau bantuan teknis.
                        </div>
                    </div>

                    <!-- FAQ 6 -->
                    <div class="bg-white border border-slate-300/80 rounded-none overflow-hidden transition-colors">
                        <button type="button" 
                                @click="activeFaq = (activeFaq === 6 ? null : 6)" 
                                class="w-full py-3.5 px-4 flex items-center justify-between text-left cursor-pointer transition-colors hover:bg-slate-50/80">
                            <span class="text-xs sm:text-sm font-bold text-slate-900">Mengapa harganya bisa lebih terjangkau?</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200 shrink-0 ml-3"
                               :class="activeFaq === 6 ? 'rotate-180 text-blue-600' : ''"></i>
                        </button>
                        <div x-show="activeFaq === 6" 
                             x-collapse 
                             x-cloak 
                             class="px-4 pb-3.5 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/40">
                            Kami memanfaatkan paket resmi skala grup/keluarga (*family plan* & *team subscription*) serta sistem manajemen otomatis yang efisien sehingga biaya langganan per orang menjadi jauh lebih hemat dan rasional.
                        </div>
                    </div>



                </div>
        </div>

        <!-- Thank You Banner Section: Dyanaf Creative -->
        <div class="mt-10 pt-8 pb-3 border-t border-slate-200/90 text-center max-w-2xl mx-auto px-4">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-pink-50 border border-pink-200/80 text-pink-500 mb-3.5">
                <i class="fa-solid fa-heart text-lg text-pink-500"></i>
            </div>
            <h4 class="text-base sm:text-lg font-bold text-slate-900 font-['Outfit'] tracking-tight">
                Terima Kasih Telah Mengunjungi Dyanaf Creative
            </h4>
            <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                Kami siap menemani setiap momen spesial Anda—mulai dari kreasi bouquet dan gift, hingga aneka layanan kreatif dan kebutuhan digital pilihan. Senang bisa menjadi bagian dari hari Anda!
            </p>
            <div class="mt-4 flex items-center justify-center space-x-2 text-xs font-semibold text-slate-500">
                <span>Handcrafted & Curated with care</span>
                <span>•</span>
                <span class="text-pink-600 font-bold">Dyanaf Creative</span>
            </div>
        </div>

    </div>

</div>
@endsection
