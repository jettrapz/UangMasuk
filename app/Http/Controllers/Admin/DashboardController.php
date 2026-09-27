<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $userId = Auth::id();

        // 1. Filter Bulan Keseluruhan
        $selectedMonth = $request->string('month')->value();

        $query = Transaction::query()->where('created_by', $userId);

        if (!empty($selectedMonth)) {
            $query->whereRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') = ?", [$selectedMonth]);
        }

        $transactions = $query->latest('tanggal_transfer')->get();

        // Rekap Bulanan
        $months = Transaction::query()
            ->where('created_by', $userId)
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
        $totalOkupansi = $transactions->sum('okupansi_jam');
        $totalCount = $transactions->count();

        // Rata-rata Durasi Main (Jam per Booking)
        $avgDuration = $totalCount > 0 ? $totalOkupansi / $totalCount : 0;

        // Data untuk Chart Tren Bulanan
        $chartMonths = $months->reverse()->values();
        $chartLabels = $chartMonths->pluck('month')->map(function ($m) {
            return Carbon::createFromFormat('Y-m', $m)->format('M Y');
        });
        $chartNominal = $chartMonths->pluck('total_nominal');
        $chartOkupansi = $chartMonths->pluck('total_okupansi');

        // 2. Filter Khusus Pemain Sering Main (Milik Admin Ini)
        $playerMonthFilter = $request->input('player_month', 'current');

        $playerQuery = Transaction::query()
            ->where('created_by', $userId)
            ->select('nama', DB::raw('COUNT(*) as total_main'), DB::raw('SUM(okupansi_jam) as total_jam'))
            ->whereNotNull('nama')
            ->where('nama', '!=', '');

        if ($playerMonthFilter === 'current') {
            $playerQuery->whereYear('tanggal_transfer', Carbon::now()->year)
                        ->whereMonth('tanggal_transfer', Carbon::now()->month);
        } elseif ($playerMonthFilter === 'selected' && !empty($selectedMonth)) {
            $playerQuery->whereRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') = ?", [$selectedMonth]);
        } elseif ($playerMonthFilter !== 'all' && preg_match('/^\d{4}-\d{2}$/', $playerMonthFilter)) {
            $playerQuery->whereRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') = ?", [$playerMonthFilter]);
        }

        $topPlayers = $playerQuery->groupBy('nama')
            ->orderByDesc('total_main')
            ->limit(5)
            ->get();

        // 3. Jam Main Paling Ramai (Milik Admin Ini)
        $peakHours = Transaction::query()
            ->where('created_by', $userId)
            ->when(!empty($selectedMonth), function ($q) use ($selectedMonth) {
                $q->whereRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') = ?", [$selectedMonth]);
            })
            ->select('jam_mulai', DB::raw('COUNT(*) as total_booking'))
            ->whereNotNull('jam_mulai')
            ->groupBy('jam_mulai')
            ->orderByDesc('total_booking')
            ->get();

        return view('ledger.admin.dashboard', compact(
            'transactions',
            'months',
            'totalNominal',
            'totalOkupansi',
            'avgDuration',
            'chartLabels',
            'chartNominal',
            'chartOkupansi',
            'topPlayers',
            'peakHours',
            'selectedMonth',
            'playerMonthFilter'
        ));
    }
}