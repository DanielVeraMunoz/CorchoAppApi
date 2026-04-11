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

        $author = \App\Models\User::factory()->create();
        $recipient = \App\Models\User::factory()->create();
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
            'giver_id' => $user->id,
            'recipient_id' => $note->user_id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/users/' . $note->id . '/thanks');

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
        ])->getJson('/api/users/' . $note->id . '/thanks');

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
