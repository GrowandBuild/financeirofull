<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Rotas de Produtos - Protegidas
Route::middleware('auth')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/compra', [ProductController::class, 'compra'])->name('products.compra');
    Route::post('/compra/save', [ProductController::class, 'savePurchase'])->name('products.save-purchase');
    Route::delete('/purchases/{purchase}', [ProductController::class, 'destroyPurchase'])->name('purchases.destroy');
    Route::get('/api/products', [ProductController::class, 'apiProducts'])->name('products.api');
});

// Rotas do Sistema de Fluxo de Caixa
Route::prefix('cashflow')->name('cashflow.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\CashFlowController::class, 'dashboard'])->name('dashboard');
    Route::get('/transactions', [\App\Http\Controllers\CashFlowController::class, 'transactions'])->name('transactions');
    Route::get('/add', [\App\Http\Controllers\CashFlowController::class, 'add'])->name('add');
    Route::post('/store', [\App\Http\Controllers\CashFlowController::class, 'store'])->name('store');
    Route::get('/reports', [\App\Http\Controllers\CashFlowController::class, 'reports'])->name('reports');
    Route::delete('/transactions/{cashflow}', [\App\Http\Controllers\CashFlowController::class, 'destroy'])->name('transactions.destroy');
});

// Rotas da Agenda Financeira
Route::prefix('financial-schedule')->name('financial-schedule.')->middleware('auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\FinancialScheduleController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\FinancialScheduleController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\FinancialScheduleController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [\App\Http\Controllers\FinancialScheduleController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\FinancialScheduleController::class, 'update'])->name('update');
    Route::post('/{id}/confirm', [\App\Http\Controllers\FinancialScheduleController::class, 'confirm'])->name('confirm');
    Route::post('/{id}/unconfirm', [\App\Http\Controllers\FinancialScheduleController::class, 'unconfirm'])->name('unconfirm');
    Route::post('/{id}/cancel', [\App\Http\Controllers\FinancialScheduleController::class, 'cancel'])->name('cancel');
    Route::delete('/{id}', [\App\Http\Controllers\FinancialScheduleController::class, 'destroy'])->name('destroy');
    Route::get('/notifications', [\App\Http\Controllers\FinancialScheduleController::class, 'getNotifications'])->name('notifications');
});

// Rotas de Objetivos
Route::resource('goals', \App\Http\Controllers\GoalController::class)->middleware('auth');

// Rotas de Sabedoria (Livros)
Route::resource('books', \App\Http\Controllers\BookController::class)->middleware('auth');

// Rotas de Categorias
Route::prefix('categories')->name('categories.')->middleware('auth')->group(function () {
    Route::post('/quick-create', [\App\Http\Controllers\CategoryController::class, 'quickCreate'])->name('quick-create');
});

// Rotas de Admin
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::get('reset', [\App\Http\Controllers\Admin\ResetController::class, 'index'])->name('reset.index');
    Route::post('reset', [\App\Http\Controllers\Admin\ResetController::class, 'reset'])->name('reset.execute');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
