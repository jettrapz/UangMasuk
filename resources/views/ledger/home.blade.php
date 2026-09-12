@extends('ledger.layout')

@section('content')
<div class="screen">
    <div class="landing">
        <div class="landing-inner">
            <div class="eyebrow">Sistem Pencatatan Transaksi</div>
            <h1>Ledger</h1>
            <p class="lead">Catat uang masuk beserta bukti transfer, lalu pantau okupansi dan pendapatan bulanan.</p>
            <div class="role-grid">
                <a class="role-card" href="{{ route('login') }}">
                    <div class="icon">+</div>
                    <h3>Login</h3>
                    <p>Masuk dengan email dan kata sandi.</p>
                    <span class="go">Buka halaman login &rarr;</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
