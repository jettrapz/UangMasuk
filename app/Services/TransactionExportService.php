<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionExportService
{
    public function export(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $query = Transaction::query();
        
        if (Auth::user()->role === 'admin') {
            $query->where('created_by', Auth::id());
        }

        $rows = $query->latest('tanggal_transfer')->get();

        $csv = "ATAS NAMA / KOMUNITAS,TGL/BULAN MAIN,JAM MAIN,PEMBAYARAN,TGL PEMBAYARAN,NOMINAL,NOTE\n";

        foreach ($rows as $row) {
            $values = [
                $row->nama,
                $row->tanggal_main,
                "{$row->jam_mulai} - {$row->jam_selesai}",
                $row->jenis_transfer,
                $row->tanggal_transfer,
                $row->nominal,
                $row->catatan,
            ];
            $csv .= implode(',', array_map(fn ($value) => '"' . str_replace('"', '""', (string) $value) . '"', $values)) . "\n";
        }

        $filename = Auth::user()->role === 'admin'
            ? 'transaksi-admin-' . now()->format('Y-m-d') . '.csv'
            : 'transaksi-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(fn () => print $csv, $filename);
    }
}
