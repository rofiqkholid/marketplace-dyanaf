<!-- Logout Confirmation Modal Component (English) -->
<div x-data="{ openLogoutModal: false }"
     x-effect="document.body.style.overflow = openLogoutModal ? 'hidden' : ''"
     @open-logout-modal.window="openLogoutModal = true"
     @keydown.escape.window="openLogoutModal = false">

    <!-- Modal Backdrop & Container -->
    <div x-show="openLogoutModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50"
         style="display: none;">
        
        <!-- Modal Card Box -->
        <div @click.outside="openLogoutModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="w-full max-w-md bg-white border border-slate-300 rounded-none p-6 text-left relative">
            
            <!-- Close Icon -->
            <button @click="openLogoutModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <!-- Icon & Content Header -->
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 rounded-none bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 text-xl font-bold border border-rose-200">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 font-['Outfit']">Confirm Logout</h3>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        Are you sure you want to log out of your account? You will need to sign in again to access your account.
                    </p>
                </div>
            </div>

            <!-- Action Buttons (Cancel / Yes, Logout) -->
            <div class="mt-6 flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
                <button type="button" 
                        @click="openLogoutModal = false" 
                        class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300/80 rounded-none transition-colors cursor-pointer">
                    Cancel
                </button>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-none transition-colors active:scale-[0.98] flex items-center space-x-2 cursor-pointer">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>Yes, Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

