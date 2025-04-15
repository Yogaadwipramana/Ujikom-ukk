@extends('layouts.templates')

@section('title', 'Detail Pembayaran')

@section('content')
<style>
    .card {
        border-radius: 12px;
        border: none;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .bg-dark {
        background-color: #212529 !important;
        border-radius: 8px;
    }
    .table-light {
        background-color: #f8f9fa;
    }
    .btn {
        border-radius: 6px;
        padding: 8px 16px;
        font-weight: 500;
    }
</style>

<div class="container mt-4">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <h2 class="fw-bold">Detail Pembayaran</h2>

    <div class="card shadow-sm mt-4 p-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <p class="fw-bold mb-1">Invoice - INV-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</p>
                <p class="text-muted">{{ \Carbon\Carbon::parse($order->tanggal_penjualan)->format('d M Y') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('orders.struk', $order->id) }}" class="btn btn-primary" target="_blank">
                    Unduh
                </a>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>
        </div>

        <table class="table mt-3">
            <thead class="table-light">
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Quantity</th>
                    <th>Sub Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>Rp. {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>{{ $product->total_barang }}</td>
                    <td>Rp. {{ number_format($product->price * $product->total_barang, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row mt-4">
            <div class="col-md-8">
                <div class="bg-light p-3 rounded">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="text-muted mb-1">POIN DIGUNAKAN</p>
                            <h5 class="fw-bold">{{ number_format($order->member_point_used ?? 0, 0, ',', '.') }}</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted mb-1">KASIR</p>
                            <h5 class="fw-bold">{{ $order->user->name ?? '-' }}</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted mb-1">KEMBALIAN</p>
                            <h5 class="fw-bold">Rp. {{ number_format($order->customer_return ?? 0, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-dark text-white p-3 rounded text-end">
                    <p class="mb-1">TOTAL</p>
                    <h3 class="fw-bold">Rp. {{ number_format($order->total_harga_after_point ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
    

<script>
// document.addEventListener("DOMContentLoaded", function() {
//     let cartData = JSON.parse(sessionStorage.getItem("cart") || "[]");
//     let invoiceNumber = sessionStorage.getItem("invoiceNumber") || "INV-0001";
//     let transactionDate = sessionStorage.getItem("transactionDate") || new Date().toLocaleDateString("id-ID");
//     let totalAmount = sessionStorage.getItem("totalAmount") || 0;
//     let changeAmount = sessionStorage.getItem("changeAmount") || 0;
//     let cashierName = sessionStorage.getItem("cashierName") || "Petugas";
//     let pointsUsed = sessionStorage.getItem("pointsUsed") || 0;

//     document.getElementById("invoice-number").textContent = invoiceNumber;
//     document.getElementById("transaction-date").textContent = transactionDate;
//     document.getElementById("total-amount").textContent = `Rp. ${parseInt(totalAmount).toLocaleString('id-ID')}`;
//     document.getElementById("change-amount").textContent = `Rp. ${parseInt(changeAmount).toLocaleString('id-ID')}`;
//     document.getElementById("cashier-name").textContent = cashierName;
//     document.getElementById("points-used").textContent = parseInt(pointsUsed).toLocaleString('id-ID');
    
//     let productListContainer = document.getElementById("product-list");
//     productListContainer.innerHTML = "";
    
//     cartData.forEach(item => {
//         let row = `
//             <tr>
//                 <td>${item.name}</td>
//                 <td>Rp. ${parseInt(item.price).toLocaleString('id-ID')}</td>
//                 <td>${item.quantity}</td>
//                 <td>Rp. ${(item.quantity * item.price).toLocaleString('id-ID')}</td>
//             </tr>
//         `;
//         productListContainer.innerHTML += row;
//     });
// });
</script>
@endsection
