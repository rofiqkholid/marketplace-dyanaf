<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\MenuMasterController;
use App\Http\Controllers\ProductMasterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $products = \App\Models\Product::where('is_active', true)->get();
    return view('page.marketplace', compact('products'));
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

// Dynamic Brand Details Route (Clean via Database)
Route::get('/brand/{slug}', function ($slug) {
    $brand = \App\Models\Brand::where('slug', $slug)->where('is_active', true)->firstOrFail();
    return view('page.brand-detail', compact('brand', 'slug'));
})->name('brand.detail');

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

// Create Order / Order Management Routes (Restricted to role: pemilik in controller)
Route::get('/orders-create', [\App\Http\Controllers\OrderController::class, 'adminIndex'])->name('orders.admin');
Route::post('/orders-create', [\App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
Route::put('/orders-create/{order}', [\App\Http\Controllers\OrderController::class, 'update'])->name('orders.update');
Route::patch('/orders-create/{order}/status', [\App\Http\Controllers\OrderController::class, 'updateStatus'])->name('orders.update-status');
Route::delete('/orders-create/{order}', [\App\Http\Controllers\OrderController::class, 'destroy'])->name('orders.destroy');


