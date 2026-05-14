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
        $note = \App\Models\Note::factory()->create([
            'is_completed' => false,
        ]);
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

    public function test_unauthenticated_user_cannot_create_comment()
    {

        $note = \App\Models\Note::factory()->create(['is_completed' => false]);

        $response = $this->postJson('/api/notes/' . $note->id . '/comments', [
            'content' => 'Este es un comentario de prueba',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_admin_can_create_comment()
    {

        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $note = \App\Models\Note::factory()->create(['is_completed' => false]);
        $token = $admin->createToken('auth_token')->accessToken;

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
            'user_id' => $admin->id,
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

    public function test_authenticated_admin_can_list_comments()
    {

        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $note = \App\Models\Note::factory()->create();
        $token = $admin->createToken('auth_token')->accessToken;

        Comment::factory()->count(3)->create([
            'note_id' => $note->id,
            'user_id' => $admin->id,
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

    public function test_athenticated_user_cannot_delete_others_comment()
    {

        $user = \App\Models\User::factory()->create();
        $otherUser = \App\Models\User::factory()->create();
        $note = \App\Models\Note::factory()->create();
        $token = $user->createToken('auth_token')->accessToken;

        $comment = Comment::factory()->create([
            'note_id' => $note->id,
            'user_id' => $otherUser->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/comments/' . $comment->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_authenticated_admin_can_delete_any_comment()
    {

        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $otherUser = \App\Models\User::factory()->create();
        $note = \App\Models\Note::factory()->create();
        $token = $admin->createToken('auth_token')->accessToken;

        $comment = Comment::factory()->create([
            'note_id' => $note->id,
            'user_id' => $otherUser->id,
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

    public function test_authenticated_admin_can_update_any_comment()
    {

        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $otherUser = \App\Models\User::factory()->create();
        $note = \App\Models\Note::factory()->create();
        $token = $admin->createToken('auth_token')->accessToken;

        $comment = Comment::factory()->create([
            'note_id' => $note->id,
            'user_id' => $otherUser->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson('/api/comments/' . $comment->id, [
            'content' => 'Comentario actualizado por admin',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Comentario actualizado por admin',
        ]);
    }

    public function test_unauthenticated_user_cannot_list_comments()
    {
        $note = \App\Models\Note::factory()->create();

        $response = $this->getJson('/api/notes/' . $note->id . '/comments');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_cannot_update_other_users_comment()
    {
        $user1 = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();
        $note = \App\Models\Note::factory()->create();
        $token = $user1->createToken('auth_token')->accessToken;

        $comment = Comment::factory()->create([
            'note_id' => $note->id,
            'user_id' => $user2->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson('/api/comments/' . $comment->id, [
            'content' => 'Intento modificar comentario ajeno',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
            'content' => 'Intento modificar comentario ajeno',
        ]);
    }

    public function test_authenticated_user_cannot_comment_on_completed_note()
    {
        $user = \App\Models\User::factory()->create();
        $note = \App\Models\Note::factory()->create(['is_completed' => true]);
        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/notes/' . $note->id . '/comments', [
            'content' => 'Este es un comentario de prueba',
        ]);

        $response->assertStatus(400);
         $this->assertDatabaseMissing('comments', [
            'content' => 'Este es un comentario de prueba',
            'note_id' => $note->id,
            'user_id' => $user->id,
        ]);
    }
}
