<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class PaymentController extends Controller
{
    /**
     * Set up Midtrans configuration.
     */
    protected function initMidtrans(): void
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Create checkout transaction & get Midtrans Snap Token.
     */
    public function createSnapToken(Request $request, Product $product)
    {
        $this->initMidtrans();

        $validated = $request->validate([
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
            'payment_status' => 'unpaid',
            'notes' => $validated['notes'] ?? null,
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
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

        try {
            $snapToken = Snap::getSnapToken($params);
            $order->update(['snap_token' => $snapToken]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_number' => $order->order_number,
            ]);
        } catch (\Throwable $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage(), [
                'order_id' => $order->order_number,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran ke Midtrans: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Midtrans Webhook Notification.
     */
    public function handleWebhook(Request $request)
    {
        $this->initMidtrans();

        try {
            $notif = new Notification();
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Invalid notification: ' . $e->getMessage()], 400);
        }

        $orderNumber = $notif->order_id;
        $transactionStatus = $notif->transaction_status;
        $paymentType = $notif->payment_type ?? null;
        $fraudStatus = $notif->fraud_status ?? null;

        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->payment_type = $paymentType;

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $order->payment_status = 'challenge';
            } else if ($fraudStatus == 'accept') {
                $order->payment_status = 'paid';
                $order->status = 'processing';
            }
        } else if ($transactionStatus == 'settlement') {
            $order->payment_status = 'paid';
            $order->status = 'processing';
        } else if ($transactionStatus == 'pending') {
            $order->payment_status = 'pending';
        } else if ($transactionStatus == 'deny') {
            $order->payment_status = 'denied';
            $order->status = 'cancelled';
        } else if ($transactionStatus == 'expire') {
            $order->payment_status = 'expired';
            $order->status = 'cancelled';
        } else if ($transactionStatus == 'cancel') {
            $order->payment_status = 'cancelled';
            $order->status = 'cancelled';
        }

        $order->save();

        // Tambah Dyanaf Points otomatis jika pembayaran sukses (settlement / capture accept)
        if ($order->payment_status === 'paid' && empty($order->points_awarded)) {
            $customerEmail = $order->customer_email ?? null;
            $user = $customerEmail ? User::where('email', $customerEmail)->first() : null;

            if ($user) {
                $pointsToAdd = ($order->item_type ?? 'product') === 'brand' ? 3 : 5;
                $user->increment('dyanaf_points', $pointsToAdd);
                $order->points_awarded = true;
                $order->save();

                Log::info("Webhook: Dyanaf Points Awarded +{$pointsToAdd} to {$user->email} for order {$orderNumber}");
            }
        }

        return response()->json(['message' => 'Notification handled']);
    }
}
