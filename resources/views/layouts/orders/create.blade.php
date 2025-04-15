@extends('layouts.templates')

@section('title', 'Tambah Penjualan')

@section('content')
    <div class="container mt-4">
        <h2 class="fw-bold">Tambah Penjualan</h2>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Penjualan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Penjualan</li>
            </ol>
        </nav>

        <div class="row">
            @foreach ($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                            alt="{{ $product->name }}">
                        <div class="card-body text-center">
                            <input type="hidden" class="product-id" value="{{ $product->id }}">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="text-muted">Stok:
                                <span id="stock-{{ $product->id }}"
                                    data-original-stock="{{ $product->stock }}">{{ $product->stock }}</span>
                            </p>

                            <p class="fw-bold text-primary">Rp. <span
                                    id="price-{{ $product->id }}">{{ number_format($product->price, 0, ',', '.') }}</span>
                            </p>

                            <div class="d-flex justify-content-center align-items-center">
                                <button class="btn btn-outline-secondary btn-sm me-2 decrement"
                                    data-id="{{ $product->id }}" disabled>-</button>
                                <span id="quantity-{{ $product->id }}" class="mx-2 fw-bold">0</span>
                                <button class="btn btn-outline-primary btn-sm increment"
                                    data-id="{{ $product->id }}">+</button>
                            </div>
                            <p class="fw-bold mt-2">Subtotal: <span id="subtotal-{{ $product->id }}">Rp. 0</span></p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Sticky Checkout Button -->
    <div class="sticky-checkout text-center">
        <button class="btn btn-success fw-bold py-1 px-3" id="checkout">Checkout</button>
    </div>

    <style>
        .sticky-checkout {
            position: fixed;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 8px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        #checkout {
            font-size: 14px;
        }
    </style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let cart = {};


            document.querySelectorAll(".increment").forEach(button => {
                button.addEventListener("click", function() {
                    let id = this.dataset.id;
                    let stock = parseInt(document.getElementById(`stock-${id}`).textContent);
                    let price = parseInt(document.getElementById(`price-${id}`).textContent.replace(
                        /\./g, ""));

                    if (!cart[id]) {
                        cart[id] = {
                            quantity: 0,
                            price: price,
                            name: document.querySelector(`#quantity-${id}`).closest(
                                '.card-body').querySelector('h5').textContent
                        };
                    }

                    let originalStock = parseInt(document.getElementById(`stock-${id}`)
                        .getAttribute("data-original-stock"));

                    if (originalStock === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Stok Habis!',
                            text: 'Produk ini tidak tersedia saat ini.',
                            confirmButtonColor: '#d33'
                        });
                        return;
                    }

                    if (cart[id].quantity + 1 > originalStock) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Melebihi Stok!',
                            text: 'Anda tidak dapat menambahkan lebih dari stok yang tersedia.',
                            confirmButtonColor: '#ffc107'
                        });
                        return;
                    }

                    cart[id].quantity++;

                    updateUI(id);
                });
            });

            document.querySelectorAll(".decrement").forEach(button => {
                button.addEventListener("click", function() {
                    let id = this.dataset.id;

                    if (cart[id] && cart[id].quantity > 0) {
                        cart[id].quantity--;
                    }

                    updateUI(id);
                });
            });

            function updateUI(id) {
                let quantityEl = document.getElementById(`quantity-${id}`);
                let subtotalEl = document.getElementById(`subtotal-${id}`);
                let stockEl = document.getElementById(`stock-${id}`);
                let decrementBtn = document.querySelector(`.decrement[data-id='${id}']`);

                let quantity = cart[id] ? cart[id].quantity : 0;
                let subtotal = quantity * cart[id].price;

                // Hitung sisa stock berdasarkan jumlah awal dikurangi quantity
                let originalStock = parseInt(stockEl.getAttribute("data-original-stock"));
                let remainingStock = originalStock - quantity;

                quantityEl.textContent = quantity;
                subtotalEl.textContent = `Rp. ${new Intl.NumberFormat('id-ID').format(subtotal)}`;
                stockEl.textContent = remainingStock;
                decrementBtn.disabled = quantity === 0;
            }


            document.getElementById("checkout").addEventListener("click", function() {
                let cartData = Object.entries(cart)
                    .filter(([_, item]) => item.quantity > 0)
                    .map(([id, item]) => ({
                        id: id,
                        name: item.name,
                        quantity: item.quantity,
                        price: item.price,
                        subtotal: item.quantity * item.price
                    }));

                if (cartData.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Keranjang Kosong!',
                        text: 'Silakan tambahkan produk sebelum checkout.',
                        confirmButtonColor: '#ffc107'
                    });
                    return;
                }

                console.log("=== DATA KERANJANG ===");
                console.table(cartData);

                // Uncomment jika nanti mau disimpan dan dialihkan
                sessionStorage.setItem("cart", JSON.stringify(cartData));
                window.location.href = "{{ route('orders.checkout') }}";
            });

        });
    </script>
@endsection
