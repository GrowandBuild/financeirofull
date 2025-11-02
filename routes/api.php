<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rotas para funcionalidade offline
Route::get('/products', [ProductController::class, 'apiProducts'])->name('api.products');
Route::post('/products', [ProductController::class, 'apiStore'])->name('api.products.store');
Route::put('/products/{product}', [ProductController::class, 'apiUpdate'])->name('api.products.update');
Route::delete('/products/{product}', [ProductController::class, 'apiDestroy'])->name('api.products.destroy');

Route::get('/purchases', [PurchaseController::class, 'apiIndex'])->name('api.purchases');
Route::post('/purchases', [PurchaseController::class, 'apiStore'])->name('api.purchases.store');
Route::put('/purchases/{purchase}', [PurchaseController::class, 'apiUpdate'])->name('api.purchases.update');
Route::delete('/purchases/{purchase}', [PurchaseController::class, 'apiDestroy'])->name('api.purchases.destroy');
