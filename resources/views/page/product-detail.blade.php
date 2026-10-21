@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
        <a href="/" class="hover:text-blue-600 transition-colors inline-flex items-center gap-1.5">
            <i class="fa-solid fa-house text-xs text-slate-500 relative -top-[0.5px]"></i>
            <span>Marketplace</span>
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <a href="{{ route('all-product') }}" class="hover:text-blue-600 transition-colors">
            Semua Produk
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="font-semibold text-slate-800 truncate max-w-[200px] sm:max-w-none">{{ $product->name }}</span>
    </nav>

    <!-- Main Product Detail Card -->
    <div class="bg-white border border-slate-300/80 p-4 sm:p-6 rounded-none">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">
            
            <!-- Left: Product Image / Visual Showcase -->
            <div class="lg:col-span-5 space-y-3">
                <div class="relative bg-[#f1f5f9] border border-slate-300/80 rounded-none overflow-hidden aspect-[4/3] flex items-center justify-center group">
                    @if($product->image)
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex flex-col items-center justify-center text-slate-400 p-6 text-center">
                            <i class="fa-regular fa-image text-5xl mb-2"></i>
                            <span class="text-xs font-medium">Gambar Tidak Tersedia</span>
                        </div>
                    @endif

                    <!-- Badges on Image -->
                    <div class="absolute top-3 inset-x-3 flex items-center justify-between pointer-events-none z-10">
                        <div>
                            @if($product->category)
                                <span class="px-2.5 py-1 bg-slate-900/85 text-white text-[11px] font-medium tracking-wide">
                                    {{ $product->category }}
                                </span>
                            @endif
                        </div>
                        <div>
                            @if($product->is_trending)
                                <span class="px-2.5 py-1 bg-rose-600 text-white text-[11px] font-medium tracking-wide flex items-center gap-1">
                                    <i class="fa-solid fa-fire text-[10px]"></i> Trending
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Product Information & Purchase CTAs -->
            <div class="lg:col-span-7 flex flex-col justify-between space-y-5">
                <div class="space-y-4">
                    <!-- Title -->
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-['Outfit'] leading-tight">
                            {{ $product->name }}
                        </h1>
                    </div>

                    <!-- Description directly below title -->
                    <div class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                        {{ $product->description }}
                    </div>

                    <!-- Price section -->
                    <div class="flex items-baseline gap-2.5">
                        <span class="text-2xl sm:text-3xl font-semibold text-slate-900">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                        @if($originalPrice && $originalPrice > $product->price)
                            <span class="text-sm text-slate-400 line-through">
                                Rp {{ number_format($originalPrice, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>

                    <!-- Service Highlights (Cards Grid) -->
                    <div class="space-y-2.5 pt-2">
                        <div>
                            <h3 class="text-xs font-bold text-slate-800">Keunggulan Layanan</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div class="flex items-center gap-2.5 p-2.5 bg-slate-50 border border-slate-200/90 rounded-lg">
                                <div class="w-7 h-7 bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 rounded-md">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </div>
                                <span class="text-xs font-medium text-slate-700">Hasil rapi, teliti & sesuai panduan</span>
                            </div>
                            <div class="flex items-center gap-2.5 p-2.5 bg-slate-50 border border-slate-200/90 rounded-lg">
                                <div class="w-7 h-7 bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 rounded-md">
                                    <i class="fa-regular fa-comments text-xs"></i>
                                </div>
                                <span class="text-xs font-medium text-slate-700">Konsultasi gratis via WhatsApp</span>
                            </div>
                            <div class="flex items-center gap-2.5 p-2.5 bg-slate-50 border border-slate-200/90 rounded-lg">
                                <div class="w-7 h-7 bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 rounded-md">
                                    <i class="fa-solid fa-rotate-left text-xs"></i>
                                </div>
                                <span class="text-xs font-medium text-slate-700">Garansi perbaikan/revisi minor</span>
                            </div>
                            <div class="flex items-center gap-2.5 p-2.5 bg-slate-50 border border-slate-200/90 rounded-lg">
                                <div class="w-7 h-7 bg-purple-100 text-purple-600 flex items-center justify-center shrink-0 rounded-md">
                                    <i class="fa-regular fa-clock text-xs"></i>
                                </div>
                                <span class="text-xs font-medium text-slate-700">Pengerjaan tepat waktu</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons & Share Area -->
                @php
                    $waText = "Halo Dyanaf Creative, saya tertarik untuk memesan produk:\n\n*{$product->name}*\nHarga: Rp " . number_format($product->price, 0, ',', '.') . "\n\nMohon info detail dan langkah pemesanannya. Terima kasih!";
                    $waUrl = "https://wa.me/6285881721193?text=" . urlencode($waText);
                    $currentUrl = request()->fullUrl();
                @endphp
                <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                     x-data="{ copied: false, loadingPayment: false }">
                    <!-- Left: CTA Buttons -->
                    <div class="grid grid-cols-2 sm:flex sm:items-center gap-2.5 w-full sm:w-auto">
                        <button type="button"
                                @click="$dispatch('open-payment-modal')"
                                class="inline-flex items-center justify-center gap-2 px-4 sm:px-5 h-10 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold transition-colors cursor-pointer">
                            <i class="fa-solid fa-cart-shopping text-xs"></i>
                            <span>Order Sekarang</span>
                        </button>

                        <a href="https://wa.me/6285881721193?text={{ urlencode('Halo Dyanaf Creative, saya ingin tanya mengenai layanan ' . $product->name) }}"
                           target="_blank"
                           class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 h-10 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-xs sm:text-sm font-medium transition-colors">
                            <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                            <span>Tanya Admin</span>
                        </a>
                    </div>

                    <!-- Right: Share Icons with responsive divider (border-t on mobile, border-l on desktop) -->
                    <div class="flex items-center gap-1.5 w-full sm:w-auto border-t sm:border-t-0 sm:border-l border-slate-200 pt-3 sm:pt-0 sm:pl-4">
                        <!-- WhatsApp -->
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($product->name . ' - Cek layanan ini di Dyanaf Creative: ' . $currentUrl) }}"
                           target="_blank"
                           title="Bagikan ke WhatsApp"
                           class="flex-1 sm:flex-none w-auto sm:w-10 h-10 flex items-center justify-center border border-slate-300 hover:text-emerald-600 text-slate-600 transition-colors">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                        </a>

                        <!-- Facebook -->
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($currentUrl) }}"
                           target="_blank"
                           title="Bagikan ke Facebook"
                           class="flex-1 sm:flex-none w-auto sm:w-10 h-10 flex items-center justify-center border border-slate-300 hover:text-blue-600 text-slate-600 transition-colors">
                            <i class="fa-brands fa-facebook-f text-base"></i>
                        </a>

                        <!-- Telegram -->
                        <a href="https://t.me/share/url?url={{ urlencode($currentUrl) }}&text={{ urlencode($product->name) }}"
                           target="_blank"
                           title="Bagikan ke Telegram"
                           class="flex-1 sm:flex-none w-auto sm:w-10 h-10 flex items-center justify-center border border-slate-300 hover:text-sky-500 text-slate-600 transition-colors">
                            <i class="fa-brands fa-telegram text-base"></i>
                        </a>

                        <!-- Instagram -->
                        <a href="https://www.instagram.com/"
                           target="_blank"
                           title="Kunjungi Instagram Dyanaf"
                           class="flex-1 sm:flex-none w-auto sm:w-10 h-10 flex items-center justify-center border border-slate-300 hover:text-pink-600 text-slate-600 transition-colors">
                            <i class="fa-brands fa-instagram text-lg"></i>
                        </a>

                        <!-- Copy Link -->
                        <button type="button"
                                @click="navigator.clipboard.writeText('{{ $currentUrl }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                title="Salin Tautan"
                                class="flex-2 sm:flex-none px-3 h-10 border border-slate-300 hover:text-blue-600 text-slate-600 transition-colors text-xs font-medium inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i class="fa-solid text-sm" :class="copied ? 'fa-check text-emerald-600' : 'fa-link'"></i>
                            <span x-text="copied ? 'Tersalin!' : 'Salin Link'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How to Order & Service Guarantee Section -->
    <div class="bg-white border border-slate-300/80 p-5 sm:p-6 rounded-none">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-sm font-bold text-slate-900 font-['Outfit']">
                Cara Pemesanan di Dyanaf Creative
            </h2>
            <span class="text-xs text-slate-500 hidden sm:inline">3 Langkah Mudah & Cepat</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-slate-50/70 border border-slate-200 relative">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center mb-3">1</div>
                <h3 class="text-xs font-bold text-slate-800 mb-1.5">Pilih Layanan & Konsultasi</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Klik tombol WhatsApp untuk menyampaikan kebutuhan, materi referensi, dan deadline yang Anda inginkan.
                </p>
            </div>
            <div class="p-4 bg-slate-50/70 border border-slate-200 relative">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center mb-3">2</div>
                <h3 class="text-xs font-bold text-slate-800 mb-1.5">Konfirmasi & Pengerjaan</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Lakukan pembayaran resmi sesuai instruksi admin, kemudian tim ahli kami akan langsung memproses pesanan Anda.
                </p>
            </div>
            <div class="p-4 bg-slate-50/70 border border-slate-200 relative">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center mb-3">3</div>
                <h3 class="text-xs font-bold text-slate-800 mb-1.5">Serah Terima & Garansi</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    File hasil dikirimkan lengkap dengan garansi revisi untuk memastikan hasil sesuai dengan ekspektasi Anda.
                </p>
            </div>
        </div>
    </div>

    <!-- Related Products Section -->
    @if($relatedProducts->isNotEmpty())
        @php
            $originalPricesMap = [
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
        <div class="bg-white p-4 sm:p-5 border border-slate-300/80 rounded-none space-y-4">
            <div class="border-b border-slate-300/80 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 font-['Outfit']">
                        Layanan Terkait Lainnya
                    </h2>
                    <p class="text-xs text-slate-500">Mungkin Anda juga membutuhkan layanan berikut.</p>
                </div>
                <a href="{{ route('all-product') }}" class="text-xs text-blue-600 hover:text-blue-700 font-semibold inline-flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($relatedProducts as $related)
                    @php
                        $relOrigPrice = $originalPricesMap[$related->name] ?? null;
                    @endphp
                    <div class="group relative rounded-none bg-white hover:bg-slate-50 border border-slate-300/80 p-3 transition-colors flex flex-col justify-between overflow-hidden cursor-pointer">
                        <!-- Overlay link to product detail -->
                        <a href="{{ route('product.detail', $related) }}" class="absolute inset-0 z-0" aria-label="{{ $related->name }}"></a>

                        <div>
                            <a href="{{ route('product.detail', $related) }}" class="block h-40 rounded-none bg-[#eef2f6] border border-slate-300/70 overflow-hidden relative mb-3 cursor-pointer">
                                @if($related->image)
                                    <img src="{{ $related->image }}" alt="{{ $related->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-400">
                                        <i class="fa-regular fa-image text-3xl mb-1"></i>
                                        <span class="text-[11px] font-medium text-slate-400">No Image</span>
                                    </div>
                                @endif

                                @if($related->category)
                                    <span class="absolute top-2 left-2 px-2 py-0.5 bg-slate-900/80 text-white text-[10px] font-medium rounded-none pointer-events-none z-10">
                                        {{ $related->category }}
                                    </span>
                                @endif
                            </a>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors leading-snug">{{ $related->name }}</h4>
                                <p class="text-xs text-slate-600 mt-1 line-clamp-2 leading-relaxed">{{ $related->description }}</p>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-t border-slate-300/70 flex items-end justify-between gap-2 relative z-10">
                            <div>
                                @if($relOrigPrice && $relOrigPrice > $related->price)
                                    <span class="text-[11px] text-slate-400 line-through block leading-none mb-1">
                                        Rp {{ number_format($relOrigPrice, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-500 block font-medium leading-none mb-1">Harga</span>
                                @endif
                                <span class="text-sm font-bold text-[#1e293b] leading-none group-hover:text-blue-600 transition-colors">
                                    Rp {{ number_format($related->price, 0, ',', '.') }}
                                </span>
                            </div>
                            <a href="https://wa.me/6285881721193?text={{ urlencode('Halo Dyanaf Creative, saya tertarik memesan ' . $related->name) }}"
                               target="_blank"
                               title="Order {{ $related->name }}"
                               class="text-blue-600 hover:text-blue-700 text-lg transition-colors p-1">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Center Button: Lihat Semua Produk -->
            <div class="mt-6 flex justify-center pt-2">
                <a href="{{ route('all-product') }}" 
                   class="group inline-flex items-center justify-center space-x-2 px-6 py-2.5 bg-white text-slate-800 border border-slate-300 rounded-none text-xs font-semibold hover:border-slate-400 hover:text-blue-600 transition-colors">
                    <span>Lihat Semua Produk</span>
                    <i class="fa-solid fa-arrow-right text-[11px] transition-transform duration-200 group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>
    @endif

    <!-- Custom Modal Payment Component (VA & QRIS Custom UI) -->
    <x-modal-payment :product="$product" />
</div>
@endsection

