<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Notification;
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
            : ($user->name ?? 'Pelanggan Dyanaf');

        $customerEmail = !empty($validated['customer_email'])
            ? $validated['customer_email']
            : ($user->email ?? 'customer@dyanaf.com');

        $customerPhone = !empty($validated['customer_phone'])
            ? $validated['customer_phone']
            : ($user->phone ?? '085881721193');

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

            // Simpan record Order ke database
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'item_type' => 'product',
                'product_id' => $product->id,
                'item_name' => $product->name,
                'price' => $price,
                'quantity' => 1,
                'total_amount' => $price,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_type' => $paymentMethod === 'qris' ? 'qris' : 'bank_transfer',
                'bank' => $bankName,
                'va_number' => $vaNumber,
                'qris_url' => $qrisUrl,
                'midtrans_transaction_id' => $res->transaction_id ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'payment_method' => $paymentMethod,
                'bank' => $bankName,
                'va_number' => $vaNumber,
                'qris_url' => $qrisUrl,
                'gross_amount' => $price,
                'expire_time' => $res->expiry_time ?? null,
            ]);

        } catch (\Throwable $e) {
            Log::error('ModalPayment Charge Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'product_id' => $product->id,
                'method' => $paymentMethod,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran via ' . strtoupper($paymentMethod) . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mengecek status pembayaran secara realtime (polling oleh modal kustom).
     */
    public function checkStatus(Request $request, string $orderNumber)
    {
        $this->initMidtrans();

        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan'], 404);
        }

        // Jika sudah status paid di database, langsung kembalikan
        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => true,
                'payment_status' => 'paid',
                'order_status' => $order->status,
                'order_number' => $order->order_number,
            ]);
        }

        // Cek langsung ke Midtrans Transaction Status
        try {
            $status = Transaction::status($orderNumber);
            $res = (object) $status;
            $transactionStatus = $res->transaction_status ?? null;
            $fraudStatus = $res->fraud_status ?? null;

            if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
                $order->payment_status = 'paid';
                $order->status = 'processing';
                $order->paid_at = now();
                $order->save();
            } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                $order->payment_status = $transactionStatus;
                $order->status = 'cancelled';
                $order->save();
            }

            return response()->json([
                'success' => true,
                'payment_status' => $order->payment_status,
                'order_status' => $order->status,
                'order_number' => $order->order_number,
            ]);
        } catch (\Throwable $e) {
            // Kembalikan status lokal jika gagal query ke midtrans
            return response()->json([
                'success' => true,
                'payment_status' => $order->payment_status,
                'order_status' => $order->status,
                'order_number' => $order->order_number,
            ]);
        }
    }
}
