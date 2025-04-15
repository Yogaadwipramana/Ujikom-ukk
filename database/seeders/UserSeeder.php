<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'PetugasKasir',
                'email' => 'petugas@gmail.com',
                'role' => 'petugas',
                'password' => Hash::make('1234'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'role' => 'admin',
                'password' => Hash::make('1234'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
