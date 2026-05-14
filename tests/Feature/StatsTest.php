<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StatsTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function test_authenticated_user_can_view_community_stats()
    {
        $user = \App\Models\User::factory()->create();
        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/stats/community');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'total_users',
                'total_notes',
                'total_comments',
                'total_thanks',
            ]
        ]);
    }

    public function test_unauthenticated_user_cannot_view_stats()
    {
        $response = $this->getJson('/api/stats/community');
        $response->assertStatus(401);

        $response = $this->getJson('/api/stats/top-helpers');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_view_top_helpers()
    {
        $community = \App\Models\Community::factory()->create();
        $user = \App\Models\User::factory()->create(['community_id' => $community->id]);
        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/stats/top-helpers');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'user_id',
                    'name',
                    'thanks_count',
                ]
            ]
        ]);
    }
}
