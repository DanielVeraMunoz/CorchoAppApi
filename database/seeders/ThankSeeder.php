<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ThankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $thanks = [
            ['note_id' => 1, 'giver_id' => 2, 'recipient_id' => 1,],
            ['note_id' => 1, 'giver_id' => 3, 'recipient_id' => 1, ],
            ['note_id' => 2, 'giver_id' => 1, 'recipient_id' => 2, ],
            ['note_id' => 3, 'giver_id' => 2, 'recipient_id' => 3, ],    
        ];

        foreach ($thanks as $thank) {
            \App\Models\Thank::create($thank);
        }
    }
}
