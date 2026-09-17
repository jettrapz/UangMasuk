<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $query = Transaction::query()
            ->where('created_by', Auth::id());

        if ($request->filled('month')) {
            $query->whereRaw(
                "DATE_FORMAT(tanggal_transfer, '%Y-%m') = ?",
                [$request->string('month')]
            );
        }

        $transactions = $query
            ->latest('tanggal_transfer')
            ->get();

        $months = Transaction::query()
            ->where('created_by', Auth::id())
            ->selectRaw("
                DATE_FORMAT(tanggal_transfer, '%Y-%m') as month,
                COUNT(*) as count,
                SUM(nominal) as total_nominal,
                SUM(okupansi_jam) as total_okupansi
            ")
            ->groupBy('month')
            ->orderByDesc('month')
            ->get();

        $totalNominal = $transactions->sum('nominal');

        // Data untuk chart
        $chartMonths = $months->reverse()->values();

        $chartLabels = $chartMonths->pluck('month');
        $chartNominal = $chartMonths->pluck('total_nominal');
        $chartOkupansi = $chartMonths->pluck('total_okupansi');

        return view('ledger.admin.dashboard', compact(
            'transactions',
            'months',
            'totalNominal',
            'chartLabels',
            'chartNominal',
            'chartOkupansi'
        ));
    }
}