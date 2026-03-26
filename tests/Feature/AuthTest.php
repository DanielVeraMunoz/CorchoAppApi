<?php

namespace Tests\Feature;

use App\Models\Community;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {

        $community = Community::factory()->create();

        $data = [
            'name' => 'Test Register',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'community_id' => $community->id,
            'floor' => '2',
            'door' => 'A',
        ];


        $response = $this->postJson('/api/register', $data);


        $response->assertStatus(201);

        $response->assertJsonStructure([
            'message',
            'data' => ['id', 'name', 'email'],
            'access_token',
            'token_type',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_login(): void
    {
        //Arrange
        
        $community = Community::factory()->create();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'community_id' => $community->id
        ]);

        //Act

        $response = $this->postJson('/api/login',[
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        //Assert

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'data' => ['id', 'name', 'email'],
            'access_token',
            'token_type',
        ]);


    }
}
