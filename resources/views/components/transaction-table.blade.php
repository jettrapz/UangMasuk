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
                            <a href="{{ asset('storage/' . $transaction->gambar_bukti) }}" target="_blank">Lihat</a>
                        @else
                            -
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
                                        <x-icon name="heroicon-o-pencil" class="w-4 h-4 inline mr-1 " />
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

<script>
    document.querySelectorAll('[data-modal-open]').forEach((button) => {
        button.addEventListener('click', () => {
            document.getElementById(button.dataset.modalOpen)?.showModal();
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('dialog')?.close();
        });
    });

    document.querySelectorAll('.confirm-modal').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.close();
            }
        });
    });
</script>