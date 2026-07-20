<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;

Route::prefix('products')->group(function () {
    Route::post('/', [ProductController::class, 'store']); // LIST PRODUCT
    Route::get('/{productId}', [ProductController::class, 'show']); // GET /api/products/1 (PRODUCT DETAIL)
});

Route::prefix('orders')->group(function () {
    Route::post('/', [OrderController::class, 'store']); // ORDER PRODUCT
    Route::get('/{orderId}', [OrderController::class, 'show']); // GET /api/orders/1?email=customer@test.com (ORDER DETAIL)
});