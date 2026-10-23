<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produks = [
            [
                'nama' => 'Laptop ASUS ROG Zephyrus G14',
                'kategori' => 'Elektronik',
                'harga' => 24500000,
                'stok' => 15,
                'deskripsi' => 'Laptop gaming ultra-portable dengan AMD Ryzen 9 dan RTX 4060.',
            ],
            [
                'nama' => 'Keyboard Mechanical Keychron K2',
                'kategori' => 'Aksesoris',
                'harga' => 1250000,
                'stok' => 40,
                'deskripsi' => 'Wireless mechanical keyboard 75% layout dengan Gateron Brown switch.',
            ],
            [
                'nama' => 'Mouse Logitech MX Master 3S',
                'kategori' => 'Aksesoris',
                'harga' => 1650000,
                'stok' => 25,
                'deskripsi' => 'Mouse ergonomis nirkabel dengan sensor 8000 DPI quiet click.',
            ],
            [
                'nama' => 'Monitor Dell UltraSharp U2723QE',
                'kategori' => 'Elektronik',
                'harga' => 8900000,
                'stok' => 10,
                'deskripsi' => 'Monitor 27 inch 4K UHD IPS Black dengan USB-C Hub 90W.',
            ],
        ];

        foreach ($produks as $item) {
            Produk::firstOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
