<?php

namespace Database\Seeders;

use App\Models\Community;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $communities = [
            ['name' => 'Edificio Sol', 'address' => 'Calle Mayor 10', 'postal_code' => '08001'],
            ['name' => 'Residencial Luna', 'address' => 'Av. Diagonal 25', 'postal_code' => '08002'],
            ['name' => 'Comunidad Estrella', 'address' => 'Plaza Catalunya 1', 'postal_code' => '08003'],
        ];
        foreach ($communities as $community) {
            Community::create($community);
        }
    }
}
