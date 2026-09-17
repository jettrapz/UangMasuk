<?php


namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TransactionsExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithCustomStartCell,
    WithColumnWidths,
    WithEvents
{
    public function query(): Builder
    {
        $query = Transaction::query();

        if (Auth::user()->role === 'admin') {
            $query->where('created_by', Auth::id());
        }

        return $query->latest('tanggal_transfer');
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function headings(): array
    {
        return [
            'ATAS NAMA / KOMUNITAS',
            'TGL/BULAN MAIN',
            'JAM MAIN',
            'PEMBAYARAN',
            'TGL PEMBAYARAN',
            'NOMINAL',
            'NOTE',
        ];
    }

    public function map($row): array
    {
        $jamMain = ($row->jam_mulai && $row->jam_selesai)
            ? "{$row->jam_mulai} - {$row->jam_selesai}"
            : ($row->jam_mulai ?: ($row->jam_selesai ?: '-'));

        return [
            $row->nama,
            $row->tanggal_main,
            $jamMain,
            $row->jenis_transfer,
            $row->tanggal_transfer,
            $row->nominal,
            $row->catatan,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 18,
            'C' => 15,
            'D' => 15,
            'E' => 18,
            'F' => 15,
            'G' => 40,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. Set Title di A1 & Merge A1:G1
                $sheet->setCellValue('A1', 'Pembayaran Lapangan dan Coaching ke Rekening PT Adinata Perkasa Utama');
                $sheet->mergeCells('A1:G1');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => '000000'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // 2. Style Header di Baris 5 (FgColor #70AD47, Teks Putih Bold)
                $sheet->getStyle('A5:G5')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '70AD47'],
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // 3. Set Tinggi Baris
                $sheet->getRowDimension(1)->setRowHeight(24);
                $sheet->getRowDimension(5)->setRowHeight(32);

                // 4. Hitung Baris Terakhir Data
                $highestRow = $sheet->getHighestRow(); // Contoh: Baris 15 jika ada 10 data
                $dataRange = "A5:G{$highestRow}";

                // 5. Terapkan Border Tipis ke Seluruh Tabel (Header + Isi Data)
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
            },
        ];
    }
}
