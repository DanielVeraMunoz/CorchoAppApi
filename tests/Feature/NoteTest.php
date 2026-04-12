<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Category;
use App\Models\Community;
use App\Models\User;
use App\Models\Note;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_note()
    {

        //Arrange
        $community = Community::factory()->create();
        $user = User::factory()->create(['community_id' => $community->id]);
        $category = Category::factory()->create();

        $token = $user->createToken('auth_token')->accessToken;

        $data = [
            'title' => 'Nota de prueba :D',
            'description' => 'Aquí iría la descripción jeje',
            'category_id' => $category->id,
            'event_date' => '2027-12-31',
        ];

        //Act (hacer la petición)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/notes', $data);

        //Assert (verificar la respuesta)
        $response->assertStatus(201); //201 es el código de estado para "creado"
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'title',
                'description',
                'event_date',
            ],
        ]);

        $this->assertDatabaseHas('notes', [
            'title' => 'Nota de prueba :D',
            'description' => 'Aquí iría la descripción jeje',
            'category_id' => $category->id,
            'event_date' => '2027-12-31',
        ]);
    }

    public function test_authenticated_admin_can_create_note()
    {

        //Arrange
        $community = Community::factory()->create();
        $admin = User::factory()->create(['community_id' => $community->id, 'role' => 'admin']);
        $category = Category::factory()->create();

        $token = $admin->createToken('auth_token')->accessToken;

        $data = [
            'title' => 'Nota de prueba :D',
            'description' => 'Aquí iría la descripción jeje',
            'category_id' => $category->id,
            'event_date' => '2027-12-31',
        ];

        //Act (hacer la petición)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/notes', $data);

        //Assert (verificar la respuesta)
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'title',
                'description',
                'event_date',
            ],
        ]);

        $this->assertDatabaseHas('notes', [
            'title' => 'Nota de prueba :D',
            'description' => 'Aquí iría la descripción jeje',
            'category_id' => $category->id,
            'event_date' => '2027-12-31',
        ]);
    }


    public function test_unauthenticated_user_cannot_create_note()
    {
        $data = [
            'title' => 'Nota de prueba :D',
            'description' => 'Aquí iría la descripción jeje',
            'category_id' => 1,
            'event_date' => '2027-12-31',
        ];

        $response = $this->postJson('/api/notes', $data);

        $response->assertStatus(401); //401 es el código de estado para "no autorizado"
    }

    public function test_unauthenticated_user_cannot_list_notes()
    {
        $response = $this->getJson('/api/notes');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_list_notes()
    {


        $community = Community::factory()->create();
        $user = User::factory()->create(['community_id' => $community->id]);
        $category = Category::factory()->create();

        Note::factory()->count(3)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/notes');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_authenticated_admin_can_list_notes()
    {
        $community = Community::factory()->create();
        $admin = User::factory()->create(['community_id' => $community->id, 'role' => 'admin']);
        $category = Category::factory()->create();

        Note::factory()->count(3)->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
        ]);

        $token = $admin->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/notes');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }


    public function test_authenticated_user_can_view_note()
    {
        $community = Community::factory()->create();
        $user = User::factory()->create(['community_id' => $community->id]);
        $category = Category::factory()->create();

        $note = Note::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/notes/' . $note->id);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Nota obtenida correctamente',
            'data' => [
                'id' => $note->id,
                'title' => $note->title,
            ]
        ]);
    }

    public function test_authenticated_admin_can_view_note()
    {
        $community = Community::factory()->create();
        $admin = User::factory()->create(['community_id' => $community->id, 'role' => 'admin']);
        $category = Category::factory()->create();

        $note = Note::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
        ]);

        $token = $admin->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/notes/' . $note->id);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Nota obtenida correctamente',
            'data' => [
                'id' => $note->id,
                'title' => $note->title,
            ]
        ]);
    }

    public function test_authenticated_user_can_update_own_note()
    {

        $community = Community::factory()->create();
        $user = User::factory()->create(['community_id' => $community->id]);
        $category = Category::factory()->create();

        $note = Note::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Título original :)'
        ]);

        $token = $user->createToken('auth_token')->accessToken;

        $newData = [
            'title' => 'Título cambiado :D',
            'description' => 'Descripción modificada',
            'category_id' => $category->id,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson('/api/notes/' . $note->id, $newData);


        $response->assertStatus(200);
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => 'Título cambiado :D',
        ]);
    }

    public function test_unauthenticated_user_cannot_update_other_note()
    {

        $community = Community::factory()->create();
        $user1 = User::factory()->create(['community_id' => $community->id]);
        $user2 = User::factory()->create(['community_id' => $community->id]);
        $category = Category::factory()->create();

        $note = Note::factory()->create([
            'user_id' => $user1->id,
            'category_id' => $category->id,
            'title' => 'Título original :)'
        ]);

        $newData = [
            'title' => 'Título cambiado :D',
            'description' => 'Descripción modificada',
            'category_id' => $category->id,
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->putJson('/api/notes/' . $note->id, $newData);

        $response->assertStatus(401);
    }

    public function test_authenticated_admin_can_update_any_note()
    {

        $community = Community::factory()->create();
        $admin = User::factory()->create(['community_id' => $community->id, 'role' => 'admin']);
        $category = Category::factory()->create();

        $note = Note::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Título original :)'
        ]);

        $token = $admin->createToken('auth_token')->accessToken;

        $newData = [
            'title' => 'Título cambiado :D',
            'description' => 'Descripción modificada',
            'category_id' => $category->id,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson('/api/notes/' . $note->id, $newData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => 'Título cambiado :D',
        ]);
    }

    public function test_authenticated_user_can_delete_own_note()
    {

        $community = Community::factory()->create();
        $user = User::factory()->create(['community_id' => $community->id]);
        $category = Category::factory()->create();

        $note = Note::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/notes/' . $note->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('notes', [
            'id' => $note->id,
        ]);
    }

    public function test_authenticated_user_cannot_delete_other_note()
    {

        $community = Community::factory()->create();
        $user1 = User::factory()->create(['community_id' => $community->id]);
        $user2 = User::factory()->create(['community_id' => $community->id]);
        $category = Category::factory()->create();

        $note = Note::factory()->create([
            'user_id' => $user1->id,
            'category_id' => $category->id,
        ]);

        $token = $user2->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/notes/' . $note->id);

        $response->assertStatus(403);
    }

    public function test_authenticated_admin_can_delete_any_note()
    {

        $community = Community::factory()->create();
        $admin = User::factory()->create(['community_id' => $community->id, 'role' => 'admin']);
        $category = Category::factory()->create();

        $note = Note::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
        ]);

        $token = $admin->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/notes/' . $note->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('notes', [
            'id' => $note->id,
        ]);
    }

    public function test_note_author_can_complete_own_note()
    {
        $community = Community::factory()->create();
        $user = User::factory()->create(['community_id' => $community->id]);
        $category = Category::factory()->create();
        $note = Note::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'is_completed' => false,
        ]);

        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->patchJson('/api/notes/' . $note->id . '/complete');

        $response->assertStatus(200);
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'is_completed' => true,
        ]);
    }

    public function test_admin_can_complete_any_note()
    {
        $community = Community::factory()->create();
        $admin = User::factory()->create(['community_id' => $community->id, 'role' => 'admin']);
        $category = Category::factory()->create();
        $note = Note::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'is_completed' => false,
        ]);

        $token = $admin->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->patchJson('/api/notes/' . $note->id . '/complete');

        $response->assertStatus(200);
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'is_completed' => true,
        ]);
    }

    public function test_note_author_can_reopen_own_note()
    {
        $community = Community::factory()->create();
        $user = User::factory()->create(['community_id' => $community->id]);
        $category = Category::factory()->create();
        $note = Note::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'is_completed' => true,
        ]);

        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->patchJson('/api/notes/' . $note->id . '/reopen', ['is_completed' => false]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'is_completed' => false,
        ]);
    }

    public function test_admin_can_reopen_any_note()
    {
        $community = Community::factory()->create();
        $admin = User::factory()->create(['community_id' => $community->id, 'role' => 'admin']);
        $category = Category::factory()->create();
        $note = Note::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'is_completed' => true,
        ]);

        $token = $admin->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->patchJson('/api/notes/' . $note->id . '/reopen', ['is_completed' => false]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'is_completed' => false,
        ]);
    }
}
