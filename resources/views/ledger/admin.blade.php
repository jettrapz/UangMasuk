@extends('ledger.layout')

@section('content')
  <div class="app-shell">
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-mark">A</div>
        <div class="brand-text">
          <div class="name">Ledger</div>
          <div class="role">Admin - {{ Auth::user()->name }}</div>
        </div>
      </div>
      <nav class="nav">
        <div class="nav-group-label">Menu</div><a href="{{ route('admin') }}">Input Transaksi</a><a
          href="{{ route('admin.dashboard') }}">Dashboard</a>
      </nav>
      <div class="sidebar-foot">
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost btn-sm"
            style="width:100%;">Keluar</button></form>
      </div>
    </aside>
    <main class="main">
      <div class="topbar">
        <div>
          <div class="eyebrow">Admin</div>
          <h1>Input Uang Masuk</h1>
          <p class="sub">Catat transaksi baru lengkap dengan bukti transfer dan data okupansi.</p>
        </div>
        <div class="topbar-actions"><a class="btn btn-teal" href="{{ route('superadmin') }}">Dashboard Super Admin</a>
        </div>
      </div>
      @if(session('success'))
      <div class="gate-error" style="color:#35b3a3;">{{ session('success') }}</div>@endif
      @if($errors->any())
      <div class="gate-error">{{ $errors->first() }}</div>@endif
      <div class="card" style="margin-bottom:22px;">
        <div class="card-title">{{ $editing ? 'Edit Transaksi' : 'Form Transaksi' }}</div>
        @if($editing)
          <div class="edit-banner">Mengedit transaksi: {{ $editing->nama }} <a class="btn btn-ghost btn-sm"
        href="{{ route('admin') }}">Batal Edit</a></div>@endif
        <form method="POST"
          action="{{ $editing ? route('admin.transactions.update', $editing) : route('admin.transactions.store') }}"
          enctype="multipart/form-data">
          @csrf
          @if($editing) @method('PUT') @endif
          <div class="form-grid">
            <div class="field"><label for="nama">Nama</label><input class="input" id="nama" name="nama" required
                value="{{ old('nama', $editing?->nama) }}"></div>
            <div class="field"><label for="tanggal_main">Tanggal Main</label><input type="date" class="input"
                id="tanggal_main" name="tanggal_main" required
                value="{{ old('tanggal_main', $editing?->tanggal_main?->format('Y-m-d') ?? now()->toDateString()) }}">
            </div>
            <div class="field"><label for="jenis_transfer">Jenis Transfer</label><select class="input" id="jenis_transfer"
                name="jenis_transfer">
                <option value="Qris" @selected(old('jenis_transfer', $editing?->jenis_transfer) === 'Qris')>QRIS</option>
                <option value="Transfer" @selected(old('jenis_transfer', $editing?->jenis_transfer) === 'Transfer')>Transfer
                </option>
                <option value="Cash" @selected(old('jenis_transfer', $editing?->jenis_transfer) === 'Cash')>Cash</option>
              </select></div>
            <div class="field"><label for="tanggal_transfer">Tanggal Pembayaran</label><input type="date" class="input"
                id="tanggal_transfer" name="tanggal_transfer" required
                value="{{ old('tanggal_transfer', $editing?->tanggal_transfer?->format('Y-m-d') ?? now()->toDateString()) }}">
            </div>
            <div class="field"><label for="nominal">Nominal (Rp)</label><input type="number" class="input" id="nominal"
                name="nominal" min="0" required value="{{ old('nominal', $editing?->nominal) }}"></div>
            <div class="field"><label for="jam_mulai">Jam Mulai</label><input type="time" class="input" id="jam_mulai"
                name="jam_mulai" required
                value="{{ old('jam_mulai', $editing?->jam_mulai ? substr($editing->jam_mulai, 0, 5) : null) }}"></div>
            <div class="field"><label for="jam_selesai">Jam Selesai</label><input type="time" class="input"
                id="jam_selesai" name="jam_selesai" required
                value="{{ old('jam_selesai', $editing?->jam_selesai ? substr($editing->jam_selesai, 0, 5) : null) }}">
            </div>
            <div class="field"><label for="gambar_bukti">Bukti Transfer</label><input type="file" class="input"
                id="gambar_bukti" name="gambar_bukti" accept="image/*"><small>JPG/PNG, maksimal 4MB.</small></div>
            <div class="field" style="grid-column:1/-1;"><label for="catatan">Catatan</label><textarea class="input"
                id="catatan" name="catatan" rows="3">{{ old('catatan', $editing?->catatan) }}</textarea></div>
          </div>
          @if($editing && $editing->gambar_bukti)<small>Bukti saat ini: <a
            href="{{ asset('storage/' . $editing->gambar_bukti) }}" target="_blank">Lihat gambar</a>. Upload baru untuk
          menggantinya.</small><br>@endif
          <button class="btn btn-primary" type="submit">{{ $editing ? 'Update Transaksi' : 'Simpan Transaksi' }}</button>
        </form>
      </div>
      <div class="card">
        <div class="card-title">Riwayat Transaksi</div>
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
                <th>Aksi</th>
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
                  <td><a class="btn btn-ghost btn-sm" href="{{ route('admin.transactions.edit', $transaction) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.transactions.destroy', $transaction) }}">@csrf
                      @method('DELETE')<button class="btn btn-ghost btn-sm" type="submit">Hapus</button></form>
                  </td>
              </tr>@empty<tr>
                <td colspan="9">Belum ada transaksi.</td>
              </tr>@endforelse
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
@endsection