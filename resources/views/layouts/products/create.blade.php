@extends('layouts.templates')

@section('title', 'Tambah Produk')

@section('content')
<div class="container mt-5">
    <h2 class="fw-bold">Tambah Produk</h2>

    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Harga</label>
                            <input type="text" class="form-control @error('price') is-invalid @enderror"
                                   id="price" name="price"
                                   value="{{ old('price', $product->price ?? '') ? 'Rp ' . number_format($product->price ?? 0, 0, ',', '.') : '' }}"
                                   required oninput="formatRupiah(this)" onfocus="formatRupiah(this)">
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="image" class="form-label">Gambar Produk</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stok</label>
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock') }}" required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function formatRupiah(input) {
        let value = input.value.replace(/[^\d]/g, ''); // Hanya angka
        let formatted = new Intl.NumberFormat('id-ID').format(value); // Format angka dengan titik
        input.value = value ? `Rp ${formatted}` : ''; // Tambahkan "Rp " jika ada angka
    }
</script>

@endsection
