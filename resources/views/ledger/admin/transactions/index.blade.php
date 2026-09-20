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
          <p class="sub">Catat transaksi baru — lihat riwayat di tab Riwayat Transaksi.</p>
        </div>
        {{-- <div class="topbar-actions">
          <a class="btn btn-teal" href="{{ route('admin.transactions.history') }}">Riwayat</a>
          <a class="btn btn-ghost btn-sm" href="{{ route('admin.dashboard') }}">Dashboard</a>
        </div> --}}
      </div>
      <div class="card">
        <div class="card-title">Form Transaksi</div>
        <p class="sub">Gunakan menu <strong>Input Transaksi</strong> untuk menambah data, dan <strong>Riwayat
            Transaksi</strong> untuk melihat/edit/hapus.</p>
        <a class="btn btn-primary" href="{{ route('admin.transactions.create') }}">Buka Form Input</a>
      </div>
    </main>
  </div>
@endsection