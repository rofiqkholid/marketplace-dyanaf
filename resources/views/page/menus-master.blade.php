@extends('layouts.app')

@section('content')
<div class="space-y-4 md:space-y-6" x-data="menuMasterData()" x-effect="document.body.style.overflow = (isFormModalOpen || isDeleteModalOpen) ? 'hidden' : ''">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-900 font-['Outfit']">Menu Master Management</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola struktur hirarki menu navigasi, urutan, akses role, dan keaktifan menu aplikasi.</p>
        </div>
        <button 
            @click="openAddModal()" 
            class="inline-flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2.5 transition-colors shrink-0 cursor-pointer">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Menu Baru</span>
        </button>
    </div>


    <!-- Main Menu Management Table & Hierarchy -->
    <div class="bg-white border border-slate-300/80 overflow-hidden">
        <div class="p-3 bg-white border-b border-slate-300/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-800 font-['Outfit']">Daftar Struktur Menu Navigasi</h2>
            </div>

            <!-- Search Input -->
            <div class="relative w-full sm:w-64">
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    placeholder="Cari menu / submenu..." 
                    class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] text-left border-collapse">
                <thead>
                    <tr class="bg-slate-200/70 border-b border-slate-300 text-xs font-medium text-slate-600">
                        <th class="py-2.5 px-3.5 w-12 text-center">Icon</th>
                        <th class="py-2.5 px-3.5">Nama Menu / Submenu</th>
                        <th class="py-2.5 px-3.5">Parent Level</th>
                        <th class="py-2.5 px-3.5">URL Target</th>
                        <th class="py-2.5 px-3.5 w-20 text-center">Urutan</th>
                        <th class="py-2.5 px-3.5 w-28 text-center">Akses Role</th>
                        <th class="py-2.5 px-3.5 w-24 text-center">Status</th>
                        <th class="py-2.5 px-3.5 w-24 text-center">Maintenance</th>
                        <th class="py-2.5 px-3.5 w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/80 text-xs">
                    @forelse($menus as $menu)
                        <!-- Parent Row (Level 1) -->
                        <tr 
                            x-show="matchesSearch('{{ strtolower($menu->name) }}', '{{ strtolower($menu->url) }}')" 
                            class="bg-white hover:bg-slate-50 transition-colors">
                            <td class="py-2.5 px-3.5 text-center text-slate-700">
                                @if($menu->icon)
                                    <i class="{{ $menu->icon }} text-sm"></i>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3.5 font-medium text-slate-900">
                                <span>{{ $menu->name }}</span>
                            </td>
                            <td class="py-2.5 px-3.5 text-slate-500 font-normal">
                                <span class="text-xs font-normal text-slate-500">Root</span>
                            </td>
                            <td class="py-2.5 px-3.5 text-slate-600 text-xs">
                                {{ $menu->url }}
                            </td>
                            <td class="py-2.5 px-3.5 text-center font-medium text-slate-700">
                                {{ $menu->order }}
                            </td>
                            <td class="py-2.5 px-3.5 text-center">
                                @if($menu->role === 'pemilik')
                                    <span class="text-xs font-normal text-slate-600">Pemilik</span>
                                @else
                                    <span class="text-xs font-normal text-slate-600">Semua</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3.5 text-center">
                                <button type="button" 
                                        @click="toggleActive({{ $menu->id }})" 
                                        :title="menuActiveStates[{{ $menu->id }}] ? 'Status: Aktif (Klik untuk nonaktifkan)' : 'Status: Nonaktif (Klik untuk aktifkan)'" 
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                        :class="menuActiveStates[{{ $menu->id }}] ? 'bg-emerald-500' : 'bg-slate-300'">
                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white ring-0 transition duration-200 ease-in-out"
                                          :class="menuActiveStates[{{ $menu->id }}] ? 'translate-x-4' : 'translate-x-0'"></span>
                                </button>
                            </td>
                            <td class="py-2.5 px-3.5 text-center">
                                <button type="button" 
                                        @click="toggleMaintenance({{ $menu->id }})" 
                                        :title="menuMaintenanceStates[{{ $menu->id }}] ? 'Maintenance: Aktif (Klik untuk nonaktifkan)' : 'Maintenance: Nonaktif (Klik untuk aktifkan)'" 
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                        :class="menuMaintenanceStates[{{ $menu->id }}] ? 'bg-amber-500' : 'bg-slate-300'">
                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white ring-0 transition duration-200 ease-in-out"
                                          :class="menuMaintenanceStates[{{ $menu->id }}] ? 'translate-x-4' : 'translate-x-0'"></span>
                                </button>
                            </td>
                            <td class="py-2.5 px-3.5 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <button 
                                        @click="openEditModal({{ json_encode($menu) }})" 
                                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200 cursor-pointer"
                                        title="Edit Menu">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.596C16.424 1.815 17.69 1.815 18.471 2.596L21.4 5.525C22.181 6.306 22.181 7.572 21.4 8.35303Z" fill="currentColor"/>
                                            <path d="M12.071 6.16901L3.00002 15.24C2.68402 15.556 2.45702 15.952 2.34802 16.388L1.05002 21.579C0.914016 22.123 1.39902 22.608 1.94302 22.472L7.13402 21.174C7.57002 21.065 7.96602 20.838 8.28202 20.522L17.353 11.451L12.071 6.16901Z" fill="currentColor"/>
                                        </svg>
                                    </button>
                                    <button 
                                        @click="openDeleteModal({{ json_encode($menu) }})" 
                                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200 cursor-pointer"
                                        title="Hapus Menu">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                            <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                            <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Children Rows (Level 2 Submenus) -->
                        @foreach($menu->children as $child)
                            <tr 
                                x-show="matchesSearch('{{ strtolower($child->name) }}', '{{ strtolower($child->url) }}')" 
                                class="bg-slate-50/70 hover:bg-blue-50/40 transition-colors">
                                <td class="py-2 px-3.5 text-center text-slate-600 pl-6">
                                    @if($child->icon)
                                        <i class="{{ $child->icon }} text-xs"></i>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3.5 font-medium text-slate-800 pl-8">
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-turn-up rotate-90 text-[10px] text-slate-400"></i>
                                        <span>{{ $child->name }}</span>
                                    </div>
                                </td>
                                <td class="py-2 px-3.5 text-slate-500 font-normal">
                                    <span class="text-xs text-slate-600 font-medium">{{ $menu->name }}</span>
                                </td>
                                <td class="py-2 px-3.5 text-slate-600 text-xs">
                                    {{ $child->url }}
                                </td>
                                <td class="py-2 px-3.5 text-center text-slate-600 font-medium">
                                    {{ $child->order }}
                                </td>
                                <td class="py-2 px-3.5 text-center">
                                    @if($child->role === 'pemilik')
                                        <span class="text-xs font-normal text-slate-600">Pemilik</span>
                                    @else
                                        <span class="text-xs font-normal text-slate-600">Semua</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3.5 text-center">
                                    <button type="button" 
                                            @click="toggleActive({{ $child->id }})" 
                                            :title="menuActiveStates[{{ $child->id }}] ? 'Status: Aktif (Klik untuk nonaktifkan)' : 'Status: Nonaktif (Klik untuk aktifkan)'" 
                                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                            :class="menuActiveStates[{{ $child->id }}] ? 'bg-emerald-500' : 'bg-slate-300'">
                                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white ring-0 transition duration-200 ease-in-out"
                                              :class="menuActiveStates[{{ $child->id }}] ? 'translate-x-4' : 'translate-x-0'"></span>
                                    </button>
                                </td>
                                <td class="py-2 px-3.5 text-center">
                                    <button type="button" 
                                            @click="toggleMaintenance({{ $child->id }})" 
                                            :title="menuMaintenanceStates[{{ $child->id }}] ? 'Maintenance: Aktif (Klik untuk nonaktifkan)' : 'Maintenance: Nonaktif (Klik untuk aktifkan)'" 
                                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                            :class="menuMaintenanceStates[{{ $child->id }}] ? 'bg-amber-500' : 'bg-slate-300'">
                                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white ring-0 transition duration-200 ease-in-out"
                                              :class="menuMaintenanceStates[{{ $child->id }}] ? 'translate-x-4' : 'translate-x-0'"></span>
                                    </button>
                                </td>
                                <td class="py-2 px-3.5 text-center">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        <button 
                                            @click="openEditModal({{ json_encode($child) }})" 
                                            class="w-8 h-8 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200 cursor-pointer"
                                            title="Edit Submenu">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.596C16.424 1.815 17.69 1.815 18.471 2.596L21.4 5.525C22.181 6.306 22.181 7.572 21.4 8.35303Z" fill="currentColor"/>
                                                <path d="M12.071 6.16901L3.00002 15.24C2.68402 15.556 2.45702 15.952 2.34802 16.388L1.05002 21.579C0.914016 22.123 1.39902 22.608 1.94302 22.472L7.13402 21.174C7.57002 21.065 7.96602 20.838 8.28202 20.522L17.353 11.451L12.071 6.16901Z" fill="currentColor"/>
                                            </svg>
                                        </button>
                                        <button 
                                            @click="openDeleteModal({{ json_encode($child) }})" 
                                            class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200 cursor-pointer"
                                            title="Hapus Submenu">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                                <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                                <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-500">
                                <i class="fa-solid fa-folder-open text-2xl mb-2 text-slate-400 block"></i>
                                <span>Belum ada menu navigasi yang terdaftar.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add & Edit Menu Modal (Matching Logout Modal style) -->
    <template x-teleport="body">
        <div 
            x-show="isFormModalOpen" 
            x-cloak 
            style="display: none;" 
            class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-slate-900/50 w-screen h-screen min-h-full min-w-full"
            @keydown.escape.window="isFormModalOpen = false">
            
            <div 
                @click.away="isFormModalOpen = false"
                class="w-full max-w-2xl bg-white border border-slate-300 rounded-none p-6 text-left relative max-h-[90vh] overflow-y-auto">
                
                <!-- Close Icon -->
                <button @click="isFormModalOpen = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <!-- Icon & Content Header -->
                <div class="flex items-start space-x-4 mb-4">
                    <div class="w-12 h-12 rounded-none bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 text-xl font-bold border border-blue-200">
                        <i class="fa-solid" :class="isEditMode ? 'fa-pen-to-square' : 'fa-plus'"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 font-['Outfit']" x-text="isEditMode ? 'Edit Menu Navigasi' : 'Tambah Menu Baru'"></h3>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Isi dan sesuaikan informasi hirarki menu navigasi aplikasi di bawah ini.
                        </p>
                    </div>
                </div>

                <form :action="formActionUrl" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="_method" :value="isEditMode ? 'PUT' : 'POST'" :disabled="!isEditMode">

                    <!-- Nama Menu -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Menu <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            x-model="formData.name" 
                            required 
                            placeholder="Contoh: Products, Discord, User Management..." 
                            class="w-full p-2 bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-blue-500 focus:outline-none">
                    </div>

                    <!-- Parent Menu Dropdown -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Parent Level / Section</label>
                        @php
                            $parentOptions = array_merge(
                                [['value' => '', 'label' => 'Tanpa Parent (Root Level)']],
                                $parentMenus->map(fn($p) => ['value' => (string)$p->id, 'label' => $p->name . ' (Parent Header)'])->toArray()
                            );
                        @endphp
                        <x-select 
                            name="parent_id" 
                            bind="formData.parent_id" 
                            :options="$parentOptions" 
                            placeholder="Tanpa Parent (Root Level)"
                        />
                        <span class="text-[11px] text-slate-500 mt-1 block">Pilih Parent jika menu ini adalah Sub-Menu (Level 2). Kosongkan jika Root level.</span>
                    </div>

                    <!-- URL Target & Icon Class -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">URL / Path Target</label>
                            <input 
                                type="text" 
                                name="url" 
                                x-model="formData.url" 
                                placeholder="Contoh: /menus-master atau #" 
                                class="w-full p-2 bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-blue-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Class Icon (FontAwesome)</label>
                            <div class="flex items-stretch space-x-2">
                                <input 
                                    type="text" 
                                    name="icon" 
                                    x-model="formData.icon" 
                                    placeholder="fa-solid fa-list-check" 
                                    class="w-full p-2 bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-blue-500 focus:outline-none">
                                <div class="w-9 bg-slate-100 border border-slate-300 flex items-center justify-center shrink-0">
                                    <i :class="formData.icon || 'fa-solid fa-question text-slate-400'"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order & Role Limit -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Urutan Tampil (Order) <span class="text-rose-500">*</span></label>
                            <input 
                                type="number" 
                                name="order" 
                                x-model="formData.order" 
                                required 
                                min="0" 
                                class="w-full p-2 bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-blue-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Akses Role</label>
                            <x-select 
                                name="role" 
                                bind="formData.role" 
                                :options="[
                                    '' => 'Semua Role (Publik)',
                                    'pemilik' => 'Khusus Pemilik (Admin/Owner)',
                                    'pengunjung' => 'Khusus Pengunjung'
                                ]" 
                                placeholder="Semua Role (Publik)"
                            />
                        </div>
                    </div>

                    <!-- Status & Maintenance Checkboxes -->
                    <div class="pt-3 border-t border-slate-200 space-y-2.5">
                        <label class="inline-flex items-center space-x-2.5 cursor-pointer group select-none">
                            <input 
                                type="checkbox" 
                                name="is_active" 
                                value="1" 
                                :checked="formData.is_active" 
                                x-model="formData.is_active" 
                                class="hidden">
                            
                            <div class="w-4.5 h-4.5 rounded-none border flex items-center justify-center shrink-0 transition-colors"
                                 :class="formData.is_active ? 'bg-sky-50 border-sky-400 text-sky-500 font-bold' : 'bg-white border-slate-300 text-transparent'">
                                <i class="fa-solid fa-check text-[11px]"></i>
                            </div>

                            <span class="font-bold text-slate-800 text-xs group-hover:text-blue-600 transition-colors">
                                Aktifkan Menu Ini (Tampilkan di Sidebar Navigation)
                            </span>
                        </label>

                        <div>
                            <label class="inline-flex items-center space-x-2.5 cursor-pointer group select-none">
                                <input 
                                    type="checkbox" 
                                    name="is_maintenance" 
                                    value="1" 
                                    :checked="formData.is_maintenance" 
                                    x-model="formData.is_maintenance" 
                                    class="hidden">
                                
                                <div class="w-4.5 h-4.5 rounded-none border flex items-center justify-center shrink-0 transition-colors"
                                     :class="formData.is_maintenance ? 'bg-amber-50 border-amber-400 text-amber-500 font-bold' : 'bg-white border-slate-300 text-transparent'">
                                    <i class="fa-solid fa-check text-[11px]"></i>
                                </div>

                                <span class="font-bold text-slate-800 text-xs group-hover:text-amber-600 transition-colors">
                                    Set Mode Maintenance (Arahkan pengunjung ke halaman maintenance)
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons Footer -->
                    <div class="mt-6 flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
                        <button 
                            type="button" 
                            @click="isFormModalOpen = false" 
                            class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300/80 rounded-none transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-none transition-colors active:scale-[0.98] flex items-center space-x-2 cursor-pointer">
                            <i class="fa-solid fa-check text-xs"></i>
                            <span x-text="isEditMode ? 'Simpan Perubahan' : 'Tambah Menu'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Delete Confirmation Modal (Matching Logout Modal style) -->
    <template x-teleport="body">
        <div 
            x-show="isDeleteModalOpen" 
            x-cloak 
            style="display: none;" 
            class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-slate-900/50 w-screen h-screen min-h-full min-w-full"
            @keydown.escape.window="isDeleteModalOpen = false">
            
            <div 
                @click.away="isDeleteModalOpen = false"
                class="w-full max-w-md bg-white border border-slate-300 rounded-none p-6 text-left relative">
                
                <!-- Close Icon -->
                <button @click="isDeleteModalOpen = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <!-- Icon & Content Header -->
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-none bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 text-xl font-bold border border-rose-200">
                        <i class="fa-solid fa-trash-can"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 font-['Outfit']">Konfirmasi Hapus Menu</h3>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Apakah Anda yakin ingin menghapus menu <span class="font-bold text-slate-900" x-text="deleteTarget ? deleteTarget.name : ''"></span>? Semua sub-menu di dalamnya juga akan terhapus.
                        </p>
                    </div>
                </div>

                <!-- Action Buttons (Cancel / Yes, Delete) -->
                <div class="mt-6 flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
                    <button type="button" 
                            @click="isDeleteModalOpen = false" 
                            class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300/80 rounded-none transition-colors cursor-pointer">
                        Batal
                    </button>

                    <form :action="deleteActionUrl" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-none transition-colors active:scale-[0.98] flex items-center space-x-2 cursor-pointer">
                            <i class="fa-solid fa-check text-xs"></i>
                            <span>Ya, Hapus</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>

