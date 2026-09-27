@extends('layouts.layout')

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

    $peakHourLabels = $peakHours->pluck('jam_mulai')->map(fn($item) => substr($item, 0, 5))->toArray();
    $peakHourCounts = $peakHours->pluck('total_booking')->toArray();
  @endphp
  <div class="app-shell">
    <x-sidebar brand-mark="S" role-label="Super Admin" dashboard-route="superadmin"
      dashboard-label="Dashboard Super Admin" dashboard-pattern="superadmin"
      transactions-route="superadmin.transactions.create" transactions-pattern="superadmin.transactions.*"
      history-route="superadmin.transactions.history" history-pattern="superadmin.transactions.history" />
    <main class="main">
      <div class="topbar">
        <div>
          <div class="eyebrow">Super Admin</div>
          <h1>Dashboard Statistik</h1>
          <p class="sub">Ringkasan pendapatan, okupansi, dan statistik pemain.</p>
        </div>
        <div class="topbar-actions" style="display: flex; gap: 10px; align-items: center;">
          <form method="GET" action="{{ route('superadmin') }}" id="globalFilterForm" style="display: flex; gap: 8px;">
            <input type="hidden" name="player_month" value="{{ $playerMonthFilter }}">
            <select name="month" onchange="document.getElementById('globalFilterForm').submit()" class="btn"
              class="btn btn-teal">
              <option value="">-- Semua Bulan --</option>
              @foreach($months as $m)
                <option value="{{ $m->month }}" {{ $selectedMonth == $m->month ? 'selected' : '' }}>
                  {{ \Carbon\Carbon::createFromFormat('Y-m', $m->month)->translatedFormat('F Y') }}
                </option>
              @endforeach
            </select>
          </form>

          <a class="btn btn-teal" href="{{ route('superadmin.export') }}">Export Excel</a>
        </div>
      </div>

      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-label">Total Transaksi</div>
          <div class="stat-value">{{ $transactions->count() }}</div>
          <div class="stat-note">{{ $selectedMonth ? 'Bulan terpilih' : 'Sepanjang waktu' }}</div>
        </div>
        <div class="stat-card teal">
          <div class="stat-label">Total Pendapatan</div>
          <div class="stat-value">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
          <div class="stat-note">{{ $selectedMonth ? 'Bulan terpilih' : 'Sepanjang waktu' }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total Okupansi</div>
          <div class="stat-value">{{ number_format($totalOkupansi, 2, ',', '.') }} jam</div>
          <div class="stat-note">Jam bermain terekam</div>
        </div>

        {{-- KPI BARU: RATA-RATA DURASI MAIN --}}
        <div class="stat-card red">
          <div class="stat-label">Rata-rata Durasi</div>
          <div class="stat-value">{{ number_format($avgDuration, 1, ',', '.') }} Jam</div>
          <div class="stat-note">Durasi main per booking</div>
        </div>
      </div>

      {{-- SEC: Analitik Pemain & Jam Ramai --}}
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">

        {{-- Card Top Pemain --}}
        <div class="card">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <div class="card-title" style="margin-bottom:0;">Pemain Paling Sering Main</div>

            <form method="GET" action="{{ route('superadmin') }}" id="playerFilterForm">
              <input type="hidden" name="month" value="{{ $selectedMonth }}">
              <select name="player_month" onchange="document.getElementById('playerFilterForm').submit()"
                style="font-size: 12px; padding: 4px 8px; border-radius: 4px;">
                <option value="current" {{ $playerMonthFilter == 'current' ? 'selected' : '' }}>Bulan Ini
                  ({{ \Carbon\Carbon::now()->translatedFormat('F') }})</option>
                <option value="all" {{ $playerMonthFilter == 'all' ? 'selected' : '' }}>Sepanjang Waktu</option>
                @if($selectedMonth)
                  <option value="selected" {{ $playerMonthFilter == 'selected' ? 'selected' : '' }}>Ikuti Filter Utama
                    ({{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y') }})</option>
                @endif
              </select>
            </form>
          </div>

          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Pemain</th>
                  <th>Sesi Main</th>
                  <th>Total Durasi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($topPlayers as $index => $player)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $player->nama }}</strong></td>
                    <td>{{ $player->total_main }}x</td>
                    <td>{{ number_format($player->total_jam, 1, ',', '.') }} jam</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" style="text-align: center; color: #888;">Belum ada data bermain pada periode ini.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        {{-- Card Chart Jam Ramai --}}
        <div class="card chart-card">
          <div class="card-title">Jam Ramai Pelanggan</div>
          <canvas id="peakHourChart"></canvas>
        </div>

      </div>

      <div class="card chart-card" style="margin-bottom:20px;">
        <div class="card-title">Tren Bulanan - Pendapatan &amp; Okupansi</div>
        <canvas id="trendChart"></canvas>
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
            <tbody>
              @forelse($months as $month)
                <tr>
                  <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $month->month)->translatedFormat('F Y') }}</td>
                  <td>{{ $month->count }}</td>
                  <td class="mono">Rp {{ number_format($month->total_nominal, 0, ',', '.') }}</td>
                  <td>{{ number_format($month->total_okupansi, 2, ',', '.') }} jam</td>
                  <td class="mono">Rp
                    {{ number_format($month->count ? $month->total_nominal / $month->count : 0, 0, ',', '.') }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5">Belum ada transaksi.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
  <script>
    // 1. Chart Tren Bulanan
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

    // 2. Chart Jam Ramai Pelanggan
    const peakLabels = @json($peakHourLabels);
    const peakCounts = @json($peakHourCounts);
    new Chart(document.getElementById('peakHourChart'), {
      type: 'bar',
      data: {
        labels: peakLabels,
        datasets: [{
          label: 'Jumlah Booking',
          data: peakCounts,
          backgroundColor: '#35b3a3',
          borderRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
      }
    });
  </script>
@endsection