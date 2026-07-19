<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MtgCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MtgCard>
 */
final class MtgCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'set' => fake()->lexify('???'),
            'number' => (string) fake()->numberBetween(1, 300),
            'quantity' => fake()->numberBetween(1, 10),
            'is_sold' => false,
            'name' => fake()->words(2, true),
            'type_line' => 'Creature — Goblin Warrior',
            'mana_cost' => '{1}{R}',
            'rarity' => fake()->randomElement(['common', 'uncommon', 'rare', 'mythic']),
            'price' => fake()->randomFloat(2, 0.10, 50.00),
            'image_url' => fake()->imageUrl(),
            'scryfall_id' => fake()->uuid(),
        ];
    }
}
