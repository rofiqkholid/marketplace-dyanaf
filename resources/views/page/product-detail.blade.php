@extends('layouts.app')

@section('content')
<div class="space-y-4 pb-16 sm:pb-0">
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

    <!-- Main Product Detail Card (Gaya Brand Detail) -->
    <div class="bg-white border border-slate-300/80 p-5 sm:p-6 rounded-none relative overflow-hidden">
        <div class="flex flex-col sm:flex-row items-center sm:items-stretch gap-6 sm:gap-8">
            <!-- Product Image / Showcase -->
            <div class="w-24 h-24 sm:w-28 sm:h-28 shrink-0 bg-[#f1f5f9] border border-slate-300/80 rounded-none overflow-hidden relative group">
                @if($product->image)
                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                        <i class="fa-regular fa-image text-3xl"></i>
                    </div>
                @endif
            </div>

            <!-- Product Info & Details -->
            <div class="flex-1 text-center sm:text-left flex flex-col justify-between">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-['Outfit'] leading-none mb-3 sm:mb-3.5">
                        {{ $product->name }}
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-600 leading-normal max-w-3xl mb-2 sm:mb-2.5">
                        {{ $product->description }}
                    </p>
                </div>

                <!-- Quick Highlights (Baris Horizontal Ber-Icon Persis Brand Detail) -->
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-x-4 gap-y-1.5 text-xs text-slate-600 leading-none pt-1">
                    <div class="flex items-center space-x-1.5">
                        <i class="fa-solid fa-bolt text-amber-500"></i>
                        <span>Pengerjaan Cepat</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <i class="fa-solid fa-rotate-left text-blue-600"></i>
                        <span>Garansi Revisi Minor</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <i class="fa-solid fa-check text-emerald-600"></i>
                        <span>Hasil Rapi & Teliti</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                        <span>Konsultasi Gratis via Admin</span>
                    </div>
                </div>
            </div>

            <!-- Price Tag (Paling Kanan Bawah Dekat Border: Harga Asli Dicoret, Diskon Berapa Rp, lalu Total Akhir) -->
            <div class="shrink-0 flex flex-col items-center sm:items-end justify-end text-center sm:text-right pt-2 sm:pt-0 sm:self-end">
                @if($originalPrice && $originalPrice > $product->price)
                    @php
                        $discountAmount = $originalPrice - $product->price;
                    @endphp
                    <!-- Harga Asli Dicoret (Hanya tampil di Desktop sm:) -->
                    <span class="hidden sm:inline text-xs font-normal text-slate-400 line-through leading-tight">
                        Rp {{ number_format($originalPrice, 0, ',', '.') }}
                    </span>
                    <!-- Nominal Diskon / Hemat (Hanya tampil di Desktop sm:) -->
                    <span class="hidden sm:inline text-xs font-normal text-slate-500 leading-tight my-0.5">
                        Hemat Rp {{ number_format($discountAmount, 0, ',', '.') }}
                    </span>
                @endif
                <!-- Total Harga Akhir -->
                <span class="text-xl sm:text-2xl font-bold text-slate-900 leading-tight">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Action Buttons & Share Area -->
        @php
            $waText = "Halo Dyanaf Creative, saya tertarik untuk memesan produk:\n\n*{$product->name}*\nHarga: Rp " . number_format($product->price, 0, ',', '.') . "\n\nMohon info detail dan langkah pemesanannya. Terima kasih!";
            $waUrl = "https://wa.me/6285881721193?text=" . urlencode($waText);
            $currentUrl = request()->fullUrl();
        @endphp
        <div class="mt-5 pt-4 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
             x-data="{ copied: false, loadingPayment: false }">
            <!-- Left: CTA Buttons (Desktop version) -->
            <div class="hidden sm:flex sm:items-center gap-2.5 w-auto">
                <button type="button"
                        @click="$dispatch('open-payment-modal')"
                        class="inline-flex items-center justify-center gap-2 px-5 h-10 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors cursor-pointer">
                    <i class="fa-solid fa-cart-shopping text-xs"></i>
                    <span>Order Sekarang</span>
                </button>

                <a href="https://wa.me/6285881721193?text={{ urlencode('Halo Dyanaf Creative, saya ingin tanya mengenai layanan ' . $product->name) }}"
                   target="_blank"
                   class="inline-flex items-center justify-center gap-2 px-4 h-10 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-sm font-medium transition-colors">
                    <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                    <span>Tanya Admin</span>
                </a>
            </div>

            <!-- Right: Share Icons (Langsung icon tanpa kotak border, tanpa garis border ganda di mobile) -->
            <div class="flex items-center justify-center sm:justify-start gap-3 w-full sm:w-auto sm:border-l border-slate-200 sm:pl-5">
                <span class="text-xs text-slate-400 font-medium hidden sm:inline">Bagikan:</span>

                <!-- WhatsApp -->
                <a href="https://api.whatsapp.com/send?text={{ urlencode($product->name . ' - Cek layanan ini di Dyanaf Creative: ' . $currentUrl) }}"
                   target="_blank"
                   title="Bagikan ke WhatsApp"
                   class="text-slate-500 hover:text-emerald-600 transition-colors p-1">
                    <i class="fa-brands fa-whatsapp text-xl"></i>
                </a>

                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($currentUrl) }}"
                   target="_blank"
                   title="Bagikan ke Facebook"
                   class="text-slate-500 hover:text-blue-600 transition-colors p-1">
                    <i class="fa-brands fa-facebook-f text-lg"></i>
                </a>

                <!-- Telegram -->
                <a href="https://t.me/share/url?url={{ urlencode($currentUrl) }}&text={{ urlencode($product->name) }}"
                   target="_blank"
                   title="Bagikan ke Telegram"
                   class="text-slate-500 hover:text-sky-500 transition-colors p-1">
                    <i class="fa-brands fa-telegram text-xl"></i>
                </a>

                <!-- Instagram -->
                <a href="https://www.instagram.com/"
                   target="_blank"
                   title="Kunjungi Instagram Dyanaf"
                   class="text-slate-500 hover:text-pink-600 transition-colors p-1">
                    <i class="fa-brands fa-instagram text-xl"></i>
                </a>

                <!-- Copy Link -->
                <button type="button"
                        @click="navigator.clipboard.writeText('{{ $currentUrl }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        title="Salin Tautan"
                        class="text-slate-500 hover:text-blue-600 transition-colors text-xs font-medium inline-flex items-center gap-1.5 p-1 cursor-pointer">
                    <i class="fa-solid text-base" :class="copied ? 'fa-check text-emerald-600' : 'fa-link'"></i>
                    <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                </button>
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

    <!-- Floating Bottom Action Bar for Mobile -->
    <div x-data
         class="sm:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-300/80 p-3 z-40 shadow-[0_-4px_12px_rgba(0,0,0,0.06)]">
        <div class="flex items-center gap-2.5 max-w-lg mx-auto">
            <!-- Tanya Admin (Kiri) -->
            <a href="https://wa.me/6285881721193?text={{ urlencode('Halo Dyanaf Creative, saya ingin tanya mengenai layanan ' . $product->name) }}"
               target="_blank"
               class="flex-1 inline-flex items-center justify-center gap-2 h-11 bg-white active:bg-slate-100 text-slate-800 border border-slate-300 text-xs font-semibold transition-all">
                <i class="fa-brands fa-whatsapp text-emerald-600 text-base"></i>
                <span>Tanya Admin</span>
            </a>

            <!-- Order Sekarang (Kanan) -->
            <button type="button"
                    @click="window.dispatchEvent(new CustomEvent('open-payment-modal'))"
                    class="flex-1 inline-flex items-center justify-center gap-2 h-11 bg-blue-600 active:bg-blue-700 text-white text-xs font-bold transition-all shadow-sm cursor-pointer">
                <i class="fa-solid fa-cart-shopping text-xs"></i>
                <span>Order Sekarang</span>
            </button>
        </div>
    </div>
</div>
@endsection

