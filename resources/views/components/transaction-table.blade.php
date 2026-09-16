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
                    <td>@if($transaction->gambar_bukti)<a href="{{ asset('storage/' . $transaction->gambar_bukti) }}"
                    target="_blank">Lihat</a>@else-@endif</td>
                    <td>{{ $transaction->nama }}</td>
                    <td>{{ $transaction->tanggal_main->format('d M Y') }}</td>
                    <td>{{ $transaction->jenis_transfer }}</td>
                    <td>{{ $transaction->tanggal_transfer->format('d M Y') }}</td>
                    <td class="mono">Rp {{ number_format($transaction->nominal, 0, ',', '.') }}</td>
                    <td>{{ $transaction->jam_mulai }} -
                        {{ $transaction->jam_selesai }}<br>{{ number_format($transaction->okupansi_jam, 2, ',', '.') }} jam
                    </td>
                    <td>{{ $transaction->catatan ?: '-' }}</td>
                    @if($actions ?? true)
                        <td>
                            @if(isset($actionButtons))
                                {{ $actionButtons }}
                            @else
                                @if($editRoute ?? null)
                                    <a class="btn btn-ghost btn-sm" href="{{ route($editRoute, $transaction) }}">Edit</a>
                                @endif
                                @if($deleteRoute ?? null)
                                    <form method="POST" action="{{ route($deleteRoute, $transaction) }}" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-ghost btn-sm" type="submit">Hapus</button>
                                    </form>
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
