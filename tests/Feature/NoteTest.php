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

    public function test_authenticated_user_can_list_notes(){


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
}
