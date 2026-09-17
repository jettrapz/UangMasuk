<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Auth\LedgerLoginController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\TransactionController as SuperAdminTransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LedgerController::class, 'home'])->name('home');

Route::get('/login', [LedgerLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LedgerLoginController::class, 'login']);
Route::post('/logout', [LedgerLoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminTransactionController::class, 'index'])->name('admin');
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/export', [LedgerController::class, 'export'])->name('admin.export');
    Route::get('/admin/transactions/create', [AdminTransactionController::class, 'create'])->name('admin.transactions.create');
    Route::post('/admin/transactions', [AdminTransactionController::class, 'store'])->name('admin.transactions.store');
    Route::get('/admin/transactions/{transaction}/edit', [AdminTransactionController::class, 'edit'])->name('admin.transactions.edit');
    Route::put('/admin/transactions/{transaction}', [AdminTransactionController::class, 'update'])->name('admin.transactions.update');
    Route::delete('/admin/transactions/{transaction}', [AdminTransactionController::class, 'destroy'])->name('admin.transactions.destroy');
});

Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/superadmin', [SuperAdminDashboardController::class, 'index'])->name('superadmin');
    Route::get('/superadmin/transactions/create', [SuperAdminTransactionController::class, 'create'])->name('superadmin.transactions.create');
    Route::post('/superadmin/transactions', [SuperAdminTransactionController::class, 'store'])->name('superadmin.transactions.store');
    Route::get('/superadmin/transactions/{transaction}/edit', [SuperAdminTransactionController::class, 'edit'])->name('superadmin.transactions.edit');
    Route::put('/superadmin/transactions/{transaction}', [SuperAdminTransactionController::class, 'update'])->name('superadmin.transactions.update');
    Route::delete('/superadmin/transactions/{transaction}', [SuperAdminTransactionController::class, 'destroy'])->name('superadmin.transactions.destroy');
    Route::get('/superadmin/export', [LedgerController::class, 'export'])->name('superadmin.export');
});
