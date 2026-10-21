<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\MenuMasterController;
use App\Http\Controllers\ProductMasterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $products = \App\Models\Product::where('is_active', true)
        ->where('is_trending', true)
        ->get();
    $brands = \App\Models\Brand::where('is_active', true)->get()->keyBy('slug');
    return view('page.marketplace', compact('products', 'brands'));
});

// Trending Page Route (Filter Produk & Brand dengan is_trending = true dan is_active = true)
Route::get('/trending', function () {
    $trendingProducts = \App\Models\Product::where('is_active', true)
        ->where('is_trending', true)
        ->orderBy('updated_at', 'desc')
        ->get();

    $trendingBrands = \App\Models\Brand::where('is_active', true)
        ->where('is_trending', true)
        ->orderBy('updated_at', 'desc')
        ->get();

    return view('page.trending', compact('trendingProducts', 'trendingBrands'));
})->name('trending');

// New Arrival Page Route
Route::get('/new-arrival', function () {
    $newProducts = \App\Models\Product::where('is_active', true)
        ->orderBy('created_at', 'desc')
        ->get();

    $newBrands = \App\Models\Brand::where('is_active', true)
        ->orderBy('created_at', 'desc')
        ->get();

    return view('page.new-arrival', compact('newProducts', 'newBrands'));
})->name('new-arrival');

// All Products Page Route (Katalog Semua Produk)
Route::get('/all-product', function () {
    $products = \App\Models\Product::where('is_active', true)
        ->orderBy('name', 'asc')
        ->get();

    $categories = \App\Models\Product::where('is_active', true)
        ->whereNotNull('category')
        ->distinct()
        ->pluck('category');

    return view('page.all-product', compact('products', 'categories'));
})->name('all-product');

// Dynamic Brand Details Route (Clean via Database)
Route::get('/brand/{slug}', function ($slug) {
    $brand = \App\Models\Brand::where('slug', $slug)->where('is_active', true)->firstOrFail();
    return view('page.brand-detail', compact('brand', 'slug'));
})->name('brand.detail');

// Dynamic Product Details Route
Route::get('/product/{product}', function (\App\Models\Product $product) {
    if (!$product->is_active) {
        abort(404);
    }

    $originalPrices = [
        'Makalah (Tanpa Materi)' => 140000,
        'Makalah (Ada Materi)' => 98000,
        'Jurnal Ilmiah' => 182000,
        'CV Kreatif' => 35000,
        'CV ATS-Friendly' => 84000,
        'Surat Lamaran Kerja' => 28000,
        'Gabung & Rapikan PDF' => 14000,
        'Website Landing Page' => 2000000,
        'Web Operasional & POS' => 10000000,
        'Desain Grafis & Promo' => 140000,
        'Data Entry & Spreadsheet' => 210000,
        'Jasa Ketik Word' => 140000,
        'Jasa Excel & Analisis Data' => 182000,
    ];
    $originalPrice = $originalPrices[$product->name] ?? null;

    $relatedProducts = \App\Models\Product::where('is_active', true)
        ->where('id', '!=', $product->id)
        ->where(function($q) use ($product) {
            $q->where('category', $product->category)
              ->orWhere('is_trending', true);
        })
        ->take(4)
        ->get();

    return view('page.product-detail', compact('product', 'originalPrice', 'relatedProducts'));
})->name('product.detail');

// Google OAuth SSO Routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

// Menu Management Master Routes (Restricted to role: pemilik in controller)
Route::get('/menus-master', [MenuMasterController::class, 'index'])->name('menus-master.index');
Route::post('/menus-master', [MenuMasterController::class, 'store'])->name('menus-master.store');
Route::put('/menus-master/{menu}', [MenuMasterController::class, 'update'])->name('menus-master.update');
Route::delete('/menus-master/{menu}', [MenuMasterController::class, 'destroy'])->name('menus-master.destroy');
Route::patch('/menus-master/{menu}/toggle-active', [MenuMasterController::class, 'toggleActive'])->name('menus-master.toggle-active');
Route::patch('/menus-master/{menu}/toggle-maintenance', [MenuMasterController::class, 'toggleMaintenance'])->name('menus-master.toggle-maintenance');

