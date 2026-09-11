<?php

use App\Http\Controllers\LedgerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LedgerController::class, 'home'])->name('home');
Route::get('/login/{role}', [LedgerController::class, 'showLogin'])->name('login');
Route::post('/login/{role}', [LedgerController::class, 'login'])->name('login.store');
Route::post('/logout/{role}', [LedgerController::class, 'logout'])->name('logout');

Route::middleware('ledger.role:admin')->group(function () {
    Route::get('/admin', [LedgerController::class, 'admin'])->name('admin');
    Route::post('/admin/transactions', [LedgerController::class, 'store'])->name('admin.transactions.store');
    Route::get('/admin/transactions/{transaction}/edit', [LedgerController::class, 'edit'])->name('admin.transactions.edit');
    Route::put('/admin/transactions/{transaction}', [LedgerController::class, 'update'])->name('admin.transactions.update');
    Route::delete('/admin/transactions/{transaction}', [LedgerController::class, 'destroy'])->name('admin.transactions.destroy');
});

Route::middleware('ledger.role:superadmin')->group(function () {
    Route::get('/superadmin', [LedgerController::class, 'superadmin'])->name('superadmin');
    Route::get('/superadmin/export', [LedgerController::class, 'export'])->name('superadmin.export');
});
