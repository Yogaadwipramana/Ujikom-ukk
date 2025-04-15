@extends('layouts.templates')

@section('title', 'Penjualan')


@section('content')

    <head>
        <style>
            .table th,
            .table td {
                vertical-align: middle;
                text-align: center;
            }

            .btn-sm {
                padding: 6px 12px;
                font-size: 14px;
            }

            .table-hover tbody tr:hover {
                background-color: #f8f9fa;
            }

            .pagination {
                justify-content: center;
            }
        </style>
    </head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <div class="container mt-4">
        <h2 class="fw-bold">Penjualan</h2>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-home"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Penjualan</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <a href="{{ route('orders.export') }}" class="btn btn-primary mb-2">Export Penjualan (.xlsx)</a>

            @if (auth()->user()->role === 'petugas')
                <a href="{{ route('orders.create') }}" class="btn btn-success mb-2">Tambah Penjualan</a>
            @endif

            <div class="d-flex align-items-center mb-2">
                <label class="me-2">Tampilkan:</label>
                <select id="entryLimit" class="form-select w-auto">
                    <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $limit == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>


            <input type="text" class="form-control w-25 mb-2" placeholder="Cari Nama Pelanggan..." id="searchBox">
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr class="text-muted">
                            <th>#</th>
                            <th>Nama Pelanggan</th>
                            <th>Tanggal Penjualan</th>
                            <th>Total Harga</th>
                            <th>Dibuat Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableData">
                        @forelse ($orders as $index => $order)
                            <tr>
                                <td>{{ ($orders->currentPage() - 1) * $orders->perPage() + $index + 1 }}</td>
                                <td>{{ $order->member->name ?? 'Non Members' }}</td>

                                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                @php
                                    $totalHarga = is_array(json_decode($order->total_harga, true))
                                        ? array_sum(json_decode($order->total_harga, true))
                                        : (float) $order->total_harga;
                                @endphp

                                <td>Rp. {{ number_format($totalHarga, 0, ',', '.') }}</td>


                                <td>{{ $order->user->name ?? '-' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-warning btn-sm btn-lihat"
                                            data-id="{{ $order->id }}">Lihat</button>
                                        <a href="{{ route('orders.struk', $order->id) }}" class="btn btn-sm btn-primary"
                                            target="_blank">
                                            Unduh Bukti
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data penjualan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-3">
                    {{ $orders->appends(['limit' => $limit])->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="orderDetailModalLabel">Detail Penjualan</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDetailContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status"></div>
                        <p>Memuat data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function() {
    let timer;

    $("#searchBox").on("keyup", function() {
        clearTimeout(timer);
        let query = $(this).val();

        timer = setTimeout(function() {
            $.ajax({
                url: "{{ route('orders.search') }}",
                type: "GET",
                data: {
                    q: query
                },
                success: function(data) {
                    $("#tableData").html(data);
                },
                error: function() {
                    $("#tableData").html(
                        "<tr><td colspan='6' class='text-danger text-center'>Gagal memuat hasil pencarian.</td></tr>"
                    );
                }
            });
        }, 300);
    });


            $(document).on("click", ".btn-lihat", function() {
                const orderId = $(this).data("id");
                $("#orderDetailModal").modal("show");
                $("#orderDetailContent").html(`
            <div class="text-center">
                <div class="spinner-border" role="status"></div>
                <p>Memuat data...</p>
            </div>
        `);
                $.ajax({
                    url: "/orders/orders/" + orderId,
                    type: "GET",
                    success: function(res) {
                        $("#orderDetailContent").html(res);
                    },
                    error: function() {
                        $("#orderDetailContent").html(
                            "<p class='text-danger'>Gagal memuat data detail.</p>");
                    }
                });
            });
        });

        $("#entryLimit").on("change", function() {
            const selectedLimit = $(this).val();
            const searchQuery = $("#searchBox").val();

            const url = new URL(window.location.href);
            url.searchParams.set("limit", selectedLimit);
            if (searchQuery) {
                url.searchParams.set("q", searchQuery);
            }

            window.location.href = url.href; 
        });
    </script>
@endsection
