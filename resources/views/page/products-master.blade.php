@extends('layouts.app')

@section('content')
<div class="space-y-4 md:space-y-6" x-data="productMasterData()" x-effect="document.body.style.overflow = (isFormModalOpen || isDeleteModalOpen) ? 'hidden' : ''">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-900 font-['Outfit']">Product Master Management</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola katalog produk, harga, stok, deskripsi, dan status keaktifan produk marketplace.</p>
        </div>
        <button 
            @click="openAddModal()" 
            class="inline-flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2.5 transition-colors shrink-0 cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Produk Baru</span>
        </button>
    </div>

    <!-- Main Product Management Table Card -->
    <div class="bg-white border border-slate-300/80 overflow-hidden">
        <div class="p-3 bg-white border-b border-slate-300/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-800 font-['Outfit']">Daftar Produk Marketplace</h2>
            </div>

            <!-- Search Input -->
            <div class="relative w-full sm:w-64">
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    placeholder="Cari produk / kategori..." 
                    class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1050px] text-left border-collapse">
                <thead>
                    <tr class="bg-slate-200/70 border-b border-slate-300 text-xs font-medium text-slate-600">
                        <th class="py-2.5 px-3.5 w-16 text-center">Gambar</th>
                        <th class="py-2.5 px-3.5 w-52 min-w-[200px]">Nama Produk</th>
                        <th class="py-2.5 px-3.5 w-80 min-w-[320px]">Deskripsi</th>
                        <th class="py-2.5 px-3.5 w-36 min-w-[130px]">Kategori</th>
                        <th class="py-2.5 px-3.5 w-32 min-w-[120px] text-right">Harga</th>
                        <th class="py-2.5 px-3.5 w-20 min-w-[80px] text-center">Stok</th>
                        <th class="py-2.5 px-3.5 w-24 min-w-[90px] text-center">Status</th>
                        <th class="py-2.5 px-3.5 w-32 min-w-[120px] text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/80 text-xs">
                    @forelse($products as $product)
                        <tr 
                            x-show="matchesSearch('{{ strtolower($product->name) }}', '{{ strtolower($product->category) }}')" 
                            class="bg-white hover:bg-slate-50 transition-colors">
                            <td class="py-2.5 px-3.5 text-center">
                                @if($product->image)
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-10 h-10 object-contain rounded border border-slate-200 mx-auto">
                                @else
                                    <div class="w-10 h-10 bg-slate-100 border border-slate-200 text-slate-400 flex items-center justify-center rounded mx-auto">
                                        <i class="fa-regular fa-image text-sm"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="py-2.5 px-3.5 font-semibold text-slate-900">
                                {{ $product->name }}
                            </td>
                            <td class="py-2.5 px-3.5 text-slate-500 text-xs">
                                {{ $product->description ?: '—' }}
                            </td>
                            <td class="py-2.5 px-3.5 text-slate-700 font-medium">
                                {{ $product->category }}
                            </td>
                            <td class="py-2.5 px-3.5 text-right font-medium text-slate-900">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3.5 text-center font-medium text-slate-700">
                                {{ $product->stock }}
                            </td>
                            <td class="py-2.5 px-3.5 text-center">
                                <form action="{{ route('products-master.toggle-active', $product->id) }}" method="POST" class="inline-flex items-center justify-center">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="{{ $product->is_active ? 'Status: Aktif (Klik untuk nonaktifkan)' : 'Status: Nonaktif (Klik untuk aktifkan)' }}" class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $product->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}">
                                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white ring-0 transition duration-200 ease-in-out {{ $product->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                    </button>
                                </form>
                            </td>
                            <td class="py-2.5 px-3.5 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <button 
                                        @click="openEditModal({{ json_encode($product) }})" 
                                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200 cursor-pointer"
                                        title="Edit Produk">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.596C16.424 1.815 17.69 1.815 18.471 2.596L21.4 5.525C22.181 6.306 22.181 7.572 21.4 8.35303Z" fill="currentColor"/>
                                            <path d="M12.071 6.16901L3.00002 15.24C2.68402 15.556 2.45702 15.952 2.34802 16.388L1.05002 21.579C0.914016 22.123 1.39902 22.608 1.94302 22.472L7.13402 21.174C7.57002 21.065 7.96602 20.838 8.28202 20.522L17.353 11.451L12.071 6.16901Z" fill="currentColor"/>
                                        </svg>
                                    </button>
                                    <button 
                                        @click="openDeleteModal({{ json_encode($product) }})" 
                                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200 cursor-pointer"
                                        title="Hapus Produk">
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
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-box-open text-3xl mb-2"></i>
                                <p class="text-xs">Belum ada data produk tersimpan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form (Tambah & Edit Produk) -->
    <template x-teleport="body">
        <div 
            x-show="isFormModalOpen" 
            x-cloak 
            style="display: none;"
            class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-slate-900/50 w-screen h-screen min-h-full min-w-full"
            @keydown.escape.window="closeFormModal()">

            <div 
                @click.outside="closeFormModal()" 
                class="bg-white border border-slate-300 w-full max-w-xl overflow-hidden text-left relative max-h-[90vh] overflow-y-auto">
                
                <div class="p-4 bg-white border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800 font-['Outfit']" x-text="formMode === 'add' ? 'Tambah Produk Baru' : 'Edit Produk'"></h3>
                    <button @click="closeFormModal()" class="text-slate-400 hover:text-slate-600 text-sm">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form :action="formAction" method="POST" class="p-4 space-y-4">
                    @csrf
                    <template x-if="formMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Produk <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            x-model="formData.name" 
                            required 
                            placeholder="Contoh: Google One 2TB + Gemini Advanced"
                            class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                            <x-select 
                                name="category" 
                                bind="formData.category" 
                                :options="array_combine($categories, $categories)" 
                                placeholder="-- Pilih Kategori --" 
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Stok <span class="text-rose-500">*</span></label>
                            <input 
                                type="number" 
                                name="stock" 
                                x-model="formData.stock" 
                                min="0" 
                                required 
                                class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Harga (Rp) <span class="text-rose-500">*</span></label>
                            <input 
                                type="number" 
                                name="price" 
                                x-model="formData.price" 
                                min="0" 
                                step="500" 
                                required 
                                placeholder="85000"
                                class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">URL Gambar (Opsional)</label>
                            <input 
                                type="text" 
                                name="image" 
                                x-model="formData.image" 
                                placeholder="https://..."
                                class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Produk</label>
                        <textarea 
                            name="description" 
                            x-model="formData.description" 
                            rows="3" 
                            placeholder="Tuliskan deskripsi singkat produk..."
                            class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500"></textarea>
                    </div>

                    <div class="flex items-center space-x-2 pt-1">
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            id="is_active" 
                            value="1" 
                            x-model="formData.is_active" 
                            class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label for="is_active" class="text-xs font-medium text-slate-700 cursor-pointer">Status Aktif (Tampilkan di marketplace)</label>
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
                            <span x-text="formMode === 'add' ? 'Simpan Produk' : 'Perbarui Produk'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Modal Konfirmasi Hapus Produk -->
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
                    <h3 class="text-sm font-bold text-slate-800 font-['Outfit']">Konfirmasi Hapus Produk</h3>
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
                                Apakah Anda yakin ingin menghapus produk <strong class="text-slate-900" x-text="targetProduct.name"></strong>?
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
                            Ya, Hapus Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>

<script>
    function productMasterData() {
        return {
            searchQuery: '',
            isFormModalOpen: false,
            isDeleteModalOpen: false,
            formMode: 'add', // 'add' or 'edit'
            formAction: '{{ route("products-master.store") }}',
            deleteAction: '',
            formData: {
                name: '',
                category: '',
                price: '',
                stock: 10,
                description: '',
                image: '',
                is_active: true
            },
            targetProduct: {},

            matchesSearch(name, category) {
                if (!this.searchQuery.trim()) return true;
                const query = this.searchQuery.toLowerCase();
                return name.includes(query) || category.includes(query);
            },

            openAddModal() {
                this.formMode = 'add';
                this.formAction = '{{ route("products-master.store") }}';
                this.formData = {
                    name: '',
                    category: '',
                    price: '',
                    stock: 10,
                    description: '',
                    image: '',
                    is_active: true
                };
                this.isFormModalOpen = true;
            },

            openEditModal(product) {
                this.formMode = 'edit';
                this.formAction = `/products-master/${product.id}`;
                this.formData = {
                    name: product.name || '',
                    category: product.category || '',
                    price: product.price || 0,
                    stock: product.stock !== undefined ? product.stock : 0,
                    description: product.description || '',
                    image: product.image || '',
                    is_active: product.is_active ? true : false
                };
                this.isFormModalOpen = true;
            },

            closeFormModal() {
                this.isFormModalOpen = false;
            },

            openDeleteModal(product) {
                this.targetProduct = product;
                this.deleteAction = `/products-master/${product.id}`;
                this.isDeleteModalOpen = true;
            },

            closeDeleteModal() {
                this.isDeleteModalOpen = false;
            }
        };
    }
</script>
@endsection
