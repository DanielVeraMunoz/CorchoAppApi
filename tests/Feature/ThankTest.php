<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ThankTest extends TestCase
{

    use RefreshDatabase;

    public function test_authenticated_user_can_thank_note()
    {
        $user = \App\Models\User::factory()->create();
        $note = \App\Models\Note::factory()->create();
        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/notes/' . $note->id . '/thanks');

        $response->assertStatus(201);
        $this->assertDatabaseHas('thanks', [
            'note_id' => $note->id,
            'giver_id' => $user->id,
            'recipient_id' => $note->user_id,
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
        ])->getJson('/api/notes/' . $note->id . '/thanks');

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
}
