<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Comment;

class CommentTest extends TestCase
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

    public function test_authenticated_user_can_list_comments()
    {

        $user = \App\Models\User::factory()->create();
        $note = \App\Models\Note::factory()->create();
        $token = $user->createToken('auth_token')->accessToken;

        Comment::factory()->count(3)->create([
            'note_id' => $note->id,
            'user_id' => $user->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/notes/' . $note->id . '/comments');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_authenticated_user_can_delete_own_comment()
    {

        $user = \App\Models\User::factory()->create();
        $note = \App\Models\Note::factory()->create();
        $token = $user->createToken('auth_token')->accessToken;

        $comment = Comment::factory()->create([
            'note_id' => $note->id,
            'user_id' => $user->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/comments/' . $comment->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_authenticated_user_can_update_own_comment()
    {

        $user = \App\Models\User::factory()->create();
        $note = \App\Models\Note::factory()->create();
        $token = $user->createToken('auth_token')->accessToken;

        $comment = Comment::factory()->create([
            'note_id' => $note->id,
            'user_id' => $user->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson('/api/comments/' . $comment->id, [
            'content' => 'Comentario actualizado',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Comentario actualizado',
        ]);
    }
}
