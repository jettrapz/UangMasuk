<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LedgerController extends Controller
{
    public function home(): View
    {
        return view('ledger.home');
    }

    public function export(): BinaryFileResponse
    {
        $filename = 'transaksi-uang-masuk-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new TransactionsExport, $filename);
    }
}
