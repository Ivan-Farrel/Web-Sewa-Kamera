<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if ($email && $password) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => env('ADMIN_NAME', 'Administrator'),
                    'password' => $password,
                    'role' => 'admin',
                ],
            );
        }
    }
}
