<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class TransactionService
{
    public function create(array $data): Transaction
    {
        $data['okupansi_jam'] = $this->calculateDuration($data['jam_mulai'], $data['jam_selesai']);
        $data['created_by'] = Auth::id();

        if (isset($data['gambar_bukti']) && $data['gambar_bukti'] instanceof UploadedFile) {
            $data['gambar_bukti'] = $this->uploadEvidence($data['gambar_bukti']);
        }

        return Transaction::create($data);
    }

    public function update(Transaction $transaction, array $data): bool
    {
        $data['okupansi_jam'] = $this->calculateDuration($data['jam_mulai'], $data['jam_selesai']);
        $data['updated_by'] = Auth::id();

        if (isset($data['gambar_bukti']) && $data['gambar_bukti'] instanceof UploadedFile) {
            $this->deleteOldEvidence($transaction->gambar_bukti);
            $data['gambar_bukti'] = $this->uploadEvidence($data['gambar_bukti']);
        }

        return $transaction->update($data);
    }

    public function delete(Transaction $transaction): bool
    {
        $this->deleteOldEvidence($transaction->gambar_bukti);
        return $transaction->delete();
    }

    public function calculateDuration(string $start, string $end): float
    {
        $minutes = (strtotime($end) - strtotime($start)) / 60;
        if ($minutes <= 0) {
            $minutes += 1440;
        }
        return round($minutes / 60, 2);
    }

    protected function uploadEvidence(UploadedFile $file): string
    {
        return $file->store('bukti', 'public');
    }

    protected function deleteOldEvidence(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
