@extends('layouts.layout')

@section('content')
  <div class="app-shell">
    <x-sidebar brand-mark="S" role-label="Super Admin" dashboard-route="superadmin"
      dashboard-label="Dashboard Super Admin" dashboard-pattern="superadmin"
      transactions-route="superadmin.transactions.create" transactions-pattern="superadmin.transactions.*"
      history-route="superadmin.transactions.history" history-pattern="superadmin.transactions.history" />
    <main class="main">
      <div class="topbar">
        <div>
          <div class="eyebrow">Super Admin</div>
          <h1>Input Transaksi</h1>
          <p class="sub">Catat transaksi baru — lihat riwayat di tab Riwayat Transaksi.</p>
        </div>
        {{-- <div class="topbar-actions">
          <a class="btn btn-teal" href="{{ route('superadmin.transactions.history') }}">Riwayat</a>
          <a class="btn btn-ghost btn-sm" href="{{ route('superadmin') }}">Dashboard</a>
        </div> --}}
      </div>
      <div class="card">
        <div class="card-title">Form Transaksi</div>
        <p class="sub">Gunakan menu <strong>Input Transaksi</strong> untuk menambah data, dan <strong>Riwayat
            Transaksi</strong> untuk melihat/edit/hapus.</p>
        <a class="btn btn-primary" href="{{ route('superadmin.transactions.create') }}">Buka Form Input</a>
      </div>
    </main>
  </div>
@endsection