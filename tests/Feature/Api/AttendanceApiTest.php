<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AttendanceApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test API Login authentication.
     */
    public function test_api_login_validation_and_token_response(): void
    {
        $password = 'secret-pass-123';
        $user = User::factory()->create([
            'email' => 'karyawan@test.com',
            'password' => Hash::make($password),
            'api_token' => 'test-api-token-value',
        ]);

        // Test login with invalid password
        $response = $this->postJson('/api/login', [
            'email' => 'karyawan@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);

        // Test login with correct password
        $responseSuccess = $this->postJson('/api/login', [
            'email' => 'karyawan@test.com',
            'password' => $password,
        ]);

        $responseSuccess->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                ]
            ]);
    }

    /**
     * Test protected API profile retrieval.
     */
    public function test_api_profile_requires_auth_and_returns_payload(): void
    {
        // 1. Without Token
        $responseNoAuth = $this->getJson('/api/profile');
        $responseNoAuth->assertStatus(401);

        // 2. With Valid Token
        $user = User::factory()->create([
            'api_token' => 'secret-auth-token-12345',
            'birth_date' => '1998-05-15',
        ]);

        $responseAuth = $this->getJson('/api/profile', [
            'Authorization' => 'Bearer secret-auth-token-12345'
        ]);

        $responseAuth->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'email' => $user->email,
                    'birth_date' => '1998-05-15',
                ]
            ]);
    }
}
