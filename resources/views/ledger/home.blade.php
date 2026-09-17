@extends('layouts.layout')

@section('content')
<style>
    :root {
        --bg: #10151b;
        --surface: #1a222b;
        --surface-2: #212b35;
        --surface-3: #283441;
        --border: #2c3945;
        --border-soft: #232e39;
        --text: #eef2f5;
        --text-dim: #96a5b3;
        --text-faint: #5f707e;
        --accent: #e8ac52; /* coin gold */
        --accent-dim: #6b5230;
        --accent-ink: #241a0b;
        --teal: #35b3a3; /* masuk / positif */
        --teal-dim: #17332f;
        --red: #e2636b;
        --red-dim: #3a1f22;
        --font-display: "Space Grotesk", "Segoe UI", sans-serif;
        --font-body: "Inter", "Segoe UI", sans-serif;
        --font-mono: "IBM Plex Mono", monospace;
        --radius: 10px;
        --radius-sm: 6px;
        --shadow-lift: 0 8px 24px rgba(0, 0, 0, .35);
    }

    /* Container Main Layout */
    .ledger-home {
        min-height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 1.5rem;
        background-color: var(--bg);
        font-family: var(--font-body);
        color: var(--text);
    }

    .ledger-container {
        max-width: 960px;
        width: 100%;
        margin: 0 auto;
    }

    /* Hero Section */
    .hero-section {
        text-align: center;
        max-width: 680px;
        margin: 0 auto 3rem auto;
    }

    .eyebrow-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        background-color: var(--teal-dim);
        color: var(--teal);
        font-family: var(--font-mono);
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-bottom: 1.25rem;
        border: 1px solid rgba(53, 179, 163, 0.3);
    }

    .hero-title {
        font-family: var(--font-display);
        font-size: 2.75rem;
        font-weight: 700;
        color: var(--text);
        line-height: 1.2;
        margin-bottom: 1rem;
        letter-spacing: -0.02em;
    }

    .hero-title span {
        color: var(--accent);
    }

    .hero-description {
        font-size: 1.05rem;
        color: var(--text-dim);
        line-height: 1.6;
    }

    /* Action Grid (Login CTA Card) */
    .action-grid {
        display: flex;
        justify-content: center;
        margin-bottom: 3.5rem;
    }

    .login-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        max-width: 580px;
        padding: 1.5rem 2rem;
        background-color: var(--surface);
        border-radius: var(--radius);
        text-decoration: none;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-lift);
        transition: all 0.25s ease;
    }

    .login-card:hover {
        transform: translateY(-3px);
        border-color: var(--accent);
        background-color: var(--surface-2);
    }

    .card-left {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .card-icon {
        width: 52px;
        height: 52px;
        border-radius: var(--radius-sm);
        background-color: var(--accent);
        color: var(--accent-ink);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-weight: bold;
    }

    .card-text h3 {
        font-family: var(--font-display);
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--text);
        margin: 0 0 0.25rem 0;
    }

    .card-text p {
        font-size: 0.875rem;
        color: var(--text-dim);
        margin: 0;
    }

    .card-action {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--accent);
        font-family: var(--font-mono);
        transition: gap 0.2s ease;
    }

    .login-card:hover .card-action {
        gap: 0.75rem;
    }

    /* Feature Grid */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
    }

    .feature-card {
        background-color: var(--surface-2);
        padding: 1.5rem;
        border-radius: var(--radius);
        border: 1px solid var(--border-soft);
        transition: border-color 0.2s ease;
    }

    .feature-card:hover {
        border-color: var(--border);
    }

    .feature-icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-sm);
        background-color: var(--surface-3);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        color: var(--teal);
    }

    .feature-card h4 {
        font-family: var(--font-display);
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text);
        margin: 0 0 0.5rem 0;
    }

    .feature-card p {
        font-size: 0.875rem;
        color: var(--text-dim);
        line-height: 1.5;
        margin: 0;
    }

    /* Responsive adjustment */
    @media (max-width: 640px) {
        .login-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 1.25rem;
        }
        .card-action {
            align-self: flex-end;
        }
        .hero-title {
            font-size: 2rem;
        }
    }
</style>

<div class="ledger-home">
    <div class="ledger-container">
        <!-- Hero Header -->
        <div class="hero-section">
            <div class="eyebrow-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Sistem Internal Perusahaan
            </div>
            <h1 class="hero-title">Financial <span>Ledger</span></h1>
            <p class="hero-description">
                Catat uang masuk beserta bukti transfer, lalu pantau okupansi dan pendapatan bulanan secara terstruktur dan aman.
            </p>
        </div>

        <!-- Login CTA Card -->
        <div class="action-grid">
            <a href="{{ route('login') }}" class="login-card">
                <div class="card-left">
                    <div class="card-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                    </div>
                    <div class="card-text">
                        <h3>Masuk ke Sistem</h3>
                        <p>Masuk dengan email dan kata sandi Anda</p>
                    </div>
                </div>
                <div class="card-action">
                    <span>Buka Halaman Login</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </div>
            </a>
        </div>

        <!-- Highlight Fitur -->
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon-wrapper">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                </div>
                <h4>Input Uang Masuk</h4>
                <p>Pencatatan transaksi kas masuk lengkap dengan penyimpanan bukti transfer.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon-wrapper">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                </div>
                <h4>Monitoring Bulanan</h4>
                <p>Pantau okupansi dan total grafik pendapatan arus kas secara real-time.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon-wrapper">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                </div>
                <h4>Akses Terkontrol</h4>
                <p>Sistem otorisasi aman yang memisahkan wewenang Admin dan Super Admin.</p>
            </div>
        </div>
    </div>
</div>
@endsection