<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('members')->insert([
            [
                'name' => 'Andi Wijaya',
                'no_telepon' => '081234567890',
                'point' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sari Maulana',
                'no_telepon' => '089876543210',
                'point' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
