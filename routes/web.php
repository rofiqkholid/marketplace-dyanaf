<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\MenuMasterController;
use App\Http\Controllers\ProductMasterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public Marketplace Routes
Route::get('/', [MarketplaceController::class, 'index'])->name('marketplace');
Route::get('/trending', [MarketplaceController::class, 'trending'])->name('trending');
Route::get('/new-arrival', [MarketplaceController::class, 'newArrival'])->name('new-arrival');
Route::get('/all-product', [MarketplaceController::class, 'allProducts'])->name('all-product');
Route::get('/brand/{slug}', [MarketplaceController::class, 'brandDetail'])->name('brand.detail');
Route::get('/product/{product}', [MarketplaceController::class, 'productDetail'])->name('product.detail');


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


