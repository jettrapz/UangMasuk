@extends('layouts.layout')

@section('content')
  <div class="app-shell">
    <x-sidebar brand-mark="S" role-label="Super Admin" dashboard-route="superadmin" dashboard-label="Dashboard Super Admin"
      dashboard-pattern="superadmin" transactions-route="superadmin.transactions.create"
      transactions-pattern="superadmin.transactions.*" history-route="superadmin.transactions.history"
      history-pattern="superadmin.transactions.history" />
    <main class="main">
      <div class="topbar">
        <div>
          <div class="eyebrow">Super Admin</div>
          <h1>Edit Transaksi</h1>
          <p class="sub">Perbarui data transaksi.</p>
        </div>
        <div class="topbar-actions">
          <a class="btn btn-teal" href="{{ route('superadmin.transactions.history') }}">Riwayat</a>
          <a class="btn btn-ghost btn-sm" href="{{ route('superadmin') }}">Dashboard</a>
        </div>
      </div>
      @if(session('success'))<x-alert type="success" :message="session('success')" />@endif
      @if($errors->any())<x-alert type="error" :message="$errors->first()" />@endif
      <div class="card">
        <div class="card-title">Edit Transaksi: {{ $editing->nama }}</div>
        <div class="edit-banner">Mengedit transaksi: {{ $editing->nama }} <a class="btn btn-ghost btn-sm" href="{{ route('superadmin.transactions.create') }}">Batal Edit</a></div>
        <form method="POST" action="{{ route('superadmin.transactions.update', $editing) }}" enctype="multipart/form-data">
          @csrf @method('PUT')
          <x-transaction-form :value="$editing->nama" :dateValue="$editing->tanggal_main->format('Y-m-d')" :transferDateValue="$editing->tanggal_transfer->format('Y-m-d')" :selectedType="$editing->jenis_transfer" :numericValue="$editing->nominal" :timeValue="substr($editing->jam_mulai, 0, 5)" :endTimeValue="substr($editing->jam_selesai, 0, 5)" :notes="$editing->catatan" />
          @if($editing->gambar_bukti)<small>Bukti saat ini: <a href="{{ asset('storage/' . $editing->gambar_bukti) }}" target="_blank">Lihat gambar</a>. Upload baru untuk menggantinya.</small><br>@endif
          <button class="btn btn-primary" type="submit">Update Transaksi</button>
        </form>
      </div>
    </main>
  </div>
@endsection
