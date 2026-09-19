<?php

namespace App\Http\Controllers\Admin;

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

        return view('ledger.admin.transactions.create', compact('editing'));
    }

    public function history(): View
    {
        $transactions = Transaction::forRole(Auth::user())
            ->latest('tanggal_transfer')
            ->get();

        return view('ledger.admin.transactions.history', compact('transactions'));
    }

    public function create(): View
    {
        $editing = null;

        return view('ledger.admin.transactions.create', compact('editing'));
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $this->transactionService->create($request->validated());

        return redirect()->route('admin.transactions.create')
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function edit(Transaction $transaction): View
    {
        abort_unless(
            Auth::user()->role === 'superadmin' || $transaction->created_by === Auth::id(),
            403
        );

        return view('ledger.admin.transactions.edit', [
            'editing' => $transaction,
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        abort_unless(
            Auth::user()->role === 'superadmin' || $transaction->created_by === Auth::id(),
            403
        );

        $this->transactionService->update($transaction, $request->validated());

        return redirect()->route('admin.transactions.create')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        abort_unless(
            Auth::user()->role === 'superadmin' || $transaction->created_by === Auth::id(),
            403
        );

        $this->transactionService->delete($transaction);

        return back()->with('success', 'Transaksi berhasil dihapus.');
    }
}
