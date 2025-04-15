@extends('layouts.templates')

@section('title', 'Daftar Produk')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<head>
    <style>
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #ddd;
        }
        .btn-group {
            display: flex;
            gap: 5px;
        }
    </style>
</head>

<div class="container mt-5">
    <h2 class="fw-bold mb-3">Daftar Produk</h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-home"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Produk</li>
            </ol>
        </nav>

        @if(auth()->user()->role === 'admin')
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm px-4">+ Tambah Produk</a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr class="text-muted">
                        <th>#</th>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        @if(auth()->user()->role === 'admin')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <img src="{{ asset($product->image ? 'storage/' . $product->image : 'images/default-product.jpg') }}" class="product-img" alt="Produk">


                            </td>
                            <td>{{ $product->name }}</td>
                            <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td>{{ $product->stock }}</td>

                            @if(auth()->user()->role === 'admin')
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm"> Edit</a>

                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#updateStockModal{{ $product->id }}">
                                            Update Stok
                                        </button>

                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal{{ $product->id }}">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>

                        @if(auth()->user()->role === 'admin')
                            <!-- Modal Konfirmasi Hapus -->
                            <div class="modal fade" id="deleteConfirmModal{{ $product->id }}" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-danger">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <p>Apakah Anda yakin ingin menghapus produk <strong>{{ $product->name }}</strong>?</p>
                                        </div>
                                        <div class="modal-footer d-flex justify-content-center">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Update Stok -->
                            <div class="modal fade" id="updateStockModal{{ $product->id }}" tabindex="-1" aria-labelledby="updateStockModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Update Stok Produk</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('products.update', $product->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Produk</label>
                                                    <input type="text" class="form-control" value="{{ $product->name }}" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="stock" class="form-label">Stok</label>
                                                    <input type="number" class="form-control" name="stock" value="{{ $product->stock }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Produk Berhasil Dihapus -->
@if(session('deleted'))
    <div class="modal fade show" id="productDeletedModal" tabindex="-1" aria-labelledby="productDeletedModalLabel" aria-hidden="true" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Berhasil Dihapus</h5>
                    <button type="button" class="btn-close" onclick="closeDeletedModal()"></button>
                </div>
                <div class="modal-body text-center">
                    <p>{{ session('deleted') }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function closeDeletedModal() {
            document.getElementById('productDeletedModal').style.display = 'none';
        }
        setTimeout(closeDeletedModal, 2000);
    </script>
@endif
@endsection
