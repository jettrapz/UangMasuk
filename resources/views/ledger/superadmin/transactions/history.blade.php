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
          <h1>Riwayat Transaksi</h1>
          <p class="sub">Daftar seluruh transaksi — kelola via aksi Edit/Hapus.</p>
        </div>
        <div class="topbar-actions">
          <a class="btn btn-teal" href="{{ route('superadmin.transactions.create') }}">Input Transaksi</a>
          <a class="btn btn-ghost btn-sm" href="{{ route('superadmin') }}">Dashboard</a>
        </div>
      </div>
      @if(session('success'))<x-alert type="success" :message="session('success')" />@endif
      <div class="card">
        <div class="card-title">Riwayat Transaksi</div>
        <x-transaction-table :transactions="$transactions" :actions="true" edit-route="superadmin.transactions.edit"
          delete-route="superadmin.transactions.destroy" />
      </div>
    </main>
  </div>
@endsection