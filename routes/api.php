<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\FinancialScheduleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rotas para funcionalidade offline - Protegidas por auth web
Route::middleware('auth')->group(function () {
    // Produtos
    Route::get('/products', [ProductController::class, 'apiProducts'])->name('api.products');
    Route::post('/products', [ProductController::class, 'apiStore'])->name('api.products.store');
    Route::put('/products/{product}', [ProductController::class, 'apiUpdate'])->name('api.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'apiDestroy'])->name('api.products.destroy');
    
    // Compras
    Route::get('/purchases', [PurchaseController::class, 'apiIndex'])->name('api.purchases');
    Route::post('/purchases', [PurchaseController::class, 'apiStore'])->name('api.purchases.store');
    Route::put('/purchases/{purchase}', [PurchaseController::class, 'apiUpdate'])->name('api.purchases.update');
    Route::delete('/purchases/{purchase}', [PurchaseController::class, 'apiDestroy'])->name('api.purchases.destroy');
    
    // Transações Financeiras (CashFlow)
    Route::get('/cashflows', [CashFlowController::class, 'apiIndex'])->name('api.cashflows');
    Route::post('/cashflows', [CashFlowController::class, 'apiStore'])->name('api.cashflows.store');
    Route::put('/cashflows/{cashflow}', [CashFlowController::class, 'apiUpdate'])->name('api.cashflows.update');
    Route::delete('/cashflows/{cashflow}', [CashFlowController::class, 'apiDestroy'])->name('api.cashflows.destroy');
    
    // Agenda Financeira (Schedule)
    Route::get('/schedules', [FinancialScheduleController::class, 'apiIndex'])->name('api.schedules');
    Route::post('/schedules', [FinancialScheduleController::class, 'apiStore'])->name('api.schedules.store');
    Route::put('/schedules/{schedule}', [FinancialScheduleController::class, 'apiUpdate'])->name('api.schedules.update');
    Route::delete('/schedules/{schedule}', [FinancialScheduleController::class, 'apiDestroy'])->name('api.schedules.destroy');
});
