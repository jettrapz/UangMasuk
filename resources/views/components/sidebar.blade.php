@props([
    'brandMark',
    'roleLabel',
    'dashboardRoute',
    'dashboardLabel',
    'dashboardPattern',
    'transactionsRoute',
    'transactionsPattern',
    'historyRoute' => null,
    'historyPattern' => null,
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
    <a href="{{ route($transactionsRoute) }}" class="{{ request()->routeIs($transactionsPattern) && !($historyPattern && request()->routeIs($historyPattern)) ? 'active' : '' }}">
      Input Transaksi
    </a>
    @if($historyRoute)
      <a href="{{ route($historyRoute) }}" class="{{ $historyPattern && request()->routeIs($historyPattern) ? 'active' : '' }}">
        Riwayat Transaksi
      </a>
    @endif
  </nav>

  <div class="sidebar-foot">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="btn btn-ghost btn-sm" style="width:100%;">Keluar</button>
    </form>
  </div>
</aside>
