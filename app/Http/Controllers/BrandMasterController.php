<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandMasterController extends Controller
{
    /**
     * Ensure only user with role 'pemilik' can access Brand management.
     */
    protected function checkRole()
    {
        if (!Auth::check() || Auth::user()->role !== 'pemilik') {
            abort(403, 'Akses Ditolak. Halaman Brand Master hanya dapat diakses oleh Pemilik.');
        }
    }

    /**
     * Display a listing of brands.
     */
    public function index()
    {
        $this->checkRole();

        $brands = Brand::orderBy('id', 'asc')->get();

        return view('page.brands-master', compact('brands'));
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(Request $request)
    {
        $this->checkRole();

        $validated = $request->validate([
            'slug' => 'required|string|max:100|unique:brands,slug',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'logo' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'restock_date' => 'nullable|date',
            'packages_json' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $packages = [];
        if (!empty($validated['packages_json'])) {
            $decoded = json_decode($validated['packages_json'], true);
            if (is_array($decoded)) {
                $packages = $decoded;
            }
        }

        Brand::create([
            'slug' => strtolower(trim($validated['slug'])),
            'name' => $validated['name'],
            'category' => $validated['category'],
            'logo' => $validated['logo'] ?? null,
            'description' => $validated['description'] ?? null,
            'stock' => $validated['stock'],
            'restock_date' => $validated['restock_date'] ?? null,
            'packages' => $packages,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->route('brands-master.index')->with('success', 'Brand baru berhasil ditambahkan!');
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $this->checkRole();

        $validated = $request->validate([
            'slug' => 'required|string|max:100|unique:brands,slug,' . $brand->id,
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'logo' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'restock_date' => 'nullable|date',
            'packages_json' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $packages = $brand->packages ?? [];
        if (!empty($validated['packages_json'])) {
            $decoded = json_decode($validated['packages_json'], true);
            if (is_array($decoded)) {
                $packages = $decoded;
            }
        }

        $brand->update([
            'slug' => strtolower(trim($validated['slug'])),
            'name' => $validated['name'],
            'category' => $validated['category'],
            'logo' => $validated['logo'] ?? null,
            'description' => $validated['description'] ?? null,
            'stock' => $validated['stock'],
            'restock_date' => $validated['restock_date'] ?? null,
            'packages' => $packages,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : false,
        ]);

        return redirect()->route('brands-master.index')->with('success', 'Brand ' . $brand->name . ' berhasil diperbarui!');
    }

    /**
     * Remove the specified brand from storage.
     */
    public function destroy(Brand $brand)
    {
        $this->checkRole();

        $name = $brand->name;
        $brand->delete();

        return redirect()->route('brands-master.index')->with('success', 'Brand ' . $name . ' berhasil dihapus!');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(Brand $brand)
    {
        $this->checkRole();

        $brand->update([
            'is_active' => !$brand->is_active,
        ]);

        return response()->json([
            'status' => 'success',
            'is_active' => $brand->is_active,
            'message' => 'Status aktif brand berhasil diubah.',
        ]);
    }
}
