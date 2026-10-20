<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProdukTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to create an authenticated user.
     */
    private function authenticateUser(): User
    {
        $user = User::create([
            'name'     => 'Hisyam Tester',
            'email'    => 'tester_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
        ]);

        Sanctum::actingAs($user);

        return $user;
    }

    /**
     * Test GET /api/produk without token returns 401.
     */
    public function test_get_produk_without_token_returns_401(): void
    {
        $response = $this->getJson('/api/produk');

        $response->assertStatus(401);
    }

    /**
     * Test GET /api/produk with token returns 200.
     */
    public function test_get_produk_with_token_returns_200(): void
    {
        $this->authenticateUser();

        Produk::create([
            'nama'      => 'Laptop Asus ROG',
            'kategori'  => 'Elektronik',
            'harga'     => 15000000,
            'stok'      => 5,
            'deskripsi' => 'Laptop gaming bertenaga tinggi',
        ]);

        $response = $this->getJson('/api/produk');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         '*' => ['id', 'nama', 'kategori', 'harga', 'stok', 'deskripsi'],
                     ],
                 ]);
    }

    /**
     * Test POST /api/produk creates a product and returns 201.
     */
    public function test_post_produk_creates_record_returns_201(): void
    {
        $this->authenticateUser();

        $payload = [
            'nama'      => 'Keyboard Mechanical',
            'kategori'  => 'Aksesoris',
            'harga'     => 750000,
            'stok'      => 20,
            'deskripsi' => 'Keyboard mechanical RGB blue switch',
        ];

        $response = $this->postJson('/api/produk', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('data.nama', 'Keyboard Mechanical')
                 ->assertJsonPath('data.stok', 20);

        $this->assertDatabaseHas('produks', [
            'nama'     => 'Keyboard Mechanical',
            'kategori' => 'Aksesoris',
        ]);
    }

    /**
     * Test POST /api/produk with invalid data returns 422.
     */
    public function test_post_produk_validation_failure_returns_422(): void
    {
        $this->authenticateUser();

        // Empty payload to trigger validation errors
        $response = $this->postJson('/api/produk', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nama', 'kategori', 'harga', 'stok']);
    }

    /**
     * Test GET /api/produk/{id} returns single product.
     */
    public function test_get_single_produk_returns_correct_data(): void
    {
        $this->authenticateUser();

        $produk = Produk::create([
            'nama'      => 'Monitor Gaming 24 Inch',
            'kategori'  => 'Elektronik',
            'harga'     => 2100000,
            'stok'      => 8,
            'deskripsi' => '144Hz IPS panel',
        ]);

        $response = $this->getJson("/api/produk/{$produk->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.nama', 'Monitor Gaming 24 Inch')
                 ->assertJsonPath('data.harga', '2100000.00');
    }

    /**
     * Test PUT /api/produk/{id} updates the product.
     */
    public function test_put_produk_updates_record(): void
    {
        $this->authenticateUser();

        $produk = Produk::create([
            'nama'      => 'Mouse Wireless',
            'kategori'  => 'Aksesoris',
            'harga'     => 150000,
            'stok'      => 10,
        ]);

        $response = $this->putJson("/api/produk/{$produk->id}", [
            'nama'  => 'Mouse Wireless RGB',
            'stok'  => 15,
            'harga' => 175000,
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.nama', 'Mouse Wireless RGB')
                 ->assertJsonPath('data.stok', 15);

        $this->assertDatabaseHas('produks', [
            'id'   => $produk->id,
            'nama' => 'Mouse Wireless RGB',
            'stok' => 15,
        ]);
    }

    /**
     * Test DELETE /api/produk/{id} removes the product.
     */
    public function test_delete_produk_removes_record(): void
    {
        $this->authenticateUser();

        $produk = Produk::create([
            'nama'      => 'Kabel HDMI 2m',
            'kategori'  => 'Aksesoris',
            'harga'     => 45000,
            'stok'      => 30,
        ]);

        $response = $this->deleteJson("/api/produk/{$produk->id}");

        $response->assertStatus(200)
                 ->assertJson(['status' => 'success']);

        $this->assertDatabaseMissing('produks', ['id' => $produk->id]);
    }
}
