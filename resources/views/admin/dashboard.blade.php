@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-4">

    <h1 class="mt-4">Dashboard</h1>
    <p class="text-muted mb-4">Overview</p>

    {{-- ===== STATISTIK ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded p-3 me-3">
                        <i class="fas fa-map"></i>
                    </div>
                    <div>
                        <small class="text-muted">Total Lapangan</small>
                        <h4 class="fw-bold mb-0">{{ $totalLapangan }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded p-3 me-3">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <small class="text-muted">Total Event</small>
                        <h4 class="fw-bold mb-0">{{ $totalEvent }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded p-3 me-3">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <small class="text-muted">User Terdaftar</small>
                        <h4 class="fw-bold mb-0">{{ $totalUser }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-secondary bg-opacity-10 text-secondary rounded p-3 me-3">
                        <i class="fas fa-bookmark"></i>
                    </div>
                    <div>
                        <small class="text-muted">Booking Hari Ini</small>
                        <h4 class="fw-bold mb-0">{{ $bookingToday }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== GRAFIK & TASK ===== --}}
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <strong>Grafik Booking</strong>
                    <small class="text-muted d-block">Tahun {{ now()->year }}</small>
                </div>
                <div class="card-body">
                    <canvas id="bookingChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between">
                    <strong>Tasks</strong>
                    <small class="text-primary">This Month</small>
                </div>
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block">
                        <canvas id="taskChart" width="180" height="180"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle fw-bold fs-4">
                            {{ $taskPercentage }}%
                        </div>
                    </div>

                    <div class="mt-4 text-start">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-success me-2">&nbsp;</span>
                            Berhasil ({{ $taskSuccess }})
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-warning me-2">&nbsp;</span>
                            Pending ({{ $taskPending }})
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-danger me-2">&nbsp;</span>
                            Gagal ({{ $taskFailed }})
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TRANSAKSI TERBARU ===== --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <strong>5 Transaksi Terbaru</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Lapangan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentTransactions as $trx)
                    <tr>
                        <td>#{{ $trx->id }}</td>
                        <td>{{ $trx->user->name ?? '-' }}</td>
                        <td>{{ $trx->lapangan->nama ?? '-' }}</td>
                        <td>Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td>
                            @if ($trx->status_pembayaran == 'success')
                            <span class="badge bg-success">Berhasil</span>
                            @elseif ($trx->status_pembayaran == 'pending')
                            <span class="badge bg-warning">Pending</span>
                            @else
                            <span class="badge bg-danger">Gagal</span>
                            @endif
                        </td>
                        <td>{{ $trx->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Belum ada transaksi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari controller
        const bookingPerMonth = @json($bookingPerMonth);
        const taskData = @json([$taskSuccess, $taskPending, $taskFailed]);

        // Grafik Booking
        const bookingCtx = document.getElementById('bookingChart');
        if (bookingCtx) {
            new Chart(bookingCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Booking',
                        data: bookingPerMonth,
                        fill: true,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13,110,253,0.1)',
                        tension: 0.4,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        // Grafik Task (Doughnut)
        const taskCtx = document.getElementById('taskChart');
        if (taskCtx) {
            new Chart(taskCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: taskData,
                        backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true
                        }
                    }
                }
            });
        }
    });
</script>
@endpush