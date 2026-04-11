<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $comments = [
            ['note_id' => 1, 'user_id' => 2, 'content' => 'Yo puedo ayudarme mañana.'],
            ['note_id' => 1, 'user_id' => 3, 'content' => 'Ah, no lo sabía! Gracias por la información.'],
            ['note_id' => 2, 'user_id' => 1, 'content' => 'Muy útil, gracias por tu trabajo.'],
            ['note_id' => 3, 'user_id' => 2, 'content' => 'Excelente explicación, me ha aclarado muchas dudas.'],    
        ];

        foreach ($comments as $comment) {
            \App\Models\Comment::create($comment);
        }
    }
}
