<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\CardSet;
use App\Models\CardType;
use App\Models\Faction;
use App\Models\Rarity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Card>
 */
class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'slug' => $this->faker->unique()->slug(),
            'image' => $this->faker->unique()->imageUrl(),
            'altered_api_id' => $this->faker->unique()->randomNumber(),
            'faction_id' => Faction::factory(),
            'rarity_id' => Rarity::factory(),
            'card_type_id' => CardType::factory(),
            'card_set_id' => CardSet::factory(),
        ];
    }
}
