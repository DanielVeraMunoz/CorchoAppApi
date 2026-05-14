<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ThankTest extends TestCase
{

    use RefreshDatabase;

    public function test_authenticated_user_can_thank_a_user()
    {
        $community = \App\Models\Community::factory()->create();
        $author = \App\Models\User::factory()->create(['community_id' => $community->id]);
        $recipient = \App\Models\User::factory()->create(['community_id' => $community->id]);
        $note = \App\Models\Note::factory()->create(['user_id' => $author->id]);
        $token = $author->createToken('auth_token')->accessToken;


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/users/' . $recipient->id . '/thanks', [
            'note_id' => $note->id,
            'message' => 'Gracias por tu aporte!',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('thanks', [
            'note_id' => $note->id,
            'giver_id' => $author->id,
            'recipient_id' => $recipient->id,
        ]);
    }


    public function test_authenticated_user_can_list_thanks()
    {
        $user = \App\Models\User::factory()->create();
        $note = \App\Models\Note::factory()->create();
        $token = $user->createToken('auth_token')->accessToken;

        \App\Models\Thank::factory()->count(3)->create([
            'note_id' => $note->id,
            'giver_id' => $note->user_id,
            'recipient_id' => $user->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/users/' . $user->id . '/thanks');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_authenticated_admin_can_list_thanks()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $note = \App\Models\Note::factory()->create();
        $token = $admin->createToken('auth_token')->accessToken;

        \App\Models\Thank::factory()->count(3)->create([
            'note_id' => $note->id,
            'giver_id' => $admin->id,
            'recipient_id' => $note->user_id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/users/' . $note->user_id . '/thanks');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }


    public function test_authenticated_user_can_delete_own_thank()
    {
        $user = \App\Models\User::factory()->create();
        $note = \App\Models\Note::factory()->create();
        $token = $user->createToken('auth_token')->accessToken;

        $thank = \App\Models\Thank::factory()->create([
            'note_id' => $note->id,
            'giver_id' => $user->id,
            'recipient_id' => $note->user_id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/thanks/' . $thank->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('thanks', [
            'id' => $thank->id,
        ]);
    }

    public function test_authenticated_user_cannot_delete_others_thank()
    {
        $user1 = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();
        $note = \App\Models\Note::factory()->create();
        $token = $user1->createToken('auth_token')->accessToken;

        $thank = \App\Models\Thank::factory()->create([
            'note_id' => $note->id,
            'giver_id' => $user2->id,
            'recipient_id' => $note->user_id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/thanks/' . $thank->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('thanks', [
            'id' => $thank->id,
        ]);
    }

    public function test_user_cannot_thank_themselves()
    {
        $community = \App\Models\Community::factory()->create();
        $user = \App\Models\User::factory()->create(['community_id' => $community->id]);
        $note = \App\Models\Note::factory()->create(['user_id' => $user->id]);
        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/users/' . $user->id . '/thanks', [
            'note_id' => $note->id,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'No puedes agradecerte a ti mismo.']);
    }

    public function test_user_cannot_thank_user_from_different_community()
    {
        $community1 = \App\Models\Community::factory()->create();
        $community2 = \App\Models\Community::factory()->create();
        $author = \App\Models\User::factory()->create(['community_id' => $community1->id]);
        $recipient = \App\Models\User::factory()->create(['community_id' => $community2->id]);
        $note = \App\Models\Note::factory()->create(['user_id' => $author->id]);
        $token = $author->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/users/' . $recipient->id . '/thanks', [
            'note_id' => $note->id,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'El destinatario debe ser parte de la misma comunidad.']);
    }

    public function test_unauthenticated_user_cannot_create_thank()
    {
        $user = \App\Models\User::factory()->create();
        $note = \App\Models\Note::factory()->create();

        $response = $this->postJson('/api/users/' . $user->id . '/thanks', [
            'note_id' => $note->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_admin_can_delete_any_thank()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $user = \App\Models\User::factory()->create();
        $note = \App\Models\Note::factory()->create();
        $token = $admin->createToken('auth_token')->accessToken;

        $thank = \App\Models\Thank::factory()->create([
            'note_id' => $note->id,
            'giver_id' => $user->id,
            'recipient_id' => $note->user_id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/thanks/' . $thank->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('thanks', [
            'id' => $thank->id,
        ]);
    }
}
