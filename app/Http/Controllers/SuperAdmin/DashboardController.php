<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // 1. Filter Bulan Keseluruhan
        $selectedMonth = $request->string('month')->value();

        $query = Transaction::query();
        if (!empty($selectedMonth)) {
            $query->whereRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') = ?", [$selectedMonth]);
        }

        $transactions = $query->latest('tanggal_transfer')->get();

        // Rekap Bulanan
        $months = Transaction::query()
            ->selectRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') month, COUNT(*) count, SUM(nominal) total_nominal, SUM(okupansi_jam) total_okupansi")
            ->groupBy('month')
            ->orderByDesc('month')
            ->get();

        $totalNominal = $transactions->sum('nominal');
        $totalOkupansi = $transactions->sum('okupansi_jam');
        $totalCount = $transactions->count();

        // Hitung Rata-rata Durasi Main (dalam Jam per Booking)
        $avgDuration = $totalCount > 0 ? $totalOkupansi / $totalCount : 0;

        // 2. Filter Khusus Pemain Sering Main
        $playerMonthFilter = $request->input('player_month', 'current');

        $playerQuery = Transaction::query()
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

        // 3. Jam Main Paling Ramai
        $peakHours = Transaction::query()
            ->when(!empty($selectedMonth), function ($q) use ($selectedMonth) {
                $q->whereRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') = ?", [$selectedMonth]);
            })
            ->select('jam_mulai', DB::raw('COUNT(*) as total_booking'))
            ->whereNotNull('jam_mulai')
            ->groupBy('jam_mulai')
            ->orderByDesc('total_booking')
            ->get();

        return view('ledger.superadmin.dashboard', compact(
            'transactions',
            'months',
            'totalNominal',
            'totalOkupansi',
            'avgDuration',
            'topPlayers',
            'peakHours',
            'selectedMonth',
            'playerMonthFilter'
        ));
    }
}
