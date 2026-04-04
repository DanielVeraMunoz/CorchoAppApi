<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Avisos oficiales', 'color' => '#FFCCBC'],
            ['name' => 'Reuniones', 'color' => '#BBDEFB'],
            ['name' => 'Sugerencias', 'color' => '#C8E6C9'],
            ['name' => 'Eventos', 'color' => '#FFF9C4'],
            ['name' => 'Favores', 'color' => '#B9F6CA'],
            ['name' => 'Mercadillo', 'color' => '#FFE0B2'],
            ['name' => 'Incidencias', 'color' => '#C5CAE9'],
            ['name' => 'Cajón desastre', 'color' => '#FFCDD2'],
        ];
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
