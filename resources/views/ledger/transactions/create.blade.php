@extends('layouts.layout')

@section('content')
  @php
    $isSuper = Auth::user()->role === 'superadmin';
    $brandMark = $isSuper ? 'S' : 'A';
    $roleLabel = $isSuper ? 'Super Admin' : 'Admin';
    $dashRoute = $isSuper ? 'superadmin' : 'admin.dashboard';
    $dashPattern = $isSuper ? 'superadmin' : 'admin.dashboard';
    $txCreate = $isSuper ? 'superadmin.transactions.create' : 'admin.transactions.create';
    $txPattern = $isSuper ? 'superadmin.transactions.*' : 'admin.transactions.*';
    $histRoute = $isSuper ? 'superadmin.transactions.history' : 'admin.transactions.history';
    $histPattern = $isSuper ? 'superadmin.transactions.history' : 'admin.transactions.history';
    $storeRoute = $isSuper ? 'superadmin.transactions.store' : 'admin.transactions.store';
    $updateRoute = $isSuper ? 'superadmin.transactions.update' : 'admin.transactions.update';
  @endphp
  <div class="app-shell">
    <x-sidebar :brand-mark="$brandMark" :role-label="$roleLabel" :dashboard-route="$dashRoute" :dashboard-label="$isSuper ? 'Dashboard Super Admin' : 'Dashboard'" :dashboard-pattern="$dashPattern" :transactions-route="$txCreate" :transactions-pattern="$txPattern" :history-route="$histRoute" :history-pattern="$histPattern" />
    <main class="main">
      <div class="topbar"><div><div class="eyebrow">{{ $roleLabel }}</div><h1>{{ $isSuper ? 'Input Transaksi' : 'Input Uang Masuk' }}</h1><p class="sub">Catat transaksi baru lengkap dengan bukti transfer dan data okupansi.</p></div></div>
      @if(session('success'))<x-alert type="success" :message="session('success')" />@endif
      @if($errors->any())<x-alert type="error" :message="$errors->first()" />@endif
      <div class="card">
        <div class="card-title">{{ $editing ? 'Edit Transaksi' : 'Form Transaksi' }}</div>
        @if($editing)<div class="edit-banner">Mengedit transaksi: {{ $editing->nama }} <a class="btn btn-ghost btn-sm" href="{{ route($txCreate) }}">Batal Edit</a></div>@endif
        <form method="POST" action="{{ $editing ? route($updateRoute, $editing) : route($storeRoute) }}" enctype="multipart/form-data">
          @csrf @if($editing) @method('PUT') @endif
          <x-transaction-form :value="$editing?->nama" :dateValue="$editing?->tanggal_main?->format('Y-m-d') ?? now()->toDateString()" :transferDateValue="$editing?->tanggal_transfer?->format('Y-m-d') ?? now()->toDateString()" :selectedType="$editing?->jenis_transfer" :numericValue="$editing?->nominal" :timeValue="$editing?->jam_mulai ? substr($editing->jam_mulai, 0, 5) : null" :endTimeValue="$editing?->jam_selesai ? substr($editing->jam_selesai, 0, 5) : null" :notes="$editing?->catatan" />
          @if($editing && $editing->gambar_bukti)<small>Bukti saat ini: <a href="{{ asset('storage/' . $editing->gambar_bukti) }}" target="_blank">Lihat gambar</a>. Upload baru untuk menggantinya.</small><br>@endif
          <button class="btn btn-primary" type="submit">{{ $editing ? 'Update Transaksi' : 'Simpan Transaksi' }}</button>
        </form>
      </div>
    </main>
  </div>
@endsection
