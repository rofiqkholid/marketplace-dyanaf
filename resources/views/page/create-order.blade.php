@extends('layouts.app')

@section('content')
<div class="space-y-4 md:space-y-6" x-data="orderMasterData()" x-effect="document.body.style.overflow = (isFormModalOpen || isDeleteModalOpen) ? 'hidden' : ''">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-900 font-['Outfit']">Create & Order Management</h1>
            <p class="text-xs text-slate-500 mt-1">Buat pesanan baru, generate nomor order unik (contoh: SEN100226-1-P29), dan kelola status tracking pesanan.</p>
        </div>
        <button 
            @click="openAddModal()" 
            class="inline-flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2.5 transition-colors shrink-0 cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Create Order Baru</span>
        </button>
    </div>

<!-- Main Order Management Table Card -->
    <div id="table-card-container" class="table-card-container relative bg-white border border-slate-300/80">
        <div class="p-3 bg-white border-b border-slate-300/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-800 font-['Outfit']">Daftar Order Pelanggan</h2>
            </div>

            <!-- Search Input -->
            <form method="GET" action="{{ route('orders.admin') }}" 
                  onsubmit="event.preventDefault(); window.navigateTable(this.action + '?' + new URLSearchParams(new FormData(this)).toString(), this.closest('.table-card-container') || 'table-card-container');"
                  class="relative w-full sm:w-64">
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <input 
                    type="text" 
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari no. order, customer, produk..." 
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
            <table class="w-full min-w-[1200px] text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-200/70 border-b border-slate-300 text-xs font-medium text-slate-600 whitespace-nowrap">
                        <th class="py-2.5 px-3.5 w-48 min-w-[180px]">Nomor Order</th>
                        <th class="py-2.5 px-3.5 w-48 min-w-[180px]">Nama Customer</th>
                        <th class="py-2.5 px-3.5 w-60 min-w-[220px]">Item Pesanan</th>
                        <th class="py-2.5 px-3.5 w-24 min-w-[90px] text-center">Tipe</th>
                        <th class="py-2.5 px-3.5 w-32 min-w-[120px] text-right">Harga</th>
                        <th class="py-2.5 px-3.5 w-20 min-w-[80px] text-center">Qty</th>
                        <th class="py-2.5 px-3.5 w-36 min-w-[130px] text-right">Total</th>
                        <th class="py-2.5 px-3.5 w-36 min-w-[130px] text-center">Status</th>
                        <th class="py-2.5 px-3.5 w-36 min-w-[130px] text-center">Waktu</th>
                        <th class="py-2.5 px-3.5 w-32 min-w-[120px] text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/80 text-xs whitespace-nowrap">
                    @forelse($orders as $order)
                        <tr class="bg-white hover:bg-slate-50 transition-colors">
                            
                            <td class="py-2.5 px-3.5 font-mono font-bold text-blue-600 whitespace-nowrap">
                                <a href="{{ route('tracking-order', ['order_number' => $order->order_number]) }}" target="_blank" class="hover:underline" title="Lihat di Tracking">
                                    {{ $order->order_number }}
                                </a>
                            </td>

                            <td class="py-2.5 px-3.5 font-semibold text-slate-900 whitespace-nowrap">
                                <div>{{ $order->customer_name }}</div>
                                @if($order->customer_phone)
                                    <div class="text-[11px] text-slate-500 font-normal">{{ $order->customer_phone }}</div>
                                @endif
                            </td>

                            <td class="py-2.5 px-3.5 text-slate-800 whitespace-nowrap">
                                <div class="font-medium text-slate-900">{{ $order->item_name }}</div>
                                @if($order->package_name)
                                    <div class="text-[11px] text-slate-500">Paket: {{ $order->package_name }}</div>
                                @endif
                            </td>

                            <td class="py-2.5 px-3.5 text-center text-slate-700 font-medium whitespace-nowrap">
                                {{ $order->item_type === 'brand' ? 'Brand' : 'Produk' }}
                            </td>

                            <td class="py-2.5 px-3.5 text-right font-medium text-slate-700 whitespace-nowrap">
                                Rp {{ number_format($order->price, 0, ',', '.') }}
                            </td>

                            <td class="py-2.5 px-3.5 text-center font-medium text-slate-700 whitespace-nowrap">
                                {{ $order->quantity }}
                            </td>

                            <td class="py-2.5 px-3.5 text-right font-bold text-slate-900 whitespace-nowrap">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>

                            <td class="py-2.5 px-3.5 text-center whitespace-nowrap">
                                <div class="inline-block w-28 text-left relative"
                                     x-data="{
                                         open: false,
                                         dropUp: {{ $loop->iteration > max(1, count($orders) - 3) ? 'true' : 'false' }},
                                         currentStatus: '{{ $order->status }}',
                                         statusLabels: {
                                             'pending': 'Pending',
                                             'processing': 'Diproses',
                                             'completed': 'Selesai',
                                             'cancelled': 'Dibatalkan'
                                         },
                                         statusColors: {
                                             'pending': 'border-amber-300 bg-amber-50 text-amber-700',
                                             'processing': 'border-blue-300 bg-blue-50 text-blue-700',
                                             'completed': 'border-emerald-300 bg-emerald-50 text-emerald-700',
                                             'cancelled': 'border-rose-300 bg-rose-50 text-rose-700'
                                         },
                                         toggleDropdown() {
                                             this.open = !this.open;
                                         },
                                         selectStatus(val) {
                                             this.currentStatus = val;
                                             this.open = false;
                                             updateOrderStatus({{ $order->id }}, val);
                                         }
                                     }"
                                     @click.outside="open = false"
                                     :class="open ? 'z-50' : 'z-10'">
                                    
                                    <!-- Trigger Button -->
                                    <button type="button" 
                                            @click="toggleDropdown()" 
                                            class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs font-semibold border transition-colors cursor-pointer"
                                            :class="statusColors[currentStatus] || 'border-slate-300 bg-slate-50 text-slate-800'">
                                        <span x-text="statusLabels[currentStatus] || currentStatus"></span>
                                        <i class="fa-solid fa-chevron-down text-[9px] transition-transform duration-200"
                                           :class="open ? (dropUp ? 'rotate-0' : 'rotate-180') : (dropUp ? 'rotate-180' : '')"></i>
                                    </button>

                                    <!-- Dropdown Menu (Auto opens upwards if near bottom of table/window) -->
                                    <div x-show="open" 
                                         x-cloak
                                         x-transition:enter="transition ease-out duration-100 transform"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75 transform"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute left-0 w-full bg-white border border-slate-300 shadow-xl py-1 text-xs z-[100]"
                                         :class="dropUp ? 'bottom-full mb-1' : 'top-full mt-1'"
                                         style="display: none;">
                                        @foreach($tableStatusOptions as $val => $label)
                                            <button type="button"
                                                    @click="selectStatus('{{ $val }}')"
                                                    class="w-full text-left px-3 py-1.5 hover:bg-slate-50 flex items-center justify-between transition-colors cursor-pointer"
                                                    :class="currentStatus === '{{ $val }}' ? 'font-bold text-blue-600 bg-blue-50/60' : 'text-slate-700'">
                                                <span>{{ $label }}</span>
                                                <i x-show="currentStatus === '{{ $val }}'" class="fa-solid fa-check text-[10px] text-blue-600"></i>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </td>

                            <td class="py-2.5 px-3.5 text-center text-slate-500 text-[11px] whitespace-nowrap">
                                {{ $order->created_at ? $order->created_at->format('d/m/y H:i') : '—' }}
                            </td>

                            <td class="py-2.5 px-3.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1.5" x-data="{ copied: false }">
                                    <button 
                                        type="button"
                                        @click="navigator.clipboard.writeText('{{ $order->order_number }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                        class="w-8 h-8 flex items-center justify-center rounded-xl transition-all duration-200 cursor-pointer relative"
                                        :class="copied ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-800'"
                                        :title="copied ? 'Disalin!' : 'Copy No Order'">
                                        <template x-if="!copied">
                                            <i class="fa-regular fa-copy text-xs"></i>
                                        </template>
                                        <template x-if="copied">
                                            <i class="fa-solid fa-check text-xs text-emerald-600"></i>
                                        </template>
                                    </button>
                                    <button 
                                        @click="openEditModal({{ json_encode($order) }})" 
                                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200 cursor-pointer"
                                        title="Edit Order">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.596C16.424 1.815 17.69 1.815 18.471 2.596L21.4 5.525C22.181 6.306 22.181 7.572 21.4 8.35303Z" fill="currentColor"/>
                                            <path d="M12.071 6.16901L3.00002 15.24C2.68402 15.556 2.45702 15.952 2.34802 16.388L1.05002 21.579C0.914016 22.123 1.39902 22.608 1.94302 22.472L7.13402 21.174C7.57002 21.065 7.96602 20.838 8.28202 20.522L17.353 11.451L12.071 6.16901Z" fill="currentColor"/>
                                        </svg>
                                    </button>
                                    <button 
                                        @click="openDeleteModal({{ json_encode($order) }})" 
                                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200 cursor-pointer"
                                        title="Hapus Order">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                            <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                            <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">
                                @if(request('search'))
                                    <i class="fa-solid fa-magnifying-glass text-2xl mb-2 block"></i>
                                    <p class="text-xs">Tidak ada data pesanan yang sesuai dengan pencarian "{{ request('search') }}".</p>
                                @else
                                    <i class="fa-solid fa-receipt text-3xl mb-2 block"></i>
                                    <p class="text-xs">Belum ada data pesanan tersimpan.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-table-footer :paginator="$orders" />
    </div>

    <!-- Modal Form (Tambah / Edit Order) -->
    <template x-teleport="body">
        <div 
            x-show="isFormModalOpen" 
            x-cloak 
            style="display: none;"
            class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-slate-900/50 w-screen h-screen min-h-full min-w-full"
            @keydown.escape.window="closeFormModal()">

            <div 
                @click.outside="closeFormModal()" 
                class="bg-white border border-slate-300 w-full max-w-lg overflow-hidden text-left relative max-h-[90vh] overflow-y-auto">
                
                <div class="p-4 bg-white border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800 font-['Outfit']" x-text="formMode === 'add' ? 'Create Order Baru' : 'Edit Data Order'"></h3>
                    <button @click="closeFormModal()" class="text-slate-400 hover:text-slate-600 text-sm">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form :action="formAction" method="POST" class="p-4 space-y-4">
                    @csrf
                    <template x-if="formMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Preview Nomor Order jika edit -->
                    <template x-if="formMode === 'edit'">
                        <div class="p-2.5 bg-blue-50 border border-blue-200 text-xs">
                            <span class="text-slate-500">Nomor Order:</span>
                            <span class="font-mono font-bold text-blue-700 ml-1" x-text="formData.order_number"></span>
                        </div>
                    </template>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Customer <span class="text-rose-500">*</span></label>
                            <input 
                                type="text" 
                                name="customer_name" 
                                x-model="formData.customer_name" 
                                required 
                                placeholder="Contoh: Rofiq Kholid"
                                class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp / HP</label>
                            <input 
                                type="text" 
                                name="customer_phone" 
                                x-model="formData.customer_phone" 
                                placeholder="085881721193"
                                class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Tipe Item (Hanya saat create) -->
                    <template x-if="formMode === 'add'">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Item <span class="text-rose-500">*</span></label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center space-x-2 p-2 border border-slate-300 cursor-pointer" :class="formData.item_type === 'product' ? 'bg-blue-50 border-blue-500 text-blue-700 font-semibold' : 'text-slate-700'">
                                        <input type="radio" name="item_type" value="product" x-model="formData.item_type" @change="onItemTypeChange()" class="text-blue-600">
                                        <span class="text-xs">Produk Marketplace</span>
                                    </label>
                                    <label class="flex items-center space-x-2 p-2 border border-slate-300 cursor-pointer" :class="formData.item_type === 'brand' ? 'bg-blue-50 border-blue-500 text-blue-700 font-semibold' : 'text-slate-700'">
                                        <input type="radio" name="item_type" value="brand" x-model="formData.item_type" @change="onItemTypeChange()" class="text-blue-600">
                                        <span class="text-xs">Brand / Akun</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Pilih Produk -->
                            <div x-show="formData.item_type === 'product'">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Produk <span class="text-rose-500">*</span></label>
                                <x-select 
                                    name="product_id" 
                                    bind="formData.product_id" 
                                    :options="$productOptions" 
                                    placeholder="Pilih Produk" 
                                />
                            </div>

                            <!-- Pilih Brand -->
                            <div x-show="formData.item_type === 'brand'">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Brand <span class="text-rose-500">*</span></label>
                                <x-select 
                                    name="brand_id" 
                                    bind="formData.brand_id" 
                                    :options="$brandOptions" 
                                    placeholder="Pilih Brand" 
                                />
                            </div>

                            <!-- Nama Paket jika Brand dipilih -->
                            <div x-show="formData.item_type === 'brand'">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Paket Layanan</label>
                                
                                <template x-if="currentBrandPackages.length > 0">
                                    <div class="relative w-full" x-data="{ open: false }" @click.outside="open = false">
                                        <input type="hidden" name="package_name" :value="formData.package_name">
                                        <button 
                                            type="button" 
                                            @click="open = !open" 
                                            class="w-full flex items-center justify-between p-2 text-xs bg-slate-50 border border-slate-300 text-slate-900 hover:bg-white focus:bg-white focus:border-blue-500 focus:outline-none transition-colors cursor-pointer">
                                            <span x-text="formData.package_name || 'Pilih Paket Layanan'" :class="!formData.package_name ? 'text-slate-400' : 'text-slate-900 font-medium'"></span>
                                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-blue-600' : ''"></i>
                                        </button>
                                        <div 
                                            x-show="open" 
                                            x-cloak
                                            style="display: none;"
                                            class="absolute left-0 right-0 top-full mt-1 z-50 bg-white border border-slate-300 max-h-60 overflow-y-auto py-1 text-xs shadow-lg">
                                            <template x-for="(pkg, idx) in currentBrandPackages" :key="idx">
                                                <button 
                                                    type="button"
                                                    @click="selectPackage(pkg); open = false"
                                                    class="w-full flex items-center justify-between px-3 py-2 text-left transition-colors cursor-pointer"
                                                    :class="formData.package_name === pkg.title ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-700 hover:bg-slate-50'">
                                                    <span x-text="pkg.title"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="currentBrandPackages.length === 0">
                                    <input 
                                        type="text" 
                                        name="package_name" 
                                        x-model="formData.package_name" 
                                        placeholder="Contoh: Paket 1 Bulan Premium 4K UHD"
                                        class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                                </template>
                            </div>
                        </div>
                    </template>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Satuan (Rp) <span class="text-rose-500">*</span></label>
                            <input 
                                type="number" 
                                name="price" 
                                x-model="formData.price" 
                                min="0" 
                                required 
                                placeholder="50000"
                                class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah (Qty) <span class="text-rose-500">*</span></label>
                            <input 
                                type="number" 
                                name="quantity" 
                                x-model="formData.quantity" 
                                min="1" 
                                required 
                                class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status Order <span class="text-rose-500">*</span></label>
                        <x-select 
                            name="status" 
                            bind="formData.status" 
                            :options="$statusOptions" 
                            placeholder="Pilih Status" 
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Detail Akun (Opsional)</label>
                        <textarea 
                            name="notes" 
                            x-model="formData.notes" 
                            rows="2" 
                            placeholder="Catatan pengerjaan, email akun, atau instruksi..."
                            class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex items-center justify-end space-x-2">
                        <button 
                            type="button" 
                            @click="closeFormModal()" 
                            class="px-4 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors cursor-pointer">
                            <span x-text="formMode === 'add' ? 'Simpan Order & Generate Kode' : 'Perbarui Order'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Modal Konfirmasi Hapus Order -->
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
                    <h3 class="text-sm font-bold text-slate-800 font-['Outfit']">Konfirmasi Hapus Order</h3>
                    <button @click="closeDeleteModal()" class="text-slate-400 hover:text-slate-600 text-sm">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form :action="deleteAction" method="POST" class="p-4 space-y-4">
                    @csrf
                    @method('DELETE')

                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-triangle-exclamation text-base"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-700 leading-relaxed">
                                Apakah Anda yakin ingin menghapus order <strong class="text-slate-900" x-text="targetOrder.order_number"></strong> milik <span x-text="targetOrder.customer_name"></span>?
                            </p>
                            <p class="text-[11px] text-slate-500 mt-1">Tindakan ini tidak dapat dibatalkan.</p>
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
                            Ya, Hapus Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>

