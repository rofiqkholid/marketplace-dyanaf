@extends('layouts.app')

@section('content')
<div class="space-y-4 md:space-y-6" x-data="userData()" x-effect="document.body.style.overflow = (isFormModalOpen || isDeleteModalOpen) ? 'hidden' : ''">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-900 font-['Outfit']">User Management</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola data pengguna, peranan (role), email, avatar, dan hak akses akun marketplace.</p>
        </div>
        <button 
            @click="openAddModal()" 
            class="inline-flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2.5 transition-colors shrink-0 cursor-pointer">
            <i class="fa-solid fa-user-plus text-xs"></i>
            <span>Tambah User Baru</span>
        </button>
    </div>

    <!-- Main User Management Table Card -->
    <div class="bg-white border border-slate-300/80 overflow-hidden">
        <div class="p-3 bg-white border-b border-slate-300/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-800 font-['Outfit']">Daftar Pengguna Marketplace</h2>
            </div>

            <!-- Search Input -->
            <div class="relative w-full sm:w-64">
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    placeholder="Cari nama, email, role..." 
                    class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] text-left border-collapse">
                <thead>
                    <tr class="bg-slate-200/70 border-b border-slate-300 text-xs font-medium text-slate-600">
                        <th class="py-2.5 px-3.5 w-14 text-center">Avatar</th>
                        <th class="py-2.5 px-3.5 w-44">Nama Lengkap</th>
                        <th class="py-2.5 px-3.5 w-56">Email</th>
                        <th class="py-2.5 px-3.5 w-64">Google ID / SSO</th>
                        <th class="py-2.5 px-3.5 w-28 text-center">Role / Hak Akses</th>
                        <th class="py-2.5 px-3.5 w-28 text-center">Dyanaf Poin</th>
                        <th class="py-2.5 px-3.5 w-36 text-center">Terdaftar</th>
                        <th class="py-2.5 px-3.5 w-28 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/80 text-xs">
                    @forelse($users as $user)
                        <tr 
                            x-show="matchesSearch('{{ strtolower($user->name) }}', '{{ strtolower($user->email) }}', '{{ strtolower($user->role) }}')" 
                            class="bg-white hover:bg-slate-50 transition-colors">
                            <td class="py-2.5 px-3.5 text-center">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar }}" 
                                         alt="{{ $user->name }}" 
                                         referrerpolicy="no-referrer"
                                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=1e293b&background=e2e8f0';"
                                         class="w-9 h-9 rounded-full object-cover border border-slate-200 mx-auto">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 text-slate-500 flex items-center justify-center font-bold text-xs mx-auto">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-2.5 px-3.5 font-semibold text-slate-900">
                                {{ $user->name }}
                            </td>
                            <td class="py-2.5 px-3.5 text-slate-700 text-xs">
                                {{ $user->email }}
                            </td>
                            <td class="py-2.5 px-3.5 text-slate-600 text-xs">
                                @if($user->google_id)
                                    <div class="flex items-center space-x-1.5 text-slate-700">
                                        <i class="fa-brands fa-google text-xs text-rose-500"></i>
                                        <span title="{{ $user->google_id }}">{{ $user->google_id }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3.5 text-center text-slate-700 text-xs">
                                {{ ucfirst($user->role) }}
                            </td>
                            <td class="py-2.5 px-3.5 text-center">
                                <span class="inline-flex items-center space-x-1.5 px-3 py-0.5 bg-gradient-to-r from-purple-600 via-fuchsia-600 to-rose-500 text-white text-xs font-bold rounded-full shrink-0 border border-white/20">
                                    <i class="fa-solid fa-bolt text-[10px] text-amber-300"></i>
                                    <span class="tabular-nums tracking-wide">{{ $user->dyanaf_points }}</span>
                                </span>
                            </td>
                            <td class="py-2.5 px-3.5 text-center text-slate-500 text-[11px]">
                                {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '—' }}
                            </td>
                            <td class="py-2.5 px-3.5 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <button 
                                        @click="openEditModal({{ json_encode($user) }})" 
                                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200 cursor-pointer"
                                        title="Edit User">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.596C16.424 1.815 17.69 1.815 18.471 2.596L21.4 5.525C22.181 6.306 22.181 7.572 21.4 8.35303Z" fill="currentColor"/>
                                            <path d="M12.071 6.16901L3.00002 15.24C2.68402 15.556 2.45702 15.952 2.34802 16.388L1.05002 21.579C0.914016 22.123 1.39902 22.608 1.94302 22.472L7.13402 21.174C7.57002 21.065 7.96602 20.838 8.28202 20.522L17.353 11.451L12.071 6.16901Z" fill="currentColor"/>
                                        </svg>
                                    </button>
                                    @if($user->id !== Auth::id())
                                        <button 
                                            @click="openDeleteModal({{ json_encode($user) }})" 
                                            class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200 cursor-pointer"
                                            title="Hapus User">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                                <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                                <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                            </svg>
                                        </button>
                                    @else
                                        <button disabled class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 text-slate-300 cursor-not-allowed" title="Tidak dapat menghapus akun sendiri">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-300" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                                <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                                <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-users-slash text-3xl mb-2"></i>
                                <p class="text-xs">Belum ada data user tersimpan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form (Tambah & Edit User) -->
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
                    <h3 class="text-sm font-bold text-slate-800 font-['Outfit']" x-text="formMode === 'add' ? 'Tambah User Baru' : 'Edit Data User'"></h3>
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
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            x-model="formData.name" 
                            required 
                            placeholder="Contoh: Dhen Rofiq"
                            class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email <span class="text-rose-500">*</span></label>
                        <input 
                            type="email" 
                            name="email" 
                            x-model="formData.email" 
                            required 
                            placeholder="user@dyanaf.com"
                            class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Role / Hak Akses <span class="text-rose-500">*</span></label>
                            <x-select 
                                name="role" 
                                bind="formData.role" 
                                :options="['pengunjung' => 'Pengunjung', 'pemilik' => 'Pemilik']" 
                                placeholder="-- Pilih Role --" 
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Dyanaf Poin (Kredit)
                            </label>
                            <input 
                                type="number" 
                                name="dyanaf_points" 
                                x-model="formData.dyanaf_points" 
                                min="0"
                                placeholder="0"
                                class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Password <span x-show="formMode === 'add'" class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            x-model="formData.password" 
                            :required="formMode === 'add'"
                            placeholder="••••••••"
                            class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
                        <span x-show="formMode === 'edit'" class="text-[10px] text-slate-400 block mt-0.5">Kosongkan jika tidak ingin mengubah password</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">URL Avatar / Foto (Opsional)</label>
                        <input 
                            type="text" 
                            name="avatar" 
                            x-model="formData.avatar" 
                            placeholder="https://..."
                            class="w-full px-3 py-2 bg-white border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-blue-500">
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
                            <span x-text="formMode === 'add' ? 'Simpan User' : 'Perbarui User'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Modal Konfirmasi Hapus User -->
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
                    <h3 class="text-sm font-bold text-slate-800 font-['Outfit']">Konfirmasi Hapus User</h3>
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
                                Apakah Anda yakin ingin menghapus user <strong class="text-slate-900" x-text="targetUser.name"></strong> (<span x-text="targetUser.email"></span>)?
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
                            Ya, Hapus User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>

