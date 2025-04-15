@extends('layouts.templates')

@section('title', 'Members')

@section('content')
<div class="container">
    <h2 class="fw-bold">Members</h2>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Penjualan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Members</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Produk</th>
                                <th>QTY</th>
                                <th>Harga</th>
                                <th>Sub Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalHarga = 0;
                                $totalBayar = 0;
                            @endphp
                            @foreach($products as $item)
                                @php
                                    $subTotal = $item['total_barang'] * $item['total_harga'];
                                    $totalHarga += $subTotal;
                                    $totalBayar += $subTotal;
                                @endphp
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td>{{ $item['total_barang'] }}</td>
                                    <td>Rp. {{ number_format($item['total_harga'], 0, ',', '.') }}</td>
                                    <td>Rp. {{ number_format($subTotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <h5>Total Harga:
                        <span class="float-end" id="totalHarga">
                            Rp. {{ number_format((float) $total_harga, 0, ',', '.') }}
                        </span>
                    </h5>
                    <h5>Total Bayar:
                        <span class="float-end" id="totalBayar">
                            Rp. {{ number_format((float) $data['customer_pay'], 0, ',', '.') }}
                        </span>
                    </h5>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <form id="memberForm" method="POST" action="">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="nama_member">Nama Member</label>
                            <input type="text" class="form-control" id="nama_member" name="nama_member" value="{{ $nama_member }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="no_telepon">No Telepon</label>
                            <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="{{ $no_telepon }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="poin">Poin</label>
                            <input type="text" class="form-control" id="poin" name="poin" value="{{ $poin }}" readonly>
                        </div>
                        <div class="form-group form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="gunakan_poin" >
                            <label class="form-check-label" for="gunakan_poin">Gunakan poin</label>
                            <p class="text-danger small">Poin tidak dapat digunakan pada pembelanjaan pertama.</p>
                        </div>
                        <input type="hidden" name="gunakan_diskon" id="gunakan_diskon_input" value="0">


                        <!-- Hidden input -->
                        {{-- <input type="hidden" name="id_members" value="{{ $id_members }}"> --}}
                        <input type="hidden" name="products_id" value="{{ $products_id }}">
                        <input type="hidden" name="total_barang" value="{{ implode(',', array_column($products->toArray(), 'total_barang')) }}">
                        <input type="hidden" name="total_harga" value="{{ implode(',', array_column($products->toArray(), 'total_harga')) }}">
                        <input type="hidden" name="customer_pay" value="{{ $customer_pay }}">
                        <input type="hidden" name="customer_return" value="{{ $customer_return }}">
                        <input type="hidden" name="member_point_used" value="{{ $member_point_used }}">
                        <input type="hidden" name="total_harga_after_point" value="{{ $total_harga_after_point }}">

                        <button type="submit" class="btn btn-success">Simpan Transaksi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        const poin = parseInt($('#poin').val());
        const originalTotalHarga = {{ (float) $data['total_harga'] }};

        if (poin < 150) {
            $('#gunakan_poin').prop('disabled', true);
        }

        $('#gunakan_poin').on('change', function () {
            let isChecked = $(this).is(':checked');
            let totalHargaAfterPoint = originalTotalHarga;

            if (isChecked) {
                totalHargaAfterPoint -= 150;
                $('input[name="member_point_used"]').val(150);
            } else {
                $('input[name="member_point_used"]').val(0);
            }

            $('#totalHarga').text('Rp. ' + new Intl.NumberFormat('id-ID').format(totalHargaAfterPoint));
            $('input[name="total_harga_after_point"]').val(totalHargaAfterPoint);
        });

        $('#memberForm').on('submit', function(e) {
            e.preventDefault();

            const gunakanPoin = $('#gunakan_poin').is(':checked') ? 1 : 0;
            $('#gunakan_diskon_input').val(gunakanPoin);

            const formData = {
                _token: $('input[name="_token"]').val(),
                nama_member: $('#nama_member').val(),
                no_telepon: $('#no_telepon').val(),
                point: $('#poin').val(),
                gunakan_diskon: gunakanPoin,
                products_id: $('input[name="products_id"]').val(),
                total_barang: $('input[name="total_barang"]').val(),
                total_harga: $('input[name="total_harga"]').val(),
                customer_pay: $('input[name="customer_pay"]').val(),
                customer_return: $('input[name="customer_return"]').val(),
                member_point_used: $('input[name="member_point_used"]').val(),
                total_harga_after_point: $('input[name="total_harga_after_point"]').val(),
            };

            $.ajax({
                url: "{{ route('members.save') }}",
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Transaksi berhasil disimpan!',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "/orders/orders/detail-print/" + response.member_id;
                        }
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal menyimpan transaksi!',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Coba Lagi'
                    });
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>

@endsection
