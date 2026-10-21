@extends('layouts.app')

@section('content')
<div class="space-y-4 md:space-y-6">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-900 font-['Outfit']">New Arrival & Restock</h1>
            <p class="text-xs text-slate-500 mt-1">Koleksi brand terbaru, layanan jasa teranyar, dan update restok akun di Dyanaf Creative.</p>
        </div>
    </div>

    <!-- Combined Content Card -->
    <div class="bg-transparent sm:bg-[#f8fafc] p-0 sm:p-4 border-0 sm:border sm:border-slate-300/80 space-y-6">

        <!-- Section: Brand & Akun Terbaru -->
        <div>
            <div class="flex items-center justify-between mb-3 px-1">
                <h2 class="text-sm font-bold text-slate-900 font-['Outfit']">Brand & Akun Baru Ditambahkan</h2>
            </div>

            @if($newBrands->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    @foreach($newBrands as $brand)
                        <a href="{{ route('brand.detail', $brand->slug) }}" 
                           class="group relative overflow-hidden flex flex-col justify-between p-3.5 bg-white hover:bg-slate-50 border border-slate-300/80 transition-colors">
                            <!-- Diagonal Badge / Ribbon Miring -->
                            <div class="absolute top-0 right-0 w-20 h-20 overflow-hidden pointer-events-none z-10">
                                @if($brand->stock > 0)
                                    <div class="absolute top-[14px] right-[-28px] w-28 bg-blue-600 text-white text-[9px] font-extrabold uppercase tracking-wider text-center py-0.5 rotate-45 shadow-sm">
                                        NEW
                                    </div>
                                @else
                                    <div class="absolute top-[14px] right-[-28px] w-28 bg-slate-600 text-white text-[9px] font-extrabold uppercase tracking-wider text-center py-0.5 rotate-45 shadow-sm">
                                        RESTOCK
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col items-center text-center">
                                <div class="w-14 h-14 flex items-center justify-center mb-2.5 p-1 group-hover:scale-105 transition-transform">
                                    @if($brand->logo)
                                        <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}" class="max-h-full max-w-full object-contain">
                                    @else
                                        <div class="w-12 h-12 bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($brand->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-1 w-full">{{ $brand->name }}</h3>
                                <span class="text-[10px] text-slate-500 mt-0.5 truncate w-full">{{ $brand->category }}</span>
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-slate-200 flex items-center justify-between text-[11px]">
                                <span class="text-slate-500 text-[10px]">Stok: <strong class="{{ $brand->stock > 0 ? 'text-emerald-600' : 'text-slate-400' }}">{{ $brand->stock }}</strong></span>
                                <span class="text-slate-600 font-medium group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all inline-flex items-center text-[10px]">
                                    Detail <i class="fa-solid fa-chevron-right text-[8px] ml-1"></i>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-white border border-slate-300/80 p-8 text-center text-slate-500">
                    <i class="fa-solid fa-wand-magic-sparkles text-3xl text-slate-400 mb-2"></i>
                    <p class="text-xs font-medium text-slate-700">Belum ada brand atau akun baru.</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pantau terus halaman ini untuk rilisan akun berikutnya.</p>
                </div>
            @endif
        </div>

        <!-- Full Horizontal Divider Line -->
        <div class="border-b border-slate-300/80 mx-0 sm:-mx-4"></div>

        <!-- Section: Layanan & Produk Baru Ditambahkan -->
        <div>
            <div class="flex items-center justify-between mb-3 px-1">
                <h2 class="text-sm font-bold text-slate-900 font-['Outfit']">Layanan Jasa & Digital Teranyar</h2>
            </div>

            @if($newProducts->isNotEmpty())
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
                            'Website Landing Page' => 2000000,
                            'Web Operasional & POS' => 10000000,
                            'Desain Grafis & Promo' => 140000,
                            'Data Entry & Spreadsheet' => 210000,
                            'Jasa Ketik Word' => 140000,
                            'Jasa Excel & Analisis Data' => 182000,
                        ];
                    @endphp

                    @foreach($newProducts as $product)
                        @php
                            $originalPrice = $originalPrices[$product->name] ?? null;
                        @endphp
                        <div class="group relative bg-white hover:bg-slate-50 border border-slate-300/80 p-3 transition-colors flex flex-col justify-between overflow-hidden cursor-pointer">
                            <!-- Overlay link to product detail -->
                            <a href="{{ route('product.detail', $product) }}" class="absolute inset-0 z-0" aria-label="{{ $product->name }}"></a>

                            <div>
                                <a href="{{ route('product.detail', $product) }}" class="block h-40 bg-[#eef2f6] border border-slate-300/70 overflow-hidden relative mb-3 cursor-pointer">
                                    @if($product->image)
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-400">
                                            <i class="fa-regular fa-image text-3xl mb-1"></i>
                                            <span class="text-[11px] font-medium text-slate-400">No Image</span>
                                        </div>
                                    @endif

                                    @if($product->category)
                                        <span class="absolute top-2 left-2 px-2 py-0.5 bg-slate-900/80 text-white text-[10px] font-medium pointer-events-none z-10">
                                            {{ $product->category }}
                                        </span>
                                    @endif

                                    <span class="absolute top-2 right-2 px-2 py-0.5 bg-blue-600 text-white text-[10px] font-medium leading-normal pointer-events-none z-10">
                                        New
                                    </span>
                                </a>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors leading-snug">{{ $product->name }}</h3>
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
                                <a href="https://wa.me/6285881721193?text={{ urlencode('Halo Dyanaf Creative, saya tertarik memesan produk new arrival: ' . $product->name) }}"
                                   target="_blank"
                                   title="Order {{ $product->name }}"
                                   class="text-blue-600 hover:text-blue-700 text-lg transition-colors p-1">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white border border-slate-300/80 p-8 text-center text-slate-500">
                    <i class="fa-solid fa-box-open text-3xl text-slate-400 mb-2"></i>
                    <p class="text-xs font-medium text-slate-700">Belum ada produk baru.</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Produk baru akan ditampilkan di sini setelah ditambahkan.</p>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
