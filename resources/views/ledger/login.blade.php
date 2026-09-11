@extends('ledger.layout')

@section('content')
<div class="gate">
    <div class="gate-card">
        <div class="brand">
            <div class="brand-mark">{{ $role === 'admin' ? 'A' : 'S' }}</div>
            <div class="brand-text"><div class="name">Ledger</div><div class="role">Login {{ ucfirst($role) }}</div></div>
        </div>
        <h2>Masuk sebagai {{ ucfirst($role) }}</h2>
        <p class="desc">Masukkan kata sandi untuk melanjutkan.</p>
        <form method="POST" action="{{ route('login.store', $role) }}">
            @csrf
            <div class="field">
                <label for="password">Kata sandi</label>
                <input type="password" id="password" name="password" class="input" required autofocus>
            </div>
            @error('password')<div class="gate-error">{{ $message }}</div>@enderror
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Masuk</button>
        </form>
        <p class="footnote" style="margin-top:16px;">Demo password: <span class="mono">admin123</span></p>
        <p class="footnote"><a href="{{ route('home') }}">&larr; Kembali ke beranda</a></p>
    </div>
</div>
@endsection
