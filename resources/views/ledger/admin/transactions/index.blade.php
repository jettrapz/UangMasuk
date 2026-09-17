@extends('layouts.layout')

@section('content')
  <div class="app-shell">
    <x-sidebar brand-mark="A" role-label="Admin" dashboard-route="admin.dashboard" dashboard-label="Dashboard"
      dashboard-pattern="admin.dashboard" transactions-route="admin.transactions.create"
      transactions-pattern="admin.transactions.*" />
    <main class="main">
      <div class="topbar">
        <div>
          <div class="eyebrow">Admin</div>
          <h1>Input Uang Masuk</h1>
          <p class="sub">Catat transaksi baru lengkap dengan bukti transfer dan data okupansi.</p>
        </div>
        <div class="topbar-actions">
          <a class="btn btn-teal" href="{{ route('admin.dashboard') }}">Dashboard</a>
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
              href="{{ route('admin.transactions.create') }}">Batal
        Edit</a></div>@endif
        <form method="POST" action="{{ $editing
    ? route('admin.transactions.update', $editing)
    : route('admin.transactions.store') }}" enctype="multipart/form-data">
          @csrf
          @if($editing) @method('PUT') @endif
          <x-transaction-form :value="old('nama', $editing?->nama)" :dateValue="old('tanggal_main', $editing?->tanggal_main?->format('Y-m-d') ?? now()->toDateString())"
            :transferDateValue="old('tanggal_transfer', $editing?->tanggal_transfer?->format('Y-m-d') ?? now()->toDateString())" :selectedType="old('jenis_transfer', $editing?->jenis_transfer)"
            :numericValue="old('nominal', $editing?->nominal)" :timeValue="old('jam_mulai', $editing?->jam_mulai ? substr($editing->jam_mulai, 0, 5) : null)" :endTimeValue="old('jam_selesai', $editing?->jam_selesai ? substr($editing->jam_selesai, 0, 5) : null)" :notes="old('catatan', $editing?->catatan)" />
          @if($editing && $editing->gambar_bukti)<small>Bukti saat ini: <a
            href="{{ asset('storage/' . $editing->gambar_bukti) }}" target="_blank">Lihat gambar</a>. Upload baru untuk
          menggantinya.</small><br>@endif
          <button class="btn btn-primary" type="submit">{{ $editing ? 'Update Transaksi' : 'Simpan Transaksi' }}</button>
        </form>
      </div>
      <div class="card">
        <div class="card-title">Riwayat Transaksi</div>
        <x-transaction-table :transactions="$transactions" :actions="true" edit-route="admin.transactions.edit"
          delete-route="admin.transactions.destroy" />
      </div>
    </main>
  </div>
@endsection