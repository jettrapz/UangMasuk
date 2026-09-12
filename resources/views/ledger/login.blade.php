@extends('ledger.layout')

@section('content')
<div class="gate">
    <div class="gate-card">
        <div class="brand">
            <div class="brand-mark">L</div>
            <div class="brand-text"><div class="name">Ledger</div></div>
        </div>
        <h2>Login Ledger</h2>
        <p class="desc">Masukkan kredensial Anda untuk melanjutkan.</p>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="input" required autofocus value="{{ old('email') }}">
            </div>
            <div class="field">
                <label for="password">Kata sandi</label>
                <input type="password" id="password" name="password" class="input" required>
            </div>
            <div class="field" style="margin-top:8px;">
                <label>
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
            </div>
            @error('email')<div class="gate-error">{{ $message }}</div>@enderror
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Masuk</button>
        </form>
        <p class="footnote" style="margin-top:16px;">Demo: <span class="mono">admin@ledger.local</span> / <span class="mono">admin123</span></p>
        <p class="footnote"><a href="{{ route('home') }}">&larr; Kembali ke beranda</a></p>
    </div>
</div>
@endsection
