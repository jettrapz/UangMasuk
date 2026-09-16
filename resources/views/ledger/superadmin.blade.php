@extends('ledger.layout')

@section('content')
  @php
    $chartLabels = [];
    $chartNominal = [];
    $chartOkupansi = [];
    foreach ($months as $month) {
      $chartLabels[] = \Carbon\Carbon::createFromFormat('Y-m', $month->month)->format('M Y');
      $chartNominal[] = (float) $month->total_nominal;
      $chartOkupansi[] = (float) $month->total_okupansi;
    }
  @endphp
  <div class="app-shell">
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-mark">S</div>
        <div class="brand-text">
          <div class="name">Ledger</div>
          <div class="role">Super Admin - {{ Auth::user()->name }}</div>
        </div>
      </div>
      <nav class="nav">
        <div class="nav-group-label">Menu</div>
          @if(Auth::user()->role === 'superadmin')
            <a href="{{ route('superadmin.transactions.create') }}">Input Transaksi</a>
          @endif
          <a href="{{ route('superadmin') }}" class="active">Dashboard Super Admin</a>
      </nav>
      <div class="sidebar-foot">
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost btn-sm"
            style="width:100%;">Keluar</button></form>
      </div>
    </aside>
    <main class="main">
      <div class="topbar">
        <div>
          <div class="eyebrow">Super Admin</div>
          <h1>Dashboard Rekap</h1>
          <p class="sub">Ringkasan pendapatan dan okupansi, per bulan maupun keseluruhan.</p>
        </div>
        <div class="topbar-actions"><a class="btn btn-teal" href="{{ route('superadmin.export') }}">Ekspor CSV</a></div>
      </div>
      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-label">Total Transaksi</div>
          <div class="stat-value">{{ $transactions->count() }}</div>
          <div class="stat-note">Sepanjang waktu</div>
        </div>
        <div class="stat-card teal">
          <div class="stat-label">Total Pendapatan</div>
          <div class="stat-value">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
          <div class="stat-note">Sepanjang waktu</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total Okupansi</div>
          <div class="stat-value">{{ number_format($transactions->sum('okupansi_jam'), 2, ',', '.') }} jam</div>
          <div class="stat-note">Jam bermain terekam</div>
        </div>
        <div class="stat-card red">
          <div class="stat-label">Rata-rata / Transaksi</div>
          <div class="stat-value">Rp
            {{ number_format($transactions->count() ? $totalNominal / $transactions->count() : 0, 0, ',', '.') }}
          </div>
          <div class="stat-note">Nominal rata-rata</div>
        </div>
      </div>
      <div class="card chart-card" style="margin-bottom:20px;">
        <div class="card-title">Tren Bulanan - Pendapatan &amp; Okupansi</div><canvas id="trendChart"></canvas>
        <div class="chart-legend">
          <div class="legend-item"><span class="legend-dot" style="background:#e8ac52;"></span> Pendapatan (Rp)</div>
          <div class="legend-item"><span class="legend-dot" style="background:#35b3a3;"></span> Okupansi (jam)</div>
        </div>
      </div>
      <div class="card" style="margin-bottom:20px;">
        <div class="card-title">Rekap Per Bulan</div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Bulan</th>
                <th>Jumlah Transaksi</th>
                <th>Total Nominal</th>
                <th>Total Okupansi</th>
                <th>Rata-rata</th>
              </tr>
            </thead>
            <tbody>@forelse($months as $month)
              <tr>
                <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $month->month)->translatedFormat('F Y') }}</td>
                <td>{{ $month->count }}</td>
                <td class="mono">Rp {{ number_format($month->total_nominal, 0, ',', '.') }}</td>
                <td>{{ number_format($month->total_okupansi, 2, ',', '.') }} jam</td>
                <td class="mono">Rp
                  {{ number_format($month->count ? $month->total_nominal / $month->count : 0, 0, ',', '.') }}
                </td>
            </tr>@empty<tr>
                <td colspan="5">Belum ada transaksi.</td>
              </tr>@endforelse
            </tbody>
          </table>
        </div>
      </div>
      <div class="card">
        <div class="card-title">Detail Transaksi &amp; Bukti Transfer</div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Bukti</th>
                <th>Nama</th>
                <th>Tgl Main</th>
                <th>Jenis</th>
                <th>Tgl Transfer</th>
                <th>Nominal</th>
                <th>Jam</th>
                <th>Okupansi</th>
                <th>Catatan</th>
              </tr>
            </thead>
            <tbody>@forelse($transactions as $transaction)
              <tr>
                <td>@if($transaction->gambar_bukti)<a href="{{ asset('storage/' . $transaction->gambar_bukti) }}"
                target="_blank">Lihat</a>@else-@endif</td>
                <td>{{ $transaction->nama }}</td>
                <td>{{ $transaction->tanggal_main->format('d M Y') }}</td>
                <td>{{ $transaction->jenis_transfer }}</td>
                <td>{{ $transaction->tanggal_transfer->format('d M Y') }}</td>
                <td class="mono">Rp {{ number_format($transaction->nominal, 0, ',', '.') }}</td>
                <td>{{ $transaction->jam_mulai }} - {{ $transaction->jam_selesai }}</td>
                <td>{{ number_format($transaction->okupansi_jam, 2, ',', '.') }} jam</td>
                <td>{{ $transaction->catatan ?: '-' }}</td>
            </tr>@empty<tr>
                <td colspan="9">Belum ada transaksi.</td>
              </tr>@endforelse
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
  <script>
    const chartLabels = @json($chartLabels);
    const chartNominal = @json($chartNominal);
    const chartOkupansi = @json($chartOkupansi);
    new Chart(document.getElementById('trendChart'), {
      type: 'line',
      data: {
        labels: chartLabels,
        datasets: [
          { label: 'Pendapatan (Rp)', data: chartNominal, borderColor: '#e8ac52', backgroundColor: 'rgba(232, 172, 82, .15)', yAxisID: 'nominal', tension: 0, fill: false },
          { label: 'Okupansi (jam)', data: chartOkupansi, borderColor: '#35b3a3', backgroundColor: 'rgba(53, 179, 163, .15)', yAxisID: 'okupansi', tension: 0, fill: false },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          nominal: { position: 'left', beginAtZero: true, ticks: { callback: value => 'Rp ' + Number(value).toLocaleString('id-ID') } },
          okupansi: { position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }, ticks: { callback: value => value + ' jam' } },
        },
      },
    });
  </script>
@endsection