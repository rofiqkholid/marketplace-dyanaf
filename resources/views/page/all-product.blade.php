@extends('layouts.app')

@section('content')
<div class="space-y-4 md:space-y-6" x-data="{ 
    searchQuery: '',
    selectedCategory: 'all'
}">

    <!-- Page Header -->
    <div>
        <h1 class="text-xl md:text-2xl font-bold text-slate-900 font-['Outfit']">Katalog Semua Produk</h1>
        <p class="text-xs text-slate-500 mt-1">Daftar lengkap layanan pengerjaan tugas, aplikasi web, dan aset digital Dyanaf Creative.</p>
    </div>

    <!-- Combined Filter & Search Bar Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2.5">
        <!-- Filter Category Tabs (Horizontal Scrollable on Mobile) -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none text-xs flex-1">
            <button type="button"
                    @click="selectedCategory = 'all'"
                    :class="selectedCategory === 'all' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50'"
                    class="px-3 py-1.5 border font-medium whitespace-nowrap transition-colors shrink-0">
                Semua ({{ $products->count() }})
            </button>
            @foreach($categories as $cat)
                @php
                    $catCount = $products->where('category', $cat)->count();
                @endphp
                <button type="button"
                        @click="selectedCategory = '{{ $cat }}'"
                        :class="selectedCategory === '{{ $cat }}' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50'"
                        class="px-3 py-1.5 border font-medium whitespace-nowrap transition-colors shrink-0">
                    {{ $cat }} ({{ $catCount }})
                </button>
            @endforeach
        </div>

        <!-- Search Bar Input (Integrated on right on Desktop, full width on Mobile) -->
        <div class="w-full md:w-64 shrink-0">
            <div class="relative">
                <input type="text" 
                       x-model="searchQuery"
                       placeholder="Cari produk / layanan..." 
                       class="w-full pl-8 pr-7 py-1.5 bg-white border border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-600 transition-colors">
                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[11px] pointer-events-none"></i>
                <button type="button" 
                        x-show="searchQuery.length > 0" 
                        @click="searchQuery = ''"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-[11px]">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="bg-transparent sm:bg-[#f8fafc] p-0 sm:p-4 border-0 sm:border sm:border-slate-300/80">
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

        @if($products->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($products as $product)
                    @php
                        $originalPrice = $originalPrices[$product->name] ?? null;
                    @endphp
                    <div x-data="{
                            name: {{ json_encode($product->name) }},
                            category: {{ json_encode($product->category ?? '') }},
                            description: {{ json_encode($product->description ?? '') }},
                            matches() {
                                const q = searchQuery.toLowerCase().trim();
                                const matchCategory = selectedCategory === 'all' || this.category === selectedCategory;
                                if (!matchCategory) return false;
                                if (!q) return true;
                                return this.name.toLowerCase().includes(q) || 
                                       this.description.toLowerCase().includes(q) || 
                                       this.category.toLowerCase().includes(q);
                            }
                         }"
                         x-show="matches()"
                         x-transition
                         class="group relative bg-white hover:bg-slate-50 border border-slate-300/80 p-3 transition-colors flex flex-col justify-between overflow-hidden">
                        <div>
                            <div class="h-40 bg-[#eef2f6] border border-slate-300/70 flex flex-col items-center justify-center text-slate-400 mb-3 overflow-hidden relative">
                                @if($product->image)
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fa-regular fa-image text-3xl mb-1"></i>
                                    <span class="text-[11px] font-medium text-slate-400">No Image</span>
                                @endif

                                @if($product->category)
                                    <span class="absolute top-2 left-2 px-2 py-0.5 bg-slate-900/80 text-white text-[10px] font-medium leading-normal">
                                        {{ $product->category }}
                                    </span>
                                @endif

                                @if($product->is_trending)
                                    <span class="absolute top-2 right-2 px-2 py-0.5 bg-rose-600 text-white text-[10px] font-medium leading-normal">
                                        Trending
                                    </span>
                                @endif
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
                @endforeach
            </div>
        @else
            <div class="bg-white border border-slate-300/80 p-8 text-center text-slate-500">
                <i class="fa-solid fa-box-open text-3xl text-slate-400 mb-2"></i>
                <p class="text-xs font-medium text-slate-700">Belum ada produk yang tersedia.</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Produk baru akan segera diupdate.</p>
            </div>
        @endif

    </div>

</div>
@endsection
