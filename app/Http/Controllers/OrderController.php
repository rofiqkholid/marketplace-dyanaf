<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Halaman Publik: Tracking Order Status.
     */
    public function tracking(Request $request)
    {
        $orderNumber = trim($request->query('order_number', ''));
        $order = null;
        $searched = false;

        if (!empty($orderNumber)) {
            $searched = true;
            $order = Order::where('order_number', $orderNumber)->first();
        }

        return view('page.tracking-order', compact('order', 'orderNumber', 'searched'));
    }

    /**
     * Halaman Publik: Download Asset Order.
     */
    public function downloadAsset(Request $request)
    {
        $orderNumber = trim($request->query('order_number', ''));
        $order = null;
        $searched = false;

        if (!empty($orderNumber)) {
            $searched = true;
            $order = Order::with('assets')->where('order_number', $orderNumber)->first();
        }

        return view('page.download-asset', compact('order', 'orderNumber', 'searched'));
    }

    /**
     * Ensure only user with role 'pemilik' can access order management.
     */
    protected function checkRole()
    {
        if (!Auth::check() || Auth::user()->role !== 'pemilik') {
            abort(403, 'Akses Ditolak. Halaman Create / Manage Order hanya dapat diakses oleh Pemilik.');
        }
    }

    /**
     * Halaman Pemilik: Daftar Order & Form Create Order.
     */
    public function adminIndex(Request $request)
    {
        $this->checkRole();

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 50, 100])) {
            $perPage = 10;
        }

        $query = Order::query();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%")
                  ->orWhere('package_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
        $products = Product::where('is_active', true)->orderBy('name', 'asc')->get();
        $brands = Brand::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('page.create-order', compact('orders', 'products', 'brands'));
    }

    /**
     * Store new order by Admin / Pemilik.
     */
    public function store(Request $request)
    {
        $this->checkRole();

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'item_type' => 'required|in:product,brand',
            'product_id' => 'nullable|required_if:item_type,product|exists:products,id',
            'brand_id' => 'nullable|required_if:item_type,brand|exists:brands,id',
            'package_name' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $itemId = $validated['item_type'] === 'brand' ? (int)$validated['brand_id'] : (int)$validated['product_id'];
        
        // Dapatkan nama produk / brand
        if ($validated['item_type'] === 'brand') {
            $brand = Brand::findOrFail($validated['brand_id']);
            $itemName = $brand->name;
        } else {
            $product = Product::findOrFail($validated['product_id']);
            $itemName = $product->name;
        }

        // Generate nomor order unik berformat: SEN100226-1-P29
        $orderNumber = Order::generateOrderNumber($validated['item_type'], $itemId);
        $totalAmount = (float)$validated['price'] * (int)$validated['quantity'];

        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'item_type' => $validated['item_type'],
            'product_id' => $validated['item_type'] === 'product' ? $validated['product_id'] : null,
            'brand_id' => $validated['item_type'] === 'brand' ? $validated['brand_id'] : null,
            'item_name' => $itemName,
            'package_name' => $validated['package_name'] ?? null,
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'total_amount' => $totalAmount,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('orders.admin')->with('success', "Order #{$order->order_number} berhasil dibuat!");
    }

    /**
     * Update order details & status.
     */
    public function update(Request $request, Order $order)
    {
        $this->checkRole();

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $totalAmount = (float)$validated['price'] * (int)$validated['quantity'];

        $order->update([
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'total_amount' => $totalAmount,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('orders.admin')->with('success', "Order #{$order->order_number} berhasil diperbarui!");
    }

    /**
     * Update status order secara cepat (quick patch).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $this->checkRole();

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'order_status' => $order->status,
                'message' => "Status Order #{$order->order_number} berhasil diubah ke {$order->status}!"
            ]);
        }

        return redirect()->route('orders.admin')->with('success', "Status Order #{$order->order_number} berhasil diubah ke {$order->status}!");
    }

    /**
     * Delete order.
     */
    public function destroy(Order $order)
    {
        $this->checkRole();

        $number = $order->order_number;
        $order->delete();

        return redirect()->route('orders.admin')->with('success', "Order #{$number} berhasil dihapus!");
    }
}
