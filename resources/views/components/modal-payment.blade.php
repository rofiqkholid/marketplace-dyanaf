@props(['product'])

<div x-data="{
    openPaymentModal: false,
    step: 'choose_method', // 'choose_method', 'pay_details', 'success'
    selectedMethod: 'qris',
    customerName: '{{ Auth::user()->name ?? '' }}',
    customerEmail: '{{ Auth::user()->email ?? '' }}',
    customerPhone: '{{ Auth::user()->phone ?? '' }}',
    notes: '',
    loading: false,
    errorMessage: '',
    copied: false,
    orderNumber: '',
    bank: '',
    vaNumber: '',
    qrisUrl: '',
    totalAmount: {{ (int) $product->price }},
    expiryTime: '',
    pollingInterval: null,

    open() {
        this.openPaymentModal = true;
        this.step = 'choose_method';
        this.errorMessage = '';
        this.clearInterval();
    },

    close() {
        this.openPaymentModal = false;
        this.clearInterval();
    },

    clearInterval() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    },

    processPayment() {
        this.loading = true;
        this.errorMessage = '';

        fetch('{{ route('payment.modal.charge', $product) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                payment_method: this.selectedMethod,
                customer_name: this.customerName,
                customer_email: this.customerEmail,
                customer_phone: this.customerPhone,
                notes: this.notes
            })
        })
        .then(res => res.json())
        .then(data => {
            this.loading = false;
            if (!data.success) {
                this.errorMessage = data.message || 'Gagal memproses pembayaran. Silakan coba lagi.';
                return;
            }

            this.orderNumber = data.order_number;
            this.bank = data.bank;
            this.vaNumber = data.va_number;
            this.qrisUrl = data.qris_url;
            this.totalAmount = data.gross_amount;
            this.expiryTime = data.expire_time;
            this.step = 'pay_details';

            // Mulai polling status pembayaran otomatis tiap 3 detik
            this.startPolling();
        })
        .catch(err => {
            this.loading = false;
            this.errorMessage = 'Terjadi kesalahan koneksi. Periksa internet Anda.';
        });
    },

    startPolling() {
        this.clearInterval();
        this.pollingInterval = setInterval(() => {
            if (!this.orderNumber || this.step !== 'pay_details') {
                this.clearInterval();
                return;
            }

            fetch('/payment/status/' + encodeURIComponent(this.orderNumber))
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.payment_status === 'paid') {
                        this.step = 'success';
                        this.clearInterval();
                    }
                })
                .catch(() => {});
        }, 3000);
    },

    copyText(text) {
        if (!text) return;
        navigator.clipboard.writeText(text);
        this.copied = true;
        setTimeout(() => this.copied = false, 2000);
    }
}"
x-effect="document.body.style.overflow = openPaymentModal ? 'hidden' : ''"
@open-payment-modal.window="open()"
@keydown.escape.window="close()"
class="relative z-[9999]">

    <!-- Backdrop Overlay (Tanpa Blur) -->
    <div x-show="openPaymentModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/50 flex items-center justify-center p-3 sm:p-4 z-[9999]"
         style="display: none;">

        <!-- Modal Dialog Container -->
        <div @click.outside="close()"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="w-full max-w-lg bg-white border border-slate-300 rounded-none shadow-xl overflow-hidden flex flex-col max-h-[92vh]">

            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-wallet text-slate-800 text-lg"></i>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 font-['Outfit']">Pembayaran Dyanaf Marketplace</h3>
                        <p class="text-[11px] text-slate-500">Transaksi aman & terverifikasi otomatis</p>
                    </div>
                </div>
                <button type="button" 
                        @click="close()" 
                        class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-5 overflow-y-auto space-y-4 text-xs">

                <!-- Alert Error jika ada -->
                <div x-show="errorMessage" 
                     x-transition 
                     class="p-3 bg-rose-50 border border-rose-200 text-rose-700 flex items-start gap-2">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 mt-0.5"></i>
                    <span x-text="errorMessage"></span>
                </div>

                <!-- STEP 1: PILIH METODE PEMBAYARAN & FORM DATA -->
                <div x-show="step === 'choose_method'" class="space-y-4">
                    
                    <!-- Ringkasan Produk Pesanan -->
                    <div class="p-3 bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            @if($product->image)
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover border border-slate-200">
                            @else
                                <div class="w-12 h-12 bg-slate-200 flex items-center justify-center text-slate-400">
                                    <i class="fa-regular fa-image text-lg"></i>
                                </div>
                            @endif
                            <div>
                                <h4 class="font-bold text-slate-900 leading-tight">{{ $product->name }}</h4>
                                <span class="text-[11px] text-slate-500 block mt-0.5">{{ $product->category ?? 'Layanan Digital' }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[10px] text-slate-500 block">Total Tagihan</span>
                            <span class="text-sm font-bold text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Pilihan Metode Pembayaran Kustom -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-2">Pilih Metode Pembayaran</label>
                        
                        <!-- Tab / Kategori: QRIS -->
                        <div class="space-y-2">
                            <!-- QRIS / E-Wallet Option -->
                            <div @click="selectedMethod = 'qris'"
                                 :class="selectedMethod === 'qris' ? 'border-blue-600 bg-blue-100/50' : 'border-slate-300 hover:border-slate-400 bg-white'"
                                 class="flex items-center justify-between p-3 border cursor-pointer transition-all select-none">
                                <div>
                                    <span class="font-bold text-slate-900 block">QRIS (GoPay, ShopeePay, OVO, Dana, LinkAja)</span>
                                    <span class="text-[11px] text-slate-500">Scan instan dari semua aplikasi m-banking & e-wallet</span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <img src="{{ asset('image/payment-logo/qris.png') }}" alt="QRIS" class="h-5 w-auto object-contain">
                                </div>
                            </div>

                            <!-- Virtual Account Bank Section Header -->
                            <div class="pt-2">
                                <span class="text-xs font-bold text-slate-800 block mb-1.5">Transfer Virtual Account (Verifikasi Otomatis)</span>
                            </div>

                            <!-- BNI Virtual Account -->
                            <div @click="selectedMethod = 'bni'"
                                 :class="selectedMethod === 'bni' ? 'border-blue-600 bg-blue-100/50' : 'border-slate-300 hover:border-slate-400 bg-white'"
                                 class="flex items-center justify-between p-3.5 sm:px-4 sm:py-3.5 border cursor-pointer transition-all select-none">
                                <span class="font-semibold text-xs sm:text-[13px] text-slate-900">BNI Virtual Account</span>
                                <img src="{{ asset('image/payment-logo/bni.png') }}" alt="BNI" class="h-5 sm:h-5.5 w-auto object-contain">
                            </div>

                            <!-- Mandiri Bill Payment -->
                            <div @click="selectedMethod = 'mandiri'"
                                 :class="selectedMethod === 'mandiri' ? 'border-blue-600 bg-blue-100/50' : 'border-slate-300 hover:border-slate-400 bg-white'"
                                 class="flex items-center justify-between p-3.5 sm:px-4 sm:py-3.5 border cursor-pointer transition-all select-none">
                                <span class="font-semibold text-xs sm:text-[13px] text-slate-900">Mandiri Bill Payment</span>
                                <img src="{{ asset('image/payment-logo/mandiri.png') }}" alt="Mandiri" class="h-5 sm:h-5.5 w-auto object-contain">
                            </div>

                            <!-- BRI Virtual Account -->
                            <div @click="selectedMethod = 'bri'"
                                 :class="selectedMethod === 'bri' ? 'border-blue-600 bg-blue-100/50' : 'border-slate-300 hover:border-slate-400 bg-white'"
                                 class="flex items-center justify-between p-3.5 sm:px-4 sm:py-3.5 border cursor-pointer transition-all select-none">
                                <span class="font-semibold text-xs sm:text-[13px] text-slate-900">BRI (BRIVA)</span>
                                <img src="{{ asset('image/payment-logo/bri.png') }}" alt="BRI" class="h-5 sm:h-5.5 w-auto object-contain">
                            </div>

                            <!-- CIMB Niaga Virtual Account -->
                            <div @click="selectedMethod = 'cimb'"
                                 :class="selectedMethod === 'cimb' ? 'border-blue-600 bg-blue-100/50' : 'border-slate-300 hover:border-slate-400 bg-white'"
                                 class="flex items-center justify-between p-3.5 sm:px-4 sm:py-3.5 border cursor-pointer transition-all select-none">
                                <span class="font-semibold text-xs sm:text-[13px] text-slate-900">CIMB Niaga Virtual Account</span>
                                <img src="{{ asset('image/payment-logo/cimb.png') }}" alt="CIMB" class="h-5 sm:h-5.5 w-auto object-contain">
                            </div>

                            <!-- Permata Virtual Account -->
                            <div @click="selectedMethod = 'permata'"
                                 :class="selectedMethod === 'permata' ? 'border-blue-600 bg-blue-100/50' : 'border-slate-300 hover:border-slate-400 bg-white'"
                                 class="flex items-center justify-between p-3.5 sm:px-4 sm:py-3.5 border cursor-pointer transition-all select-none">
                                <span class="font-semibold text-xs sm:text-[13px] text-slate-900">Permata Bank Virtual Account</span>
                                <img src="{{ asset('image/payment-logo/permata.png') }}" alt="Permata" class="h-5 sm:h-5.5 w-auto object-contain">
                            </div>

                            <!-- BCA Virtual Account (Belum Aktif) -->
                            <div @click="errorMessage = 'Metode BCA Virtual Account sedang menunggu aktivasi oleh Midtrans. Silakan pilih BNI, Mandiri, BRI, CIMB, Permata, atau QRIS.'"
                                 class="flex items-center justify-between p-3.5 sm:px-4 sm:py-3.5 border border-slate-200 bg-slate-50 cursor-pointer opacity-70 transition-all select-none">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-xs sm:text-[13px] text-slate-700 block">BCA Virtual Account</span>
                                    <span class="text-[9px] bg-amber-100 text-amber-800 px-1.5 py-0.5 font-medium">Menunggu Aktivasi</span>
                                </div>
                                <img src="{{ asset('image/payment-logo/bca.png') }}" alt="BCA" class="h-5 sm:h-5.5 w-auto object-contain grayscale opacity-60">
                            </div>
                        </div>
                    </div>

                    <!-- Form Data Pembeli Opsional -->
                    <div class="space-y-2.5 mt-4 pt-4 border-t border-slate-200">
                        <span class="text-xs font-bold text-slate-800 block">Informasi Kontak Pemesan</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div>
                                <label class="text-[10px] text-slate-500 block mb-0.5">Nama Lengkap</label>
                                <input type="text" x-model="customerName" placeholder="Nama Anda" class="w-full px-2.5 py-1.5 border border-slate-300 text-xs bg-white focus:outline-none focus:border-blue-600">
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-500 block mb-0.5">No. WhatsApp</label>
                                <input type="text" x-model="customerPhone" placeholder="08xxxxxxxxxx" class="w-full px-2.5 py-1.5 border border-slate-300 text-xs bg-white focus:outline-none focus:border-blue-600">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: DETAIL PEMBAYARAN KUSTOM (QRIS / VA DISPLAY) -->
                <div x-show="step === 'pay_details'" class="space-y-4">
                    
                    <!-- Header Info Order -->
                    <div class="bg-slate-50 border border-slate-200 p-3 text-center">
                        <span class="text-[10px] text-slate-500 block">Nomor Pesanan</span>
                        <span class="text-sm font-bold text-slate-900 font-mono tracking-wide" x-text="orderNumber"></span>
                        <div class="mt-2 text-center">
                            <span class="text-[10px] text-slate-500 block">Total Pembayaran</span>
                            <span class="text-lg font-bold text-slate-900" x-text="'Rp ' + Number(totalAmount).toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    <!-- TAMPILAN CUSTOM QRIS -->
                    <div x-show="selectedMethod === 'qris'" class="p-4 bg-white border border-slate-300 text-center space-y-3">
                        <div class="flex items-center justify-center gap-1.5 font-bold text-slate-800">
                            <i class="fa-solid fa-qrcode text-blue-600"></i>
                            <span>Scan QRIS untuk Membayar</span>
                        </div>
                        <p class="text-[11px] text-slate-500">Buka BCA Mobile, GoPay, ShopeePay, Dana, OVO, atau aplikasi m-Banking Anda, lalu scan kode QR di bawah ini:</p>
                        
                        <div class="inline-block p-3 bg-white border-2 border-slate-800 shadow-md">
                            <template x-if="qrisUrl">
                                <img :src="qrisUrl" alt="Kode QRIS Pembayaran" class="w-56 h-56 object-contain mx-auto" />
                            </template>
                            <template x-if="!qrisUrl">
                                <div class="w-56 h-56 flex flex-col items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-circle-notch fa-spin text-2xl mb-2 text-blue-600"></i>
                                    <span class="text-[11px]">Memuat QRIS...</span>
                                </div>
                            </template>
                        </div>

                        <div class="text-[11px] text-slate-500 flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-arrows-rotate fa-spin text-blue-600"></i>
                            <span>Menunggu pembayaran Anda...</span>
                        </div>
                    </div>

                    <!-- TAMPILAN CUSTOM VIRTUAL ACCOUNT -->
                    <div x-show="selectedMethod !== 'qris'" class="p-4 bg-white border border-slate-300 space-y-3">
                        <div class="border-b border-slate-200 pb-2">
                            <span class="font-bold text-slate-800" x-text="(selectedMethod === 'mandiri' ? 'Mandiri Bill Payment' : 'Virtual Account ' + bank)"></span>
                        </div>

                        <div>
                            <span class="text-[11px] text-slate-500 block mb-1" x-text="selectedMethod === 'mandiri' ? 'Kode Perusahaan & No Tagihan:' : 'Nomor Virtual Account:'"></span>
                            <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-300">
                                <span class="text-base font-bold font-mono text-slate-900 tracking-wider" x-text="vaNumber"></span>
                                <button type="button" 
                                        @click="copyText(vaNumber)" 
                                        class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-[11px] font-semibold transition-colors flex items-center gap-1">
                                    <i class="fa-solid" :class="copied ? 'fa-check text-emerald-600' : 'fa-copy'"></i>
                                    <span x-text="copied ? 'Tersalin' : 'Salin'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Petunjuk Singkat Transfer -->
                        <div class="text-[11px] text-slate-600 space-y-1 bg-slate-50 p-2.5 border border-slate-200">
                            <span class="font-bold block text-slate-800 mb-1">Cara Pembayaran:</span>
                            <p>1. Buka m-Banking atau ATM bank Anda.</p>
                            <p>2. Pilih menu <strong>Transfer > Virtual Account</strong> (atau <strong>Pembayaran > Bayar Tagihan</strong> untuk Mandiri).</p>
                            <p>3. Masukkan nomor di atas dan konfirmasi pembayaran.</p>
                        </div>

                        <div class="text-[11px] text-slate-500 flex items-center justify-center gap-1.5 pt-1">
                            <i class="fa-solid fa-arrows-rotate fa-spin text-blue-600"></i>
                            <span>Status: Menunggu transfer...</span>
                        </div>
                    </div>

                </div>

                <!-- STEP 3: SUKSES PEMBAYARAN KUSTOM -->
                <div x-show="step === 'success'" class="p-6 text-center space-y-3">
                    <div class="w-14 h-14 bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl mx-auto border border-emerald-300">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Pembayaran Berhasil Diterima!</h3>
                    <p class="text-xs text-slate-600 leading-relaxed max-w-sm mx-auto">
                        Terima kasih atas pesanan Anda. Order Anda sedang diproses oleh tim Dyanaf Creative.
                    </p>
                    <div class="pt-2">
                        <a :href="'{{ route('tracking-order') }}?order_number=' + encodeURIComponent(orderNumber)"
                           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <span>Lacak Status Pesanan</span>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Modal Footer (Buttons) -->
            <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                <!-- Jika step 1 -->
                <template x-if="step === 'choose_method'">
                    <div class="w-full flex items-center justify-end space-x-2.5">
                        <button type="button" 
                                @click="close()" 
                                class="px-4 py-2 border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 text-xs font-medium transition-colors">
                            Batal
                        </button>
                        <button type="button" 
                                @click="processPayment()" 
                                :disabled="loading"
                                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-xs font-bold transition-colors inline-flex items-center gap-2 cursor-pointer">
                            <template x-if="!loading">
                                <span class="inline-flex items-center gap-1.5">
                                    <span>Lanjut Bayar</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                                    <span>Memproses...</span>
                                </span>
                            </template>
                        </button>
                    </div>
                </template>

                <!-- Jika step 2 (pay_details) -->
                <template x-if="step === 'pay_details'">
                    <div class="w-full flex items-center justify-between">
                        <button type="button" 
                                @click="step = 'choose_method'; clearInterval()" 
                                class="text-xs text-slate-600 hover:text-slate-900 font-medium inline-flex items-center gap-1">
                            <i class="fa-solid fa-arrow-left text-[10px]"></i>
                            <span>Ubah Metode</span>
                        </button>
                        <button type="button" 
                                @click="close()" 
                                class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors">
                            Selesai Nanti
                        </button>
                    </div>
                </template>

                <!-- Jika step 3 (success) -->
                <template x-if="step === 'success'">
                    <div class="w-full flex items-center justify-end">
                        <button type="button" 
                                @click="close()" 
                                class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors">
                            Tutup
                        </button>
                    </div>
                </template>
            </div>

        </div>
    </div>
</div>
