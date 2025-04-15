<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Struk Pembelian</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        .total {
            background: #f0f0f0;
            padding: 10px;
            margin-top: 15px;
        }

        .center {
            text-align: center;
        }

        .no-border td {
            border: none !important;
            padding: 3px;
        }
    </style>
</head>

<body>

    <h2>Toko Jaya Abadi</h2>
    <hr>

    <table class="no-border">
        <tr>
            <td><strong>Member Status:</strong> {{ $order->member ? 'Member' : 'Non Member' }}</td>
        </tr>
        @if ($order->member)
            <tr>
                <<td><strong>No. HP:</strong> {{ $order->member->no_telepon ?? 'Non Member' }}</td>

            </tr>
            <tr>
                <td><strong>Bergabung Sejak:</strong>
                    {{ \Carbon\Carbon::parse($order->member->created_at)->format('d F Y') }}
                </td>
            </tr>
            <tr>
                <td><strong>Poin Member:</strong> {{ $order->member->point }} Point</td>
            </tr>
        @else
            <tr>
                <td colspan="1"><em>Data member tidak tersedia</em></td>
            </tr>
        @endif
    </table>


    <table>
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $index => $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $qty_list[$index] ?? 0 }}</td>
                    <td>Rp. {{ number_format($harga_list[$index] ?? 0, 0, ',', '.') }}</td>
                    <td>Rp. {{ number_format(($harga_list[$index] ?? 0) * ($qty_list[$index] ?? 0), 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>        

    </table>


    <div class="total">
        <p>Total Harga: Rp. {{ number_format($total_harga_sum, 0, ',', '.') }}</p>
        <p>Poin Digunakan: {{ number_format(floatval($order->point_used), 0, ',', '.') }} Point</p>
        <p>Harga Setelah Poin: Rp. {{ number_format(floatval($order->total_harga_after_point), 0, ',', '.') }}</p>
        <p>Uang Pembeli: Rp. {{ number_format(floatval($order->customer_pay), 0, ',', '.') }}</p>
        <p>Total Kembalian: Rp. {{ number_format(floatval($order->customer_return), 0, ',', '.') }}</p>

    </div>

    <p class="center" style="margin-top: 20px;">
        {{ $order->created_at }} | Petugas: Kasir <br>
        <strong>Terima kasih atas pembelian Anda!</strong>
    </p>

</body>

</html>
