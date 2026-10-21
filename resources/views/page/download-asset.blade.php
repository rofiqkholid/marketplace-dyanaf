@extends('layouts.app')

@section('content')
<div class="space-y-4 md:space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-900 font-['Outfit']">Download Asset Order</h1>
            <p class="text-xs text-slate-500 mt-1">Unduh berkas dokumen, desain, atau tautan Google Drive hasil pengerjaan pesanan Anda.</p>
        </div>
    </div>

    <!-- Search / Input Nomor Order Card -->
    <div class="bg-white border border-slate-300/80 p-5 sm:p-8">
        <div class="max-w-xl mx-auto text-center space-y-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-cloud-arrow-down"></i>
            </div>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900 font-['Outfit']">Ambil File Hasil Pesanan</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Masukkan nomor order Anda untuk melihat dan mengunduh seluruh file aset yang telah disiapkan admin.
                </p>
            </div>

            <!-- Search Form -->
            <form action="{{ route('download-asset') }}" method="GET" x-data="{ orderVal: '{{ $orderNumber ?? '' }}' }" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 max-w-md mx-auto pt-2">
                <div class="relative flex-1">
                    <input 
                        type="text" 
                        name="order_number" 
                        x-model="orderVal" 
                        placeholder="Contoh: SEN121026-4-B10" 
                        required
                        class="w-full pl-9 pr-8 py-2.5 bg-white border border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 transition-colors">
                    <i class="fa-solid fa-receipt absolute left-3 top-3.5 text-slate-400 text-xs"></i>
                    <template x-if="orderVal.length > 0">
                        <button 
                            type="button" 
                            @click="orderVal = ''; window.location.href = '{{ route('download-asset') }}'" 
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
                    <span>Cari Asset</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Download Result Section -->
    @if($searched)
        @if($order)
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
                        <div class="text-xs sm:text-sm font-semibold mt-0.5 flex items-center sm:justify-end gap-1.5">
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

                <!-- Unified 2-Column Section: Kiri Informasi Order, Kanan Daftar File/Asset -->
                <div class="border border-slate-200 bg-[#f8fafc] grid grid-cols-1 lg:grid-cols-12 divide-y lg:divide-y-0 lg:divide-x divide-slate-200">
                    
                    <!-- Kolom Kiri: Informasi Pemesan (5 col) -->
                    <div class="lg:col-span-5 p-3.5 sm:p-5 space-y-4">
                        <!-- Informasi Pemesan -->
                        <div>
                            <h3 class="text-xs font-bold text-slate-800 font-['Outfit'] border-b border-slate-200/80 pb-2">Informasi Pemesan</h3>
                            <div class="text-xs space-y-2 text-slate-600 mt-2.5">
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

                        @if($order->notes)
                            <div class="p-3 bg-blue-50/70 border border-blue-200 text-xs">
                                <span class="font-bold text-blue-900 block mb-0.5">Catatan Order:</span>
                                <p class="text-blue-800 leading-relaxed italic text-[11px]">{{ $order->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Kolom Kanan: Daftar File & Download Asset (7 col) -->
                    <div class="lg:col-span-7 p-3.5 sm:p-5 flex flex-col justify-between bg-[#f8fafc]">
                        <div>
                            <div class="flex items-center justify-between border-b border-slate-200/80 pb-2 mb-3">
                                <h3 class="text-xs font-bold text-slate-800 font-['Outfit']">File & Aset Siap Download</h3>
                                <span class="text-[11px] font-semibold text-slate-500">{{ $order->assets->count() }} File</span>
                            </div>

                            @if($order->assets->isNotEmpty())
                                <div class="space-y-2.5">
                                    @foreach($order->assets as $asset)
                                        @php
                                            $ext = strtolower($asset->file_type ?? '');
                                            $isDrive = !empty($asset->drive_link);
                                        @endphp
                                        <div class="p-3 bg-white border border-slate-200 hover:border-blue-400 transition-colors flex items-center justify-between gap-3">
                                            <div class="flex items-start sm:items-center space-x-3 flex-1 min-w-0">
                                                <!-- Icon File -->
                                                <div class="shrink-0 mt-0.5 sm:mt-0">
                                                    @if($isDrive)
                                                        <i class="fa-brands fa-google-drive text-2xl text-emerald-600"></i>
                                                    @elseif(in_array($ext, ['png', 'jpg', 'jpeg']))
                                                        <i class="fa-solid fa-file-image text-2xl text-purple-600"></i>
                                                    @elseif($ext === 'pdf')
                                                        <i class="fa-solid fa-file-pdf text-2xl text-rose-600"></i>
                                                    @elseif(in_array($ext, ['doc', 'docx']))
                                                        <i class="fa-solid fa-file-word text-2xl text-blue-600"></i>
                                                    @elseif(in_array($ext, ['ppt', 'pptx']))
                                                        <i class="fa-solid fa-file-powerpoint text-2xl text-amber-600"></i>
                                                    @else
                                                        <i class="fa-solid fa-file text-2xl text-slate-500"></i>
                                                    @endif
                                                </div>

                                                <!-- Title & Detail -->
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug truncate" title="{{ $asset->title }}">{{ $asset->title }}</h4>
                                                    
                                                    <div class="flex items-center space-x-1.5 sm:space-x-2 text-[10px] sm:text-[11px] text-slate-500 mt-0.5 flex-wrap">
                                                        @if($asset->file_type && $asset->file_type !== 'link')
                                                            <span class="uppercase font-semibold text-slate-700">{{ $asset->file_type }}</span>
                                                        @endif
                                                        @if($asset->file_size)
                                                            <span>&bull;</span>
                                                            <span>{{ number_format($asset->file_size / 1024, 0) }} KB</span>
                                                        @endif
                                                        <span>&bull;</span>
                                                        <span>{{ $asset->created_at->format('d/m/Y') }}</span>
                                                    </div>

                                                    @if($asset->notes)
                                                        <div class="mt-1.5 p-1.5 bg-white border border-slate-200 text-[11px] text-slate-600">
                                                            <span class="font-semibold text-slate-700 block text-[10px] mb-0.5">Catatan:</span>
                                                            <p class="leading-relaxed italic">"{{ $asset->notes }}"</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Action Button Download (Right side) -->
                                            <div class="shrink-0 flex items-center space-x-1.5">
                                                @if($asset->file_path)
                                                    <button type="button"
                                                            x-data="{
                                                                downloading: false,
                                                                startDownload() {
                                                                    if (this.downloading) return;
                                                                    this.downloading = true;
                                                                    const iframe = document.createElement('iframe');
                                                                    iframe.style.display = 'none';
                                                                    iframe.src = '{{ route('download-asset.file', $asset) }}';
                                                                    document.body.appendChild(iframe);
                                                                    setTimeout(() => {
                                                                        this.downloading = false;
                                                                        iframe.remove();
                                                                    }, 3000);
                                                                }
                                                            }"
                                                            @click="startDownload()"
                                                            class="inline-flex items-center space-x-1 sm:space-x-1.5 px-2.5 sm:px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[11px] sm:text-xs font-semibold transition-colors cursor-pointer whitespace-nowrap active:scale-[0.98]"
                                                            :class="downloading ? 'opacity-90 cursor-wait' : ''">
                                                        <template x-if="!downloading">
                                                            <i class="fa-solid fa-download text-[11px] sm:text-xs"></i>
                                                        </template>
                                                        <template x-if="downloading">
                                                            <i class="fa-solid fa-circle-notch fa-spin text-[11px] sm:text-xs"></i>
                                                        </template>
                                                        <span x-text="downloading ? 'Mengunduh...' : 'Download'"></span>
                                                    </button>
                                                @endif

                                                @if($asset->drive_link)
                                                    <a href="{{ $asset->drive_link }}" 
                                                       target="_blank"
                                                       class="inline-flex items-center space-x-1 sm:space-x-1.5 px-2.5 sm:px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] sm:text-xs font-semibold transition-colors cursor-pointer whitespace-nowrap">
                                                        <i class="fa-brands fa-google-drive text-[11px] sm:text-xs"></i>
                                                        <span>Buka Drive</span>
                                                        <i class="fa-solid fa-arrow-up-right-from-square text-[8px] sm:text-[9px] ml-0.5"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- No Assets Uploaded Yet for this order -->
                                <div class="p-8 text-center bg-slate-50 border border-slate-200 text-slate-500 space-y-2">
                                    <i class="fa-solid fa-clock-rotate-left text-3xl text-slate-400"></i>
                                    <h4 class="text-xs font-bold text-slate-700">Aset Belum Diupload</h4>
                                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                                        Pesanan Anda sedang disiapkan. Admin belum mengunggah file aset untuk nomor order ini. Silakan cek kembali secara berkala.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

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
                        Nomor order <strong class="font-mono text-slate-700">{{ $orderNumber }}</strong> tidak terdaftar dalam sistem. Pastikan penulisan kode sudah sesuai.
                    </p>
                </div>
            </div>
        @endif
    @endif

</div>
@endsection
