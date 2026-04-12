<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{

use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function test_user_and_admin_can_get_categories()
    {
        $user = \App\Models\User::factory()->create();
        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->get('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ]);
    }
}
