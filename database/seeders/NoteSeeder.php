<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notes = [
            ['user_id' => 1, 
            'category_id' => 7,
            'title' => '¿Alguien puede ayudarme con la instalación de la caldera?', 
            'description' => 'Tengo problemas para instalar la nueva caldera en mi apartamento. ¿Alguien tiene experiencia con esto?'],
            ['user_id' => 2, 'category_id' => 7, 'title' => '¿Dónde puedo encontrar un buen fontanero en el edificio?', 'description' => 'Estoy buscando recomendaciones para un fontanero confiable que pueda ayudarme con una fuga en mi baño.'],
            ['user_id' => 3, 'category_id' => 5, 'title' => '¿Alguien sabe cómo configurar el Wi-Fi en el edificio?', 'description' => 'No puedo conectarme a la red Wi-Fi del edificio. ¿Alguien sabe cómo configurarla correctamente?'],
            ['user_id' => 1, 'category_id' => 5, 'title' => '¿Alguien tiene una escalera que pueda prestarme?', 'description' => 'Necesito una escalera para limpiar las ventanas exteriores. ¿Alguien tiene una que pueda prestarme por un día?'],
        ];

        foreach ($notes as $note) {
            \App\Models\Note::create($note);
        }
    }
}
