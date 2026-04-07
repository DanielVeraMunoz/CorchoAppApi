<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    /**
     * A basic feature test example.
    //  */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    use RefreshDatabase;

    public function test_authenticated_user_can_create_comment()
    {
    
        $user = \App\Models\User::factory()->create();
        $note = \App\Models\Note::factory()->create();

        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/notes/' . $note->id . '/comments', [
            'content' => 'Este es un comentario de prueba',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', [
            'content' => 'Este es un comentario de prueba',
            'note_id' => $note->id,
            'user_id' => $user->id,
        ]);

    }
}
