<!-- Custom Payment UI Modal (Core API) -->
<div id="customPaymentModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="custom-payment-title" role="dialog" aria-modal="true">
    <!-- Backdrop - Transparent Dark -->
    <div id="customPaymentBackdrop" class="fixed inset-0 bg-black/60 transition-opacity duration-300 opacity-0"></div>

    <div class="fixed inset-0 z-10 flex items-center justify-center p-0 sm:p-4">
        <!-- Modal Panel - Full screen mobile, centered desktop -->
        <div id="customPaymentPanel" class="relative w-full h-full sm:h-auto sm:max-h-[90vh] sm:max-w-4xl bg-white border-0 sm:border-[3px] border-slate-900 rounded-none sm:rounded-[24px] shadow-none sm:shadow-[8px_8px_0px_0px_#1a1a1a] transition-all duration-500 ease-out scale-95 opacity-0 flex flex-col overflow-hidden">

            <!-- Header - Minimal -->
            <div class="flex items-center justify-between p-4 sm:p-6 border-b-[2.5px] border-slate-900 bg-[#faf8ef]">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-[#8be4d6] border-2 border-slate-900 shadow-[2px_2px_0px_0px_#1a1a1a] text-slate-900 font-extrabold">
                        <i class="fa-solid fa-credit-card text-base"></i>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold text-slate-900" id="custom-payment-title" data-i18n="payment_step2">Metode Pembayaran</span>
                        <p class="text-xs font-semibold text-slate-600 hidden sm:block" data-i18n="payment_select_method_info">Pilih metode pembayaran favorit Anda.</p>
                    </div>
                </div>
                <button type="button" onclick="closeCustomPaymentModal()" class="w-9 h-9 rounded-xl bg-white border-2 border-slate-900 shadow-[2px_2px_0px_0px_#1a1a1a] text-slate-900 hover:bg-slate-100 flex items-center justify-center font-black cursor-pointer transition-all active:translate-x-[1px] active:translate-y-[1px] active:shadow-none">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Step Indicator -->
            <div class="px-6 py-3.5 bg-[#faf8ef] border-b-[2.5px] border-slate-900">
                <div class="flex items-center justify-center gap-4 sm:gap-8">
                    <!-- Step 1 - Completed -->
                    <div class="flex items-center gap-2">
                        <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-[#8be4d6] border-2 border-slate-900 text-slate-900 text-xs font-black shadow-[1.5px_1.5px_0px_0px_#1a1a1a]">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <span class="text-xs sm:text-sm font-bold text-slate-500" data-i18n="payment_step1">Lengkapi Data</span>
                    </div>

                    <!-- Connector Line -->
                    <div class="w-8 sm:w-16 h-[2.5px] bg-slate-900"></div>

                    <!-- Step 2 - Active -->
                    <div class="flex items-center gap-2">
                        <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-[#ffdd44] border-2 border-slate-900 text-slate-900 text-xs font-black shadow-[1.5px_1.5px_0px_0px_#1a1a1a]">
                            2
                        </div>
                        <span class="text-xs sm:text-sm font-extrabold text-slate-900" data-i18n="payment_step2">Metode Pembayaran</span>
                    </div>
                </div>
            </div>

            <!-- Order Summary - Neo brutalist yellow -->
            <div class="px-4 py-3 sm:px-6 sm:py-4 bg-[#ffdd44] border-b-[2.5px] border-slate-900 text-slate-900">
                <div class="grid grid-cols-2 gap-2 sm:gap-4">
                    <div>
                        <p class="text-[10px] sm:text-xs font-bold text-slate-800 mb-0.5 sm:mb-1" data-i18n="payment_service_label">Layanan</p>
                        <p class="text-sm sm:text-base font-black text-slate-900" id="custom-service-name"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] sm:text-xs font-bold text-slate-800 mb-0.5 sm:mb-1" data-i18n="payment_total_label">Total Pembayaran</p>
                        <p class="text-base sm:text-xl font-black text-slate-900" id="custom-price-display"></p>
                    </div>
                </div>
                <div class="mt-2 sm:mt-3 flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-900">
                    <i class="fa-solid fa-user text-slate-900 text-xs"></i>
                    <span id="custom-customer-name"></span>
                </div>
            </div>

            <!-- Payment Methods - List Style -->
            <div class="flex-1 px-6 py-4 overflow-y-auto">

                <!-- Payment Method Selection -->
                <div id="payment-selection" class="">
                    <p class="text-sm text-gray-500 sm:hidden mb-4">Pilih metode pembayaran favorit Anda</p>

                    <!-- E-Wallets & QRIS Section -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-[#2b3a4b] mb-3 flex items-center gap-2">
                            <i class="fas fa-wallet"></i>
                            E-Wallet & QRIS
                        </h4>


                        <!-- QRIS Dinamis - AKTIF -->
                        <button type="button" onclick="selectQrisPayment()" class="w-full flex items-center justify-between p-4 bg-white border border-gray-200 hover:border-[#2b3a4b] hover:bg-gray-50 transition-all cursor-pointer mb-2">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 flex items-center justify-center border border-gray-200 bg-white p-1">
                                    <img src="{{ asset('image/payment-logo/qris.png') }}" alt="QRIS" class="w-full h-full object-contain" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-qrcode text-[#2b3a4b]\'></i>';">
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-[#2b3a4b]">QRIS</p>
                                    <p class="text-xs text-gray-500">Scan QR dengan semua e-wallet</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                            </div>
                        </button>

                        <!-- GoPay - AKTIF -->
                        <button type="button" onclick="selectGopayPayment()" class="w-full flex items-center justify-between p-4 bg-white border border-gray-200 hover:border-[#2b3a4b] hover:bg-gray-50 transition-all cursor-pointer mb-2">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 flex items-center justify-center border border-gray-200 bg-white p-1">
                                    <img src="{{ asset('image/payment-logo/gopay.png') }}" alt="GoPay" class="w-full h-full object-contain" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-[#00AA13] font-bold text-xs\'>GoPay</span>';">
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-[#2b3a4b]">GoPay</p>
                                    <p class="text-xs text-gray-500">Bayar via GoPay</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                            </div>
                        </button>

                    </div>

                    <!-- Virtual Account Section -->
                    <div>
                        <h4 class="text-sm font-semibold text-[#2b3a4b] mb-3 flex items-center gap-2">
                            <i class="fas fa-university"></i>
                            Virtual Account
                        </h4>


                        <!-- BNI VA -->
                        <button type="button" onclick="selectVaPayment('bni_va')" class="w-full flex items-center justify-between p-4 bg-white border border-gray-200 hover:border-[#2b3a4b] hover:bg-gray-50 transition-all cursor-pointer mb-2">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 flex items-center justify-center border border-gray-200 bg-white p-1">
                                    <img src="{{ asset('image/payment-logo/bni.png') }}" alt="BNI" class="w-full h-full object-contain" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-[#2b3a4b] font-bold text-sm\'>BNI</span>';">
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-[#2b3a4b]">BNI Virtual Account</p>
                                    <p class="text-xs text-gray-500">Transfer Bank BNI</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                            </div>
                        </button>

                        <!-- BRI VA -->
                        <button type="button" onclick="selectVaPayment('bri_va')" class="w-full flex items-center justify-between p-4 bg-white border border-gray-200 hover:border-[#2b3a4b] hover:bg-gray-50 transition-all cursor-pointer mb-2">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 flex items-center justify-center border border-gray-200 bg-white p-1">
                                    <img src="{{ asset('image/payment-logo/bri.png') }}" alt="BRI" class="w-full h-full object-contain" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-[#2b3a4b] font-bold text-sm\'>BRI</span>';">
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-[#2b3a4b]">BRI Virtual Account</p>
                                    <p class="text-xs text-gray-500">Transfer Bank BRI</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                            </div>
                        </button>

                        <!-- Permata VA (Test - Different payment type) -->
                        <button type="button" onclick="selectVaPayment('permata_va')" class="w-full flex items-center justify-between p-4 bg-white border border-gray-200 hover:border-[#2b3a4b] hover:bg-gray-50 transition-all cursor-pointer mb-2">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 flex items-center justify-center border border-gray-200 bg-white p-1">
                                    <img src="{{ asset('image/payment-logo/permata.png') }}" alt="Permata" class="w-full h-full object-contain" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-[#2b3a4b] font-bold text-xs\'>PERMATA</span>';">
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-[#2b3a4b]">Permata Virtual Account</p>
                                    <p class="text-xs text-gray-500">Transfer Bank Permata</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                            </div>
                        </button>

                        <!-- CIMB Niaga VA - AKTIF -->
                        <button type="button" onclick="selectVaPayment('cimb_va')" class="w-full flex items-center justify-between p-4 bg-white border border-gray-200 hover:border-[#2b3a4b] hover:bg-gray-50 transition-all cursor-pointer mb-2">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 flex items-center justify-center border border-gray-200 bg-white p-1">
                                    <img src="{{ asset('image/payment-logo/cimb.png') }}" alt="CIMB" class="w-full h-full object-contain" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-[#2b3a4b] font-bold text-xs\'>CIMB</span>';">
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-[#2b3a4b]">CIMB Niaga Virtual Account</p>
                                    <p class="text-xs text-gray-500">Transfer Bank CIMB Niaga</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                            </div>
                        </button>

                        <!-- Mandiri VA - AKTIF -->
                        <button type="button" onclick="selectVaPayment('mandiri_va')" class="w-full flex items-center justify-between p-4 bg-white border border-gray-200 hover:border-[#2b3a4b] hover:bg-gray-50 transition-all cursor-pointer">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 flex items-center justify-center border border-gray-200 bg-white p-1">
                                    <img src="{{ asset('image/payment-logo/mandiri.png') }}" alt="Mandiri" class="w-full h-full object-contain" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-[#2b3a4b] font-bold text-xs\'>MANDIRI</span>';">
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-[#2b3a4b]">Mandiri Virtual Account</p>
                                    <p class="text-xs text-gray-500">Transfer Bank Mandiri</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                            </div>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Footer - Security Badge -->
            <div class="p-4 bg-gray-50 border-t border-gray-200 text-center shrink-0">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-shield-alt text-green-500 mr-1"></i>
                    Transaksi aman dan terenkripsi oleh Midtrans
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    let customPaymentData = {
        serviceName: '',
        price: 0,
        customerName: '',
        phone: '',
        orderId: null,
        selectedMethod: null
    };
    let customScrollPosition = 0;

    function showCustomPaymentModal(serviceName, price, customerName, phone, orderId = null) {
        console.log('showCustomPaymentModal called with:', {
            serviceName,
            price,
            customerName,
            phone,
            orderId
        });

        customPaymentData = {
            serviceName: serviceName,
            price: price,
            customerName: customerName,
            phone: phone,
            orderId: orderId,
            selectedMethod: null,
            paymentSelected: false
        };

        console.log('customPaymentData set:', customPaymentData);

        // Populate modal
        document.getElementById('custom-service-name').textContent = serviceName;
        document.getElementById('custom-price-display').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
        document.getElementById('custom-customer-name').textContent = customerName;

        // Show modal
        const modal = document.getElementById('customPaymentModal');
        const backdrop = document.getElementById('customPaymentBackdrop');
        const panel = document.getElementById('customPaymentPanel');

        modal.classList.remove('hidden');

        // Lock body scroll safely
        document.documentElement.classList.add('overflow-hidden');
        document.body.classList.add('overflow-hidden');
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');

            // No Snap embed - using Core API now
            // Payment methods will trigger their own modals (e.g., QRIS modal)
        }, 10);
    }

    // Helper function to select QRIS payment
    function selectQrisPayment() {
        // Save data locally before closing modal (orderId gets reset in closeCustomPaymentModal)
        const savedOrderId = customPaymentData.orderId;
        const savedServiceName = customPaymentData.serviceName;
        const savedPrice = customPaymentData.price;
        const savedCustomerName = customPaymentData.customerName;
        const savedPhone = customPaymentData.phone;

        customPaymentData.paymentSelected = true;
        closeCustomPaymentModal(true); // skipCancel = true
        setTimeout(() => {
            showQrisPayment(savedServiceName, savedPrice, savedCustomerName, savedPhone, savedOrderId);
        }, 300);
    }

    // Helper function to select VA payment
    function selectVaPayment(bankMethod) {
        console.log('selectVaPayment called with bankMethod:', bankMethod);
        console.log('customPaymentData.orderId:', customPaymentData.orderId);

        // Save data locally before closing modal (orderId gets reset in closeCustomPaymentModal)
        const savedOrderId = customPaymentData.orderId;
        const savedServiceName = customPaymentData.serviceName;
        const savedPrice = customPaymentData.price;
        const savedCustomerName = customPaymentData.customerName;
        const savedPhone = customPaymentData.phone;

        customPaymentData.paymentSelected = true;
        closeCustomPaymentModal(true); // skipCancel = true
        setTimeout(() => {
            console.log('Calling showVaPayment with savedOrderId:', savedOrderId);
            showVaPayment(bankMethod, savedServiceName, savedPrice, savedCustomerName, savedPhone, savedOrderId);
        }, 300);
    }

    // Helper function to select GoPay payment
    function selectGopayPayment() {
        // Save data locally before closing modal (orderId gets reset in closeCustomPaymentModal)
        const savedOrderId = customPaymentData.orderId;
        const savedServiceName = customPaymentData.serviceName;
        const savedPrice = customPaymentData.price;
        const savedCustomerName = customPaymentData.customerName;
        const savedPhone = customPaymentData.phone;

        customPaymentData.paymentSelected = true;
        closeCustomPaymentModal(true); // skipCancel = true
        setTimeout(() => {
            showGopayPayment(savedServiceName, savedPrice, savedCustomerName, savedPhone, savedOrderId);
        }, 300);
    }

    function closeCustomPaymentModal(skipCancel = false) {
        console.log('closeCustomPaymentModal called', {
            skipCancel: skipCancel,
            orderId: customPaymentData.orderId,
            paymentSelected: customPaymentData.paymentSelected
        });

        // Cancel transaction if exists and payment not selected
        if (!skipCancel && customPaymentData.orderId && !customPaymentData.paymentSelected) {
            console.log('Calling cancel API for order:', customPaymentData.orderId);
            fetch('{{ route("api.payment.cancel") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        order_id: customPaymentData.orderId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Cancel response:', data);
                    if (typeof showToast === 'function') {
                        showToast('Pembayaran dibatalkan', 'info');
                    }
                })
                .catch(err => {
                    console.error('Cancel error:', err);
                });
        } else if (!skipCancel && !customPaymentData.paymentSelected) {
            // No orderId but still show toast if modal closed without payment
            if (typeof showToast === 'function') {
                showToast('Pembayaran dibatalkan', 'info');
            }
        }

        const modal = document.getElementById('customPaymentModal');
        const backdrop = document.getElementById('customPaymentBackdrop');
        const panel = document.getElementById('customPaymentPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('scale-100', 'opacity-100');
        panel.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');

            // Unlock body scroll cleanly
            document.documentElement.classList.remove('overflow-hidden');
            document.body.classList.remove('overflow-hidden');
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';
            document.body.style.top = '';
            document.body.style.left = '';
            document.body.style.right = '';
            document.body.style.width = '';

            // Reset orderId after closing
            customPaymentData.orderId = null;
            customPaymentData.paymentSelected = false;
        }, 300);
    }

    function handleCustomPaymentSuccess(orderId) {
        fetch('{{ route("api.payment.success") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                order_id: orderId
            })
        }).then(() => {
            if (typeof showToast === 'function') {
                showToast('Pembayaran Berhasil! Mengarahkan ke WhatsApp...', 'success');
            }

            const message = `Saya sudah melakukan pembayaran, berikut detail pesanan saya:

*Order ID:* ${orderId}
*Nama:* ${customPaymentData.customerName}
*Layanan:* ${customPaymentData.serviceName}
*Total:* Rp ${new Intl.NumberFormat('id-ID').format(customPaymentData.price)}

Mohon segera diproses. Terima kasih!`;

            const waNumber = '6285881721193';
            const waUrl = `https://wa.me/${waNumber}?text=${encodeURIComponent(message)}`;
            setTimeout(() => {
                window.location.href = waUrl;
            }, 1500);
        });
    }

    function handleCustomPaymentCancel(orderId, message) {
        fetch('{{ route("api.payment.cancel") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                order_id: orderId
            })
        }).then(() => {
            if (typeof showPageLoader === 'function') {
                showPageLoader();
            }
            if (typeof showToast === 'function') {
                showToast(message, 'warning', true);
            }
        });
    }
</script>