// Maintenance Page Route
Route::get('/maintenance', function () {
    return view('page.maintenance');
})->name('maintenance');

// Product Management Master Routes (Restricted to role: pemilik in controller)
Route::get('/products-master', [ProductMasterController::class, 'index'])->name('products-master.index');
Route::post('/products-master', [ProductMasterController::class, 'store'])->name('products-master.store');
Route::put('/products-master/{product}', [ProductMasterController::class, 'update'])->name('products-master.update');
Route::delete('/products-master/{product}', [ProductMasterController::class, 'destroy'])->name('products-master.destroy');
Route::patch('/products-master/{product}/toggle-active', [ProductMasterController::class, 'toggleActive'])->name('products-master.toggle-active');
Route::patch('/products-master/{product}/toggle-trending', [ProductMasterController::class, 'toggleTrending'])->name('products-master.toggle-trending');

// User Management Routes (Restricted to role: pemilik in controller)
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/user', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

// Brand Management Master Routes (Restricted to role: pemilik in controller)
Route::get('/brands-master', [\App\Http\Controllers\BrandMasterController::class, 'index'])->name('brands-master.index');
Route::post('/brands-master', [\App\Http\Controllers\BrandMasterController::class, 'store'])->name('brands-master.store');
Route::put('/brands-master/{brand}', [\App\Http\Controllers\BrandMasterController::class, 'update'])->name('brands-master.update');
Route::delete('/brands-master/{brand}', [\App\Http\Controllers\BrandMasterController::class, 'destroy'])->name('brands-master.destroy');
Route::patch('/brands-master/{brand}/toggle-active', [\App\Http\Controllers\BrandMasterController::class, 'toggleActive'])->name('brands-master.toggle-active');
Route::patch('/brands-master/{brand}/toggle-trending', [\App\Http\Controllers\BrandMasterController::class, 'toggleTrending'])->name('brands-master.toggle-trending');

// Tracking Order Public Route
Route::get('/tracking-order', [\App\Http\Controllers\OrderController::class, 'tracking'])->name('tracking-order');

// Download Asset Public Route
Route::get('/download-asset', [\App\Http\Controllers\OrderController::class, 'downloadAsset'])->name('download-asset');
Route::get('/download-asset/file/{orderAsset}', [\App\Http\Controllers\OrderController::class, 'downloadFile'])->name('download-asset.file');

// Create Order / Order Management Routes (Restricted to role: pemilik in controller)
Route::get('/orders-create', [\App\Http\Controllers\OrderController::class, 'adminIndex'])->name('orders.admin');
Route::post('/orders-create', [\App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
Route::put('/orders-create/{order}', [\App\Http\Controllers\OrderController::class, 'update'])->name('orders.update');
Route::patch('/orders-create/{order}/status', [\App\Http\Controllers\OrderController::class, 'updateStatus'])->name('orders.update-status');
Route::delete('/orders-create/{order}', [\App\Http\Controllers\OrderController::class, 'destroy'])->name('orders.destroy');

// Upload Asset Routes (Restricted to role: pemilik in controller)
Route::get('/assets-upload', [\App\Http\Controllers\AssetUploadController::class, 'index'])->name('assets-upload.index');
Route::post('/assets-upload', [\App\Http\Controllers\AssetUploadController::class, 'store'])->name('assets-upload.store');
Route::delete('/assets-upload/{orderAsset}', [\App\Http\Controllers\AssetUploadController::class, 'destroy'])->name('assets-upload.destroy');

// Custom Modal Payment Routes (Core API Custom UI: VA & QRIS)
Route::post('/payment/charge/{product}', [\App\Http\Controllers\ModalPaymentController::class, 'charge'])->name('payment.modal.charge');
Route::post('/payment/charge-brand/{brand}', [\App\Http\Controllers\ModalPaymentController::class, 'chargeBrand'])->name('payment.modal.charge-brand');
Route::get('/payment/status/{orderNumber}', [\App\Http\Controllers\ModalPaymentController::class, 'checkStatus'])->name('payment.modal.status');


