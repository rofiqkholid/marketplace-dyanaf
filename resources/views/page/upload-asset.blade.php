@extends('layouts.app')

@section('content')
<div class="space-y-4 md:space-y-6" x-data="uploadAssetData()" x-effect="document.body.style.overflow = (isUploadModalOpen || isDeleteModalOpen) ? 'hidden' : ''">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-900 font-['Outfit']">Upload Asset Management</h1>
            <p class="text-xs text-slate-500 mt-1">Upload file hasil pengerjaan (PNG/JPG, PDF, Word, PPT) atau lampirkan link Google Drive untuk diunduh pelanggan.</p>
        </div>
        <button 
            @click="openUploadModal()" 
            class="inline-flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2.5 transition-colors shrink-0 cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Upload Asset Baru</span>
        </button>
    </div>

    @php
        $orderOptions = [];
        foreach($orders as $ord) {
            $orderOptions[$ord->id] = $ord->order_number . ' — ' . $ord->customer_name . ' (' . $ord->item_name . ') [' . ucfirst($ord->status) . ']';
        }
    @endphp

    <!-- Main Table Card -->
    <div id="table-card-container" class="table-card-container relative bg-white border border-slate-300/80 overflow-hidden">
        <div class="p-3 bg-white border-b border-slate-300/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-800 font-['Outfit']">Daftar Asset File Pesanan</h2>
            </div>

            <!-- Search Input -->
            <form method="GET" action="{{ route('assets-upload.index') }}" 
                  onsubmit="event.preventDefault(); window.navigateTable(this.action + '?' + new URLSearchParams(new FormData(this)).toString(), this.closest('.table-card-container') || 'table-card-container');"
                  class="relative w-full sm:w-64">
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <input 
                    type="text" 
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari no. order, customer, judul..." 
                    class="w-full pl-8 pr-8 py-1.5 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                @if(request('search'))
                    <a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => 1]) }}" 
                       onclick="event.preventDefault(); window.navigateTable(this.getAttribute('href'), this.closest('.table-card-container') || 'table-card-container');"
                       class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 text-xs" title="Reset pencarian">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] text-left border-collapse">
                <thead>
                    <tr class="bg-slate-200/70 border-b border-slate-300 text-xs font-medium text-slate-600">
                        <th class="py-2.5 px-3.5 w-16 text-center">Tipe</th>
                        <th class="py-2.5 px-3.5 w-48 min-w-[170px]">No. Order</th>
                        <th class="py-2.5 px-3.5 w-52 min-w-[190px]">Customer & Layanan</th>
                        <th class="py-2.5 px-3.5 w-72 min-w-[250px]">Judul & Catatan</th>
                        <th class="py-2.5 px-3.5 w-52 min-w-[180px]">File / Tautan Unduh</th>
                        <th class="py-2.5 px-3.5 w-36 min-w-[120px]">Tgl Upload</th>
                        <th class="py-2.5 px-3.5 w-24 min-w-[90px] text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/80 text-xs">
                    @forelse($assets as $asset)
                        @php
                            $ext = strtolower($asset->file_type ?? '');
                            $isDrive = !empty($asset->drive_link);
                        @endphp
                        <tr class="bg-white hover:bg-slate-50 transition-colors">
                            
                            <!-- Icon / Tipe -->
                            <td class="py-2.5 px-3.5 text-center">
                                @if($isDrive)
                                    <i class="fa-brands fa-google-drive text-lg text-emerald-600" title="Google Drive Link"></i>
                                @elseif(in_array($ext, ['png', 'jpg', 'jpeg']))
                                    <i class="fa-solid fa-file-image text-lg text-purple-600" title="Gambar"></i>
                                @elseif($ext === 'pdf')
                                    <i class="fa-solid fa-file-pdf text-lg text-rose-600" title="Dokumen PDF"></i>
                                @elseif(in_array($ext, ['doc', 'docx']))
                                    <i class="fa-solid fa-file-word text-lg text-blue-600" title="Microsoft Word"></i>
                                @elseif(in_array($ext, ['ppt', 'pptx']))
                                    <i class="fa-solid fa-file-powerpoint text-lg text-amber-600" title="PowerPoint"></i>
                                @else
                                    <i class="fa-solid fa-file text-lg text-slate-500" title="File"></i>
                                @endif
                            </td>

                            <!-- Nomor Order -->
                            <td class="py-2.5 px-3.5 font-mono font-bold text-blue-600 whitespace-nowrap">
                                <a href="{{ route('tracking-order', ['order_number' => $asset->order->order_number ?? '']) }}" target="_blank" class="hover:underline" title="Lihat di Tracking">
                                    {{ $asset->order->order_number ?? '-' }}
                                </a>
                            </td>

                            <!-- Customer & Layanan -->
                            <td class="py-2.5 px-3.5">
                                <div class="font-semibold text-slate-900">{{ $asset->order->customer_name ?? '-' }}</div>
                                <div class="text-slate-500 text-[11px] truncate max-w-[200px]" title="{{ $asset->order->item_name ?? '-' }}">
                                    {{ $asset->order->item_name ?? '-' }}
                                </div>
                            </td>

                            <!-- Judul & Catatan -->
                            <td class="py-2.5 px-3.5">
                                <div class="font-semibold text-slate-900">{{ $asset->title }}</div>
                                @if($asset->notes)
                                    <div class="text-[11px] text-slate-500 mt-0.5 line-clamp-1 italic" title="{{ $asset->notes }}">
                                        "{{ $asset->notes }}"
                                    </div>
                                @else
                                    <div class="text-[11px] text-slate-400 mt-0.5">—</div>
                                @endif
                            </td>

                            <!-- File / Tautan Unduh -->
                            <td class="py-2.5 px-3.5">
                                <div class="space-y-1">
                                    @if($asset->file_path)
                                        <div class="flex items-center space-x-1.5">
                                            <a href="{{ route('download-asset.file', $asset) }}" 
                                               class="text-blue-600 hover:text-blue-800 font-medium inline-flex items-center space-x-1 hover:underline">
                                                <i class="fa-solid fa-download text-[10px]"></i>
                                                <span>Download {{ strtoupper($ext) }}</span>
                                            </a>
                                            @if($asset->file_size)
                                                <span class="text-[10px] text-slate-400">({{ number_format($asset->file_size / 1024, 0) }} KB)</span>
                                            @endif
                                        </div>
                                    @endif

                                    @if($asset->drive_link)
                                        <div>
                                            <a href="{{ $asset->drive_link }}" 
                                               target="_blank" 
                                               class="text-emerald-600 hover:text-emerald-800 font-medium inline-flex items-center space-x-1 hover:underline">
                                                <i class="fa-brands fa-google-drive text-[11px]"></i>
                                                <span>Google Drive</span>
                                                <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Tanggal Upload -->
                            <td class="py-2.5 px-3.5 text-slate-500 whitespace-nowrap">
                                {{ $asset->created_at->format('d M Y') }}
                                <span class="block text-[10px] text-slate-400">{{ $asset->created_at->format('H:i') }} WIB</span>
                            </td>

                            <!-- Aksi (Sama persis dengan icon di products-master) -->
                            <td class="py-2.5 px-3.5 text-center">
                                <button 
                                    @click="openDeleteModal('{{ $asset->id }}', '{{ addslashes($asset->title) }}', '{{ $asset->order->order_number ?? '' }}')" 
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200 cursor-pointer mx-auto"
                                    title="Hapus Asset">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                        <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                        <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                @if(request('search'))
                                    <i class="fa-solid fa-magnifying-glass text-2xl mb-2 block"></i>
                                    <p class="text-xs">Tidak ada data asset yang sesuai dengan pencarian "{{ request('search') }}".</p>
                                @else
                                    <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-400 block"></i>
                                    <p class="text-xs text-slate-400">Belum ada data asset tersimpan.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-table-footer :paginator="$assets" />
    </div>

    <!-- Modal Form: Upload Asset Baru (Sesuai Style Modal Products-Master) -->
    <template x-teleport="body">
        <div 
            x-show="isUploadModalOpen" 
            x-cloak 
            style="display: none;"
            class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-slate-900/50 w-screen h-screen min-h-full min-w-full"
            @keydown.escape.window="closeUploadModal()">

            <div 
                @click.outside="closeUploadModal()" 
                class="bg-white border border-slate-300 w-full max-w-xl overflow-hidden text-left relative max-h-[90vh] overflow-y-auto">
                
                <div class="p-4 bg-white border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800 font-['Outfit']">Upload Asset Order Pelanggan</h3>
                    <button @click="closeUploadModal()" class="text-slate-400 hover:text-slate-600 text-sm">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form action="{{ route('assets-upload.store') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-4">
                    @csrf

                    <!-- Pilih Order No -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Pilih Nomor Order Pelanggan <span class="text-rose-500">*</span>
                        </label>
                        <x-select 
                            name="order_id" 
                            bind="selectedOrderId" 
                            :options="$orderOptions" 
                            placeholder="Pilih Order Terkait" 
                        />
                    </div>

                    <!-- Judul File / Label Asset -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Nama / Judul File Asset <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="title" 
                            required 
                            placeholder="Contoh: Final Makalah Bab 1-5 / Desain Logo AI & PNG / PPT Sidang"
                            class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Upload File Dokumen / Media -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Upload File Dokumen / Media
                        </label>
                        <input 
                            type="file" 
                            name="asset_file" 
                            accept=".png,.jpg,.jpeg,.pdf,.doc,.docx,.ppt,.pptx"
                            class="w-full px-3 py-1.5 bg-white border border-slate-300 text-xs text-slate-700 file:mr-3 file:py-1 file:px-3 file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <p class="text-[11px] text-slate-400 mt-1">Format: <strong>PNG, JPG, PDF, Word (.doc/.docx), PowerPoint (.ppt/.pptx)</strong> (Maks: 50MB).</p>
                    </div>

                    <!-- Link Google Drive -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Link Google Drive (Opsional)
                        </label>
                        <input 
                            type="url" 
                            name="drive_link" 
                            placeholder="https://drive.google.com/drive/folders/... atau https://drive.google.com/file/d/..."
                            class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Catatan / Petunjuk -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan untuk Pelanggan (Opsional)</label>
                        <textarea 
                            name="notes" 
                            rows="2" 
                            placeholder="Contoh: Password zip, instruksi revisi, atau akses akun..."
                            class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500 resize-none"></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="pt-3 border-t border-slate-200 flex items-center justify-end space-x-2">
                        <button 
                            type="button" 
                            @click="closeUploadModal()" 
                            class="px-4 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors cursor-pointer">
                            Simpan & Upload Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Modal Konfirmasi Hapus Asset (Sesuai Style Modal Products-Master) -->
    <template x-teleport="body">
        <div 
            x-show="isDeleteModalOpen" 
            x-cloak 
            style="display: none;"
            class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-slate-900/50 w-screen h-screen min-h-full min-w-full"
            @keydown.escape.window="closeDeleteModal()">

            <div 
                @click.outside="closeDeleteModal()" 
                class="bg-white border border-slate-300 w-full max-w-md overflow-hidden text-left relative">
                
                <div class="p-4 bg-white border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800 font-['Outfit']">Konfirmasi Hapus Asset</h3>
                    <button @click="closeDeleteModal()" class="text-slate-400 hover:text-slate-600 text-sm">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form :action="'{{ url('/assets-upload') }}/' + deleteAssetId" method="POST" class="p-4 space-y-4">
                    @csrf
                    @method('DELETE')

                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-triangle-exclamation text-base"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Apakah Anda yakin ingin menghapus file asset <strong class="text-slate-900" x-text="deleteAssetTitle"></strong> untuk order <strong class="font-mono text-slate-900" x-text="deleteOrderNumber"></strong>?
                            </p>
                            <p class="text-[11px] text-rose-500 mt-1">File fisik pada server akan dihapus secara permanen.</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex items-center justify-end space-x-2">
                        <button 
                            type="button" 
                            @click="closeDeleteModal()" 
                            class="px-4 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold transition-colors cursor-pointer">
                            Hapus Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>

<script>
function uploadAssetData() {
    return {
        isUploadModalOpen: false,
        isDeleteModalOpen: false,
        selectedOrderId: '',
        deleteAssetId: '',
        deleteAssetTitle: '',
        deleteOrderNumber: '',

        openUploadModal() {
            this.selectedOrderId = '';
            this.isUploadModalOpen = true;
        },

        closeUploadModal() {
            this.isUploadModalOpen = false;
        },

        openDeleteModal(id, title, orderNumber) {
            this.deleteAssetId = id;
            this.deleteAssetTitle = title;
            this.deleteOrderNumber = orderNumber;
            this.isDeleteModalOpen = true;
        },

        closeDeleteModal() {
            this.isDeleteModalOpen = false;
        }
    };
}
</script>
@endsection
