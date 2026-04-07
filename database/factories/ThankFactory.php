<?php

namespace Database\Factories;

use App\Models\Thank;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Note;


/**
 * @extends Factory<Thank>
 */
class ThankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'note_id' => Note::factory(),
            'giver_id' => User::factory(),
            'recipient_id' => User::factory(),
        ];
    }
}
