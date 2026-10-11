<div x-data="toastManager()"
     @notify.window="addToast($event.detail)"
     class="fixed bottom-5 right-5 z-[9999] flex flex-col-reverse gap-3 max-w-sm w-full pointer-events-none px-4 sm:px-0">

    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-x-12 scale-95"
             x-transition:enter-end="opacity-100 translate-x-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-x-0 scale-100"
             x-transition:leave-end="opacity-0 translate-x-12 scale-95"
             class="pointer-events-auto bg-white border rounded-2xl p-4 flex items-start space-x-3.5"
             :class="{
                 'border-emerald-200': toast.type === 'success',
                 'border-rose-200': toast.type === 'error',
                 'border-amber-200': toast.type === 'warning',
                 'border-blue-200': toast.type === 'info'
             }">

            <!-- Icon -->
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                 :class="{
                     'bg-emerald-50 text-emerald-600': toast.type === 'success',
                     'bg-rose-50 text-rose-600': toast.type === 'error',
                     'bg-amber-50 text-amber-600': toast.type === 'warning',
                     'bg-blue-50 text-blue-600': toast.type === 'info'
                 }">
                <i x-show="toast.type === 'success'" class="fa-solid fa-circle-check text-lg"></i>
                <i x-show="toast.type === 'error'" class="fa-solid fa-circle-xmark text-lg"></i>
                <i x-show="toast.type === 'warning'" class="fa-solid fa-triangle-exclamation text-lg"></i>
                <i x-show="toast.type === 'info'" class="fa-solid fa-circle-info text-lg"></i>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0 pt-0.5">
                <h4 class="text-xs font-bold text-slate-800 font-['Outfit']" x-text="toast.title"></h4>
                <p class="text-xs text-slate-600 mt-0.5 leading-relaxed" x-text="toast.message"></p>
            </div>

            <!-- Close Button -->
            <button @click="removeToast(toast.id)" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg shrink-0 cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </template>
</div>

<script>
    function toastManager() {
        return {
            toasts: [],
            STORAGE_KEY: 'dyanaf_active_toasts',

            init() {
                const now = Date.now();
                
                // 1. Restore active toasts from sessionStorage that have not expired yet
                try {
                    const stored = JSON.parse(sessionStorage.getItem(this.STORAGE_KEY) || '[]');
                    stored.forEach(t => {
                        const remaining = t.expiresAt - now;
                        if (remaining > 500) {
                            this.toasts.push({
                                id: t.id,
                                type: t.type,
                                title: t.title,
                                message: t.message,
                                expiresAt: t.expiresAt,
                                show: true
                            });
                            setTimeout(() => {
                                this.removeToast(t.id);
                            }, remaining);
                        }
                    });
                } catch (e) {
                    sessionStorage.removeItem(this.STORAGE_KEY);
                }

                // 2. Add current Laravel session flash messages
                @if(session('success'))
                    this.addToast({ type: 'success', title: 'Berhasil!', message: @json(session('success')) });
                @endif
                @if(session('error'))
                    this.addToast({ type: 'error', title: 'Gagal!', message: @json(session('error')) });
                @endif
                @if(session('warning'))
                    this.addToast({ type: 'warning', title: 'Peringatan', message: @json(session('warning')) });
                @endif
                @if(session('info'))
                    this.addToast({ type: 'info', title: 'Informasi', message: @json(session('info')) });
                @endif
                @if($errors->any())
                    @foreach($errors->all() as $error)
                        this.addToast({ type: 'error', title: 'Kesalahan Validasi', message: @json($error) });
                    @endforeach
                @endif

                this.syncStorage();
            },

            addToast(detail) {
                const now = Date.now();
                const duration = detail.duration || 5000;
                const id = now + Math.random().toString(36).substring(2, 9);
                const type = detail.type || 'info';
                const title = detail.title || (type === 'success' ? 'Berhasil!' : type === 'error' ? 'Gagal!' : type === 'warning' ? 'Peringatan' : 'Informasi');
                const message = detail.message || '';

                const toast = {
                    id: id,
                    type: type,
                    title: title,
                    message: message,
                    expiresAt: now + duration,
                    show: true
                };

                this.toasts.push(toast);
                this.syncStorage();

                setTimeout(() => {
                    this.removeToast(id);
                }, duration);
            },

            removeToast(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) {
                    toast.show = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                        this.syncStorage();
                    }, 300);
                }
            },

            syncStorage() {
                try {
                    const now = Date.now();
                    const active = this.toasts.filter(t => t.expiresAt > now).map(t => ({
                        id: t.id,
                        type: t.type,
                        title: t.title,
                        message: t.message,
                        expiresAt: t.expiresAt
                    }));
                    sessionStorage.setItem(this.STORAGE_KEY, JSON.stringify(active));
                } catch (e) {}
            }
        };
    }

    window.notify = function(type, title, message, duration = 5000) {
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { type, title, message, duration }
        }));
    };
</script>
