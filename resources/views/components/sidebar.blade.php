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
    <button class="sidebar-toggle" type="button" data-sidebar-toggle
      aria-label="Toggle sidebar" title="Toggle sidebar">
      <span class="sidebar-toggle-icon sidebar-toggle-icon--close"><x-icon name="heroicon-o-x-mark" aria-hidden="true" /></span>
      <span class="sidebar-toggle-icon sidebar-toggle-icon--open"><x-icon name="heroicon-o-bars-3" aria-hidden="true" /></span>
    </button>
  </div>

  <nav class="nav">
    <div class="nav-group-label">Menu</div>
    <a href="{{ route($dashboardRoute) }}" class="{{ request()->routeIs($dashboardPattern) ? 'active' : '' }}">
      <x-icon name="heroicon-o-home" aria-hidden="true" />
      <span class="nav-label">{{ $dashboardLabel }}</span>
    </a>
    <a href="{{ route($transactionsRoute) }}" class="{{ request()->routeIs($transactionsPattern) && !($historyPattern && request()->routeIs($historyPattern)) ? 'active' : '' }}">
      <x-icon name="heroicon-o-plus-circle" aria-hidden="true" />
      <span class="nav-label">Input Transaksi</span>
    </a>
    @if($historyRoute)
      <a href="{{ route($historyRoute) }}" class="{{ $historyPattern && request()->routeIs($historyPattern) ? 'active' : '' }}">
        <x-icon name="heroicon-o-clock" aria-hidden="true" />
        <span class="nav-label">Riwayat Transaksi</span>
      </a>
    @endif
  </nav>

  <div class="sidebar-foot">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="btn btn-ghost btn-sm" style="width:100%;">
        <x-icon name="heroicon-o-arrow-left-on-rectangle" aria-hidden="true" />
        <span class="nav-label">Keluar</span>
      </button>
    </form>
  </div>
</aside>

<script>
  document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
      document.body.classList.toggle('sidebar-collapsed');
    });
  });
</script>
