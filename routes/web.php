<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductBatchController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleDetailController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('products/export', [ProductController::class, 'export'])->name('products.export');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1');
});

Route::middleware('auth')->group(function () {
    Route::redirect('/', 'dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/inventories/product', [InventoryController::class, 'productInventory'])->name('inventories.product');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::get('/settings/password', [SettingController::class, 'editPassword'])->name('settings.edit.password');
    Route::put('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.update.password');
    Route::get('/settings/days-prediction', [SettingController::class, 'editPredictDay'])->name('settings.edit.prediction');
    Route::put('/settings/days-prediction', [SettingController::class, 'updatePrediction'])->name('settings.update.prediction');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/inventory', [DashboardController::class, 'inventoryData'])->name('dashboard.inventory.data');

    Route::post('/sales/{id}/refund', [SaleController::class, 'refund'])->name('sales.refund');

    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('product_batches', ProductBatchController::class);
    Route::resource('inventories', InventoryController::class);
    Route::resource('sales', SaleController::class);
    Route::resource('sale_details', SaleDetailController::class);
    Route::resource('expenses', ExpenseController::class);

    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::get('/receipt/{sale_id}', [POSController::class, 'receipt'])->name('pos.receipt');
    Route::post('/pos/add-item', [POSController::class, 'addItem'])->name('pos.addItem');
    Route::post('/pos/remove-item', [POSController::class, 'removeItem'])->name('pos.removeItem');
    Route::post('/pos/update-item', [POSController::class, 'updateItem'])->name('pos.updateItem');
    Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
    Route::post('/pos/apply-discount', [POSController::class, 'applyDiscount'])->name('pos.applyDiscount');
    Route::post('/pos/remove-all-items', [POSController::class, 'removeAllItems'])->name('pos.removeAllItems');

    Route::patch('/inventories/{inventory}/empty-quantity', [InventoryController::class, 'emptyQuantity'])->name('inventories.emptyQuantity');
    Route::patch('/product-batches/{productBatch}/return-date', [ProductBatchController::class, 'returnProduct'])->name('product_batches.returnDate');

    

});

Route::get('locale/{lang}', [LocaleController::class, 'setLocale']);
Route::get('/refresh-captcha', function () {
    return response()->json(['captcha' => captcha_img()]);
});
