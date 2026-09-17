<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
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
}
