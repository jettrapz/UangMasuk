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
  @endphp
  <div class="app-shell">
    <x-sidebar :brand-mark="$brandMark" :role-label="$roleLabel" :dashboard-route="$dashRoute" :dashboard-label="$isSuper ? 'Dashboard Super Admin' : 'Dashboard'" :dashboard-pattern="$dashPattern" :transactions-route="$txCreate" :transactions-pattern="$txPattern" :history-route="$histRoute" :history-pattern="$histPattern" />
    <main class="main">
      <div class="topbar"><div><div class="eyebrow">{{ $roleLabel }}</div><h1>Input Transaksi</h1><p class="sub">Catat transaksi baru — lihat riwayat di tab Riwayat Transaksi.</p></div></div>
      <div class="card"><div class="card-title">Form Transaksi</div><p class="sub">Gunakan menu <strong>Input Transaksi</strong> untuk menambah data, dan <strong>Riwayat Transaksi</strong> untuk melihat/edit/hapus.</p><a class="btn btn-primary" href="{{ route($txCreate) }}">Buka Form Input</a></div>
    </main>
  </div>
@endsection
