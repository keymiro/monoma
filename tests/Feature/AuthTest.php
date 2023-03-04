<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    /**
     * Test successful login
     *
     * @return void
     */
    public function testLoginSuccess(): void
    {
        // create a user to login
        $user = User::factory()->create([
            'password' => bcrypt($password = $this->faker->password)
        ]);

        // send login request with valid credentials
        $response = $this->postJson(route('auth'), [
            'username' => $user->username,
            'password' => $password
        ]);

        // assert response
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'meta' => ['success', 'errors'],
            'data' => ['token', 'minutes_to_expire']
        ]);
        $response->assertJson(['meta' => ['success' => true, 'errors' => []]]);
    }

    /**
     * Test invalid credentials login
     *
     * @return void
     */
    public function testLoginInvalidCredentials(): void
    {
        // create a user to login
        $user = User::factory()->create();

        // send login request with invalid credentials
        $response = $this->postJson(route('auth'), [
            'username' => $user->username,
            'password' => $this->faker->password
        ]);

        // assert response
        $response->assertStatus(JsonResponse::HTTP_UNAUTHORIZED);
        $response->assertJsonStructure(['meta' => ['success', 'errors']]);
        $response->assertJson(['meta' => ['success' => false, 'errors' => ['Credenciales invalidas']]]);
    }

    /**
     * Test logout
     *
     * @return void
     */
    public function testLogout(): void
    {
        // create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // send logout request
        $response = $this->postJson(route('logout'));

        // assert response
        $response->assertSuccessful();
        $response->assertJson(['message' => 'Haz salido correctamente']);
    }
}
