<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order; 
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // Data produk untuk chart pie
        $products = Product::select('name', 'stock')->get();
    
        // Ambil total penjualan (dalam rupiah) berdasarkan tanggal_penjualan (5 hari terakhir)
        $salesData = Order::select(
                DB::raw('DATE(tanggal_penjualan) as date'),
                DB::raw('SUM(total_harga_after_point) as total_sales')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get()
            ->sortBy('date'); // urutkan dari tanggal lama ke terbaru
    
        // Siapkan label & data untuk chart dengan format tanggal Indonesia (WIB)
        $salesLabels = $salesData->pluck('date')->map(function($date) {
            return Carbon::parse($date, 'Asia/Jakarta')->format('d M Y');
        });
    
        $salesCounts = $salesData->pluck('total_sales');
    
        // Total penjualan hari ini (WIB)
        $today = Carbon::now(new \DateTimeZone('Asia/Jakarta'))->startOfDay();

        $totalPenjualan = Order::whereDate('tanggal_penjualan', $today)
            ->sum('total_harga_after_point');
    
        return view('layouts.dashboard', compact(
            'products', 'salesLabels', 'salesCounts', 'totalPenjualan', 'today'
        ));
    }
}
