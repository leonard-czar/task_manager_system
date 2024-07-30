<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions, WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_register_user()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token']);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    public function test_register_user_input_is_validated()
    {
        $response = $this->postJson('/api/register', []);


        $response->assertStatus(422)->assertJson([
            'error' => [
                'name' => ['The name field is required.'],
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.']
            ]
        ]);
    }

    public function test_login_user()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token']);
    }

    public function test_login_user_with_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'email@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'invalid login details']);
    }

    public function test_logout_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'logged out successfully']);

    }
}
