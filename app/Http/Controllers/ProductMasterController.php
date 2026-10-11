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
    public function index()
    {
        $this->checkRole();

        $products = Product::orderBy('created_at', 'desc')->get();
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
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;

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
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : false;

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
    public function toggleActive(Product $product)
    {
        $this->checkRole();

        $product->update(['is_active' => !$product->is_active]);

        return redirect()->route('products-master.index')->with('success', 'Status keaktifan produk berhasil diubah!');
    }
}
