<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LedgerController extends Controller
{
    public function home(): View
    {
        return view('ledger.home');
    }

    public function showLogin(string $role): View|RedirectResponse
    {
        abort_unless(in_array($role, ['admin', 'superadmin'], true), 404);

        if (session('ledger_role') === $role) {
            return redirect()->route($role);
        }

        return view('ledger.login', compact('role'));
    }

    public function login(Request $request, string $role): RedirectResponse
    {
        abort_unless(in_array($role, ['admin', 'superadmin'], true), 404);

        $request->validate(['password' => ['required', 'string']]);
        $password = $role === 'admin' ? 'admin123' : 'super123';

        if (!hash_equals($password, $request->string('password')->toString())) {
            return back()->withErrors(['password' => 'Kata sandi salah. Coba lagi.']);
        }

        $request->session()->regenerate();
        $request->session()->put('ledger_role', $role);

        return redirect()->route($role);
    }

    public function logout(Request $request, string $role): RedirectResponse
    {
        $request->session()->forget('ledger_role');
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function admin(): View
    {
        return view('ledger.admin', [
            'transactions' => Transaction::latest('tanggal_transfer')->get(),
            'editing' => null,
        ]);
    }

    public function edit(Transaction $transaction): View
    {
        return view('ledger.admin', [
            'transactions' => Transaction::latest('tanggal_transfer')->get(),
            'editing' => $transaction,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tanggal_main' => ['required', 'date'],
            'jenis_transfer' => ['required', 'in:Qris,Transfer,Cash'],
            'tanggal_transfer' => ['required', 'date'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i'],
            'catatan' => ['nullable', 'string'],
            'gambar_bukti' => ['nullable', 'image', 'max:4096'],
        ]);

        $data['okupansi_jam'] = $this->duration($data['jam_mulai'], $data['jam_selesai']);
        if ($request->hasFile('gambar_bukti')) {
            $data['gambar_bukti'] = $request->file('gambar_bukti')->store('bukti', 'public');
        }

        Transaction::create($data);

        return back()->with('success', 'Transaksi berhasil disimpan.');
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tanggal_main' => ['required', 'date'],
            'jenis_transfer' => ['required', 'in:Qris,Transfer,Cash'],
            'tanggal_transfer' => ['required', 'date'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i'],
            'catatan' => ['nullable', 'string'],
            'gambar_bukti' => ['nullable', 'image', 'max:4096'],
        ]);

        $data['okupansi_jam'] = $this->duration($data['jam_mulai'], $data['jam_selesai']);
        if ($request->hasFile('gambar_bukti')) {
            if ($transaction->gambar_bukti) {
                Storage::disk('public')->delete($transaction->gambar_bukti);
            }
            $data['gambar_bukti'] = $request->file('gambar_bukti')->store('bukti', 'public');
        }

        $transaction->update($data);

        return redirect()->route('admin')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
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

    public function export()
    {
        $rows = Transaction::latest('tanggal_transfer')->get();
        $csv = "ATAS NAMA / KOMUNITAS,TGL/BULAN MAIN,JAM MAIN,PEMBAYARAN,TGL PEMBAYARAN,NOMINAL,NOTE\n";
        foreach ($rows as $row) {
            $values = [$row->nama, $row->tanggal_main, "{$row->jam_mulai} - {$row->jam_selesai}", $row->jenis_transfer, $row->tanggal_transfer, $row->nominal, $row->catatan];
            $csv .= implode(',', array_map(fn ($value) => '"' . str_replace('"', '""', (string) $value) . '"', $values)) . "\n";
        }

        return response()->streamDownload(fn () => print $csv, 'transaksi-' . now()->format('Y-m-d') . '.csv');
    }

    private function duration(string $start, string $end): float
    {
        $minutes = (strtotime($end) - strtotime($start)) / 60;
        if ($minutes <= 0) $minutes += 1440;

        return round($minutes / 60, 2);
    }
}