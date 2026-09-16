<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LedgerController extends Controller
{
    public function home(): View
    {
        return view('ledger.home');
    }

    public function admin(): View
    {
        return view('ledger.admin', [
            'transactions' => $this->transactionsForCurrentUser(),
            'editing' => null,
        ]);
    }

    public function superadminInput(): View
    {
        return view('ledger.admin', [
            'transactions' => Transaction::latest('tanggal_transfer')->get(),
            'editing' => null,
        ]);
    }

    public function edit(Transaction $transaction): View
    {
        $this->authorizeTransaction($transaction);

        return view('ledger.admin', [
            'transactions' => $this->transactionsForCurrentUser(),
            'editing' => $transaction,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->transactionRules());

        $data['okupansi_jam'] = $this->duration($data['jam_mulai'], $data['jam_selesai']);
        $data['created_by'] = Auth::id();
        if ($request->hasFile('gambar_bukti')) {
            $data['gambar_bukti'] = $request->file('gambar_bukti')->store('bukti', 'public');
        }

        Transaction::create($data);

        return redirect()->route($this->inputRoute())
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorizeTransaction($transaction);
        $data = $request->validate($this->transactionRules());

        $data['okupansi_jam'] = $this->duration($data['jam_mulai'], $data['jam_selesai']);
        $data['updated_by'] = Auth::id();
        if ($request->hasFile('gambar_bukti')) {
            $oldImage = $transaction->gambar_bukti;
            $data['gambar_bukti'] = $request->file('gambar_bukti')->store('bukti', 'public');
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $transaction->update($data);

        return redirect()->route($this->inputRoute())
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorizeTransaction($transaction);
        $transaction->delete();

        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    public function superadmin(Request $request): View
    {
        $query = Transaction::query();
        if ($request->filled('month')) {
            $query->whereRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') = ?", [$request->string('month')]);
        }

        $transactions = $query->latest('tanggal_transfer')->get();
        $months = Transaction::query()->selectRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') month, COUNT(*) count, SUM(nominal) total_nominal, SUM(okupansi_jam) total_okupansi")
            ->groupBy('month')->orderByDesc('month')->get();
        $totalNominal = $transactions->sum('nominal');

        return view('ledger.superadmin', compact('transactions', 'months', 'totalNominal'));
    }

    public function adminDashboard(Request $request): View
    {
        $query = Transaction::query();
        $query->where('created_by', Auth::id());
        if ($request->filled('month')) {
            $query->whereRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') = ?", [$request->string('month')]);
        }

        $transactions = $query->latest('tanggal_transfer')->get();
        $months = Transaction::query()
            ->where('created_by', Auth::id())
            ->selectRaw("DATE_FORMAT(tanggal_transfer, '%Y-%m') month, COUNT(*) count, SUM(nominal) total_nominal, SUM(okupansi_jam) total_okupansi")
            ->groupBy('month')->orderByDesc('month')->get();
        $totalNominal = $transactions->sum('nominal');

        return view('ledger.admin-dashboard', compact('transactions', 'months', 'totalNominal'));
    }

    public function export()
    {
        $query = Transaction::query();
        if (Auth::user()->role === 'admin') {
            $query->where('created_by', Auth::id());
        }
        
        $rows = $query->latest('tanggal_transfer')->get();
        $csv = "ATAS NAMA / KOMUNITAS,TGL/BULAN MAIN,JAM MAIN,PEMBAYARAN,TGL PEMBAYARAN,NOMINAL,NOTE\n";
        foreach ($rows as $row) {
            $values = [$row->nama, $row->tanggal_main, "{$row->jam_mulai} - {$row->jam_selesai}", $row->jenis_transfer, $row->tanggal_transfer, $row->nominal, $row->catatan];
            $csv .= implode(',', array_map(fn ($value) => '"' . str_replace('"', '""', (string) $value) . '"', $values)) . "\n";
        }

        $filename = Auth::user()->role === 'admin'
            ? 'transaksi-admin-' . now()->format('Y-m-d') . '.csv'
            : 'transaksi-' . now()->format('Y-m-d') . '.csv';
            
        return response()->streamDownload(fn () => print $csv, $filename);
    }

    private function duration(string $start, string $end): float
    {
        $minutes = (strtotime($end) - strtotime($start)) / 60;
        if ($minutes <= 0) $minutes += 1440;

        return round($minutes / 60, 2);
    }

    private function transactionRules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'tanggal_main' => ['required', 'date'],
            'jenis_transfer' => ['required', 'in:Qris,Transfer,Cash'],
            'tanggal_transfer' => ['required', 'date'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i'],
            'catatan' => ['nullable', 'string'],
            'gambar_bukti' => ['nullable', 'image', 'max:4096'],
        ];
    }

    private function transactionsForCurrentUser()
    {
        return (Auth::user()->role === 'superadmin'
            ? Transaction::query()
            : Transaction::where('created_by', Auth::id()))
            ->latest('tanggal_transfer')
            ->get();
    }

    private function authorizeTransaction(Transaction $transaction): void
    {
        abort_unless(
            Auth::user()->role === 'superadmin' || $transaction->created_by === Auth::id(),
            403
        );
    }

    private function inputRoute(): string
    {
        return Auth::user()->role === 'superadmin'
            ? 'superadmin.transactions.create'
            : 'admin';
    }
}