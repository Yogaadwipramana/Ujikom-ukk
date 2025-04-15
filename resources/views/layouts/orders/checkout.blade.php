@extends('layouts.templates')

@section('title', 'Checkout')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    body {
        background-color: #f8f9fa;
    }
    .checkout-container {
        max-width: 1300px;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
    .total-price {
        font-size: 1.2rem;
        font-weight: bold;
    }
    .btn-primary {
        width: 100px;
    }
    .member-status {
        color: red;
        font-size: 0.9rem;
    }
    #phone-input {
        display: none;
    }
    .error-text {
        color: red;
        font-size: 0.9rem;
    }
    .success-text {
        color: green;
        font-size: 0.9rem;
    }
</style>

<div class="container mt-4">
    <h2 class="fw-bold">Checkout</h2>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Penjualan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
    </nav>

    <div class="container mt-5">
        <div class="checkout-container mx-auto p-4">
            <div class="row">
                <div class="col-md-6">
                    <h5>Produk yang dipilih</h5>
                    <div id="checkout-items">
                        <p class="text-muted">Memuat produk...</p>
                    </div>
                    <p class="total-price mt-3">Total: <span id="total-price">Rp. 0</span></p>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Member Status <span class="member-status">Dapat juga membuat member</span></label>
                    <select class="form-select" id="member-status">
                        <option value="non-member">Bukan Member</option>
                        <option value="member">Member</option>
                    </select>

                    <div class="mt-3" id="phone-input">
                        <label class="form-label">No Telepon <span class="member-status">Daftar/Digunakan Member</span></label>
                        <input type="text" class="form-control" id="no_telepon" placeholder="Masukkan No Telepon" maxlength="12">
                        <p id="phone-error" class="error-text mt-1" style="display: none;"></p>                        
                    </div>

                    <label class="form-label mt-3">Total Bayar</label>
                    <input type="text" class="form-control" id="total_harga" placeholder="Rp. 0">

                    <p id="payment-status" class="error-text mt-1"></p>

                    <button id="place-order" class="btn btn-primary mt-3 float-end" disabled>Pesan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const cartData = JSON.parse(sessionStorage.getItem("cart") || "[]");
    const checkoutItemsContainer = document.getElementById("checkout-items");
    const totalPriceEl = document.getElementById("total-price");
    const totalPaymentEl = document.getElementById("total_harga");
    const paymentStatus = document.getElementById("payment-status");
    const placeOrderBtn = document.getElementById("place-order");
    const memberStatus = document.getElementById("member-status");
    const phoneInput = document.getElementById("phone-input");
    const phoneNumber = document.getElementById("no_telepon");
    

    if (cartData.length === 0) {
        checkoutItemsContainer.innerHTML = "<p class='text-muted'>Tidak ada produk yang dipilih.</p>";
        return;
    }

    // Tampilkan produk
    checkoutItemsContainer.innerHTML = "";
    let totalPrice = 0;

    cartData.forEach(item => {
        const itemSubtotal = item.quantity * item.price;
        totalPrice += itemSubtotal;

        const itemHtml = `
            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <div>
                    <h6 class="mb-0">${item.name}</h6>
                    <small>${item.quantity} x Rp. ${new Intl.NumberFormat('id-ID').format(item.price)}</small>
                </div>
                <span class="fw-bold">Rp. ${new Intl.NumberFormat('id-ID').format(itemSubtotal)}</span>
            </div>
        `;
        checkoutItemsContainer.innerHTML += itemHtml;
    });

    totalPriceEl.textContent = `Rp. ${totalPrice.toLocaleString('id-ID')}`;

    // Toggle input nomor telepon jika member
    memberStatus.addEventListener("change", function () {
        phoneInput.style.display = this.value === "member" ? "block" : "none";
        if (this.value !== "member") phoneNumber.value = "";
    });

    // Validasi jumlah bayar
    totalPaymentEl.addEventListener("input", function () {
        let inputAmount = this.value.replace(/\D/g, "");
        inputAmount = parseInt(inputAmount) || 0;
        this.value = `Rp. ${inputAmount.toLocaleString('id-ID')}`;

        if (inputAmount < totalPrice) {
            paymentStatus.textContent = "Jumlah bayar kurang!";
            paymentStatus.classList.add("error-text");
            paymentStatus.classList.remove("success-text");
            placeOrderBtn.disabled = true;
        } else {
            const kembalian = inputAmount - totalPrice;
            paymentStatus.textContent = kembalian > 0 ? `Kembalian: Rp. ${kembalian.toLocaleString('id-ID')}` : "";
            paymentStatus.classList.remove("error-text");
            paymentStatus.classList.add("success-text");
            placeOrderBtn.disabled = false;
        }
    });

    // Validasi nomor telepon maksimal 12 digit angka
phoneNumber.addEventListener("input", function () {
    let input = this.value.replace(/\D/g, ""); // Hapus semua non-digit
    if (input.length > 12) {
        input = input.slice(0, 12); // Potong ke 12 digit
        document.getElementById("phone-error").style.display = "block";
        document.getElementById("phone-error").textContent = "Nomor telepon maksimal 12 digit.";
    } else {
        document.getElementById("phone-error").style.display = "none";
        document.getElementById("phone-error").textContent = "";
    }
    this.value = input;
});


   // Saat tombol "Pesan" diklik
   placeOrderBtn.addEventListener("click", function () {
    // Ambil ulang total harga dan data belanja
    let totalBarang = 0;
    let totalHarga = 0;

    cartData.forEach(item => {
            totalBarang += item.quantity;
            totalHarga += item.quantity * item.price;
        });
        
    let bayarInput = totalPaymentEl.value.replace(/\D/g, "");
    let customerPay = parseInt(bayarInput) || 0;
    let customerReturn = customerPay - totalHarga;

    const requestData = {
        // name_customer: phoneNumber.value || "Guest",
        no_telepon: phoneNumber.value || null,
        total_barang: cartData.map(item => item.quantity),
        products_id: cartData.map(item => item.id),
        total_harga: cartData.map(item => item.quantity * item.price),
        customer_pay: customerPay,
        customer_return: customerReturn > 0 ? customerReturn : 0,
        member_point_used: 0,
        total_harga_after_point: totalHarga,
    };

    if (memberStatus.value === "member" && phoneNumber.value.trim() !== "") {

        const params = new URLSearchParams(requestData).toString();
window.location.href = `/orders/member?${params}`;

    } else {
        // Bukan member → langsung simpan ke database
        Swal.fire({
            icon: 'success',
            title: 'Pesanan Berhasil!',
            text: 'Terima kasih telah berbelanja.',
            confirmButtonText: 'OK'
        }).then(() => {
            sessionStorage.removeItem("cart");

            fetch("{{ route('orders.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify(requestData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert("Redirect gagal.");
                }
            })
            .catch(err => {
                console.error("❌ Error:", err);
            });
        });
    }
});



});
</script>
@endsection
