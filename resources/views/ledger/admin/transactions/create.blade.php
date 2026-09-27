@extends('layouts.layout')

@section('content')
  <div class="app-shell">
    <x-sidebar brand-mark="A" role-label="Admin" dashboard-route="admin.dashboard" dashboard-label="Dashboard"
      dashboard-pattern="admin.dashboard" transactions-route="admin.transactions.create"
      transactions-pattern="admin.transactions.*" history-route="admin.transactions.history"
      history-pattern="admin.transactions.history" />
    <main class="main">
      <div class="topbar">
        <div>
          <div class="eyebrow">Admin</div>
          <h1>Input Uang Masuk</h1>
          <p class="sub">Catat transaksi baru lengkap dengan bukti transfer dan data okupansi.</p>
        </div>
        {{-- <div class="topbar-actions">
          <a class="btn btn-teal" href="{{ route('admin.transactions.history') }}">Riwayat</a>
          <a class="btn btn-ghost btn-sm" href="{{ route('admin.dashboard') }}">Dashboard</a>
        </div> --}}
      </div>
      @if(session('success'))<x-alert type="success" :message="session('success')" />@endif
      @if($errors->any())<x-alert type="error" :message="$errors->first()" />@endif
      <div class="card">
        <div class="card-title">{{ $editing ? 'Edit Transaksi' : 'Form Transaksi' }}</div>
        @if($editing)
          <div class="edit-banner">Mengedit transaksi: {{ $editing->nama }} <a class="btn btn-ghost btn-sm"
        href="{{ route('admin.transactions.create') }}">Batal Edit</a></div>@endif
        <form method="POST"
          action="{{ $editing ? route('admin.transactions.update', $editing) : route('admin.transactions.store') }}"
          enctype="multipart/form-data">
          @csrf
          @if($editing) @method('PUT') @endif
          <x-transaction-form :value="$editing?->nama" :dateValue="$editing?->tanggal_main?->format('Y-m-d') ?? now()->toDateString()" :transferDateValue="$editing?->tanggal_transfer?->format('Y-m-d') ?? now()->toDateString()" :selectedType="$editing?->jenis_transfer" :numericValue="$editing?->nominal"
            :timeValue="$editing?->jam_mulai ? substr($editing->jam_mulai, 0, 5) : null"
            :endTimeValue="$editing?->jam_selesai ? substr($editing->jam_selesai, 0, 5) : null"
            :notes="$editing?->catatan" />
          @if($editing && $editing->gambar_bukti)<small>Bukti saat ini: <a
            href="{{ asset('storage/' . $editing->gambar_bukti) }}" target="_blank">Lihat gambar</a>. Upload baru untuk
          menggantinya.</small><br>@endif
          <button class="btn btn-primary" type="submit">{{ $editing ? 'Update Transaksi' : 'Simpan Transaksi' }}</button>
        </form>
      </div>
    </main>
  </div>
@endsection