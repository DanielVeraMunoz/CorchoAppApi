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

        $response->assertStatus(200);
        $this->assertDatabaseHas('thanks', [
            'note_id' => $note->id,
            'user_id' => $user->id,
        ]);
    }
}
