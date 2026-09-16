@extends('layouts.layout')

@section('content')
  <div class="app-shell">
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-mark">S</div>
        <div class="brand-text">
          <div class="name">Ledger</div>
          <div class="role">Super Admin - {{ Auth::user()->name }}</div>
        </div>
      </div>
      <nav class="nav">
        <div class="nav-group-label">Menu</div>
        <a href="{{ route('superadmin') }}">Dashboard Super Admin</a>
        <a href="{{ route('superadmin.transactions.create') }}" class="active">Input Transaksi</a>
      </nav>
      <div class="sidebar-foot">
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost btn-sm"
            style="width:100%;">Keluar</button></form>
      </div>
    </aside>
    <main class="main">
      <div class="topbar">
        <div>
          <div class="eyebrow">Super Admin</div>
          <h1>Input Transaksi</h1>
          <p class="sub">Catat transaksi baru lengkap dengan bukti transfer dan data okupansi.</p>
        </div>
        <div class="topbar-actions">
          <a class="btn btn-teal" href="{{ route('superadmin') }}">Dashboard</a>
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
        href="{{ route('superadmin.transactions.create') }}">Batal Edit</a></div>@endif
        <form method="POST" action="{{ $editing
    ? route('superadmin.transactions.update', $editing)
    : route('superadmin.transactions.store') }}" enctype="multipart/form-data">
          @csrf
          @if($editing) @method('PUT') @endif
          <x-transaction-form :value="old('nama', $editing?->nama)" :dateValue="old('tanggal_main', $editing?->tanggal_main?->format('Y-m-d') ?? now()->toDateString())" :transferDateValue="old('tanggal_transfer', $editing?->tanggal_transfer?->format('Y-m-d') ?? now()->toDateString())" :selectedType="old('jenis_transfer', $editing?->jenis_transfer)" :numericValue="old('nominal', $editing?->nominal)" :timeValue="old('jam_mulai', $editing?->jam_mulai ? substr($editing->jam_mulai, 0, 5) : null)" :endTimeValue="old('jam_selesai', $editing?->jam_selesai ? substr($editing->jam_selesai, 0, 5) : null)" :notes="old('catatan', $editing?->catatan)" />
          @if($editing && $editing->gambar_bukti)<small>Bukti saat ini: <a
            href="{{ asset('storage/' . $editing->gambar_bukti) }}" target="_blank">Lihat gambar</a>. Upload baru untuk
          menggantinya.</small><br>@endif
          <button class="btn btn-primary" type="submit">{{ $editing ? 'Update Transaksi' : 'Simpan Transaksi' }}</button>
        </form>
      </div>
      <div class="card">
        <div class="card-title">Riwayat Transaksi</div>
        <x-transaction-table :transactions="$transactions" :actions="true"
          edit-route="superadmin.transactions.edit" delete-route="superadmin.transactions.destroy" />
      </div>
    </main>
  </div>
@endsection