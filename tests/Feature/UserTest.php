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
        $community = \App\Models\Community::factory()->create();
        $user = \App\Models\User::factory()->create(['community_id' => $community->id]);
        $token = $user->createToken('auth_token')->accessToken;

        \App\Models\User::factory()->count(5)->create(['community_id' => $community->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/users');

        $response->assertStatus(200);
        $response->assertJsonCount(6, 'data');
    }

    public function test_authenticated_admin_can_list_users()
    {
        $community = \App\Models\Community::factory()->create();
        $admin = \App\Models\User::factory()->create(['role' => 'admin', 'community_id' => $community->id]);
        $token = $admin->createToken('auth_token')->accessToken;

        \App\Models\User::factory()->count(5)->create(['community_id' => $community->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/users');

        $response->assertStatus(200);
        $response->assertJsonCount(6, 'data');
    }

    public function test_authenticated_user_can_view_user()
    {
        $community = \App\Models\Community::factory()->create();
        $user = \App\Models\User::factory()->create(['community_id' => $community->id]);
        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/users/' . $user->id);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Usuario obtenido correctamente',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
            ]
        ]);
    }

    public function test_authenticated_admin_can_view_user()
    {
        $community = \App\Models\Community::factory()->create();
        $admin = \App\Models\User::factory()->create(['role' => 'admin', 'community_id' => $community->id]);
        $token = $admin->createToken('auth_token')->accessToken;

        $user = \App\Models\User::factory()->create(['community_id' => $community->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/users/' . $user->id);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Usuario obtenido correctamente',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
            ]
        ]);
    }
    

    public function test_authenticated_user_can_update_own_profile()
    {

        $community = \App\Models\Community::factory()->create();
        $user = \App\Models\User::factory()->create(['community_id' => $community->id]);
        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson('/api/users/' . $user->id, [
            'name' => 'Nuevo Nombre',
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Usuario actualizado correctamente',
            'data' => [
                'id' => $user->id,
                'name' => 'Nuevo Nombre',
            ]
        ]);
    }

    public function test_authenticated_user_cant_update_other_profile()
    {
        $community = \App\Models\Community::factory()->create();
        $user1 = \App\Models\User::factory()->create(['community_id' => $community->id]);
        $user2 = \App\Models\User::factory()->create(['community_id' => $community->id]);
        $token = $user1->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson('/api/users/' . $user2->id, [
            'name' => 'Nuevo Nombre',
            'email' => $user2->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'No tienes permiso para editar este perfil',
        ]);
    }

    public function test_authenticated_admin_can_update_other_profile()
    {
        $community = \App\Models\Community::factory()->create();
        $admin = \App\Models\User::factory()->create(['role' => 'admin', 'community_id' => $community->id]);
        $user = \App\Models\User::factory()->create(['community_id' => $community->id]);
        $token = $admin->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson('/api/users/' . $user->id, [
            'name' => 'Nuevo Nombre',
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Usuario actualizado correctamente',
            'data' => [
                'id' => $user->id,
                'name' => 'Nuevo Nombre',
            ]
        ]);
    }

    public function test_authenticated_admin_can_delete_own_profile()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/users/' . $admin->id);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Usuario eliminado correctamente',
        ]);
    }

    public function test_authenticated_user_can_delete_own_profile()
    {
        $user = \App\Models\User::factory()->create();
        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/users/' . $user->id);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Usuario eliminado correctamente',
        ]);
    }

    public function test_authenticated_user_cant_delete_other_profile()
    {
        $community = \App\Models\Community::factory()->create();
        $user1 = \App\Models\User::factory()->create(['community_id' => $community->id]);
        $user2 = \App\Models\User::factory()->create(['community_id' => $community->id]);
        $token = $user1->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/users/' . $user2->id);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'No tienes permiso para eliminar este perfil',
        ]);
    }

    public function test_authenticated_admin_can_delete_other_profile()
    {
        $community = \App\Models\Community::factory()->create();
        $admin = \App\Models\User::factory()->create(['role' => 'admin', 'community_id' => $community->id]);
        $user = \App\Models\User::factory()->create(['community_id' => $community->id]);
        $token = $admin->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/users/' . $user->id);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Usuario eliminado correctamente',
        ]);
    }

    
}
