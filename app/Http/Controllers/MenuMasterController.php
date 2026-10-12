<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuMasterController extends Controller
{
    /**
     * Ensure only user with role 'pemilik' can access menu management.
     */
    protected function checkRole()
    {
        if (!Auth::check() || Auth::user()->role !== 'pemilik') {
            abort(403, 'Akses Ditolak. Halaman Management Menu hanya dapat diakses oleh Pemilik.');
        }
    }

    /**
     * Display a listing of the menus.
     */
    public function index(Request $request)
    {
        $this->checkRole();

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 50, 100])) {
            $perPage = 10;
        }

        $query = Menu::with(['children' => function ($q) {
            $q->orderBy('order');
        }])->whereNull('parent_id');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%")
                  ->orWhereHas('children', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('url', 'like', "%{$search}%");
                  });
            });
        }

        // Fetch paginated root level menus with children
        $menus = $query->orderBy('order')->paginate($perPage)->withQueryString();

        // Parent options for form dropdown
        $parentMenus = Menu::whereNull('parent_id')->orderBy('order')->get();

        return view('page.menus-master', compact('menus', 'parentMenus'));
    }

    /**
     * Store a newly created menu in storage.
     */
    public function store(Request $request)
    {
        $this->checkRole();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'url' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_maintenance' => 'nullable|boolean',
            'role' => 'nullable|string|in:pemilik,pengunjung',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;
        $validated['is_maintenance'] = $request->has('is_maintenance') ? (bool)$request->is_maintenance : false;
        $validated['url'] = $validated['url'] ?: '#';
        $validated['role'] = $validated['role'] ?: null;

        Menu::create($validated);

        return redirect()->route('menus-master.index')->with('success', 'Menu baru berhasil ditambahkan!');
    }

    /**
     * Update the specified menu in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $this->checkRole();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'url' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_maintenance' => 'nullable|boolean',
            'role' => 'nullable|string|in:pemilik,pengunjung',
        ]);

        // Prevent selecting self as parent
        if (!empty($validated['parent_id']) && $validated['parent_id'] == $menu->id) {
            return redirect()->back()->withErrors(['parent_id' => 'Menu tidak dapat menjadi parent bagi dirinya sendiri.']);
        }

        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : false;
        $validated['is_maintenance'] = $request->has('is_maintenance') ? (bool)$request->is_maintenance : false;
        $validated['url'] = $validated['url'] ?: '#';
        $validated['role'] = $validated['role'] ?: null;

        $menu->update($validated);

        return redirect()->route('menus-master.index')->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Remove the specified menu from storage.
     */
    public function destroy(Menu $menu)
    {
        $this->checkRole();

        $menu->delete();

        return redirect()->route('menus-master.index')->with('success', 'Menu berhasil dihapus!');
    }

    /**
     * Toggle active status of the specified menu.
     */
    public function toggleActive(Request $request, Menu $menu)
    {
        $this->checkRole();

        $menu->update(['is_active' => !$menu->is_active]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'is_active' => $menu->is_active,
                'message' => 'Status keaktifan menu berhasil diubah!'
            ]);
        }

        return redirect()->back()->with('success', 'Status keaktifan menu berhasil diubah!');
    }

    /**
     * Toggle maintenance status of the specified menu.
     */
    public function toggleMaintenance(Request $request, Menu $menu)
    {
        $this->checkRole();

        $menu->update(['is_maintenance' => !$menu->is_maintenance]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'is_maintenance' => $menu->is_maintenance,
                'message' => 'Status maintenance menu berhasil diubah!'
            ]);
        }

        return redirect()->back()->with('success', 'Status maintenance menu berhasil diubah!');
    }
}
