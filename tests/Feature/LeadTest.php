<?php


namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Lead;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function testIndexWithValidTokenAndManagerRole()
    {
        $user = User::where('username', 'manager')->first();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->get(route('leads.index'));

        $response->assertStatus(200);
    }

    public function testIndexWithValidTokenAndAgentRole()
    {
        $user = User::where('username', 'agent')->first();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->get(route('leads.index'));

        $response->assertStatus(200);
    }

    public function testIndexWithExpiredToken()
    {
        $user = User::where('username', 'manager')->first();

        $token = JWTAuth::fromUser($user);

        JWTAuth::setToken($token)->invalidate();

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->get(route('leads.index'));

        $response->assertStatus(401);
    }

    public function testShowWithValidTokenAndAuthorizedUser()
    {
        $user = User::factory()->create(['role' => 'manager']);

        $token = JWTAuth::fromUser($user);

        $lead = Lead::factory()->create(['owner' => $user->id]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->get(route('leads.show', $lead->id));

        $response->assertStatus(200);
    }

    public function testShowWithValidTokenAndUnauthorizedUser()
    {
        $user = User::factory()->create(['role' => 'agent']);

        $token = JWTAuth::fromUser($user);

        $lead = Lead::where('owner', '!=', $user->id)->first();

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->get(route('leads.show', $lead->id));

        $response->assertStatus(401);
    }

    public function testShowWithExpiredToken()
    {
        $user = User::factory()->create(['role' => 'agent']);

        $token = JWTAuth::fromUser($user);

        // Simulate token expiration
        JWTAuth::setToken($token)->invalidate();

        $lead = Lead::factory()->create(['owner' => $user->id]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->get(route('leads.show', $lead->id));

        $response->assertStatus(401);
    }

    public function testStoreWithValidTokenAndManagerRole()
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $lead = Lead::factory()->create(['owner' => $manager->id])->toArray();
        $token = JWTAuth::fromUser($manager, ['exp' => Carbon::now()->addHours(24)->timestamp]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->post(route('leads.store'), $lead);

        $response->assertStatus(201);
    }

}
