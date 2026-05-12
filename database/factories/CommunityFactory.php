<?php

namespace Database\Factories;

use App\Models\Community;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Community>
 */
class CommunityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invite_code' => strtoupper($this->faker->bothify('????-####')),
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'postal_code' => $this->faker->postcode(),
        ];
    }
}