</div>

<script>
    function menuMasterData() {
        return {
            isFormModalOpen: false,
            isDeleteModalOpen: false,
            isEditMode: false,
            searchQuery: '',
            formActionUrl: '{{ route("menus-master.store") }}',
            deleteActionUrl: '',
            deleteTarget: {},
            menuActiveStates: {
                @foreach($menus as $m)
                    {{ $m->id }}: {{ $m->is_active ? 'true' : 'false' }},
                    @foreach($m->children as $c)
                        {{ $c->id }}: {{ $c->is_active ? 'true' : 'false' }},
                    @endforeach
                @endforeach
            },
            menuMaintenanceStates: {
                @foreach($menus as $m)
                    {{ $m->id }}: {{ $m->is_maintenance ? 'true' : 'false' }},
                    @foreach($m->children as $c)
                        {{ $c->id }}: {{ $c->is_maintenance ? 'true' : 'false' }},
                    @endforeach
                @endforeach
            },
            formData: {
                id: null,
                name: '',
                parent_id: '',
                url: '#',
                icon: '',
                order: 1,
                role: '',
                is_active: true,
                is_maintenance: false
            },
            presetIcons: [
                'fa-solid fa-star text-amber-600 text-sm',
                'fa-solid fa-chart-line text-rose-600 text-sm',
                'fa-solid fa-wand-magic-sparkles text-blue-500 text-sm',
                'fa-solid fa-crown text-amber-600 text-sm',
                'fa-solid fa-tags text-emerald-600 text-sm',
                'fa-brands fa-discord text-blue-500 w-4 text-center',
                'fa-solid fa-gamepad text-emerald-600 w-4 text-center',
                'fa-solid fa-laptop-code text-cyan-600 w-4 text-center',
                'fa-solid fa-robot text-blue-500 w-4 text-center',
                'fa-solid fa-layer-group text-purple-600 w-4 text-center',
                'fa-solid fa-users text-blue-600 w-4 text-center',
                'fa-solid fa-list-check text-emerald-600 w-4 text-center'
            ],

            matchesSearch(name, url) {
                if (!this.searchQuery) return true;
                const query = this.searchQuery.toLowerCase();
                return name.includes(query) || url.includes(query);
            },

            openAddModal() {
                this.isEditMode = false;
                this.formActionUrl = '{{ route("menus-master.store") }}';
                this.formData = {
                    id: null,
                    name: '',
                    parent_id: '',
                    url: '#',
                    icon: '',
                    order: 1,
                    role: '',
                    is_active: true,
                    is_maintenance: false
                };
                this.isFormModalOpen = true;
            },

            openEditModal(menu) {
                this.isEditMode = true;
                this.formActionUrl = '/menus-master/' + menu.id;
                this.formData = {
                    id: menu.id,
                    name: menu.name || '',
                    parent_id: menu.parent_id || '',
                    url: menu.url || '#',
                    icon: menu.icon || '',
                    order: menu.order || 0,
                    role: menu.role || '',
                    is_active: Boolean(menu.is_active),
                    is_maintenance: Boolean(menu.is_maintenance)
                };
                this.isFormModalOpen = true;
            },

            openDeleteModal(menu) {
                this.deleteTarget = menu;
                this.deleteActionUrl = '/menus-master/' + menu.id;
                this.isDeleteModalOpen = true;
            },

            async toggleActive(id) {
                try {
                    const response = await fetch(`/menus-master/${id}/toggle-active`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.status === 'success') {
                        this.menuActiveStates[id] = data.is_active;
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: {
                                type: 'success',
                                title: 'Berhasil',
                                message: data.message || 'Status keaktifan menu berhasil diubah!'
                            }
                        }));
                    }
                } catch (err) {
                    console.error(err);
                }
            },

            async toggleMaintenance(id) {
                try {
                    const response = await fetch(`/menus-master/${id}/toggle-maintenance`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.status === 'success') {
                        this.menuMaintenanceStates[id] = data.is_maintenance;
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: {
                                type: 'success',
                                title: 'Berhasil',
                                message: data.message || 'Status maintenance menu berhasil diubah!'
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
