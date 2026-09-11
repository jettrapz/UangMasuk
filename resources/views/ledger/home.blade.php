@extends('ledger.layout')

@section('content')
<div class="screen">
    <div class="landing">
        <div class="landing-inner">
            <div class="eyebrow">Sistem Pencatatan Transaksi</div>
            <h1>Ledger</h1>
            <p class="lead">Catat uang masuk beserta bukti transfer, lalu pantau okupansi dan pendapatan bulanan.</p>
            <div class="role-grid">
                <a class="role-card" href="{{ route('login', 'admin') }}">
                    <div class="icon">+</div>
                    <h3>Masuk sebagai Admin</h3>
                    <p>Input transaksi baru dan unggah bukti transfer.</p>
                    <span class="go">Buka halaman input &rarr;</span>
                </a>
                <a class="role-card" href="{{ route('login', 'superadmin') }}">
                    <div class="icon">&#9776;</div>
                    <h3>Masuk sebagai Super Admin</h3>
                    <p>Lihat dashboard, rekap, dan ekspor transaksi.</p>
                    <span class="go">Buka dashboard &rarr;</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
