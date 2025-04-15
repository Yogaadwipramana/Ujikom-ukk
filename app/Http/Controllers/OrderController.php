<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Member;
use App\Models\Product;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use PDF;

use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->get('limit', 10); // default 10
        $search = $request->get('q');

        $orders = Order::query()
            ->with(['member', 'user'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('member', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return view('layouts.orders.index', compact('orders', 'limit'));
    }



    public function memberPage(Request $request)
    {
        $data = $request->all();

        $nama_member = '';
        $point = 0;


        // Cari member berdasarkan no_telepon
        if (!empty($data['no_telepon'])) {
            $member = Member::where('no_telepon', $data['no_telepon'])->first();

            if ($member) {

                $nama_member = $member->name;
                $point        = $member->point;
            }
        }


        // dd($id_member   );

        // Ambil data produk dari ID
        $productIds = explode(',', $data['products_id'] ?? '');
        $products = Product::whereIn('id', $productIds)->get();

        // total_barang dan total_harga dipisah pakai koma
        $totalBarang = explode(',', $data['total_barang'] ?? '');
        $totalHarga = explode(',', $data['total_harga'] ?? '');

        $totalHarga = 0;

        foreach ($products as $index => $product) {
            $jumlahBarang = isset($totalBarang[$index]) ? (int)$totalBarang[$index] : 1;
            $hargaTotal = isset($totalHarga[$index]) ? (int)$totalHarga[$index] : ($product->price * $jumlahBarang);

            $product->total_barang = $jumlahBarang;
            $product->total_harga = $hargaTotal;

            // Tambahkan ke total harga keseluruhan
            $totalHarga += $hargaTotal;
        }

        // dd($products);
        return view('layouts.orders.member', [

            'data' => $data,
            'nama_member' => $nama_member,
            'no_telepon' => $data['no_telepon'] ?? '',
            'poin' => $point,
            'products_id' => $data['products_id'] ?? '',
            'customer_pay' => $data['customer_pay'] ?? 0,
            'customer_return' => $data['customer_return'] ?? 0,
            'member_point_used' => $data['member_point_used'] ?? 0,
            'total_harga_after_point' => $data['total_harga_after_point'] ?? 0,
            'products' => $products,
            'total_harga' => $totalHarga, // Kirim total harga keseluruhan
        ]);
    }

    public function checkPhone(Request $request)
    {
        $exists = Member::where('no_telepon', $request->no_telepon)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }



    public function save(Request $request)
    {
        \DB::beginTransaction();

        try {
            $member = Member::where('no_telepon', $request->no_telepon)->first();

            if (!$member) {
                $member = Member::create([
                    'no_telepon' => $request->no_telepon,
                    'name' => $request->nama_member,
                    'point' => 0,
                ]);
            } else {
                $member->update([
                    'name' => $request->nama_member,
                ]);
            }

            $productIds = explode(',', $request->products_id);
            $totalBarang = explode(',', $request->total_barang);
            $totalHarga = explode(',', $request->total_harga);

            $totalHargaSum = array_sum($totalHarga);

            $gunakanDiskon = $request->gunakan_diskon == 1;
            $totalHargaAfterDiskon = $totalHargaSum;

            $diskonPoint = 0;
            if ($gunakanDiskon && $member->point >= 150) {
                $diskonPoint = 150;
                $totalHargaAfterDiskon -= $diskonPoint;
                $member->point -= $diskonPoint;
            }

            if ($totalHargaAfterDiskon > 50000) {
                $member->point += 100;
            }

            $member->save();

            $customerPay = (int)$request->customer_pay;
            $kembalian = $customerPay - $totalHargaAfterDiskon;

            $order = Order::create([
                'members_id' => $member->id,
                'total_barang' => json_encode(array_map('intval', $totalBarang)),
                'total_harga' => json_encode(array_map('intval', $totalHarga)),
                'total_harga_after_point' => $totalHargaAfterDiskon,
                'customer_pay' => $request->customer_pay,
                'customer_return' => $kembalian,
                'member_point_used' => $request->member_point_used,
                'products_id' => json_encode(array_map('intval', $productIds)),
                'users_id' => auth()->id(),
                'tanggal_penjualan' => now('Asia/Jakarta'),
            ]);

            foreach ($productIds as $index => $productId) {
                $product = Product::find((int)$productId);
                $qty = (int)$totalBarang[$index];
                $product->stock -= $qty;
                $product->save();
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan!',
                'order_id' => $order->id,
                'member_id' => $member->id,
            ]);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }



    public function show($id)
    {
        $order = Order::with(['user', 'member'])->findOrFail($id);

        $products = json_decode($order->products_id, true);
        $total_barang = json_decode($order->total_barang, true);
        $total_harga = json_decode($order->total_harga, true);

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !is_array($products) ||
            !is_array($total_barang) ||
            !is_array($total_harga)
        ) {
            return response("Data tidak valid atau gagal di-decode.", 400);
        }

        // Informasi member (jika ada)
        $member = $order->member;
        $isMember = !is_null($member);
        $memberName = $isMember ? $member->name : 'Non Members';
        $noTelepon = $isMember ? $member->no_telepon : '-';
        $point = $isMember ? $member->point : '-';

        $html = '<div style="font-family: Arial, sans-serif; font-size: 14px;">';
        $html .= '<h5><strong>Informasi Pelanggan</strong></h5>';
        $html .= '<table style="width: 100%; margin-bottom: 10px;">';
        $html .= '<tr><td style="width: 150px;">Nama Pelanggan</td><td>: ' . $memberName . '</td></tr>';

        if ($isMember) {
            $html .= '<tr><td>No. Telepon</td><td>: ' . $noTelepon . '</td></tr>';
            $html .= '<tr><td>Point Dimiliki</td><td>: ' . $point . '</td></tr>';
        }

        $html .= '<tr><td>Tanggal</td><td>: ' . $order->created_at->format('d M Y') . '</td></tr>';
        $html .= '<tr><td>Dibuat oleh</td><td>: ' . ($order->user->name ?? '-') . '</td></tr>';
        $html .= '</table>';
        $html .= '<hr>';

        $html .= '<h5><strong>Detail Barang</strong></h5>';
        $html .= '<table class="table table-bordered" style="width: 100%; border-collapse: collapse;">';
        $html .= '<thead>';
        $html .= '<tr style="background-color: #f2f2f2;">';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px;">Produk</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px;">Qty</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px;">Harga</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px;">Subtotal</th>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        $total = 0;

        foreach ($products as $key => $productId) {
            $product = \App\Models\Product::find($productId);
            $name = $product ? $product->name : '-';

            $qty = $total_barang[$key] ?? 0;
            $price = $total_harga[$key] ?? 0;
            $subtotal = $qty * $price;
            $total += $subtotal;

            $html .= '<tr>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $name . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $qty . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">Rp. ' . number_format($price, 0, ',', '.') . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">Rp. ' . number_format($subtotal, 0, ',', '.') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= '<hr>';

        $html .= '<table style="width: 100%; margin-top: 10px;">';
        $html .= '<tr><td style="width: 150px;">Total</td><td>: <strong>Rp. ' . number_format($total, 0, ',', '.') . '</strong></td></tr>';
        $html .= '<tr><td>Pembayaran</td><td>: Rp. ' . number_format($order->customer_pay, 0, ',', '.') . '</td></tr>';
        $html .= '<tr><td>Kembalian</td><td>: Rp. ' . number_format($order->customer_return, 0, ',', '.') . '</td></tr>';
        $html .= '</table>';

        $html .= '</div>';

        return response($html);
    }

    public function search(Request $request)
    {
        $query = $request->q;

        if ($query) {
            $orders = Order::with('member', 'user')
                ->whereHas('member', function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%");
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $orders = Order::with('member', 'user')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        Log::info('Hasil pencarian order:', $orders->toArray());

        $output = "";

        if ($orders->count() > 0) {
            foreach ($orders as $index => $order) {
                $totalHargaArray = json_decode($order->total_harga, true);
                $totalHarga = is_array($totalHargaArray) ? array_sum($totalHargaArray) : 0;

                $output .= '
                        <tr>
                            <td>' . ($index + 1) . '</td>
                            <td>' . ($order->member->name ?? 'Non Member') . '</td>
                            <td>' . $order->created_at->format('Y-m-d') . '</td>
                            <td>Rp. ' . number_format($totalHarga, 0, ',', '.') . '</td>
                            <td>' . ($order->user->name ?? 'Non Member') . '</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-warning btn-sm btn-lihat" data-id="' . $order->id . '">Lihat</button>
                                    <a href="' . route('orders.export', $order->id) . '" class="btn btn-primary btn-sm">Unduh Bukti</a>
                                </div>
                            </td>
                        </tr>';
            }
        } else {
            $output = '<tr><td colspan="6" class="text-center">Data tidak ditemukan</td></tr>';
        }

        return response($output);
    }




    public function create()
    {
        $products = Product::all();
        return view('layouts.orders.create', compact('products'));
    }


    public function checkout()
    {
        return view('layouts.orders.checkout');
    }



    public function detailPrint($id)
    {
        $order = Order::where('members_id', $id)->latest()->first();

        if (!$order) {
            $order = Order::where('id', $id)->first();

            if (!$order) {
                abort(404, 'Order tidak ditemukan.');
            }
        }

        // Ambil array produk dan jumlah barang
        $productIds = is_array($order->products_id)
            ? $order->products_id
            : json_decode($order->products_id, true);

        $totalBarang = is_array($order->total_barang)
            ? $order->total_barang
            : json_decode($order->total_barang, true);

        $products = \App\Models\Product::whereIn('id', $productIds)->get();

        foreach ($products as $index => $product) {
            $product->total_barang = $totalBarang[$index] ?? 1;
        }

        return view('layouts.orders.detailPrint', compact('order', 'products'));
    }



    public function toMemberPage(Request $request)
    {
        // Validasi input
        $request->validate([
            'cart' => 'required|array',
            'no_telepon' => 'required|string|max:20',
        ]);

        $cart = $request->input('cart');

        // Hitung total harga dari cart
        $totalHarga = collect($cart)->sum(function ($item) {
            return $item['quantity'] * $item['price'];
        });

        // Hitung point jika total >= 50000
        $point = $totalHarga >= 50000 ? 10000 : 0;

        // Simpan ke session untuk digunakan di halaman member
        session([
            'cart' => $cart,
            'no_telepon' => $request->input('no_telepon'),
            'point' => $point,
        ]);

        return response()->json([
            'redirect' => route('orders.member')
        ]);
    }

    public function member()
    {
        return view('layouts.orders.member', [
            'cart' => session('cart') ?? [],
            'nama_member' => '', // kosong, agar diisi manual
            'no_telepon' => session('no_telepon') ?? '',
            'point' => session('point') ?? 0,
        ]);
    }



    public function export()
    {
        return Excel::download(new OrdersExport, 'laporan-pembelian.xlsx');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $member = null;
        if ($request->no_telepon) {
            $member = Member::where('no_telepon', $request->no_telepon)->first();
        }

        // Buat order terlebih dahulu
        $order = Order::create([
            'products_id' => json_encode(array_map('intval', $data['products_id'])),
            'members_id' => $member?->id,
            'users_id' => auth()->id(),
            'tanggal_penjualan' => now(),
            'total_barang' => json_encode(array_map('intval', $data['total_barang'])),
            'total_harga' => json_encode(array_map('intval', $data['total_harga'])),
            'customer_pay' => $data['customer_pay'],
            'customer_return' => $data['customer_return'],
            'member_point_used' => $data['member_point_used'],
            'total_harga_after_point' => $data['total_harga_after_point'],
        ]);

        // Update stok produk
        foreach ($data['products_id'] as $index => $productId) {
            $qty = $data['total_barang'][$index];

            $product = Product::find($productId);
            if ($product) {
                $product->stock -= $qty;
                $product->save();
            }
        }

        return response()->json([
            'message' => 'Order berhasil disimpan!',
            'redirect' => route('orders.detailPrint', $order->id)
        ]);
    }

    public function cetakStruk($id)
    {
        $order = Order::with(['member'])->findOrFail($id);

        // Decode JSON dari database
        $productIDs = json_decode($order->products_id, true);
        $qtyList = json_decode($order->total_barang, true);
        $hargaList = json_decode($order->total_harga, true);

        // Ambil data produk dari tabel products
        $products = Product::whereIn('id', $productIDs)->get();

        // Hitung total harga (qty * harga per item)
        $totalHargaSum = 0;
        foreach ($qtyList as $i => $qty) {
            $harga = $hargaList[$i] ?? 0;
            $totalHargaSum += $qty * $harga;
        }

        // Hitung diskon point dan kembalian
        $pointUsed = (int) ($order->point_used ?? 0);
        $customerPay = (int) ($order->customer_pay ?? 0);
        $order->total_harga_after_point = $totalHargaSum - $pointUsed;
        $order->customer_return = $customerPay - $order->total_harga_after_point;

        return PDF::loadView('layouts.orders.struk', [
            'order' => $order,
            'products' => $products,
            'qty_list' => $qtyList,
            'harga_list' => $hargaList,
            'total_harga_sum' => $totalHargaSum, // Dikirim ke view
        ])->setPaper('A5')->download('struk-pembelian-' . $order->id . '.pdf');
    }


}
