@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <!-- Breadcrumb & Back -->
    <nav aria-label="Breadcrumb" class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="/" class="hover:text-blue-600 transition-colors inline-flex items-center gap-1.5">
            <i class="fa-solid fa-house text-xs text-slate-500 relative -top-[0.5px]"></i>
            <span>Marketplace</span>
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 relative -top-[0.5px]"></i>
        <span class="font-semibold text-slate-800">{{ $brand['name'] }}</span>
    </nav>

    <!-- Brand Header Banner Card -->
    <div class="bg-white border border-slate-300/80 p-5 sm:p-6 rounded-none relative overflow-hidden">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 sm:gap-8">
            <!-- Brand Logo -->
            <div class="w-24 h-24 sm:w-28 sm:h-28 flex items-center justify-center shrink-0">
                <img src="{{ asset($brand['logo']) }}" alt="{{ $brand['name'] }}" class="max-h-full max-w-full object-contain">
            </div>

            <!-- Brand Info & Details -->
            <div class="flex-1 text-center sm:text-left">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-['Outfit'] leading-none mb-3 sm:mb-3.5">{{ $brand['name'] }}</h1>
                
                <p class="text-xs sm:text-sm text-slate-600 leading-normal max-w-3xl mb-1.5">
                    {{ $brand['description'] }}
                </p>

                <!-- Quick Highlights -->
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-x-4 gap-y-1 text-xs text-slate-600 leading-none">
                    <div class="flex items-center space-x-1.5">
                        <i class="fa-solid fa-bolt text-amber-500"></i>
                        <span>Proses Instan & Aman</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <i class="fa-solid fa-headset text-blue-600"></i>
                        <span>Full Garansi Durasi</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                        <span>Konsultasi Gratis via Admin</span>
                    </div>
                </div>

                <!-- Dyanaf Point Reward & Stock Info -->
                <div class="pt-2 flex flex-wrap items-center justify-center sm:justify-start gap-x-4 gap-y-1.5 text-xs text-slate-700 leading-none">
                    <!-- Dyanaf Point Badge -->
                    @auth
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-gradient-to-r from-purple-50 via-fuchsia-50 to-rose-50 border border-fuchsia-200/80 rounded-full text-[11px] font-medium text-purple-900" title="Dapatkan 3 Dyanaf Point setelah transaksi berhasil">
                            <span class="inline-flex items-center justify-center w-3.5 h-3.5 rounded-full bg-gradient-to-r from-purple-600 to-rose-500 text-white text-[8px]">
                                <i class="fa-solid fa-bolt text-amber-300"></i>
                            </span>
                            <span>Dapat <strong class="font-bold text-fuchsia-700">+3</strong> Dyanaf Point</span>
                        </div>
                    @else
                        <a href="{{ route('auth.google') }}" class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-slate-50 hover:bg-purple-50/60 border border-slate-200 hover:border-fuchsia-200 rounded-full text-[11px] text-slate-600 hover:text-purple-700 transition-colors group cursor-pointer" title="Login dengan Google untuk mendapatkan 3 Dyanaf Point">
                            <span class="inline-flex items-center justify-center w-3.5 h-3.5 rounded-full bg-slate-300 group-hover:bg-gradient-to-r group-hover:from-purple-600 group-hover:to-rose-500 text-white text-[8px] transition-all">
                                <i class="fa-solid fa-bolt text-white group-hover:text-amber-300"></i>
                            </span>
                            <span>Login untuk <strong class="font-semibold text-slate-800 group-hover:text-fuchsia-700">+3 Point</strong></span>
                        </a>
                    @endauth

                    <span class="font-bold">
                        @if($brand->stock > 0)
                            Stok: {{ $brand->stock }}
                        @else
                            Stok: 0 (Habis)
                        @endif
                    </span>

                    @if($brand->restock_date)
                        <span class="font-bold text-slate-600">Restok: {{ \Carbon\Carbon::parse($brand->restock_date)->locale('id')->translatedFormat('d F Y') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Product / Package List Section -->
    <div class="bg-white p-4 sm:p-5 border border-slate-300/80 rounded-none space-y-4">
        <div class="border-b border-slate-300/80 pb-3">
            <h2 class="text-sm sm:text-base font-bold text-slate-900 font-['Outfit']">
                Pilihan Paket & Layanan {{ $brand['name'] }}
            </h2>
            <p class="text-xs text-slate-500">Pilih paket langganan atau bantuan setup sesuai kebutuhan Anda.</p>
        </div>

        <!-- Package Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($brand['packages'] as $package)
                <div class="relative overflow-hidden bg-[#f8fafc] hover:bg-[#eef2f6] border border-slate-300/80 p-4 rounded-none flex flex-col justify-between transition-colors {{ $brand->stock <= 0 ? 'opacity-75' : '' }}">
                    @if($brand->stock <= 0)
                        <!-- Diagonal Sold Badge / Ribbon -->
                        <div class="absolute top-0 right-0 w-24 h-24 overflow-hidden pointer-events-none z-10">
                            <div class="absolute top-[18px] right-[-32px] w-32 bg-red-600 text-white text-[10px] font-extrabold uppercase tracking-widest text-center py-1 rotate-45 shadow-sm">
                                HABIS
                            </div>
                        </div>
                    @endif

                    <div>
                        <div class="mb-2">
                            <h3 class="text-sm font-bold text-slate-900 {{ $brand->stock <= 0 ? 'pr-10' : '' }}">{{ $package['title'] }}</h3>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed mb-3">
                            {{ $package['desc'] }}
                        </p>

                        <!-- Feature list -->
                        @if(!empty($package['features']))
                            <ul class="space-y-1.5 mb-4 text-xs text-slate-600 border-t border-slate-100 pt-2.5">
                                @foreach($package['features'] as $feat)
                                    <li class="flex items-center space-x-2">
                                        <i class="fa-solid fa-check text-emerald-500 text-[10px]"></i>
                                        <span>{{ $feat }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-slate-300/70 flex items-center justify-between mt-auto">
                        <div>
                            @if(!empty($package['original_price']))
                                <span class="text-[10px] text-slate-400 line-through block leading-none mb-1">
                                    Rp {{ number_format($package['original_price'], 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-[10px] text-slate-500 block font-medium leading-none mb-1">Harga Mulai</span>
                            @endif
                            <span class="text-sm sm:text-base font-bold text-[#1e293b] leading-none">
                                Rp {{ number_format($package['price'], 0, ',', '.') }}
                            </span>
                        </div>

                        @if($brand->stock > 0)
                            <a href="https://wa.me/6285881721193?text={{ urlencode('Halo Dyanaf Creative, saya tertarik memesan ' . $brand['name'] . ' - ' . $package['title'] . ' (Rp ' . number_format($package['price'], 0, ',', '.') . ')') }}"
                               target="_blank"
                               title="Order {{ $package['title'] }}"
                               class="text-blue-600 hover:text-blue-700 text-lg transition-colors p-1">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </a>
                        @else
                            <button type="button" 
                                    disabled 
                                    title="Stok Habis"
                                    class="text-slate-300 cursor-not-allowed text-lg p-1">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-xs text-slate-500">
                    Paket layanan sedang dipersiapkan. Hubungi admin untuk pemesanan langsung.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
