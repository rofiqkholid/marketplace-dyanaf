<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Ensure only user with role 'pemilik' can access user management.
     */
    protected function checkRole()
    {
        if (!Auth::check() || Auth::user()->role !== 'pemilik') {
            abort(403, 'Akses Ditolak. Halaman User Management hanya dapat diakses oleh Pemilik.');
        }
    }

    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $this->checkRole();

        $users = User::orderBy('created_at', 'desc')->get();
        $roles = ['pemilik', 'pengunjung'];

        return view('page.user', compact('users', 'roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $this->checkRole();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|string|in:pemilik,pengunjung',
            'dyanaf_points' => 'nullable|integer|min:0',
            'password' => 'required|string|min:6',
            'avatar' => 'nullable|string|max:1000',
        ]);

        $validated['dyanaf_points'] = $validated['dyanaf_points'] ?? 0;
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User baru berhasil ditambahkan!');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->checkRole();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|string|in:pemilik,pengunjung',
            'dyanaf_points' => 'nullable|integer|min:0',
            'password' => 'nullable|string|min:6',
            'avatar' => 'nullable|string|max:1000',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $this->checkRole();

        // Prevent self-deletion
        if (Auth::id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}
