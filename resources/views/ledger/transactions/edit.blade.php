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
    $updateRoute = $isSuper ? 'superadmin.transactions.update' : 'admin.transactions.update';
  @endphp
  <div class="app-shell">
    <x-sidebar :brand-mark="$brandMark" :role-label="$roleLabel" :dashboard-route="$dashRoute" :dashboard-label="$isSuper ? 'Dashboard Super Admin' : 'Dashboard'" :dashboard-pattern="$dashPattern" :transactions-route="$txCreate" :transactions-pattern="$txPattern" :history-route="$histRoute" :history-pattern="$histPattern" />
    <main class="main">
      <div class="topbar"><div><div class="eyebrow">{{ $roleLabel }}</div><h1>Edit Transaksi</h1><p class="sub">Perbarui data transaksi.</p></div></div>
      @if(session('success'))<x-alert type="success" :message="session('success')" />@endif
      @if($errors->any())<x-alert type="error" :message="$errors->first()" />@endif
      <div class="card">
        <div class="card-title">Edit Transaksi: {{ $editing->nama }}</div>
        <div class="edit-banner">Mengedit transaksi: {{ $editing->nama }} <a class="btn btn-ghost btn-sm" href="{{ route($txCreate) }}">Batal Edit</a></div>
        <form method="POST" action="{{ route($updateRoute, $editing) }}" enctype="multipart/form-data">
          @csrf @method('PUT')
          <x-transaction-form :value="$editing->nama" :dateValue="$editing->tanggal_main->format('Y-m-d')" :transferDateValue="$editing->tanggal_transfer->format('Y-m-d')" :selectedType="$editing->jenis_transfer" :numericValue="$editing->nominal" :timeValue="substr($editing->jam_mulai, 0, 5)" :endTimeValue="substr($editing->jam_selesai, 0, 5)" :notes="$editing->catatan" />
          @if($editing->gambar_bukti)<small>Bukti saat ini: <a href="{{ asset('storage/' . $editing->gambar_bukti) }}" target="_blank">Lihat gambar</a>. Upload baru untuk menggantinya.</small><br>@endif
          <button class="btn btn-primary" type="submit">Update Transaksi</button>
        </form>
      </div>
    </main>
  </div>
@endsection
