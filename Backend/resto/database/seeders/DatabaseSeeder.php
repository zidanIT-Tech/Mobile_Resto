<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun ADMIN
        User::create([
            'username' => 'Pak Bos',
            'email' => 'admin@resto.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'aktif'
        ]);

        // 2. Akun CHEF (Perhatikan role-nya 'chef')
        User::create([
            'username' => 'Chef Juna',
            'email' => 'chef@resto.com',
            'password' => Hash::make('password'),
            'role' => 'chef',
            'status' => 'aktif'
        ]);

        // 3. Akun KASIR
        User::create([
            'username' => 'Mbak Kasir',
            'email' => 'kasir@resto.com',
            'password' => Hash::make('password'),
            'role' => 'kasir',
            'status' => 'aktif'
        ]);
    }
}