<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'event_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'is_completed' => $this->faker->boolean(),
        ];
    }
}
