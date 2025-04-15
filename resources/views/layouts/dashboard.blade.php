@extends('layouts.templates')

@section('title', 'Dashboard')

@section('content')
    @if (auth()->user()->role == 'admin')
        {{-- ADMIN VIEW --}}
        <h1>Dashboard</h1>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-home"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>

        <p class="mb-4">Selamat Datang, Administrator!</p>

        <div class="card p-4 shadow-sm border-0 mb-4">
            <h5 class="text-center mb-4">Statistik Penjualan & Stok Produk</h5>
            <div class="row">
                {{-- batang --}}
                <div class="col-md-6 mb-4">
                    <canvas id="salesChart" height="250"></canvas>
                </div>
                {{-- pie --}}
                <div class="col-md-4 mb-4">
                    <canvas id="productChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <script>
            const salesLabels = {!! json_encode($salesLabels ?? []) !!};
            const salesCounts = {!! json_encode($salesCounts ?? []) !!};

            const colors = [
                'rgba(75, 192, 192, 0.7)', 'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)',
                'rgba(255, 159, 64, 0.7)', 'rgba(153, 102, 255, 0.7)', 'rgba(255, 205, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)', 'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)',
                'rgba(255, 159, 64, 0.7)', 'rgba(153, 102, 255, 0.7)', 'rgba(255, 205, 86, 0.7)'
            ];

            const backgroundColors = salesLabels.map((_, index) => colors[index % colors.length]);

            const ctx = document.getElementById('salesChart').getContext('2d');

            const salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: salesLabels,
                    datasets: [{
                        label: 'Total Penjualan (Rp)',
                        data: salesCounts.map(Number),
                        backgroundColor: backgroundColors,
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        borderRadius: 5,
                    }]
                },
                options: {
                    responsive: true,
                    onClick: function(evt) {
                        const points = salesChart.getElementsAtEventForMode(evt, 'nearest', {
                            intersect: true
                        }, true);
                        if (points.length) {
                            const index = points[0].index;
                            const tanggal = salesLabels[index];
                            const total = Number(salesCounts[index]);

                            alert(`Tanggal: ${tanggal}\nTotal Penjualan: Rp ${total.toLocaleString('id-ID')}`);
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });


            const productLabels = {!! json_encode($products->pluck('name')) !!};
            const productData = {!! json_encode($products->pluck('stock')) !!};

            new Chart(document.getElementById('productChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: productLabels,
                    datasets: [{
                        data: productData,
                        backgroundColor: [
                            '#FF6384', '#FFCE56', '#36A2EB', '#4BC0C0', '#FF9F40',
                            '#9966FF', '#C9CBCF', '#FF5733', '#33FF57', '#3357FF',
                            '#FF33A1', '#A133FF', '#33FFF1', '#F1FF33', '#8D33FF'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    return `${label}: ${value} stok`;
                                }
                            }
                        }
                    }
                }
            });
        </script>
    @else
        {{-- PETUGAS / KASIR VIEW --}}
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-home"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
            </div>

            <div class="d-flex justify-content-center align-items-center" style="min-height: 60vh;">
                <div class="card shadow-lg p-5"
                    style="width: 700px; border-radius: 30px; background: linear-gradient(135deg, #e3f2fd, #ffffff);">
                    <div class="text-center mb-4">
                        <i class="fas fa-cash-register fa-3x text-primary mb-3"></i>
                        <h2 class="fw-bold text-dark">Total Penjualan Hari Ini</h2>
                    </div>
                    <div class="text-center">
                        <h1 class="text-success fw-bold mb-2" style="font-size: 4rem;">
                            Rp {{ number_format($totalPenjualan ?? 0, 0, ',', '.') }}
                        </h1>
                        <p class="text-muted" style="font-size: 1.2rem;">
                            {{ \Carbon\Carbon::parse($today ?? now())->isoFormat('dddd, D MMMM Y') }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    @endif
@endsection
