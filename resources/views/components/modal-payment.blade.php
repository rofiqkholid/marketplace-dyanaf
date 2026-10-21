@props(['product'])

<div x-data="{
    openPaymentModal: false,
    step: 'choose_method', // 'choose_method', 'pay_details', 'success'
    selectedMethod: 'qris',
    customerName: '{{ addslashes(Auth::check() ? Auth::user()->name : 'Pelanggan Dyanaf') }}',
    customerEmail: '{{ addslashes(Auth::check() ? Auth::user()->email : 'customer@dyanaf.com') }}',
    customerPhone: '{{ addslashes(Auth::check() ? (Auth::user()->phone ?? '') : '085881721193') }}',
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

    <!-- Backdrop Overlay (Full di mobile, centered dialog di desktop) -->
    <div x-show="openPaymentModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 flex items-center justify-center p-0 sm:p-4 z-[9999]"
         style="display: none;">

        <!-- Modal Dialog Container -->
        <div x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="w-full h-full sm:h-auto sm:max-h-[92vh] sm:max-w-3xl bg-white border-0 sm:border border-slate-300 rounded-none shadow-none sm:shadow-2xl overflow-hidden flex flex-col">

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

            <!-- Order Summary Banner -->
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 text-slate-900">
                <div class="grid grid-cols-2 gap-2 sm:gap-4 items-center">
                    <div>
                        <p class="text-[10px] sm:text-[11px] font-bold text-slate-500 mb-0.5">Layanan / Produk</p>
                        <p class="text-sm sm:text-base font-bold text-slate-900 line-clamp-1">{{ $product->name }}</p>
                        <span class="text-[11px] text-slate-500 block">{{ $product->category ?? 'Layanan Digital' }}</span>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] sm:text-[11px] font-bold text-slate-500 mb-0.5">Total Pembayaran</p>
                        <p class="text-base sm:text-xl font-bold text-blue-600" x-text="orderNumber ? ('Rp ' + Number(totalAmount).toLocaleString('id-ID')) : 'Rp {{ number_format($product->price, 0, ',', '.') }}'"></p>
                    </div>
                </div>
                <div class="mt-2.5 pt-2 border-t border-slate-200 flex items-center justify-between text-xs font-medium text-slate-600">
                    <div class="flex items-center gap-1.5">
                        <i class="fa-solid fa-user text-slate-400 text-[11px]"></i>
                        <span class="font-semibold text-slate-800">{{ Auth::check() ? Auth::user()->name : 'Pelanggan Dyanaf' }}</span>
                    </div>
                    <template x-if="orderNumber">
                        <div class="text-right">
                            <span class="text-[10px] text-slate-500">Nomor Pesanan: </span>
                            <span class="font-bold font-mono text-slate-900" x-text="orderNumber"></span>
                        </div>
                    </template>
                </div>
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

                <!-- STEP 1: PILIH METODE PEMBAYARAN -->
                <div x-show="step === 'choose_method'" class="space-y-5">
                    
                    <!-- Section E-Wallet & QRIS -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 mb-2.5 flex items-center gap-2">
                            <i class="fa-solid fa-wallet text-slate-700"></i>
                            <span>E-Wallet & QRIS</span>
                        </h4>

                        <!-- QRIS Option -->
                        <div @click="selectedMethod = 'qris'"
                             :class="selectedMethod === 'qris' ? 'border-blue-600 bg-blue-50/70' : 'border-gray-200 hover:bg-gray-50 bg-white'"
                             class="flex items-center justify-between p-3.5 border rounded-none cursor-pointer transition-all select-none">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 flex items-center justify-center border border-gray-200 bg-white p-1 shrink-0">
                                    <img src="{{ asset('image/payment-logo/qris.png') }}" alt="QRIS" class="w-full h-full object-contain">
                                </div>
                                <div>
                                    <p class="text-xs sm:text-sm font-bold text-slate-900">QRIS (Semua Pembayaran)</p>
                                    <p class="text-[11px] text-slate-500">Scan QR dengan GoPay, ShopeePay, Dana, OVO, LinkAja & m-Banking</p>
                                </div>
                            </div>
                            <div class="flex items-center shrink-0 pl-2">
                                <i class="fa-solid fa-chevron-right text-xs" :class="selectedMethod === 'qris' ? 'text-blue-600' : 'text-gray-400'"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Section Virtual Account -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 mb-2.5 flex items-center gap-2">
                            <i class="fa-solid fa-building-columns text-slate-700"></i>
                            <span>Virtual Account (Transfer Bank)</span>
                        </h4>

                        <!-- Grid 2 Kolom (Kiri Kanan) untuk Virtual Account -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- BNI VA -->
                            <div @click="selectedMethod = 'bni'"
                                 :class="selectedMethod === 'bni' ? 'border-blue-600 bg-blue-50/70' : 'border-gray-200 hover:bg-gray-50 bg-white'"
                                 class="flex items-center justify-between p-3 border rounded-none cursor-pointer transition-all select-none">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 flex items-center justify-center border border-gray-200 bg-white p-1 shrink-0">
                                        <img src="{{ asset('image/payment-logo/bni.png') }}" alt="BNI" class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">BNI Virtual Account</p>
                                        <p class="text-[10px] text-slate-500">Transfer Bank BNI</p>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-xs shrink-0" :class="selectedMethod === 'bni' ? 'text-blue-600' : 'text-gray-400'"></i>
                            </div>

                            <!-- BRI VA -->
                            <div @click="selectedMethod = 'bri'"
                                 :class="selectedMethod === 'bri' ? 'border-blue-600 bg-blue-50/70' : 'border-gray-200 hover:bg-gray-50 bg-white'"
                                 class="flex items-center justify-between p-3 border rounded-none cursor-pointer transition-all select-none">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 flex items-center justify-center border border-gray-200 bg-white p-1 shrink-0">
                                        <img src="{{ asset('image/payment-logo/bri.png') }}" alt="BRI" class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">BRI (BRIVA)</p>
                                        <p class="text-[10px] text-slate-500">Transfer Bank BRI</p>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-xs shrink-0" :class="selectedMethod === 'bri' ? 'text-blue-600' : 'text-gray-400'"></i>
                            </div>

                            <!-- Mandiri Bill Payment -->
                            <div @click="selectedMethod = 'mandiri'"
                                 :class="selectedMethod === 'mandiri' ? 'border-blue-600 bg-blue-50/70' : 'border-gray-200 hover:bg-gray-50 bg-white'"
                                 class="flex items-center justify-between p-3 border rounded-none cursor-pointer transition-all select-none">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 flex items-center justify-center border border-gray-200 bg-white p-1 shrink-0">
                                        <img src="{{ asset('image/payment-logo/mandiri.png') }}" alt="Mandiri" class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">Mandiri Bill Payment</p>
                                        <p class="text-[10px] text-slate-500">Transfer Bank Mandiri</p>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-xs shrink-0" :class="selectedMethod === 'mandiri' ? 'text-blue-600' : 'text-gray-400'"></i>
                            </div>

                            <!-- CIMB Niaga VA -->
                            <div @click="selectedMethod = 'cimb'"
                                 :class="selectedMethod === 'cimb' ? 'border-blue-600 bg-blue-50/70' : 'border-gray-200 hover:bg-gray-50 bg-white'"
                                 class="flex items-center justify-between p-3 border rounded-none cursor-pointer transition-all select-none">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 flex items-center justify-center border border-gray-200 bg-white p-1 shrink-0">
                                        <img src="{{ asset('image/payment-logo/cimb.png') }}" alt="CIMB" class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">CIMB Niaga VA</p>
                                        <p class="text-[10px] text-slate-500">Transfer Bank CIMB Niaga</p>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-xs shrink-0" :class="selectedMethod === 'cimb' ? 'text-blue-600' : 'text-gray-400'"></i>
                            </div>

                            <!-- Permata VA -->
                            <div @click="selectedMethod = 'permata'"
                                 :class="selectedMethod === 'permata' ? 'border-blue-600 bg-blue-50/70' : 'border-gray-200 hover:bg-gray-50 bg-white'"
                                 class="flex items-center justify-between p-3 border rounded-none cursor-pointer transition-all select-none">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 flex items-center justify-center border border-gray-200 bg-white p-1 shrink-0">
                                        <img src="{{ asset('image/payment-logo/permata.png') }}" alt="Permata" class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">Permata VA</p>
                                        <p class="text-[10px] text-slate-500">Transfer Bank Permata</p>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-xs shrink-0" :class="selectedMethod === 'permata' ? 'text-blue-600' : 'text-gray-400'"></i>
                            </div>

                            <!-- BCA VA -->
                            <div @click="selectedMethod = 'bca'"
                                 :class="selectedMethod === 'bca' ? 'border-blue-600 bg-blue-50/70' : 'border-gray-200 hover:bg-gray-50 bg-white'"
                                 class="flex items-center justify-between p-3 border rounded-none cursor-pointer transition-all select-none">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 flex items-center justify-center border border-gray-200 bg-white p-1 shrink-0">
                                        <img src="{{ asset('image/payment-logo/bca.png') }}" alt="BCA" class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">BCA Virtual Account</p>
                                        <p class="text-[10px] text-slate-500">Transfer Bank BCA</p>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-xs shrink-0" :class="selectedMethod === 'bca' ? 'text-blue-600' : 'text-gray-400'"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: DETAIL PEMBAYARAN KUSTOM (QRIS / VA DISPLAY) -->
                <div x-show="step === 'pay_details'" class="space-y-4">

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
                <div x-show="step === 'success'" class="py-8 px-6 text-center space-y-4">
                    <!-- Icon Centang Bulat (Rounded-Full) -->
                    <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                        <div class="absolute inset-0 rounded-full bg-emerald-100 animate-ping opacity-25"></div>
                        <div class="w-20 h-20 rounded-full bg-emerald-50 border-2 border-emerald-400 text-emerald-600 flex items-center justify-center text-3xl shadow-sm">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 font-['Outfit']">Pembayaran Berhasil Diterima!</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                            Terima kasih atas pesanan Anda. Sistem kami telah memverifikasi pembayaran secara otomatis.
                        </p>
                    </div>

                    <!-- Ringkasan Info Order -->
                    <div class="p-3.5 bg-slate-50 border border-slate-200 text-left max-w-sm mx-auto space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Nomor Pesanan</span>
                            <span class="font-bold font-mono text-slate-800" x-text="orderNumber"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs pt-1.5 border-t border-slate-200/80">
                            <span class="text-slate-500">Total Terbayar</span>
                            <span class="font-bold text-slate-900" x-text="'Rp ' + Number(totalAmount).toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    <!-- Tombol Lacak Status Pesanan -->
                    <div class="pt-2">
                        <a :href="'{{ route('tracking-order') }}?order_number=' + encodeURIComponent(orderNumber)"
                           class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors shadow-sm">
                            <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
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
                                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-xs font-bold transition-colors inline-flex items-center gap-2 cursor-pointer">
                            <template x-if="!loading">
                                <span class="inline-flex items-center gap-2">
                                    <span>Lanjut Bayar</span>
                                    <i class="fa-solid fa-arrow-right text-sm"></i>
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="inline-flex items-center gap-2">
                                    <i class="fa-solid fa-circle-notch fa-spin text-sm"></i>
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
                                class="px-4 py-2 border border-slate-300 bg-white hover:bg-slate-100 hover:text-blue-600 text-slate-800 text-xs font-semibold transition-colors">
                            Selesai Nanti
                        </button>
                    </div>
                </template>

                <!-- Jika step 3 (success) -->
                <template x-if="step === 'success'">
                    <div class="w-full flex items-center justify-end">
                        <button type="button" 
                                @click="close()" 
                                class="group inline-flex items-center justify-center space-x-2 px-5 py-2 bg-white text-slate-800 border border-slate-300 rounded-none text-xs font-semibold hover:border-slate-400 hover:text-blue-600 transition-colors">
                            <span>Tutup</span>
                            <i class="fa-solid fa-arrow-right text-[10px] transition-transform duration-200 group-hover:translate-x-1"></i>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Footer Security Badge (Dari custom-payment-ui) -->
            <div class="px-5 py-2.5 bg-slate-50/80 border-t border-slate-200 text-center shrink-0">
                <p class="text-[11px] text-slate-500 font-medium flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                    <span>Transaksi aman & terenkripsi otomatis oleh Midtrans Payment Gateway</span>
                </p>
            </div>

        </div>
    </div>
</div>
