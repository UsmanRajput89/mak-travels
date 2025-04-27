<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:client --personal');
    }

    #[Test]
    public function user_can_register_successfully()
    {
        $response = $this->postJson('/api/register', [
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'confirm_password' => 'password',
        ]);


        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'User Registered Successfully',
            ]);
    }

    #[Test]
    public function user_can_login_successfully()
    {
        // First, register the user
        $this->postJson('/api/register', [
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'confirm_password' => 'password',
        ]);

        // Now attempt to login
        $response = $this->postJson('/api/login', [
            'email' => 'testuser@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'User logged in successfully',
            ]);
    }
    #[Test]
    public function user_cannot_register_with_invalid_data()
    {
        $response = $this->postJson('/api/register', [
            'username' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'confirm_password' => '321',
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function user_cannot_login_with_wrong_credentials()
    {
        // First, register the user
        $this->postJson('/api/register', [
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'confirm_password' => 'password',
        ]);

        // Attempt to login with wrong credentials
        $response = $this->postJson('/api/login', [
            'email' => 'testuser@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => 0,
                'message' => 'Invalid credentials',
            ]);
    }

    #[Test]
    public function user_can_logout_successfully()
    {
        // First, register the user
        $this->postJson('/api/register', [
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'confirm_password' => 'password',
        ]);

        // Login to get the token
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'testuser@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.token');

        // Logout
        $response = $this->postJson('/api/logout', [], ['Authorization' => "Bearer $token"]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Successfully logged out',
            ]);
    }


}
