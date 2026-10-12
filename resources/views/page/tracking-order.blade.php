@extends('layouts.app')

@section('content')
<div class="space-y-4 md:space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-900 font-['Outfit']">Tracking Status Order</h1>
            <p class="text-xs text-slate-500 mt-1">Cek progres pengerjaan pesanan, detail layanan, dan keaktifan akun secara real-time.</p>
        </div>
    </div>

    <!-- Tracking Input Search Card -->
    <div class="bg-white border border-slate-300/80 p-5 sm:p-8">
        <div class="max-w-xl mx-auto text-center space-y-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900 font-['Outfit']">Lacak Status Pesanan Anda</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Masukkan nomor order yang diberikan oleh admin untuk memantau status pengerjaan.
                </p>
            </div>

            <!-- Search Form -->
            <form action="{{ route('tracking-order') }}" method="GET" x-data="{ orderVal: '{{ $orderNumber ?? '' }}' }" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 max-w-md mx-auto pt-2">
                <div class="relative flex-1">
                    <input 
                        type="text" 
                        name="order_number" 
                        x-model="orderVal" 
                        placeholder="Masukkan nomor order..." 
                        required
                        class="w-full pl-9 pr-8 py-2.5 bg-white border border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 transition-colors">
                    <i class="fa-solid fa-receipt absolute left-3 top-3.5 text-slate-400 text-xs"></i>
                    <template x-if="orderVal.length > 0">
                        <button 
                            type="button" 
                            @click="orderVal = ''; window.location.href = '{{ route('tracking-order') }}'" 
                            title="Hapus pencarian"
                            class="absolute right-2.5 top-2.5 w-5 h-5 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                            <i class="fa-solid fa-circle-xmark text-xs"></i>
                        </button>
                    </template>
                </div>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors cursor-pointer shrink-0 flex items-center justify-center space-x-1.5">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span>Lacak Order</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Tracking Result Section -->
    @if($searched)
        @if($order)
            <!-- Order Result Found -->
            <div class="bg-white border border-slate-300/80 p-4 sm:p-6 space-y-5 sm:space-y-6">

                <!-- Header Info -->
                <div class="flex flex-row items-start sm:items-center justify-between pb-3 sm:pb-4 border-b border-slate-200 gap-3">
                    <div class="min-w-0 flex-1">
                        <span class="text-[11px] sm:text-xs font-bold text-slate-700 block mb-0.5">Nomor Order Pelanggan:</span>
                        <div class="text-xs sm:text-lg text-slate-800 font-normal font-['Outfit'] truncate">
                            {{ $order->order_number }}
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-[11px] sm:text-xs font-bold text-slate-700 block mb-0.5">Status Saat Ini:</span>
                        <div class="text-xs sm:text-sm font-semibold mt-0.5 flex items-center justify-end gap-1.5">
                            @if($order->status === 'completed')
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                <span class="text-emerald-700">Selesai</span>
                            @elseif($order->status === 'processing')
                                <i class="fa-solid fa-arrows-rotate text-blue-600"></i>
                                <span class="text-blue-700">Sedang Diproses</span>
                            @elseif($order->status === 'pending')
                                <i class="fa-solid fa-hourglass-half text-amber-500"></i>
                                <span class="text-amber-700">Pending</span>
                            @else
                                <i class="fa-solid fa-ban text-rose-600"></i>
                                <span class="text-rose-700">Dibatalkan</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Progress Tracker Visual Step -->
                @php
                    $stepIndex = match($order->status) {
                        'pending' => 1,
                        'processing' => 2,
                        'completed' => 3,
                        'cancelled' => 0,
                    };
                @endphp

                @if($order->status !== 'cancelled')
                    <div class="py-2 sm:py-3 px-1 sm:px-0">
                        <div class="grid grid-cols-3 relative">
                            <!-- Line connector -->
                            <div class="absolute top-3.5 sm:top-4 left-1/6 right-1/6 h-0.5 bg-slate-200 z-0">
                                <div class="h-full bg-blue-600 transition-all duration-500" 
                                     style="width: {{ $stepIndex === 1 ? '0%' : ($stepIndex === 2 ? '50%' : '100%') }}"></div>
                            </div>

                            <!-- Step 1: Order Diterima -->
                            <div class="flex flex-col items-center text-center relative z-10 px-0.5 sm:px-1">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-bold transition-colors {{ $stepIndex >= 1 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                                    @if($stepIndex > 1)
                                        <i class="fa-solid fa-check text-[10px] sm:text-xs"></i>
                                    @else
                                        1
                                    @endif
                                </div>
                                <span class="text-[10px] sm:text-xs font-bold text-slate-800 mt-1.5 sm:mt-2 font-['Outfit'] leading-tight">Order Masuk</span>
                                <span class="text-[9px] sm:text-[10px] text-slate-400 sm:text-slate-500 mt-0.5 leading-tight">{{ $order->created_at->format('d/m/y H:i') }}</span>
                            </div>

                            <!-- Step 2: Sedang Diproses -->
                            <div class="flex flex-col items-center text-center relative z-10 px-0.5 sm:px-1">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-bold transition-colors {{ $stepIndex >= 2 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                                    @if($stepIndex > 2)
                                        <i class="fa-solid fa-check text-[10px] sm:text-xs"></i>
                                    @else
                                        2
                                    @endif
                                </div>
                                <span class="text-[10px] sm:text-xs font-bold text-slate-800 mt-1.5 sm:mt-2 font-['Outfit'] leading-tight">Diproses</span>
                                <span class="text-[9px] sm:text-[10px] text-slate-400 sm:text-slate-500 mt-0.5 leading-tight">Penyiapan Akun</span>
                            </div>

                            <!-- Step 3: Selesai -->
                            <div class="flex flex-col items-center text-center relative z-10 px-0.5 sm:px-1">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-bold transition-colors {{ $stepIndex >= 3 ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                                    <i class="fa-solid fa-check text-[10px] sm:text-xs"></i>
                                </div>
                                <span class="text-[10px] sm:text-xs font-bold text-slate-800 mt-1.5 sm:mt-2 font-['Outfit'] leading-tight">Selesai</span>
                                <span class="text-[9px] sm:text-[10px] text-slate-400 sm:text-slate-500 mt-0.5 leading-tight">Siap Digunakan</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="py-2 text-xs text-rose-600">
                        Pesanan ini berstatus <strong>Dibatalkan</strong>. Silakan hubungi admin jika terdapat kendala pembayaran.
                    </div>
                @endif

                <!-- Order Details (Unified Card: Side-by-Side on Desktop, Stacked on Mobile) -->
                <div class="border border-slate-200 bg-[#f8fafc] grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-200">
                    <!-- Sisi Kiri: Informasi Pemesan -->
                    <div class="p-3.5 sm:p-5 space-y-2.5">
                        <h3 class="text-xs font-bold text-slate-800 font-['Outfit'] border-b border-slate-200/80 pb-2">Informasi Pemesan</h3>
                        <div class="text-xs space-y-2 text-slate-600">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Nama Customer:</span>
                                <strong class="text-slate-800">{{ $order->customer_name }}</strong>
                            </div>
                            @if($order->customer_phone)
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-400">Kontak:</span>
                                    <span class="text-slate-800 font-mono">{{ $order->customer_phone }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Tanggal Dibuat:</span>
                                <span class="text-slate-700">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Detail Pembelian -->
                    <div class="p-3.5 sm:p-5 space-y-2.5 flex flex-col justify-between">
                        <div>
                            <h3 class="text-xs font-bold text-slate-800 font-['Outfit'] border-b border-slate-200/80 pb-2">Detail Pembelian</h3>
                            <div class="text-xs space-y-2 text-slate-600 mt-2.5">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-400">Nama Layanan:</span>
                                    <strong class="text-slate-800">{{ $order->item_name }}</strong>
                                </div>
                                @if($order->package_name)
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-400">Paket:</span>
                                        <span class="text-slate-800 font-medium">{{ $order->package_name }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-400">Jumlah & Harga:</span>
                                    <span class="text-slate-800">{{ $order->quantity }}x @ Rp {{ number_format($order->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-2.5 border-t border-slate-200">
                            <span class="text-xs sm:text-sm font-bold text-slate-800">Total Pembayaran:</span>
                            <span class="text-sm sm:text-base font-bold text-slate-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                @if($order->notes)
                    <div class="p-3.5 bg-blue-50/70 border border-blue-200 text-xs">
                        <span class="font-bold text-blue-900 block mb-1">Catatan / Petunjuk Admin:</span>
                        <p class="text-blue-800 leading-relaxed whitespace-pre-line">{{ $order->notes }}</p>
                    </div>
                @endif

                <!-- Action Buttons: Download Asset (jika selesai) & Konfirmasi WhatsApp -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-2.5">
                    @if($order->status === 'completed')
                        <a href="{{ route('maintenance', ['page' => 'Download Asset']) }}"
                           class="inline-flex items-center space-x-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors cursor-pointer w-full sm:w-auto justify-center">
                            <i class="fa-solid fa-cloud-arrow-down text-sm"></i>
                            <span>Download Asset</span>
                        </a>
                    @endif

                    <a href="https://wa.me/6285881721193?text={{ urlencode('Halo Admin Dyanaf Creative, saya ingin menanyakan status pesanan saya dengan nomor order: ' . $order->order_number) }}"
                       target="_blank"
                       class="inline-flex items-center space-x-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors cursor-pointer w-full sm:w-auto justify-center">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Konfirmasi Pesanan via WhatsApp</span>
                    </a>
                </div>

            </div>
        @else
            <!-- Order Not Found -->
            <div class="bg-white border border-slate-300/80 p-8 text-center space-y-3">
                <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto text-xl">
                    <i class="fa-solid fa-circle-question"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 font-['Outfit']">Nomor Order Tidak Ditemukan</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                        Nomor order <strong class="font-mono text-slate-700">{{ $orderNumber }}</strong> tidak terdaftar dalam sistem. Pastikan penulisan kode sudah sesuai (huruf besar/kecil dan tanda strip).
                    </p>
                </div>
            </div>
        @endif
    @endif

</div>
@endsection
