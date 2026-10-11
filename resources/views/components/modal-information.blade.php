<!-- Centered Information / Require Login Modal Component -->
<div x-data="{ 
        openInformationModal: false, 
        infoTitle: 'Informasi', 
        infoMsg: '', 
        modalType: 'login', // 'login', 'insufficient_points', 'info'
        btnText: 'Login dengan Google',
        btnUrl: '{{ route('auth.google') }}',
        showLoginBtn: true,
        loginUrl: '{{ route('auth.google') }}' 
     }"
     x-effect="document.body.style.overflow = openInformationModal ? 'hidden' : ''"
     @open-modal-information.window="
        openInformationModal = true; 
        infoTitle = $event.detail.title || 'Informasi'; 
        infoMsg = $event.detail.message || '';
        modalType = $event.detail.type || 'info';
        btnText = $event.detail.btnText || 'Login dengan Google';
        btnUrl = $event.detail.btnUrl || '{{ route('auth.google') }}';
        showLoginBtn = ($event.detail.type === 'login');
     "
     @open-require-login-modal.window="
        openInformationModal = true; 
        infoTitle = $event.detail.title || 'Login Diperlukan'; 
        infoMsg = $event.detail.message || 'Silakan login terlebih dahulu menggunakan akun Google untuk mengakses fitur ini.';
        modalType = 'login';
        showLoginBtn = true;
     "
     @keydown.escape.window="openInformationModal = false">

    <!-- Modal Backdrop & Container -->
    <div x-show="openInformationModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50"
         style="display: none;">
        
        <!-- Modal Card Box (Clean, Compact max-w-md Design) -->
        <div @click.outside="openInformationModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="w-full max-w-md bg-white border border-slate-300/90 shadow-xl p-6 sm:p-7 relative text-center">
            
            <!-- Close Button (Top Right) -->
            <button @click="openInformationModal = false" 
                    type="button"
                    class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors"
                    title="Tutup">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <!-- Centered Distinctive Icon Badge -->
            <div class="mb-4 flex justify-center">
                <template x-if="modalType === 'login'">
                    <div class="w-13 h-13 rounded-full bg-blue-50 border border-blue-200/80 flex items-center justify-center text-blue-600 shadow-sm">
                        <i class="fa-solid fa-user-lock text-xl"></i>
                    </div>
                </template>
                <template x-if="modalType === 'insufficient_points'">
                    <div class="w-13 h-13 rounded-full bg-amber-50 border border-amber-200/80 flex items-center justify-center text-amber-500 shadow-sm">
                        <i class="fa-solid fa-bolt text-xl"></i>
                    </div>
                </template>
                <template x-if="modalType === 'coming_soon'">
                    <div class="w-13 h-13 rounded-full bg-blue-50 border border-blue-200/80 flex items-center justify-center text-blue-600 shadow-sm">
                        <i class="fa-solid fa-rocket text-xl"></i>
                    </div>
                </template>
                <template x-if="modalType === 'info'">
                    <div class="w-13 h-13 rounded-full bg-blue-50 border border-blue-200/80 flex items-center justify-center text-blue-600 shadow-sm">
                        <i class="fa-solid fa-circle-info text-xl"></i>
                    </div>
                </template>
            </div>

            <!-- Header & Description -->
            <div class="space-y-2.5 mb-6">
                <h3 class="text-base sm:text-lg font-bold text-slate-900 font-['Outfit'] tracking-tight" x-text="infoTitle">
                    Informasi
                </h3>
                <div class="text-xs sm:text-[13px] text-slate-600 leading-relaxed max-w-sm mx-auto">
                    <p x-html="infoMsg"></p>
                </div>
            </div>

            <!-- Action Buttons -->
            <template x-if="modalType === 'coming_soon' || modalType === 'info'">
                <div>
                    <button type="button" 
                            @click="openInformationModal = false" 
                            class="w-full inline-flex items-center justify-center py-2.5 px-4 text-xs sm:text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors cursor-pointer shadow-sm active:scale-[0.99]">
                        Mengerti
                    </button>
                </div>
            </template>

            <template x-if="modalType !== 'coming_soon' && modalType !== 'info'">
                <div class="grid grid-cols-2 gap-2.5">
                    <button type="button" 
                            @click="openInformationModal = false" 
                            class="inline-flex items-center justify-center py-2.5 px-3 text-xs sm:text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-colors cursor-pointer">
                        <span x-text="modalType === 'insufficient_points' ? 'Tutup' : 'Nanti Saja'"></span>
                    </button>

                    <!-- Login with Google CTA -->
                    <template x-if="modalType === 'login'">
                        <a :href="loginUrl" 
                           class="inline-flex items-center justify-center space-x-2 py-2.5 px-3 text-xs sm:text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-sm cursor-pointer active:scale-[0.99]">
                            <!-- Google Logo SVG -->
                            <svg class="w-3.5 h-3.5 shrink-0 bg-white p-0.5 rounded-xs" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                            <span class="truncate">Login Google</span>
                        </a>
                    </template>

                    <!-- Insufficient Points CTA -->
                    <template x-if="modalType === 'insufficient_points'">
                        <a href="/" 
                           @click="openInformationModal = false"
                           class="inline-flex items-center justify-center space-x-1.5 py-2.5 px-3 text-xs sm:text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-sm cursor-pointer active:scale-[0.99]">
                            <i class="fa-solid fa-cart-shopping text-xs"></i>
                            <span class="truncate">Order Produk</span>
                        </a>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>
