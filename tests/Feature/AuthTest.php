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

        $community = Community::factory()->create(['invite_code' => 'TEST-1234']);

        $data = [
            'name' => 'Test Register',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'invite_code' => 'TEST-1234',
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

    public function test_user_cant_login_with_wrong_password(): void
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
            'password' => 'wrongpassword',
        ]);

        //Assert
        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Credenciales incorrectas',
        ]);
    }

    public function test_register_with_invalid_invite_code_returns_422(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test2@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'invite_code' => 'INVALID-CODE',
            'floor' => '2',
            'door' => 'A',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);
    }

    public function test_register_with_weak_password_returns_422(): void
    {
        $community = Community::factory()->create(['invite_code' => 'TEST-9999']);

        $data = [
            'name' => 'Test User',
            'email' => 'weak@example.com',
            'password' => '1234',
            'password_confirmation' => '1234',
            'invite_code' => 'TEST-9999',
            'floor' => '1',
            'door' => 'B',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);
    }

    public function test_user_can_logout(): void{
        //Arrange

        $community = Community::factory()->create();

        $user = User::factory()->create([
            'community_id' => $community->id
        ]);

        $token = $user->createToken('auth_token')->accessToken;

        //Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/logout');

        //Assert
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Logout correcto',
        ]);
    }

    
}
