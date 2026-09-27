<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request): View
    {
        $editing = null;

        return view('ledger.superadmin.transactions.create', compact('editing'));
    }

    public function history(): View
    {
        $transactions = Transaction::latest('tanggal_transfer')->get();

        return view('ledger.superadmin.transactions.history', compact('transactions'));
    }

    public function create(): View
    {
        $editing = null;

        return view('ledger.superadmin.transactions.create', compact('editing'));
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $this->transactionService->create($request->validated());

        return redirect()->route('superadmin.transactions.create')
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function edit(Transaction $transaction): View
    {
        return view('ledger.superadmin.transactions.edit', [
            'editing' => $transaction,
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->transactionService->update($transaction, $request->validated());

        return redirect()->route('superadmin.transactions.create')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->transactionService->delete($transaction);

        return back()->with('success', 'Transaksi berhasil dihapus.');
    }
}
