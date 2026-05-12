<?php

namespace Database\Seeders;

use App\Models\Community;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $communities = [
            ['name' => 'Edificio Sol', 'address' => 'Calle Mayor 10', 'postal_code' => '08001', 'invite_code' => Str::upper(Str::random(8))],
            ['name' => 'Residencial Luna', 'address' => 'Av. Diagonal 25', 'postal_code' => '08002', 'invite_code' => Str::upper(Str::random(8))],
            ['name' => 'Comunidad Estrella', 'address' => 'Plaza Catalunya 1', 'postal_code' => '08003', 'invite_code' => Str::upper(Str::random(8))],
        ];
        foreach ($communities as $community) {
            Community::create($community);
        }
    }
}
