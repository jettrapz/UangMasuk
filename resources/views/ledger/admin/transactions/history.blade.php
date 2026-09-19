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
          <h1>Riwayat Transaksi</h1>
          <p class="sub">Daftar transaksi milik Anda — kelola via aksi Edit/Hapus.</p>
        </div>
        <div class="topbar-actions">
          <a class="btn btn-teal" href="{{ route('admin.transactions.create') }}">Input Transaksi</a>
          <a class="btn btn-ghost btn-sm" href="{{ route('admin.dashboard') }}">Dashboard</a>
        </div>
      </div>
      @if(session('success'))<x-alert type="success" :message="session('success')" />@endif
      <div class="card">
        <div class="card-title">Riwayat Transaksi</div>
        <x-transaction-table :transactions="$transactions" :actions="true" edit-route="admin.transactions.edit"
          delete-route="admin.transactions.destroy" />
      </div>
    </main>
  </div>
@endsection