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

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $query = Transaction::query();
        if ($request->filled('month')) {
            $query->whereRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') = ?", [$request->string('month')]);
        }

        $transactions = $query->latest('tanggal_transfer')->get();
        $months = Transaction::query()->selectRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') month, COUNT(*) count, SUM(nominal) total_nominal, SUM(okupansi_jam) total_okupansi")
            ->groupBy('month')->orderByDesc('month')->get();
        $totalNominal = $transactions->sum('nominal');

        return view('ledger.superadmin.dashboard', compact('transactions', 'months', 'totalNominal'));
    }
}
