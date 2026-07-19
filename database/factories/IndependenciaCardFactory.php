<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\IndependenciaCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IndependenciaCard>
 */
final class IndependenciaCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'deck' => (string) fake()->numberBetween(1, 2),
            'card_id' => (string) fake()->unique()->numberBetween(1, 100),
            'name' => fake()->words(3, true),
            'type' => fake()->randomElement(['magic', 'trap', 'fire', 'water', 'earth', 'light', 'dark']),
            'stars' => fake()->numberBetween(0, 12),
            'monster_type' => fake()->randomElement(['magic', 'trap', 'Prócer de la Independencia', 'Indio / Guerrero', 'Batalla']),
            'new_monster_type' => fake()->randomElement(['magic', 'trap', 'hero', 'warrior', 'event']),
            'attack' => fake()->numberBetween(0, 5000),
            'defense' => fake()->numberBetween(0, 5000),
            'description' => fake()->sentence(),
            'effect' => fake()->sentence(),
        ];
    }
}
