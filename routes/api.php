<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\AuthController;

// Public routes (with stricter rate limiting)
Route::middleware(['throttle:login'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes (with rate limiting)
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // Products
    Route::apiResource('products', ProductController::class);
    Route::get('products/{product}/variants', [ProductController::class, 'variants']);
    
    // Customers
    Route::apiResource('customers', CustomerController::class);
    Route::post('customers/{customer}/loyalty-points', [CustomerController::class, 'addLoyaltyPoints']);
    Route::post('customers/{customer}/store-credit', [CustomerController::class, 'addStoreCredit']);
    
    // Sales
    Route::apiResource('sales', SaleController::class);
    Route::post('sales/{sale}/refund', [SaleController::class, 'refund']);
    Route::get('sales/{sale}/invoice', [SaleController::class, 'invoice']);
    
    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('sales', [ReportController::class, 'sales']);
        Route::get('inventory', [ReportController::class, 'inventory']);
        Route::get('customers', [ReportController::class, 'customers']);
    });
    
    // User info
    Route::get('/user', function (Illuminate\Http\Request $request) {
        return $request->user();
    });
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