<script>
    function userData() {
        return {
            searchQuery: '',
            isFormModalOpen: false,
            isDeleteModalOpen: false,
            formMode: 'add', // 'add' or 'edit'
            formAction: '{{ route("users.store") }}',
            deleteAction: '',
            formData: {
                name: '',
                email: '',
                role: 'pengunjung',
                dyanaf_points: 0,
                password: '',
                avatar: ''
            },
            targetUser: {},

            matchesSearch(name, email, role) {
                if (!this.searchQuery.trim()) return true;
                const query = this.searchQuery.toLowerCase();
                return name.includes(query) || email.includes(query) || role.includes(query);
            },

            openAddModal() {
                this.formMode = 'add';
                this.formAction = '{{ route("users.store") }}';
                this.formData = {
                    name: '',
                    email: '',
                    role: 'pengunjung',
                    dyanaf_points: 0,
                    password: '',
                    avatar: ''
                };
                this.isFormModalOpen = true;
            },

            openEditModal(user) {
                this.formMode = 'edit';
                this.formAction = `/users/${user.id}`;
                this.formData = {
                    name: user.name || '',
                    email: user.email || '',
                    role: user.role || 'pengunjung',
                    dyanaf_points: user.dyanaf_points ?? 0,
                    password: '',
                    avatar: user.avatar || ''
                };
                this.isFormModalOpen = true;
            },

            closeFormModal() {
                this.isFormModalOpen = false;
            },

            openDeleteModal(user) {
                this.targetUser = user;
                this.deleteAction = `/users/${user.id}`;
                this.isDeleteModalOpen = true;
            },

            closeDeleteModal() {
                this.isDeleteModalOpen = false;
            }
        };
    }
</script>
@endsection
