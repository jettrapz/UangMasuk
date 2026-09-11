<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Transaction::query()
            ->latest('tanggal_transfer')
            ->latest('id')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tanggal_main' => ['required', 'date'],
            'jenis_transfer' => ['required', 'in:Qris,Transfer,Cash'],
            'tanggal_transfer' => ['required', 'date'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string'],
            'jam_mulai' => ['nullable', 'date_format:H:i'],
            'jam_selesai' => ['nullable', 'date_format:H:i'],
            'okupansi_jam' => ['nullable', 'numeric', 'min:0'],
            'gambar_bukti' => ['nullable', 'string'],
        ]);

        return response()->json(Transaction::create($data), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Transaction::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaction = Transaction::findOrFail($id);
        $data = $request->validate([
            'nama' => ['sometimes', 'string', 'max:255'],
            'tanggal_main' => ['sometimes', 'date'],
            'jenis_transfer' => ['sometimes', 'in:Qris,Transfer,Cash'],
            'tanggal_transfer' => ['sometimes', 'date'],
            'nominal' => ['sometimes', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string'],
            'jam_mulai' => ['nullable', 'date_format:H:i'],
            'jam_selesai' => ['nullable', 'date_format:H:i'],
            'okupansi_jam' => ['nullable', 'numeric', 'min:0'],
            'gambar_bukti' => ['nullable', 'string'],
        ]);
        $transaction->update($data);
        return $transaction->fresh();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Transaction::findOrFail($id)->delete();
        return response()->noContent();
    }
}
