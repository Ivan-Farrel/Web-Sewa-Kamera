<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\KycSubmission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Administrator
        $admin = User::updateOrCreate(
            ['email' => 'admin@camhub.com'],
            [
                'name' => 'Admin CamHub',
                'phone' => '081200000001',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // 2. Akun Mitra / Penjual Titip Sewa
        $mitra = User::updateOrCreate(
            ['email' => 'mitra@camhub.com'],
            [
                'name' => 'Budi Rental Kamera',
                'phone' => '081288887777',
                'password' => Hash::make('password123'),
                'role' => 'mitra',
            ]
        );

        // Tambah KYC terverifikasi untuk Mitra
        KycSubmission::firstOrCreate(
            ['user_id' => $mitra->id],
            [
                'nama_lengkap' => 'Budi Santoso (Mitra)',
                'nik' => '3578012345670001',
                'tanggal_lahir' => '1992-08-17',
                'jenis_kelamin' => 'Laki-laki',
                'dokumen_path' => 'kyc/sample_ktp.jpg',
                'status' => 'Disetujui',
            ]
        );

        // 3. Akun Pembeli (Jual Beli & Sewa)
        $customer = User::updateOrCreate(
            ['email' => 'pembeli@camhub.com'],
            [
                'name' => 'Rian Fotografer',
                'phone' => '081399998888',
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ]
        );

        // Hubungkan beberapa produk ke Mitra
        Product::whereIn('id', [1, 4, 8])->update(['user_id' => $mitra->id]);
    }
}
