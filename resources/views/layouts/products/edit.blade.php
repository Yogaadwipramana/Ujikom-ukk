@extends('layouts.templates')

@section('title', 'Edit Produk')

@section('content')
    <div class="container mt-5">
        <h2 class="fw-bold">Edit Produk</h2>

        <div class="card shadow-sm rounded-3">
            <div class="card-body">
                <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                   
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Produk</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $product->name) }}" required>
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

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Gambar Produk</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    id="image" name="image">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image"
                                        class="img-thumbnail mt-2" width="100">
                                @endif
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="stock" class="form-label">Stok</label>
                                <input type="number" class="form-control @error('stock') is-invalid @enderror"
                                    id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required>
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
            let value = input.value.replace(/[^\d]/g, '');
            let formatted = new Intl.NumberFormat('id-ID').format(value);
            input.value = value ? `Rp ${formatted}` : '';
        }
    </script>

@endsection
