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
      <div class="role" title="{{ $roleLabel }} - {{ Auth::user()->name }}">
        {{ $roleLabel }} - {{ Auth::user()->name }}
      </div>
    </div>
    <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-label="Toggle sidebar" title="Toggle sidebar">
      <span class="sidebar-toggle-icon sidebar-toggle-icon--close"><x-icon name="heroicon-o-x-mark"
          aria-hidden="true" /></span>
      <span class="sidebar-toggle-icon sidebar-toggle-icon--open"><x-icon name="heroicon-o-bars-3"
          aria-hidden="true" /></span>
    </button>
  </div>

  <nav class="nav">
    <div class="nav-group-label">Menu</div>
    <a href="{{ route($dashboardRoute) }}" class="{{ request()->routeIs($dashboardPattern) ? 'active' : '' }}">
      <x-icon name="heroicon-o-home" aria-hidden="true" />
      <span class="nav-label">{{ $dashboardLabel }}</span>
    </a>
    <a href="{{ route($transactionsRoute) }}"
      class="{{ request()->routeIs($transactionsPattern) && !($historyPattern && request()->routeIs($historyPattern)) ? 'active' : '' }}">
      <x-icon name="heroicon-o-plus-circle" aria-hidden="true" />
      <span class="nav-label">Input Transaksi</span>
    </a>
    @if($historyRoute)
      <a href="{{ route($historyRoute) }}"
        class="{{ $historyPattern && request()->routeIs($historyPattern) ? 'active' : '' }}">
        <x-icon name="heroicon-o-clock" aria-hidden="true" />
        <span class="nav-label">Riwayat Transaksi</span>
      </a>
    @endif
  </nav>

  <div class="sidebar-foot">
    <!-- Tombol Pemicu Modal Logout -->
    <button class="btn btn-ghost btn-sm" style="width:100%;" type="button" data-modal-open="logout-modal"
      aria-label="Keluar dari sistem">
      <x-icon name="heroicon-o-arrow-left-on-rectangle" aria-hidden="true" />
      <span class="nav-label">Keluar</span>
    </button>

    <!-- Modal Konfirmasi Logout -->
    <dialog class="confirm-modal" id="logout-modal">
      <div class="confirm-modal-content">
        <div class="confirm-modal-header">
          <h2>Konfirmasi Keluar</h2>
          <button class="modal-close" type="button" data-modal-close aria-label="Tutup">&times;</button>
        </div>
        <p>Apakah Anda yakin ingin keluar dari sesi ini?</p>
        <div class="confirm-modal-actions">
          <button class="btn btn-ghost btn-sm" type="button" data-modal-close>Batal</button>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-danger btn-sm" type="submit">Ya, Keluar</button>
          </form>
        </div>
      </div>
    </dialog>
  </div>
</aside>

<script>
  document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
      document.body.classList.toggle('sidebar-collapsed');
    });
  });
</script>