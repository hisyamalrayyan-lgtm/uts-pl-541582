<?php

namespace Tests\Feature;

use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TugasTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // AUTH TESTS
    // =========================================================

    /**
     * Test register returns 201 with token.
     */
    public function test_register_returns_201_with_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'Hisyam',
            'email'                 => 'hisyam@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'token',
                     'user' => ['id', 'name', 'email'],
                 ]);
    }

    /**
     * Test register validates required fields (422).
     */
    public function test_register_validates_required_fields(): void
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Test login returns token.
     */
    public function test_login_returns_token(): void
    {
        User::create([
            'name'     => 'Test User',
            'email'    => 'user@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'user@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'token', 'user']);
    }

    /**
     * Test login with wrong credentials returns 422.
     */
    public function test_login_with_wrong_credentials_returns_422(): void
    {
        $response = $this->postJson('/api/login', [
            'email'    => 'notexist@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test logout returns 200.
     */
    public function test_logout_returns_200(): void
    {
        $user = User::create([
            'name'     => 'Test User',
            'email'    => 'test' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Logged out successfully']);
    }

    /**
     * Test accessing protected route without token returns 401.
     */
    public function test_accessing_protected_route_without_token_returns_401(): void
    {
        $response = $this->getJson('/api/tugas');

        $response->assertStatus(401);
    }

    // =========================================================
    // TUGAS CRUD TESTS (require auth via Sanctum::actingAs)
    // =========================================================

    /**
     * Test GET /api/tugas returns 200 with auth.
     */
    public function test_get_tugas_returns_200(): void
    {
        $user = User::create([
            'name'     => 'Test User',
            'email'    => 'test' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/tugas');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data', 'message']);
    }

    /**
     * Test POST /api/tugas creates a record (201).
     */
    public function test_post_tugas_creates_record(): void
    {
        $user = User::create([
            'name'     => 'Test User',
            'email'    => 'test' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
        ]);
        Sanctum::actingAs($user);

        $payload = [
            'judul'     => 'Tugas Pertama',
            'deskripsi' => 'Deskripsi tugas pertama',
            'status'    => 'pending',
        ];

        $response = $this->postJson('/api/tugas', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.judul', 'Tugas Pertama');

        $this->assertDatabaseHas('tugas', ['judul' => 'Tugas Pertama']);
    }

    /**
     * Test POST /api/tugas without judul returns 422.
     */
    public function test_post_tugas_without_judul_returns_422(): void
    {
        $user = User::create([
            'name'     => 'Test User',
            'email'    => 'test' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/tugas', ['deskripsi' => 'Tanpa judul']);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['judul']);
    }

    /**
     * Test GET /api/tugas/{id} returns the correct data.
     */
    public function test_get_single_tugas_returns_correct_data(): void
    {
        $user = User::create([
            'name'     => 'Test User',
            'email'    => 'test' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
        ]);
        Sanctum::actingAs($user);

        $tugas = Tugas::create([
            'judul'     => 'Tugas Detail',
            'deskripsi' => 'Detail deskripsi',
            'status'    => 'selesai',
        ]);

        $response = $this->getJson("/api/tugas/{$tugas->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.judul', 'Tugas Detail')
                 ->assertJsonPath('data.status', 'selesai');
    }

    /**
     * Test PUT /api/tugas/{id} updates a record.
     */
    public function test_put_tugas_updates_record(): void
    {
        $user = User::create([
            'name'     => 'Test User',
            'email'    => 'test' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
        ]);
        Sanctum::actingAs($user);

        $tugas = Tugas::create([
            'judul'  => 'Tugas Lama',
            'status' => 'pending',
        ]);

        $response = $this->putJson("/api/tugas/{$tugas->id}", [
            'judul'  => 'Tugas Baru',
            'status' => 'selesai',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.judul', 'Tugas Baru')
                 ->assertJsonPath('data.status', 'selesai');
    }

    /**
     * Test DELETE /api/tugas/{id} deletes a record.
     */
    public function test_delete_tugas_removes_record(): void
    {
        $user = User::create([
            'name'     => 'Test User',
            'email'    => 'test' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
        ]);
        Sanctum::actingAs($user);

        $tugas = Tugas::create([
            'judul'  => 'Tugas Hapus',
            'status' => 'pending',
        ]);

        $response = $this->deleteJson("/api/tugas/{$tugas->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Tugas deleted successfully']);

        $this->assertDatabaseMissing('tugas', ['id' => $tugas->id]);
    }
}
