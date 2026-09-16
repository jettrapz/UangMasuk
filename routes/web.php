<?php

use App\Http\Controllers\Auth\LedgerLoginController;
use App\Http\Controllers\LedgerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LedgerController::class, 'home'])->name('home');

Route::get('/login', [LedgerLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LedgerLoginController::class, 'login']);
Route::post('/logout', [LedgerLoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [LedgerController::class, 'admin'])->name('admin');
    Route::get('/admin/dashboard', [LedgerController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/admin/export', [LedgerController::class, 'export'])->name('admin.export');
    Route::post('/admin/transactions', [LedgerController::class, 'store'])->name('admin.transactions.store');
    Route::get('/admin/transactions/{transaction}/edit', [LedgerController::class, 'edit'])->name('admin.transactions.edit');
    Route::put('/admin/transactions/{transaction}', [LedgerController::class, 'update'])->name('admin.transactions.update');
    Route::delete('/admin/transactions/{transaction}', [LedgerController::class, 'destroy'])->name('admin.transactions.destroy');
});

Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/superadmin', [LedgerController::class, 'superadmin'])->name('superadmin');
    Route::get('/superadmin/transactions/create', [LedgerController::class, 'superadminInput'])->name('superadmin.transactions.create');
    Route::post('/superadmin/transactions', [LedgerController::class, 'store'])->name('superadmin.transactions.store');
    Route::get('/superadmin/transactions/{transaction}/edit', [LedgerController::class, 'edit'])->name('superadmin.transactions.edit');
    Route::put('/superadmin/transactions/{transaction}', [LedgerController::class, 'update'])->name('superadmin.transactions.update');
    Route::delete('/superadmin/transactions/{transaction}', [LedgerController::class, 'destroy'])->name('superadmin.transactions.destroy');
    Route::get('/superadmin/export', [LedgerController::class, 'export'])->name('superadmin.export');
});
