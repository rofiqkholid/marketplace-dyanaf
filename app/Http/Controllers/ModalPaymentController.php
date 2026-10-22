<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPaidNotification;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;

class ModalPaymentController extends Controller
{
    /**
     * Inisialisasi konfigurasi Midtrans Core API.
     */
    protected function initMidtrans(): void
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Memproses checkout kustom untuk metode Bank Transfer (VA) atau QRIS (GoPay/ShopeePay/Semua e-wallet).
     */
    public function charge(Request $request, Product $product)
    {
        $this->initMidtrans();

        $validated = $request->validate([
            'payment_method' => 'required|in:bca,bni,bri,cimb,permata,mandiri,qris',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $customerName = !empty($validated['customer_name']) 
            ? $validated['customer_name'] 
            : ($user ? $user->name : 'Pelanggan Dyanaf');

        $customerEmail = !empty($validated['customer_email']) 
            ? $validated['customer_email'] 
            : ($user ? $user->email : 'customer@dyanaf.com');

        $customerPhone = !empty($validated['customer_phone']) 
            ? $validated['customer_phone'] 
            : ($user && !empty($user->phone) ? $user->phone : '085881721193');

        $orderNumber = Order::generateOrderNumber('product', $product->id);
        $price = (float) $product->price;
        $paymentMethod = $validated['payment_method'];

        // Siapkan parameter Midtrans Core API
        $params = [
            'transaction_details' => [
                'order_id' => $orderNumber,
                'gross_amount' => (int) $price,
            ],
            'customer_details' => [
                'first_name' => $customerName,
                'email' => $customerEmail,
                'phone' => $customerPhone,
            ],
            'item_details' => [
                [
                    'id' => 'PRD-' . $product->id,
                    'price' => (int) $price,
                    'quantity' => 1,
                    'name' => mb_strimwidth($product->name, 0, 45, '...'),
                ]
            ],
        ];

        // Format charge sesuai metode pembayaran yang dipilih
        if ($paymentMethod === 'qris') {
            $params['payment_type'] = 'qris';
            $params['qris'] = [
                'acquirer' => 'gopay',
            ];
        } elseif ($paymentMethod === 'permata') {
            $params['payment_type'] = 'permata';
        } elseif ($paymentMethod === 'mandiri') {
            $params['payment_type'] = 'echannel';
            $params['echannel'] = [
                'bill_info1' => 'Payment For:',
                'bill_info2' => mb_strimwidth($product->name, 0, 25, ''),
            ];
        } else {
            // bca, bni, bri, cimb
            $params['payment_type'] = 'bank_transfer';
            $params['bank_transfer'] = [
                'bank' => $paymentMethod,
            ];
        }

        try {
            $response = CoreApi::charge($params);
            $res = (object) $response;

            $vaNumber = null;
            $bankName = $paymentMethod !== 'qris' ? strtoupper($paymentMethod) : null;
            $qrisUrl = null;

            if ($paymentMethod === 'permata') {
                $vaNumber = $res->permata_va_number ?? null;
            } elseif ($paymentMethod === 'mandiri') {
                $billKey = $res->bill_key ?? '';
                $billerCode = $res->biller_code ?? '';
                $vaNumber = $billerCode . ' - ' . $billKey;
            } elseif (isset($res->va_numbers) && is_array($res->va_numbers) && count($res->va_numbers) > 0) {
                $vaNumber = $res->va_numbers[0]->va_number ?? null;
                $bankName = strtoupper($res->va_numbers[0]->bank ?? $paymentMethod);
            }

            // QRIS QRCode URL
            if ($paymentMethod === 'qris') {
                if (!empty($res->actions)) {
                    foreach ($res->actions as $action) {
                        if (($action->name ?? '') === 'generate-qr-code') {
                            $qrisUrl = $action->url;
                            break;
                        }
                    }
                }
                // Fallback QRIS URL via API jika tidak ada action eksplisit
                if (!$qrisUrl && !empty($res->transaction_id)) {
                    $qrisUrl = "https://api.midtrans.com/v2/qris/{$res->transaction_id}/qr-code";
                }
            }

            // Simpan HANYA ke tabel payment_transactions (TIDAK MASUK ke tabel orders pengerjaan produk saat pending)
            $trx = PaymentTransaction::create([
                'order_number' => $orderNumber,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'product_id' => $product->id,
                'item_name' => $product->name,
                'amount' => $price,
                'payment_method' => $paymentMethod,
                'bank' => $bankName,
                'va_number' => $vaNumber,
                'qris_url' => $qrisUrl,
                'midtrans_transaction_id' => $res->transaction_id ?? null,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'order_number' => $trx->order_number,
                'payment_method' => $paymentMethod,
                'bank' => $bankName,
                'va_number' => $vaNumber,
                'qris_url' => $qrisUrl,
                'gross_amount' => $price,
                'expire_time' => $res->expiry_time ?? null,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran via ' . strtoupper($paymentMethod) . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Memproses checkout kustom paket Brand untuk metode Bank Transfer (VA) atau QRIS.
     */
    public function chargeBrand(Request $request, Brand $brand)
    {
        $this->initMidtrans();

        $validated = $request->validate([
            'package_title' => 'required|string|max:255',
            'package_price' => 'required|numeric|min:1000',
            'payment_method' => 'required|in:bca,bni,bri,cimb,permata,mandiri,qris',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $customerName = !empty($validated['customer_name']) 
            ? $validated['customer_name'] 
            : ($user ? $user->name : 'Pelanggan Dyanaf');

        $customerEmail = !empty($validated['customer_email']) 
            ? $validated['customer_email'] 
            : ($user ? $user->email : 'customer@dyanaf.com');

        $customerPhone = !empty($validated['customer_phone']) 
            ? $validated['customer_phone'] 
            : ($user && !empty($user->phone) ? $user->phone : '085881721193');

        $orderNumber = Order::generateOrderNumber('brand', $brand->id);
        $price = (float) $validated['package_price'];
        $paymentMethod = $validated['payment_method'];
        $packageName = $validated['package_title'];

        $params = [
            'transaction_details' => [
                'order_id' => $orderNumber,
                'gross_amount' => (int) $price,
            ],
            'customer_details' => [
                'first_name' => $customerName,
                'email' => $customerEmail,
                'phone' => $customerPhone,
            ],
            'item_details' => [
                [
                    'id' => 'BRD-' . $brand->id,
                    'price' => (int) $price,
                    'quantity' => 1,
                    'name' => mb_strimwidth($brand->name . ' - ' . $packageName, 0, 45, '...'),
                ]
            ],
        ];

        if ($paymentMethod === 'qris') {
            $params['payment_type'] = 'qris';
            $params['qris'] = ['acquirer' => 'gopay'];
        } elseif ($paymentMethod === 'permata') {
            $params['payment_type'] = 'permata';
        } elseif ($paymentMethod === 'mandiri') {
            $params['payment_type'] = 'echannel';
            $params['echannel'] = [
                'bill_info1' => 'Payment For:',
                'bill_info2' => mb_strimwidth($brand->name, 0, 25, ''),
            ];
        } else {
            $params['payment_type'] = 'bank_transfer';
            $params['bank_transfer'] = ['bank' => $paymentMethod];
        }

        try {
            $response = CoreApi::charge($params);
            $res = (object) $response;

            $vaNumber = null;
            $bankName = strtoupper($paymentMethod);
            $qrisUrl = null;

            if ($paymentMethod === 'permata') {
                $vaNumber = $res->permata_va_number ?? null;
            } elseif ($paymentMethod === 'mandiri') {
                $billKey = $res->bill_key ?? '';
                $billerCode = $res->biller_code ?? '';
                $vaNumber = $billerCode . ' - ' . $billKey;
            } elseif (isset($res->va_numbers) && is_array($res->va_numbers) && count($res->va_numbers) > 0) {
                $vaNumber = $res->va_numbers[0]->va_number ?? null;
                $bankName = strtoupper($res->va_numbers[0]->bank ?? $paymentMethod);
            }

            if ($paymentMethod === 'qris') {
                if (!empty($res->actions)) {
                    foreach ($res->actions as $action) {
                        if (($action->name ?? '') === 'generate-qr-code') {
                            $qrisUrl = $action->url;
                            break;
                        }
                    }
                }
                if (!$qrisUrl && !empty($res->transaction_id)) {
                    $qrisUrl = "https://api.midtrans.com/v2/qris/{$res->transaction_id}/qr-code";
                }
            }

            $trx = PaymentTransaction::create([
                'order_number' => $orderNumber,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'item_type' => 'brand',
                'brand_id' => $brand->id,
                'item_name' => $brand->name,
                'package_name' => $packageName,
                'amount' => $price,
                'payment_method' => $paymentMethod,
                'bank' => $bankName,
                'va_number' => $vaNumber,
                'qris_url' => $qrisUrl,
                'midtrans_transaction_id' => $res->transaction_id ?? null,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'order_number' => $trx->order_number,
                'payment_method' => $paymentMethod,
                'bank' => $bankName,
                'va_number' => $vaNumber,
                'qris_url' => $qrisUrl,
                'gross_amount' => $price,
                'expire_time' => $res->expiry_time ?? null,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran via ' . strtoupper($paymentMethod) . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mengecek status pembayaran secara realtime (polling oleh modal kustom).
     * Jika sukses (settlement/capture), baru simpan/buat order ke tabel orders pengerjaan produk/brand.
     */
    public function checkStatus(Request $request, string $orderNumber)
    {
        $this->initMidtrans();

        $trx = PaymentTransaction::where('order_number', $orderNumber)->first();
        $order = Order::where('order_number', $orderNumber)->first();

        // Jika order sudah terbentuk di tabel orders dan sudah lunas
        if ($order && $order->payment_status === 'paid') {
            return response()->json([
                'success' => true,
                'payment_status' => 'paid',
                'order_status' => $order->status,
                'order_number' => $order->order_number,
            ]);
        }

        // Cek langsung ke status transaksi Midtrans
        try {
            $status = Transaction::status($orderNumber);
            $res = (object) $status;
            $transactionStatus = $res->transaction_status ?? null;
            $fraudStatus = $res->fraud_status ?? null;

            $isPaid = ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept'));

            if ($isPaid) {
                // Update status di payment_transactions
                if ($trx) {
                    $trx->status = 'paid';
                    $trx->paid_at = now();
                    $trx->save();
                }

                // BUAT ORDER BARU DI TABEL ORDERS HANYA JIKA SUDAH SUKSES BAYAR
                if (!$order && $trx) {
                    $order = Order::create([
                        'order_number' => $trx->order_number,
                        'customer_name' => $trx->customer_name,
                        'customer_email' => $trx->customer_email,
                        'customer_phone' => $trx->customer_phone,
                        'item_type' => $trx->item_type ?? 'product',
                        'product_id' => $trx->product_id,
                        'brand_id' => $trx->brand_id,
                        'item_name' => $trx->item_name,
                        'package_name' => $trx->package_name,
                        'price' => $trx->amount,
                        'quantity' => 1,
                        'total_amount' => $trx->amount,
                        'status' => 'processing', // Langsung masuk antrian pengerjaan
                        'payment_status' => 'paid',
                        'payment_type' => $trx->payment_method === 'qris' ? 'qris' : 'bank_transfer',
                        'bank' => $trx->bank,
                        'va_number' => $trx->va_number,
                        'qris_url' => $trx->qris_url,
                        'midtrans_transaction_id' => $trx->midtrans_transaction_id,
                        'notes' => $trx->notes,
                        'paid_at' => now(),
                    ]);

                    $trx->order_id = $order->id;
                    $trx->save();
                } elseif ($order) {
                    $order->payment_status = 'paid';
                    $order->status = 'processing';
                    $order->paid_at = now();
                    $order->save();
                }

                // TAMBAH OTOMATIS DYANAF POINT JIKA USER TERDAFTAR (5 poin produk, 3 poin brand)
                $targetOrder = $order ?: $trx;
                if ($targetOrder && empty($targetOrder->points_awarded)) {
                    $customerEmail = $targetOrder->customer_email ?? null;
                    $user = $customerEmail ? User::where('email', $customerEmail)->first() : null;

                    // Jika belum ketemu dan ada user yang sedang login saat transaksi
                    if (!$user && Auth::check()) {
                        $user = Auth::user();
                    }

                    if ($user) {
                        $pointsToAdd = ($targetOrder->item_type ?? 'product') === 'brand' ? 3 : 5;
                        $user->increment('dyanaf_points', $pointsToAdd);

                        if ($order) {
                            $order->points_awarded = true;
                            $order->save();
                        }
                        if ($trx) {
                            $trx->points_awarded = true;
                            $trx->save();
                        }
                    }
                }

                // KIRIM EMAIL KONFIRMASI PEMBAYARAN JIKA USER MENYERTAKAN EMAIL
                if ($order && !empty($order->customer_email) && empty($order->email_sent)) {
                    // Hanya kirim jika email valid dan bukan default dummy
                    if (filter_var($order->customer_email, FILTER_VALIDATE_EMAIL) && $order->customer_email !== 'customer@dyanaf.com') {
                        try {
                            Mail::to($order->customer_email)->send(new OrderPaidNotification($order));
                            $order->email_sent = true;
                            $order->save();
                        } catch (\Throwable $mailErr) {
                            Log::error("Gagal mengirim email notifikasi order #{$order->order_number}: " . $mailErr->getMessage());
                        }
                    }
                }

                return response()->json([
                    'success' => true,
                    'payment_status' => 'paid',
                    'order_status' => $order ? $order->status : 'processing',
                    'order_number' => $orderNumber,
                ]);
            }

            if (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                if ($trx) {
                    $trx->status = $transactionStatus;
                    $trx->save();
                }
                if ($order) {
                    $order->payment_status = $transactionStatus;
                    $order->status = 'cancelled';
                    $order->save();
                }

                return response()->json([
                    'success' => true,
                    'payment_status' => $transactionStatus,
                    'order_status' => 'cancelled',
                    'order_number' => $orderNumber,
                ]);
            }

            // Masih pending
            return response()->json([
                'success' => true,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'order_number' => $orderNumber,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => true,
                'payment_status' => $trx ? $trx->status : ($order->payment_status ?? 'pending'),
                'order_status' => $order ? $order->status : 'pending',
                'order_number' => $orderNumber,
            ]);
        }
    }
}

