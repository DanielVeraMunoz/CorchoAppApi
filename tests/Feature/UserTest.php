<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

        use RefreshDatabase;

    public function test_authenticated_user_can_list_users()
    {
        $user = \App\Models\User::factory()->create();
        $token = $user->createToken('auth_token')->accessToken;

        \App\Models\User::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/users');

        $response->assertStatus(200);
        $response->assertJsonCount(6, 'data'); 
    }

}
