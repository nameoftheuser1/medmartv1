<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductBatchController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleDetailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::redirect('/', 'dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/inventory', [DashboardController::class, 'inventoryData'])->name('dashboard.inventory.data');

    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('product_batches', ProductBatchController::class);
    Route::resource('inventories', InventoryController::class);
    Route::resource('sales', SaleController::class);
    Route::resource('sale_details', SaleDetailController::class);

    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos/add-item', [POSController::class, 'addItem'])->name('pos.addItem');
    Route::post('/pos/remove-item', [POSController::class, 'removeItem'])->name('pos.removeItem');
    Route::post('/pos/update-item', [POSController::class, 'updateItem'])->name('pos.updateItem');
    Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
    Route::post('/pos/apply-discount', [POSController::class, 'applyDiscount'])->name('pos.applyDiscount');
});

Route::get('locale/{lang}', [LocaleController::class, 'setLocale']);
