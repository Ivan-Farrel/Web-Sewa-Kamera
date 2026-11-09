<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // --- KAMERA (SESUAI KODE AWAL) ---
            ['name' => 'Canon 1200D', 'category' => 'DSLR', 'price_8h' => 40000, 'price_12h' => 60000, 'price_24h' => 90000, 'price_2d' => 150000, 'overtime_per_hour' => 8000, 'image' => 'Canon 1200D .jpg'],
            ['name' => 'Canon 1300D', 'category' => 'DSLR', 'price_8h' => 60000, 'price_12h' => 90000, 'price_24h' => 100000, 'price_2d' => 160000, 'overtime_per_hour' => 10000, 'image' => 'Canon 1300D .jpg'],
            ['name' => 'Canon 700D', 'category' => 'DSLR', 'price_8h' => 70000, 'price_12h' => 100000, 'price_24h' => 125000, 'price_2d' => 220000, 'overtime_per_hour' => 12000, 'image' => 'Canon 700D.jpg'],
            ['name' => 'Sony A6000', 'category' => 'Mirrorless', 'price_8h' => 90000, 'price_12h' => 135000, 'price_24h' => 150000, 'price_2d' => 260000, 'overtime_per_hour' => 15000, 'image' => 'Sony a6000.jpg'],
            ['name' => 'Sony A6400', 'category' => 'Mirrorless', 'price_8h' => 100000, 'price_12h' => 150000, 'price_24h' => 170000, 'price_2d' => 300000, 'overtime_per_hour' => 17000, 'image' => 'Sony a6400.jpg'],
            ['name' => 'Sony A6500', 'category' => 'Mirrorless', 'price_8h' => 110000, 'price_12h' => 160000, 'price_24h' => 190000, 'price_2d' => 350000, 'overtime_per_hour' => 18000, 'image' => 'Sony a6500.jpg'],
            ['name' => 'Sony A7 II (BO)', 'category' => 'Mirrorless', 'price_8h' => 175000, 'price_12h' => 200000, 'price_24h' => 250000, 'price_2d' => 400000, 'overtime_per_hour' => 30000, 'image' => 'Sony A7 II.jpg'],
            ['name' => 'Sony A7 III (BO)', 'category' => 'Mirrorless', 'price_8h' => 200000, 'price_12h' => 250000, 'price_24h' => 300000, 'price_2d' => 500000, 'overtime_per_hour' => 30000, 'image' => 'Sony a7 III.jpg'],

            // --- TAMBAHAN LENSA CANON ---
            ['name' => 'Canon 50MM F 1.8 STM', 'category' => 'Lensa', 'price_8h' => 20000, 'price_12h' => 35000, 'price_24h' => 50000, 'price_2d' => 90000, 'overtime_per_hour' => 5000, 'image' => 'Canon 50mm f1.8.jpg'],
            ['name' => 'Canon 50MM F 1.4 USM', 'category' => 'Lensa', 'price_8h' => 40000, 'price_12h' => 50000, 'price_24h' => 70000, 'price_2d' => 120000, 'overtime_per_hour' => 6000, 'image' => 'Canon 50mm f1.4.jpg'],
            ['name' => 'Canon 10-18MM F 4.5', 'category' => 'Lensa', 'price_8h' => 40000, 'price_12h' => 55000, 'price_24h' => 75000, 'price_2d' => 135000, 'overtime_per_hour' => 8000, 'image' => 'Canon 10-18mm.jpg'],
            ['name' => 'Canon 70-200MM F 2.8 NON IS', 'category' => 'Lensa', 'price_8h' => 65000, 'price_12h' => 95000, 'price_24h' => 120000, 'price_2d' => 200000, 'overtime_per_hour' => 10000, 'image' => 'Canon 70-200mm.jpg'],
            ['name' => 'Samyang Fisheye 8MM (Canon)', 'category' => 'Lensa', 'price_8h' => 25000, 'price_12h' => 45000, 'price_24h' => 60000, 'price_2d' => 100000, 'overtime_per_hour' => 6000, 'image' => 'Samyang 8mm Canon.jpg'],
            ['name' => 'Sigma ART 35MM F 1.4 (Canon)', 'category' => 'Lensa', 'price_8h' => 40000, 'price_12h' => 55000, 'price_24h' => 75000, 'price_2d' => 135000, 'overtime_per_hour' => 8000, 'image' => 'Sigma 35mm Canon.jpg'],
            ['name' => 'Yongnuo 35MM F 2 (Canon)', 'category' => 'Lensa', 'price_8h' => 20000, 'price_12h' => 35000, 'price_24h' => 50000, 'price_2d' => 90000, 'overtime_per_hour' => 5000, 'image' => 'Yongnuo 35mm Canon.jpg'],
            ['name' => 'Tamron 70-300MM F 4-5.6 (Canon)', 'category' => 'Lensa', 'price_8h' => 35000, 'price_12h' => 50000, 'price_24h' => 60000, 'price_2d' => 100000, 'overtime_per_hour' => 6000, 'image' => 'Tamron 70-300mm Canon.jpg'],

            // --- TAMBAHAN LENSA SONY ---
            ['name' => 'Sony 28MM F 2 FE', 'category' => 'Lensa', 'price_8h' => 25000, 'price_12h' => 45000, 'price_24h' => 60000, 'price_2d' => 100000, 'overtime_per_hour' => 6000, 'image' => 'Sony 28mm f2.jpg'],
            ['name' => 'Sony 35MM F 1.8 OSS', 'category' => 'Lensa', 'price_8h' => 25000, 'price_12h' => 45000, 'price_24h' => 60000, 'price_2d' => 100000, 'overtime_per_hour' => 6000, 'image' => 'Sony 35mm OSS.jpg'],
            ['name' => 'Sony 35MM F 1.8 FE', 'category' => 'Lensa', 'price_8h' => 40000, 'price_12h' => 60000, 'price_24h' => 90000, 'price_2d' => 150000, 'overtime_per_hour' => 8000, 'image' => 'Sony 35mm FE.jpg'],
            ['name' => 'Sony 50MM F 1.8 FE', 'category' => 'Lensa', 'price_8h' => 25000, 'price_12h' => 45000, 'price_24h' => 60000, 'price_2d' => 100000, 'overtime_per_hour' => 6000, 'image' => 'Sony 50mm FE.jpg'],
            ['name' => 'Sony 85MM F 1.8 FE', 'category' => 'Lensa', 'price_8h' => 40000, 'price_12h' => 55000, 'price_24h' => 75000, 'price_2d' => 135000, 'overtime_per_hour' => 8000, 'image' => 'Sony 85mm FE.jpg'],
            ['name' => 'Sony 70-200MM F 4 FE', 'category' => 'Lensa', 'price_8h' => 110000, 'price_12h' => 130000, 'price_24h' => 150000, 'price_2d' => 260000, 'overtime_per_hour' => 15000, 'image' => 'Sony 70-200mm f4.jpg'],
            ['name' => 'Sigma DC 16MM F 1.4 (Sony)', 'category' => 'Lensa', 'price_8h' => 45000, 'price_12h' => 60000, 'price_24h' => 75000, 'price_2d' => 135000, 'overtime_per_hour' => 8000, 'image' => 'Sigma 16mm Sony.jpg'],
            ['name' => 'Sigma ART 24-70MM F 2.8 (Sony)', 'category' => 'Lensa', 'price_8h' => 100000, 'price_12h' => 120000, 'price_24h' => 140000, 'price_2d' => 250000, 'overtime_per_hour' => 14000, 'image' => 'Sigma 24-70mm Sony.jpg'],
            ['name' => 'Sigma ART 35MM F 1.4 (Sony)', 'category' => 'Lensa', 'price_8h' => 60000, 'price_12h' => 90000, 'price_24h' => 100000, 'price_2d' => 160000, 'overtime_per_hour' => 10000, 'image' => 'Sigma 35mm Sony.jpg'],
            ['name' => 'Artisan 7.5MM F 2.8 Fisheye (Sony)', 'category' => 'Lensa', 'price_8h' => 45000, 'price_12h' => 60000, 'price_24h' => 75000, 'price_2d' => 135000, 'overtime_per_hour' => 8000, 'image' => 'Artisan 7.5mm Sony.jpg'],
            ['name' => 'Tamron 28-75MM F 2.8 (Sony)', 'category' => 'Lensa', 'price_8h' => 100000, 'price_12h' => 120000, 'price_24h' => 140000, 'price_2d' => 250000, 'overtime_per_hour' => 14000, 'image' => 'Tamron 28-75mm Sony.jpg'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}