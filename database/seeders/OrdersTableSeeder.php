<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('orders')->insert([
            [
                'name_customer' => 'Pelanggam',
                'products_id' => json_encode([1, 2]), // misalnya ID produk
                'members_id' => 1,
                'users_id' => 1, // pastikan ada user dengan id 1
                'tanggal_penjualan' => now(),
                'total_barang' => 2,
                'total_harga' => 120000,
                'customer_pay' => 150000,
                'customer_return' => 30000,
                'member_point_used' => 500,
                'total_harga_after_point' => 115000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_customer' => 'Pelanggan',
                'products_id' => json_encode([3]),
                'members_id' => 2,
                'users_id' => 2,
                'tanggal_penjualan' => now(),
                'total_barang' => 1,
                'total_harga' => 50000,
                'customer_pay' => 60000,
                'customer_return' => 10000,
                'member_point_used' => 0,
                'total_harga_after_point' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
