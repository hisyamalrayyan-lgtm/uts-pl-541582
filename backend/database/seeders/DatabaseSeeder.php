<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat user default untuk login/testing tanpa memicu factory segfault
        User::firstOrCreate(
            ['email' => 'admin@kepl.test'],
            [
                'name' => 'Admin KEPL',
                'password' => Hash::make('password123'),
            ]
        );

        $this->call([
            TugasSeeder::class,
            ProdukSeeder::class,
        ]);
    }
}
