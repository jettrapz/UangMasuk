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
    $editRoute = $isSuper ? 'superadmin.transactions.edit' : 'admin.transactions.edit';
    $deleteRoute = $isSuper ? 'superadmin.transactions.destroy' : 'admin.transactions.destroy';
  @endphp
  <div class="app-shell">
    <x-sidebar :brand-mark="$brandMark" :role-label="$roleLabel" :dashboard-route="$dashRoute" :dashboard-label="$isSuper ? 'Dashboard Super Admin' : 'Dashboard'" :dashboard-pattern="$dashPattern" :transactions-route="$txCreate" :transactions-pattern="$txPattern" :history-route="$histRoute" :history-pattern="$histPattern" />
    <main class="main">
      <div class="topbar"><div><div class="eyebrow">{{ $roleLabel }}</div><h1>Riwayat Transaksi</h1><p class="sub">{{ $isSuper ? 'Daftar seluruh transaksi — kelola via aksi Edit/Hapus.' : 'Daftar transaksi milik Anda — kelola via aksi Edit/Hapus.' }}</p></div></div>
      @if(session('success'))<x-alert type="success" :message="session('success')" />@endif
      @if($errors->any())<x-alert type="error" :message="$errors->first()" />@endif
      <div class="card"><div class="card-title">Riwayat Transaksi</div><x-transaction-table :transactions="$transactions" :actions="true" :edit-route="$editRoute" :delete-route="$deleteRoute" /></div>
    </main>
  </div>
@endsection