<script>
    function orderMasterData() {
        return {
            searchQuery: '',
            isFormModalOpen: false,
            isDeleteModalOpen: false,
            formMode: 'add',
            formAction: '{{ route("orders.store") }}',
            deleteAction: '',
            formData: {
                order_number: '',
                customer_name: '',
                customer_phone: '',
                item_type: 'product',
                product_id: '',
                brand_id: '',
                package_name: '',
                price: 0,
                quantity: 1,
                status: 'pending',
                notes: ''
            },
            productsData: @js($products->keyBy('id')),
            brandsData: @js($brands->keyBy('id')),
            targetOrder: {},

            get currentBrandPackages() {
                if (!this.formData.brand_id) return [];
                const brand = this.brandsData[this.formData.brand_id];
                if (!brand || !brand.packages) return [];
                return Array.isArray(brand.packages) ? brand.packages : [];
            },

            init() {
                this.$watch('formData.product_id', (val) => {
                    if (val && this.productsData && this.productsData[val]) {
                        this.formData.price = parseFloat(this.productsData[val].price) || 0;
                    }
                });
                this.$watch('formData.brand_id', (val) => {
                    this.formData.package_name = '';
                });
            },

            selectPackage(pkg) {
                this.formData.package_name = pkg.title || '';
                if (pkg.price) {
                    this.formData.price = parseFloat(pkg.price) || 0;
                }
            },

            onItemTypeChange() {
                this.formData.product_id = '';
                this.formData.brand_id = '';
                this.formData.package_name = '';
                this.formData.price = 0;
            },

            openAddModal() {
                this.formMode = 'add';
                this.formAction = '{{ route("orders.store") }}';
                this.formData = {
                    order_number: '',
                    customer_name: '',
                    customer_phone: '',
                    item_type: 'product',
                    product_id: '',
                    brand_id: '',
                    package_name: '',
                    price: 0,
                    quantity: 1,
                    status: 'pending',
                    notes: ''
                };
                this.isFormModalOpen = true;
            },

            openEditModal(order) {
                this.formMode = 'edit';
                this.formAction = `/orders-create/${order.id}`;
                this.formData = {
                    order_number: order.order_number,
                    customer_name: order.customer_name,
                    customer_phone: order.customer_phone || '',
                    item_type: order.item_type,
                    product_id: order.product_id || '',
                    brand_id: order.brand_id || '',
                    package_name: order.package_name || '',
                    price: parseFloat(order.price) || 0,
                    quantity: parseInt(order.quantity) || 1,
                    status: order.status,
                    notes: order.notes || ''
                };
                this.isFormModalOpen = true;
            },

            closeFormModal() {
                this.isFormModalOpen = false;
            },

            openDeleteModal(order) {
                this.targetOrder = order;
                this.deleteAction = `/orders-create/${order.id}`;
                this.isDeleteModalOpen = true;
            },

            closeDeleteModal() {
                this.isDeleteModalOpen = false;
            },

            async updateOrderStatus(id, newStatus) {
                try {
                    const response = await fetch(`/orders-create/${id}/status`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: newStatus })
                    });
                    const data = await response.json();
                    if (data.status === 'success') {
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: {
                                type: 'success',
                                title: 'Berhasil',
                                message: data.message || 'Status order berhasil diperbarui!'
                            }
                        }));
                    }
                } catch (err) {
                    console.error(err);
                }
            }
        };
    }
</script>
@endsection
