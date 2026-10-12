@extends('layouts.app')

@section('content')
<div class="space-y-4 md:space-y-6">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-900 font-['Outfit']">Sedang Trending Saat Ini</h1>
            <p class="text-xs text-slate-500 mt-1">Produk dan brand terpopuler dengan permintaan tertinggi di Dyanaf Creative.</p>
        </div>
    </div>

    <!-- Combined Trending Content Card -->
    <div class="bg-transparent sm:bg-[#f8fafc] p-0 sm:p-4 border-0 sm:border sm:border-slate-300/80 space-y-6">

        <!-- Section: Brand & Layanan Populer Trending -->
        <div>
            <div class="flex items-center justify-between mb-3 px-1">
                <h2 class="text-sm font-bold text-slate-900 font-['Outfit']">Brand & Akun Premium Trending</h2>
            </div>

            @if($trendingBrands->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    @foreach($trendingBrands as $brand)
                        <a href="{{ route('brand.detail', $brand->slug) }}" 
                           class="group relative flex flex-col justify-between p-3.5 bg-white hover:bg-slate-50 border border-slate-300/80 transition-colors">
                            <!-- Trending Badge -->
                            <div class="absolute top-2 right-2">
                                <span class="inline-flex items-center px-1.5 py-0.5 bg-rose-600 text-white text-[9px] font-bold rounded-none">
                                    <i class="fa-solid fa-fire text-[8px] mr-1"></i> HOT
                                </span>
                            </div>

                            <div class="flex flex-col items-center text-center">
                                <div class="w-14 h-14 flex items-center justify-center mb-2.5 p-1 group-hover:scale-105 transition-transform">
                                    @if($brand->logo)
                                        <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}" class="max-h-full max-w-full object-contain">
                                    @else
                                        <div class="w-12 h-12 bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-xs rounded-none">
                                            {{ strtoupper(substr($brand->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-1 w-full">{{ $brand->name }}</h3>
                                <span class="text-[10px] text-slate-500 mt-0.5 truncate w-full">{{ $brand->category }}</span>
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-slate-200 flex items-center justify-between text-[11px]">
                                <span class="text-slate-500 text-[10px]">Stok: <strong class="text-slate-700">{{ $brand->stock }}</strong></span>
                                <span class="text-slate-600 font-medium group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all inline-flex items-center text-[10px]">
                                    Detail <i class="fa-solid fa-chevron-right text-[8px] ml-1"></i>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-white border border-slate-300/80 p-8 text-center text-slate-500">
                    <i class="fa-solid fa-fire-burner text-3xl text-slate-400 mb-2"></i>
                    <p class="text-xs font-medium text-slate-700">Belum ada brand yang diaktifkan sebagai trending.</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Aktifkan status trending pada Brand Master Management.</p>
                </div>
            @endif
        </div>

        <!-- Full Horizontal Divider Line (Edge to Edge) -->
        <div class="border-b border-slate-300/80 mx-0 sm:-mx-4"></div>

        <!-- Section: Produk Marketplace Trending -->
        <div>
            <div class="flex items-center justify-between mb-3 px-1">
                <h2 class="text-sm font-bold text-slate-900 font-['Outfit']">Produk Jasa & Digital Paling Dicari</h2>
            </div>

            @if($trendingProducts->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @php
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

                    @foreach($trendingProducts as $product)
                        @php
                            $originalPrice = $originalPrices[$product->name] ?? null;
                        @endphp
                        <div class="group relative bg-white hover:bg-slate-50 border border-slate-300/80 p-3 transition-colors flex flex-col justify-between overflow-hidden">
                            <div>
                                <div class="h-40 bg-[#eef2f6] border border-slate-300/70 flex flex-col items-center justify-center text-slate-400 mb-3 overflow-hidden relative">
                                    @if($product->image)
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-regular fa-image text-3xl mb-1"></i>
                                        <span class="text-[11px] font-medium text-slate-400">No Image</span>
                                    @endif

                                    @if($product->category)
                                        <span class="absolute top-2 left-2 px-2 py-0.5 bg-slate-900/80 text-white text-[10px] font-medium">
                                            {{ $product->category }}
                                        </span>
                                    @endif

                                    <span class="absolute top-2 right-2 px-2 py-0.5 bg-rose-600 text-white text-[10px] font-bold rounded-none shadow-none">
                                        <i class="fa-solid fa-fire text-[9px] mr-1"></i> TRENDING
                                    </span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 leading-snug">{{ $product->name }}</h3>
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
                                <a href="https://wa.me/6285881721193?text={{ urlencode('Halo Dyanaf Creative, saya tertarik memesan produk trending: ' . $product->name) }}"
                                   target="_blank"
                                   title="Order {{ $product->name }}"
                                   class="text-rose-600 hover:text-rose-700 text-lg transition-colors p-1">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white border border-slate-300/80 p-8 text-center text-slate-500">
                    <i class="fa-solid fa-box-open text-3xl text-slate-400 mb-2"></i>
                    <p class="text-xs font-medium text-slate-700">Belum ada produk yang diaktifkan sebagai trending.</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Aktifkan status trending pada Product Master Management.</p>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
