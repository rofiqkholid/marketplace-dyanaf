<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssetUploadController extends Controller
{
    /**
     * Ensure only role 'pemilik' can access asset upload management.
     */
    protected function checkRole()
    {
        if (!Auth::check() || Auth::user()->role !== 'pemilik') {
            abort(403, 'Akses Ditolak. Halaman Upload Asset hanya dapat diakses oleh Pemilik.');
        }
    }

    /**
     * Halaman Admin: Daftar Asset Terupload & Form Upload.
     */
    public function index(Request $request)
    {
        $this->checkRole();

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 50, 100])) {
            $perPage = 10;
        }

        $query = OrderAsset::with('order');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('order', function($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%")
                         ->orWhere('customer_name', 'like', "%{$search}%")
                         ->orWhere('item_name', 'like', "%{$search}%");
                  });
            });
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
        // Ambil order yang statusnya processing atau completed (bisa semua order aktif)
        $orders = Order::orderBy('created_at', 'desc')->get();

        return view('page.upload-asset', compact('assets', 'orders'));
    }

    /**
     * Store new asset for an order.
     */
    public function store(Request $request)
    {
        $this->checkRole();

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'title' => 'required|string|max:255',
            'asset_file' => 'nullable|file|mimes:png,jpg,jpeg,pdf,doc,docx,ppt,pptx|max:51200', // max 50MB
            'drive_link' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [
            'order_id.required' => 'Silakan pilih nomor order terkait.',
            'order_id.exists' => 'Nomor order tidak valid.',
            'title.required' => 'Judul / Nama File Aset wajib diisi.',
            'asset_file.mimes' => 'Format file harus berupa PNG, JPG, JPEG, PDF, DOC, DOCX, PPT, atau PPTX.',
            'asset_file.max' => 'Ukuran file maksimal 50MB. Untuk ukuran lebih besar gunakan Google Drive link.',
        ]);

        if (!$request->hasFile('asset_file') && empty(trim($request->drive_link ?? ''))) {
            return back()->withErrors(['asset_file' => 'Harap upload file aset ATAU masukkan link Google Drive.'])->withInput();
        }

        $filePath = null;
        $fileType = null;
        $fileSize = null;

        if ($request->hasFile('asset_file')) {
            $file = $request->file('asset_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $fileName = time() . '_' . uniqid() . '.' . $extension;
            $filePath = $file->storeAs('assets/orders', $fileName, 'public');
            $fileType = $extension;
            $fileSize = $file->getSize();
        } elseif (!empty($request->drive_link)) {
            $fileType = 'link';
        }

        OrderAsset::create([
            'order_id' => $request->order_id,
            'title' => $request->title,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'drive_link' => $request->drive_link,
            'notes' => $request->notes,
        ]);

        return redirect()->route('assets-upload.index')->with('success', 'Aset pesanan berhasil diupload dan siap diunduh oleh pelanggan!');
    }

    /**
     * Delete an uploaded asset.
     */
    public function destroy(OrderAsset $orderAsset)
    {
        $this->checkRole();

        if ($orderAsset->file_path && Storage::disk('public')->exists($orderAsset->file_path)) {
            Storage::disk('public')->delete($orderAsset->file_path);
        }

        $orderAsset->delete();

        return redirect()->route('assets-upload.index')->with('success', 'Aset berhasil dihapus.');
    }
}
