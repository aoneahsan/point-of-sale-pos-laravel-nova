<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POS\POSController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\SaleController;

Route::get('/', function () {
    return redirect('/admin');
});

// POS Routes
Route::middleware(['auth'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [POSController::class, 'index'])->name('index');
    Route::get('/receipt/{sale}', [POSController::class, 'receipt'])->name('receipt');
});

// API Routes
Route::middleware(['auth:sanctum'])->prefix('api/v1')->name('api.')->group(function () {
    // Products
    Route::apiResource('products', ProductController::class);

    // Customers
    Route::apiResource('customers', CustomerController::class);

    // Sales
    Route::apiResource('sales', SaleController::class);

    // Reports
    Route::get('/reports/sales', [SaleController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/inventory', [ProductController::class, 'inventoryReport'])->name('reports.inventory');
});
