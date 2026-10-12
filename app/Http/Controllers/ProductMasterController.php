<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductMasterController extends Controller
{
    /**
     * Ensure only user with role 'pemilik' can access product management.
     */
    protected function checkRole()
    {
        if (!Auth::check() || Auth::user()->role !== 'pemilik') {
            abort(403, 'Akses Ditolak. Halaman Management Produk hanya dapat diakses oleh Pemilik.');
        }
    }

    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $this->checkRole();

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 50, 100])) {
            $perPage = 10;
        }

        $query = Product::query();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
        $categories = [
            'Daily Assignments Assistance',
            'Graphic Design',
            'Software',
            'AI Tools',
            'Games',
            'Accounts',
            'Gift & Bouquet',
        ];

        return view('page.products-master', compact('products', 'categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $this->checkRole();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;
        $validated['is_trending'] = $request->has('is_trending') ? (bool)$request->is_trending : false;

        Product::create($validated);

        return redirect()->route('products-master.index')->with('success', 'Produk baru berhasil ditambahkan!');
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $this->checkRole();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : false;
        $validated['is_trending'] = $request->has('is_trending') ? (bool)$request->is_trending : false;

        $product->update($validated);

        return redirect()->route('products-master.index')->with('success', 'Data produk berhasil diperbarui!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $this->checkRole();

        $product->delete();

        return redirect()->route('products-master.index')->with('success', 'Produk berhasil dihapus!');
    }

    /**
     * Toggle active status of the specified product.
     */
    public function toggleActive(Request $request, Product $product)
    {
        $this->checkRole();

        $product->update(['is_active' => !$product->is_active]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'is_active' => $product->is_active,
                'message' => 'Status keaktifan produk ' . $product->name . ' berhasil diubah!'
            ]);
        }

        return redirect()->route('products-master.index')->with('success', 'Status keaktifan produk berhasil diubah!');
    }

    /**
     * Toggle trending status of the specified product.
     */
    public function toggleTrending(Request $request, Product $product)
    {
        $this->checkRole();

        $product->update(['is_trending' => !$product->is_trending]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'is_trending' => $product->is_trending,
                'message' => 'Status trending produk ' . $product->name . ' berhasil diubah!'
            ]);
        }

        return redirect()->route('products-master.index')->with('success', 'Status trending produk berhasil diubah!');
    }
}
