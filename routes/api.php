<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemApiController;
use App\Http\Controllers\Api\SupplierApiController;
use App\Http\Controllers\Api\PurchaseOrderApiController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('items')->group(function () {
    Route::get('search', [ItemApiController::class, 'search'])->name('api.items.search');
});

Route::prefix('suppliers')->group(function () {
    Route::get('list', [SupplierApiController::class, 'list'])->name('api.suppliers.list');
});

Route::prefix('purchase-orders')->group(function () {
    Route::get('list', [PurchaseOrderApiController::class, 'list'])->name('api.purchase_orders.list');
    Route::get('by-number', [PurchaseOrderApiController::class, 'byNumber'])->name('api.purchase_orders.by_number');
});

