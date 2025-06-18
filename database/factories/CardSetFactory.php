<?php

namespace Database\Factories;

use App\Models\CardSet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CardSet>
 */
class CardSetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'slug' => $this->faker->unique()->slug,
            'altered_api_id' => $this->faker->unique()->randomNumber(),
        ];
    }
}
