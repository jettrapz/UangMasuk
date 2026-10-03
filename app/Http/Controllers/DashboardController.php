<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isSuper = $user->role === 'superadmin';
        $selectedMonth = $request->string('month')->value();

        $query = Transaction::query()->when(! $isSuper, fn ($q) => $q->where('created_by', $user->id));
        if (! empty($selectedMonth)) {
            $query->whereRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') = ?", [$selectedMonth]);
        }
        $transactions = $query->latest('tanggal_transfer')->get();

        $months = Transaction::query()->when(! $isSuper, fn ($q) => $q->where('created_by', $user->id))
            ->selectRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') as month, COUNT(*) as count, SUM(nominal) as total_nominal, SUM(okupansi_jam) as total_okupansi")
            ->groupBy('month')->orderByDesc('month')->get();

        $totalNominal = $transactions->sum('nominal');
        $totalOkupansi = $transactions->sum('okupansi_jam');
        $totalCount = $transactions->count();
        $avgDuration = $totalCount > 0 ? $totalOkupansi / $totalCount : 0;

        $chartMonths = $months->reverse()->values();
        $chartLabels = $chartMonths->pluck('month')->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'));
        $chartNominal = $chartMonths->pluck('total_nominal');
        $chartOkupansi = $chartMonths->pluck('total_okupansi');

        $playerMonthFilter = $request->input('player_month', 'current');
        $playerQuery = Transaction::query()->when(! $isSuper, fn ($q) => $q->where('created_by', $user->id))
            ->select('nama', DB::raw('COUNT(*) as total_main'), DB::raw('SUM(okupansi_jam) as total_jam'))
            ->whereNotNull('nama')->where('nama', '!=', '');
        if ($playerMonthFilter === 'current') {
            $playerQuery->whereYear('tanggal_transfer', Carbon::now()->year)->whereMonth('tanggal_transfer', Carbon::now()->month);
        } elseif ($playerMonthFilter === 'selected' && ! empty($selectedMonth)) {
            $playerQuery->whereRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') = ?", [$selectedMonth]);
        } elseif ($playerMonthFilter !== 'all' && preg_match('/^\d{4}-\d{2}$/', $playerMonthFilter)) {
            $playerQuery->whereRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') = ?", [$playerMonthFilter]);
        }
        $topPlayers = $playerQuery->groupBy('nama')->orderByDesc('total_main')->limit(5)->get();

        $peakHours = Transaction::query()->when(! $isSuper, fn ($q) => $q->where('created_by', $user->id))
            ->when(! empty($selectedMonth), fn ($q) => $q->whereRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') = ?", [$selectedMonth]))
            ->select('jam_mulai', DB::raw('COUNT(*) as total_booking'))->whereNotNull('jam_mulai')->groupBy('jam_mulai')->orderByDesc('total_booking')->get();

        return view('ledger.dashboard', compact('transactions', 'months', 'totalNominal', 'totalOkupansi', 'avgDuration', 'chartLabels', 'chartNominal', 'chartOkupansi', 'topPlayers', 'peakHours', 'selectedMonth', 'playerMonthFilter'));
    }
}
