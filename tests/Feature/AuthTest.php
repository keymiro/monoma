<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test successful login.
     *
     * @return void
     */
    public function testLoginSuccess(): void
    {
        // Create a user to login
        $password = $this->faker->password;
        $user = User::factory()->create([
            'password' => bcrypt($password)
        ]);

        // Send login request with valid credentials
        $response = $this->postJson(route('auth'), [
            'username' => $user->username,
            'password' => $password
        ]);

        // Assert response
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'meta' => ['success', 'errors'],
            'data' => ['token', 'minutes_to_expire']
        ]);
        $response->assertJson(['meta' => ['success' => true, 'errors' => []]]);

        // Assert the token is valid
        $token = $response->json('data.token');
        $this->assertTrue(JWTAuth::setToken($token)->check());
    }

    /**
     * Test invalid credentials login.
     *
     * @return void
     */
    public function testLoginInvalidCredentials(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('auth'), [
            'username' => $user->username,
            'password' => $this->faker->password
        ]);

        $response->assertStatus(401);
        $response->assertJsonStructure(['meta' => ['success', 'errors']]);
        $response->assertJson(['meta' => ['success' => false, 'errors' => ['Credenciales invalidas']]]);
    }

    /**
     * Test successful logout.
     *
     * @return void
     */
    public function testLogoutSuccess(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token);

        $response = $this->postJson(route('logout'));

        $response->assertStatus(200);
        $this->assertEquals('Haz salido correctamente', $response->json('message'));
    }

}
