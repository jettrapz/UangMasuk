<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Bukti</th>
                <th>Nama</th>
                <th>Tgl Main</th>
                <th>Jenis</th>
                <th>Tgl Transfer</th>
                <th>Nominal</th>
                <th>Jam / Okupansi</th>
                <th>Catatan</th>
                @if($actions ?? true)
                    <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>
                        @if($transaction->gambar_bukti)
                            {{-- Thumbnail Gambar --}}
                            <div class="bukti-thumbnail-wrap">
                                <img src="{{ asset('storage/' . $transaction->gambar_bukti) }}"
                                    alt="Bukti Transfer {{ $transaction->nama }}" class="bukti-img-thumb"
                                    data-modal-open="preview-bukti-{{ $transaction->getKey() }}" title="Klik untuk memperbesar">
                            </div>

                            {{-- Modal Preview Ukuran Penuh --}}
                            <dialog class="confirm-modal image-preview-modal" id="preview-bukti-{{ $transaction->getKey() }}">
                                <div class="confirm-modal-content image-modal-content">
                                    <div class="confirm-modal-header">
                                        <h2>Bukti Transfer - {{ $transaction->nama }}</h2>
                                        <button class="modal-close" type="button" data-modal-close
                                            aria-label="Tutup">&times;</button>
                                    </div>
                                    <div class="modal-image-body">
                                        <img src="{{ asset('storage/' . $transaction->gambar_bukti) }}"
                                            alt="Bukti Transfer {{ $transaction->nama }}" class="full-preview-img">
                                    </div>
                                    <div class="confirm-modal-actions" style="justify-content: space-between;">
                                        <a href="{{ asset('storage/' . $transaction->gambar_bukti) }}" target="_blank"
                                            class="btn btn-ghost btn-sm">Buka Tab Baru</a>
                                        <button class="btn btn-teal btn-sm" type="button" data-modal-close>Tutup</button>
                                    </div>
                                </div>
                            </dialog>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $transaction->nama }}</td>
                    <td>{{ $transaction->tanggal_main->format('d M Y') }}</td>
                    <td>{{ $transaction->jenis_transfer }}</td>
                    <td>{{ $transaction->tanggal_transfer->format('d M Y') }}</td>
                    <td class="mono">Rp {{ number_format($transaction->nominal, 0, ',', '.') }}</td>
                    <td>
                        {{ $transaction->jam_mulai }} - {{ $transaction->jam_selesai }}<br>
                        {{ number_format($transaction->okupansi_jam, 2, ',', '.') }} jam
                    </td>
                    <td>{{ $transaction->catatan ?: '-' }}</td>
                    @if($actions ?? true)
                        <td>
                            @if(isset($actionButtons) && $actionButtons)
                                {{ $actionButtons }}
                            @else
                                @if($editRoute ?? null)
                                    <a class="btn btn-ghost btn-sm btn-icon-edit" href="{{ route($editRoute, $transaction) }}">
                                        <x-icon name="heroicon-o-pencil" class="w-4 h-4 inline mr-1" />
                                    </a>
                                @endif

                                @if($deleteRoute ?? null)
                                    <button class="btn btn-danger btn-sm" type="button"
                                        data-modal-open="delete-transaction-{{ $transaction->getKey() }}"
                                        aria-label="Hapus transaksi {{ $transaction->nama }}">
                                        <x-icon name="heroicon-o-trash" class="w-4 h-4 inline mr-1" />
                                    </button>

                                    <dialog class="confirm-modal" id="delete-transaction-{{ $transaction->getKey() }}">
                                        <div class="confirm-modal-content">
                                            <div class="confirm-modal-header">
                                                <h2>Hapus transaksi?</h2>
                                                <button class="modal-close" type="button" data-modal-close
                                                    aria-label="Tutup">&times;</button>
                                            </div>
                                            <p>Transaksi atas nama <strong>{{ $transaction->nama }}</strong> akan dihapus.</p>
                                            <div class="confirm-modal-actions">
                                                <button class="btn btn-ghost btn-sm" type="button" data-modal-close>Batal</button>
                                                <form method="POST" action="{{ route($deleteRoute, $transaction) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>
                                @endif
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $actions ?? true ? '9' : '8' }}">Belum ada transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- CSS Tambahan untuk Thumbnail & Modal Preview --}}
<style>
    .bukti-img-thumb {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .bukti-img-thumb:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .image-modal-content {
        max-width: 500px;
        width: 90%;
    }

    .modal-image-body {
        padding: 10px 0;
        text-align: center;
    }

    .full-preview-img {
        max-width: 100%;
        max-height: 70vh;
        border-radius: 8px;
        object-fit: contain;
    }
</style>