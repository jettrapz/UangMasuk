@props([
    'brandMark',
    'roleLabel',
    'dashboardRoute',
    'dashboardLabel',
    'dashboardPattern',
    'transactionsRoute',
    'transactionsPattern',
])

<aside class="sidebar">
  <div class="brand">
    <div class="brand-mark">{{ $brandMark }}</div>
    <div class="brand-text">
      <div class="name">Ledger</div>
      <div class="role">{{ $roleLabel }} - {{ Auth::user()->name }}</div>
    </div>
  </div>

  <nav class="nav">
    <div class="nav-group-label">Menu</div>
    <a href="{{ route($dashboardRoute) }}" class="{{ request()->routeIs($dashboardPattern) ? 'active' : '' }}">
      {{ $dashboardLabel }}
    </a>
    <a href="{{ route($transactionsRoute) }}" class="{{ request()->routeIs($transactionsPattern) ? 'active' : '' }}">
      Input Transaksi
    </a>
  </nav>

  <div class="sidebar-foot">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="btn btn-ghost btn-sm" style="width:100%;">Keluar</button>
    </form>
  </div>
</aside>
