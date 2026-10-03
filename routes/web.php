<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Auth\LedgerLoginController;
use App\Http\Controllers\LedgerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LedgerController::class, 'home'])->name('home');

Route::get('/login', [LedgerLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LedgerLoginController::class, 'login']);
Route::post('/logout', [LedgerLoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [TransactionController::class, 'index'])->name('admin');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/export', [LedgerController::class, 'export'])->name('admin.export');
    Route::get('/admin/transactions/history', [TransactionController::class, 'history'])->name('admin.transactions.history');
    Route::get('/admin/transactions/create', [TransactionController::class, 'create'])->name('admin.transactions.create');
    Route::post('/admin/transactions', [TransactionController::class, 'store'])->name('admin.transactions.store');
    Route::get('/admin/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('admin.transactions.edit');
    Route::put('/admin/transactions/{transaction}', [TransactionController::class, 'update'])->name('admin.transactions.update');
    Route::delete('/admin/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('admin.transactions.destroy');
});

Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/superadmin', [DashboardController::class, 'index'])->name('superadmin');
    Route::get('/superadmin/transactions/history', [TransactionController::class, 'history'])->name('superadmin.transactions.history');
    Route::get('/superadmin/transactions/create', [TransactionController::class, 'create'])->name('superadmin.transactions.create');
    Route::post('/superadmin/transactions', [TransactionController::class, 'store'])->name('superadmin.transactions.store');
    Route::get('/superadmin/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('superadmin.transactions.edit');
    Route::put('/superadmin/transactions/{transaction}', [TransactionController::class, 'update'])->name('superadmin.transactions.update');
    Route::delete('/superadmin/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('superadmin.transactions.destroy');
    Route::get('/superadmin/export', [LedgerController::class, 'export'])->name('superadmin.export');
});
