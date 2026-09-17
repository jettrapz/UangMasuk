<?php

namespace App\Services;

use App\Exports\TransactionsExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TransactionExportService
{
    public function export(): BinaryFileResponse
    {
        $filename = Auth::user()->role === 'admin'
            ? 'transaksi-admin-' . now()->format('Y-m-d') . '.xlsx'
            : 'transaksi-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new TransactionsExport, $filename);
    }
}